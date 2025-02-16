<?php
/**
 * Smush backup class
 *
 * @package Smush\Core\Modules
 */

namespace Smush\Core\Modules;

use Smush\Core\Helper;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Backup
 */
class Backup extends Abstract_Module {

	/**
	 * Smush instance.
	 *
	 * @var Smush
	 */
	private $smush;

	/**
	 * Key for storing file path for image backup
	 *
	 * @var string
	 */
	private $backup_key = 'smush-full';

	/**
	 * Backup constructor.
	 */
	public function init() {
		// Handle Restore operation.
		add_action( 'wp_ajax_smush_restore_image', array( $this, 'restore_image' ) );

		// Handle bulk restore from modal.
		add_action( 'wp_ajax_get_image_count', array( $this, 'get_image_count' ) );
		add_action( 'wp_ajax_restore_step', array( $this, 'restore_step' ) );
	}

	/**
	 * Creates a backup of file for the given attachment path
	 *
	 * Checks if there is a existing backup, else create one
	 *
	 * @todo Looks like all calls to this method in the plugin pass both params. Why are they optional?
	 *
	 * @param string $file_path      File path.
	 * @param string $attachment_id  Attachment ID.
	 */
	public function create_backup( $file_path = '', $attachment_id = '' ) {
		if ( empty( $file_path ) ) {
			return;
		}

		// Return file path if backup is disabled.
		if ( ! $this->settings->get( 'backup' ) || ! WP_Smush::is_pro() ) {
			return;
		}

		$mod           = WP_Smush::get_instance()->core()->mod;
		$attachment_id = ! empty( $mod->smush->attachment_id ) ? $mod->smush->attachment_id : $attachment_id;

		// We'll need it for self::add_to_image_backup_sizes() anyway.
		if ( empty( $attachment_id ) ) {
			return;
		}

		// Add WordPress 5.3 support for -scaled images size.
		if ( false !== strpos( $file_path, '-scaled.' ) && function_exists( 'wp_get_original_image_path' ) ) {

			// Scaled images already have a backup. Use that and don't create a new one.
			$file_path = wp_get_original_image_path( $attachment_id );
			$this->add_to_image_backup_sizes( $attachment_id, $file_path );

			return;
		}

		// Get the width & height of the original image size.
		$meta = wp_get_attachment_metadata( $attachment_id );

		// $meta can be false on failure. I don't see why width and height would be empty, but just in case.
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$imagesize = array( $meta['width'], $meta['height'] );
		} else {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$imagesize = @getimagesize( $file_path );

			if ( ! $imagesize ) {
				return;
			}
		}

		// Get a backup path if empty.
		$backup_path = $this->get_image_backup_path( $file_path );

		// If we don't have any backup path yet, bail!
		if ( empty( $backup_path ) ) {
			return;
		}

		if ( ! empty( $attachment_id ) && $mod->png2jpg->is_converted( $attachment_id ) ) {
			// No need to create a backup, we already have one if enabled.
			return;
		}

		$copied = false;

		// Check for backup from other plugins, like nextgen, if it doesn't exists, create our own.
		if ( ! file_exists( $backup_path ) ) {
			$copied = @copy( $file_path, $backup_path );
		}

		// Store the backup path in image backup sizes.
		if ( $copied ) {
			$this->add_to_image_backup_sizes( $attachment_id, $backup_path );
		}
	}

	/**
	 * Store new backup path for the image
	 *
	 * @param string $attachment_id  Attachment ID.
	 * @param string $backup_path    Backup path.
	 * @param string $backup_key     Backup key.
	 *
	 * @return bool|int
	 */
	public function add_to_image_backup_sizes( $attachment_id = '', $backup_path = '', $backup_key = '' ) {
		if ( empty( $attachment_id ) || empty( $backup_path ) ) {
			return false;
		}

		// Get the Existing backup sizes.
		$backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
		if ( empty( $backup_sizes ) ) {
			$backup_sizes = array();
		}

		// Prevent phar deserialization vulnerability.
		if ( false !== stripos( $backup_path, 'phar://' ) ) {
			return false;
		}

		// Return if backup file doesn't exists.
		if ( ! file_exists( $backup_path ) ) {
			return false;
		}

		list( $width, $height ) = getimagesize( $backup_path );

		// Store our backup Path.
		$backup_key                  = empty( $backup_key ) ? $this->backup_key : $backup_key;
		$backup_sizes[ $backup_key ] = array(
			'file'   => wp_basename( $backup_path ),
			'width'  => $width,
			'height' => $height,
		);

		// Add to the cached list.
		$this->add_to_images_with_backups_cache_list( $attachment_id );

		return update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );
	}

	/**
	 * Restore the image and its sizes from backup
	 *
	 * @param string $attachment  Attachment.
	 * @param bool   $resp        Send JSON response or not.
	 *
	 * @return bool
	 */
	public function restore_image( $attachment = '', $resp = true ) {
		// If no attachment id is provided, check $_POST variable for attachment_id.
		if ( empty( $attachment ) ) {
			// Check Empty fields.
			if ( empty( $_POST['attachment_id'] ) || empty( $_POST['_nonce'] ) ) {
				wp_send_json_error(
					array(
						'error_msg' => esc_html__( 'Error in processing restore action, Fields empty.', 'wp-smushit' ),
					)
				);
			}
			// Check Nonce.
			if ( ! wp_verify_nonce( $_POST['_nonce'], 'wp-smush-restore-' . $_POST['attachment_id'] ) ) {
				wp_send_json_error(
					array(
						'error_msg' => esc_html__( 'Image not restored, Nonce verification failed.', 'wp-smushit' ),
					)
				);
			}
		}

		// Store the restore success/failure for Full size image.
		$restored    = false;
		$restore_png = false;

		// Process now.
		$attachment_id = empty( $attachment ) ? absint( (int) $_POST['attachment_id'] ) : $attachment;

		// Set a Option to avoid the smush-restore-smush loop.
		update_option( "wp-smush-restore-$attachment_id", true );

		/**
		 * Delete webp.
		 *
		 * Run WebP::delete_images always even when the module is deactivated.
		 *
		 * @since 3.8.0
		 */
		WP_Smush::get_instance()->core()->mod->webp->delete_images( $attachment_id );

		// The scaled images' paths are re-saved when getting the original image.
		// This avoids storing the S3's url in there.
		add_filter( 'as3cf_get_attached_file', array( $this, 'skip_as3cf_url_get_attached_file' ), 10, 4 );

		// Restore Full size -> get other image sizes -> restore other images.
		// Get the Original Path.
		$file_path = get_attached_file( $attachment_id );

		// Add WordPress 5.3 support for -scaled images size.
		if ( false !== strpos( $file_path, '-scaled.' ) && function_exists( 'wp_get_original_image_path' ) ) {
			$file_path = wp_get_original_image_path( $attachment_id, true );
		}

		// And go back to normal after retrieving the original path.
		remove_filter( 'as3cf_get_attached_file', array( $this, 'skip_as3cf_url_get_attached_file' ), 10 );

		// Get the backup path.
		$backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );

		// If there are.
		if ( ! empty( $backup_sizes ) ) {
			// 1. Check if the image was converted from PNG->JPG, Get the corresponding backup path
			if ( ! empty( $backup_sizes['smush_png_path'] ) ) {
				$backup_path = $backup_sizes['smush_png_path'];
				// If we don't have the backup path in backup sizes, Check for legacy original file path.
				if ( empty( $backup_path ) ) {
					// Check if it's a jpg converted from png, and restore the jpg to png.
					$original_file = get_post_meta( $attachment_id, WP_SMUSH_PREFIX . 'original_file', true );
					$backup_path   = Helper::original_file( $original_file );
				}

				// If we have a backup path for PNG file, use restore_png().
				if ( ! empty( $backup_path ) ) {
					$restore_png = true;
				}
			}

			// 2. If we don't have a backup path from PNG->JPG, check for normal smush backup path.
			if ( empty( $backup_path ) ) {
				if ( ! empty( $backup_sizes[ $this->backup_key ] ) ) {
					$backup_path = $backup_sizes[ $this->backup_key ];
				} else {
					// If we don't have a backup path, check for legacy backup naming convention.
					$backup_path = $this->get_image_backup_path( $file_path );
				}
			}
			$backup_path = is_array( $backup_path ) && ! empty( $backup_path['file'] ) ? $backup_path['file'] : $backup_path;

			$is_bak_file = false === strpos( $backup_path, '.bak' );

			if ( $is_bak_file ) {
				$backup_full_path = $backup_path;
			} else {
				$backup_full_path = str_replace( wp_basename( $file_path ), wp_basename( $backup_path ), $file_path );
			}
		}

		// Finally, if we have the backup path, perform the restore operation.
		if ( ! empty( $backup_full_path ) ) {
			/**
			 * Allows S3 to hook, check and download the file
			 */
			do_action( 'smush_file_exists', $backup_full_path, $attachment_id, array() );

			if ( $restore_png ) {
				// restore PNG full size and all other image sizes.
				$restored = $this->restore_png( $attachment_id, $backup_full_path, $file_path );

				// JPG file is already deleted, Update backup sizes.
				if ( $restored ) {
					$this->remove_from_backup_sizes( $attachment_id, 'smush_png_path', $backup_sizes );
				}
			} else {
				// If file exists, corresponding to our backup path.
				// Restore.
				if ( ! $is_bak_file ) {
					$restored = @copy( $backup_full_path, $file_path );
				} else {
					$restored = true;
				}

				// Remove the backup, if we were able to restore the image.
				if ( $restored ) {
					// Update backup sizes.
					$this->remove_from_backup_sizes( $attachment_id, '', $backup_sizes );

					// Delete the backup.
					@unlink( $backup_full_path );
					do_action( 'smush_s3_backup_remove', $attachment_id );
				}
			}
		} elseif ( file_exists( $file_path . '_backup' ) ) {
			// Try to restore from other backups, if any.
			$restored = @copy( $file_path . '_backup', $file_path );
		}

		// Prevent the image from being offloaded during 'wp_generate_attachment_metadata'.
		add_filter( 'as3cf_wait_for_generate_attachment_metadata', '__return_true' );
		// Generate all other image size, and update attachment metadata.
		$metadata = wp_generate_attachment_metadata( $attachment_id, $file_path );
		// Aaand go back to normal.
		remove_filter( 'as3cf_wait_for_generate_attachment_metadata', '__return_true' );

		// Update metadata to db if it was successfully generated.
		if ( ! empty( $metadata ) && ! is_wp_error( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}

		// If any of the image is restored, we count it as success.
		if ( $restored ) {
			// Remove the Meta, And send json success.
			delete_post_meta( $attachment_id, Smush::$smushed_meta_key );

			// Remove PNG to JPG conversion savings.
			delete_post_meta( $attachment_id, WP_SMUSH_PREFIX . 'pngjpg_savings' );

			// Remove Original File.
			delete_post_meta( $attachment_id, WP_SMUSH_PREFIX . 'original_file' );

			// Delete resize savings.
			delete_post_meta( $attachment_id, WP_SMUSH_PREFIX . 'resize_savings' );

			// Remove from the cached list.
			$this->remove_from_images_with_backups_cache_list( $attachment_id );

			// Get the Button html without wrapper.
			$button_html = WP_Smush::get_instance()->library()->generate_markup( $attachment_id );

			// Remove the transient.
			delete_option( "wp-smush-restore-$attachment_id" );

			\Smush\Core\Core::remove_from_smushed_list( $attachment_id );

			if ( ! $resp ) {
				return true;
			}

			$size = file_exists( $file_path ) ? filesize( $file_path ) : 0;
			if ( $size > 0 ) {
				$update_size = size_format( $size, 0 ); // Used in js to update image stat.
			}

			wp_send_json_success(
				array(
					'stats'    => $button_html,
					'new_size' => isset( $update_size ) ? $update_size : 0,
				)
			);
		}
		// Remove the transient.
		delete_option( "wp-smush-restore-$attachment_id" );

		if ( $resp ) {
			wp_send_json_error( array( 'message' => '<div class="wp-smush-error">' . __( 'Unable to restore image', 'wp-smushit' ) . '</div>' ) );
		}

		return false;
	}

	/**
	 * Returns the original file instead of S3 URL.
	 *
	 * @since 3.8.3
	 *
	 * @param string             $url S3 URL.
	 * @param string             $file Local file.
	 * @param int                $attachment_id Attachment ID.
	 * @param Media_Library_Item $as3cf_item Instance of Media_Library_Item.
	 * @return string
	 */
	public function skip_as3cf_url_get_attached_file( $url, $file, $attachment_id, $as3cf_item ) {
		return $file;
	}

	/**
	 * Restore PNG.
	 *
	 * @param string $image_id       Image ID.
	 * @param string $original_file  Original file.
	 * @param string $file_path      File path.
	 *
	 * @return bool
	 */
	private function restore_png( $image_id = '', $original_file = '', $file_path = '' ) {
		// If we don't have attachment id, there is nothing we can do.
		if ( empty( $image_id ) ) {
			return false;
		}

		$meta = '';

		$mod = WP_Smush::get_instance()->core()->mod;

		// Else get the Attachment details.
		/**
		 * For Full Size
		 * 1. Get the original file path
		 * 2. Update the attachment metadata and all other meta details
		 * 3. Delete the JPEG
		 * 4. And we're done
		 * 5. Add a action after updating the URLs, that'd allow the users to perform a additional search, replace action
		 */
		if ( empty( $original_file ) ) {
			$original_file = get_post_meta( $image_id, WP_SMUSH_PREFIX . 'original_file', true );
		}
		$original_file_path = Helper::original_file( $original_file );
		if ( file_exists( $original_file_path ) ) {
			// Update the path details in meta and attached file, replace the image.
			$meta = $mod->png2jpg->update_image_path( $image_id, $file_path, $original_file_path, $meta, 'full', 'restore' );

			// Unlink JPG.
			if ( ! empty( $meta['file'] ) && $original_file == $meta['file'] ) {
				@unlink( $file_path );
			}

			$meta = wp_generate_attachment_metadata( $image_id, $original_file_path );

			/**
			 *  Perform a action after the image URL is updated in post content
			 */
			do_action( 'wp_smush_image_url_updated', $image_id, $file_path, $original_file );
		}
		// Update Meta.
		if ( ! empty( $meta ) ) {
			// Remove Smushing, while attachment data is updated for the image.
			remove_filter( 'wp_generate_attachment_metadata', array( $mod->smush, 'smush_image' ), 15 );
			wp_update_attachment_metadata( $image_id, $meta );

			return true;
		}

		return false;

	}

	/**
	 * Remove a specific backup key from Backup Size array
	 *
	 * @param string $attachment_id  Attachment ID.
	 * @param string $backup_key     Backup key.
	 * @param array  $backup_sizes   Backup sizes.
	 */
	private function remove_from_backup_sizes( $attachment_id = '', $backup_key = '', $backup_sizes = array() ) {
		// Get backup sizes.
		$backup_sizes = empty( $backup_sizes ) ? get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true ) : $backup_sizes;
		$backup_key   = empty( $backup_key ) ? $this->backup_key : $backup_key;

		// If we don't have any backup sizes list or if the particular key is not set, return.
		if ( empty( $backup_sizes ) || ! isset( $backup_sizes[ $backup_key ] ) ) {
			return;
		}

		unset( $backup_sizes[ $backup_key ] );

		// Store it in attachment meta.
		update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $backup_sizes );
	}

	/**
	 * Get the attachments that can be restored.
	 *
	 * @since 3.6.0  Changed from private to public.
	 *
	 * @param bool $return_ids  Whether to return ids or just the count.
	 *
	 * @return array|int  Attachments IDs / Number of attachments.
	 */
	public function get_attachments_with_backups( $return_ids = false ) {
		$images = wp_cache_get( 'images_with_backups', 'wp-smush' );

		if ( ! $images ) {
			global $wpdb;
			$images = $wpdb->get_col(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attachment_backup_sizes' AND (`meta_value` LIKE '%smush-full%' OR `meta_value` LIKE '%smush_png_path%')"
			); // Db call ok.

			if ( $images ) {
				wp_cache_set( 'images_with_backups', $images, 'wp-smush' );
			}
		}

		if ( $return_ids ) {
			return $images;
		}

		return count( $images );
	}

	/**
	 * Adds an attachment to the cached list of images with backup.
	 *
	 * @since 3.8.3
	 *
	 * @param integer $attachment_id Attachment ID to add to the list.
	 */
	private function add_to_images_with_backups_cache_list( $attachment_id ) {
		$images        = wp_cache_get( 'images_with_backups', 'wp-smush' );
		$attachment_id = strval( $attachment_id );

		if ( empty( $images ) ) {
			$images = array( $attachment_id );
		} elseif ( ! in_array( $attachment_id, $images, true ) ) {
			$images[] = $attachment_id;
		}

		wp_cache_set( 'images_with_backups', $images, 'wp-smush' );
	}

	/**
	 * Removes an attachment from the cached list of images with backup.
	 *
	 * @since 3.8.3
	 *
	 * @param integer $attachment_id Attachment ID to add to the list.
	 */
	private function remove_from_images_with_backups_cache_list( $attachment_id ) {
		$images        = wp_cache_get( 'images_with_backups', 'wp-smush' );
		$attachment_id = strval( $attachment_id );

		if ( ! empty( $images ) && in_array( $attachment_id, $images, true ) ) {

			$index = array_search( $attachment_id, $images, true );
			if ( false !== $index ) {
				unset( $images[ $index ] );
				wp_cache_set( 'images_with_backups', array_values( $images ), 'wp-smush' );
			}
		}
	}

	/**
	 * Get the number of attachments that can be restored.
	 *
	 * @since 3.2.2
	 */
	public function get_image_count() {
		check_ajax_referer( 'smush_bulk_restore', '_wpnonce' );
		wp_send_json_success(
			array(
				'items' => $this->get_attachments_with_backups( true ),
			)
		);
	}

	/**
	 * Bulk restore images from the modal.
	 *
	 * @since 3.2.2
	 */
	public function restore_step() {
		check_ajax_referer( 'smush_bulk_restore', '_wpnonce' );
		$id = filter_input( INPUT_POST, 'item', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );

		$status = $id ? $this->restore_image( $id, false ) : false;

		$original_meta = wp_get_attachment_metadata( $id, true );

		// Try to get the file name from path.
		$file_name = explode( '/', $original_meta['file'] );

		if ( is_array( $file_name ) ) {
			$file_name = array_pop( $file_name );
		} else {
			$file_name = $original_meta['file'];
		}

		wp_send_json_success(
			array(
				'success' => $status,
				'src'     => $file_name ? $file_name : __( 'Error getting file name', 'wp-smushit' ),
				'thumb'   => wp_get_attachment_image( $id ),
				'link'    => Helper::get_image_media_link( $id, $file_name, true ),
			)
		);
	}

	/**
	 * Returns the backup path for attachment
	 *
	 * @param string $attachment_path  Attachment path.
	 *
	 * @return bool|string
	 */
	public function get_image_backup_path( $attachment_path ) {
		// If attachment id is not available, return false.
		if ( empty( $attachment_path ) ) {
			return false;
		}
		$path = pathinfo( $attachment_path );

		// If we don't have complete filename return false.
		if ( empty( $path['extension'] ) ) {
			return false;
		}

		return trailingslashit( $path['dirname'] ) . $path['filename'] . '.bak.' . $path['extension'];
	}

	/**
	 * Clear up all the backup files for the image, if any.
	 *
	 * @param int $image_id  Attachment ID.
	 */
	public function delete_backup_files( $image_id ) {
		$smush_meta = get_post_meta( $image_id, Smush::$smushed_meta_key, true );
		if ( empty( $smush_meta ) ) {
			// Return if we don't have any details.
			return;
		}

		// Get the attachment details.
		$meta = wp_get_attachment_metadata( $image_id );

		// Attachment file path.
		$file = get_attached_file( $image_id );

		// Get the backup path.
		$backup_name = $this->get_image_backup_path( $file );

		// If file exists, corresponding to our backup path, delete it.
		@unlink( $backup_name );

		// Remove from the cached list.
		$this->remove_from_images_with_backups_cache_list( $image_id );

		// Check meta for rest of the sizes.
		if ( ! empty( $meta ) && ! empty( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size ) {
				// Get the file path.
				if ( empty( $size['file'] ) ) {
					continue;
				}

				// Image Path and Backup path.
				$image_size_path  = path_join( dirname( $file ), $size['file'] );
				$image_bckup_path = $this->get_image_backup_path( $image_size_path );
				@unlink( $image_bckup_path );
			}
		}
	}

}