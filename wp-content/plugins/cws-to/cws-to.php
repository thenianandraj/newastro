<?php
/**
Plugin Name: CWS Theme Options
Plugin URI: http://cwsthemes.com/
Description: CWS Theme Options.
Text Domain: cws-to
Version: 1.5.7
*/

define( 'CWSTO_VERSION', '1.5.6' );
define( 'CWSTO_REQUIRED_WP_VERSION', '4.0' );

if (!defined('CWSTO_THEME_DIR')) {
	define('CWSTO_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());
	define('CWSTO_FW_DIR', CWSTO_THEME_DIR . '/core-fw'); // !!!!
	define('CWSTO_FW_URI', get_template_directory_uri() . '/core-fw'); // !!!!
}

if (!defined('CWSTO_HOST'))
	define('CWSTO_HOST', 'http://up.cwsthemes.com/cws-to');

if (!defined('CWSTO_PLUGIN_NAME'))
	define('CWSTO_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CWSTO_PLUGIN_DIR'))
	define('CWSTO_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CWSTO_PLUGIN_NAME);

if (!defined('CWSTO_PLUGIN_URL'))
	define('CWSTO_PLUGIN_URL', plugins_url() . '/' . CWSTO_PLUGIN_NAME);

	$theme = wp_get_theme();
	if ($theme->get( 'Template' )) {
		define('CWSTO_THEMESLUG', $theme->get('Template'));
	} else {
		define('CWSTO_THEMESLUG', $theme->get('TextDomain'));
	}

	require_once( CWSTO_PLUGIN_DIR . '/components.php' );
	require_once( CWSTO_PLUGIN_DIR . '/pbfw.php' );

	if (is_file(CWSTO_FW_DIR . '/sections.php')) {
		require_once( CWSTO_FW_DIR . '/sections.php' );
	}

	if (is_file(CWSTO_PLUGIN_DIR . '/plates/Engine.php')) {
		require_once( CWSTO_PLUGIN_DIR . '/plates/Engine.php' );
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Data.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Directory.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/FileExtension.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Folder.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Folders.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Func.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Functions.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Name.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Template/Template.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Extension/ExtensionInterface.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Extension/Asset.php');
		require_once( CWSTO_PLUGIN_DIR . '/plates/Extension/URI.php');
	}

	if (is_file(get_template_directory() . '/core/cws-metaboxes.php')) { // !!!!
		require_once( get_template_directory() . '/core/cws-metaboxes.php' );
	}

	define('CORE_CWSFW_SLUG', 'cwsfw');

	function cws_core_cwsfw_get_args() {
		$theme = wp_get_theme();
		return array(
			'theme_slug'		=> CWSTO_THEMESLUG,
			'theme_name'		=> $theme->get( 'Name' ),
			'theme_version'	=> $theme->get( 'Version' ),
			'menu_type'			=> 'menu',
			'menu_title'		=> esc_html__('Theme Options', 'cws-to' ),
			'page_title'		=> esc_html__('Theme Options', 'cws-to' ),
		);
	};

	function cws_core_norm_array($in, &$out, $pref) {
		foreach ($in as $key => $value) {
			if (is_array($value)) {
				$bracket = strlen($pref) > 0 ? ']' : '';
				cws_core_norm_array($value, $out, $pref . $key . $bracket . '[');
			} else {
				$bracket = strlen($pref) > 0 ? ']' : '';
				$out[$pref . $key . $bracket] = $value;
			}
		}
	}

	function cws_core_cwsfw_startFramework($args) {
		//Regenerate permalinks, if slug (blog / portfolio / staff / testimonials) changed
		$cws_rewrite_slug = get_option('cws_rewrite_slug');
		if (empty($cws_rewrite_slug)){
			update_option('cws_rewrite_slug', false);
		}
		//-------------------------		
		$theme_slug = $args['theme_slug'];
		$values = get_option(CWSTO_THEMESLUG);
		$sections = cwsfw_get_sections();
		if (empty($values)) {
			$values = array();
			foreach ($sections as $key => $v) {
				cws_core_cwsfw_print_layout($v['layout'], '', $values);
			}
			update_option(CWSTO_THEMESLUG, $values);
		}

		// here we need to create theme options menu and admin sidebar
		$args = cws_core_cwsfw_get_args();
		add_menu_page('Theme Options', 'Theme Options', 'manage_options', CORE_CWSFW_SLUG, 'cws_core_cwsfw_callback', '', 199);
		foreach ($sections as $key => $value) {
			add_submenu_page(CORE_CWSFW_SLUG, $value['title'], $value['title'], 'manage_options', CORE_CWSFW_SLUG . '&section=' . $key, '__return_null');
		}
		remove_submenu_page( CORE_CWSFW_SLUG, CORE_CWSFW_SLUG ); // remove first duplicate
	}

	function cws_core_cwsfw_callback($a) {
		$active_section = isset($_GET['section']) ? $_GET['section'] : '';
		$sections = cwsfw_get_sections();
		$values = get_option(CWSTO_THEMESLUG);
		if (!empty($values)) {
			$s_sections = cws_core_cwsfw_build_array_keys($sections);
			cws_core_cwsfw_fillMbAttributes($values, $s_sections);
		}	

		$nonce = esc_html(wp_create_nonce( 'cwsfw_ajax_nonce_' . CWSTO_THEMESLUG));

		global $wp_filesystem;
		WP_Filesystem();
		$result = $wp_filesystem->get_contents(CWSTO_FW_DIR . '/def.json');
		if ($result) {
		$result_d = json_decode($result, true);
		$normalized_res = array();
		cws_core_norm_array($result_d, $normalized_res, '');
		echo '<script id="cwsfw_defaults" type="text/template">';
		echo json_encode($normalized_res);
		echo '</script>';
		echo '<script id="cwsfw_defaults_a" type="text/template">';
		echo json_encode($result);
		echo '</script>';
		}

		echo "<form method='post' id='cwsfw' action='./options.php' enctype='multipart/form-data' data-nonce='$nonce' data-theme='".CWSTO_THEMESLUG."'>";
		echo '<div class="sidebar-panel"><div class="cwsfw_header">';
		echo '<div class="theme_name">'. CWSTO_THEMESLUG .'</div>';
		echo '</div>';
		echo '<div class="cwsfw_sections">';
		echo '<ul class="cwsfw_section_items">';
		foreach ($sections as $key => $value) {
			if (is_array($value['icon'])) {
				$icon = sprintf('<i class="%s %s-%s"></i>', $value['icon'][0], $value['icon'][0], $value['icon'][1]);
			} else {
				// direct link
				$icon = '<span></span>';
			}
			$active = ($key == $active_section) ? ' class="active"' : '';
			echo '<li' . $active . ' data-key="'. $key .'">' . $icon . '<p>' . $value['title'] . '</p></li>';
		}
		/*
		 now we need to add import/export section
		*/
		echo '<li data-key="impexp_options"><i class="fa fa-exchange"></i><p>'. esc_html__('Import & Export options', CWSTO_THEMESLUG) .'</p></li>';
		echo '</ul></div></div>';
		echo '<div class="cwsfw_controls_body"><div class="cwsfw_top_buttons">
		<div class="spinner is-active" style="display:none;float:none;width:auto;height:auto;padding:10px 0 10px 50px;background-position:20px 0;"></div>
		';
		// submit_button( esc_attr__( 'Save Changes', CWSTO_THEMESLUG ), 'primary', 'cwsfw_save', false );
		echo '<button class="button button-primary" name="'.esc_attr('cwsfw_save').'" id="'.esc_attr('cwsfw_save').'" type="submit"><i class="fa fa-floppy-o"></i> '.esc_attr__( 'Save Changes', CWSTO_THEMESLUG ).'</button>';

		cws_core_cwsfw_e_notices();
		echo '</div>';
		echo '<div class="cwsfw_controls">';
		foreach ($sections as $key => $v) {
			if ( !isset($v['active']) || true === $v['active'] ) {
				$active_class = ($key == $active_section) ? '' : ' disable';
				echo '<div class="section' . $active_class . '" data-section="'. $key .'">';
				echo cws_core_cwsfw_print_layout($v['layout'], '');
				echo '</div>';
			}
		}

		echo '<div class="section disable" data-section="impexp_options">';
		echo '<div class="cws_pb_ftabs"><a href="#" data-tab="impexp_options" class="active"><i class="fa fa-arrow-circle-o-up"></i>' . esc_html__('Import & Export options', CWSTO_THEMESLUG) . '</a><div class="clear"></div></div>';
		echo '<div class="cws_form_tab open" data-tabkey="impexp_options">';
		echo '<div class="row row_options">';
	?>
	<textarea id="cwsfw_impexp_ta" style="display:none"></textarea>
	<form enctype="multipart/form-data" id="cwsfw-impexp-upload-form" method="post" class="wp-form" action="">
	<?php
		$upload_dir = wp_upload_dir();
		if ( ! empty( $upload_dir['error'] ) ) :
	?><div class="error"><p><?php esc_html_e('Before you can upload your import file, you will need to fix the following error:', CWSTO_THEMESLUG); ?></p>
			<p><strong><?php echo $upload_dir['error']; ?></strong></p></div>
	<?php
		else :
	?>
		<label for="upload"><?php esc_html_e( 'Choose a file from your computer:', CWSTO_THEMESLUG ); ?></label>
		<div>
			<input type="file" id="cws_impexp_import" name="cws_impexp_import" size="25" />
		</div>
		<div>
	<?php
		submit_button( esc_attr__( 'Import theme\'s data', CWSTO_THEMESLUG ), 'secondary disabled', 'cwsfw_import', false );
		echo ' <a href="#" class="button secondary" download="' . CWSTO_THEMESLUG . '.json" id="cwsfw_export">'. esc_html__('Export current Theme Options', CWSTO_THEMESLUG) .'</a></div>';
		endif;
		?>
	</form>
	<?php
		echo '</div>'; // row
		echo '<div class="clear"></div>';
		echo '</div>'; // cws_form_tab
		echo '</div>';

		echo '<div class="cwsfw_bottom_buttons_wrapper">
				<div class="cwsfw_bottom_buttons">';

		echo '
		<button class="button button-primary" name="'.esc_attr('cwsfw_save-1').'" id="'.esc_attr('cwsfw_save-1').'" type="submit"><i class="fa fa-floppy-o"></i> '.esc_attr__( 'Save Changes', CWSTO_THEMESLUG ).'</button>
		<button class="button button-primary" name="'.esc_attr('cwsfw_reset_all-1').'" id="'.esc_attr('cwsfw_reset_all-1').'" type="submit"><i class="fa fa-refresh"></i> '.esc_attr__( 'Reset all', CWSTO_THEMESLUG ).'</button>
		<button class="button button-primary" name="'.esc_attr('cwsfw_reset_sec').'" id="'.esc_attr('cwsfw_reset_sec').'" type="submit"><i class="fa fa-undo"></i> '.esc_attr__( 'Reset section', CWSTO_THEMESLUG ).'</button>
		';
		// submit_button( esc_attr__( 'Save Changes', CWSTO_THEMESLUG ), 'primary', 'cwsfw_save-1', false );
		// submit_button( esc_attr__( 'Reset all', CWSTO_THEMESLUG ), 'secondary', 'cwsfw_reset_all', false );
		// submit_button( esc_attr__( 'Reset section', CWSTO_THEMESLUG ), 'secondary', 'cwsfw_reset_sec', false );

		echo '
				</div>
			</div>

		</form>';
	}

	function cws_core_cwsfw_e_notices() {
	?>
	<div class="cwsfw_notices">
	<div class="cwsfw_unsaved"><?php esc_html_e('There\'re unsaved changes. Don\'t forget to save them.', CWSTO_THEMESLUG ) ?></div>
	</div>
	<?php
	}

	/* convert array to strings w/delimiter */
	/*function cwsfw_array2str($arr) {
		$out = '';
		$i = 0;
		foreach ($arr as $k => $v) {
			$out .= ($i===0 ? '' : ';') . $v;
			$i++;
		}
		return $out;
	}*/
	add_action( 'wp_ajax_cws_callback_team_zip', 'cws_callback_team_zip' );
	add_action( 'wp_ajax_nopriv_cws_callback_team_zip', 'cws_callback_team_zip' );

	function cws_callback_team_zip(){
		if ( wp_verify_nonce( $_REQUEST['nonce'], 'cws_team_list_ajax') ) {
			selectNameHotelsDB($_POST['name']);			
		}
		wp_die(); 
	}		

	function selectNameHotelsDB($name = false){
		global $wpdb;
		$hotels_array = array();

		if(!empty($name)){
			$name   = esc_sql( $name );
			$res = $wpdb->get_results("SELECT ID,post_title,post_name FROM $wpdb->posts WHERE post_title LIKE '%$name%' AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'cws_staff'");			
			foreach ($res as $key => $value) {
				$hotels_array[] = array($value->ID, $value->post_title, $value->post_name);
			}
		}
		echo json_encode(array('items' => $hotels_array));			
	}



	add_action( 'wp_ajax_cwsfw_'. CWSTO_THEMESLUG .'_ajax_save', 'cws_core_cwsfw_ajax_save' );

	function cws_core_cwsfw_ajax_save() {
		global $aasana_theme_funcs;

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'cwsfw_ajax_nonce_' . CWSTO_THEMESLUG ) ) {
			echo json_encode( array( 'status' => esc_html__('Invalid nonce', CWSTO_THEMESLUG) ) );
			die();
		}
		$pdata = stripslashes( $_POST['data'] );

		$values = array();
		parse_str( $pdata, $values );

		$action = explode('_', $_POST['action']);

		//Regenerate permalinks, if slug (blog / portfolio / staff / testimonials) changed
		$check_slug = array('blog_slug','portfolio_slug','staff_slug','testimonials_slug', 'classes_slug');
		$rewrite_slugs = false;
		foreach ($check_slug as $key => $value) {
			$old_value = $aasana_theme_funcs->cws_get_option($value); //From Database
			$new_value = $values[$value]; //From ThemeOptions
			if ($old_value != $new_value){
				$rewrite_slugs = true;
			}
		}
		if ($rewrite_slugs){
			update_option('cws_rewrite_slug', true);
		} 
		//-------------------------

		update_option($action[1], $values);
		echo json_encode( array( 'status' => 'success' ) );
		die();
	}

	add_action( 'wp_ajax_cwsfw_'. CWSTO_THEMESLUG .'_ajax_read_def', 'cws_core_cwsfw_ajax_read_def' );

	function cws_core_cwsfw_ajax_read_def() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'cwsfw_ajax_nonce_' . CWSTO_THEMESLUG ) ) {
			echo json_encode( array( 'status' => esc_html__('Invalid nonce', CWSTO_THEMESLUG) ) );
			die();
		}
		global $wp_filesystem;
		WP_Filesystem();
		$result = json_decode($wp_filesystem->get_contents(CWSTO_FW_DIR . '/def.json'), true);
		$val_str = '';
		foreach ($result as $k => $v) {
			reset($v);
			$key = key($v);
			$val_str .= $key . '=' . $v[$key] . '&';
		}
		$values = array();
		parse_str($val_str, $values);
		$action = explode('_', $_POST['action']);

		if (!empty($_POST['data'])) {
			// reset section
			$sec_keys = explode(',',$_POST['data']);
			foreach ($sec_keys as $key) {
				if (isset($values[$key])) {
					unset($values[$key]);
				}
			}
			$original = get_option($action[1]);
			$values = array_merge($original, $values);
		} else {
			update_option($action[1], $result);
		}
		echo json_encode( array('status' => 'success'));
		die();
	}

	function cws_core_cwsfw_admin_init() {
		if (function_exists('cwsfw_get_sections')) {
			cws_core_cwsfw_startFramework( cws_core_cwsfw_get_args() );
		}
	}
	add_action( 'admin_menu', 'cws_core_cwsfw_admin_init' );

	function cws_core_cwsfw_after_setup_theme() {
		$values = get_option(CWSTO_THEMESLUG);
		if (empty($values) && function_exists('cwsfw_get_sections')) {
			$sections = cwsfw_get_sections();
			$values = array();
			foreach ($sections as $key => $v) {
				cws_core_cwsfw_print_layout($v['layout'], '', $values);
			}
			update_option(CWSTO_THEMESLUG, $values);
		}
	}

	add_action( 'after_setup_theme', 'cws_core_cwsfw_after_setup_theme' );

	function cws_core_cwsfw_admin_scripts($a) {
		global $pagenow;
		global $aasana_theme_funcs;

		$theme_admin_pages = array();
		if ($aasana_theme_funcs && method_exists($aasana_theme_funcs, 'get_theme_config')) {
			$theme_admin_pages = $aasana_theme_funcs->get_theme_config('admin_pages');
			$theme_admin_pages = ($theme_admin_pages) ?: array(); // !!! since PHP 5.3
		}

		if( ($a == 'post-new.php' || $a == 'post.php' || $a == 'toplevel_page_cwsfw' || in_array($a, $theme_admin_pages)) ) {
			$theme_uri = get_template_directory_uri();
			wp_enqueue_style( 'cws_font_awesome', $theme_uri . '/css/font-awesome.css' );
			wp_enqueue_style( 'cws-iconpack', $theme_uri . '/fonts/cws-iconpack/flaticon.css' );
			wp_enqueue_script('qtip-js', CWSTO_PLUGIN_URL . '/js/jquery.qtip.js', array('jquery'), false );
			wp_enqueue_style('qtip-css', CWSTO_PLUGIN_URL . '/css/jquery.qtip.css', false, '2.0.0' );
			if (has_action('fw_enqueue_scripts')) {
				do_action('fw_enqueue_scripts');
			} else {


				$styles =	array('select2_init' => 'select2.css');

				foreach($styles as $key=>$sc){
					wp_enqueue_style( $key, CWSTO_PLUGIN_URL . '/css/' . $sc);
				}

				$scripts = array ('select2_init' => 'select2.min.js');

				foreach ($scripts as $alias => $src) {
					wp_enqueue_script ($alias, CWSTO_PLUGIN_URL . "/js/$src", array(), "1.0", true);			
				}
				
			}
			wp_enqueue_media();
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('wp-color-picker');
			wp_enqueue_script('cwsfw-main-js', CWSTO_PLUGIN_URL . '/js/cwsfw.js', array('jquery', 'wp-backbone', 'customize-controls', 'qtip-js'), false );

			wp_localize_script('cwsfw-main-js', 'cwsfw_params', array(
				'pagenow' => esc_js($pagenow),
			));

			wp_enqueue_style('cwsfw-main-css', CWSTO_PLUGIN_URL . '/css/cwsfw.css', false, '2.0.0' );

			wp_enqueue_script('webfont_js','https://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js',array('jquery'),'1.5.18', true);
		} else if ($a == 'toplevel_page_cwsfw') {
			wp_enqueue_style('cwsfw-main-css', CWSTO_PLUGIN_URL . '/css/cwsfw.css', false, '2.0.0' );
		}
	}

	add_action('admin_enqueue_scripts', 'cws_core_cwsfw_admin_scripts');

	function cws_core_cwsfw_customize_enqueue() {
		cws_core_cwsfw_admin_scripts('toplevel_page_cwsfw');
	}

	add_action('customize_controls_enqueue_scripts', 'cws_core_cwsfw_customize_enqueue' );

	add_action( 'customize_save_after', 'cws_core_cwsfw_customize_save_after' );

	function cws_core_cwsfw_customize_save_after($wp_customize) {
		$post_values = json_decode( stripslashes_deep( $_POST['customized'] ), true );
		if (isset($post_values['cwsfw_settings'])) {
			$current_options = get_option(CWSTO_THEMESLUG);
			$new_options = $post_values['cwsfw_settings'];
			foreach ($new_options as $key => $value) {
				$current_options[$key] = $value;
			}
			update_option(CWSTO_THEMESLUG, $current_options);
		}
		if (isset($post_values['cwsfw_mb_settings'])) {
			$pid = $wp_customize->get_setting('cwsfw_mb_settings')->default;
			if ($pid) {
				$save_array = get_post_meta($pid, 'cws_mb_post');

				$save_array = get_post_meta($pid, $key);
				if (!empty($save_array[0])) {
					$save_array = $save_array[0];
				}

				$cwsfw_mb_settings0 = $post_values['cwsfw_mb_settings'];
				if (!empty($cwsfw_mb_settings0) && !empty($save_array)) {
					$save_array = array_merge($save_array, $cwsfw_mb_settings0);
				} else if (!empty($cwsfw_mb_settings0) && empty($save_array)) {
					$save_array = $cwsfw_mb_settings0;
				}
				update_post_meta($pid, 'cws_mb_post', $save_array);
			}
		}
	}

	add_action( 'customize_register', 'cws_core_cwsfw_customize_register' );
	add_action( 'stop_previewing_theme', 'cws_core_cwsfw_stop_previewing_theme' );


	function cws_show_cust_section() { 
		if (is_page() || 'post' == get_post_type() || 'cws_portfolio' == get_post_type()) {
			return true;
		} else {
			return false;
		}
	}	
	function cws_core_cwsfw_customize_register( $wp_customize ) {
		require_once( CWSTO_PLUGIN_DIR . '/class-cwsfw-section.php' );
		$wp_customize->add_panel( 'cwsfw', array(
				'type'	=> 'cwsfw',
				'title' => esc_html__( 'Theme Options', CWSTO_THEMESLUG ),
				'description' => esc_html__( 'CWS Theme Options.', CWSTO_THEMESLUG ),
				//'priority'        => 100,
				//'active_callback' => array( $this, 'is_panel_active' ),
			) );

		$sections = cwsfw_get_sections();
		$values = get_option(CWSTO_THEMESLUG);
		if (!empty($values)) {
			$s_sections = cws_core_cwsfw_build_array_keys($sections);
			cws_core_cwsfw_fillMbAttributes($values, $s_sections);
		}

		$wp_customize->add_setting('cwsfw_settings', array(
			'default' => array(),
			'sanitize_callback' => '__return_false'
		));

		foreach ($sections as $key => $value) {
			$sec_name = 'cwsfw_' . $key;

			$wp_customize->add_section( $sec_name, array(
				'title' => $value['title'],
				'panel' => 'cwsfw',
				'args' => array(),
			));
			$wp_customize->add_control(
				new CWSFW_Section(
					$wp_customize, $key . '_general', array(
						'label' => esc_html__( 'General', CWSTO_THEMESLUG ),
						'section' => $sec_name,
						'settings' => 'cwsfw_settings',
						'args' => &$value['layout'],
					)
				)
			);
		}

		$pid = 0;
		if (!isset($_POST['wp_customize'])) {
			if (isset($_GET['url'])) {
				$pid = url_to_postid($_GET['url']);
			} elseif (isset($_GET['page_id'])) {
				$pid = $_GET['page_id'];
			}
			if ($pid) {
				update_option('cwsfw_cust_id', $pid);
			}
		}
		if (!$pid) {
			$pid = (int)get_option('cwsfw_cust_id');
		}

		$cwsfw_mb_settings = $wp_customize->add_setting('cwsfw_mb_settings', array(
			'default' => $pid,
			'sanitize_callback' => '__return_false'
		));

		if (is_file(get_template_directory() . '/core/cws-metaboxes.php')) {
			require_once( get_template_directory() . '/core/cws-metaboxes.php' );
		}

		$metabox_class = CWSTO_THEMESLUG . '_Metaboxes';
		if (class_exists($metabox_class)) {
			$g_metaboxes = new $metabox_class($pid);

			$wp_customize->add_section( 'cwsfw_mb', array(
				'title' => esc_html__( 'Page Metaboxes', CWSTO_THEMESLUG ),
				'description' => esc_html__( 'Page Metaboxes description', CWSTO_THEMESLUG ),
				'active_callback' => 'cws_show_cust_section',
				//'args' => array(),
			) );

			$mb_attr = &$g_metaboxes->mb_page_layout;

			$cws_stored_meta = cws_core_cwsfw_get_post_meta( $pid );			
		
		if (!empty($cws_stored_meta) && !empty($cws_stored_meta[0])) {					
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $mb_attr);


			$wp_customize->add_control(
				new CWSFW_Section(
					$wp_customize, 'cwsfw_mb_page', array(
						'label' => esc_html__( 'General', CWSTO_THEMESLUG ),
						'section' => 'cwsfw_mb',
						'settings' => 'cwsfw_mb_settings',
						'args' => $mb_attr,
					)
				)
			);
		}

		}

		if (isset($_POST['wp_customize'])) {
			// second run
			//update_option('cwsfw_cust_id', '');
		}
		return $wp_customize;
	}

	function cws_core_cwsfw_stop_previewing_theme() {
		update_option('cwsfw_cust_id', '');
	}

	function cws_core_cwsfw_get_post_meta($pid, $key = 'cws_mb_post') {
		$ret = get_post_meta($pid, $key);
		if (!empty($ret[0])) {
			$ret = $ret[0];
		}
		if (is_customize_preview()) {
			global $cwsfw_mb_settings;
			if (!empty($cwsfw_mb_settings) && !empty($ret)) {
				$ret = array_merge($ret, $cwsfw_mb_settings);
			} else if (!empty($cwsfw_mb_settings) && empty($ret)) {
				$ret = $cwsfw_mb_settings;
			}
		}
		$ret = array($ret);
		return $ret;
	}

	/* update */
	// add_filter( 'pre_set_site_transient_update_plugins', 'cws_core_cwsfw_check_for_update' );
	// set_transient('update_plugins', 24);

	function cws_core_cwsfw_check_for_update($transient) {
		if (empty($transient->checked)) { return $transient; }
		$plugin_path = CWSTO_PLUGIN_NAME . '/' . CWSTO_PLUGIN_NAME . '.php';

		$result = wp_remote_get(CWSTO_HOST . '/cws-update.php');
		if ( isset($result->errors) ) {
			return $transient;
		} else {
			if (200 == $result['response']['code']) {
				$resp = json_decode($result['body']);
				if ( !empty($resp->new_version) && version_compare( CWSTO_VERSION, $resp->new_version, '<' ) ) {
					$transient->response[$plugin_path] = $resp;
				}
			}
		}
		return $transient;
	}

	function cws_core_cwsfw_plugins_api($res, $action = null, $args = null) {
		if ( ($action == 'plugin_information') && isset($args->slug) && ($args->slug == CWSTO_PLUGIN_NAME) ) {
			$result = wp_remote_get(CWSTO_HOST . '/cws-update.php?info=1');
			if ( isset($result->errors) ) {
				return;
			} else if (200 == $result['response']['code']) {
				$res = json_decode($result['body'], true);
				$res = (object) array_map(__FUNCTION__, $res);
			}
		}
		return $res;
	}

	add_filter('plugins_api', 'cws_core_cwsfw_plugins_api', 20, 3);

function cws_core_get_option($name) {
	$ret = null;
	if (is_customize_preview()) {
	global $cwsfw_settings;
		if (isset($cwsfw_settings[$name])) {
			$ret = $cwsfw_settings[$name];
			if (is_array($ret)) {
			$theme_options = get_option( CWSTO_THEMESLUG );
				if (isset($theme_options[$name])) {
					$to = $theme_options[$name];
						foreach ($ret as $key => $value) {
							$to[$key] = $value;
						}
					$ret = $to;
				}
			}
			return $ret;
		}
	}
	$theme_options = get_option( CWSTO_THEMESLUG );
	$ret = isset($theme_options[$name]) ? $theme_options[$name] : null;
	$ret = stripslashes_deep( $ret );
	return $ret;
}
