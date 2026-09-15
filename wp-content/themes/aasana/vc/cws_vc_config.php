<?php
	if ( !class_exists( 'cws_ext_VC_Config' ) ){
		class cws_ext_VC_Config extends Aasana_Funcs{

			public function __construct ( $args = array() ){

				require_once(trailingslashit(get_template_directory()) . '/vc/vc_extends/cws_vc_extends.php');
				add_action( 'admin_init', array( $this, 'remove_meta_boxes' ) );
				add_action( 'admin_menu', array( $this, 'remove_grid_elements_menu' ) );
				add_action( 'vc_iconpicker-type-cws_flaticons', array( $this, 'add_cws_flaticons' ) );
				add_action( 'init', array( $this, 'remove_vc_elements' ) );
				
				add_action( 'init', array( $this, 'config' ) );
				if (( in_array( 'cws-essentials/cws-essentials.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'cws-essentials/cws-essentials.php' ) )) {
					add_action( 'init', array( $this, 'extend_shortcodes' ) );
				};	
				add_action( 'init', array( $this, 'extend_params' ) );
				add_action( 'init', array( $this, 'modify_vc_elements' ) );
				add_action('admin_enqueue_scripts', array($this, 'cws_vc_init' ) );
			}

			public function add_cws_shortcode($name, $param1, $param2)  {
				$short = 'shortcode';
				call_user_func('vc_add_' . $short.$name, $param1, $param2);
			}
			public function config (){
				vc_set_default_editor_post_types( array(
					'page',					
					'megamenu_item'
				)); 
			}
			public function get_defaults (){
				$this->args = wp_parse_args( $this->args, $this->defaults );
			}
			// Extend Composer with Theme Shortcodes
			public function extend_shortcodes (){
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_portfolio_posts_grid.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_staff_posts_grid.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_vc_blog.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_text.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_icon.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_button.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_embed.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_call_to_action.php' );			
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_msg_box.php' );	
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_progress_bar.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_milestone.php' );			
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_services.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_testimonial.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_pricing_plan.php' );	
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_divider.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_spacing.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_testimonial_posts.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_carousel.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_gift_cards.php' );

				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_tips.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_banners.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_classes_posts_grid.php' );		

				if ( in_array( 'learnpress/learnpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_vc_lp_course_posts_grid.php' );	
				};

				if ( in_array( 'the-events-calendar/the-events-calendar.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (is_plugin_active_for_network( 'the-events-calendar/the-events-calendar.php' )) ) {
					require_once( trailingslashit( get_template_directory() ) . 'vc/theme_shortcodes/cws_sc_events_posts_grid.php' );	
				};

			}
			// Extend Composer with Custom Parametres
			public function extend_params (){
				require_once( trailingslashit( get_template_directory() ) . 'vc/params/cws_dropdown.php' );
				require_once( trailingslashit( get_template_directory() ) . 'vc/params/cws_svg.php' );
			}
			// Modify VC Elements
			public function modify_vc_elements (){
				if ( function_exists( 'vc_add'.'_shortcode_param' ) ) {
 					$this->add_cws_shortcode('_param' , 'cws_svg' , 'cws_vc_svg');
				}
				vc_remove_param( 'vc_row', 'columns_placement' );
				vc_remove_param( 'vc_row', 'el_class' );
				vc_add_param( 'vc_row' , array(
					'type' => 'dropdown',
					'heading' => "Row Divider",
					'param_name' => 'el_class',
					'value' => array(
						esc_html__( 'None', 'aasana' ) => "",
						esc_html__( 'Top', 'aasana' ) => "top_line",
						esc_html__( 'Bottom', 'aasana' ) => "bottom_line",
						esc_html__( 'Top and Bottom', 'aasana' ) => "top_line bottom_line",						
					)
				));				
				vc_add_param('vc_row',array(
					"type" => "textfield",
					"heading" => __("Minimum Height", 'aasana'),
					"param_name"		=> "cws_row_min_height",
					"value" => '0px',
					"description"	=> esc_html__( 'Add a minimum height to the row so you can have a row without any content but still display it at a certain height. Such as a background with a video or image background but without any content', 'aasana' ),
				));	

				vc_remove_param( 'vc_tta_accordion', 'style' );
				vc_remove_param( 'vc_tta_accordion', 'shape' );
				vc_remove_param( 'vc_tta_accordion', 'color' );
				vc_remove_param( 'vc_tta_accordion', 'no_fill' );
				vc_remove_param( 'vc_tta_accordion', 'spacing' );
				vc_remove_param( 'vc_tta_accordion', 'gap' );

				vc_remove_param( 'vc_tta_tabs', 'style' );
				vc_remove_param( 'vc_tta_tabs', 'shape' );
				vc_remove_param( 'vc_tta_tabs', 'color' );
				vc_remove_param( 'vc_tta_tabs', 'no_fill_content_area' );
				vc_remove_param( 'vc_tta_tabs', 'spacing' );
				vc_remove_param( 'vc_tta_tabs', 'gap' );
				vc_remove_param( 'vc_tta_tabs', 'pagination_style' );
				vc_remove_param( 'vc_tta_tabs', 'pagination_color' );

				vc_remove_param( 'vc_toggle', 'style' );
				vc_remove_param( 'vc_toggle', 'color' );
				vc_remove_param( 'vc_toggle', 'size' );
				vc_remove_param( 'vc_toggle', 'use_custom_heading' );

				vc_remove_param( 'vc_images_carousel', 'partial_view' );	
			}
			// Remove VC Elements
			public function remove_vc_elements (){
				vc_remove_element( 'vc_separator' );
				vc_remove_element( 'vc_text_separator' );
				vc_remove_element( 'vc_message' );
				vc_remove_element( 'vc_gallery' );
				vc_remove_element( 'vc_tta_tour' );
				vc_remove_element( 'vc_tta_pageable' );
				vc_remove_element( 'vc_custom_heading' );
				vc_remove_element( 'vc_btn' );
				vc_remove_element( 'vc_cta' );
				vc_remove_element( 'vc_posts_slider' );
				vc_remove_element( 'vc_progress_bar' );
				vc_remove_element( 'vc_basic_grid' );
				vc_remove_element( 'vc_media_grid' );
				vc_remove_element( 'vc_masonry_grid' );
				vc_remove_element( 'vc_masonry_media_grid' );
				vc_remove_element( 'vc_widget_sidebar' );
			}
			public function add_cws_flaticons ( $icons ){
				$icon_id = "";
				$fi_array = array();
				$fi_icons = call_user_func('aasana' . '_get_all_flaticon_icons');
				$fi_exists = is_array( $fi_icons ) && !empty( $fi_icons );				
				if ( !is_array( $fi_icons ) || empty( $fi_icons ) ){
					return $icons;
				}
				for ( $i = 0; $i < count( $fi_icons ); $i++ ){
					$icon_id = $fi_icons[$i];
					$icon_class = "flaticon-{$icon_id}";
					array_push( $fi_array, array( "$icon_class" => $icon_id ) );
				}
				$icons = array_merge( $icons, $fi_array );
				return $icons;
			}
			// Remove teaser metabox
			public function remove_meta_boxes() {
				remove_meta_box( 'vc_teaser', 'page', 		'side' );
				remove_meta_box( 'vc_teaser', 'post', 		'side' );
				remove_meta_box( 'vc_teaser', 'portfolio', 	'side' );
				remove_meta_box( 'vc_teaser', 'product', 	'side' );
			}
			// Remove 'Grid Elements' from Admin menu
			public function remove_grid_elements_menu(){
			  remove_menu_page( 'edit.php?post_type=vc_grid_item' );
			}
			public function cws_vc_init(){
				wp_enqueue_style( 'vc-css-styles', trailingslashit( get_template_directory_uri() ) . 'vc/vc_extends/css/cws_vc.css' );
			}
		}
	}
	/**/
	/* Config and enable extension */
	/**/
	$vc_config = new cws_ext_VC_Config ();
	/**/
	/* \Config and enable extension */


	if(!class_exists('VC_CWS_Background')){
		class VC_CWS_Background extends cws_ext_VC_Config{
			static public $columns = 0;
			static public $row_atts = '';
			static public $column_atts = '';

			function __construct(){
				add_action('admin_init',array($this,'cws_bg_init'));
			}
			public static function cws_open_vc_shortcode($atts, $content){
				global $aasana_theme_funcs;
				$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
				$bg_image_position = $bg_cws_size = $bg_cws_attachment = $customize_colors_overlay = $cws_overlay_color = $bg_cws_repeat = $html = $bg_layer_position = $bg_layer_margin = $bg_layer_min_height = $data_layer_margin = $el_style_layer = "";
				extract( shortcode_atts( array(
				    "gap" => "",
				    "bg_cws_repeat" => "",		   
				    "bg_image_position" => "",
				    "bg_layer_position" => "",
				    "bg_layer_margin" => "",
				    "bg_layer_min_height" => "",
				    "customize_colors_overlay" => "",
				    "full_width" => "",
				    "bg_cws_size" => "",
				    "bg_cws_attachment" => "",
				    "cws_overlay_color" => $theme_color,
				    "add_layers_parallax" => "",
				    "bg_layer_parallax_speed" => "0.05",
				    "over_section"		=> "",
				    "cws_bg_hovering"		=> "",
				    "cws_row_min_height"		=> "",
					
				), $atts ) );	

				$output = '<!-- CWS Row -->';
				$el_style_bg = !empty($bg_cws_repeat) ? 'background-repeat:'.$bg_cws_repeat.';' : '';
				$el_style_bg .= !empty($bg_cws_size) ? 'background-size:'.$bg_cws_size.';' : '';
				$el_style_bg .= !empty($bg_cws_attachment) ? 'background-attachment:'.$bg_cws_attachment.';' : '';
				$el_style_bg .= !empty($cws_row_min_height) ? 'min-height:'.(int) $cws_row_min_height .'px;' : '';
				if(isset($bg_image_position) && !empty($bg_image_position)){
					switch ($bg_image_position) {
						case 'left_top':
							$el_style_bg .= 'background-position:0%,0%;';
							break;
						case 'left_center':
							$el_style_bg .= 'background-position:0%,50%;';
							break;
						case 'left_bottom':
							$el_style_bg .= 'background-position:0%,100%;';
							break;
						case 'right_top':
							$el_style_bg .= 'background-position:100%,0%;';
							break;
						case 'right_center':
							$el_style_bg .= 'background-position:100%,50%;';
							break;
						case 'right_bottom':
							$el_style_bg .= 'background-position:100%,100%;';
							break;
						case 'center_top':
							$el_style_bg .= 'background-position:50%,0%;';
							break;
						case 'center_center':
							$el_style_bg .= 'background-position:50%,50%;';
							break;
						case 'center_bottom':
							$el_style_bg .= 'background-position:50%,100%;';
							break;
					}
				}

				$output .= '<div class="cws-content'.(!empty($cws_bg_hovering) ? ' add_hovering' : "").''.(!empty($cws_row_min_height) ? ' row_full_height' : "").'"'.(!empty($el_style_bg) ? ' style='.esc_attr($el_style_bg) : "").'>';	
				self::$row_atts = $atts;

				if(isset(self::$row_atts['bg_layer_position']) && !empty(self::$row_atts['bg_layer_position'])){
					switch (self::$row_atts['bg_layer_position']) {
						case 'left_top':
						$el_style_layer .= 'background-position:0%,0%;';
						break;
						case 'left_center':
						$el_style_layer .= 'background-position:0%,50%;';
						break;
						case 'left_bottom':
						$el_style_layer .= 'background-position:0%,100%;';
						break;
						case 'right_top':
						$el_style_layer .= 'background-position:100%,0%;';
						break;
						case 'right_center':
						$el_style_layer .= 'background-position:100%,50%;';
						break;
						case 'right_bottom':
						$el_style_layer .= 'background-position:100%,100%;';
						break;
						case 'center_top':
						$el_style_layer .= 'background-position:50%,0%;';
						break;
						case 'center_center':
						$el_style_layer .= 'background-position:50%,50%;';
						break;
						case 'center_bottom':
						$el_style_layer .= 'background-position:50%,100%;';
						break;
					}
				}	
				$tag = "vc_row";
				
				$el_class = $full_height = $parallax_speed_bg = $parallax_speed_video = $full_width = $equal_height = $flex_row = $columns_placement = $content_placement = $parallax = $parallax_image = $css = $el_id = $video_bg = $video_bg_url = $video_bg_parallax = $css_animation = '';
				$disable_element = '';
				$after_output = '';

				$sc_obj = Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag );
				$row_class_vc = vc_map_get_attributes( $sc_obj->getShortcode(), $atts );
				extract( $row_class_vc );
				wp_enqueue_script( 'wpb_composer_front_js' );
				$el_class = $sc_obj->getExtraClass( $el_class ) . $sc_obj->getCSSAnimation( $css_animation );
				$css_classes = array(
					'vc_row',
					'wpb_row',
					//deprecated
					'vc_row-fluid',
				);

				if ( 'yes' === $disable_element ) {
					if ( vc_is_page_editable() ) {
						$css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
					} else {
						return '';
					}
				}

				if ( vc_shortcode_custom_css_has_property( $css, array(
						'border',
						'background',
					) ) || $video_bg || $parallax
				) {
					$css_classes[] = 'vc_row-has-fill';
				}

				if ( ! empty( $atts['gap'] ) ) {
					$css_classes[] = 'vc_column-gap-' . $atts['gap'];
				}

				$wrapper_attributes = array();
				// build attributes for wrapper
				if ( ! empty( $el_id ) ) {
					$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
				}
				if ( ! empty( $full_width ) ) {
					$wrapper_attributes[] = 'data-vc-full-width="true"';
					$wrapper_attributes[] = 'data-vc-full-width-init="false"';
					if ( 'stretch_row_content' === $full_width ) {
						$wrapper_attributes[] = 'data-vc-stretch-content="true"';
					} elseif ( 'stretch_row_content_no_spaces' === $full_width ) {
						$wrapper_attributes[] = 'data-vc-stretch-content="true"';
						$css_classes[] = 'vc_row-no-padding';
					}
					$after_output .= '<div class="vc_row-full-width vc_clearfix"></div>';
				}
				if(!empty($add_layers_parallax)){
					$css_classes[] = 'cws_prlx_layer';
				}
				if(!empty($full_width) && $full_width == 'stretch_row'){
					$css_classes[] = 'cws_stretch_row';
				}
				
				$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $css_classes ) ) ), $tag, $atts ) );
				$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
				if(!empty($add_layers_parallax)){
					$wrapper_attributes[] = ' data-scroll-speed=" ' .(!empty($bg_layer_parallax_speed) ? esc_attr($bg_layer_parallax_speed) : "0.05"). '"';
				}

				if(isset(self::$row_atts['bg_layer_margin']) && !empty(self::$row_atts['bg_layer_margin'])){
					$data_layer_margin .=  self::$row_atts['bg_layer_margin'];			
				}					

				if(isset(self::$row_atts['bg_layer_min_height']) && !empty(self::$row_atts['bg_layer_min_height'])){
					$el_style_layer .=  'min-height:'.(int)self::$row_atts['bg_layer_min_height']."px;";		
				}
				if(isset(self::$row_atts['cws_layer_image']) && !empty(self::$row_atts['cws_layer_image'])){
					$src = wp_get_attachment_image_src(self::$row_atts['cws_layer_image'], 'full');
					$output .= "<div class='cws-layer".(!empty($add_layers_parallax) ? " cws_prlx_section" : "")."'>";
					$output .= "<div ".( implode( ' ', $wrapper_attributes ) )." style='background-image:url(".esc_attr($src[0]).");".(!empty($el_style_layer) ? $el_style_layer : "")."'".(!empty($data_layer_margin) ? " data-layer-margin='".($data_layer_margin)."'" : "")."></div>";
					$output .= $after_output;
					$output .= "</div>";
				}
				return $output;
			}

			public static function cws_open_vc_shortcode_column($atts, $content){					
				$output = '';
				self::$columns++;
				$el_style_layer = '';	
					
				if(self::$columns == 1){

					$el_style = '';
					if(isset(self::$row_atts['bg_cws_color']) && !empty(self::$row_atts['bg_cws_color'])){
						if(self::$row_atts['bg_cws_color'] == 'gradient'){
							$el_style = cws_render_builder_gradient_rules(self::$row_atts);
						}else{
							$el_style =  "background-color:". self::$row_atts['cws_overlay_color'].";";
						}
					}	

					if(!empty($el_style)){
						$output .= "<div class='cws-overlay-bg' style='".esc_attr($el_style)."'></div>";
					}				
					if(isset(self::$row_atts['cws_pattern_image']) && !empty(self::$row_atts['cws_pattern_image'])){
						$src = wp_get_attachment_image_src(self::$row_atts['cws_pattern_image']);
						$output .= "<div class='cws-overlay-bg' style='background-image:url(".esc_attr($src[0]).")'></div>";
					}							
				}
				global $aasana_theme_funcs;
				$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
				$bg_image_position = $bg_cws_size = $bg_cws_attachment = $customize_colors_overlay = $cws_overlay_color = $bg_cws_repeat = $html = "";
				/**
				 * Shortcode attributes
				 * @var $atts
				 * @var $el_class
				 * @var $width
				 * @var $css
				 * @var $offset
				 * @var $content - shortcode content
				 * Shortcode class
				 * @var $this WPBakeryShortCode_VC_Column
				 */
				extract( shortcode_atts( array(
				    "gap" => "",
				    "bg_cws_repeat" => "",		   
				    "bg_image_position" => "",
				    "customize_colors_overlay" => "",
				    "full_width" => "",
				    "bg_cws_color" => "",
				    "bg_cws_size" => "",
				    "cws_pattern_image" => "",
				    "cws_layer_image" => "",
				    "bg_cws_attachment" => "",
				    "cws_overlay_color" => $theme_color,
					
				), $atts ) );	
				$tag = "vc_column";
				$sc_obj = Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag );
				$el_class = $width = $css = $offset = $mm_column_title = '';
				$atts = vc_map_get_attributes( $sc_obj->getShortcode(), $atts );
				extract( $atts );

				$width = wpb_translateColumnWidthToSpan( $width );
				$width = vc_column_offset_class_merge( $offset, $width );
				$css_classes = array(
					$sc_obj->getExtraClass( $el_class ),
					'wpb_column',
					'vc_column_container',
					$width,
				);

				if (vc_shortcode_custom_css_has_property( $css, array('border', 'background') )) {
					$css_classes[]='vc_col-has-fill';
				}
				if (vc_shortcode_custom_css_has_property( $css, array('border') )) {
					$css_classes[]='vc_col-has-border';
				}
				if (vc_shortcode_custom_css_has_property( $css, array('padding') )) {
					$css_classes[]='vc_col-has-padding';
				}
				if (vc_shortcode_custom_css_has_property( $css, array('margin') )) {
					$css_classes[]='vc_col-has-margin';
				}

				$wrapper_attributes = array();

				$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $tag, $atts ) );
			
				$css_class .= " cws-column";
				$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';
	
				$el_style_bg = !empty($bg_cws_repeat) ? 'background-repeat:'.$bg_cws_repeat.';' : '';
				$el_style_bg .= !empty($bg_cws_size) ? 'background-size:'.$bg_cws_size.';' : '';
				$el_style_bg .= !empty($bg_cws_attachment) ? 'background-attachment:'.$bg_cws_attachment.';' : '';
				if(isset($bg_image_position) && !empty($bg_image_position)){
					switch ($bg_image_position) {
						case 'left_top':
							$el_style_bg .= 'background-position:0%,0%;';
							break;
						case 'left_center':
							$el_style_bg .= 'background-position:0%,50%;';
							break;
						case 'left_bottom':
							$el_style_bg .= 'background-position:0%,100%;';
							break;
						case 'right_top':
							$el_style_bg .= 'background-position:100%,0%;';
							break;
						case 'right_center':
							$el_style_bg .= 'background-position:100%,50%;';
							break;
						case 'right_bottom':
							$el_style_bg .= 'background-position:100%,100%;';
							break;
						case 'center_top':
							$el_style_bg .= 'background-position:50%,0%;';
							break;
						case 'center_center':
							$el_style_bg .= 'background-position:50%,50%;';
							break;
						case 'center_bottom':
							$el_style_bg .= 'background-position:50%,100%;';
							break;
					}
				}
				$output .= "<div " . implode( ' ', $wrapper_attributes ).(!empty($el_style_bg) ? " style='".esc_attr($el_style_bg)."'" : "").">";	
				$output .= '<div class="cws_vc_column-inner ' . esc_attr( trim( vc_shortcode_custom_css_class( $css ) ) ) . '">';
				return $output;
			} 
			public static function cws_close_vc_shortcode_column($atts, $content){			
				global $aasana_theme_funcs;
				$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );		
				$bg_image_position = $bg_cws_size = $bg_cws_attachment = $customize_colors_overlay = $cws_overlay_color = $bg_cws_repeat = $html = "";
				extract( shortcode_atts( array(
				    "gap" => "",
				    "bg_cws_repeat" => "",		   
				    "bg_image_position" => "",
				    "customize_colors_overlay" => "",
				    "full_width" => "",
				    "bg_cws_size" => "",
				    "bg_cws_color" => "",
				    "cws_pattern_image" => "",
				    "cws_layer_image" 	=> "",
				    "bg_layer_position" 	=> "",
				    "bg_cws_attachment" => "",
				    "cws_overlay_color" => $theme_color,
					
				), $atts ) );	
				$output = '<!-- CWS Column --> ';
				$el_style = '';

				if(isset($bg_cws_color) && !empty($bg_cws_color)){
					if($bg_cws_color == 'gradient'){
						$el_style = cws_render_builder_gradient_rules($atts);
					}else{
						$el_style =  "background-color:". $cws_overlay_color.";";
					}
				}

				if(!empty($el_style)){
					$output .= "<div class='cws-overlay-bg' style='".esc_attr($el_style)."'></div>";
				}				
				if(isset($cws_layer_image) && !empty($cws_layer_image)){
					$src = wp_get_attachment_image_src($cws_layer_image);
					$output .= "<div class='cws-layer cws_prlx_layer' style='background-image:url(".esc_attr($src[0]).")'></div>";
				}
				if(isset($cws_pattern_image) && !empty($cws_pattern_image)){
					$src = wp_get_attachment_image_src($cws_pattern_image);
					$output .= "<div class='cws-overlay-bg' style='background-image:url(".esc_attr($src[0]).")'></div>";
				}

				$output .= "</div>";
				$output .= "</div>";
				return $output;
			} 
			/* end parallax_shortcode */			

			public static function cws_accordion_customzize($atts, $content){					
								/**
				 * Shortcode attributes
				 * Shortcode class
				 * @var $this WPBakeryShortCode_VC_Accordion
				 */													
						
				global $aasana_theme_funcs;
				$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
				extract( shortcode_atts( array(
					"add_customize"	=> "",
					"active_icon_color"	=> $theme_color,
					"icon_color"	=> $theme_color,
					"title_color"	=> $theme_color,
					"active_title_color"	=> $theme_color,
					"active_fill_color"	=> "",
				    "add_icon_bordered" => "",
					
				), $atts ) );
				$tag = "vc_tta_accordion";
				$sc_obj = Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag );
				$el_class = $width = $css = $offset = $mm_column_title = $styles = '';
				$atts = vc_map_get_attributes( $sc_obj->getShortcode(), $atts );
				extract( $atts );


				$out = '';
				$accordion_id = uniqid( "cws_vc_shortcode_acc_" );
				$out .= "<div id='{$accordion_id}'>";
					if ( !empty( $add_customize ) ){
						
						if(!empty($active_fill_color)){
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active{
									background:$active_fill_color;
								}
							";							
						}		

						if(!empty($active_title_color)){
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active h4 a span{
									color:$active_title_color;
								}
							";							
						}		

						if(!empty($title_color)){
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel h4 a,
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel h4 a  > *{
									color:$title_color;
								}
							";							
						}	

						if(!empty($icon_color)){
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel h4 i:before,
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel h4 i:after{
									color:$icon_color;
									background:$icon_color;
								}								
							";							
						}						
						
						if(!empty($active_icon_color)){
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active h4 i,
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active h4 i:before,
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active h4 i:after{
									color:$active_icon_color;
								}
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active h4 i:before{
								background:$active_icon_color;
							}
							";							
						}						
						if(!empty($add_icon_bordered)){
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel h4 i{
									box-shadow: 0 0 0 3px {$icon_color};
								}
							";			
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel-title > a{
									padding: 14px 60px 14px 20px !important;
								}								
								html[dir='rtl'] #{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel-title > a{
									padding: 14px 80px 14px 20px !important;
								}
							";				
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel.vc_active h4 i{
									box-shadow: 0 0 0 3px {$active_icon_color};
								}
							";							
							$styles .= "
								#{$accordion_id} .vc_tta.vc_general.vc_tta-accordion .vc_tta-panel-title > a > .vc_tta-title-text{
									margin-left:60px;
								}
							";							
						}					

					}
					if ( !empty( $styles ) ){
						$out .= "<style type='text/css' id='{$accordion_id}_acc_style' scoped>";
							$out .= $styles;
						$out .= "</style>";			
					}
				return $out;
			} 			

			public static function cws_accordion_close($atts, $content){				
				return "</div>";
			} 
			/* end parallax_shortcode */

			public static function cws_close_vc_shortcode($atts, $content){	
				self::$row_atts = '';
				if(isset(self::$columns) && !empty(self::$columns)){
					self::$columns = 0;
				}
				return $output = "</div>";
			}

			function cws_bg_init(){
				$group_name = __('Design Options', 'aasana');

				global $aasana_theme_funcs;
				$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
				if(function_exists('vc_add_param')){


					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Image Repeat", 'aasana'),
							"param_name" => "bg_cws_repeat",
							"value" => array(							
									__("Repeat", 'aasana') => "repeat",
									__("No Repeat", 'aasana') => "no-repeat",
									__("Repeat X", 'aasana') => "repeat-x",
									__("Repeat Y", 'aasana') => "repeat-y",
								),
							"description" => __("Options to control repeatation of the background image. Learn on <a href='http://www.w3schools.com/cssref/playit.asp?filename=playcss_background-repeat' target='_blank'>W3School</a>", 'aasana'),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Attachment", 'aasana'),
							"param_name" => "bg_cws_attachment",
							"value" => array(
									__("Scroll", 'aasana') => "scroll",
									__("Fixed", 'aasana') => "fixed",
								),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Image Size", 'aasana'),
							"param_name" => "bg_cws_size",
							"value" => array(
									__("Initial", 'aasana') => "initial",
									__("Cover", 'aasana') => "cover",
									__("Contain", 'aasana') => "contain",
								),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Image Position", 'aasana'),
							"param_name" => "bg_image_position",
							"value" => array(
									__("Left Top", 'aasana') => "left_top",
									__("Left Center", 'aasana') => "left_center",
									__("Left Bottom", 'aasana') => "left_bottom",
									__("Right Top", 'aasana') => "right_top",
									__("Right Center", 'aasana') => "right_center",
									__("Right Bottom", 'aasana') => "right_bottom",
									__("Center Top", 'aasana') => "center_top",
									__("Center Center", 'aasana') => "center_center",
									__("Center Bottom", 'aasana') => "center_bottom",
								),	
							"group" => $group_name,
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Color", 'aasana'),
							"param_name"		=> "bg_cws_color",
							"value" => array(
									__("None", 'aasana') => "none",
									__("Color", 'aasana') => "color",
									__("Gradient", 'aasana') => "gradient",
								),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_row',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'Color', 'aasana' ),
							"param_name" => "cws_overlay_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'color',
								
							),
							"value"			=> $theme_color
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'From', 'aasana' ),
							"param_name" => "cws_gradient_color_from",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'gradient',
								
							),
							"value"			=> $theme_color
						)
					);					
					vc_add_param('vc_row',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'To', 'aasana' ),
							"param_name" => "cws_gradient_color_to",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'gradient',
								
							),
							"value"			=> $theme_color
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Type", 'aasana'),
							"param_name"		=> "cws_gradient_type",
							"value" => array(
									__("Linear", 'aasana') => "linear",
									__("Radial", 'aasana') => "radial",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'gradient',
								
							),
						)
					);	
					vc_add_param('vc_row',array(
							"type"			=> "textfield",
							"class" => "",
							"heading"		=> esc_html__( 'Angle', 'aasana' ),
							"param_name"	=> "cws_gradient_angle",
							"value" => '45',
							"description"	=> esc_html__( 'Degrees: -360 to 360', 'aasana' ),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_type",
								'value' => 'linear',						
							),
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Shape variant", 'aasana'),
							"param_name"		=> "cws_gradient_shape_variant_type",
							"value" => array(
									__("Simple", 'aasana') => "simple",
									__("Extended", 'aasana') => "extended",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_type",
								'value' => 'radial',	
							),
						)
					);					
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Shape", 'aasana'),
							"param_name"		=> "cws_gradient_shape_type",
							"value" => array(
									__("Ellipse", 'aasana') => "ellipse",
									__("Circle", 'aasana') => "circle",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_shape_variant_type",
								'value' => 'simple',	
							),
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Size keyword", 'aasana'),
							"param_name"		=> "cws_gradient_size_keyword_type",
							"value" => array(
									__("Closest side", 'aasana') => "closest_side",
									__("Farthest side", 'aasana') => "farthest_side",
									__("Closest corner", 'aasana') => "closest_corner",
									__("Farthest corner", 'aasana') => "farthest_corner",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_shape_variant_type",
								'value' => 'extended',	
							),
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Size", 'aasana'),
							"param_name"		=> "cws_gradient_size_type",
							"value" => '60% 55%',
							"description"	=> esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_shape_variant_type",
								'value' => 'extended',	
							),
						)
					);					
					vc_add_param('vc_row',array(
							"type" => "attach_image",
							"class" => "",
							"heading" => __("Pattern", 'aasana'),
							"param_name"		=> "cws_pattern_image",
							"group" => $group_name,
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "checkbox",
							"param_name"		=> "add_layers",
							"group" => $group_name,						
							"value"			=> array( esc_html__( 'Add Layer', 'aasana' ) => true )
						)
					);							
					vc_add_param('vc_row',array(
							"type" => "attach_image",
							"class" => "",
							"heading" => __("Layer", 'aasana'),
							"param_name"		=> "cws_layer_image",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_layers",
								"not_empty"	=> true
							),
						)
					);		
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Layer Image Position", 'aasana'),
							"param_name" => "bg_layer_position",
							"dependency"	=> array(
								"element"	=> "add_layers",
								"not_empty"	=> true
							),
							"value" => array(
									__("Left Top", 'aasana') => "left_top",
									__("Left Center", 'aasana') => "left_center",
									__("Left Bottom", 'aasana') => "left_bottom",
									__("Right Top", 'aasana') => "right_top",
									__("Right Center", 'aasana') => "right_center",
									__("Right Bottom", 'aasana') => "right_bottom",
									__("Center Top", 'aasana') => "center_top",
									__("Center Center", 'aasana') => "center_center",
									__("Center Bottom", 'aasana') => "center_bottom",
								),	
							"group" => $group_name,
						)
					);				
					vc_add_param('vc_row',array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Layer Margin", 'aasana'),
							"param_name" => "bg_layer_margin",
							"dependency"	=> array(
								"element"	=> "add_layers",
								"not_empty"	=> true
							),
							"value" => "0px 0px",
							"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
							"group" => $group_name,
						)
					);					
					vc_add_param('vc_row',array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Min Height", 'aasana'),
							"param_name" => "bg_layer_min_height",
							"dependency"	=> array(
								"element"	=> "add_layers",
								"not_empty"	=> true
							),
							"value" => "0px",
							"group" => $group_name,
						)
					);			
					vc_add_param('vc_row',array(
							"type" => "checkbox",
							"class" => "",
							"heading" => __("Add Parallax", 'aasana'),
							"param_name"		=> "add_layers_parallax",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_layers",
								"not_empty"	=> true
							),
						)
					);			
					vc_add_param('vc_row',array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Parallax Speed", 'aasana'),
							"param_name" => "bg_layer_parallax_speed",
							"dependency"	=> array(
								"element"	=> "add_layers_parallax",
								"not_empty"	=> true
							),
							"value" => "0.05",
							"group" => $group_name,
						)
					);
/*					vc_add_param('vc_row',array(
							"type" => "checkbox",
							"param_name"		=> "add_hover_animations",
							"group" => $group_name,						
							"value"			=> array( esc_html__( 'Add Hover Animation', 'aasana' ) => true )
						)
					);		
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Hover Color", 'aasana'),
							"param_name"		=> "cws_bg_hovering",
							"value" => array(
									__("None", 'aasana') => "none",
									__("Color", 'aasana') => "color",
									__("Gradient", 'aasana') => "gradient",
								),
							"dependency"	=> array(
								"element"	=> "add_hover_animations",
								"not_empty"	=> true
								
							),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_row',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'Color', 'aasana' ),
							"param_name" => "cws_bg_hover_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hovering",
								'value' => 'color',
								
							),
							"value"			=> ""
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'From', 'aasana' ),
							"param_name" => "cws_bg_hover_gradient_color_from",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hovering",
								'value' => 'gradient',
								
							),
							"value"			=> ""
						)
					);					
					vc_add_param('vc_row',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'To', 'aasana' ),
							"param_name" => "cws_bg_hover_gradient_color_to",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hovering",
								'value' => 'gradient',
								
							),
							"value"			=> ""
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Type", 'aasana'),
							"param_name"		=> "cws_bg_hover_gradient_type",
							"value" => array(
									__("Linear", 'aasana') => "linear",
									__("Radial", 'aasana') => "radial",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hovering",
								'value' => 'gradient',
								
							),
						)
					);	
					vc_add_param('vc_row',array(
							"type"			=> "textfield",
							"class" => "",
							"heading"		=> esc_html__( 'Angle', 'aasana' ),
							"param_name"	=> "cws_bg_hover_gradient_angle",
							"value" => '45',
							"description"	=> esc_html__( 'Degrees: -360 to 360', 'aasana' ),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hover_gradient_type",
								'value' => 'linear',						
							),
						)
					);
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Shape variant", 'aasana'),
							"param_name"		=> "cws_bg_hover_gradient_shape_variant_type",
							"value" => array(
									__("Simple", 'aasana') => "simple",
									__("Extended", 'aasana') => "extended",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hover_gradient_type",
								'value' => 'radial',	
							),
						)
					);					
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Shape", 'aasana'),
							"param_name"		=> "cws_bg_hover_gradient_shape_type",
							"value" => array(
									__("Ellipse", 'aasana') => "ellipse",
									__("Circle", 'aasana') => "circle",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hover_gradient_shape_variant_type",
								'value' => 'simple',	
							),
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Size keyword", 'aasana'),
							"param_name"		=> "cws_bg_hover_gradient_size_keyword_type",
							"value" => array(
									__("Closest side", 'aasana') => "closest_side",
									__("Farthest side", 'aasana') => "farthest_side",
									__("Closest corner", 'aasana') => "closest_corner",
									__("Farthest corner", 'aasana') => "farthest_corner",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hover_gradient_shape_variant_type",
								'value' => 'extended',	
							),
						)
					);						
					vc_add_param('vc_row',array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Size", 'aasana'),
							"param_name"		=> "cws_bg_hover_gradient_size_type",
							"value" => '60% 55%',
							"description"	=> esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_bg_hover_gradient_shape_variant_type",
								'value' => 'extended',	
							),
						)
					);
					vc_add_param('vc_row',array(
							"type" => "checkbox",
							"class" => "",
							"heading" => __("Add Mousemove Parallax", 'aasana'),
							"param_name"		=> "add_mouse_parallax",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_hover_animations",
								"not_empty"	=> true
								
							),
						)
					);		*/	




					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Image Repeat", 'aasana'),
							"param_name" => "bg_cws_repeat",
							"value" => array(							
									__("Repeat", 'aasana') => "repeat",
									__("No Repeat", 'aasana') => "no-repeat",
									__("Repeat X", 'aasana') => "repeat-x",
									__("Repeat Y", 'aasana') => "repeat-y",
								),
							"description" => __("Options to control repeatation of the background image. Learn on <a href='http://www.w3schools.com/cssref/playit.asp?filename=playcss_background-repeat' target='_blank'>W3School</a>", 'aasana'),
							"group" => $group_name,
						)
					); 


					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Attachment", 'aasana'),
							"param_name" => "bg_cws_attachment",
							"value" => array(
									__("Scroll", 'aasana') => "scroll",
									__("Fixed", 'aasana') => "fixed",
								),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Image Size", 'aasana'),
							"param_name" => "bg_cws_size",
							"value" => array(
									__("Initial", 'aasana') => "initial",
									__("Cover", 'aasana') => "cover",
									__("Contain", 'aasana') => "contain",
								),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Image Position", 'aasana'),
							"param_name" => "bg_image_position",
							"value" => array(
									__("Left Top", 'aasana') => "left_top",
									__("Left Center", 'aasana') => "left_center",
									__("Left Bottom", 'aasana') => "left_bottom",
									__("Right Top", 'aasana') => "right_top",
									__("Right Center", 'aasana') => "right_center",
									__("Right Bottom", 'aasana') => "right_bottom",
									__("Center Top", 'aasana') => "center_top",
									__("Center Center", 'aasana') => "center_center",
									__("Center Bottom", 'aasana') => "center_bottom",
								),	
							"group" => $group_name,
						)
					);
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Background Color", 'aasana'),
							"param_name"		=> "bg_cws_color",
							"value" => array(
									__("None", 'aasana') => "none",
									__("Color", 'aasana') => "color",
									__("Gradient", 'aasana') => "gradient",
								),
							"group" => $group_name,
						)
					);
					vc_add_param('vc_column',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'Color', 'aasana' ),
							"param_name" => "cws_overlay_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'color',
								
							),
							"value"			=> $theme_color
						)
					);						
					vc_add_param('vc_column',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'From', 'aasana' ),
							"param_name" => "cws_gradient_color_from",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'gradient',
								
							),
							"value"			=> $theme_color
						)
					);					
					vc_add_param('vc_column',array(
							"type" => "colorpicker",
							"class" => "",
							"heading"		=> esc_html__( 'To', 'aasana' ),
							"param_name" => "cws_gradient_color_to",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'gradient',
								
							),
							"value"			=> $theme_color
						)
					);
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Type", 'aasana'),
							"param_name"		=> "cws_gradient_type",
							"value" => array(
									__("Linear", 'aasana') => "linear",
									__("Radial", 'aasana') => "radial",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "bg_cws_color",
								'value' => 'gradient',
								
							),
						)
					);	
					vc_add_param('vc_column',array(
							"type"			=> "textfield",
							"class" => "",
							"heading"		=> esc_html__( 'Angle', 'aasana' ),
							"param_name"	=> "cws_gradient_angle",
							"value" => '45',
							"description"	=> esc_html__( 'Degrees: -360 to 360', 'aasana' ),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_type",
								'value' => 'linear',						
							),
						)
					);
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Shape variant", 'aasana'),
							"param_name"		=> "cws_gradient_shape_variant_type",
							"value" => array(
									__("Simple", 'aasana') => "simple",
									__("Extended", 'aasana') => "extended",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_type",
								'value' => 'radial',	
							),
						)
					);					
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Shape", 'aasana'),
							"param_name"		=> "cws_gradient_shape_type",
							"value" => array(
									__("Ellipse", 'aasana') => "ellipse",
									__("Circle", 'aasana') => "circle",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_shape_variant_type",
								'value' => 'simple',	
							),
						)
					);						
					vc_add_param('vc_column',array(
							"type" => "dropdown",
							"class" => "",
							"heading" => __("Size keyword", 'aasana'),
							"param_name"		=> "cws_gradient_size_keyword_type",
							"value" => array(
									__("Closest side", 'aasana') => "closest_side",
									__("Farthest side", 'aasana') => "farthest_side",
									__("Closest corner", 'aasana') => "closest_corner",
									__("Farthest corner", 'aasana') => "farthest_corner",
								),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_shape_variant_type",
								'value' => 'extended',	
							),
						)
					);						
					vc_add_param('vc_column',array(
							"type" => "textfield",
							"class" => "",
							"heading" => __("Size", 'aasana'),
							"param_name"		=> "cws_gradient_size_type",
							"value" => '60% 55%',
							"description"	=> esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ),
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "cws_gradient_shape_variant_type",
								'value' => 'extended',	
							),
						)
					);					
					vc_add_param('vc_column',array(
							"type" => "attach_image",
							"class" => "",
							"heading" => __("Pattern", 'aasana'),
							"param_name"		=> "cws_pattern_image",
							"group" => $group_name,
						)
					);						
					vc_add_param('vc_tta_accordion',array(
							"type" => "checkbox",
							"param_name"		=> "add_customize",
							"group" => $group_name,
							"value"			=> array( esc_html__( 'Cusomize Accordion', 'aasana' ) => true )
						)
					);				
					vc_add_param('vc_tta_accordion',array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Title Color", 'aasana'),
							"param_name"		=> "title_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_customize",
								"not_empty"	=> true
							),
							'value' => $theme_color
						)
					);						
					vc_add_param('vc_tta_accordion',array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Active Title Color", 'aasana'),
							"param_name"		=> "active_title_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_customize",
								"not_empty"	=> true
							),
							'value' => $theme_color
						)
					);						
					vc_add_param('vc_tta_accordion',array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Icon Color", 'aasana'),
							"param_name"		=> "icon_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_customize",
								"not_empty"	=> true
							),
							'value' => $theme_color
						)
					);						
					vc_add_param('vc_tta_accordion',array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Active Icon Color", 'aasana'),
							"param_name"		=> "active_icon_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_customize",
								"not_empty"	=> true
							),
							'value' => $theme_color
						)
					);											
					vc_add_param('vc_tta_accordion',array(
							"type" => "colorpicker",
							"class" => "",
							"heading" => __("Active Fill Color", 'aasana'),
							"param_name"		=> "active_fill_color",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_customize",
								"not_empty"	=> true
							),
							'value' => "#fff"
						)
					);	
					vc_add_param('vc_tta_accordion',array(
							"type" => "checkbox",
							"param_name"		=> "add_icon_bordered",
							"group" => $group_name,
							"dependency"	=> array(
								"element"	=> "add_customize",
								"not_empty"	=> true
							),
							"value"			=> array( esc_html__( 'Add Icon Bordered', 'aasana' ) => true )
						)
					);	
				}
			} 
		}
		new VC_CWS_Background;
	}
	
	if ( !function_exists( 'vc_theme_before_vc_row' ) ) {
		function vc_theme_before_vc_row($atts, $content = null) {
			$GLOBALS['cws_row_atts'] = $atts;
			return VC_CWS_Background::cws_open_vc_shortcode($atts, $content);
		}
	}

	if ( !function_exists( 'vc_theme_before_vc_column' ) ) {
		function vc_theme_before_vc_column($atts, $content = null) {
			new VC_CWS_Background();
			return VC_CWS_Background::cws_open_vc_shortcode_column($atts, $content);
		}
	}		
	if ( !function_exists( 'vc_theme_before_vc_tta_accordion' ) ) {
		function vc_theme_before_vc_tta_accordion($atts, $content = null) {
			new VC_CWS_Background();
			return VC_CWS_Background::cws_accordion_customzize($atts, $content);
		}
	}	
	if ( !function_exists( 'vc_theme_after_vc_tta_accordion' ) ) {
		function vc_theme_after_vc_tta_accordion($atts, $content = null) {
			new VC_CWS_Background();
			return VC_CWS_Background::cws_accordion_close($atts, $content);
		}
	}				
	if ( !function_exists( 'vc_theme_after_vc_column' ) ) {
		function vc_theme_after_vc_column($atts, $content = null) {
			new VC_CWS_Background();
			return VC_CWS_Background::cws_close_vc_shortcode_column($atts, $content);
		}
	}	
	if ( !function_exists( 'vc_theme_after_vc_row' ) ) {
		function vc_theme_after_vc_row($atts, $content = null) {
			unset($GLOBALS['cws_row_atts']);
			return VC_CWS_Background::cws_close_vc_shortcode($atts, $content);
		}
	}
	
?>