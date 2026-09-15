<?php
class Aasana_SCG {
	protected static $text_domain = 'aasana';
	private $cws_tmce_sc_settings_config = array();

	public function __construct() {
		$this->init();
	}

	private function init() {
		add_filter( 'mce_buttons_3', array($this, 'mce_sc_buttons') );
		add_filter( 'mce_external_plugins', array($this, 'mce_sc_plugin') );
		add_action( 'wp_ajax_cws_ajax_sc_settings', array($this, 'ajax_sc_settings_callback') );
		add_action( 'admin_enqueue_scripts', array($this, 'scg_scripts_enqueue'), 11 );
		$body_font = cws_core_get_option( 'body-font' );
		$body_font_color = $body_font['color'];

		$this->cws_tmce_sc_settings_config = array(
			'embed' => array(
				'title' => esc_html__( 'Embed audio/video file', 'aasana' ),
				'icon' => 'dashicons dashicons-format-video',
				'fields' => array(
					'url' => array(
						'title' => esc_html__( 'Url', 'aasana' ),
						'desc' => esc_html__( 'Embed url', 'aasana' ),
						'type' => 'text',
					),
					'width' => array(
						'title' => esc_html__( 'Width', 'aasana' ),
						'desc' => esc_html__( 'Max width in pixels', 'aasana' ),
						'type' => 'number',
					),
					'height' => array(
						'title' => esc_html__( 'Height', 'aasana' ),
						'desc' => esc_html__( 'Max height in pixels', 'aasana' ),
						'type' => 'number',
					)
				)
			),
			'dropcap' => array(
				'title' => esc_html__( 'CWS Dropcap', 'aasana' ),
				'icon' => 'fa fa-font',
				'required' => 'single_char_selected',
				'paired' => true,
				'fields' => array(
					'dropcap_style' => array(
      					'title' => esc_html__( 'Letter Style', 'aasana' ),
      					'type' => 'select',
      					'source' => array(
      						'square' => array(esc_html__( 'Square', 'aasana' ), true),
      						'round' => array(esc_html__( 'Round', 'aasana' ), false),
       
      					),
      				),
					'dropcap_size' => array(
      					'title' => esc_html__( 'Dropcap size', 'aasana' ),
      					'desc' => esc_html__( 'in pixels', 'aasana' ),
      					'type' => 'number',
      					'value' => '70'
     				),  
     				'dropcap_border' => array(
      					'title' => esc_html__( 'Border', 'aasana' ),
      					'type' => 'checkbox',
     				), 
     				'dropcap_fill' => array(
      					'title' => esc_html__( 'Fill', 'aasana' ),
      					'type' => 'checkbox',
     				),       				
				)
			),
			'mark' => array(
				'title' => esc_html__( 'CWS Mark Selection', 'aasana' ),
				'icon' => 'fa fa-pencil',
				'paired' => true,
				'required' => 'selection',
				'fields' => array(
					'font_color' => array(
						'title' => esc_html__( 'Font Color', 'aasana' ),
						'type' => 'text',
						'atts' => 'data-default-color="#fff"',
					),
					'bg_color' => array(
						'title' => esc_html__( 'Background Color', 'aasana' ),
						'type' => 'text',
						'atts' => 'data-default-color="' . AASANA_COLOR . '"',
					)
				)
			),
			'custom_list' => array(
				'title' => esc_html__( 'CWS List Selection', 'aasana' ),
				'icon' => 'fa fa-list-ul',
				'required' => 'list_selection',
				'fields' => array(
					'list_style' => array(
						'title' => esc_html__( 'List Style', 'aasana' ),
						'type' => 'select',
						'source' => array(
							'dot_style' => array(esc_html__( 'Dots', 'aasana' ), true, 'd:icon;d:customize_checkmarks'),
							'checkmarks_style' => array(esc_html__( 'Checkmarks', 'aasana' ), false, 'd:icon;e:customize_checkmarks'),
							'custom_icon_style' => array(esc_html__( 'Custom Icon', 'aasana' ), false, 'e:icon;d:customize_checkmarks'),
						),
					),		
					'customize_checkmarks' => array(
      					'title' => esc_html__( 'Customize Checkmarks', 'aasana' ),			
      					'type' => 'checkbox',
      					'atts' => 'data-options="e:bg_color;e:i_color;e:f_color;e:ofs_padding;e:margin_top;e:margin_bottom"',
     				),  			
					'i_color' => array(
						'title' => esc_html__( 'Icon Color', 'aasana' ),
						'type' => 'text',
						'addrowclasses' => 'disable',
						'atts' => 'data-default-color="' . AASANA_COLOR . '"',
					),					
					'f_color' => array(
						'title' => esc_html__( 'Font Color', 'aasana' ),
						'type' => 'text',
						'addrowclasses' => 'disable',
						'atts' => 'data-default-color="#707273"',
					),
					'bg_color' => array(
						'title' => esc_html__( 'Background Color', 'aasana' ),
						'type' => 'text',
						'addrowclasses' => 'disable',
						'atts' => 'data-default-color="#fff"',
					),	
					'ofs_padding' => array(
						'title' => esc_html__( 'Left & Right paddings', 'aasana' ),
						'type' => 'number',
						'atts' => 'min="0" max="200" step="1"',
						'value' => '0'
					),						
					'margin_top' => array(
						'title' => esc_html__( 'Top margin', 'aasana' ),
						'desc' => esc_html__( 'in pixels', 'aasana' ),
						'type' => 'number',
						'value' => '0'
					),
					'margin_bottom' => array(
						'title' => esc_html__( 'Bottom margin', 'aasana' ),
						'desc' => esc_html__( 'in pixels', 'aasana' ),
						'type' => 'number',
						'value' => '0'
					),			

					'icon' => array(
						'title' => esc_html__( 'Icon', 'aasana' ),
						'type' => 'select',
						'addrowclasses' => 'fai disable',
						'source' => 'fa',
					),
				)
			),
			'carousel' => array(
				'title' => esc_html__( 'CWS Shortcode Carousel', 'aasana' ),
				'icon' => 'fa fa-arrows-h',
				'required' => 'sc_selection_or_nothing',
				'paired' => true,
				'def_content' => "<ul><li>" . esc_html__( 'Some content here', 'aasana' ) . "</li><li>" . esc_html__( 'Some content here', 'aasana' ) . "</li></ul>",
				'fields' => array(
					'title' => array(
						'title' => esc_html__( 'Carousel title', 'aasana' ),
						'type' => 'text',
					),
					'columns' => array(
						'title' => esc_html__( 'Columns', 'aasana' ),
						'type' => 'select',
						'source' => array(
							'1' => array(esc_html__( 'One', 'aasana' ), true),
							'2' => array(esc_html__( 'Two', 'aasana' ), false),
							'3' => array(esc_html__( 'Three', 'aasana' ), false),
							'4' => array(esc_html__( 'Four', 'aasana' ), false)
						),
					)
				)
			),
		);
	}

	public function scg_scripts_enqueue($a) {
		if( $a == 'post-new.php' || $a == 'post.php' ) {
			$prefix = 'cws_sc_';
			$data = array();
			foreach ( $this->cws_tmce_sc_settings_config as $sect_name => $section ) {
				array_push( $data, array(
					'sc_name' => $prefix . $sect_name,
					'title' => isset( $section['title'] ) ? $section['title'] : '',
					'icon' => isset( $section['icon'] ) ? $section['icon'] : '',
					'required' => isset( $section['required'] ) ? $section['required'] : '',
					'def_content' => isset( $section['def_content'] ) ? $section['def_content'] : '',
					'has_options' => isset( $section['fields'] ) && is_array( $section['fields'] ) && !empty( $section['fields'] )
				));
			}
			wp_localize_script('cwsfw-main-js', 'cws_sc_data', $data);
			wp_register_script( 'cws-redux-sc-settings', get_template_directory_uri() . '/core/js/cws_sc_settings_controller.js', array( 'jquery' ) );
			wp_enqueue_script( 'cws-redux-sc-settings' );
		}
	}

	public function mce_sc_buttons ( $buttons ) {
		$cws_sc_names = array_keys( $this->cws_tmce_sc_settings_config );
		$cws_sc_prefix = 'cws_sc_';
		foreach ($cws_sc_names as $key => $v) {
			$cws_sc_names[$key] = $cws_sc_prefix . $v;
		}
		$buttons = array_merge( $buttons, $cws_sc_names );
		return $buttons;
	}

	public function mce_sc_plugin ( $plugin_array ) {
		$plugin_array['cws_shortcodes'] = get_template_directory_uri() . '/core/js/cws_tmce.js';
		return $plugin_array;
	}

	public function ajax_sc_settings_callback () {
		$shortcode = trim( $_POST['shortcode'] );
		$prefix = 'cws_sc_';
		$selection = isset( $_POST['selection'] ) ? stripslashes( trim( $_POST['selection'] ) ) : '';
		$def_content = isset( $_POST['def_content'] ) ? trim( $_POST['def_content'] ) : '';
		$shortcode = substr($shortcode, 7);
		$paired = isset($this->cws_tmce_sc_settings_config[$shortcode]['paired']) && $this->cws_tmce_sc_settings_config[$shortcode]['paired']? '1' : '0';
		?>
		<div class="cws_sc_settings_container">
			<input type="hidden" name="cws_sc_name" id="cws_sc_name" value="<?php echo esc_attr($shortcode); ?>" />
			<input type="hidden" name="cws_sc_selection" id="cws_sc_selection" value="<?php echo apply_filters( 'cws_dbl_to_sngl_quotes', $selection); ?>" />
			<input type="hidden" name="cws_sc_def_content" id="cws_sc_def_content" value="<?php echo esc_attr($def_content); ?>" />
			<input type="hidden" name="cws_sc_prefix" id="cws_sc_prefix" value="<?php echo esc_attr($prefix); ?>" />
			<input type="hidden" name="cws_sc_paired" id="cws_sc_paired" value="<?php echo esc_attr($paired); ?>" />
	<?php
		$meta = array(
			array (
				'text' => $selection,
				)
			);
		$sc_fields = $this->cws_tmce_sc_settings_config[$shortcode]['fields'];

		if (function_exists('cws_core_cwsfw_fillMbAttributes')) {
			cws_core_cwsfw_fillMbAttributes( $meta, $sc_fields );
			echo cws_core_cwsfw_print_layout($sc_fields, 'cws_sc_');
		}

	?>
		<input type="submit" class="button button-primary button-large" id="cws_insert_button" value="<?php esc_html_e('Insert Shortcode', 'aasana' ) ?>">
		</div>
	<?php
		wp_die();
	}
}
?>