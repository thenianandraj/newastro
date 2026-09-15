<?php
/*
 * CWS Image Resizer v1.0
 *
 * (c) 2017 CWS Team
 *
 * Uses WP's Image Editor Class
 *
 * @param $url string the local image URL to manipulate
 * @param $params array the options to perform on the image. Keys and values supported:
 *          'width' int pixels
 *          'height' int pixels
 *          'crop' bool | array()
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!function_exists('cwsthumb_get_id_from_url')) {
	function cwsthumb_get_id_from_url($attachment_url) {
		global $wpdb;
		$attachment_id = '';
		if (!empty($attachment_url)) {
			$attachment_id = attachment_url_to_postid($attachment_url);
		}
		return $attachment_id;
	}
}

if(!function_exists('cwsthumb_get_suffix')) {
	function cwsthumb_get_suffix($width, $height, $crop) {

		if (is_array($crop)){
			$crop = $crop[0][0].$crop[1][0];
		} else {
			$crop  = $crop ? '1' : '0';
		}

		return sprintf('%04x', $width) . sprintf('%04x', $height) . $crop;
	}
}

if(!function_exists('cws_thumb')) {
		function cws_thumb($url, $params) {
			global $wp_filesystem;

			if( empty( $wp_filesystem ) ) {
				require_once( ABSPATH .'/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			extract(shortcode_atts(array(
				'width'=> '',
				'height'=>'',
				'crop' => false ), $params));

			$attach_id = cwsthumb_get_id_from_url($url);
			if (!$attach_id) { return null; }

			list($src, $orig_image_width, $orig_image_height) = wp_get_attachment_image_src($attach_id, 'full');
			if (empty($width) && empty($height)) {
				$width = $orig_image_width;
				$height = $orig_image_height;
			}
			if (!$crop) {
				if (empty($width)) {
					if($orig_image_height != 0 && $height != 0){
						$width = (int)(($height / $orig_image_height) * $orig_image_width);
					}
					
				} else if (empty($height)) {
					$height = (int)(($width / $orig_image_width) * $orig_image_height);
				}
			} else {
				if (empty($width)) {
					$width = $height;
				} else if (empty($height)) {
					$height = $width;
				}
			}

			$return_array = array($url, $width, $height, null);
			$img_suffix = cwsthumb_get_suffix($width, $height, $crop);
			$img_path = get_attached_file($attach_id);
			$img_path_array = pathinfo($img_path);
			$new_img_path = $img_path_array['dirname'].'/'.$img_path_array['filename'].'_'.$img_suffix.'.'.$img_path_array['extension'];
			$retina_new_img_path = $img_path_array['dirname'].'/'.$img_path_array['filename'].'_'.$img_suffix.'@2x.'.$img_path_array['extension'];

			if($wp_filesystem->exists($new_img_path)) {
				// check if there is a retina version
				$retina_new_img_url = null;
				if($wp_filesystem->exists($new_img_path)) {
					$img_url = wp_get_attachment_url($attach_id);
					$new_img_url = str_replace($img_path_array['basename'], $img_path_array['filename'].'_'.$img_suffix.'.'.$img_path_array['extension'], $img_url);
					$retina_new_img_url = str_replace($img_path_array['basename'], $img_path_array['filename'].'_'.$img_suffix.'@2x.'.$img_path_array['extension'], $img_url);
					if(!$wp_filesystem->exists($retina_new_img_path)) {
						$retina_new_img_url = null;
					}
				}
				return array($new_img_url, $width, $height, $retina_new_img_url);
			}

			if(!empty($attach_id)) {
				// Retina Dimensions
				$retina_width = (int)$width << 1;
				$retina_height = (int)$height << 1;

				$retina_thumb = true;

				//Make sure we can get Retina
				if ( ((isset($retina_width) && $retina_width > $orig_image_width) || (isset($retina_height) && $retina_height > $orig_image_height)) || ($retina_width == 0 && $retina_height == 0)  ) {
					$retina_thumb = false;
				}

				//Retina Dimensions
				$img_path = get_attached_file($attach_id);
				$img_url  = wp_get_attachment_url($attach_id);

				//Thumbnail path
				$new_img_url = str_replace($img_path_array['basename'], $img_path_array['filename'].'_'.$img_suffix.'.'.$img_path_array['extension'], $img_url);
				$retina_new_img_url = str_replace($img_path_array['basename'], $img_path_array['filename'].'_'.$img_suffix.'@2x.'.$img_path_array['extension'], $img_url);

				//Get image object
				$image_object = wp_get_image_editor($img_path);

				if(!is_wp_error($image_object)) {
					//Resize and save
					$image_object->resize(isset( $width ) ? $width : null, isset( $height ) ? $height : null, isset( $crop ) ? $crop : false);
					$image_object->save($new_img_path);

					//Get sizes of new image object
					$image_sizes = $image_object->get_size();
					$image_width = $image_sizes['width'];
					$image_height = $image_sizes['height'];
				}

				if ( $retina_thumb ) {
					//Get image object (Retina)
					$retina_object = wp_get_image_editor($img_path);

					if(!is_wp_error($retina_object)) {
						//Resize and save
						if ( ( isset( $retina_width ) && $retina_width ) || ( isset( $retina_height ) && $retina_height ) ) {
							$retina_object->resize(isset( $retina_width ) ? $retina_width : null, isset( $retina_height ) ? $retina_height : null, isset( $crop ) ? $crop : false);
							$retina_object->save($retina_new_img_path);
						}
					}
				} else {
					$retina_new_img_url = null;
				}
				// Data to return
				$return_array = array (
					0 => $new_img_url,
					1 => isset($image_width) ? $image_width : $orig_image_width,
					2 => isset($image_height) ? $image_height : $orig_image_height,
					3 => $retina_new_img_url,
				);



		}
		return $return_array;
	}
}
?>