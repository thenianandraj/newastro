<?php
defined('AASANA_COLOR') or define('AASANA_COLOR', '#7b6cd5');
defined('AASANA_FOOTER_COLOR') or define('AASANA_FOOTER_COLOR', '#fafafa');
defined('AASANA_SECONDARY_COLOR') or define('AASANA_SECONDARY_COLOR', '#ea8fca');

if (!is_customize_preview()) {
	new Aasana_Metaboxes();
}

class Aasana_Metaboxes {
	public $mb_page_layout = array();
	public $mb_post_layout = array();
	public $mb_staff_layout = array();
	public $mb_portfolio_layout = array();
	public $mb_classes_layout = array();

	public static $instance;

	public function __construct($a = null) {
		$this->mb_page_layout = array(
			'tab0' => array(
				'type' => 'tab',
				'init' => 'open',
				'title' => esc_html__( 'General', 'cws-essentials' ),
				'layout' => array(
					'is_general' => array(
						'type' => 'checkbox',
						'title' => esc_html__('Customize', 'cws-essentials' ),
						'atts' => 'data-options="e:sb_layout;e:is_blog;e:page_sidebars;e:spacings_page;e:slider_override"',
						'addrowclasses' => 'alt checkbox',
					),
					'page_sidebars' => array(
						'type' => 'fields',
						'addrowclasses' => 'disable inside-box groups',
						'layout' => array(
							'layout' => array(
								'title' => esc_html__('Sidebar Position', 'cws-essentials' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-12',
								'subtype' => 'images',
								'value' => array(
									'{page_sidebars}'=>	array( esc_html__('Default', 'cws-essentials' ), true, 'd:def--sidebar1;d:def--sidebar2', '/img/default.png' ),
									'left' => array( esc_html__('Left', 'cws-essentials' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
									'right' => array( esc_html__('Right', 'cws-essentials' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
									'both' => array( esc_html__('Double', 'cws-essentials' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
									'none' => array( esc_html__('None', 'cws-essentials' ), false, 'd:sb1;d:sb2', '/img/none.png' )
								),
							),
							'sb1' => array(
								'title' => esc_html__('Select a sidebar', 'cws-essentials' ),
								'type' => 'select',
								'addrowclasses' => 'disable box grid-col-6',
								'source' => 'sidebars',
							),
							'sb2' => array(
								'title' => esc_html__('Select right sidebar', 'cws-essentials' ),
								'type' => 'select',
								'addrowclasses' => 'disable box grid-col-6',
								'source' => 'sidebars',
							),
						),
					),
					'is_blog' => array(
						'type' => 'checkbox',
						'title' => esc_html__('Add Blog posts', 'cws-essentials' ),
						'atts' => 'data-options="e:blogtype;e:category"',
						'addrowclasses' => 'disable checkbox grid-col-12',
					),
					'blogtype' => array(
						'type' => 'radio',
						'subtype' => 'images',
						'title' => esc_html__('Blog Layout', 'cws-essentials' ),
						'addrowclasses' => 'disable grid-col-12',
						'value' => array(
							'default'=>	array( esc_html__('Default', 'cws-essentials' ), false, '', '/img/default.png' ),
							'large' => array( esc_html__('Large', 'cws-essentials' ), false, '', '/img/large.png' ),
							'medium' => array( esc_html__('Medium', 'cws-essentials' ), true, '', '/img/medium.png' ),
							'small' => array( esc_html__('Small', 'cws-essentials' ), false, '', '/img/small.png' ),
							'2' => array(  esc_html__('Two', 'cws-essentials' ), false, '', '/img/pinterest_2_columns.png'),
							'3' => array( esc_html__('Three', 'cws-essentials' ), false, '', '/img/pinterest_3_columns.png'),
							'4' => array( esc_html__('Four', 'cws-essentials' ), false, '', '/img/pinterest_4_columns.png'),
						),
					),
					'category' => array(
						'title' => esc_html__('Category', 'cws-essentials' ),
						'type' => 'taxonomy',
						'addrowclasses' => 'disable grid-col-12',
						'atts' => 'multiple',
						'taxonomy' => 'category',
						'source' => array(),
					),
					'spacings_page' => array(
						'title' => esc_html__( 'Page Spacings', 'cws-essentials' ),
						'type' => 'margins',
						'addrowclasses' => 'disable grid-col-12 two-inputs',
						'value' => array(
							'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '70'),
							'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '70'),
						),
					),
					'slider_override' => array(
						'type' => 'fields',
						'addrowclasses' => 'disable inside-box groups',
						
						'layout' => array(
							'is_override' => array(
								'type' => 'checkbox',
								'title' => esc_html__( 'Add Image Slider', 'cws-essentials' ),
								'atts' => 'data-options="e:slider_shortcode;e:is_wide;"',
								'addrowclasses' => 'checkbox grid-col-12',
							),
							'slider_shortcode' => array(
								'addrowclasses' => 'disable box grid-col-12',
								'type' => 'text',
								'default' => ''
							),
							'is_wide' => array( // wide_slider
								'type' => 'checkbox',
								'title' => esc_html__( 'Full-Width Slider', 'cws-essentials' ),
								'atts' => 'checked',
								'addrowclasses' => 'disable checkbox box grid-col-12',
							),
						),
					),
				),
			),
			'tab1' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Header', 'cws-essentials' ),
				'layout' => array(
					'customizer_header_mb' => array(
						'title' => esc_html__( 'Customize', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'type' => 'checkbox',
						'atts' => 'data-options="e:header_zone;e:header_image;e:header_image_style;e:header_image_position;e:header_image_size;e:header_image_repeat;e:header_color_overlay_type;e:override_topbar_color;e:override_menu_color;e:override_menu_border;e:header_spacings"',
						'tooltip' => array(
							'title' => esc_html__( '(Override Theme Options)', 'cws-essentials' ),
							'content' => esc_html__( 'Sample text', 'cws-essentials' ),
						),
					),
					'header_zone' => array(
						'type' => 'group',
						'addrowclasses' => 'group sortable drop disable',
						'tooltip' => array(
							'title' => esc_html__( 'Order header parts', 'cws-essentials' ),
							'content' => esc_html__( 'Drag to reorder', 'cws-essentials' ),
						),
						'title' => esc_html__('Header order', 'cws-essentials' ),
						'button_title' => esc_html__('Add new sidebar', 'cws-essentials' ),
						'value' => array(
						    array('title' => 'Top Bar','val' => 'top_bar_box'),
						    array('title' => 'Header Zone','val' => 'drop_zone_start'),
						    array('title' => 'Logo','val' => 'logo_box'),
						    array('title' => 'Menu','val' => 'menu_box'),
						    array('title' => 'Header Zone','val' => 'drop_zone_end'),
						    array('title' => 'Title area','val' => 'header_box'),
						),
						'layout' => array(
							'title' => array(
								'type' => 'text',
								'value' => '',
								'atts' => 'data-role="title"',
								'title' => esc_html__('Sidebar', 'cws-essentials' ),
							),
							'val' => array(
								'type' => 'text',
							)

						)
					),
					'header_image' => array(
						'title' => esc_html__( 'Background Image', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-2 disable',
						'type' => 'media',
					),
					'header_image_style' => array(
						'title' => esc_html__( 'Style', 'cws-essentials' ),
						'type' => 'radio',
						'addrowclasses' => 'grid-col-2 disable',
						'value' => array(
							'none' => array( esc_html__( 'Scroll', 'cws-essentials' ),  true, '' ),
							'fixed' => array( esc_html__( 'Fixed', 'cws-essentials' ),  false, '' ),
							'local' => array( esc_html__( 'Local', 'cws-essentials' ),  false, '' ),
						),
					),
					'header_image_position' => array(
						'title' => esc_html__( 'Background Position', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-2',
						'cols' => 3,
						'type' => 'radio',
						'value' => array(
							'tl'=>	array( '', false ),
							'tc'=>	array( '', false ),
							'tr'=>	array( '', false ),
							'cl'=>	array( '', false ),
							'cc'=>	array( '', true ),
							'cr'=>	array( '', false ),
							'bl'=>	array( '', false ),
							'bc'=>	array( '', false ),
							'br'=>	array( '', false ),
						),
					),						
					'header_image_size' => array(
						'title' => esc_html__( 'Size', 'cws-essentials' ),
						'type' => 'radio',
						'addrowclasses' => 'grid-col-2 disable',
						'value' => array(
							'initial' => array( esc_html__( 'Initial', 'cws-essentials' ),  false, '' ),
							'contain' => array( esc_html__( 'Contain', 'cws-essentials' ),  false, '' ),
							'cover' => array( esc_html__( 'Cover', 'cws-essentials' ),  true, '' ),
						),
					),						
					'header_image_repeat' => array(
						'title' => esc_html__( 'Reapeat', 'cws-essentials' ),
						'type' => 'radio',
						'addrowclasses' => 'grid-col-2 disable',
						'value' => array(
							'no-repeat' => array( esc_html__( 'No repeat', 'cws-essentials' ),  true, '' ),
							'repeat' => array( esc_html__( 'Repeat', 'cws-essentials' ),  false, '' ),
							'repeat-x' => array( esc_html__( 'Repeat X', 'cws-essentials' ),  false, '' ),
							'repeat-y' => array( esc_html__( 'Repeat Y', 'cws-essentials' ),  false, '' ),
						),
					),
					'header_color_overlay_type'	=> array(
						'title'		=> esc_html__( 'Color overlay', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-4 disable clear',
						'type'	=> 'select',
						'source'	=> array(
							'none' => array( esc_html__( 'None', 'cws-essentials' ),  true, 'd:header_color_overlay_opacity;d:header_overlayc;d:header_gradient_settings;' ),
							'color' => array( esc_html__( 'Color', 'cws-essentials' ),  false, 'e:header_color_overlay_opacity;e:header_overlayc;d:header_gradient_settings;' ),
							'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:header_color_overlay_opacity;d:header_overlayc;e:header_gradient_settings;' )
							),
						),
					'header_color_overlay_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'Opacity (%)', 'cws-essentials' ),
						'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
						'value' => '40',
						'addrowclasses' => 'grid-col-4 disable',
						),
					'header_overlayc'	=> array(
						'title'	=> esc_html__( 'Overlay color', 'cws-essentials' ),
						'atts' => 'data-default-color="' . AASANA_COLOR . '"',
						'addrowclasses' => 'grid-col-4 disable',
						'value' => AASANA_COLOR,
						'type'	=> 'text',
						),
					'header_gradient_settings' => array(
						'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'grid-col-12 disable groups',
						'layout' => array(
							'first_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'From', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
								),
							'second_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'To', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
								),
							'first_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
								'value' => '100',
								),
							'second_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
								'value' => '100',
								),
							'type' => array(
								'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-6',
								'value' => array(
									'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
									'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
									),
								),
							'linear_settings' => array(
								'title' => esc_html__( 'Linear settings', 'cws-essentials'  ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-6',
								'layout' => array(
									'angle' => array(
										'type' => 'number',
										'title' => esc_html__( 'Angle', 'cws-essentials' ),
										'value' => '45',
										),
									)
								),
							'radial_settings' => array(
								'title' => esc_html__( 'Radial settings', 'cws-essentials'  ),
								'type' => 'fields',
								'addrowclasses' => 'disable',
								'layout' => array(
									'shape_settings' => array(
										'title' => esc_html__( 'Shape', 'cws-essentials' ),
										'type' => 'radio',
										'value' => array(
											'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
											'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
											),
										),
									'shape' => array(
										'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
										'type' => 'radio',
										'value' => array(
											'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
											'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
											),
										),
									'size_keyword' => array(
										'type' => 'select',
										'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
										'addrowclasses' => 'disable',
										'source' => array(
											'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
											'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
											'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
											'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
											),
										),
									'size' => array(
										'type' => 'text',
										'addrowclasses' => 'disable',
										'title' => esc_html__( 'Size', 'cws-essentials' ),
										'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
										),
									)
								)
							)
						),



					'header_spacings' => array(
						'title' => esc_html__( 'Spacings', 'cws-essentials' ),
						'type' => 'margins',
						'addrowclasses' => 'disable grid-col-4 two-inputs clear',
						'value' => array(
							'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => ''),
							'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => ''),
						),
					),
					'override_menu_color'	=> array(
						'title'	=> esc_html__( 'Override Menu\'s Font Color', 'cws-essentials' ),
						'atts' => 'data-default-color="#ffffff"',
						'addrowclasses' => 'disable grid-col-4 clear',
						'value' => '#ffffff',
						'type'	=> 'text',
					),					
					'override_topbar_color'	=> array(
						'title'	=> esc_html__( 'Override Top Bar\'s Font Color', 'cws-essentials' ),
						'atts' => 'data-default-color="#ffffff"',
						'addrowclasses' => 'disable grid-col-4',
						'value' => '#ffffff',
						'type'	=> 'text',
					),
					'override_menu_border'	=> array(
						'title'	=> esc_html__( 'Override Menu\'s Border Color', 'cws-essentials' ),
						'atts' => 'data-default-color="#ffffff"',
						'addrowclasses' => 'disable grid-col-4',
						'value' => '#ffffff',
						'type'	=> 'text',
					),
				),
			),
			'tab2' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Logo', 'cws-essentials' ),
				'layout' => array(
					'customize_logo' => array(
						'title' => esc_html__( 'Customize', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:logo-margin;e:enable_logo;e:default_logo;e:logo-position;"',
						'type' => 'checkbox',
						'tooltip' => array(
							'title' => esc_html__( '(Override Theme Options)', 'cws-essentials' ),
							'content' => esc_html__( 'Sample text', 'cws-essentials' ),
						),
					),
					'enable_logo' => array(
						'title' => esc_html__( 'Logo', 'cws-essentials' ),
						'addrowclasses' => 'disable checkbox box alt',
						'atts' => 'checked',
						'type' => 'checkbox',
					),
					'default_logo'	=> array(
						'title'		=> esc_html__( 'Display Logo Variation', 'cws-essentials' ),
						'addrowclasses' => 'disable box',
						'type'	=> 'select',
						'source'	=> array(
							'dark' => array( esc_html__( 'Dark', 'cws-essentials' ),  true, '' ),
							'light' => array( esc_html__( 'Light', 'cws-essentials' ),  false, '' ),
						),
					),
					'logo-position' => array(
						'title' => esc_html__( 'Position', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'addrowclasses' => 'disable box',
						'value' => array(
							'left' => array( esc_html__( 'Left', 'cws-essentials' ), true, 'd:logo_box_color_overlay_type;d:site_name_in_menu;e:logo_with_site_name;d:logo_box', '/img/align-left.png' ),
							'center' =>array( esc_html__( 'Center', 'cws-essentials' ), false, 'e:logo_box_color_overlay_type;e:site_name_in_menu;e:logo_with_site_name;e:logo_box;', '/img/align-center.png', ),
							'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false, 'd:logo_box_color_overlay_type;d:site_name_in_menu;e:logo_with_site_name;d:logo_box', '/img/align-right.png', ),
							'in-menu' =>array( esc_html__( 'Inside', 'cws-essentials' ), false, 'd:logo_box_color_overlay_type;d:site_name_in_menu;e:logo_with_site_name;d:logo_box', '/img/align-inner.png', ),
						),
					),
					'logo_box_color_overlay_type'	=> array(
						'title'		=> esc_html__( 'Color overlay', 'cws-essentials' ),
						'addrowclasses' => 'disable box',
						'type'	=> 'select',
						'source'	=> array(
							'none' => array( esc_html__( 'None', 'cws-essentials' ),  false, 'd:logo_box_color_overlay_opacity;d:logo_box_overlay_color;d:logo_box_gradient_settings;' ),
							'color' => array( esc_html__( 'Color', 'cws-essentials' ),  true, 'e:logo_box_color_overlay_opacity;e:logo_box_overlay_color;d:logo_box_gradient_settings;' ),
							'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:logo_box_color_overlay_opacity;d:logo_box_overlay_color;e:logo_box_gradient_settings;' )
						),
					),
					'logo_box_color_overlay_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'Opacity (%)', 'cws-essentials' ),
						'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
						'value' => '100',
						'addrowclasses' => 'disable box',
					),
					'logo_box_overlay_color'	=> array(
						'title'	=> esc_html__( 'Add Background Color Overlay to the Logo Area', 'cws-essentials' ),
						'atts' => 'data-default-color="#fafafa"',
						'addrowclasses' => 'disable box',
						'value' => '#fafafa',
						'type'	=> 'text',
					),
					'logo_box_gradient_settings' => array(
						'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'disable box inside-box groups',
						'layout' => array(
							'first_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'From', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
							),
							'second_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'To', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
							),
							'first_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
								'value' => '100',
							),
							'second_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
								'value' => '100',
							),
							'type' => array(
								'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-6',
								'value' => array(
									'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
									'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
								),
							),
							'linear_settings' => array(
								'title' => esc_html__( 'Linear settings', 'cws-essentials'  ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-6',
								'layout' => array(
									'angle' => array(
										'type' => 'number',
										'title' => esc_html__( 'Angle', 'cws-essentials' ),
										'value' => '45',
									),
								)
							),
							'radial_settings' => array(
								'title' => esc_html__( 'Radial settings', 'cws-essentials'  ),
								'type' => 'fields',
								'addrowclasses' => 'disable',
								'layout' => array(
									'shape_settings' => array(
										'title' => esc_html__( 'Shape', 'cws-essentials' ),
										'type' => 'radio',
										'value' => array(
											'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
											'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
										),
									),
									'shape' => array(
										'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
										'type' => 'radio',
										'value' => array(
											'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
											'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
										),
									),
									'size_keyword' => array(
										'type' => 'select',
										'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
										'addrowclasses' => 'disable',
										'source' => array(
											'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
											'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
											'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
											'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
										),
									),
									'size' => array(
										'type' => 'text',
										'addrowclasses' => 'disable',
										'title' => esc_html__( 'Size', 'cws-essentials' ),
										'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
									),
								)
							)
						)
					),
					'logo_box' => array(
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-12',
						'layout' => array(
							'border'	=> array(
								'title'		=> esc_html__( 'Border', 'cws-essentials' ),
								'addrowclasses' => 'box',
								'type'	=> 'select',
								'atts' => 'multiple data-none="d:border_color;d:border_type"',
								'source'	=> array(
									'top' => array( esc_html__( 'Top', 'cws-essentials' ),  false, 'e:border_color;e:border_type;' ),
									'bottom' => array( esc_html__( 'Bottom', 'cws-essentials' ), true, 'e:border_color;e:border_type;' ),
								),
							),
							'border_type'	=> array(
								'title'		=> esc_html__( 'Border type', 'cws-essentials' ),
								'addrowclasses' => 'box',
								'type'	=> 'select',
								'source'	=> array(
									'dotted' => array( esc_html__( 'Dotted', 'cws-essentials' ),  false, '' ),
									'dashed' => array( esc_html__( 'Dashed', 'cws-essentials' ),  false, '' ),
									'solid' => array( esc_html__( 'Solid', 'cws-essentials' ), true, '' ),
								),
							),
							'border_color'	=> array(
								'title'	=> esc_html__( 'Border color', 'cws-essentials' ),
								'atts' => 'data-default-color="#e6e6e6"',
								'addrowclasses' => 'box',
								'value' => '#e6e6e6',
								'type'	=> 'text',
							),
						),
					),
					'logo-margin' => array(
						'title' => esc_html__( 'Margins (px)', 'cws-essentials' ),
						'type' => 'margins',
						'addrowclasses' => 'box',
						'value' => array(
							'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '12'),
							'right' => array('placeholder' => esc_html__( 'Right', 'cws-essentials' ), 'value' => ''),
							'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '12'),
							'left' => array('placeholder' => esc_html__( 'Left', 'cws-essentials' ), 'value' => ''),
						),
					),
					'logo_with_site_name' => array(
						'title' => esc_html__( 'Add Site Name to the Logo', 'cws-essentials' ),
						'addrowclasses' => 'disable checkbox',
						'type' => 'checkbox',
					),
					'site_name_in_menu' => array(
						'title' => esc_html__( 'Insert Site Name into Menu', 'cws-essentials' ),
						'addrowclasses' => 'disable checkbox',
						'type' => 'checkbox',
					),
				),
			),
			'tab3' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Menu', 'cws-essentials' ),
				'layout' => array(
					'customize_menu' => array(
						'title' => esc_html__( 'Customize', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:enable_menu_mb;e:menu-position;e:value;e:left;e:search_place;e:value;e:none;e:top;e:mobile_menu_place;e:value;e:header_outside_slider_bg_color;e:header_outside_slider_bg_opacity;e:tooltip;e:menu_margin;e:mobile_background;e:value;e:menu_box;e:enable_mob_menu;e:show_header_outside_slider;e:show_sandwich_menu;e:show_header_slider_bg_color;e:menu_font_color;e:menu_overide_color"',
						'type' => 'checkbox',
					),
					'enable_menu_mb' => array(
						'title' => esc_html__( 'Menu', 'cws-essentials' ),
						'addrowclasses' => 'checkbox grid-col-12 disable alt',
						'type' => 'checkbox',
						'atts' => 'checked',
					),
					'menu-position' => array(
						'title' => esc_html__( 'Menu Alignment', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'addrowclasses' => 'grid-col-4 disable',
						'value' => array(
							'left' => array( esc_html__( 'Left', 'cws-essentials' ), 	true, '', get_template_directory_uri() . '/img/fw_img/align-left.png' ),
							'center' =>array( esc_html__( 'Center', 'cws-essentials' ), false, '', get_template_directory_uri() . '/img/fw_img/align-center.png' ),
							'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false, '', get_template_directory_uri() . '/img/fw_img/align-right.png' ),
						),
					),
					'search_place' => array(
						'title' => esc_html__( 'Search Icon Location', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'addrowclasses' => 'grid-col-4 disable',
						'value' => array(
							'none' => array( esc_html__( 'None', 'cws-essentials' ), 	false, '', get_template_directory_uri() . '/img/fw_img/no_layout.png' ),
							'top' => array( esc_html__( 'Top', 'cws-essentials' ), 	false, '', get_template_directory_uri() . '/img/fw_img/search-social-right.png' ),
							'left' =>array( esc_html__( 'Left', 'cws-essentials' ), false, '', get_template_directory_uri() . '/img/fw_img/search-menu-left.png' ),
							'right' =>array( esc_html__( 'Right', 'cws-essentials' ), true, '', get_template_directory_uri() . '/img/fw_img/search-menu-right.png' ),
						),
					),
					'mobile_menu_place' => array(
						'title' => esc_html__( 'Mobile Menu Location', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'addrowclasses' => 'grid-col-4 disable',
						'value' => array(
							'left' =>array( esc_html__( 'Left', 'cws-essentials' ), true, '', get_template_directory_uri() . '/img/fw_img/hamb-left.png' ),
							'center' =>array( esc_html__( 'Center', 'cws-essentials' ), false, '', get_template_directory_uri() . '/img/fw_img/hamb-center.png' ),
							'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false, '', get_template_directory_uri() . '/img/fw_img/hamb-right.png' ),
						),
					),
					'show_header_slider_bg_color' => array(
						'title' => esc_html__( 'Add Background Color', 'cws-essentials' ),
						'addrowclasses' => 'checkbox disable',
						'type' => 'checkbox',
						'atts' => 'data-options="e:header_outside_slider_bg_color;e:header_outside_slider_bg_opacity"',
					),
					'header_outside_slider_bg_color' => array(
						'tooltip' => array(
							'title' => esc_html__( 'Background Color', 'cws-essentials' ),
							'content' => esc_html__( 'This color is applied to header section including top bar.', 'cws-essentials' ),
						),
						'atts' => 'data-default-color="#ffffff"',
						'value' => '#ffffff',
						'addrowclasses' => 'grid-col-4 disable',
						'type' => 'text',
					),
					'header_outside_slider_bg_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'Opacity', 'cws-essentials' ),
						'addrowclasses' => 'disable',
						'tooltip' => array(
							'title' => esc_html__( 'Header Opacity', 'cws-essentials' ),
							'content' => esc_html__( 'This option will apply the transparent header when set to "0". <u>(Depends on "Header hovers slider" or "Customize header")</u>', 'cws-essentials' ),
						),
						'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-4',
						'value' => '30'
					),
					'menu_margin' => array(
						'title' => esc_html__( 'Spacings', 'cws-essentials' ),
						'type' => 'margins',
						'addrowclasses' => 'grid-col-4 two-inputs disable',
						'value' => array(
							'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '12'),
							'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '12'),
						),
					),
					'mobile_background'	=> array(
						'title'		=> esc_html__( 'Mobile Background Color', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-4 disable clear',
						'type'	=> 'select',
						'source'	=> array(
							'default' => array( esc_html__( 'Default', 'cws-essentials' ),  true, 'd:mobile_overlay_opacity;d:mobile_overlayc;d:mobile_gradient_settings;' ),
							'color' => array( esc_html__( 'Color', 'cws-essentials' ),  false, 'e:mobile_overlay_opacity;e:mobile_overlayc;d:mobile_gradient_settings;' ),
							'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:mobile_overlay_opacity;d:mobile_overlayc;e:mobile_gradient_settings;' )
							),
						),
					'mobile_overlayc'	=> array(
						'title'	=> esc_html__( 'Color', 'cws-essentials' ),
						'atts' => 'data-default-color="' . AASANA_COLOR . '"',
						'addrowclasses' => 'grid-col-4 disable',
						'value' => AASANA_COLOR,
						'type'	=> 'text',
						),
					'mobile_overlay_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'Opacity (%)', 'cws-essentials' ),
						'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
						'value' => '100',
						'addrowclasses' => 'grid-col-4 disable',
						),

					'mobile_gradient_settings' => array(
						'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'grid-col-12 disable groups',
						'layout' => array(
							'first_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'From', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
								),
							'second_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'To', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
								),
							'first_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
								'value' => '100',
								),
							'second_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
								'value' => '100',
								),
							'type' => array(
								'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-6',
								'value' => array(
									'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
									'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
									),
								),
							'linear_settings' => array(
								'type' => 'fields',
								'addrowclasses' => 'grid-col-6 disable',
								'layout' => array(
									'angle' => array(
										'type' => 'number',
										'title' => esc_html__( 'Angle', 'cws-essentials' ),
										'value' => '45',
										),
									)
								),
							'radial_settings' => array(
								'type' => 'fields',
								'addrowclasses' => 'grid-col-8 disable',
								'layout' => array(
									'shape_settings' => array(
										'title' => esc_html__( 'Shape', 'cws-essentials' ),
										'addrowclasses' => 'grid-col-4',
										'type' => 'radio',
										'value' => array(
											'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
											'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
											),
										),
									'shape' => array(
										'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
										'addrowclasses' => 'grid-col-4',
										'type' => 'radio',
										'value' => array(
											'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
											'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
											),
										),
									'size_keyword' => array(
										'type' => 'select',
										'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
										'addrowclasses' => 'grid-col-4 disable',
										'source' => array(
											'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
											'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
											'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
											'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
											),
										),
									'size' => array(
										'type' => 'text',
										'addrowclasses' => 'grid-col-4 disable',
										'title' => esc_html__( 'Size', 'cws-essentials' ),
										'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
										),
									)
								)
							)
						),	
					'enable_mob_menu' => array(
						'title' => esc_html__( 'Enable mobile menu on all touch devices', 'cws-essentials' ),
						'addrowclasses' => 'checkbox grid-col-12 disable',
						'atts' => 'checked',
						'type' => 'checkbox',
					),
					'show_header_outside_slider' => array(
						'title' => esc_html__( 'Header overlays slider', 'cws-essentials' ),
						'addrowclasses' => 'checkbox grid-col-12 disable',
						'type' => 'checkbox',
					),
					'show_sandwich_menu' => array(
						'title' => esc_html__( 'Use mobile menu on desktop PCs', 'cws-essentials' ),
						'addrowclasses' => 'checkbox grid-col-12 disable',
						'type' => 'checkbox',
					),
					'menu_font_color' => array(
						'type' => 'text',
						'title' => esc_html__( 'Override Font color', 'cws-essentials' ),
						'atts' => 'data-default-color="#595959"',
						'value' => '#595959',
						'addrowclasses' => 'grid-col-4',
					),						
					'menu_overide_color' => array(
						'type' => 'text',
						'title' => esc_html__( 'Override Border color', 'cws-essentials' ),
						'atts' => 'data-default-color="#595959"',
						'value' => '#595959',
						'addrowclasses' => 'grid-col-4',
					),
				),
			),
			'tab4' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Title area', 'cws-essentials' ),
				'layout' => array(
					'customize_title' => array(
						'title' => esc_html__( 'Customize', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:title_area_switcher"',
						'type' => 'checkbox',
					),
					'title_area_switcher' => array(
						'title' => esc_html__('Enable title area', 'cws-essentials' ),
						'addrowclasses' => 'disable checkbox alt',
						'atts' => 'data-options="e:header_box;e:slide_down_header;e:animate_title"',
						'type' => 'checkbox',

					),
					'slide_down_header' => array(
						'title' => esc_html__( 'Slide down Header Image on Page Load', 'cws-essentials' ),
						'addrowclasses' => 'disable checkbox box',
						'type' => 'checkbox',
					),
					'animate_title' => array(
						'title' => esc_html__( 'Add Header Animation on Mouse Scroll', 'cws-essentials' ),
						'addrowclasses' => 'disable checkbox box',
						'type' => 'checkbox',
						'tooltip' => array(
							'title' => esc_html__( 'Documentation', 'cws-essentials' ),
							'content' => esc_html__( 'Project on https://github.com/Prinzhorn/skrollr <a href="https://github.com/Prinzhorn/skrollr">GitHub</a>', 'cws-essentials' ),
						),
						'atts' => 'checked data-options="e:animate_options;"',
					),
					'animate_options' => array(
						'type' => 'group',
						'addrowclasses' => 'disable group expander box',
						'title' => esc_html__('Animation Steps', 'cws-essentials' ),
						'button_title' => esc_html__('Add New Step', 'cws-essentials' ),
						'layout' => array(
							'element'	=> array(
								'title'		=> esc_html__( 'Header Section', 'cws-essentials' ),
								'type'	=> 'select',
								'source'	=> array(
									'title' => array( esc_html__( 'Title & Breadcrumbs', 'cws-essentials' ),  true, '' ),
									'container' => array( esc_html__( 'Content Width', 'cws-essentials' ),  false, '' ),
									'section' => array( esc_html__( 'Full Width', 'cws-essentials' ), false, '' )
								),
							),
							'value' => array(
								'type' => 'number',
								'atts' => 'data-role="title"',
								'value' => '100',
								'title' => esc_html__('Top offset (in px)', 'cws-essentials' ),
							),
							'styles' => array(
								'type' => 'textarea',
								'atts' => 'rows="5"',
								'title' => esc_html__('Styles CSS', 'cws-essentials' ),
							),
						),
					),
					'header_box' => array(
						'title' => esc_html__( 'Border box\'s settings', 'cws-essentials'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-12',
						'layout' => array(
							'font_color' => array(
								'title'	=> esc_html__( 'Font Color', 'cws-essentials' ),
								'atts' => 'data-default-color="#ffffff"',
								'value' => '#ffffff',
								'addrowclasses' => 'grid-col-12',
								'type'	=> 'text',
							),
							'image' => array(
								'title' => esc_html__( 'Title area image', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-12',
								'type' => 'media',
							),
							'border'	=> array(
								'title'		=> esc_html__( 'Border', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-4',
								'type'	=> 'select',
								'atts' => 'multiple data-none="d:border_color;d:border_type"',
								'source'	=> array(
									'top' => array( esc_html__( 'Top', 'cws-essentials' ),  false, 'e:border_color;e:border_type;' ),
									'bottom' => array( esc_html__( 'Bottom', 'cws-essentials' ), true, 'e:border_color;e:border_type;' ),
								),
							),
							'border_type'	=> array(
								'title'		=> esc_html__( 'Border type', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-4',
								'type'	=> 'select',
								'source'	=> array(
									'dotted' => array( esc_html__( 'Dotted', 'cws-essentials' ),  false, '' ),
									'dashed' => array( esc_html__( 'Dashed', 'cws-essentials' ),  false, '' ),
									'solid' => array( esc_html__( 'Solid', 'cws-essentials' ), true, '' ),
								),
							),
							'border_color'	=> array(
								'title'	=> esc_html__( 'Border color', 'cws-essentials' ),
								'atts' => 'data-default-color="#e6e6e6"',
								'addrowclasses' => 'grid-col-4',
								'value' => '#e6e6e6',
								'type'	=> 'text',
							),
							'spacings' => array(
								'title' => esc_html__( 'Spacings (px)', 'cws-essentials' ),
								'type' => 'margins',
								'addrowclasses' => 'grid-col-12 two-inputs',
								'value' => array(
									'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '120'),
									'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '120'),
								),
							),
							'color_overlay_type'	=> array(
								'title'		=> esc_html__( 'Color Overlay', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-12',
								'type'	=> 'select',
								'source'	=> array(
									'none' => array( esc_html__( 'None', 'cws-essentials' ),  true, 'd:color_overlay_opacity;d:overlay_color;d:gradient_settings;' ),
									'color' => array( esc_html__( 'Color', 'cws-essentials' ),  false, 'e:color_overlay_opacity;e:overlay_color;d:gradient_settings;' ),
									'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:color_overlay_opacity;d:overlay_color;e:gradient_settings;' )
								),
							),
							'color_overlay_opacity' => array(
								'type' => 'number',
								'addrowclasses' => 'disable grid-col-12',
								'title' => esc_html__( 'Opacity', 'cws-essentials' ),
								'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
								'value' => '40'
							),
							'overlay_color'	=> array(
								'title'	=> esc_html__( 'Overlay color', 'cws-essentials' ),
								'atts' => 'data-default-color="' . AASANA_COLOR . '"',
								'addrowclasses' => 'disable grid-col-12',
								'value' => AASANA_COLOR,
								'type'	=> 'text',
								'customizer' 	=> array( 'show' => false )
							),
							'gradient_settings' => array(
								'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
								'type' => 'fields',
								'addrowclasses' => 'disable box inside-box groups grid-col-12',
								'customizer' 	=> array( 'show' => false ),
								'layout' => array(
									'first_color' => array(
										'type' => 'text',
										'title' => esc_html__( 'From', 'cws-essentials' ),
										'atts' => 'data-default-color=""',
									),
									'second_color' => array(
										'type' => 'text',
										'title' => esc_html__( 'To', 'cws-essentials' ),
										'atts' => 'data-default-color=""',
									),
									'first_color_opacity' => array(
										'type' => 'number',
										'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
										'value' => '100',
									),
									'second_color_opacity' => array(
										'type' => 'number',
										'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
										'value' => '100',
									),
									'type' => array(
										'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
										'type' => 'radio',
										'addrowclasses' => 'grid-col-6',
										'value' => array(
											'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
											'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
										),
									),
									'linear_settings' => array(
										'title' => esc_html__( 'Linear settings', 'cws-essentials'  ),
										'type' => 'fields',
										'addrowclasses' => 'disable grid-col-6',
										'layout' => array(
											'angle' => array(
												'type' => 'number',
												'title' => esc_html__( 'Angle', 'cws-essentials' ),
												'value' => '45',
											),
										)
									),
									'radial_settings' => array(
										'title' => esc_html__( 'Radial settings', 'cws-essentials'  ),
										'type' => 'fields',
										'addrowclasses' => 'disable',
										'layout' => array(
											'shape_settings' => array(
												'title' => esc_html__( 'Shape', 'cws-essentials' ),
												'type' => 'radio',
												'value' => array(
													'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
													'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
												),
											),
											'shape' => array(
												'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
												'type' => 'radio',
												'value' => array(
													'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
													'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
												),
											),
											'size_keyword' => array(
												'type' => 'select',
												'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
												'addrowclasses' => 'disable',
												'source' => array(
													'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
													'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
													'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
													'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
												),
											),
											'size' => array(
												'type' => 'text',
												'addrowclasses' => 'disable',
												'title' => esc_html__( 'Size', 'cws-essentials' ),
												'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
											),
										)
									),
								)
							),
							'hide_divider'	=> array(
								'title'	=> esc_html__( 'Hide Divider', 'cws-essentials' ),
								'type'	=> 'checkbox',
								'addrowclasses' => 'checkbox grid-col-12'
							),	
							'breadcrumbs_divider' => array(
								'title' => esc_html__( 'Breadcrumbs Divider', 'cws-essentials' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'grid-col-12',
								// 'value' => array( 'id' => '', 'src' => get_template_directory_uri() . '\img\logo_the8_128x128.png') ,
								'layout' => array(
									'is_high_dpi' => array(
										'title' => esc_html__( 'High-Resolution logo', 'cws-essentials' ),
										'addrowclasses' => 'checkbox',
										'type' => 'checkbox',
									),
								),
							),
							'breadcrumbs_dimensions' => array(
								'title' => esc_html__( 'Breadcrumbs Divider Dimensions', 'cws-essentials' ),
								'type' => 'dimensions',
								'addrowclasses' => 'grid-col-12',
								'value' => array(
									'width' => array('placeholder' => esc_html__( 'Width', 'cws-essentials' ), 'value' => ''),
									'height' => array('placeholder' => esc_html__( 'Height', 'cws-essentials' ), 'value' => ''),
								),
							),
							'breadcrumbs-margin' => array(
								'title' => esc_html__( 'Margins (px)', 'cws-essentials' ),
								'type' => 'margins',
								'addrowclasses' => 'grid-col-4',
								'value' => array(
									'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '0'),
									'left' => array('placeholder' => esc_html__( 'left', 'cws-essentials' ), 'value' => '0'),
									'right' => array('placeholder' => esc_html__( 'Right', 'cws-essentials' ), 'value' => '0'),
									'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '0'),
									),
							),
							'use_blur' => array(
								'title' => esc_html__( 'Apply blur', 'cws-essentials' ),
								'addrowclasses' => 'checkbox grid-col-12',
								'type' => 'checkbox',
								'atts' => 'data-options="e:blur_intensity;"',
							),
							'blur_intensity' => array(
								'type' => 'number',
								'addrowclasses' => 'disable grid-col-12',
								'title' => esc_html__( 'Intensity', 'cws-essentials' ),
								'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
								'value' => '8'
							),
							'effect' => array(
								'title' => esc_html__( 'Image style', 'cws-essentials' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-12',
								'value' => array(
									'none' => array( esc_html__( 'Scroll', 'cws-essentials'), true, 'd:parallax_options;d:scroll_parallax;' ),
									'fixed' => array( esc_html__( 'Fixed', 'cws-essentials'), false, 'd:parallax_options;d:scroll_parallax;' ),
									'scroll_parallax' => array( esc_html__( 'Scroll Parallax', 'cws-essentials' ), false, 'd:parallax_options;e:scroll_parallax;' ),
									'parallaxify' =>array( esc_html__( 'Parallaxify', 'cws-essentials' ), false, 'e:parallax_options;d:scroll_parallax;' ),
								),
							),
							'scroll_parallax' => array(
								'title' => esc_html__( 'Add Motion Zoom to Header Image on Page Scroll', 'cws-essentials' ),
								'addrowclasses' => 'disable checkbox grid-col-12',
								'type' => 'checkbox',
							),
							'parallax_options' => array(
								'title' => esc_html__( 'Parallax options', 'cws-essentials' ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-12 groups',
								'layout' => array(
									'scalar_x' => array(
										'type' => 'number',
										'title' => esc_html__( 'x-axis parallax intensity', 'cws-essentials' ),
										'placeholder' => esc_html__( 'Integer', 'cws-essentials' ),
										'value' => '2'
									),
									'scalar_y' => array(
										'type' => 'number',
										'title' => esc_html__( 'y-axis parallax intensity', 'cws-essentials' ),
										'placeholder' => esc_html__( 'Integer', 'cws-essentials' ),
										'value' => '2'
									),
									'limit_x' => array(
										'type' => 'number',
										'title' => esc_html__( 'Maximum x-axis shift', 'cws-essentials' ),
										'placeholder' => esc_html__( 'Integer', 'cws-essentials' ),
										'value' => '15'
									),
									'limit_y' => array(
										'type' => 'number',
										'title' => esc_html__( 'Maximum y-axis shift', 'cws-essentials' ),
										'placeholder' => esc_html__( 'Integer', 'cws-essentials' ),
										'value' => '15'
									),
								),
							),
						),
					),
				),
			),
			'tab5' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Top bar', 'cws-essentials' ),
				'layout' => array(
					'customize_topbar' => array(
						'title' => esc_html__( 'Apply "Top bar" Options', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'type' => 'checkbox',
						'tooltip' => array(
							'title' => esc_html__( '(Override Theme Options)', 'cws-essentials' ),
							'content' => esc_html__( 'Sample text', 'cws-essentials' ),
						),
						'atts' => 'data-options="e:top_bar_wide;e:top_panel_switcher;e:show_top_bar_menu;e:top_bar_menu_position;e:top_bar_bg_color;e:top_bar_bg_opacity;e:top_bar_font_color;e:top_bar_spacings;e:top_bar_border"',
					),
					'top_panel_switcher' => array(
						'title' => esc_html__( 'Switch on/off the top bar', 'cws-essentials' ),
						'addrowclasses' => 'checkbox box disable',
						'atts' => 'checked',
						'type' => 'checkbox',
					),					
					'top_bar_wide' => array(
						'title' => esc_html__( 'Wide top bar', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12 checkbox box disable',
						'type' => 'checkbox',
					),
					'show_top_bar_menu' => array(
						'title' => esc_html__( 'Show top bar menu', 'cws-essentials' ),
						'addrowclasses' => 'checkbox box disable',
						'atts' => 'data-options="e:top_bar_menu_position;"',
						'type' => 'checkbox',
					),

					'top_bar_menu_position' => array(
						'title' => esc_html__( 'TopBar menu position', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'addrowclasses' => 'disable  box',
						'value' => array(
							'left' => array( esc_html__( 'Left', 'cws-essentials' ), 	true, '', '/img/align-left.png' ),
							'center' =>array( esc_html__( 'Center', 'cws-essentials' ), false, '', '/img/align-center.png' ),
							'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false, '', '/img/align-right.png' ),
						),
					),
					'top_bar_bg_color' => array(
						'title' => esc_html__( 'Background color', 'cws-essentials' ),
						'atts' => 'data-default-color="#ffffff"',
						'value' => '#fafafa',
						'addrowclasses' => 'box disable',
						'type' => 'text',
					),
					'top_bar_bg_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'Opacity (%)', 'cws-essentials' ),
						'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
						'value' => '20',
						'addrowclasses' => 'box disable',
					),
					'top_bar_font_color' => array(
						'title' => esc_html__( 'Font color', 'cws-essentials' ),
						'atts' => 'data-default-color="#b3b3b3"',
						'value' => '#b3b3b3',
						'addrowclasses' => 'box disable',
						'type' => 'text',
					),
					'top_bar_spacings' => array(
						'title' => esc_html__( 'Spacings (px)', 'cws-essentials' ),
						'type' => 'margins',
						'addrowclasses' => 'two-inputs box disable',
						'value' => array(
							'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '18'),
							'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '18'),
						),
					),
					'top_bar_border'	=> array(
						'title'		=> esc_html__( 'Border', 'cws-essentials' ),
						'addrowclasses' => 'box disable',
						'type'	=> 'select',
						'source'	=> array(
							'none' => array( esc_html__( 'None', 'cws-essentials' ),  false, 'd:top_bar_border_color;d:top_bar_border_type;' ),
							'top' => array( esc_html__( 'Top', 'cws-essentials' ),  false, 'e:top_bar_border_color;e:top_bar_border_type;' ),
							'bottom' => array( esc_html__( 'Bottom', 'cws-essentials' ), true, 'e:top_bar_border_color;e:top_bar_border_type;' ),
							'both' => array( esc_html__( 'Top & Bottom', 'cws-essentials' ), false, 'e:top_bar_border_color;e:top_bar_border_type;' )
						),
					),
					'top_bar_border_type'	=> array(
						'title'		=> esc_html__( 'Border type', 'cws-essentials' ),
						'addrowclasses' => 'box disable',
						'type'	=> 'select',
						'source'	=> array(
							'dotted' => array( esc_html__( 'Dotted', 'cws-essentials' ),  false, '' ),
							'dashed' => array( esc_html__( 'Dashed', 'cws-essentials' ),  false, '' ),
							'solid' => array( esc_html__( 'Solid', 'cws-essentials' ), true, '' ),
						),
					),
					'top_bar_border_color'	=> array(
						'title'	=> esc_html__( 'Border color', 'cws-essentials' ),
						'atts' => 'data-default-color="#e6e6e6"',
						'addrowclasses' => 'disable box',
						'value' => '#e6e6e6',
						'type'	=> 'text',
					),

				),
			),
			'tab6' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Footer', 'cws-essentials' ),
				'layout' => array(
					'customize_footer' => array(
						'type' => 'checkbox',
						'title' => esc_html__( 'Apply "Footer" Options', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:footer;"',
					),
					'footer' => array(
						'title' => esc_html__( 'Footer Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-12 groups',
						'layout' => array(
							'footer_sidebar' => array(
								'title' 		=> esc_html__('Footer sidebar', 'cws-essentials' ),
								'type' 			=> 'select',
								'addrowclasses' => 'grid-col-6',
								'tooltip' => array(
									'title' => esc_html__( 'Footer area', 'cws-essentials' ),
									'content' => esc_html__( 'This options will set the default Footer widget area, unless you override it on each page', 'cws-essentials' ),
								),
								'source' 		=> 'sidebars',
							),
							'footer_layout' => array(
								'type' => 'select',
								'title' => esc_html__( 'Footer layout', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-6',
								'source' => array(
									'1' => array( esc_html__( '1/1 Column', 'cws-essentials' ),  false ),
									'2' => array( esc_html__( '2/2 Column', 'cws-essentials' ), false ),
									'3' => array( esc_html__( '3/3 Column', 'cws-essentials' ), false ),
									'4' => array( esc_html__( '4/4 Column', 'cws-essentials' ), false ),
									'two-three' => array( esc_html__( '2/3 + 1/3 Column', 'cws-essentials' ), false ),
									'one-two' => array( esc_html__( '1/3 + 2/3 Column', 'cws-essentials' ), false ),
									'one-three' => array( esc_html__( '1/4 + 3/4 Column', 'cws-essentials' ), false ),
									'one-one-two' => array( esc_html__( '1/4 + 1/4 + 2/4 Column', 'cws-essentials' ), false ),
									'two-one-one' => array( esc_html__( '2/4 + 1/4 + 1/4 Column', 'cws-essentials' ), true ),
									'one-two-one' => array( esc_html__( '1/4 + 2/4 + 1/4 Column', 'cws-essentials' ), false ),
								),
							),
							'footer_sidebar_position' => array(
								'title' => esc_html__( 'Footer SideBar position', 'cws-essentials' ),
								'type' => 'radio',
								'subtype' => 'images',
								'addrowclasses' => 'grid-col-12',
								'value' => array(
									'left' => array( esc_html__( 'Left', 'cws-essentials' ), 	false, '', '/img/align-left.png' ),
									'center' =>array( esc_html__( 'Center', 'cws-essentials' ), true, '', '/img/align-center.png' ),
									'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false, '', '/img/align-right.png' ),
								),
							),
							'footer_text_alignment' => array(
								'type' => 'select',
								'title' => esc_html__( 'Footer Text Alignment', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-6',
								'source' => array(
									'left' => array( esc_html__( 'Left', 'cws-essentials' ), 	false, '' ), 
									'center' => array( esc_html__( 'Center', 'cws-essentials' ), 	false, '' ),
									'right' => array( esc_html__( 'Right', 'cws-essentials' ), 	false, '' ), 
								),
							),
							'footer_spacings' => array(
								'title' => esc_html__( 'Spacings', 'cws-essentials' ),
								'type' => 'margins',
								'addrowclasses' => 'grid-col-4 two-inputs',
								'value' => array(
									'top' => array('placeholder' => esc_html__( 'Top', 'cws-essentials' ), 'value' => '40'),
									'bottom' => array('placeholder' => esc_html__( 'Bottom', 'cws-essentials' ), 'value' => '50'),
								),
							),
							'footer_copyrights_text' => array(
								'title' => esc_html__( 'Footer Copyrights content', 'cws-essentials' ),
								'type' => 'textarea',
								'addrowclasses' => 'grid-col-12',
								'value' => 'Copyrights',
								'atts' => 'rows="6"',
							),
							'show_copyrights_menu' => array(
								'title' => esc_html__( 'Show Copyrights menu', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-12 checkbox',
								'atts' => 'data-options="e:copyrights_menu_position;"',
								'type' => 'checkbox',
							),
							'copyrights_menu_position' => array(
								'title' => esc_html__( 'Menu position', 'cws-essentials' ),
								'type' => 'radio',
								'subtype' => 'images',
								'addrowclasses' => 'disable grid-col-12',
								'value' => array(
									'left' => array( esc_html__( 'Left', 'cws-essentials' ), 	true, '', '/img/align-left.png' ),
									'center' =>array( esc_html__( 'Center', 'cws-essentials' ), false, '', '/img/align-center.png' ),
									'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false, '', '/img/align-right.png' ),
								),
							),
							'footer_fixed_style' => array(
								'title' => esc_html__( 'Fixed style', 'cws-essentials' ),
								'addrowclasses' => 'checkbox grid-col-12 alt',
								'type' => 'checkbox',
							),
							'add_footer_bg_img' => array(
								'title' => esc_html__( 'Add Footer Image', 'cws-essentials' ),
								'addrowclasses' => 'checkbox alt grid-col-6',
								'type' => 'checkbox',
								'atts' => 'data-options="e:footer_img_settings;"',
							),
							'footer_img_settings' => array(
								'title' => esc_html__( 'Footer Image Settings', 'cws-essentials' ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-12 groups',
								'layout' => array(
									'footer_bg_im' => array(
										'title' => esc_html__( 'Footer Bg Image', 'cws-essentials' ),
										'type' => 'media',
										'url-atts' => 'readonly',
									),
									'footer_img_pos_x'	=> array(
										'title'		=> esc_html__( 'Footer Img Position x', 'cws-essentials' ),
										'type'	=> 'select',
										'source'	=> array(
											'left' => array( esc_html__( 'left', 'cws-essentials' ),  true ),
											'right' => array( esc_html__( 'right', 'cws-essentials' ), false ),
											'center' => array( esc_html__( 'center', 'cws-essentials' ), false ),
										),
									),
									'footer_img_pos_y'	=> array(
										'title'		=> esc_html__( 'Footer Img Position y', 'cws-essentials' ),
										'type'	=> 'select',
										'source'	=> array(
											'top' => array( esc_html__( 'top', 'cws-essentials' ),  true ),
											'center' => array( esc_html__( 'center', 'cws-essentials' ), false ),
											'bottom' => array( esc_html__( 'bottom', 'cws-essentials' ), false ),
										),
									),
									'footer_img_repeat'	=> array(
										'title'		=> esc_html__( 'Footer Img Repeat', 'cws-essentials' ),
										'type'	=> 'select',
										'source'	=> array(
											'no-repeat' => array( esc_html__('no repeat', 'cws-essentials' ), true ),
											'repeat' => array( esc_html__('repeat', 'cws-essentials' ), false ),
											'repeat-x' => array( esc_html__('repeat x', 'cws-essentials' ), false ),
											'repeat-y' => array( esc_html__('repeat y', 'cws-essentials' ), false ),
										),
									),
									'footer_img_size'	=> array(
										'title'		=> esc_html__( 'Footer Img Size', 'cws-essentials' ),
										'type'	=> 'select',
										'source'	=> array(
											'auto' => array( esc_html__('auto', 'cws-essentials' ),  true ),
											'cover' => array( esc_html__('cover', 'cws-essentials' ), false ),
											'container' => array( esc_html__('container', 'cws-essentials' ), false ),
										),
									),
									'footer_img_attachment'	=> array(
										'title'		=> esc_html__( 'Footer Img Attachment', 'cws-essentials' ),
										'type'	=> 'select',
										'source'	=> array(
											'fixed' => array( esc_html__('fixed', 'cws-essentials' ), false ),
											'scroll' => array( esc_html__('scroll', 'cws-essentials' ),  true ),
											'local' => array( esc_html__('local', 'cws-essentials' ), false )
										),
									),
								),
							),
							'footer_pattern' => array(
								'title' => esc_html__( 'Footer Pattern', 'cws-essentials' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'grid-col-12',
							),
							'footer_bg_color'	=> array(
								'title'	=> esc_html__( 'Footer Background Color', 'cws-essentials' ),
								'atts'	=> 'data-default-color="'. AASANA_FOOTER_COLOR .'"',
								'value' => AASANA_FOOTER_COLOR,
								'addrowclasses' => 'grid-col-6',
								'type'	=> 'text'
							),
							'footer_font_color' => array(
								'title' => esc_html__( 'Footer font color', 'cws-essentials' ),
								'atts' => 'data-default-color="#b0b0b0"',
								'value' => '#b0b0b0',
								'addrowclasses' => 'grid-col-6',
								'type' => 'text',
							),
							'footer_copyrights_bg_color'	=> array(
								'title'	=> esc_html__( 'Copyrights Background Color', 'cws-essentials' ),
								'atts' => 'data-default-color="#f9f9f9"',
								'value' => '#f9f9f9',
								'addrowclasses' => 'grid-col-6',
								'type'	=> 'text'
							),
							'footer_copyrights_font_color' => array(
								'title' => esc_html__( 'Copyrights Font color', 'cws-essentials' ),
								'atts' => 'data-default-color="#6f6f6f"',
								'value' => '#6f6f6f',
								'addrowclasses' => 'grid-col-6',
								'type' => 'text',
							),
							'footer_color_overlay_type'	=> array(
								'title'		=> esc_html__( 'Color overlay', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-4',
								'type'	=> 'select',
								'source'	=> array(
									'none' => array( esc_html__( 'None', 'cws-essentials' ),  true, 'd:footer_color_overlay_opacity;d:footer_overlay_color;d:footer_gradient_settings;' ),
									'color' => array( esc_html__( 'Color', 'cws-essentials' ),  false, 'e:footer_color_overlay_opacity;e:footer_overlay_color;d:footer_gradient_settings;' ),
									'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:footer_color_overlay_opacity;d:footer_overlay_color;e:footer_gradient_settings;' )
								),
							),
							'footer_color_overlay_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'Opacity', 'cws-essentials' ),
								'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
								'value' => '40',
								'addrowclasses' => 'disable grid-col-4',
							),
							'footer_overlay_color'	=> array(
								'title'	=> esc_html__( 'Overlay color', 'cws-essentials' ),
								'atts' => 'data-default-color="' . AASANA_COLOR . '"',
								'addrowclasses' => 'disable grid-col-4',
								'value' => AASANA_COLOR,
								'type'	=> 'text',
							),
							'footer_gradient_settings' => array(
								'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-12 groups',
								'layout' => array(
									'first_color' => array(
										'type' => 'text',
										'title' => esc_html__( 'From', 'cws-essentials' ),
										'atts' => 'data-default-color=""',
									),
									'second_color' => array(
										'type' => 'text',
										'title' => esc_html__( 'To', 'cws-essentials' ),
										'atts' => 'data-default-color=""',
									),
									'first_color_opacity' => array(
										'type' => 'number',
										'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
										'value' => '100',
									),
									'second_color_opacity' => array(
										'type' => 'number',
										'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
										'value' => '100',
									),
									'type' => array(
										'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
										'type' => 'radio',
										'addrowclasses' => 'disable grid-col-6',
										'value' => array(
											'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
											'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
										),
									),
									'linear_settings' => array(
										'title' => esc_html__( 'Linear settings', 'cws-essentials'  ),
										'type' => 'fields',
										'addrowclasses' => 'disable grid-col-6',
										'layout' => array(
											'angle' => array(
												'type' => 'number',
												'title' => esc_html__( 'Angle', 'cws-essentials' ),
												'value' => '45',
											),
										)
									),
									'radial_settings' => array(
										'title' => esc_html__( 'Radial settings', 'cws-essentials'  ),
										'type' => 'fields',
										'addrowclasses' => 'disable',
										'layout' => array(
											'shape_settings' => array(
												'title' => esc_html__( 'Shape', 'cws-essentials' ),
												'type' => 'radio',
												'value' => array(
													'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
													'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
												),
											),
											'shape' => array(
												'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
												'type' => 'radio',
												'value' => array(
													'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
													'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
												),
											),
											'size_keyword' => array(
												'type' => 'select',
												'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
												'addrowclasses' => 'disable',
												'source' => array(
													'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
													'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
													'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
													'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
												),
											),
											'size' => array(
												'type' => 'text',
												'addrowclasses' => 'disable',
												'title' => esc_html__( 'Size', 'cws-essentials' ),
												'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
											),
										)
									)
								)
							),
							'footer_instagram_feed' => array(
								'title' => esc_html__( 'Instagram Feed', 'cws-essentials' ),
								'addrowclasses' => 'checkbox grid-col-12 alt',
								'type' => 'checkbox',
								'tooltip' => array(
									'title' => esc_html__( 'Plugin requirement', 'cws-essentials' ),
									'content' => esc_html__( 'Instagram-feed (https://wordpress.org/plugins/instagram-feed/)', 'cws-essentials' ),
								),
								'atts' => 'data-options="e:instagram_feed_shortcode;e:footer_instagram_feed_full_width;"',
							),
							'instagram_feed_shortcode' => array(
								'title' => esc_html__( 'Instagram feed shortcode', 'cws-essentials' ),
								'addrowclasses' => 'disable grid-col-12',
								'tooltip' => array(
										'title' => esc_html__( 'Customize', 'cws-essentials' ),
										'content' => esc_html__( 'Customize page (/wp-admin/admin.php?page=sb-instagram-feed&tab=customize)', 'cws-essentials' ),
									),
								'type' => 'textarea',
								'atts' => 'rows="3"',
								'default' => '',
								'value' => '[instagram-feed cols=8 num=8 imagepadding=0 imagepaddingunit=px showheader=false showbutton=true showfollow=true]'
							),
							'footer_instagram_feed_full_width' => array(
								'title' => esc_html__( 'Full width', 'cws-essentials' ),
								'addrowclasses' => 'disable checkbox grid-col-12 alt',
								'type' => 'checkbox',
							),

						)
					),

				),
			),
			'tab7' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Layout', 'cws-essentials' ),
				'layout' => array(
					'is_boxed' => array(
						'type' => 'checkbox',
						'title' => esc_html__( 'Apply "Boxed layout" Options', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:boxed_background;e:boxed_overlay;e:boxed_overlay_type"'
					),
					'boxed_background' => array(
						'title' => esc_html__( 'Boxed Background layout', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'grid-col-12 disable groups',
						'layout' => array(
							'image' => array(
								'title' => esc_html__( 'Boxed background', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-2',
								'type' => 'media',
							),
							'size' => array(
								'title' => esc_html__( 'Background Size', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-2',
								'type' => 'radio',
								'value' => array(
									'cover' => array( esc_html__( 'Cover', 'cws-essentials' ),  true, '' ),
									'contain' =>array( esc_html__( 'Contain', 'cws-essentials' ), false,  '' ),
								),
							),
							'repeat' => array(
								'title' => esc_html__( 'Background Repeat', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-2',
								'type' => 'radio',
								'value' => array(
									'no-repeat' => array( esc_html__( 'No repeat', 'cws-essentials' ),  false, '' ),
									'repeat' => array( esc_html__( 'Tile', 'cws-essentials' ),  true, '' ),
									'repeat-x' => array( esc_html__( 'Tile Horizontally', 'cws-essentials' ),  false, '' ),
									'repeat-y' =>array( esc_html__( 'Tile Vertically', 'cws-essentials' ), false,  '' ),
								),
							),
							'attachment' => array(
								'title' => esc_html__( 'Background Attachment', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-2',
								'type' => 'radio',
								'value' => array(
									'scroll' => array( esc_html__( 'Scroll', 'cws-essentials' ),  false, '' ),
									'fixed' =>array( esc_html__( 'Fixed', 'cws-essentials' ), true,  '' ),
								),
							),
							'position' => array(
								'title' => esc_html__( 'Background Position', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-2',
								'cols' => 3,
								'type' => 'radio',
								'value' => array(
									'tl'=>	array( '', false ),
									'tc'=>	array( '', false ),
									'tr'=>	array( '', false ),
									'cl'=>	array( '', false ),
									'cc'=>	array( '', true ),
									'cr'=>	array( '', false ),
									'bl'=>	array( '', false ),
									'bc'=>	array( '', false ),
									'br'=>	array( '', false ),
								),
							),
						),
					),
					'boxed_overlay_type'	=> array(
						'title'		=> esc_html__( 'Color overlay', 'cws-essentials' ),
						'addrowclasses' => 'box disable',
						'type'	=> 'select',
						'source'	=> array(
							'none' => array( esc_html__( 'None', 'cws-essentials' ),  false, 'd:opacity;d:color;d:boxed_gradient;' ),
							'color' => array( esc_html__( 'Color', 'cws-essentials' ),  true, 'e:opacity;e:color;d:boxed_gradient;' ),
							'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:opacity;d:color;e:boxed_gradient;' )
						),
					),
					'boxed_overlay' => array(
						'title' => esc_html__( 'Boxed Overlay', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'grid-col-12 disable groups',
						'layout' => array(

							'opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'Opacity (%)', 'cws-essentials' ),
								'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
								'value' => '100',
								'addrowclasses' => 'box grid-col-12',
							),
							'color'	=> array(
								'title'	=> esc_html__( 'Overlay color', 'cws-essentials' ),
								'atts' => 'data-default-color="#fafafa"',
								'addrowclasses' => 'box grid-col-12',
								'value' => '#fafafa',
								'type'	=> 'text',
							),
						),
					),
					'boxed_gradient' => array(
						'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'inside-box groups disable',
						'layout' => array(
							'first_color' => array(
								'type' => 'text',
								'addrowclasses' => 'grid-col-12',
								'title' => esc_html__( 'From', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
							),
							'second_color' => array(
								'type' => 'text',
								'title' => esc_html__( 'To', 'cws-essentials' ),
								'atts' => 'data-default-color=""',
								'addrowclasses' => 'grid-col-12',
							),
							'first_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
								'value' => '100',
								'addrowclasses' => 'grid-col-12',
							),
							'second_color_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
								'value' => '100',
								'addrowclasses' => 'grid-col-12',
							),
							'type' => array(
								'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-6',
								'value' => array(
									'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
									'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
								),
							),
							'linear_settings' => array(
								'title' => esc_html__( 'Linear settings', 'cws-essentials'  ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-6',
								'layout' => array(
									'angle' => array(
										'type' => 'number',
										'title' => esc_html__( 'Angle', 'cws-essentials' ),
										'value' => '45',
									),
								)
							),
							'radial_settings' => array(
								'title' => esc_html__( 'Radial settings', 'cws-essentials'  ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-12',
								'layout' => array(
									'shape_settings' => array(
										'title' => esc_html__( 'Shape', 'cws-essentials' ),
										'type' => 'radio',
										'value' => array(
											'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
											'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
										),
									),
									'shape' => array(
										'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
										'type' => 'radio',
										'value' => array(
											'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
											'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
										),
									),
									'size_keyword' => array(
										'type' => 'select',
										'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
										'addrowclasses' => 'disable',
										'source' => array(
											'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
											'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
											'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
											'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
										),
									),
									'size' => array(
										'type' => 'text',
										'addrowclasses' => 'disable',
										'title' => esc_html__( 'Size', 'cws-essentials' ),
										'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
									),
								)
							)
						)
					),
				),
			),
			'tab8' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Side panel', 'cws-essentials' ),
				'layout' => array(
					'side_panel_switcher' => array(
						'title' => esc_html__( 'Switch on/off the side panel', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:side_panel"',
						'type' => 'checkbox',
					),
					'side_panel' => array(
						'title' => esc_html__( 'Side panel Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'disable box inside-box groups',
						'layout' => array(
							// 1st row
							'logo_dimensions' => array(
								'title' => esc_html__( 'Logo Dimensions', 'cws-essentials' ),
								'type' => 'dimensions',
								'addrowclasses' => 'grid-col-6',
								'value' => array(
									'width' => array('placeholder' => esc_html__( 'Width', 'cws-essentials' ), 'value' => ''),
									'height' => array('placeholder' => esc_html__( 'Height', 'cws-essentials' ), 'value' => ''),
								),
							),
							'logo_position' => array(
								'title' => esc_html__( 'Logo position', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-6',
								'type' => 'radio',
								'cols' => 3,
								'value' => array(
									'left' => array( esc_html__( 'Left', 'cws-essentials' ),  true, '' ),
									'center' =>array( esc_html__( 'Center', 'cws-essentials' ), false,  '' ),
									'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false,  '' ),
								),
							),
							// second row
							'theme'	=> array(
								'title'		=> esc_html__( 'Theme color', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-3',
								'type'	=> 'select',
								'source'	=> array(
									'dark' => array( esc_html__( 'Dark', 'cws-essentials' ),  true, '' ),
									'light' => array( esc_html__( 'Light', 'cws-essentials' ),  false, '' ),
								),
							),
							'close_position' => array(
								'title' => esc_html__( 'Close button position', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-3',
								'type' => 'radio',
								'value' => array(
									'left' => array( esc_html__( 'Left', 'cws-essentials' ),  true, '' ),
									'right' =>array( esc_html__( 'Right', 'cws-essentials' ), false,  '' ),
								),
							),
							'place' => array(
								'title' => esc_html__( 'Icon position', 'cws-essentials' ),
								'type' => 'radio',
								'subtype' => 'images',
								'addrowclasses' => 'grid-col-3',
								'value' => array(
									'topbar_left' =>array( esc_html__( 'TopBar (Left)', 'cws-essentials' ), false, '', '/img/top-hamb-left.png' ),
									'topbar_right' => array( esc_html__( 'TopBar (Right)', 'cws-essentials' ), 	false, '', '/img/top-hamb-right.png' ),
									'menu_left' =>array( esc_html__( 'Menu (Left)', 'cws-essentials' ), true, '', '/img/hamb-left.png' ),
									'menu_right' =>array( esc_html__( 'Menu (Right)', 'cws-essentials' ), false, '', '/img/hamb-right.png' ),
								),
							),
							'position' => array(
								'title' 			=> esc_html__('Side panel position', 'cws-essentials' ),
								'type' 				=> 'radio',
								'subtype' 			=> 'images',
								'addrowclasses' => 'grid-col-3',
								'value' 			=> array(
									'left' 				=> 	array( esc_html__('Left', 'cws-essentials' ), true, '',	'/img/left.png' ),
									'right' 			=> 	array( esc_html__('Right', 'cws-essentials' ), false, '', '/img/right.png' ),
								),
							),
							// 3rd row
							'logo_dark' => array(
								'title' => esc_html__( 'Logotype (Dark)', 'cws-essentials' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'grid-col-6',
								// 'value' => array( 'id' => '', 'src' => get_template_directory_uri() . '\img\logo_the8_128x128.png') ,
								'layout' => array(
									'logo_is_high_dpi' => array(
										'title' => esc_html__( 'High-Resolution logo', 'cws-essentials' ),
										'addrowclasses' => 'checkbox',
										'type' => 'checkbox',
									),
								),
							),
							'logo_light' => array(
								'title' => esc_html__( 'Logotype (Light)', 'cws-essentials' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'grid-col-6',
								'layout' => array(
									'logo_is_high_dpi' => array(
										'title' => esc_html__( 'High-Resolution logo', 'cws-essentials' ),
										'addrowclasses' => 'checkbox',
										'type' => 'checkbox',
									),
								),
							),
							// 4th row
							'bg_dark' => array(
								'title' => esc_html__( 'Background (Dark)', 'cws-essentials' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'grid-col-6',
							),
							'bg_light' => array(
								'title' => esc_html__( 'Background (Light)', 'cws-essentials' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'grid-col-6',
							),
							// 5th row
							'bg_size' => array(
								'title' => esc_html__( 'Background size', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-4',
								'type' => 'radio',
								'value' => array(
									'cover' => array( esc_html__( 'Cover', 'cws-essentials' ),  true, '' ),
									'contain' =>array( esc_html__( 'Contain', 'cws-essentials' ), false,  '' ),
								),
							),
							'bg_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'Background Opacity', 'cws-essentials' ),
								'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-4',
								'value' => '50'
							),
							'bg_position' => array(
								'title' => esc_html__( 'Background Position', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-4',
								'cols' => 3,
								'type' => 'radio',
								'value' => array(
									'tl'=>	array( '', false ),
									'tc'=>	array( '', false ),
									'tr'=>	array( '', false ),
									'cl'=>	array( '', false ),
									'cc'=>	array( '', true ),
									'cr'=>	array( '', false ),
									'bl'=>	array( '', false ),
									'bc'=>	array( '', false ),
									'br'=>	array( '', false ),
								),
							),
							// 6th row
							'sidebar' => array(
								'title' 		=> esc_html__('Sidebar source', 'cws-essentials' ),
								'type' 			=> 'select',
								'addrowclasses' => 'new_row grid-col-6',
								'source' 		=> 'sidebars',
								'value' => 'side_panel',
							),
							'appear'	=> array(
								'title'		=> esc_html__( 'Appear style', 'cws-essentials' ),
								'type'	=> 'select',
								'addrowclasses' => 'grid-col-6',
								'source'	=> array(
									'fade' => array( esc_html__( 'Fade', 'cws-essentials' ),  true ),
									'slide' => array( esc_html__( 'Slide', 'cws-essentials' ), false ),
									'pull' => array( esc_html__( 'Pull', 'cws-essentials' ), false ),
								),
							),
							'overlay_color' => array(
								'title' => esc_html__( 'Overlay color', 'cws-essentials' ),
								'atts' => 'data-default-color="#000000"',
								'value' => '#000000',
								'addrowclasses' => 'grid-col-6',
								'type' => 'text',
							),
							'overlay_opacity' => array(
								'type' => 'number',
								'title' => esc_html__( 'Overlay Opacity', 'cws-essentials' ),
								'placeholder' => esc_html__( 'In percents', 'cws-essentials' ),
								'addrowclasses' => 'grid-col-6',
								'value' => '70'
							),
						),
					),
				)
			),
			'tab9' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Colors', 'cws-essentials' ),
				'layout' => array(
					'is_theme_color' => array(
						'type' => 'checkbox',
						'title' => esc_html__( 'Apply "Colors" Options', 'cws-essentials' ),
						'addrowclasses' => 'checkbox alt',
						'atts' => 'data-options="e:theme-main-one-color;e:theme-main-secondary-color;e:theme-second-color"'
					),
					'theme-main-one-color' => array(
						'title' => esc_html__( 'Main color', 'cws-essentials' ),
						'atts' => 'data-default-color="' . AASANA_COLOR . '"',
						'value' => AASANA_COLOR,
						'addrowclasses' => 'disable grid-col-6',
						'type' => 'text',
					),						
					'theme-main-secondary-color' => array(
						'title' => esc_html__( 'Secondary Color', 'cws-essentials' ),
						'atts' => 'data-default-color="' . AASANA_SECONDARY_COLOR . '"',
						'value' => AASANA_SECONDARY_COLOR,
						'addrowclasses' => 'disable grid-col-6',
						'type' => 'text',
					),
					'theme-second-color' => array(
						'title' => esc_html__( 'Helper color', 'cws-essentials' ),
						'atts' => 'data-default-color="#784f6a"',
						'value' => '#784f6a',
						'addrowclasses' => 'disable grid-col-6',
						'type' => 'text',
					),
				),
			),
		);

		$this->mb_post_layout = array(
			'gallery' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Gallery', 'cws-essentials' ),
				'layout' => array(
					'gallery_type' => array(
						'type' => 'select',
						'title' => esc_html__( 'Gallery type', 'cws-essentials' ),
						'source' => array(
							'slider' => array(esc_html__( 'Slider', 'cws-essentials' ), true, 'd:grid_cols;'),
							'grid' => array(esc_html__( 'Grid', 'cws-essentials' ), false, 'e:grid_cols;'),
						),
					),
					'grid_cols' => array(
						'type' => 'select',
						'title' => esc_html__( 'Grid columns', 'cws-essentials' ),
						'addrowclasses' => 'disable box',
						'source' => array(
							'1' => array(esc_html__( 'one', 'cws-essentials' ), false),
							'2' => array(esc_html__( 'two', 'cws-essentials' ), false),
							'3' => array(esc_html__( 'three', 'cws-essentials' ), false),
							'4' => array(esc_html__( 'four', 'cws-essentials' ), true),
						),
					),
					'gallery' => array(
						'type' => 'gallery'
					),
				)
			),
			'video' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Video', 'cws-essentials' ),
				'layout' => array(
					'video' => array(
						'title' => esc_html__( 'Direct URL path of a video file', 'cws-essentials' ),
						'type' => 'text'
					)
				)
			),
			'audio' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Audio', 'cws-essentials' ),
				'layout' => array(
					'audio' => array(
						'title' => esc_html__( 'A self-hosted or SoundClod audio file URL', 'cws-essentials' ),
						'subtitle' => esc_html__( 'Ex.: /wp-content/uploads/audio.mp3 or http://soundcloud.com/...', 'cws-essentials' ),
						'type' => 'text'
					)
				)
			),
			'link' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Url', 'cws-essentials' ),
				'layout' => array(
					'link' => array(
						'title' => esc_html__( 'URL', 'cws-essentials' ),
						'type' => 'text'
					),
					'link_title' => array(
						'title' => esc_html__( 'Title', 'cws-essentials' ),
						'type' => 'text'
					)
				)
			),
			'quote' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Quote', 'cws-essentials' ),
				'layout' => array(
					'quote_text' => array(
						'subtitle' => esc_html__( 'Enter the quote', 'cws-essentials' ),
						'atts' => 'rows="5"',
						'type' => 'textarea'
					),
					'quote_author' => array(
						'title' => esc_html__( 'Author', 'cws-essentials' ),
						'type' => 'text'
					),
				)
			),
			'post_sidebars' => array(
				'title' => esc_html__( 'Sidebars Settings', 'cws-essentials' ),
				'type' => 'fields',
				'addrowclasses' => 'box inside-box groups',
				'layout' => array(
					'layout' => array(
						'title' => esc_html__('Sidebar Position', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'value' => array(
							'{post_sidebars}'=>	array( esc_html__('Default', 'cws-essentials' ), true, 'd:sb1;d:sb2', '/img/default.png' ),
							'left' => 	array( esc_html__('Left', 'cws-essentials' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
							'right' => 	array( esc_html__('Right', 'cws-essentials' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
							'both' => 	array( esc_html__('Double', 'cws-essentials' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
							'none' => 	array( esc_html__('None', 'cws-essentials' ), false, 'd:sb1;d:sb2', '/img/none.png' )
						),
					),
					'sb1' => array(
						'title' => esc_html__('Select a sidebar', 'cws-essentials' ),
						'type' => 'select',
						'addrowclasses' => 'disable box',
						'source' => 'sidebars',
					),
					'sb2' => array(
						'title' => esc_html__('Select right sidebar', 'cws-essentials' ),
						'type' => 'select',
						'addrowclasses' => 'disable box',
						'source' => 'sidebars',
					),
				),
			),
			'enable_lightbox' => array(
				'title' => esc_html__( 'Enable lightbox', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked',
			),
			'show_featured' => array(
				'title' => esc_html__( 'Show featured image on single post', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked data-options="e:full_width;"',
			),
			'full_width' => array(
				'type' => 'checkbox',
				'title' => esc_html__( 'Full-Width Featured Image', 'cws-essentials' ),
				'addrowclasses' => 'disable checkbox grid-col-12',
			),
			'show_related' => array(
				'title' => esc_html__( 'Show related items', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked data-options="e:rpo"',
			),
			'rpo' => array(
				'title' => esc_html__( 'Related items settings', 'cws-essentials' ),
				'type' => 'fields',
				'addrowclasses' => 'disable groups',
				'layout' => array(
					'title' => array(
						'type' => 'text',
						'addrowclasses' => 'box grid-col-12',
						'title' => esc_html__( 'Title', 'cws-essentials' ),
						'value' => esc_html__( 'Related items', 'cws-essentials' ),
						'addrowclasses' => '',
					),
					'category' => array(
						'title' => esc_html__( 'Categories (Filter)', 'cws-essentials' ),
						'type' => 'taxonomy',
						'atts' => 'multiple',
						'taxonomy' => 'category',
						'addrowclasses' => 'box grid-col-12',
						'source' => array(),
					),
					'text_length' => array(
						'type' => 'number',
						'title' => esc_html__( 'Text length', 'cws-essentials' ),
						'addrowclasses' => 'box grid-col-12',
						'value' => '90'
					),
					'cols' => array(
						'type' => 'select',
						'title' => esc_html__( 'Columns', 'cws-essentials' ),
						'addrowclasses' => 'box grid-col-12',
						'source' => array(
							'1' => array(esc_html__( 'one', 'cws-essentials' ), false),
							'2' => array(esc_html__( 'two', 'cws-essentials' ), false),
							'3' => array(esc_html__( 'three', 'cws-essentials' ), false),
							'4' => array(esc_html__( 'four', 'cws-essentials' ), true),
							),
						),
					'items_show' => array(
						'type' => 'number',
						'title' => esc_html__( 'Number of items to show', 'cws-essentials' ),
						'addrowclasses' => 'box grid-col-12',
						'value' => '4'
					),
					'posts_hide' => array(
						'title' => esc_html__( 'Hide', 'cws-essentials' ),	
						'atts' => 'multiple',
						'type' => 'select',
						'addrowclasses' => 'box grid-col-12',
						'source' => array(
							'none' => array(esc_html__( 'None', 'cws-essentials' ), true),
							'cats' => array(esc_html__( 'Categories', 'cws-essentials' ), false),
							'tags' => array(esc_html__( 'Tags', 'cws-essentials' ), false),
							'author' => array(esc_html__( 'Author', 'cws-essentials' ), false),
							'likes' => array(esc_html__( 'Likes', 'cws-essentials' ), false),
							'date' => array(esc_html__( 'Date', 'cws-essentials' ), false),
							'comments' => array(esc_html__( 'Comments', 'cws-essentials' ), false),
							'read_more' => array(esc_html__( 'Read More', 'cws-essentials' ), false),
							'social' => array(esc_html__( 'Social Icons', 'cws-essentials' ), false),
							'excerpt' => array(esc_html__( 'Excerpt', 'cws-essentials' ), false),
							),
					),
				),
			),
			'post_custom_color' => array(
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox grid-col-12',
				'title' => esc_html__( 'Edit Colors', 'cws-essentials' ),
				'atts' => 'data-options="e:post_title_color;e:post_font_color;e:post_font_sec_color;e:apply_color;e:post_font_meta_color"',
			),
			'apply_color' => array(
				'type' => 'select',
				'title' => esc_html__( 'Apply to', 'cws-essentials' ),
				'addrowclasses' => 'grid-col-12',
				'source' => array(
					'list_color' => array(esc_html__( 'Blog List', 'cws-essentials' ), true),
					'single_color' => array(esc_html__( 'Blog Single', 'cws-essentials' ), false),
					'both_color' => array(esc_html__( 'Both', 'cws-essentials' ), false),
				),
			),
			'post_title_color' => array(
				'title' 		=> esc_html__( 'Title Color', 'cws-essentials' ),
				'tooltip' => array(
					'title' => esc_html__( 'Override Title Color', 'cws-essentials' ),
					'content' => esc_html__( 'Override Title Color', 'cws-essentials' ),
				),
				'type' 			=> 'text',
				'addrowclasses' => 'disable grid-col-12',
				'atts' 			=> 'data-default-color="' . AASANA_COLOR . '"',
				'value'			=> AASANA_COLOR
			),
			'post_font_color' => array(
				'title' 		=> esc_html__( 'Text Color', 'cws-essentials' ),
				'type' 			=> 'text',
				'addrowclasses' => 'disable grid-col-12',
				'atts' 			=> 'data-default-color="#707273;"',
				'value'			=> '#707273'
			),
			'post_font_sec_color' => array(
				'title' 		=> esc_html__( 'Helper Color', 'cws-essentials' ),
				'type' 			=> 'text',
				'addrowclasses' => 'disable grid-col-12',
				'atts' 			=> 'data-default-color="' . AASANA_SECONDARY_COLOR . '"',
				'value'			=> AASANA_SECONDARY_COLOR
			),			
			'post_font_meta_color' => array(
				'title' 		=> esc_html__( 'Meta Color', 'cws-essentials' ),
				'type' 			=> 'text',
				'addrowclasses' => 'disable grid-col-12',
				'atts' 			=> 'data-default-color="' . AASANA_COLOR . '"',
				'value'			=> AASANA_COLOR
			),
			'custom_title_overlay' => array(
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox grid-col-12',
				'title' => esc_html__( 'Add Background Overlay', 'cws-essentials' ),
				'atts' => 'data-options="e:post_color_overlay_type;e:apply_bg_color;"',
			),
			'apply_bg_color' => array(
				'type' => 'select',
				'title' => esc_html__( 'Apply to', 'cws-essentials' ),
				'addrowclasses' => 'grid-col-12',
				'source' => array(
					'list_color' => array(esc_html__( 'Blog List', 'cws-essentials' ), true),
					'single_color' => array(esc_html__( 'Blog Single', 'cws-essentials' ), false),
					'both_color' => array(esc_html__( 'Both', 'cws-essentials' ), false),
				),
			),
			'post_color_overlay_type'	=> array(
				'title'		=> esc_html__( 'Color overlay', 'cws-essentials' ),
				'addrowclasses' => 'grid-col-12',
				'type'	=> 'select',
				'source'	=> array(
					'none' => array( esc_html__( 'None', 'cws-essentials' ),  false, 'd:title_bg_opacity;d:title_overlay;d:post_gradient_settings;' ),
					'color' => array( esc_html__( 'Color', 'cws-essentials' ),  true, 'e:title_bg_opacity;e:title_overlay;d:post_gradient_settings;' ),
					'gradient' => array( esc_html__( 'Gradient', 'cws-essentials' ), false, 'e:title_bg_opacity;d:title_overlay;e:post_gradient_settings;' )
					),
				),
			
			'title_overlay' => array(
				'title' => esc_html__( 'Overlay Color', 'cws-essentials' ),
				'type' => 'text',
				'addrowclasses' => 'grid-col-12 disable',
				'atts' 			=> 'data-default-color="#000000"',
				'value'			=> '#000000'
			),
			'title_bg_opacity' => array(
				'title' 		=> esc_html__( 'Opacity', 'cws-essentials' ),								
				'type' 			=> 'number',
				'addrowclasses' => 'grid-col-12',
				'atts' 			=> " min='0' max='100'",
				'value'			=> '40'
			),'post_gradient_settings' => array(
				'title' => esc_html__( 'Gradient Settings', 'cws-essentials' ),
				'type' => 'fields',
				'addrowclasses' => 'grid-col-12 disable groups',
				'layout' => array(
					'first_color' => array(
						'type' => 'text',
						'title' => esc_html__( 'From', 'cws-essentials' ),
						'atts' => 'data-default-color=""',
						),
					'second_color' => array(
						'type' => 'text',
						'title' => esc_html__( 'To', 'cws-essentials' ),
						'atts' => 'data-default-color=""',
						),
					'first_color_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'From (Opacity %)', 'cws-essentials' ),
						'value' => '100',
						),
					'second_color_opacity' => array(
						'type' => 'number',
						'title' => esc_html__( 'To (Opacity %)', 'cws-essentials' ),
						'value' => '100',
						),
					'type' => array(
						'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
						'type' => 'radio',
						'value' => array(
							'linear' => array( esc_html__( 'Linear', 'cws-essentials' ),  true, 'e:linear_settings;d:radial_settings' ),
							'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,  'd:linear_settings;e:radial_settings' ),
							),
						),
					'linear_settings' => array(
						'title' => esc_html__( 'Linear settings', 'cws-essentials'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable',
						'layout' => array(
							'angle' => array(
								'type' => 'number',
								'title' => esc_html__( 'Angle', 'cws-essentials' ),
								'value' => '45',
								),
							)
						),
					'radial_settings' => array(
						'title' => esc_html__( 'Radial settings', 'cws-essentials'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable',
						'layout' => array(
							'shape_settings' => array(
								'title' => esc_html__( 'Shape', 'cws-essentials' ),
								'type' => 'radio',
								'value' => array(
									'simple' => array( esc_html__( 'Simple', 'cws-essentials' ),  true, 'e:shape;d:size;d:size_keyword;' ),
									'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:shape;e:size;e:size_keyword;' ),
									),
								),
							'shape' => array(
								'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
								'type' => 'radio',
								'value' => array(
									'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ),  true ),
									'circle' =>array( esc_html__( 'Circle', 'cws-essentials' ), false ),
									),
								),
							'size_keyword' => array(
								'type' => 'select',
								'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
								'addrowclasses' => 'disable',
								'source' => array(
									'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
									'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
									'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
									'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
									),
								),
							'size' => array(
								'type' => 'text',
								'addrowclasses' => 'disable',
								'title' => esc_html__( 'Size', 'cws-essentials' ),
								'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'cws-essentials' ).'"',
								),
							)
						)
					)
				),	
			'custom_title_spacings' => array(
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox grid-col-12',
				'title' => esc_html__( 'Add Title Spacings', 'cws-essentials' ),
				'atts' => 'data-options="e:page_title_spacings;"',
			),
			'page_title_spacings' => array(
				'type' => 'margins',
				'addrowclasses' => 'grid-col-4 two-inputs',
				'value' => array(
					'top' => array('placeholder' => esc_html__( 'Top (in px)', 'cws-essentials' ), 'value' => '60px'),
					'bottom' => array('placeholder' => esc_html__( 'Bottom (in px)', 'cws-essentials' ), 'value' => '60px'),
				),
			),			
		);			

		$this->mb_staff_layout = array(
			'is_clickable' => array(
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'title' => esc_html__('Show details page', 'cws-essentials' ),
				'atts' => 'data-options="e:add_btn;"',
			),
			'experience' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Experience', 'cws-essentials' ),			
			),			
			'email' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Email', 'cws-essentials' ),			
			),			
			'biography' => array(
				'atts' => 'rows="5"',
				'type' => 'textarea',
				'title' 		=> esc_html__( 'Biography', 'cws-essentials' ),			
			),
			'add_btn' => array(
				'type' => 'checkbox',
				'title' => esc_html__('Add Button', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-options="e:title_btn;"',
			),
			'title_btn' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Title Button', 'cws-essentials' ),
				'addrowclasses' => 'disable',				
			),
			'add_view_btn' => array(
				'type' => 'checkbox',
				'title' => esc_html__('Add Button View Classes', 'cws-essentials' ),
				'atts' => 'data-options="e:link_to_view;e:title_btn_view;"',
			),
			'title_btn_view' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Title Button', 'cws-essentials' ),
				'addrowclasses' => 'disable',	
				'value'			=> 'View Classes',			
			),
			'link_to_view' 		=> array(
				'type' => 'select',
				'title' => esc_html__( 'Link To:', 'cws-essentials' ),
				'source' => array(
					'archive' => array(esc_html__( 'Archive', 'cws-essentials' ), true, 'd:link_custom_url;'),
					'custom_url' => array(esc_html__( 'Custom Url', 'cws-essentials' ), false, 'e:link_custom_url;'),
				),
			),
			'link_custom_url' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Custom url', 'cws-essentials' ),
				'addrowclasses' => 'disable',				
				'default' 		=> ''
			),
			'social_group' => array(
				'type' => 'group',
				'addrowclasses' => 'group expander sortable box',
				'title' => esc_html__('Social networks', 'cws-essentials' ),
				'button_title' => esc_html__('Add new social network', 'cws-essentials' ),
				'layout' => array(
					'title' => array(
						'type' => 'text',
						'atts' => 'data-role="title"',
						'title' => esc_html__('Social account title', 'cws-essentials' ),
					),
					'icon' => array(
						'type' => 'select',
						'addrowclasses' => 'fai',
						'source' => 'fa',
						'title' => esc_html__('Select the icon for this social contact', 'cws-essentials' )
					),
					'url' => array(
						'type' => 'text',
						'title' => esc_html__('Url to your account', 'cws-essentials' ),
					),
				),
			),
		);

		$this->mb_portfolio_layout = array(
			'tab0' => array(
				'type' => 'tab',
				'init' => 'open grid-col-12',
				'title' => esc_html__( 'General', 'cws-essentials' ),
				'layout' => array(
					'post_sidebars' => array(
						'title' => esc_html__( 'Page Sidebars Settings', 'cws-essentials' ),
						'type' => 'fields',
						'addrowclasses' => 'box inside-box groups',
						'layout' => array(
							'layout' => array(
								'title' => esc_html__('Sidebar Position', 'cws-essentials' ),
								'type' => 'radio',
								'subtype' => 'images',
								'value' => array(
									'{page_sidebars}'=>	array( esc_html__('Default', 'cws-essentials' ), true, 'd:def--sidebar1;d:def--sidebar2', '/img/default.png' ),
									'left' => 	array( esc_html__('Left', 'cws-essentials' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
									'right' => 	array( esc_html__('Right', 'cws-essentials' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
									'both' => 	array( esc_html__('Double', 'cws-essentials' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
									'none' => 	array( esc_html__('None', 'cws-essentials' ), false, 'd:sb1;d:sb2', '/img/none.png' )
								),
							),
							'sb1' => array(
								'title' => esc_html__('Select a sidebar', 'cws-essentials' ),
								'type' => 'select',
								'addrowclasses' => 'disable box',
								'source' => 'sidebars',
							),
							'sb2' => array(
								'title' => esc_html__('Select right sidebar', 'cws-essentials' ),
								'type' => 'select',
								'addrowclasses' => 'disable box',
								'source' => 'sidebars',
							),
						),
					),
					'full_width' => array(
						'type' => 'checkbox',
						'title' => esc_html__( 'Full Width', 'cws-essentials' ),
						'atts' => 'data-options="d:decr_pos;"',
					),
					'decr_pos' => array(
						'type' => 'select',
						'title' => esc_html__( 'Project Description', 'cws-essentials' ),
						'source' => array(
							'bot' => array(esc_html__( 'Bottom', 'cws-essentials' ), true, 'd:cont_width;'),
							'left' => array(esc_html__( 'Left', 'cws-essentials' ), false, 'e:cont_width;'),
							'left_s' => array(esc_html__( 'Left + Sticky', 'cws-essentials' ), false, 'e:cont_width;'),
							'right' => array(esc_html__( 'Right', 'cws-essentials' ), false, 'e:cont_width;'),
							'right_s' => array(esc_html__( 'Right + Sticky', 'cws-essentials' ), false, 'e:cont_width;'),
						),
					),
					'cont_width' => array(
						'type' => 'select',
						'title' => esc_html__( 'Content Width', 'cws-essentials' ),
						'source' => array(
							'25' => array(esc_html__( '1/4', 'cws-essentials' ), false),
							'33' => array(esc_html__( '1/3', 'cws-essentials' ), true),
							'50' => array(esc_html__( '1/2', 'cws-essentials' ), false),
							'66' => array(esc_html__( '2/3', 'cws-essentials' ), false),
						),
					),
					'p_type' => array(
						'type' => 'select',
						'title' => esc_html__( 'Portfolio Single\'s Format', 'cws-essentials' ),
						'source' => array(
							'image' => array(esc_html__( 'Featured Image', 'cws-essentials' ), true, 'd:gall_type;d:video_type;d:slider_type;d:rev_slider_type;'),
							'gallery' => array(esc_html__( 'Gallery', 'cws-essentials' ), false, 'e:gall_type;d:video_type;d:slider_type;d:rev_slider_type;'),
							'slider' => array(esc_html__( 'Slider', 'cws-essentials' ), false, 'd:gall_type;d:video_type;e:slider_type;d:rev_slider_type;'),
							'rev_slider' => array(esc_html__( 'External Slider', 'cws-essentials' ), false, 'd:gall_type;d:video_type;d:slider_type;e:rev_slider_type;'),
							'video' => array(esc_html__( 'Video', 'cws-essentials' ), false, 'd:gall_type;e:video_type;d:slider_type;d:rev_slider_type;'),
							'none' => array(esc_html__( 'None', 'cws-essentials' ), false, 'd:gall_type;d:video_type;d:slider_type;d:rev_slider_type;'),
						),
					),
					'gall_type' => array(
						'type' => 'fields',
						'addrowclasses' => 'box inside-box groups',
						'layout' => array(
							'gall' => array(
								'title' => esc_html__( 'Add Media', 'cws-essentials' ),
								'type' => 'gallery'
							),
						),
					),
					'slider_type' => array(
						'type' => 'fields',
						'addrowclasses' => 'box inside-box groups',
						'layout' => array(
							'slider_gall' => array(
								'title' => esc_html__( 'Add Media', 'cws-essentials' ),
								'type' => 'gallery',
								'addrowclasses' => 'grid-col-3',
							),
							'on_grid' => array(
								'title' => esc_html__( 'Show on Portfolio Grid', 'cws-essentials' ),
								'type' => 'checkbox',
								'addrowclasses' => 'grid-col-3',
							),
						),
					),
					'rev_slider_type' => array(
						'type' => 'fields',
						'addrowclasses' => 'box inside-box groups',
						'layout' => array(
							'rev_url' => array(
								'title' => esc_html__( 'Add Shortcode', 'cws-essentials' ),
								'type' => 'text',
							),
						)
					),
					'video_type' => array(
						'type' => 'fields',
						'addrowclasses' => 'box inside-box groups',
						'layout' => array(
							'video_t' => array(
								'type' => 'select',
								'source' => array(
									'youtube' => array(esc_html__( 'YouTube', 'cws-essentials' ), true, 'e:youtube_t;d:vimeo_t;d:other_t;'),
									'vimeo' => array(esc_html__( 'Vimeo', 'cws-essentials' ), false, 'd:youtube_t;e:vimeo_t;d:other_t;'),
									'other' => array(esc_html__( 'Other', 'cws-essentials' ), false, 'd:youtube_t;d:vimeo_t;e:other_t;'),
								),
							),
							'youtube_t' => array(
								'type' => 'fields',
								'addrowclasses' => 'box inside-box groups grid-col-12',
								'layout' => array(
									'url' => array(
										'title' => esc_html__( 'Video ID', 'cws-essentials' ),
										'type' => 'text',
									),
								),
							),
							'vimeo_t' => array(
								'type' => 'fields',
								'addrowclasses' => 'box inside-box groups grid-col-12',
								'layout' => array(
									'url' => array(
										'title' => esc_html__( 'Video ID', 'cws-essentials' ),
										'type' => 'text',
									),
								),
							),
							'other_t' => array(
								'type' => 'fields',
								'addrowclasses' => 'box inside-box groups grid-col-12',
								'layout' => array(
									'url' => array(
										'title' => esc_html__( 'Video URL', 'cws-essentials' ),
										'type' => 'text',
									),
								),
							),
							'popup' => array(
								'title' => esc_html__( 'Show Video in a Popup on Single', 'cws-essentials' ),
								'type' => 'checkbox',
								'addrowclasses' => 'grid-col-12',
								'atts' => 'data-options="e:img;"',
							),
							// 'img' => array(
							// 	'title' => esc_html__( 'Add Cover Image', 'cws-essentials' ),
							// 	'type' => 'media',
							// 	'addrowclasses' => 'grid-col-3',
							// ),
							'on_grid' => array(
								'title' => esc_html__( 'Show on Portfolio Grid', 'cws-essentials' ),
								'type' => 'checkbox',
								'addrowclasses' => 'grid-col-12',
								'atts' => 'data-options="e:popup_grid;"',
							),
							'popup_grid' => array(
								'title' => esc_html__( 'Show Video in a Popup on Grid', 'cws-essentials' ),
								'type' => 'checkbox',
								'addrowclasses' => 'grid-col-12',
							),
							// 'autoplay' => array(
							// 	'title' => esc_html__( 'AutoPlay Video', 'cws-essentials' ),
							// 	'type' => 'checkbox',
							// 	'addrowclasses' => 'grid-col-12',
							// ),
						)
					),
				),
			),
			'tab1' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Isotope Layout', 'cws-essentials' ),
				'layout' => array(
					'isotope_col_count' => array(
						'type' => 'select',
						'title' => esc_html__( 'Columns', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
						'source' => array(
							'1' => array(esc_html__( 'One', 'cws-essentials' ), true),
							'2' => array(esc_html__( 'Two', 'cws-essentials' ), false),
							'3' => array(esc_html__( 'Three', 'cws-essentials' ), false),
							'4' => array(esc_html__( 'Four', 'cws-essentials' ), false),
						),
					),
					'isotope_line_count' => array(
						'type' => 'select',
						'title' => esc_html__( 'Lines', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
						'source' => array(
							'1' => array(esc_html__( 'One', 'cws-essentials' ), true),
							'2' => array(esc_html__( 'Two', 'cws-essentials' ), false),
							'3' => array(esc_html__( 'Three', 'cws-essentials' ), false),
							'4' => array(esc_html__( 'Four', 'cws-essentials' ), false),
						),
					),
					'desc' => array(
						'type' => 'lable',
						'addrowclasses' => 'grid-col-12',
						'title' => esc_html__( 'This option is used in the Isotope Portfolio Layout only. The image will take the selected number of Columns/Lines and will be displayed accordingly.', 'cws-essentials' ),
					),
				),
			),
			'tab2' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Related Items', 'cws-essentials' ),
				'layout' => array(
					'carousel' => array(
						'title' => esc_html__( 'Display items carousel for this portfolio post', 'cws-essentials' ),
						'type' => 'checkbox',
						'atts' => 'checked',
						'addrowclasses' => 'checkbox grid-col-12',
					),
					'show_related' => array(
						'title' => esc_html__( 'Show related Items', 'cws-essentials' ),
						'type' => 'checkbox',
						'atts' => 'checked data-options="e:related_projects_options;e:rpo_title;e:rpo_cols;e:rpo_items_count;e:rpo_categories;"',
						'addrowclasses' => 'alt checkbox grid-col-12',
					),
					'rpo_title' => array(
						'type' => 'text',
						'title' => esc_html__( 'Title', 'cws-essentials' ),
						'value' => esc_html__( 'Related projects', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
					),
					'rpo_categories' => array(
						'title' => esc_html__( 'Categories', 'cws-essentials' ),
						'type' => 'taxonomy',
						'atts' => 'multiple',
						'addrowclasses' => 'grid-col-12',
						'taxonomy' => 'cws_portfolio_cat',
						'source' => array(),
					),
					'rpo_cols' => array(
						'type' => 'select',
						'title' => esc_html__( 'Columns', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
						'source' => array(
							'1' => array(esc_html__( 'One', 'cws-essentials' ), false),
							'2' => array(esc_html__( 'Two', 'cws-essentials' ), false),
							'3' => array(esc_html__( 'Three', 'cws-essentials' ), false),
							'4' => array(esc_html__( 'Four', 'cws-essentials' ), true),
							),
					),
					'rpo_items_count' => array(
						'type' => 'number',
						'title' => esc_html__( 'Number of Related Items', 'cws-essentials' ),
						'value' => '4',
						'addrowclasses' => 'grid-col-12',
					),
				),

			),
			'tab3' => array(
				'type' => 'tab',
				'init' => 'closed',
				'title' => esc_html__( 'Hover', 'cws-essentials' ),
				'layout' => array(
					'enable_hover' => array(
						'title' => esc_html__( 'Enable Hover', 'cws-essentials' ),
						'type' => 'checkbox',
						'atts' => 'checked data-options="e:link_options;e:link_options_single;e:link_options_fancybox"',
						'addrowclasses' => 'alt checkbox grid-col-12',
					),
					'link_options_fancybox' => array(
						'type' => 'checkbox',
						'title' => esc_html__( 'Open in a popup', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
						'atts' => 'checked'
					),
					'link_options_single' => array(
						'type' => 'checkbox',
						'title' => esc_html__( 'Single Link', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
						'atts' => 'checked data-options="e:link_options_url;"',
					),
					'link_options_url' => array(
						'type' => 'text',
						'title' => esc_html__( 'Add Custom URL', 'cws-essentials' ),
						'addrowclasses' => 'grid-col-12',
						'default' => ''
					),
				),
			),	
		);

		$this->mb_classes_layout = array(
			'post_sidebars' => array(
				'title' => esc_html__( 'Page Sidebars Settings', 'cws-essentials' ),
				'type' => 'fields',
				'addrowclasses' => 'box inside-box groups',
				'layout' => array(
					'layout' => array(
						'title' => esc_html__('Sidebar Position', 'cws-essentials' ),
						'type' => 'radio',
						'subtype' => 'images',
						'value' => array(
							'{page_sidebars}'=>	array( esc_html__('Default', 'cws-essentials' ), true, 'd:def--sidebar1;d:def--sidebar2', '/img/default.png' ),
							'left' => 	array( esc_html__('Left', 'cws-essentials' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
							'right' => 	array( esc_html__('Right', 'cws-essentials' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
							'both' => 	array( esc_html__('Double', 'cws-essentials' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
							'none' => 	array( esc_html__('None', 'cws-essentials' ), false, 'd:sb1;d:sb2', '/img/none.png' )
						),
					),
					'sb1' => array(
						'title' => esc_html__('Select a sidebar', 'cws-essentials' ),
						'type' => 'select',
						'addrowclasses' => 'disable box',
						'source' => 'sidebars',
					),
					'sb2' => array(
						'title' => esc_html__('Select right sidebar', 'cws-essentials' ),
						'type' => 'select',
						'addrowclasses' => 'disable box',
						'source' => 'sidebars',
					),
				),
			),
			'is_clickable' => array(
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'title' => esc_html__('Show details page', 'cws-essentials' ),
				'atts' => 'checked data-options="e:link_to;e:add_btn;"',
			),
			'link_to' 		=> array(
				'type' 		=> 'select',
				'title' 	=> esc_html__( 'Link Options', 'cws-essentials' ),
				'atts' => 'data-options="select:options"',
				'source' 	=> array(
					'none' 			=> array(esc_html__( 'None', 'cws-essentials' ), false, 'd:link_custom_url;'),
					'post' 			=> array(esc_html__( 'Post', 'cws-essentials' ), false, 'd:link_custom_url;'),
					'custom_url' 	=> array(esc_html__( 'Custom Url', 'cws-essentials' ), false, 'e:link_custom_url;' ),
				)
			),
			'link_custom_url' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Custom url', 'cws-essentials' ),
				'addrowclasses' => 'disable',				
			),	
			'add_btn' => array(
				'type' => 'checkbox',
				'title' => esc_html__('Add Button Link', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-options="e:title_btn;"',
			),
			'title_btn' => array(
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Title Link Button', 'cws-essentials' ),
				'addrowclasses' => 'disable',				
			),

			'our_staff' => array(
				'title' => esc_html__( 'Teacher', 'cws-essentials' ),
				'type' => 'post_type',
				'atts' => 'multiple',
				'post_type' => 'cws_staff',
				'source' => array(),
			),
			'show_staff' => array(
				'type' => 'checkbox',
				'title' => esc_html__('Show Details Teacher Page', 'cws-essentials' ),
				'atts' => 'checked',
			),
			'price' => array(
				'type' => 'text',
				'addrowclasses' => 'box',
				'title' => esc_html__( 'Price', 'cws-essentials' ),
				),
			'date_events' => array(
				'type' => 'text',
				'addrowclasses' => 'box',
				'title' => esc_html__( 'Date Events', 'cws-essentials' ),
				),			
			'time_events' => array(
				'type' => 'text',
				'title' => esc_html__( 'Time Events', 'cws-essentials' ),
				),
			'destinations' => array(
				'type' => 'text',
				'addrowclasses' => 'box',
				'title' => esc_html__( 'Destination Events', 'cws-essentials' ),
				),
			'show_featured' => array(
				'title' => esc_html__( 'Show featured image on single', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked data-options="e:wide_featured;"',
			),
			'wide_featured' => array(
				'title' => esc_html__( 'Wide featured image', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'disable checkbox box',
			),
			'carousel' => array(
				'title' => esc_html__( 'Display items carousel for this post', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked',
			),
			'show_related' => array(
				'title' => esc_html__( 'Show related items', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked data-options="e:rpo_title;e:rpo_cols;e:rpo_items_count"',
			),
			'rpo_title' => array(
				'type' => 'text',
				'addrowclasses' => 'box',
				'title' => esc_html__( 'Title', 'cws-essentials' ),
				'value' => esc_html__( 'Related items', 'cws-essentials' )
				),
			'rpo_cols' => array(
				'type' => 'select',
				'title' => esc_html__( 'Columns', 'cws-essentials' ),
				'addrowclasses' => 'box',
				'source' => array(
					'1' => array(esc_html__( 'one', 'cws-essentials' ), false),
					'2' => array(esc_html__( 'two', 'cws-essentials' ), false),
					'3' => array(esc_html__( 'three', 'cws-essentials' ), false),
					'4' => array(esc_html__( 'four', 'cws-essentials' ), true),
					),
				),
			'rpo_categories' => array(
				'title' => esc_html__( 'Categories', 'cws-essentials' ),
				'type' => 'taxonomy',
				'atts' => 'multiple',
				'taxonomy' => 'cws_classes_cat',
				'source' => array(),
			),
			'rpo_items_count' => array(
				'type' => 'number',
				'title' => esc_html__( 'Number of items to show', 'cws-essentials' ),
				'addrowclasses' => 'box',
				'value' => '4'
			),
			'work_days_group' => array(
				'type' => 'group',
				'addrowclasses' => 'group sortable',
				'title' => esc_html__('Working Days', 'cws-essentials' ),
				'button_title' => esc_html__('Add New Working Days', 'cws-essentials' ),
				'button_icon' => 'fa fa-plus',
				'layout' => array(
					'title' => array(
						'type' => 'text',
						'atts' => 'data-role="title"',
						'title' => esc_html__('Working Days', 'cws-essentials' ),
						'value' => "Monday"
					),
					'from' => array(
						'type' => 'text',
						'title' => esc_html__( 'From', 'cws-essentials' ),
						'value' => ""
					),	
					'to' => array(
						'type' => 'text',
						'title' => esc_html__( 'To', 'cws-essentials' ),
						'value' => ""
					),		
				)
			),
		);

		if ( is_customize_preview() && $a) { 
			switch (get_post_type($a)) {
				case 'cws_portfolio':
				$this->mb_page_layout = $this->mb_portfolio_layout;
				break;				
				case 'cws_staff':
				$this->mb_page_layout = $this->mb_staff_layout;
				break;				
				case 'cws_classes':
				$this->mb_page_layout = $this->mb_classes_layout;
				break;
			}
		}

		self::$instance = $this;
		$this->init();
	}

	public static function get_instance() {
		return self::$instance;
	}

	private function init() {
		add_action( 'add_meta_boxes', array($this, 'post_addmb') );
		add_action( 'add_meta_boxes_cws_testimonials', array($this, 'testimonials_addmb') );
		add_action( 'add_meta_boxes_cws_portfolio', array($this, 'portfolio_addmb') );
		add_action( 'add_meta_boxes_cws_classes', array($this, 'classes_addmb') );
		add_action( 'add_meta_boxes_cws_staff', array($this, 'staff_addmb') );
		add_action( 'add_meta_boxes_megamenu_item', array($this, 'mgmenu_addmb') );

		//add_action( 'admin_enqueue_scripts', array($this, 'mb_script_enqueue') );
		add_action( 'save_post', array($this, 'post_metabox_save'), 11, 2 );
	}

	public function testimonials_addmb() {
		add_meta_box( 'cws-post-metabox-id-1', 'CWS Testimonials Options', array($this, 'mb_testimonials_callback'), 'cws_testimonials', 'normal', 'high' );
	}	

	public function classes_addmb() {
		add_meta_box( 'cws-post-metabox-id-1', 'CWS Classes Options', array($this, 'mb_classes_callback'), 'cws_classes', 'normal', 'high' );
	}	

	public function mgmenu_addmb() {
		add_meta_box( 'cws-post-metabox-id-1', 'CWS Megamenu Options', array($this, 'mb_mgmenu_callback'), 'megamenu_item', 'normal', 'high' );
	}

	public function portfolio_addmb() {
		add_meta_box( 'cws-post-metabox-id-1', 'CWS Portfolio Options', array($this, 'mb_portfolio_callback'), 'cws_portfolio', 'normal', 'high' );
	}

	public function staff_addmb() {
		add_meta_box( 'cws-post-metabox-id-1', 'CWS Staff Options', array($this, 'mb_staff_callback'), 'cws_staff', 'normal', 'high' );
	}

	public function post_addmb() {
		add_meta_box( 'cws-post-metabox-id-1', 'CWS Post Options', array($this, 'mb_post_callback'), 'post', 'normal', 'high' );
		add_meta_box( 'cws-post-metabox-id-2', 'Header Image', array($this, 'mb_post_side_callback'), 'post', 'side', 'low' );
		add_meta_box( 'cws-post-metabox-id-3', 'CWS Page Options', array($this, 'mb_page_callback'), 'page', 'normal', 'high' );
	}

	public function mb_staff_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );
		if (function_exists('cws_core_cwsfw_fillMbAttributes') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $this->mb_staff_layout);
			}
			echo cws_core_cwsfw_print_layout($this->mb_staff_layout, 'cws_mb_');
		}
	}

	public function mb_mgmenu_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$mb_attr = array(
			'fw_mgmenu' => array(
				'type' => 'checkbox',
				'title' => esc_html__('Disable Full Width Megamenu', 'cws-essentials' ),
				'atts' => 'data-options="e:mgmenu_width;e:mgmenu_pos"',
				'addrowclasses' => 'grid-col-12',
			),
			'mgmenu_width' => array(
				'title' 		=> esc_html__( 'Set Fixed Width (in px)', 'cws-essentials' ),
				'type' 			=> 'text',
				'addrowclasses' => 'disable grid-col-12',
				'value'			=> '1170',
			),
			'mgmenu_pos' => array(
				'title' 		=> esc_html__( 'Dropdown Position', 'cws-essentials' ),
				'type' 			=> 'select',
				'addrowclasses' => 'disable grid-col-12',
				'source' => array(
					'center' => array(esc_html__( 'Center', 'cws-essentials' ), true),
					'left' => array(esc_html__( 'Left', 'cws-essentials' ), false),
					'right' => array(esc_html__( 'Right', 'cws-essentials' ), false),
				),
			),
		);

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );
		if (function_exists('cws_core_cwsfw_fillMbAttributes') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $mb_attr);
			}
			echo cws_core_cwsfw_print_layout($mb_attr, 'cws_mb_');
		}
	}

	public function mb_page_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );
		if (function_exists('cws_core_cwsfw_fillMbAttributes') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $this->mb_page_layout);
			}
			echo cws_core_cwsfw_print_layout($this->mb_page_layout, 'cws_mb_');
		}
	}

	public function mb_testimonials_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$mb_attr = array(
			'carousel' => array(
				'title' => esc_html__( 'Display controls', 'cws-essentials' ),
				'type' => 'checkbox',
				'addrowclasses' => 'checkbox',
				'atts' => 'checked',
			),
		);

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );
		if (function_exists('cws_core_cwsfw_fillMbAttributes') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $mb_attr);
			}
			echo cws_core_cwsfw_print_layout($mb_attr, 'cws_mb_');
		}

	}

	public function mb_portfolio_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );
		if (function_exists('cws_core_build_layout') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $this->mb_portfolio_layout);
			}
			echo cws_core_cwsfw_print_layout($this->mb_portfolio_layout, 'cws_mb_');
		}
	}

	public function mb_classes_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );
		if (function_exists('cws_core_cwsfw_fillMbAttributes') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $this->mb_classes_layout);
			}
			echo cws_core_cwsfw_print_layout($this->mb_classes_layout, 'cws_mb_');
		}
	}

	public function mb_post_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );

		if (function_exists('cws_core_build_layout') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $this->mb_post_layout);
			}
			echo cws_core_cwsfw_print_layout($this->mb_post_layout, 'cws_mb_');
		}		
	}

	public function mb_post_side_callback( $post ) {
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$cws_stored_meta = get_post_meta( $post->ID, 'cws_mb_post' );

		$mb_attr = array(
			'post_header_box_image' => array(
				'title' => esc_html__( 'Image', 'cws-essentials' ),
				'addrowclasses' => 'hide_label',
				'type' => 'media',
			),
		);
		if (function_exists('cws_core_build_layout') ) {
			if (!empty($cws_stored_meta[0])) {
				cws_core_cwsfw_fillMbAttributes($cws_stored_meta[0], $mb_attr);
			}
			echo cws_core_cwsfw_print_layout($mb_attr, 'cws_mb_');
		}
	}

	public function mb_script_enqueue($a) {
		global $pagenow;
		if( ($a == 'widgets.php' || $a == 'post-new.php' || $a == 'post.php' || $a == 'edit-tags.php') && ('customize.php' !== $pagenow) ) {
			wp_enqueue_script('select2-js', get_template_directory_uri() . '/core/js/select2/select2.js', array('jquery') );
			wp_enqueue_style('select2-css', get_template_directory_uri() . '/core/js/select2/select2.css', false, '2.0.0' );
			/*wp_enqueue_script('aasana-metaboxes-js', get_template_directory_uri() . '/core/js/metaboxes.js', array('jquery') );
			wp_enqueue_style('aasana-metaboxes-css', get_template_directory_uri() . '/core/css/metaboxes.css', false, '2.0.0' );*/
			wp_enqueue_script('custom-user-js', get_template_directory_uri() . '/core/js/user.js', array('jquery') );
			wp_enqueue_media();

			wp_enqueue_style( 'wp-color-picker');
			wp_enqueue_script( 'wp-color-picker');
			wp_enqueue_style( 'mb_post_css' );
		} elseif ($a == 'user-edit.php' || $a == 'profile.php' || $a == 'edit-tags.php' || $a == 'term.php') {
			wp_enqueue_media();
			wp_enqueue_script('select2-js', get_template_directory_uri() . '/core/js/select2/select2.js', array('jquery') );
			wp_enqueue_style('select2-css', get_template_directory_uri() . '/core/js/select2/select2.css', false, '2.0.0' );
			wp_enqueue_script('aasana-metaboxes-js', get_template_directory_uri() . '/core/js/metaboxes.js', array('jquery') );
			wp_enqueue_style('aasana-metaboxes-css', get_template_directory_uri() . '/core/css/metaboxes.css', false, '2.0.0' );
			wp_enqueue_script('custom-user-js', get_template_directory_uri() . '/core/js/user.js', array('jquery') );
		}
	}

	public function post_metabox_save( $post_id, $post ) {
		if ( in_array($post->post_type, array('post', 'page', 'cws_testimonials', 'cws_portfolio', 'cws_staff', 'cws_classes', 'megamenu_item')) ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;

			if ( !isset( $_POST['mb_nonce']) || !wp_verify_nonce($_POST['mb_nonce'], 'cws_mb_nonce') )
				return;

			if ( !current_user_can( 'edit_post', $post->ID ) )
				return;

			$save_array = array();

			foreach($_POST as $key => $value) {
				if (0 === strpos($key, 'cws_mb_')) {
					if ('on' === $value) {
						$value = '1';
					}
					if (is_array($value)) {
						foreach ($value as $k => $val) {
							if (is_array($val)) {
								if (!$this->is_assoc($val)) {
									// check for dummy key and delete it if there are
									if ('!!!dummy!!!' === $val[0]) {
										array_shift($val);
									}
								}
								$save_array[mb_substr($key, 7)][$k] = $val;
							} else {
								$save_array[mb_substr($key, 7)][$k] = esc_html($val);
							}
						}
					} else {
						$save_array[mb_substr($key, 7)] = esc_html($value);
					}
				}
			}
			if (!empty($save_array)) {
				update_post_meta($post_id, 'cws_mb_post', $save_array);
			}
		}
	}

	private function is_assoc(array $array) {
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}
}
?>