<?php
/*
Plugin Name: CWS Flaticons
Plugin URI: http://cwsthemes.com/
Description: Add Flaticon library support.
Text Domain: cws_flaticons
Version: 1.1.3
*/

define( 'CWS_FI_VERSION', '1.1.3' );
define( 'CWS_FI_REQUIRED_WP_VERSION', '4.0' );

if (!defined('CWS_FI_THEME_DIR'))
	define('CWS_FI_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if (!defined('CWS_FI_HOST'))
	define('CWS_FI_HOST', 'http://up.cwsthemes.com/cwsfi');

if (!defined('CWS_FI_PLUGIN_NAME'))
	define('CWS_FI_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CWS_FI_PLUGIN_DIR'))
	define('CWS_FI_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CWS_FI_PLUGIN_NAME);

if (!defined('CWS_FI_PLUGIN_URL'))
	define('CWS_FI_PLUGIN_URL', WP_PLUGIN_URL . '/' . CWS_FI_PLUGIN_NAME);

$theme = wp_get_theme();
if ($theme->get( 'Template' )) {
	define('CWSFI_THEME_SLUG', $theme->get( 'Template' ));
} else {
	define('CWSFI_THEME_SLUG', $theme->get( 'TextDomain' ));
}

/* update */
//add_filter( 'pre_set_site_transient_update_plugins', 'cwsfi_check_for_update' );

function cwsfi_check_for_update($transient) {
	if (empty($transient->checked))
		return $transient;
	$pb_path = CWS_FI_PLUGIN_NAME . '/' . CWS_FI_PLUGIN_NAME . '.php';

	$result = wp_remote_get(CWS_FI_HOST . '/cws-fi.php?tname=' . CWSFI_THEME_SLUG);
	if ( isset($result->errors) ) {
		return $transient;
	} else {
		if (200 == $result['response']['code']) {
			$resp = json_decode($result['body']);
			if ( version_compare( CWS_FI_VERSION, $resp->new_version, '<' ) ) {
				$transient->response[$pb_path] = $resp;
			}
		}
	}
	return $transient;
}

function cwsfi_plugins_api($res, $action = null, $args = null) {
	if ( ($action == 'plugin_information') && isset($args->slug) && ($args->slug == CWS_FI_PLUGIN_NAME) ) {
		$result = wp_remote_get(CWS_FI_HOST . '/cws-fi.php?info=1');
		if (200 == $result['response']['code']) {
			$res = json_decode($result['body'], true);
			$res = (object) array_map(__FUNCTION__, $res);
		}
	}
	return $res;
}

add_filter('plugins_api', 'cwsfi_plugins_api', 20, 3);

/* /update */

add_action('admin_menu', 'cws_fi_plugin_menu');

function cws_fi_plugin_menu() {
	add_theme_page('CWS FlatIcons Options', 'CWS FlatIcons', 'edit_theme_options', 'cws_flaticons', 'cws_fi_page');
}

function cws_fi_page() {
	if (isset($_FILES['zip_import'])) {
		global $wp_filesystem;
		WP_Filesystem();

		$upload = wp_handle_upload( $_FILES['zip_import'], array( 'test_form' => false, 'test_type' => false ) );
		if ( isset( $upload['error'] ) ) { return; }

		$upload_dir = wp_upload_dir();
		$font_folder = $upload_dir['basedir'] . '/cws-flaticons/';
		if ( $wp_filesystem->is_dir($font_folder) )
			$wp_filesystem->delete($font_folder, true);
		$result = unzip_file( $upload['file'], $font_folder );
		unlink($upload['file']);
		$fi_css = $font_folder . 'font/flaticon.css';

		$rdi = new RecursiveDirectoryIterator($font_folder);
		foreach(new RecursiveIteratorIterator($rdi) as $file) {
			$fname = strtolower($file->getFilename());
			if ('flaticon.css' === $fname) {
				$path = str_replace('\\', '/', $file->getPathname() );
				$rel_path = substr($path, strpos($path , '/cws-flaticons/') );
				$fi_css = $upload_dir['basedir'] . $rel_path;
			} else if (0 !== strpos($fname, 'flaticon') && 0 !== strpos($fname, '.') ) {
				unlink($file->getPathname());
			}
		}
		$out = null;
		if ( $wp_filesystem && $wp_filesystem->exists($fi_css) ) {
			$fi_content = $wp_filesystem->get_contents($fi_css);

			// remove filesize and margins
			$ficss_class = strpos($fi_content, '[class^="flaticon-"]:before');
			$ficss_class = strpos($fi_content, '{', $ficss_class) + 1;
			$ficss_class_end = strpos($fi_content, '}', $ficss_class);
			$fi_content = substr($fi_content, 0, $ficss_class) . 'font-family: Flaticon;font-style: normal;' . substr($fi_content, $ficss_class_end);

			$fi_file = fopen($fi_css, 'w+');
			fwrite($fi_file, $fi_content);
			fclose($fi_file);

			if ( preg_match_all( "/flaticon-((\w+|-?)+):before/", $fi_content, $matches, PREG_PATTERN_ORDER ) ){
				$out = array( 'td' => time(),
					'css' => $upload_dir['baseurl'] . $rel_path,
					'entries' => $matches[1],
					);
			}
			update_option('cwsfi', $out);
			esc_html_e('All done, have fun!', CWSFI_THEME_SLUG);
			echo '<script>parent.window.location.reload(true);</script>';
		} else {
			esc_html_e('Error: Required font files were not found.', CWSFI_THEME_SLUG);
		}
	} else {
		$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size = size_format( $bytes );
		$upload_dir = wp_upload_dir();
		if ( ! empty( $upload_dir['error'] ) ) :
			?><div class="error"><p><?php esc_html_e('Before you can upload your import file, you will need to fix the following error:', 'cws_flaticons'); ?></p>
			<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
		else :
	?>
			<p>
			<form enctype="multipart/form-data" id="cwsfi-upload-form" method="post" class="wp-form" action="<?php echo esc_url( wp_nonce_url( 'themes.php?page=cws_flaticons', 'cwsfi-upload' ) ); ?>">
			<label for="upload"><?php esc_html_e( 'Choose a zip file you\'ve downloaded from http://www.flaticon.com/ :', 'cws_flaticons'); ?></label> (<?php printf( esc_html__('Maximum size: %s', 'cws_flaticons'), $size ); ?>)
			<input type="file" id="upload" accept=".zip" name="zip_import" size="25" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
			<?php
				submit_button( esc_attr__( 'Import FlatIcons', 'cws_flaticons' ), 'primary', 'cwsfi-upload', false );
			?>
			</form>
			</p>
		<?php
			$cwsfi = get_option('cwsfi');
			if (!empty($cwsfi) && isset($cwsfi['css'])) {
				wp_enqueue_style( 'cwsfi-css', $cwsfi['css']);
				esc_html_e('The following icons are already imported. Keep in mind they will be overwritten if you import a new set.');
				echo '<ul class="cwsfi_icons">';
				foreach ($cwsfi['entries'] as $key => $value) {
					echo '<li><i class="flaticon-' . $value . '"></i>&nbsp;'. $value .'</li>';
				}
				echo '</ul>';
			}
		endif;
	}
}

add_filter( 'mce_buttons', 'cwsfi_mce_buttons', 110 );
add_filter( 'mce_external_plugins', 'cwsfi_mce_plugin' );

function cwsfi_mce_buttons($b) {
	$cwsfi = get_option('cwsfi');
	if (!empty($cwsfi) && isset($cwsfi['css'])) {
		wp_enqueue_style( 'cwsfi-css', $cwsfi['css']);
		wp_enqueue_style( 'cwsfi-tmce-css', CWS_FI_PLUGIN_URL . '/cwsfi_tmce.css');
	}
	array_push($b, 'cwsfi_icon');
	return $b;
}

function cwsfi_mce_plugin($pa) {
	$pa['cwsfi_sc'] = CWS_FI_PLUGIN_URL . '/cwsfi_tmce.js';
	return $pa;
}

add_action( 'admin_footer', 'cwsfi_print_templates' );

function cwsfi_print_templates() {
	$cwsfi = get_option('cwsfi');
	$out = '<script type="text/html" id="tmpl-cwsfi-icons">';
	if (!empty($cwsfi) && isset($cwsfi['entries'])) {
		foreach ($cwsfi['entries'] as $key => $value) {
			$out .= $value . ',';
		}
	}
	$out .= '</script>';
	echo $out;
}

add_filter('tiny_mce_before_init', 'cwsfi_tmce_before_init', 10);

function cwsfi_tmce_before_init($settings){
	$cwsfi = get_option('cwsfi');
	if (!empty($cwsfi) && isset($cwsfi['css'])) {
		$settings['content_css'] .= ',' . $cwsfi['css'];
	}
	return $settings;
}

add_action( 'admin_enqueue_scripts', 'cwsfi_enqueue_css' );
add_action( 'wp_enqueue_scripts', 'cwsfi_enqueue_css' );

function cwsfi_enqueue_css($a) {
	$cwsfi = get_option('cwsfi');
	if (!empty($cwsfi) && isset($cwsfi['css'])) {
		wp_enqueue_style( 'cwsfi-css', $cwsfi['css']);
	}
}

?>
