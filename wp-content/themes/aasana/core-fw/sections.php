<?php
function cwsfw_get_sections() {
	$l_components = cwsfw_get_local_components();
	$g_components = array();
	if (function_exists('cws_core_get_base_components')) {
		$g_components = cws_core_get_base_components();
		$g_components = cws_core_merge_components($g_components, $l_components);
	}

	$settings = array(
		'general_setting' => array(
			'type' => 'section',
			'title' => esc_html__( 'Header', 'aasana' ),
			'icon' => array('fa', 'header'),
			// 'active' => true // true by default
			'layout' => array(
				'general_cont' => array(
					'type' => 'tab',
					'init' => 'open',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'General', 'aasana' ),
					'layout' => array(
						'header_zone' => array(
							'type' => 'group',
							'addrowclasses' => 'group sortable drop',
							'title' => esc_html__('Header order', 'aasana' ),
							'button_title' => esc_html__('Add new sidebar', 'aasana' ),
							'value' => array(
								array('title' => 'Logo','val' => 'logo_box'),
								array('title' => 'Top Bar','val' => 'top_bar_box'),
								array('title' => 'Header Zone','val' => 'drop_zone_start'),						
								array('title' => 'Menu','val' => 'menu_box'),
								array('title' => 'Title area','val' => 'header_box'),
								array('title' => 'Header Zone','val' => 'drop_zone_end'),					
							),
							'layout' => array(
								'title' => array(
									'type' => 'text',
									'value' => '',
									'atts' => 'data-role="title"',
									'title' => esc_html__('Sidebar', 'aasana' ),
								),

								'val' => array(
									'type' => 'text',
								)

							)
						),
						'fixed_header' => array(
							'title' => esc_html__( 'Apply Fixed header', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
						),							
						'customizer_header' => array(
							'title' => esc_html__( 'Customize', 'aasana' ),
							'addrowclasses' => 'checkbox',
							'type' => 'checkbox',
							'atts' => 'checked data-options="e:header_image;e:header_image_style;e:header_image_position;e:header_color_overlay_type;e:header_spacings;e:override_topbar_color;e:override_menu_color;e:override_menu_border;e:header_image_size;e:header_image_repeat"',
						),
						'header_image' => array(
							'title' => esc_html__( 'Background Image', 'aasana' ),
							'addrowclasses' => 'grid-col-2 disable row-eq-height-150',
							'type' => 'media',
						),
						'header_image_style' => array(
							'title' => esc_html__( 'Style', 'aasana' ),
							'type' => 'radio',
							'addrowclasses' => 'grid-col-2 disable row-eq-height-150',
							'value' => array(
								'none' => array( esc_html__( 'Scroll', 'aasana' ),  false, '' ),
								'fixed' => array( esc_html__( 'Fixed', 'aasana' ),  true, '' ),
								'local' => array( esc_html__( 'Local', 'aasana' ),  false, '' ),
							),
						),
						'header_image_position' => array(
							'title' => esc_html__( 'Position', 'aasana' ),
							'addrowclasses' => 'grid-col-2 row-eq-height-150',
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
							'title' => esc_html__( 'Size', 'aasana' ),
							'type' => 'radio',
							'addrowclasses' => 'grid-col-2 disable row-eq-height-150',
							'value' => array(
								'initial' => array( esc_html__( 'Initial', 'aasana' ),  false, '' ),
								'contain' => array( esc_html__( 'Contain', 'aasana' ),  false, '' ),
								'cover' => array( esc_html__( 'Cover', 'aasana' ),  true, '' ),
							),
						),						
						'header_image_repeat' => array(
							'title' => esc_html__( 'Reapeat', 'aasana' ),
							'type' => 'radio',
							'addrowclasses' => 'grid-col-2 disable row-eq-height-150',
							'value' => array(
								'no-repeat' => array( esc_html__( 'No repeat', 'aasana' ),  true, '' ),
								'repeat' => array( esc_html__( 'Repeat', 'aasana' ),  false, '' ),
								'repeat-x' => array( esc_html__( 'Repeat X', 'aasana' ),  false, '' ),
								'repeat-y' => array( esc_html__( 'Repeat Y', 'aasana' ),  false, '' ),
							),
						),
						'header_color_overlay_type'	=> array(
							'title'		=> esc_html__( 'Add Color overlay', 'aasana' ),
							'addrowclasses' => 'grid-col-4 disable clear',
							'type'	=> 'select',
							'source'	=> array(
								'none' => array( esc_html__( 'None', 'aasana' ),  false, 'd:header_color_overlay_opacity;d:header_overlayc;d:header_gradient_settings;' ),
								'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:header_color_overlay_opacity;e:header_overlayc;d:header_gradient_settings;' ),
								'gradient' => array( esc_html__( 'Gradient', 'aasana' ), true, 'e:header_color_overlay_opacity;d:header_overlayc;e:header_gradient_settings;' )
							),
						),
						'header_overlayc'	=> array(
							'title'	=> esc_html__( 'Color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'addrowclasses' => 'grid-col-4 disable clear',
							'value' => AASANA_COLOR,
							'type'	=> 'text',
						),
						'header_color_overlay_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity (%)', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'value' => '80',
							'addrowclasses' => 'grid-col-4 disable',
						),

						'header_gradient_settings' => array(
							'title' => esc_html__( 'Gradient Settings', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'grid-col-12 disable groups',
							'layout' => array(
								'first_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'From', 'aasana' ),
									'atts' => 'data-default-color="#8e75cd"',
									'value'  => '#8e75cd'
								),
								'second_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'To', 'aasana' ),
									'atts' => 'data-default-color="#c149d1"',
									'value'  => '#c149d1'
								),
								'first_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
									'value' => '100',
								),
								'second_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
									'value' => '100',
								),
								'type' => array(
									'title' => esc_html__( 'Gradient type', 'aasana' ),
									'type' => 'radio',
									'addrowclasses' => 'grid-col-4',
									'value' => array(
										'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
										'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
									),
								),
								'linear_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-6 disable',
									'layout' => array(
										'angle' => array(
											'type' => 'number',
											'title' => esc_html__( 'Angle', 'aasana' ),
											'value' => '45',
										),
									)
								),
								'radial_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-8 disable',
									'layout' => array(
										'shape_settings' => array(
											'title' => esc_html__( 'Shape', 'aasana' ),
											'addrowclasses' => 'grid-col-4',
											'type' => 'radio',
											'value' => array(
												'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
												'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
											),
										),
										'shape' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'addrowclasses' => 'grid-col-4',
											'type' => 'radio',
											'value' => array(
												'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
												'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
											),
										),
										'size_keyword' => array(
											'type' => 'select',
											'title' => esc_html__( 'Size keyword', 'aasana' ),
											'addrowclasses' => 'grid-col-4 disable',
											'source' => array(
												'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
												'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
												'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
												'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
											),
										),
										'size' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-4 disable',
											'title' => esc_html__( 'Size', 'aasana' ),
											'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
										),
									)
								)
							)
						),						
						'override_menu_color'	=> array(
							'title'	=> esc_html__( 'Override Menu\'s Font Color', 'aasana' ),
							'atts' => 'data-default-color="#ffffff"',
							'addrowclasses' => 'grid-col-4 disable clear',
							'value' => '#ffffff',
							'type'	=> 'text',
						),						
						'override_menu_border'	=> array(
							'title'	=> esc_html__( 'Override Menu\'s Border Color', 'aasana' ),
							'atts' => 'data-default-color="#ffffff"',
							'addrowclasses' => 'grid-col-4 disable',
							'value' => '#ffffff',
							'type'	=> 'text',
						),
						'override_topbar_color'	=> array(
							'title'	=> esc_html__( 'Override TopBar\'s Font Color', 'aasana' ),
							'atts' => 'data-default-color="#ffffff"',
							'addrowclasses' => 'grid-col-4 disable',
							'value' => '#ffffff',
							'type'	=> 'text',
						),

						'header_spacings' => array(
							'title' => esc_html__( 'Add Spacings', 'aasana' ),
							'type' => 'margins',
							'addrowclasses' => 'grid-col-12 disable two-inputs',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '0'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '0'),
							),
						),
					)
				),
				'logo_cont' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Logo', 'aasana' ),
					'layout' => array(
						'enable_logo' => array(
							'title' => esc_html__( 'Logo', 'aasana' ),
							'addrowclasses' => 'checkbox alt',
							'type' => 'checkbox',
							'atts' => 'checked',
						),
						'default_logo'	=> array(
							'title'		=> esc_html__( 'Default Logo Variation', 'aasana' ),
							'addrowclasses' => 'grid-col-12',
							'type'	=> 'select',
							'source'	=> array(
								'dark' => array( esc_html__( 'Dark', 'aasana' ),  true, '' ),
								'light' => array( esc_html__( 'Light', 'aasana' ),  false, '' ),
							),
						),
						'logo_dark' => array(
							'title' => esc_html__( 'Dark Logo', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-6',
							'layout' => array(
								'is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),
						'logo_light' => array(
							'title' => esc_html__( 'Light Logo', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-6',
							'layout' => array(
								'is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),			
						'logo-dimensions' => array(
							'title' => esc_html__( 'Dimensions', 'aasana' ),
							'type' => 'dimensions',
							'addrowclasses' => 'grid-col-4',
							'value' => array(
								'width' => array('placeholder' => esc_html__( 'Width', 'aasana' ), 'value' => '180'),
								'height' => array('placeholder' => esc_html__( 'Height', 'aasana' ), 'value' => '65'),
								),
						),
						'logo-margin' => array(
							'title' => esc_html__( 'Margins (px)', 'aasana' ),
							'type' => 'margins',
							'addrowclasses' => 'grid-col-4',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '18'),
								'left' => array('placeholder' => esc_html__( 'left', 'aasana' ), 'value' => '0'),
								'right' => array('placeholder' => esc_html__( 'Right', 'aasana' ), 'value' => '0'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '12'),
								),
						),
						'logo-position' => array(
							'title' => esc_html__( 'Position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'grid-col-4',
							'value' => array(
								'left' => array( esc_html__('Left', 'aasana'), true, 'd:logo_overlay;d:site_name_in_menu;e:logo_with_site_name;d:logo_box', '/img/align-left.png' ),
								'center' =>array( esc_html__('Center', 'aasana'), false, 'e:logo_overlay;e:site_name_in_menu;e:logo_with_site_name;e:logo_box', '/img/align-center.png', ),
								'right' =>array( esc_html__('Right', 'aasana'), false, 'd:logo_overlay;d:site_name_in_menu;e:logo_with_site_name;d:logo_box', '/img/align-right.png', ),
								'in-menu' =>array( esc_html__('Inside', 'aasana'), false, 'd:logo_overlay;d:site_name_in_menu;e:logo_with_site_name;d:logo_box', '/img/align-inner.png', ),
							),
						),						
						'logo_box' => array(
							'type' => 'fields',
							'addrowclasses' => 'disable grid-col-12',
							'layout' => array(
								'border'	=> array(
									'title'		=> esc_html__( 'Border', 'aasana' ),
									'addrowclasses' => 'box',
									'type'	=> 'select',
									'atts' => 'multiple data-none="d:border_color;d:border_type"',
									'source'	=> array(
										'top' => array( esc_html__( 'Top', 'aasana' ),  false, 'e:border_color;e:border_type;' ),
										'bottom' => array( esc_html__( 'Bottom', 'aasana' ), true, 'e:border_color;e:border_type;' ),
									),
								),
								'border_type'	=> array(
									'title'		=> esc_html__( 'Type', 'aasana' ),
									'addrowclasses' => 'box',
									'type'	=> 'select',
									'source'	=> array(
										'dotted' => array( esc_html__( 'Dotted', 'aasana' ),  false, '' ),
										'dashed' => array( esc_html__( 'Dashed', 'aasana' ),  false, '' ),
										'solid' => array( esc_html__( 'Solid', 'aasana' ), true, '' ),
									),
								),
								'border_color'	=> array(
									'title'	=> esc_html__( 'Color', 'aasana' ),
									'atts' => 'data-default-color="#e6e6e6"',
									'addrowclasses' => 'box',
									'value' => '#e6e6e6',
									'type'	=> 'text',
								),
							),
						),
						'logo_overlay' => array(
							'type' => 'fields',
							'addrowclasses' => 'disable box inside-box groups grid-col-12',
							'customizer' 	=> array( 'show' => false ),
							'layout' => array(
								'type'	=> array(
									'title'		=> esc_html__( 'Add Color overlay', 'aasana' ),
									'addrowclasses' => 'new_row grid-col-4',
									'type'	=> 'select',
									'source'	=> array(
										'none' => array( esc_html__( 'None', 'aasana' ),  true, 'd:opacity;d:color;d:gradient_settings;' ),
										'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:opacity;e:color;d:gradient_settings;' ),
										'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'e:opacity;d:color;e:gradient_settings;' )
									),
								),
								'color'	=> array(
									'title'	=> esc_html__( 'Color', 'aasana' ),
									'atts' => 'data-default-color="' . AASANA_COLOR . '"',
									'addrowclasses' => 'grid-col-4 disable',
									'value' => AASANA_COLOR,
									'type'	=> 'text',
								),
								'opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'Opacity (%)', 'aasana' ),
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'value' => '40',
									'addrowclasses' => 'grid-col-4 disable',
								),
								'gradient_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-12 disable groups',
									'layout' => array(
										'first_color' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-3',
											'title' => esc_html__( 'From', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'second_color' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-3',
											'title' => esc_html__( 'To', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'first_color_opacity' => array(
											'type' => 'number',
											'addrowclasses' => 'grid-col-3',
											'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'second_color_opacity' => array(
											'type' => 'number',
											'addrowclasses' => 'grid-col-3',
											'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'type' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'type' => 'radio',
											'addrowclasses' => 'grid-col-3',
											'value' => array(
												'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
												'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
											),
										),
										'linear_settings' => array(
											'type' => 'fields',
											'addrowclasses' => 'grid-col-3 disable',
											'layout' => array(
												'angle' => array(
													'type' => 'number',
													'title' => esc_html__( 'Angle', 'aasana' ),
													'value' => '45',
												),
											)
										),
										'radial_settings' => array(
											'type' => 'fields',
											'addrowclasses' => 'grid-col-8 disable',
											'layout' => array(
												'shape_settings' => array(
													'title' => esc_html__( 'Shape', 'aasana' ),
													'addrowclasses' => 'grid-col-4',
													'type' => 'radio',
													'value' => array(
														'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
														'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
													),
												),
												'shape' => array(
													'title' => esc_html__( 'Gradient type', 'aasana' ),
													'type' => 'radio',
													'addrowclasses' => 'grid-col-4',
													'value' => array(
														'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
														'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
													),
												),
												'size_keyword' => array(
													'type' => 'select',
													'title' => esc_html__( 'Size keyword', 'aasana' ),
													'addrowclasses' => 'grid-col-4 disable',
													'source' => array(
														'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
														'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
														'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
														'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
													),
												),
												'size' => array(
													'type' => 'text',
													'addrowclasses' => 'grid-col-4 disable',
													'title' => esc_html__( 'Size', 'aasana' ),
													'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
												),
											)
										)
									)
								),
							),
						),


						'logo_sticky' => array(
							'title' => esc_html__( 'Sticky logo', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-6',
							'layout' => array(
								'logo_sticky_is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution sticky logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),

						'logo_mobile' => array(
							'title' => esc_html__( 'Mobile logo', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-6',
							'layout' => array(
								'logo_mobile_is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution mobile logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),
						'logo-dimensions-sticky' => array(
							'title' => esc_html__( 'Dimensions Sticky', 'aasana' ),
							'type' => 'dimensions',
							'addrowclasses' => 'grid-col-6',
							'value' => array(
								'width' => array('placeholder' => esc_html__( 'Width', 'aasana' ), 'value' => ''),
								'height' => array('placeholder' => esc_html__( 'Height', 'aasana' ), 'value' => ''),
								),
						),						
						'logo-dimensions-mobile' => array(
							'title' => esc_html__( 'Dimensions Mobile', 'aasana' ),
							'type' => 'dimensions',
							'addrowclasses' => 'grid-col-6',
							'value' => array(
								'width' => array('placeholder' => esc_html__( 'Width', 'aasana' ), 'value' => ''),
								'height' => array('placeholder' => esc_html__( 'Height', 'aasana' ), 'value' => ''),
								),
						),
						'site_name_in_menu' => array(
							'title' => esc_html__( 'Insert Site Name into Menu', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-6',
							'type' => 'checkbox',
						),
						'logo_with_site_name' => array(
							'title' => esc_html__( 'Add Site Name to the Logo', 'aasana' ),
							'addrowclasses' => 'new_row checkbox grid-col-12',
							'type' => 'checkbox',
							'atts' => 'checked'
						),

					)
				),
				'menu_cont' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Menu', 'aasana' ),
					'layout' => array(
						'enable_menu' => array(
							'title' => esc_html__( 'Menu', 'aasana' ),
							'addrowclasses' => 'checkbox alt grid-col-12',
							'type' => 'checkbox',
							'atts' => 'checked',
						),
						'menu-position' => array(
							'title' => esc_html__( 'Menu Position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'grid-col-4',
							'value' => array(
								'left' => array( esc_html__( 'Left', 'aasana' ), 	false, '', '/img/align-left.png' ),
								'center' =>array( esc_html__( 'Center', 'aasana' ), false, '', '/img/align-center.png' ),
								'right' =>array( esc_html__( 'Right', 'aasana' ), true, '', '/img/align-right.png' ),
							),
						),
						'search_place' => array(
							'title' => esc_html__( 'Search Icon Position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'grid-col-4',
							'value' => array(
								'none' => array( esc_html__( 'None', 'aasana' ), 	false, '', '/img/no_layout.png' ),
								'top' => array( esc_html__( 'Top', 'aasana' ), 	false, '', '/img/search-social-right.png' ),
								'left' =>array( esc_html__( 'Left', 'aasana' ), false, '', '/img/search-menu-left.png' ),
								'right' =>array( esc_html__( 'Right', 'aasana' ), true, '', '/img/search-menu-right.png' ),
							),
						),						
						'mobile_menu_place' => array(
							'title' => esc_html__( 'Mobile Menu Position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'grid-col-4',
							'value' => array(
								'left' =>array( esc_html__( 'Left', 'aasana' ), false, '', '/img/hamb-left.png' ),
								'center' =>array( esc_html__( 'Center', 'aasana' ), false, '', '/img/hamb-center.png' ),
								'right' =>array( esc_html__( 'Right', 'aasana' ), true, '', '/img/hamb-right.png' ),
							),
						),
						'search_open_background'	=> array(
							'title'		=> esc_html__( 'Search Background Color', 'aasana' ),
							'addrowclasses' => 'grid-col-4',
							'type'	=> 'select',
							'source'	=> array(
								'default' => array( esc_html__( 'Default', 'aasana' ),  false, 'd:search_color_overlay_opacity;d:search_overlayc;d:search_gradient_settings;' ),
								'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:search_color_overlay_opacity;e:search_overlayc;d:search_gradient_settings;' ),
								'gradient' => array( esc_html__( 'Gradient', 'aasana' ), true, 'e:search_color_overlay_opacity;d:search_overlayc;e:search_gradient_settings;' )
							),
						),
						'search_overlayc'	=> array(
							'title'	=> esc_html__( 'Color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'addrowclasses' => 'grid-col-4 disable',
							'value' => AASANA_COLOR,
							'type'	=> 'text',
						),
						'search_color_overlay_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity (%)', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'value' => '40',
							'addrowclasses' => 'grid-col-4 disable',
						),

						'search_gradient_settings' => array(
							'title' => esc_html__( 'Gradient Settings', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'grid-col-12 disable groups',
							'layout' => array(
								'first_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'From', 'aasana' ),
									'atts' => 'data-default-color="#8e75cd"',
								),
								'second_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'To', 'aasana' ),
									'atts' => 'data-default-color="#c149d1"',
								),
								'first_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
									'value' => '100',
								),
								'second_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
									'value' => '100',
								),
								'type' => array(
									'title' => esc_html__( 'Gradient type', 'aasana' ),
									'type' => 'radio',
									'addrowclasses' => 'grid-col-4',
									'value' => array(
										'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
										'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
									),
								),
								'linear_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-6 disable',
									'layout' => array(
										'angle' => array(
											'type' => 'number',
											'title' => esc_html__( 'Angle', 'aasana' ),
											'value' => '45',
										),
									)
								),
								'radial_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-8 disable',
									'layout' => array(
										'shape_settings' => array(
											'title' => esc_html__( 'Shape', 'aasana' ),
											'addrowclasses' => 'grid-col-4',
											'type' => 'radio',
											'value' => array(
												'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
												'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
											),
										),
										'shape' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'addrowclasses' => 'grid-col-4',
											'type' => 'radio',
											'value' => array(
												'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
												'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
											),
										),
										'size_keyword' => array(
											'type' => 'select',
											'title' => esc_html__( 'Size keyword', 'aasana' ),
											'addrowclasses' => 'grid-col-4 disable',
											'source' => array(
												'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
												'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
												'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
												'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
											),
										),
										'size' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-4 disable',
											'title' => esc_html__( 'Size', 'aasana' ),
											'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
										),
									)
								)
							)
						),	

						'show_header_slider_bg_color' => array(
							'title' => esc_html__( 'Add Background Color', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
							'atts' => 'data-options="e:header_outside_slider_bg_color;e:header_outside_slider_bg_opacity"',
						),
						'header_outside_slider_bg_color' => array(
							'title' => esc_html__( 'Color', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Color', 'aasana' ),
								'content' => esc_html__( 'This color is applied to header section including top bar.', 'aasana' ),
							),
							'atts' => 'data-default-color="#f9f9f9"',
							'value' => '#f9f9f9',
							'addrowclasses' => 'grid-col-6',
							'type' => 'text',
						),
						'header_outside_slider_bg_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'addrowclasses' => 'grid-col-6',
							'value' => '0'
						),
						'menu_font_color' => array(
							'type' => 'text',
							'title' => esc_html__( 'Override Font color', 'aasana' ),
							'atts' => 'data-default-color="#595959"',
							'value' => '#595959',
							'addrowclasses' => 'grid-col-4',
						),						
						'menu_overide_color' => array(
							'type' => 'text',
							'title' => esc_html__( 'Override Border color', 'aasana' ),
							'atts' => 'data-default-color="#595959"',
							'value' => '#595959',
							'addrowclasses' => 'grid-col-4',
						),
						'menu_margin' => array(
							'title' => esc_html__( 'Spacings', 'aasana' ),
							'type' => 'margins',
							'addrowclasses' => 'grid-col-4 two-inputs',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '12'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '12'),
							),
						),

						'mobile_background'	=> array(
							'title'		=> esc_html__( 'Mobile Background Color', 'aasana' ),
							'addrowclasses' => 'grid-col-4',
							'type'	=> 'select',
							'source'	=> array(
								'default' => array( esc_html__( 'Default', 'aasana' ),  true, 'd:mobile_overlay_opacity;d:mobile_overlayc;d:mobile_gradient_settings;' ),
								'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:mobile_overlay_opacity;e:mobile_overlayc;d:mobile_gradient_settings;' ),
								'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'e:mobile_overlay_opacity;d:mobile_overlayc;e:mobile_gradient_settings;' )
							),
						),
						'mobile_overlayc'	=> array(
							'title'	=> esc_html__( 'Color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'addrowclasses' => 'grid-col-4 disable',
							'value' => AASANA_COLOR,
							'type'	=> 'text',
						),
						'mobile_overlay_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity (%)', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'value' => '100',
							'addrowclasses' => 'grid-col-4 disable',
						),

						'mobile_gradient_settings' => array(
							'title' => esc_html__( 'Gradient Settings', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'grid-col-12 disable groups',
							'layout' => array(
								'first_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'From', 'aasana' ),
									'atts' => 'data-default-color=""',
								),
								'second_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'To', 'aasana' ),
									'atts' => 'data-default-color=""',
								),
								'first_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
									'value' => '100',
								),
								'second_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
									'value' => '100',
								),
								'type' => array(
									'title' => esc_html__( 'Gradient type', 'aasana' ),
									'type' => 'radio',
									'addrowclasses' => 'grid-col-6',
									'value' => array(
										'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
										'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
									),
								),
								'linear_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-6 disable',
									'layout' => array(
										'angle' => array(
											'type' => 'number',
											'title' => esc_html__( 'Angle', 'aasana' ),
											'value' => '45',
										),
									)
								),
								'radial_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-8 disable',
									'layout' => array(
										'shape_settings' => array(
											'title' => esc_html__( 'Shape', 'aasana' ),
											'addrowclasses' => 'grid-col-4',
											'type' => 'radio',
											'value' => array(
												'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
												'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
											),
										),
										'shape' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'addrowclasses' => 'grid-col-4',
											'type' => 'radio',
											'value' => array(
												'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
												'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
											),
										),
										'size_keyword' => array(
											'type' => 'select',
											'title' => esc_html__( 'Size keyword', 'aasana' ),
											'addrowclasses' => 'grid-col-4 disable',
											'source' => array(
												'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
												'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
												'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
												'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
											),
										),
										'size' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-4 disable',
											'title' => esc_html__( 'Size', 'aasana' ),
											'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
										),
									)
								)
							)
						),	
						'enable_mob_menu' => array(
							'title' => esc_html__( 'Enable mobile menu on all touch devices', 'aasana' ),
							'addrowclasses' => 'new_row checkbox grid-col-6',
							'atts' => 'checked',
							'type' => 'checkbox',
						),
						'show_header_outside_slider' => array(
							'title' => esc_html__( 'Header overlays slider', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-6',
							'type' => 'checkbox',
							'atts'	=> 'checked',
						),
						'show_sandwich_menu' => array(
							'title' => esc_html__( 'Show mobile menu on desktop PCs', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-6',
							'type' => 'checkbox',
						),
						'wide_menu' => array(
							'title' => esc_html__( 'Apply Full-Width Menu', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-6',
							'type' => 'checkbox',
							'atts'	=> 'checked',
						),						

					)
				),
				'sticky_menu_cont' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Sticky', 'aasana' ),
					'layout' => array(
						'menu-stick' => array(
							'title' => esc_html__( 'Sticky Menu', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12 alt',
							'type' => 'checkbox',
							'atts'	=> 'checked',
						),
						'stick-mode'	=> array(
							'title'		=> esc_html__( 'Select a Sticky\'s Mode', 'aasana' ),
							'type'	=> 'select',
							'addrowclasses' => 'grid-col-12',
							'source'	=> array(
								'smart' => array( esc_html__( 'Smart', 'aasana' ),  true ),
								'simple' => array( esc_html__( 'Simple', 'aasana' ), false ),
							),
						),
						'stick_bg_color' => array(
							'title' => esc_html__( 'Background color', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Background Color', 'aasana' ),
								'content' => esc_html__( 'This color is applied to header section including top bar.', 'aasana' ),
							),
							'atts' => 'data-default-color="#ffffff"',
							'value' => '#ffffff',
							'addrowclasses' => 'grid-col-4',
							'type' => 'text',
						),
						'stick_font_color' => array(
							'title' => esc_html__( 'Override Font color', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Override Font Color', 'aasana' ),
								'content' => esc_html__( 'This color is applied to main menu items only, submenus will use the color which is set in Typography section.<br /> This option is very useful when transparent menu is set.', 'aasana' ),
							),
							'atts' => 'data-default-color="#595959"',
							'value' => '#595959',
							'addrowclasses' => 'grid-col-4',
							'type' => 'text',
						),
						'stick_bg_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Header Opacity', 'aasana' ),
								'content' => esc_html__( 'This option will apply the transparent header when set to "0".', 'aasana' ),
							),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'addrowclasses' => 'grid-col-4',
							'value' => '30'
						),
						'stick_border'	=> array(
							'title'		=> esc_html__( 'Border', 'aasana' ),
							'addrowclasses' => 'grid-col-6',
							'type'	=> 'select',
							'source'	=> array(
								'none' => array( esc_html__( 'None', 'aasana' ),  false, 'd:stick_border_color;d:stick_border_type;' ),
								'top' => array( esc_html__( 'Top', 'aasana' ),  false, 'e:stick_border_color;e:stick_border_type;' ),
								'bottom' => array( esc_html__( 'Bottom', 'aasana' ), true, 'e:stick_border_color;e:stick_border_type;' ),
								'both' => array( esc_html__( 'Top & Bottom', 'aasana' ), false, 'e:stick_border_color;e:stick_border_type;' )
							),
						),
						'stick_border_type'	=> array(
							'title'		=> esc_html__( 'Type', 'aasana' ),
							'addrowclasses' => 'grid-col-3',
							'type'	=> 'select',
							'source'	=> array(
								'dotted' => array( esc_html__( 'Dotted', 'aasana' ),  false, '' ),
								'dashed' => array( esc_html__( 'Dashed', 'aasana' ),  false, '' ),
								'solid' => array( esc_html__( 'Solid', 'aasana' ), true, '' ),
							),
						),
						'stick_border_color'	=> array(
							'title'	=> esc_html__( 'Color', 'aasana' ),
							'atts' => 'data-default-color="#e6e6e6"',
							'addrowclasses' => 'grid-col-3 disable',
							'value' => '#e6e6e6',
							'type'	=> 'text',
						),
						'stick-shadow' => array(
							'title' => esc_html__( 'Add Shadow', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
						),
						'stick-on-mobile' => array(
							'title' => esc_html__( 'Enable Sticky menu on all touch devices', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
						),
					)
				),
				'styling_options_headers' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Title', 'aasana' ),
					'layout' => array(
						'title_area_switcher' => array(
							'title' => esc_html__( 'Title area', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12 alt',
							'type' => 'checkbox',
							'atts'	=> 'checked'
						),							
						'header_center' => array(
							'title' => esc_html__( 'Center Title & Breadcrumbs', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
							'atts'	=> 'checked'
						),
						'customize-title-area' => array(
							'title' => esc_html__( 'Customize', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
							'atts' => 'checked data-options="e:animate_title;e:slide_down_header;e:font_color;e:show_on_posts;e:show_on_archives;e:image;e:color_overlay_type;e:header_box_color_overlay_opacity;e:header_box_use_pattern;e:use_blur;e:effect;e:spacings;e:breadcrumbs_divider;e:breadcrumbs_dimensions;e:breadcrumbs-margin"',
						),
						'slide_down_header' => array(
							'title' => esc_html__( 'Slide down on Page Load', 'aasana' ),
							'addrowclasses' => 'disable checkbox grid-col-4',
							'type' => 'checkbox',
						),
						'show_on_posts' => array(
							'title' => esc_html__( 'Use Custom Settings on Posts', 'aasana' ),
							'addrowclasses' => 'disable checkbox grid-col-4',
							'type' => 'checkbox',
							'atts'	=> 'checked'
						),
						'show_on_archives' => array(
							'title' => esc_html__( 'Use Custom Settings on Archives', 'aasana' ),
							'addrowclasses' => 'disable checkbox grid-col-4',
							'type' => 'checkbox',
							'atts'	=> 'checked'
						),										
						'header_box' => array(
						'type' => 'fields',
						'addrowclasses' => 'grid-col-12',
						'layout' => array(
							'image' => array(
								'title' => esc_html__( 'Background image', 'aasana' ),
								'addrowclasses' => 'grid-col-6',
								'type' => 'media',
							),
							'effect' => array(
								'title' => esc_html__( 'Image style', 'aasana' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-6',
								'value' => array(
									'none' => array( esc_html__( 'Scroll', 'aasana'), true, 'd:parallax_options;d:scroll_parallax;' ),
									'fixed' => array( esc_html__( 'Fixed', 'aasana'), false, 'd:parallax_options;d:scroll_parallax;' ),
									'scroll_parallax' => array( esc_html__( 'Scroll Parallax', 'aasana' ), false, 'd:parallax_options;e:scroll_parallax;' ),
									'parallaxify' =>array( esc_html__( 'Parallaxify', 'aasana' ), false, 'e:parallax_options;d:scroll_parallax;' ),
								),
							),
							'scroll_parallax' => array(
								'title' => esc_html__( 'Add Motion Zoom to Header Image on Page Scroll', 'aasana' ),
								'addrowclasses' => 'disable checkbox grid-col-12',
								'type' => 'checkbox',
							),
							'parallax_options' => array(
								'title' => esc_html__( 'Parallax options', 'aasana' ),
								'type' => 'fields',
								'addrowclasses' => 'disable grid-col-12 groups',
								'layout' => array(
									'scalar_x' => array(
										'type' => 'number',
										'addrowclasses' => 'grid-col-6',
										'title' => esc_html__( 'x-axis parallax intensity', 'aasana' ),
										'placeholder' => esc_html__( 'Integer', 'aasana' ),
										'value' => '2'
									),
									'scalar_y' => array(
										'type' => 'number',
										'addrowclasses' => 'grid-col-6',										
										'title' => esc_html__( 'y-axis parallax intensity', 'aasana' ),
										'placeholder' => esc_html__( 'Integer', 'aasana' ),
										'value' => '2'
									),
									'limit_x' => array(
										'type' => 'number',
										'addrowclasses' => 'grid-col-6',
										'title' => esc_html__( 'Maximum x-axis shift', 'aasana' ),
										'placeholder' => esc_html__( 'Integer', 'aasana' ),
										'value' => '15'
									),
									'limit_y' => array(
										'type' => 'number',
										'addrowclasses' => 'grid-col-6',
										'title' => esc_html__( 'Maximum y-axis shift', 'aasana' ),
										'placeholder' => esc_html__( 'Integer', 'aasana' ),
										'value' => '15'
									),
								),
							),							
							'color_overlay_type'	=> array(
								'title'		=> esc_html__( 'Add Color overlay', 'aasana' ),
								'addrowclasses' => 'grid-col-4',
								'type'	=> 'select',
								'source'	=> array(
									'none' => array( esc_html__( 'None', 'aasana' ),  true, 'd:color_overlay_opacity;d:overlay_color;d:gradient_settings;' ),
									'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:color_overlay_opacity;e:overlay_color;d:gradient_settings;' ),
									'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'e:color_overlay_opacity;d:overlay_color;e:gradient_settings;' )
								),
							),
							'overlay_color'	=> array(
								'title'	=> esc_html__( 'Color', 'aasana' ),
								'atts' => 'data-default-color="' . AASANA_COLOR . '"',
								'addrowclasses' => 'disable grid-col-4',
								'value' => AASANA_COLOR,
								'type'	=> 'text',
							),
							'color_overlay_opacity' => array(
								'type' => 'number',
								'addrowclasses' => 'disable grid-col-4',
								'title' => esc_html__( 'Opacity', 'aasana' ),
								'placeholder' => esc_html__( 'In percents', 'aasana' ),
								'value' => '40'
							),
							'gradient_settings' => array(
								'type' => 'fields',
								'addrowclasses' => 'disable box inside-box groups grid-col-12',
								'layout' => array(
									'first_color' => array(
										'type' => 'text',
										'addrowclasses' => 'grid-col-3',
										'title' => esc_html__( 'From', 'aasana' ),
										'atts' => 'data-default-color=""',
									),
									'second_color' => array(
										'type' => 'text',
										'addrowclasses' => 'grid-col-3',
										'title' => esc_html__( 'To', 'aasana' ),
										'atts' => 'data-default-color=""',
									),
									'first_color_opacity' => array(
										'type' => 'number',
										'addrowclasses' => 'grid-col-3',
										'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
										'value' => '100',
									),
									'second_color_opacity' => array(
										'type' => 'number',
										'addrowclasses' => 'grid-col-3',
										'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
										'value' => '100',
									),
									'type' => array(
										'title' => esc_html__( 'Gradient type', 'aasana' ),
										'type' => 'radio',
										'addrowclasses' => 'grid-col-4',
										'value' => array(
											'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
											'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
										),
									),
									'linear_settings' => array(
										'type' => 'fields',
										'addrowclasses' => 'grid-col-6 disable',
										'layout' => array(
											'angle' => array(
												'type' => 'number',
												'title' => esc_html__( 'Angle', 'aasana' ),
												'value' => '45',
											),
										)
									),
									'radial_settings' => array(
										'type' => 'fields',
										'addrowclasses' => 'grid-col-6 disable',
										'layout' => array(
											'shape_settings' => array(
												'title' => esc_html__( 'Shape', 'aasana' ),
												'addrowclasses' => 'grid-col-6',
												'type' => 'radio',
												'value' => array(
													'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
													'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
												),
											),
											'shape' => array(
												'title' => esc_html__( 'Gradient type', 'aasana' ),
												'type' => 'radio',
												'addrowclasses' => 'grid-col-6',
												'value' => array(
													'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
													'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
												),
											),
											'size_keyword' => array(
												'type' => 'select',
												'title' => esc_html__( 'Size keyword', 'aasana' ),
												'addrowclasses' => 'grid-col-3 disable',
												'source' => array(
													'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
													'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
													'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
													'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
												),
											),
											'size' => array(
												'type' => 'text',
												'addrowclasses' => 'grid-col-3 disable',
												'title' => esc_html__( 'Size', 'aasana' ),
												'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
											),
										)
									),
								)
							),
							'font_color' => array(
								'title'	=> esc_html__( 'Override Font Color', 'aasana' ),
								'atts' => 'data-default-color="#ffffff"',
								'value' => '#ffffff',
								'addrowclasses' => 'grid-col-12',
								'type'	=> 'text',
							),							
							'header_box_use_pattern' => array(
								'title' => esc_html__( 'Add pattern', 'aasana' ),
								'addrowclasses' => 'disable checkbox grid-col-12',
								'type' => 'checkbox',
								'atts' => 'data-options="e:header_box_pattern_image;"',
							),
							'header_box_pattern_image' => array(
								'type' => 'media',
								'addrowclasses' => 'disable grid-col-12',
								'url-atts' => 'readonly',
							),							
							'breadcrumbs_divider' => array(
								'title' => esc_html__( 'Breadcrumbs Divider', 'aasana' ),
								'type' => 'media',
								'url-atts' => 'readonly',
								'addrowclasses' => 'disable grid-col-12',
								// 'value' => array( 'id' => '', 'src' => get_template_directory_uri() . '\img\logo_the8_128x128.png') ,
								'layout' => array(
									'is_high_dpi' => array(
										'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
										'addrowclasses' => 'checkbox',
										'type' => 'checkbox',
									),
								),
							),
							'breadcrumbs_dimensions' => array(
								'title' => esc_html__( 'Breadcrumbs Divider Dimensions', 'aasana' ),
								'type' => 'dimensions',
								'addrowclasses' => 'disable grid-col-12',
								'value' => array(
									'width' => array('placeholder' => esc_html__( 'Width', 'aasana' ), 'value' => ''),
									'height' => array('placeholder' => esc_html__( 'Height', 'aasana' ), 'value' => ''),
								),
							),
							'breadcrumbs-margin' => array(
								'title' => esc_html__( 'Margins (px)', 'aasana' ),
								'type' => 'margins',
								'addrowclasses' => 'disable grid-col-4',
								'value' => array(
									'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '0'),
									'left' => array('placeholder' => esc_html__( 'left', 'aasana' ), 'value' => '0'),
									'right' => array('placeholder' => esc_html__( 'Right', 'aasana' ), 'value' => '0'),
									'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '0'),
									),
							),
							'use_blur' => array(
								'title' => esc_html__( 'Apply blur', 'aasana' ),
								'addrowclasses' => 'checkbox grid-col-12',
								'type' => 'checkbox',
								'atts' => 'data-options="e:blur_intensity;"',
							),
							'blur_intensity' => array(
								'type' => 'number',
								'addrowclasses' => 'disable grid-col-12',
								'title' => esc_html__( 'Intensity', 'aasana' ),
								'placeholder' => esc_html__( 'In percents', 'aasana' ),
								'value' => '8'
							),
							'animate_title' => array(
								'title' => esc_html__( 'Add Mouse Scroll Animation', 'aasana' ),
								'addrowclasses' => 'disable checkbox grid-col-12',
								'type' => 'checkbox',
								'tooltip' => array(
									'title' => esc_html__( 'Documentation', 'aasana' ),
									'content' => esc_html__( 'Project on https://github.com/Prinzhorn/skrollr <a href="https://github.com/Prinzhorn/skrollr">GitHub</a>', 'aasana' ),
								),
								'atts' => 'checked data-options="e:animate_options;"',
							),
							'animate_options' => array(
								'type' => 'group',
								'addrowclasses' => 'disable group expander grid-col-12',
								'title' => esc_html__('Animation Steps', 'aasana' ),

								'button_title' => esc_html__('Add New Step', 'aasana' ),
								'layout' => array(
									'element'	=> array(
										'title'		=> esc_html__( 'Header Section', 'aasana' ),
										'type'	=> 'select',
										'source'	=> array(
											'title' => array( esc_html__( 'Title & Breadcrumbs', 'aasana' ),  true, '' ),
											'container' => array( esc_html__( 'Content Width', 'aasana' ),  false, '' ),
											'section' => array( esc_html__( 'Full Width', 'aasana' ), false, '' )
										),
									),
									'value' => array(
										'type' => 'number',
										'atts' => 'data-role="title"',
										'value' => '100',
										'title' => esc_html__('Top offset (in px)', 'aasana' ),
									),
									'styles' => array(
										'type' => 'textarea',
										'atts' => 'rows="5"',
										'title' => esc_html__('Styles CSS', 'aasana' ),
									),
								),
							),							
							'border'	=> array(
								'title'		=> esc_html__( 'Border', 'aasana' ),
								'addrowclasses' => 'grid-col-4',
								'type'	=> 'select',
								'atts' => 'multiple data-none="d:border_color;d:border_type"',
								'source'	=> array(
									'top' => array( esc_html__( 'Top', 'aasana' ),  false, 'e:border_color;e:border_type;' ),
									'bottom' => array( esc_html__( 'Bottom', 'aasana' ), true, 'e:border_color;e:border_type;' ),
								),
							),
							'border_type'	=> array(
								'title'		=> esc_html__( 'Type', 'aasana' ),
								'addrowclasses' => 'grid-col-4',
								'type'	=> 'select',
								'source'	=> array(
									'dotted' => array( esc_html__( 'Dotted', 'aasana' ),  false, '' ),
									'dashed' => array( esc_html__( 'Dashed', 'aasana' ),  false, '' ),
									'solid' => array( esc_html__( 'Solid', 'aasana' ), true, '' ),
								),
							),
							'border_color'	=> array(
								'title'	=> esc_html__( 'Color', 'aasana' ),
								'atts' => 'data-default-color="#e6e6e6"',
								'addrowclasses' => 'grid-col-4',
								'value' => '#e6e6e6',
								'type'	=> 'text',
							),
							'spacings' => array(
								'title' => esc_html__( 'Spacings (px)', 'aasana' ),
								'type' => 'margins',
								'addrowclasses' => 'grid-col-12 two-inputs',
								'value' => array(
									'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '120'),
									'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '120'),
								),
							),							
						),
					),



					)
				),
				'top_bar_cont' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Top bar', 'aasana' ),
					'layout' => array(
						'top_panel_switcher' => array(
							'title' => esc_html__( 'Top bar', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox alt',
							'type' => 'checkbox',
						),
						'top_bar_wide' => array(
							'title' => esc_html__( 'Apply Full-Width Top Bar', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox',
							'type' => 'checkbox',
						),							
						'show_top_bar_menu' => array(
							'title' => esc_html__( 'Add Menu to the Top Bar', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox',
							'atts' => 'data-options="e:top_bar_menu_position;"',
							'type' => 'checkbox',
						),
						'top_bar_menu_position' => array(
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'disable grid-col-3',
							'value' => array(
								'left' => array( esc_html__( 'Left', 'aasana' ), 	true, '', '/img/align-left.png' ),
								'center' =>array( esc_html__( 'Center', 'aasana' ), false, '', '/img/align-center.png' ),
								'right' =>array( esc_html__( 'Right', 'aasana' ), false, '', '/img/align-right.png' ),
							),
						),
						'show_language_bar' => array(
							'title' => esc_html__( 'Add Language Bar', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox',
							'atts' => 'checked data-options="e:language_bar_position;"',
							'type' => 'checkbox',
						),
						'language_bar_position' => array(
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'disable grid-col-3',
							'value' => array(
								'left' => array( esc_html__( 'Left', 'aasana' ), 	true, '', '/img/multilingual-left.png' ),
								'right' =>array( esc_html__( 'Right', 'aasana' ), false, '', '/img/multilingual-right.png' ),
							),
						),
						'toggle-share-icon' => array(
							'title' => esc_html__( 'Toogle social icons', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox',
							'atts' => 'checked',
							'type' => 'checkbox',
						),
						'top_panel_text' => array(
							'title' => esc_html__( 'Content', 'aasana' ),
							'addrowclasses' => 'grid-col-12',
							'tooltip' => array(
								'title' => esc_html__( 'Indent Adjusting', 'aasana' ),
								'content' => esc_html__( 'Adjust Indents by multiple spaces.<br /> Line breaks are working too.', 'aasana' ),
							),
							'type' => 'textarea',
							'atts' => 'rows="6"',
						),

						'top_bar_border'	=> array(
							'title'		=> esc_html__( 'Border', 'aasana' ),
							'addrowclasses' => 'grid-col-4',
							'type'	=> 'select',
							'source'	=> array(
								'none' => array( esc_html__( 'None', 'aasana' ),  false, 'd:top_bar_border_color;d:top_bar_border_type;' ),
								'top' => array( esc_html__( 'Top', 'aasana' ),  false, 'e:top_bar_border_color;e:top_bar_border_type;' ),
								'bottom' => array( esc_html__( 'Bottom', 'aasana' ), true, 'e:top_bar_border_color;e:top_bar_border_type;' ),
								'both' => array( esc_html__( 'Top & Bottom', 'aasana' ), false, 'e:top_bar_border_color;e:top_bar_border_type;' )
							),
						),
						'top_bar_border_type'	=> array(
							'title'		=> esc_html__( 'Type', 'aasana' ),
							'addrowclasses' => 'grid-col-4',
							'type'	=> 'select',
							'source'	=> array(
								'dotted' => array( esc_html__( 'Dotted', 'aasana' ),  false, '' ),
								'dashed' => array( esc_html__( 'Dashed', 'aasana' ),  false, '' ),
								'solid' => array( esc_html__( 'Solid', 'aasana' ), true, '' ),
							),
						),
						'top_bar_border_color'	=> array(
							'title'	=> esc_html__( 'Color', 'aasana' ),
							'atts' => 'data-default-color="#e6e6e6"',
							'addrowclasses' => 'grid-col-4 disable',
							'value' => '#e6e6e6',
							'type'	=> 'text',
						),
						'top_bar_bg_color' => array(
							'title' => esc_html__( 'Customize Background', 'aasana' ),
							'atts' => 'data-default-color="#fafafa"',
							'value' => '#fafafa',
							'addrowclasses' => 'new_row grid-col-4',
							'type' => 'text',
						),
						'top_bar_font_color' => array(
							'title' => esc_html__( 'Font color', 'aasana' ),
							'atts' => 'data-default-color="#b3b3b3"',
							'value' => '#b3b3b3',
							'addrowclasses' => 'grid-col-4',
							'type' => 'text',
						),
						'top_bar_bg_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity (%)', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'value' => '100',
							'addrowclasses' => 'grid-col-4',
						),
						'top_bar_spacings' => array(
							'title' => esc_html__( 'Add Spacings (px)', 'aasana' ),
							'type' => 'margins',
							'addrowclasses' => 'new_row grid-col-4 two-inputs',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '18'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '18'),
							),
						),					
					)
				),
				'side_panel_cont' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Sidebar', 'aasana' ),
					'layout' => array(
						'show_side_panel' => array(
							'title' => esc_html__( 'Side Panel', 'aasana' ),
							'addrowclasses' => 'alt checkbox',
							'type' => 'checkbox',
						),	

						'side_panel' => array(
							'type' => 'fields',
							'addrowclasses' => 'box inside-box groups grid-col-12',
							'layout' => array(
								// 1st row
								'theme'	=> array(
									'title'		=> esc_html__( 'Color Variation', 'aasana' ),
									'addrowclasses' => 'grid-col-12',
									'type'	=> 'select',
									'source'	=> array(
										'dark' => array( esc_html__( 'Dark', 'aasana' ),  true, '' ),
										'light' => array( esc_html__( 'Light', 'aasana' ),  false, '' ),
									),
								),							
								'place' => array(
									'title' => esc_html__( 'Menu Icon Location', 'aasana' ),
									'type' => 'radio',
									'subtype' => 'images',
									'addrowclasses' => 'grid-col-6',
									'value' => array(
										'topbar_left' =>array( esc_html__( 'TopBar (Left)', 'aasana' ), false, '', '/img/top-hamb-left.png' ),
										'topbar_right' => array( esc_html__( 'TopBar (Right)', 'aasana' ), 	false, '', '/img/top-hamb-right.png' ),
										'menu_left' =>array( esc_html__( 'Menu (Left)', 'aasana' ), true, '', '/img/hamb-left.png' ),
										'menu_right' =>array( esc_html__( 'Menu (Right)', 'aasana' ), false, '', '/img/hamb-right.png' ),
									),
								),
								'sidepanel_close_position' => array(
									'title' => esc_html__( 'Close Icon Position', 'aasana' ),
									'addrowclasses' => 'grid-col-6',
									'type' => 'radio',
									'value' => array(
										'left' => array( esc_html__( 'Left', 'aasana' ),  true, '' ),
										'right' =>array( esc_html__( 'Right', 'aasana' ), false,  '' ),
									),
								),
								'sidepanel-position' => array(
									'title' => esc_html__( 'Side Panel Position', 'aasana' ),
									'type' => 'radio',
									'subtype' => 'images',
									'addrowclasses' => 'grid-col-12',
									'value' => array(
										'left' => array( esc_html__( 'Left', 'aasana' ), 	false, '', '/img/align-left.png' ),
										'right' =>array( esc_html__( 'Right', 'aasana' ), true, '', '/img/align-right.png' ),
									),
								),		
								'sidebar' => array(
									'title' 		=> esc_html__('Select the Sidebar Area', 'aasana' ),
									'type' 			=> 'select',
									'addrowclasses' => 'new_row grid-col-6',
									'source' 		=> 'sidebars',
									'value' => 'side_panel',
								),
								'appear'	=> array(
									'title'		=> esc_html__( 'Animation Format', 'aasana' ),
									'type'	=> 'select',
									'addrowclasses' => 'grid-col-6',
									'source'	=> array(
										'fade' => array( esc_html__( 'Fade', 'aasana' ),  true ),
										'slide' => array( esc_html__( 'Slide', 'aasana' ), false ),
										'pull' => array( esc_html__( 'Pull', 'aasana' ), false ),
									),
								),													
								// 3rd row
								'logo_dark' => array(
									'title' => esc_html__( 'Dark Logo', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									'addrowclasses' => 'grid-col-6',
									// 'value' => array( 'id' => '', 'src' => get_template_directory_uri() . '\img\logo_the8_128x128.png') ,
									'layout' => array(
										'is_high_dpi' => array(
											'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
											'addrowclasses' => 'checkbox',
											'type' => 'checkbox',
										),
									),
								),
								'logo_light' => array(
									'title' => esc_html__( 'Light Logo', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									'addrowclasses' => 'grid-col-6',
									'layout' => array(
										'is_high_dpi' => array(
											'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
											'addrowclasses' => 'checkbox',
											'type' => 'checkbox',
										),
									),
								),
								// second row
								'logo_position' => array(
									'title' => esc_html__( 'Logo position', 'aasana' ),
									'addrowclasses' => 'grid-col-6',
									'type' => 'radio',
									'value' => array(
										'left' => array( esc_html__( 'Left', 'aasana' ),  true, '' ),
										'center' =>array( esc_html__( 'Center', 'aasana' ), false,  '' ),
										'right' =>array( esc_html__( 'Right', 'aasana' ), false,  '' ),
									),
								),
								'logo_dimensions' => array(
									'title' => esc_html__( 'Logo Dimensions', 'aasana' ),
									'type' => 'dimensions',
									'addrowclasses' => 'grid-col-6',
									'value' => array(
										'width' => array('placeholder' => esc_html__( 'Width', 'aasana' ), 'value' => ''),
										'height' => array('placeholder' => esc_html__( 'Height', 'aasana' ), 'value' => ''),
									),
								),
								// 4th row
								'bg_dark' => array(
									'title' => esc_html__( 'Background (Dark)', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									'addrowclasses' => 'grid-col-6',
								),
								'bg_light' => array(
									'title' => esc_html__( 'Background (Light)', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									'addrowclasses' => 'grid-col-6',
								),
								// 5th row
								'bg_size' => array(
									'title' => esc_html__( 'Background size', 'aasana' ),
									'addrowclasses' => 'grid-col-4',
									'type' => 'radio',
									'value' => array(
										'cover' => array( esc_html__( 'Cover', 'aasana' ),  true, '' ),
										'contain' =>array( esc_html__( 'Contain', 'aasana' ), false,  '' ),
									),
								),
								'bg_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'Background Opacity', 'aasana' ),
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'addrowclasses' => 'grid-col-4',
									'value' => '50'
								),
								'bg_position' => array(
									'title' => esc_html__( 'Background Position', 'aasana' ),
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
								'overlay_color' => array(
									'title' => esc_html__( 'Overlay color', 'aasana' ),
									'atts' => 'data-default-color="#000000"',
									'value' => '#000000',
									'addrowclasses' => 'grid-col-6',
									'type' => 'text',
								),
								'overlay_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'Opacity', 'aasana' ),
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'addrowclasses' => 'grid-col-6',
									'value' => '70'
								),
							),
						),
					)
				),
			)
		),	// end of sections
		'footer_options' => array(
			'type' => 'section',
			'title' => esc_html__('Footer', 'aasana' ),
			'icon' => array('fa', 'list-alt'),
			'layout' => array(
				'footer' => array(
					'title' => esc_html__('Footer Settings', 'aasana' ),
					'type' => 'fields',
					'addrowclasses' => 'grid-col-12 groups',	
					'layout' => array(
						'footer_fixed_style' => array(
							'title' => esc_html__( 'Apply Fixed Footer Style', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12',
							'type' => 'checkbox',
							),						
						'footer_layout' => array(
							'type' => 'select',
							'title' => esc_html__( 'Select a layout', 'aasana' ),
							'addrowclasses' => 'grid-col-6',
							'source' => array(
								'1' => array( esc_html__( '1/1 Column', 'aasana' ),  false ),
								'2' => array( esc_html__( '2/2 Column', 'aasana' ), false ),
								'3' => array( esc_html__( '3/3 Column', 'aasana' ), false ),
								'4' => array( esc_html__( '4/4 Column', 'aasana' ), false ),
								'two-three' => array( esc_html__( '2/3 + 1/3 Column', 'aasana' ), false ),
								'one-two' => array( esc_html__( '1/3 + 2/3 Column', 'aasana' ), false ),
								'one-three' => array( esc_html__( '1/4 + 3/4 Column', 'aasana' ), false ),
								'one-one-two' => array( esc_html__( '1/4 + 1/4 + 2/4 Column', 'aasana' ), false ),
								'two-one-one' => array( esc_html__( '2/4 + 1/4 + 1/4 Column', 'aasana' ), true ),
								'one-two-one' => array( esc_html__( '1/4 + 2/4 + 1/4 Column', 'aasana' ), false ),
									),
							),
						'footer_sidebar' => array(
							'title' 		=> esc_html__('Select Footer\'s Sidebar Area', 'aasana' ),
							'type' 			=> 'select',
							'addrowclasses' => 'grid-col-6',
							'tooltip' => array(
								'title' => esc_html__( 'Footer area', 'aasana' ),
								'content' => esc_html__( 'This options will set the default Footer widget area, unless you override it on each page', 'aasana' ),
								),
							'source' 		=> 'sidebars',
							),
						'footer_copyrights_text' => array(
							'title' => esc_html__( 'Footer Copyrights content', 'aasana' ),
							'type' => 'textarea',
							'addrowclasses' => 'grid-col-12',
							'value' => 'Copyrights',
							'atts' => 'rows="6"',
							),
						'show_copyrights_menu' => array(
							'title' => esc_html__( 'Show Website Menu in Copyrights Section', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox',
							'atts' => 'checked data-options="e:copyrights_menu_position;"',
							'type' => 'checkbox',
						),
						'copyrights_menu_position' => array(
							'title' => esc_html__( 'Menu Position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'disable grid-col-12',
							'value' => array(
								'left' => array( esc_html__( 'Left', 'aasana' ), 	false, '', '/img/align-left.png' ),
								'center' =>array( esc_html__( 'Center', 'aasana' ), true, '', '/img/align-center.png' ),
								'right' =>array( esc_html__( 'Right', 'aasana' ), false, '', '/img/align-right.png' ),
							),
						),
						'add_footer_bg_img' => array(
							'title' => esc_html__( 'Add Footer Image', 'aasana' ),
							'addrowclasses' => 'checkbox alt grid-col-6',
							'type' => 'checkbox',
							'atts' => 'data-options="e:footer_img_settings;"',
							),
						'footer_img_settings' => array(
							'type' => 'fields',
							'addrowclasses' => 'disable grid-col-12 groups',
							'layout' => array(
								'footer_bg_im' => array(
									'title' => esc_html__( 'Footer Bg Image', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									),
								'footer_img_pos_x'	=> array(
									'title'		=> esc_html__( 'Footer Img Position x', 'aasana' ),
									'type'	=> 'select',
									'source'	=> array(
										'left' => array( esc_html__( 'left', 'aasana' ),  true ),
										'right' => array( esc_html__( 'right', 'aasana' ), false ),
										'center' => array( esc_html__( 'center', 'aasana' ), false ),
										),
									),
								'footer_img_pos_y'	=> array(
									'title'		=> esc_html__( 'Footer Img Position y', 'aasana' ),
									'type'	=> 'select',
									'source'	=> array(
										'top' => array( esc_html__( 'top', 'aasana' ),  true ),
										'center' => array( esc_html__( 'center', 'aasana' ), false ),
										'bottom' => array( esc_html__( 'bottom', 'aasana' ), false ),
										),
									),
								'footer_img_repeat'	=> array(
									'title'		=> esc_html__( 'Footer Img Repeat', 'aasana' ),
									'type'	=> 'select',
									'source'	=> array(
										'no-repeat' => array( esc_html__('no repeat', 'aasana' ), true ),
										'repeat' => array( esc_html__('repeat', 'aasana' ), false ),
										'repeat-x' => array( esc_html__('repeat x', 'aasana' ), false ),
										'repeat-y' => array( esc_html__('repeat y', 'aasana' ), false ),
										),
									),
								'footer_img_size'	=> array(
									'title'		=> esc_html__( 'Footer Img Size', 'aasana' ),
									'type'	=> 'select',
									'source'	=> array(
										'auto' => array( esc_html__('auto', 'aasana' ),  true ),
										'cover' => array( esc_html__('cover', 'aasana' ), false ),
										'contain' => array( esc_html__('container', 'aasana' ), false ),
										),
									),
								'footer_img_attachment'	=> array(
									'title'		=> esc_html__( 'Footer Img Attachment', 'aasana' ),
									'type'	=> 'select',
									'source'	=> array(
										'fixed' => array( esc_html__('fixed', 'aasana' ), false ),
										'scroll' => array( esc_html__('scroll', 'aasana' ),  true ),
										'local' => array( esc_html__('local', 'aasana' ), false )
										),
									),
								),
							),

						'footer_pattern' => array(
							'title' => esc_html__( 'Footer Pattern', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-12',
							),
						'footer_bg_color'	=> array(
							'title'	=> esc_html__( 'Footer Background Color', 'aasana' ),
							'atts'	=> 'data-default-color="#3c3b4b"',
							'value' => "#3c3b4b",
							'addrowclasses' => 'grid-col-6',
							'type'	=> 'text'
							),
						'footer_font_color' => array(
							'title' => esc_html__( 'Footer font color', 'aasana' ),
							'atts' => 'data-default-color="#b0b0b0"',
							'value' => '#b0b0b0',
							'addrowclasses' => 'grid-col-6',
							'type' => 'text',
							),
						'footer_copyrights_bg_color'	=> array(
							'title'	=> esc_html__( 'Copyrights Background Color', 'aasana' ),
							'atts' => 'data-default-color="#654e77"',
							'value' => '#654e77',
							'addrowclasses' => 'grid-col-6',
							'type'	=> 'text'
							),
						'footer_copyrights_font_color' => array(
							'title' => esc_html__( 'Copyrights Font color', 'aasana' ),
							'atts' => 'data-default-color="#c5c5c9"',
							'value' => '#c5c5c9',
							'addrowclasses' => 'grid-col-6',
							'type' => 'text',
							),
						'footer_color_overlay_type'	=> array(
							'title'		=> esc_html__( 'Overlay Color', 'aasana' ),
							'addrowclasses' => 'grid-col-4',
							'type'	=> 'select',
							'source'	=> array(
								'none' => array( esc_html__( 'None', 'aasana' ),  true, 'd:footer_color_overlay_opacity;d:footer_overlay_color;d:footer_gradient_settings;' ),
								'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:footer_color_overlay_opacity;e:footer_overlay_color;d:footer_gradient_settings;' ),
								'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'e:footer_color_overlay_opacity;d:footer_overlay_color;e:footer_gradient_settings;' )
								),
							),
						'footer_overlay_color'	=> array(
							'title'	=> esc_html__( 'Color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'addrowclasses' => 'disable grid-col-4',
							'value' => AASANA_COLOR,
							'type'	=> 'text',
							),
						'footer_color_overlay_opacity' => array(
							'type' => 'number',
							'title' => esc_html__( 'Opacity', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'value' => '40',
							'addrowclasses' => 'disable grid-col-4',
							),
						'footer_gradient_settings' => array(
							'title' => esc_html__( 'Gradient Settings', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'disable grid-col-12 groups',
							'layout' => array(
								'first_color' => array(
									'type' => 'text',
									'addrowclasses' => 'grid-col-6',
									'title' => esc_html__( 'From', 'aasana' ),
									'atts' => 'data-default-color=""',
									),
								'second_color' => array(
									'type' => 'text',
									'addrowclasses' => 'grid-col-6',
									'title' => esc_html__( 'To', 'aasana' ),
									'atts' => 'data-default-color=""',
									),
								'first_color_opacity' => array(
									'type' => 'number',
									'addrowclasses' => 'grid-col-6',
									'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
									'value' => '100',
									),
								'second_color_opacity' => array(
									'type' => 'number',
									'addrowclasses' => 'grid-col-6',
									'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
									'value' => '100',
									),
								'type' => array(
									'title' => esc_html__( 'Gradient type', 'aasana' ),
									'type' => 'radio',
									'addrowclasses' => 'grid-col-6',
									'value' => array(
										'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
										'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
										),
									),
								'linear_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-6 disable',
									'layout' => array(
										'angle' => array(
											'type' => 'number',
											'title' => esc_html__( 'Angle', 'aasana' ),
											'value' => '45',
											),
										)
									),
								'radial_settings' => array(
									'type' => 'fields',
									'addrowclasses' => 'grid-col-6 disable',
									'layout' => array(
										'shape_settings' => array(
											'title' => esc_html__( 'Shape', 'aasana' ),
											'type' => 'radio',
											'value' => array(
												'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
												'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
												),
											),
										'shape' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'addrowclasses' => 'grid-col-6',
											'type' => 'radio',
											'value' => array(
												'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
												'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
												),
											),
										'size_keyword' => array(
											'type' => 'select',
											'title' => esc_html__( 'Size keyword', 'aasana' ),
											'addrowclasses' => 'disable',
											'source' => array(
												'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
												'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
												'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
												'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
												),
											),
										'size' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-6 disable',
											'title' => esc_html__( 'Size', 'aasana' ),
											'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
											),
										)
									)
								)
							),
						'footer_instagram_feed' => array(
							'title' => esc_html__( 'Instagram Feed', 'aasana' ),
							'addrowclasses' => 'checkbox grid-col-12 alt',
							'type' => 'checkbox',
							'tooltip' => array(
								'title' => esc_html__( 'Plugin requirement', 'aasana' ),
								'content' => esc_html__( 'Instagram-feed (https://wordpress.org/plugins/instagram-feed/)', 'aasana' ),
								),
							'atts' => 'data-options="e:instagram_feed_shortcode;e:footer_instagram_feed_full_width;"',
							),
						'instagram_feed_shortcode' => array(
							'title' => esc_html__( 'Instagram feed shortcode', 'aasana' ),
							'addrowclasses' => 'disable grid-col-12',
							'tooltip' => array(
								'title' => esc_html__( 'Customize', 'aasana' ),
								'content' => esc_html__( 'Customize page (/wp-admin/admin.php?page=sb-instagram-feed&tab=customize)', 'aasana' ),
								),
							'type' => 'textarea',
							'atts' => 'rows="3"',
							'default' => '',
							'value' => '[instagram-feed cols=8 num=8 imagepadding=0 imagepaddingunit=px showheader=false showbutton=true showfollow=true]'
							),
						'footer_instagram_feed_full_width' => array(
							'title' => esc_html__( 'Full width', 'aasana' ),
							'addrowclasses' => 'disable checkbox grid-col-12 alt',
							'type' => 'checkbox',
							),
						'footer_spacings' => array(
							'title' => esc_html__( 'Add Spacings (px)', 'aasana' ),
							'type' => 'margins',
							'addrowclasses' => 'grid-col-4 two-inputs',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '40'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '50'),
							),
						),					

						),
				),	
			)
		),	// end of sections

		'styling_options' => array(
			'type' => 'section',
			'title' => esc_html__('Styling options', 'aasana' ),
			'icon' => array('fa', 'paint-brush'),
			'layout' => array(
				'theme_colors' => array(
					'type' => 'tab',
					'init' => 'open',
					'icon' => array('fa', 'calendar-plus-o'),
					'title' => esc_html__( 'Theme colors', 'aasana' ),
					'layout' => array(
						'theme-main-one-color' => array(
							'title' => esc_html__( 'Main color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'value' => AASANA_COLOR,
							'addrowclasses' => 'grid-col-4',
							'type' => 'text',
						),						
						'theme-main-secondary-color' => array(
							'title' => esc_html__( 'Theme Secondary Color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_SECONDARY_COLOR . '"',
							'value' => AASANA_SECONDARY_COLOR,
							'addrowclasses' => 'grid-col-4',
							'type' => 'text',
						),						

						'theme-second-color' => array(
							'title' => esc_html__( 'Helper color', 'aasana' ),
							'atts' => 'data-default-color="#784f6a"',
							'value' => '#784f6a',
							'addrowclasses' => 'grid-col-4',
							'type' => 'text',
						),
					)
				),
				'layout_options_layout' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Layout', 'aasana' ),
					'layout' => array(
						'boxed_layout' => array(
							'title' => esc_html__( 'Boxed Layout', 'aasana' ),
							'addrowclasses' => 'checkbox alt',
							'type' => 'checkbox',
							'atts' => 'data-options="e:url_background;"',
						),
						'url_background' => array(
							'title' => esc_html__( 'Background Settings', 'aasana' ),
							'type' => 'info',
							'addrowclasses' => 'disable',
							'icon' => array('fa', 'calendar-plus-o'),
							'value' => '<a href="'.get_admin_url(null, 'customize.php?autofocus[control]=background_image').'" target="_blank">'.esc_html__('Click this link to customize your background settings','aasana').'</a>',
						),
					)
				)
			),
		),	// end of sections

		'layout_options' => array(
			'type' => 'section',
			'title' => esc_html__('Page layouts', 'aasana' ),
			'icon' => array('fa', 'columns'),
			'layout'	=> array(
				'layout_options_homepage'	=> array(
					'type' => 'tab',
					'init'	=> 'open',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Home', 'aasana' ),
					'layout' => array(
						'home-slider-type' => array(
							'title' => esc_html__('Slider', 'aasana' ),
							'type' => 'radio',
							'value' => array(
								'none' => 	array( esc_html__('None', 'aasana' ), true, 'd:home-header-slider-options;d:slidersection-start;d:static_img_section' ),
								'img-slider'=>	array( esc_html__('Image Slider', 'aasana' ), false, 'e:home-header-slider-options;d:slidersection-start;d:static_img_section' ),
								'video-slider' => 	array( esc_html__('Video Slider', 'aasana' ), false, 'd:home-header-slider-options;e:slidersection-start;d:static_img_section' ),
								'stat-img-slider' => 	array( esc_html__('Static image', 'aasana' ), false, 'd:home-header-slider-options;d:slidersection-start;e:static_img_section' ),
							),
						),
						'home-header-slider-options' => array(
							'title' => esc_html__( 'Slider shortcode', 'aasana' ),
							'addrowclasses' => 'disable',
							'type' => 'text',
							'value' => '[rev_slider homepage]',
						),
						'slidersection-start' => array(
							'title' => esc_html__( 'Video Slider Setting', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'disable groups',
							'layout' => array(
								'slider_switch' => array(
									'title' => esc_html__( 'Slider', 'aasana' ),
									'addrowclasses' => 'grid-col-12 checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:slider_shortcode;"',
								),
								'slider_shortcode' => array(
									'title' => esc_html__( 'Slider shortcode', 'aasana' ),
									'addrowclasses' => 'grid-col-12 disable box',
									'type' => 'text',
								),
								'set_video_header_height' => array(
									'title' => esc_html__( 'Set Video height', 'aasana' ),
									'type' => 'checkbox',
									'addrowclasses' => 'grid-col-12 checkbox',
									'atts' => 'data-options="e:video_header_height"',
								),
								'video_header_height' => array(
									'title' => esc_html__( 'Video height', 'aasana' ),
									'addrowclasses' => 'grid-col-12 disable box',
									'type' => 'number',
									'value' => '600',
								),
								'video_type' => array(
									'title' => esc_html__('Video type', 'aasana' ),
									'addrowclasses' => 'grid-col-12',
									'type' => 'radio',
									'value' => array(
										'self_hosted' => 	array( esc_html__('Self-hosted', 'aasana' ), true, 'e:sh_source;d:youtube_source;d:vimeo_source' ),
										'youtube'=>	array( esc_html__('Youtube clip', 'aasana' ), false, 'd:sh_source;e:youtube_source;d:vimeo_source' ),
										'vimeo' => 	array( esc_html__('Vimeo clip', 'aasana' ), false, 'd:sh_source;d:youtube_source;e:vimeo_source' ),
									),
								),
								'sh_source' => array(
									'title' => esc_html__( 'Add video', 'aasana' ),
									'addrowclasses' => 'grid-col-12 box',
									'url-atts' => 'readonly',
									'type' => 'media',
								),
								'youtube_source' => array(
									'title' => esc_html__( 'Youtube video code', 'aasana' ),
									'addrowclasses' => 'grid-col-12 disable box',
									'type' => 'text',
								),
								'vimeo_source' => array(
									'title' => esc_html__( 'Vimeo embed url', 'aasana' ),
									'addrowclasses' => 'grid-col-12 disable box',
									'type' => 'text',
								),
								'color_overlay_type' => array(
									'title' => esc_html__( 'Overlay', 'aasana' ),
									'addrowclasses' => 'grid-col-4',
									'type' => 'select',
									'source' => array(
										'none' => array( esc_html__( 'None', 'aasana' ), 	true, 'd:overlay_color;d:slider_gradient_settings;d:color_overlay_opacity;'),
										'color' => array( esc_html__( 'Color', 'aasana' ), 	false, 'e:overlay_color;d:slider_gradient_settings;e:color_overlay_opacity;'),
										'gradient' =>array( esc_html__( 'Gradient', 'aasana' ), false, 'd:overlay_color;e:slider_gradient_settings;e:color_overlay_opacity;'),
									),
								),
								'overlay_color' => array(
									'title' => esc_html__( 'Color', 'aasana' ),
									'addrowclasses' => 'grid-col-12',
									'atts' => 'data-default-color=""',
									'addrowclasses' => 'box',
									'type' => 'text',
								),
								'color_overlay_opacity' => array(
									'type' => 'number',
									'addrowclasses' => 'grid-col-4 box',
									'title' => esc_html__( 'Opacity', 'aasana' ),
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'value' => '40'
								),
								'slider_gradient_settings' => array(
									'title' => esc_html__( 'Gradient settings', 'aasana' ),
									'addrowclasses' => 'grid-col-12',
									'type' => 'fields',
									'addrowclasses' => 'disable box groups',
									'layout' => array(
										'first_color' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-6',
											'title' => esc_html__( 'From', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'second_color' => array(
											'type' => 'text',
											'addrowclasses' => 'grid-col-6',
											'title' => esc_html__( 'To', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'first_color_opacity' => array(
											'type' => 'number',
											'addrowclasses' => 'grid-col-6',
											'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'second_color_opacity' => array(
											'type' => 'number',
											'addrowclasses' => 'grid-col-6',
											'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'type' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'addrowclasses' => 'grid-col-12',
											'type' => 'radio',
											'value' => array(
												'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
												'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
											),
										),
										'linear_settings' => array(
											'title' => esc_html__( 'Linear settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'angle' => array(
													'type' => 'number',
													'title' => esc_html__( 'Angle', 'aasana' ),
													'value' => '45',
												),
											)
										),
										'radial_settings' => array(
											'title' => esc_html__( 'Radial settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'shape_settings' => array(
													'title' => esc_html__( 'Shape', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
														'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
													),
												),
												'shape' => array(
													'title' => esc_html__( 'Gradient type', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
														'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
													),
												),
												'size_keyword' => array(
													'type' => 'select',
													'title' => esc_html__( 'Size keyword', 'aasana' ),
													'addrowclasses' => 'disable',
													'source' => array(
														'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
														'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
														'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
														'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
													),
												),
												'size' => array(
													'type' => 'text',
													'addrowclasses' => 'disable',
													'title' => esc_html__( 'Size', 'aasana' ),
													'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
												),
											)
										)

									),
								),
								'use_pattern' => array(
									'title' => esc_html__( 'Use pattern image', 'aasana' ),
									'type' => 'checkbox',
									'addrowclasses' => 'grid-col-12 checkbox',
									'atts' => 'data-options="e:pattern_image"',
								),
								'pattern_image' => array(
									'title' => esc_html__( 'Pattern image', 'aasana' ),
									'addrowclasses' => 'grid-col-12 disable box',
									'url-atts' => 'readonly',
									'type' => 'media',
								),
							),
						),// end of video-section
						'static_img_section' => array(
							'title' => esc_html__( 'Static image Slider Setting', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'groups',
							'layout' => array(
								'home_header_image_options' => array(
									'title' => esc_html__( 'Static image', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									'layout' => array(
										'is_high_dpi' => array(
											'title' => esc_html__( 'High-Resolution image', 'aasana' ),
											'type' => 'checkbox',
											'addrowclasses' => 'checkbox',
										),
									),
								),
								'set_static_image_height' => array(
									'title' => esc_html__( 'Set Image height', 'aasana' ),
									'addrowclasses' => 'grid-col-12 checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:static_image_height;"',
								),
								'static_image_height' => array(
									'title' => esc_html__( 'Static Image Height', 'aasana' ),
									'addrowclasses' => 'grid-col-12 disable box',
									'type' => 'number',
									'default' => '600',
								),
								'static_customize_colors' => array(
									'title' => esc_html__( 'Customize colors', 'aasana' ),
									'addrowclasses' => 'grid-col-12 checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:img_header_color_overlay_type;e:img_header_overlay_color;e:img_header_color_overlay_opacity;"',
								),
								'img_header_color_overlay_type'	=> array(
									'title'		=> esc_html__( 'Color overlay type', 'aasana' ),
									'type'	=> 'select',
									'addrowclasses' => 'grid-col-12 box disable',
									'source'	=> array(
										'color' => array( esc_html__( 'Color', 'aasana' ),  true, 'e:img_header_overlay_color;d:img_header_gradient_settings;' ),
										'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'd:img_header_overlay_color;e:img_header_gradient_settings;' )
									),
								),
								'img_header_overlay_color'	=> array(
									'title'	=> esc_html__( 'Overlay color', 'aasana' ),
									'atts' => 'data-default-color="' . AASANA_COLOR . '"',
									'value' => AASANA_COLOR,
									'addrowclasses' => 'box disable',
									'type'	=> 'text',
								),
								'img_header_gradient_settings' => array(
									'title' => esc_html__( 'Gradient Settings', 'aasana' ),
									'type' => 'fields',
									'addrowclasses' => 'disable box groups',
									'layout' => array(
										'first_color' => array(
											'type' => 'text',
											'title' => esc_html__( 'From', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'second_color' => array(
											'type' => 'text',
											'title' => esc_html__( 'To', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'first_color_opacity' => array(
											'type' => 'number',
											'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'second_color_opacity' => array(
											'type' => 'number',
											'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'type' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'type' => 'radio',
											'value' => array(
												'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:img_header_gradient_linear_settings;d:img_header_gradient_radial_settings' ),
												'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:img_header_gradient_linear_settings;e:img_header_gradient_radial_settings' ),
											),
										),
										'linear_settings' => array(
											'title' => esc_html__( 'Linear settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'angle' => array(
													'type' => 'number',
													'title' => esc_html__( 'Angle', 'aasana' ),
													'value' => '45',
												),
											)
										),
										'radial_settings' => array(
											'title' => esc_html__( 'Radial settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'shape_settings' => array(
													'title' => esc_html__( 'Shape', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:img_header_gradient_shape;d:img_header_gradient_size;d:img_header_gradient_size_keyword;' ),
														'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:img_header_gradient_shape;e:img_header_gradient_size;e:img_header_gradient_size_keyword;' ),
													),
												),
												'shape' => array(
													'title' => esc_html__( 'Gradient type', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
														'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
													),
												),
												'img_header_gradient_size_keyword' => array(
													'type' => 'select',
													'title' => esc_html__( 'Size keyword', 'aasana' ),
													'addrowclasses' => 'disable',
													'source' => array(
														'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
														'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
														'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
														'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
													),
												),
												'img_header_gradient_size' => array(
													'type' => 'text',
													'addrowclasses' => 'disable',
													'title' => esc_html__( 'Size', 'aasana' ),
													'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
												),
											)
										)
									)
								),
								'img_header_color_overlay_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'Opacity', 'aasana' ),
									'addrowclasses' => 'box disable',
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'value' => '40'
								),
								'img_header_use_pattern' => array(
									'title' => esc_html__( 'Add pattern', 'aasana' ),
									'addrowclasses' => 'grid-col-12 checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:img_header_pattern_image;"',
								),
								'img_header_pattern_image' => array(
									'title' => esc_html__( 'Pattern image', 'aasana' ),
									'type' => 'media',
									'addrowclasses' => 'grid-col-12 disable box',
									'url-atts' => 'readonly',
								),
								'img_header_parallaxify' => array(
									'title' => esc_html__( 'Parallaxify image', 'aasana' ),
									'addrowclasses' => 'grid-col-12 checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:img_header_parallax_options;"',
								),
								'img_header_parallax_options' => array(
									'title' => esc_html__( 'Parallax options', 'aasana' ),
									'type' => 'fields',
									'addrowclasses' => 'disable box groups',
									'layout' => array(
										'img_header_scalar-x' => array(
											'type' => 'number',
											'title' => esc_html__( 'x-axis parallax intensity', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '2'
										),
										'img_header_scalar-y' => array(
											'type' => 'number',
											'title' => esc_html__( 'y-axis parallax intensity', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '2'
										),
										'img_header_limit-x' => array(
											'type' => 'number',
											'title' => esc_html__( 'Maximum x-axis shift', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '15'
										),
										'img_header_limit-y' => array(
											'type' => 'number',
											'title' => esc_html__( 'Maximum y-axis shift', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '15'
										),
									),
								),
							),
						),// end of static img slider-section
						'home_sidebars' => array(
							'title' => esc_html__( 'Home Page Sidebar Layout', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'box inside-box groups',
							'layout' => array(
								'layout' => array(
									'title' => esc_html__('Sidebar Position', 'aasana' ),
									'type' => 'radio',
									'addrowclasses' => 'grid-col-12',
									'subtype' => 'images',
									'value' => array(
										'left' => 	array( esc_html__('Left', 'aasana' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
										'right' => 	array( esc_html__('Right', 'aasana' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
										'both' => 	array( esc_html__('Double', 'aasana' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
										'none' => 	array( esc_html__('None', 'aasana' ), false, 'd:sb1;d:sb2', '/img/none.png' )
									),
								),
								'sb1' => array(
									'title' => esc_html__('Select a sidebar', 'aasana' ),
									'type' => 'select',
									'addrowclasses' => 'grid-col-12 disable box clear',
									'source' => 'sidebars',
								),
								'sb2' => array(
									'title' => esc_html__('Select right sidebar', 'aasana' ),
									'type' => 'select',
									'addrowclasses' => 'grid-col-12 disable box',
									'source' => 'sidebars',
								),
							),
						),
					)
				),
				'layout_options_page' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Page', 'aasana' ),
					'layout' => array(
						'page_sidebars' => array(
							'title' => esc_html__( 'Page Sidebar Layout', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'box inside-box groups',
							'layout' => array(
								'layout' => array(
									'type' => 'radio',
									'subtype' => 'images',
									'value' => array(
										'left' => 	array( esc_html__('Left', 'aasana' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
										'right' => 	array( esc_html__('Right', 'aasana' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
										'both' => 	array( esc_html__('Double', 'aasana' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
										'none' => 	array( esc_html__('None', 'aasana' ), true, 'd:sb1;d:sb2', '/img/none.png' )
									),
								),
								'sb1' => array(
									'title' => esc_html__('Select a sidebar', 'aasana' ),
									'type' => 'select',
									'addrowclasses' => 'disable box',
									'source' => 'sidebars',
								),
								'sb2' => array(
									'title' => esc_html__('Select right sidebar', 'aasana' ),
									'type' => 'select',
									'addrowclasses' => 'disable box',
									'source' => 'sidebars',
								),
							),
						),
					)
				),
				'layout_options_blog' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Blog', 'aasana' ),
					'layout' => array(
						'def_blogtype' => array(
							'title'		=> esc_html__( 'Blog Layout', 'aasana' ),
							'desc'		=> esc_html__( 'Default Blog Layout', 'aasana' ),
							'type'		=> 'radio',
							'subtype' => 'images',
							'value' => array(
								'large' => array( esc_html__('Large', 'aasana' ), true, '', '/img/large.png'),
								'medium' => array( esc_html__('Medium', 'aasana' ), false, '', '/img/medium.png'),
								'small' => array( esc_html__('Small', 'aasana' ), false, '', '/img/small.png'),
								'2' => array( esc_html__('2 Cols', 'aasana' ), false, '', '/img/pinterest_2_columns.png'),
								'3' => array( esc_html__('3 Cols', 'aasana' ), false, '', '/img/pinterest_3_columns.png'),
								'4' => array( esc_html__('4 Cols', 'aasana' ), false, '', '/img/pinterest_4_columns.png'),
							),
						),
						'blog_title' => array(
							'title' => esc_html__( 'Blog slug', 'aasana' ),
							'addrowclasses' => 'requirement',
							'type' 	=> 'text',
							'value'	=> 'Blog'
						),
						'crop_related_items' => array(
							'title' => esc_html__( 'Crop related items (Single Post)', 'aasana' ),
							'addrowclasses' => 'checkbox',
							'type' => 'checkbox',
						),
						'post_sidebars' => array(
							'title' => esc_html__( 'Posts Sidebar Layout', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'box inside-box groups',
							'layout' => array(
								'layout' => array(
									'type' => 'radio',
									'subtype' => 'images',
									'value' => array(
										'left' => 	array( esc_html__('Left', 'aasana' ), false, 'e:sb1;d:sb2',	'/img/left.png' ),
										'right' => 	array( esc_html__('Right', 'aasana' ), false, 'e:sb1;d:sb2', '/img/right.png' ),
										'both' => 	array( esc_html__('Double', 'aasana' ), false, 'e:sb1;e:sb2', '/img/both.png' ),
										'none' => 	array( esc_html__('None', 'aasana' ), true, 'd:sb1;d:sb2', '/img/none.png' )
									),
								),
								'sb1' => array(
									'title' => esc_html__('Select a sidebar', 'aasana' ),
									'type' => 'select',
									'addrowclasses' => 'disable box',
									'source' => 'sidebars',
								),
								'sb2' => array(
									'title' => esc_html__('Select right sidebar', 'aasana' ),
									'type' => 'select',
									'addrowclasses' => 'disable box',
									'source' => 'sidebars',
								),
							),
						),						
					)
				),
				'layout_options_portfolio' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Portfolio', 'aasana' ),
					'layout' => array(
						'def_layout_portfolio' => array(
							'title'		=> esc_html__( 'Portfolio Layout', 'aasana' ),
							'type'		=> 'radio',
							'subtype' => 'images',
							'tooltip' => array(
								'title' => esc_html__( 'Portfolio Layout', 'aasana' ),
								'content' => esc_html__( 'This option is applied to portfolio archive pages only', 'aasana' ),
							),
							'value' => array(
								'1' => array( esc_html__('Large', 'aasana' ), false, '', '/img/large.png'),
								'2' => array( esc_html__('2 Cols', 'aasana' ), false, '', '/img/pinterest_2_columns.png'),
								'3' => array( esc_html__('3 Cols', 'aasana' ), false, '', '/img/pinterest_3_columns.png'),
								'4' => array( esc_html__('4 Cols', 'aasana' ), true, '', '/img/pinterest_4_columns.png'),
							),
						),
						'portfolio_mode' => array(
							'title' => esc_html__( 'Display as', 'aasana' ),
							'type' => 'select',
							'source' => array(
								'grid' => array('Grid', true), // Title, isselected, data-options
								'grid_with_filter' => array('Grid with filter', false),
								'carousel' => array('Carousel', false)
							),
						),
						'def_cws_portfolio_data_to_show'	=> array(
							'title'		=> esc_html__( 'Show Meta Data', 'aasana' ),
							'type'		=> 'select',
							'atts'		=> 'multiple',
							'source'		=> array(
									'title'		=> array( esc_html__( 'Title', 'aasana' ), true ),
									'excerpt'	=> array( esc_html__( 'Excerpt', 'aasana' ), true ),
									'cats'		=> array( esc_html__( 'Categories', 'aasana' ), false )
							)
						),
						'portfolio_mode' => array(
							'title' => esc_html__( 'Display as', 'aasana' ),
							'type' => 'select',
							'source' => array(
								'grid' => array('Grid', true), // Title, isselected, data-options
								'filter_with_ajax' => array('Grid with filter(Ajax)', false),
								'filter' => array('Grid with filter', false),
								'carousel' => array('Carousel', false)
							),
						),


						'portfolio_pagination_style' => array(
							'title' => esc_html__( 'Pagination style', 'aasana' ),
							'type' => 'radio',
							'value' => array(
								'paged' => array( 'Paged', true ),
								'load_more' => array( 'Load More', false )
							),
						),


						'portfolio_slug' => array(
							'title' => esc_html__( 'Portfolio slug', 'aasana' ),
							'type' => 'text',
							'value' => 'portfolio',
						),
					)
				),
				'layout_options_staff' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Staff', 'aasana' ),
					'layout' => array(
						'def_cws_staff_layout' => array(
							'title'		=> esc_html__( 'Staff Layout', 'aasana' ),
							'type'		=> 'radio',
							'subtype' => 'images',
							'tooltip' => array(
								'title' => esc_html__( 'Staff Layout', 'aasana' ),
								'content' => esc_html__( 'This option is applied to Staff archive pages only', 'aasana' ),
							),
							'value' => array(
								'1' => array( esc_html__('Large', 'aasana' ), false, '', '/img/large.png'),
								'2' => array( esc_html__('2 Cols', 'aasana' ), false, '', '/img/pinterest_2_columns.png'),
								'3' => array( esc_html__('3 Cols', 'aasana' ), false, '', '/img/pinterest_3_columns.png'),
								'4' => array( esc_html__('4 Cols', 'aasana' ), true, '', '/img/pinterest_4_columns.png'),
							),
						),
						'def_cws_staff_data_to_hide'	=> array(
							'title'		=> esc_html__( 'Hide Meta Data', 'aasana' ),
							'type'		=> 'select',
							'atts'		=> 'multiple',
							'source'		=> array(
								'deps'			=> array( esc_html__( 'Departments', 'aasana' ), true ),
								'poss'			=> array( esc_html__( 'Positions', 'aasana' ), false ),
								'excerpt'		=> array( esc_html__( 'Excerpt', 'aasana' ), true ),
								'experience'	=> array( esc_html__( 'Experience', 'aasana' ), true ),
								'email'			=> array( esc_html__( 'Email', 'aasana' ), true ),
								'biography'		=> array( esc_html__( 'Biography', 'aasana' ), true ),
								'link_button'	 => array( esc_html__( 'Link Button', 'aasana' ), true ),
								'socials'		=> array( esc_html__( 'Social Links', 'aasana' ), false )
							)
						),
						'staff_slug' => array(
							'title' => esc_html__( 'Staff slug', 'aasana' ),
							'type' => 'text',
							'value' => 'staff',
						),

					)
				),				
				'layout_options_classes' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Classes', 'aasana' ),
					'layout' => array(
						'def_cws_classes_layout' => array(
							'title'		=> esc_html__( 'Classes Layout', 'aasana' ),
							'type'		=> 'radio',
							'subtype' => 'images',
							'value' => array(
								'1' => array( esc_html__('Large', 'aasana' ), false, '', '/img/large.png'),
								'2' => array( esc_html__('2 Cols', 'aasana' ), false, '', '/img/pinterest_2_columns.png'),
								'3' => array( esc_html__('3 Cols', 'aasana' ), false, '', '/img/pinterest_3_columns.png'),
								'4' => array( esc_html__('4 Cols', 'aasana' ), true, '', '/img/pinterest_4_columns.png'),
							),
						),
						'def_cws_classes_data_to_hide'	=> array(
							'title'		=> esc_html__( 'Hide Meta Data', 'aasana' ),
							'type'		=> 'select',
							'atts'		=> 'multiple',
							'source'		=> array(
								'title'			=> array( esc_html__( 'Title', 'aasana' ), false ),
								'excerpt'			=> array( esc_html__( 'Excerpt', 'aasana' ), false ),
								'cats'		=> array( esc_html__( 'Categories', 'aasana' ), false ),
								'teach'	=> array( esc_html__( 'Teacher', 'aasana' ), false ),
								'working_days'			=> array( esc_html__( 'Working Days', 'aasana' ), false ),
								'time_events'		=> array( esc_html__( 'Time Events', 'aasana' ), false ),
								'venue_events'	 => array( esc_html__( 'Venue Events', 'aasana' ), false ),
								'read_more'		=> array( esc_html__( 'Read More Button', 'aasana' ), false )
							)
						),					
						'def_cws_classes_data_to_hide_related'	=> array(
							'title'		=> esc_html__( 'Related Items Hide Meta Data', 'aasana' ),
							'type'		=> 'select',
							'atts'		=> 'multiple',
							'source'		=> array(
								'title'			=> array( esc_html__( 'Title', 'aasana' ), false ),
								'excerpt'			=> array( esc_html__( 'Excerpt', 'aasana' ), false ),
								'cats'		=> array( esc_html__( 'Categories', 'aasana' ), true ),
								'teach'	=> array( esc_html__( 'Teacher', 'aasana' ), true ),
								'working_days'			=> array( esc_html__( 'Working Days', 'aasana' ), true ),
								'time_events'		=> array( esc_html__( 'Time Events', 'aasana' ), true ),
								'venue_events'	 => array( esc_html__( 'Venue Events', 'aasana' ), true ),
								'read_more'		=> array( esc_html__( 'Read More Button', 'aasana' ), true )
							)
						),
						'classes_slug' => array(
							'title' => esc_html__( 'Classes slug', 'aasana' ),
							'type' => 'text',
							'value' => 'classes',
						),

					)
				),
				'layout_options_testimonials' => array(
					'type' => 'tab',
					'icon' => array( 'fa', 'fa-book' ),
					'title' => esc_html__( 'Testimonials', 'aasana' ),
					'layout' => array(
						'def_layout_testimonials' => array(
							'title'		=> esc_html__( 'Testimonials Layout', 'aasana' ),
							'type'		=> 'radio',
							'subtype' => 'images',
							'tooltip' => array(
								'title' => esc_html__( 'Testimonials Layout', 'aasana' ),
								'content' => esc_html__( 'This option is applied to testimonials archive pages only', 'aasana' ),
							),
							'value' => array(
								'1' => array( esc_html__('Large', 'aasana' ), true, '', '/img/large.png'),
								'2' => array( esc_html__('2 Cols', 'aasana' ), false, '', '/img/pinterest_2_columns.png'),
							),
						),
						'testimonials_slug' => array(
							'title' => esc_html__( 'Testimonials slug', 'aasana' ),
							'type' => 'text',
							'value' => 'testimonials',
						),
					)
				),
				'layout_options_sidebar_generator' => array(
					'type' => 'tab',
					'customizer' 	=> array( 'show' => false ),
					'icon' => array('fa', 'calendar-plus-o'),
					'title' => esc_html__( 'Sidebars', 'aasana' ),
					'layout' => array(
						'sidebars' => array(
							'type' => 'group',
							'addrowclasses' => 'group single_field requirement',
							'title' => esc_html__('Sidebar generator', 'aasana' ),
							'button_title' => esc_html__('Add new sidebar', 'aasana' ),
							'value' => array(
									array('title' => 'Footer'),
									array('title' => 'Blog Right'),
									array('title' => 'Blog Left'),
									array('title' => 'Page Right'),
									array('title' => 'Page Left'),
									array('title' => 'Side Panel'),
							),
							'layout' => array(
								'title' => array(
									'type' => 'text',
									'value' => 'New Sidebar',
									'atts' => 'data-role="title"',
									'verification' => array (
										'length' => array( array('!0'), esc_html__('Title should not be empty', 'aasana' )),
									),
									'title' => esc_html__('Sidebar', 'aasana' ),
								)
							)
						),
						'sticky_sidebars' => array(
							'title' => esc_html__( 'Sticky sidebars', 'aasana' ),
							'addrowclasses' => 'checkbox alt',
							'atts' => 'checked',
							'type' => 'checkbox',
						)
					)
				),
			)
		),	// end of sections
		'typography_options' => array(
			'type' => 'section',
			'title' => esc_html__('Typography', 'aasana' ),
			'icon' => array('fa', 'font'),
			'layout' => array(
				'menu_font_options' => array(
					'type' => 'tab',
					'init' => 'open',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Menu', 'aasana' ),
					'layout' => array(
						'menu-font' => array(
							'title' => esc_html__('Menu Font', 'aasana' ),
							'type' => 'font',
							'font-color' => true,
							'font-size' => true,
							'font-sub' => true,
							'line-height' => true,
							'value' => array(
								'font-size' => '18px',
								'line-height' => '35px',
								'color' => '#707273',
								'font-family' => 'PT Sans',
								'font-weight' => array( 'regular', 'italic', '700', '700italic'),
								'font-sub' => array('latin'),
							)
						)
					)
				),
				'header_font_options' => array(
					'type' => 'tab',
					'icon' => array('fa', 'font'),
					'title' => esc_html__( 'Header', 'aasana' ),
					'layout' => array(
						'header-font' => array(
							'title' => esc_html__('Header\'s Font', 'aasana' ),
							'type' => 'font',
							'font-color' => true,
							'font-size' => true,
							'font-sub' => true,
							'line-height' => true,
							'value' => array(
								'font-size' => '48px',
								'line-height' => '36px',
								'color' => '#7b6cd5',
								'font-family' => 'Poppins',
								'font-weight' => array( '300', 'regular', '500', '600', '700' ),
								'font-sub' => array('latin'),
							),
						)
					)
				),
				'body_font_options' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Body', 'aasana' ),
					'layout' => array(
						'body-font' => array(
							'title' => esc_html__('Body Font', 'aasana' ),
							'type' => 'font',
							'font-color' => true,
							'font-size' => true,
							'font-sub' => true,
							'line-height' => true,
							'value' => array(
								'font-size' => '15px',
								'line-height' => '24px',
								'color' => '#545454',
								'font-family' => 'PT Sans',
								'font-weight' => array( 'regular','italic','700','700italic' ),
								'font-sub' => array('latin'),
							)
						)
					)
				),
				'helper_font_options' => array(
					'type' => 'tab',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Helper', 'aasana' ),
					'layout' => array(
						'helper-font' => array(
							'title' => esc_html__('Helper Font', 'aasana' ),
							'type' => 'font',
							'font-color' => true,
							'font-size' => true,
							'font-sub' => true,
							'line-height' => true,
							'value' => array(
								'font-size' => '26px',
								'line-height' => '36px',
								'color' => AASANA_COLOR,
								'font-family' => 'Lato',
								'font-weight' => array( '300'),
								'font-sub' => array('latin'),
							)
						)
					)
				),

			)
		), // end of sections
		'help_options' => array(
			'type' => 'section',
			'title' => esc_html__('Maintenance & Help', 'aasana' ),
			'icon' => array('fa', 'life-ring'),
			'layout' => array(
				'maintenance' => array(
					'type' => 'tab',
					'init' => 'open',
					'icon' => array('fa', 'calendar-plus-o'),
					'title' => esc_html__( 'Maintenance', 'aasana' ),
					'layout' => array(
						'show_loader' => array(
							'title' => esc_html__( 'ShowLoader', 'aasana' ),
							'addrowclasses' => 'grid-col-12 checkbox alt',
							'type' => 'checkbox',
							'atts' => 'checked data-options="e:loader_logo;e:overlay_loader_color"',
						),
						'loader_logo' => array(
							'title' => esc_html__( 'Loader logo (Square)', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-12 disable',
							'layout' => array(
								'logo_is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),
						'overlay_loader_color' => array(
							'title' => esc_html__( 'Loader Color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'value' => AASANA_COLOR,
							'addrowclasses' => 'disable grid-col-12',
							'type' => 'text',
						),
						'breadcrumbs' => array(
							'title' => esc_html__( 'Show breadcrumbs', 'aasana' ),
							'addrowclasses' => 'checkbox alt',
							'atts' => 'checked',
							'type' => 'checkbox',
						),
						'blog_author' => array(
							'title' => esc_html__( 'Show post author', 'aasana' ),
							'addrowclasses' => 'checkbox alt',
							'atts' => 'checked',
							'type' => 'checkbox',
						),
						'_theme_purchase_code' => array(
							'title' => esc_html__( 'Theme purchase code', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Item Purchase Code', 'aasana' ),
								'content' => esc_html__( 'Fill in this field with your Item Purchase Code in order to get the demo content and further theme updates.<br/> Please note, this code is applied to the theme only, it will not register Revolution Slider or any other plugins.', 'aasana' ),
							),
							'type' 	=> 'text',
							'value'	=> '',
							'customizer' 	=> array( 'show' => false )
						),
					)
				),
		    'animation' => array(
			     'type' => 'tab',
			     'icon' => array('fa', 'arrow-circle-o-up'),
			     'title' => esc_html__( 'Animation', 'aasana' ),
			     'layout' => array(
					'animation_curve_menu'	=> array(
						'title'	=> esc_html__( 'Animation (Menu Anchors)', 'aasana' ),
						'type'	=> 'select',
						'addrowclasses' => 'grid-col-6',
						'source'	=> array(
							'linear' => array( esc_html__( '1. linear', 'aasana' ), false ),
							'swing' => array( esc_html__( '2. swing', 'aasana' ), false ),
							'easeInQuad' => array( esc_html__( '3. easeInQuad', 'aasana' ), false ),
							'easeOutQuad' => array( esc_html__( '4. easeOutQuad', 'aasana' ), false ),
							'easeInOutQuad' => array( esc_html__( '5. easeInOutQuad', 'aasana' ), false ),
							'easeInCubic' => array( esc_html__( '6. easeInCubic', 'aasana' ), false ),
							'easeOutCubic' => array( esc_html__( '7. easeOutCubic', 'aasana' ), true ),
							'easeInOutCubic' => array( esc_html__( '8. easeInOutCubic', 'aasana' ), false ),
							'easeInQuart' => array( esc_html__( '9. easeInQuart', 'aasana' ), false ),
							'easeOutQuart' => array( esc_html__( '10. easeOutQuart', 'aasana' ), false ),
							'easeInOutQuart' => array( esc_html__( '11. easeInOutQuart', 'aasana' ), false ),
							'easeInQuint' => array( esc_html__( '12. easeInQuint', 'aasana' ), false ),
							'easeOutQuint' => array( esc_html__( '13. easeOutQuint', 'aasana' ), false ),
							'easeInOutQuint' => array( esc_html__( '14. easeInOutQuint', 'aasana' ), false ),
							'easeInSine' => array( esc_html__( '15. easeInSine', 'aasana' ), false ),
							'easeOutSine' => array( esc_html__( '16. easeOutSine', 'aasana' ), false ),
							'easeInOutSine' => array( esc_html__( '17. easeInOutSine', 'aasana' ), false ),
							'easeInExpo' => array( esc_html__( '18. easeInExpo', 'aasana' ), false ),
							'easeOutExpo' => array( esc_html__( '19. easeOutExpo', 'aasana' ), false ),
							'easeInOutExpo' => array( esc_html__( '20. easeInOutExpo', 'aasana' ), false ),
							'easeInCirc' => array( esc_html__( '21. easeInCirc', 'aasana' ), false ),
							'easeOutCirc' => array( esc_html__( '22. easeOutCirc', 'aasana' ), false ),
							'easeInOutCirc' => array( esc_html__( '23. easeInOutCirc', 'aasana' ), false ),
							'easeInElastic' => array( esc_html__( '24. easeInElastic', 'aasana' ), false ),
							'easeOutElastic' => array( esc_html__( '25. easeOutElastic', 'aasana' ), false ),
							'easeInOutElastic' => array( esc_html__( '26. easeInOutElastic', 'aasana' ), false ),
							'easeInBack' => array( esc_html__( '27. easeInBack', 'aasana' ), false ),
							'easeOutBack' => array( esc_html__( '28. easeOutBack', 'aasana' ), false ),
							'easeInOutBack' => array( esc_html__( '29. easeInOutBack', 'aasana' ), false ),
							'easeInBounce' => array( esc_html__( '30. easeInBounce', 'aasana' ), false ),
							'easeOutBounce' => array( esc_html__( '31. easeOutBounce', 'aasana' ), false ),
							'easeInOutBounce' => array( esc_html__( '32. easeInOutBounce', 'aasana' ), false ),
						),
					),
					'animation_curve_scrolltop'	=> array(
						'title'	=> esc_html__( 'Animation (ScrollTop)', 'aasana' ),
						'type'	=> 'select',
						'addrowclasses' => 'grid-col-6',
						'source'	=> array(
							'linear' => array( esc_html__( '1. linear', 'aasana' ), false ),
							'swing' => array( esc_html__( '2. swing', 'aasana' ), false ),
							'easeInQuad' => array( esc_html__( '3. easeInQuad', 'aasana' ), false ),
							'easeOutQuad' => array( esc_html__( '4. easeOutQuad', 'aasana' ), false ),
							'easeInOutQuad' => array( esc_html__( '5. easeInOutQuad', 'aasana' ), true ),
							'easeInCubic' => array( esc_html__( '6. easeInCubic', 'aasana' ), false ),
							'easeOutCubic' => array( esc_html__( '7. easeOutCubic', 'aasana' ), false ),
							'easeInOutCubic' => array( esc_html__( '8. easeInOutCubic', 'aasana' ), false ),
							'easeInQuart' => array( esc_html__( '9. easeInQuart', 'aasana' ), false ),
							'easeOutQuart' => array( esc_html__( '10. easeOutQuart', 'aasana' ), false ),
							'easeInOutQuart' => array( esc_html__( '11. easeInOutQuart', 'aasana' ), false ),
							'easeInQuint' => array( esc_html__( '12. easeInQuint', 'aasana' ), false ),
							'easeOutQuint' => array( esc_html__( '13. easeOutQuint', 'aasana' ), false ),
							'easeInOutQuint' => array( esc_html__( '14. easeInOutQuint', 'aasana' ), false ),
							'easeInSine' => array( esc_html__( '15. easeInSine', 'aasana' ), false ),
							'easeOutSine' => array( esc_html__( '16. easeOutSine', 'aasana' ), false ),
							'easeInOutSine' => array( esc_html__( '17. easeInOutSine', 'aasana' ), false ),
							'easeInExpo' => array( esc_html__( '18. easeInExpo', 'aasana' ), false ),
							'easeOutExpo' => array( esc_html__( '19. easeOutExpo', 'aasana' ), false ),
							'easeInOutExpo' => array( esc_html__( '20. easeInOutExpo', 'aasana' ), false ),
							'easeInCirc' => array( esc_html__( '21. easeInCirc', 'aasana' ), false ),
							'easeOutCirc' => array( esc_html__( '22. easeOutCirc', 'aasana' ), false ),
							'easeInOutCirc' => array( esc_html__( '23. easeInOutCirc', 'aasana' ), false ),
							'easeInElastic' => array( esc_html__( '24. easeInElastic', 'aasana' ), false ),
							'easeOutElastic' => array( esc_html__( '25. easeOutElastic', 'aasana' ), false ),
							'easeInOutElastic' => array( esc_html__( '26. easeInOutElastic', 'aasana' ), false ),
							'easeInBack' => array( esc_html__( '27. easeInBack', 'aasana' ), false ),
							'easeOutBack' => array( esc_html__( '28. easeOutBack', 'aasana' ), false ),
							'easeInOutBack' => array( esc_html__( '29. easeInOutBack', 'aasana' ), false ),
							'easeInBounce' => array( esc_html__( '30. easeInBounce', 'aasana' ), false ),
							'easeOutBounce' => array( esc_html__( '31. easeOutBounce', 'aasana' ), false ),
							'easeInOutBounce' => array( esc_html__( '32. easeInOutBounce', 'aasana' ), false ),
						),
					),
					'curves' => array(
						'type' => 'info',
						'addrowclasses' => 'grid-col-12',
						'value' => '<img src="'. get_template_directory_uri() . '/img/easing.png" />',
						'title' => esc_html__('Easing curves', 'aasana' )
					),
			    )
		    ),
				'help' => array(
					'type' => 'tab',
					'icon' => array('fa', 'calendar-plus-o'),
					'title' => esc_html__( 'Help', 'aasana' ),
					'layout' => array(
						'help' => array(
								 'title' 			=> esc_html__( 'Help', 'aasana' ),
								 'type' 			=> 'info',
								 'subtype'		=> 'custom',
								 'value' 			=> '<a class="cwsfw_info_button" href="http://aasana.cwsthemes.com/manual" target="_blank"><i class="fa fa-life-ring"></i>&nbsp;&nbsp;' . esc_html__( 'Online Tutorial', 'aasana' ) . '</a>&nbsp;&nbsp;<a class="cwsfw_info_button" href="https://www.youtube.com/user/cwsvideotuts/playlists" target="_blank"><i class="fa fa-video-camera"></i>&nbsp;&nbsp;' . esc_html__( 'Video Tutorial', 'aasana' ) . '</a>',
							),
					)
				),
				'crop' => array(
					'type' => 'tab',
					'icon' => array('fa', 'calendar-plus-o'),
					'title' => esc_html__( 'Crop Images', 'aasana' ),
					'layout' => array(
						'crop_x' => array(
							'title' => esc_html__( 'Crop X', 'aasana' ),
							'type' => 'radio',
							'addrowclasses' => 'grid-col-3',
							'value' => array(
								'left' => array( esc_html__( 'Left', 'aasana' ),  false, '' ),
								'center' => array( esc_html__( 'Center', 'aasana' ),  true, '' ),
								'right' => array( esc_html__( 'Right', 'aasana' ),  false, '' ),
							),
						),
						'crop_y' => array(
							'title' => esc_html__( 'Crop Y', 'aasana' ),
							'type' => 'radio',
							'addrowclasses' => 'grid-col-3',
							'value' => array(
								'top' => array( esc_html__( 'Top', 'aasana' ),  false, '' ),
								'center' => array( esc_html__( 'Center', 'aasana' ),  true, '' ),
								'bottom' => array( esc_html__( 'Bottom', 'aasana' ),  false, '' ),
							),
						),

					)
				),
			)
		),
		
		'social_options' => array(
			'type' => 'section',
			'title' => esc_html__('Social Networks', 'aasana' ),
			'icon' => array('fa', 'share-alt'),
			'layout' => array(
				'social_option'	=> array(
					'type' => 'tab',
					'init'	=> 'open',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Social Options', 'aasana' ),
					'layout' => array(
						'socials' => array(
							'type' => 'fields',
							'addrowclasses' => 'grid-col-12',
							'layout' => array(
								'location' => array(
									'type' => 'select',
									'addrowclasses' => 'grid-col-12',
									'title' => esc_html__( 'Social Links Location', 'aasana' ),
									'atts' => 'multiple data-none="d:top_bar"',
									'source' => array(
										'top' => array( esc_html__( 'Top Bar', 'aasana' ), true, 'e:top_bar;'),
										'bottom' => array( esc_html__( 'Copyrights area', 'aasana' ), false, 'd:top_bar;'),
									),
								),
								'top_bar' => array(
									'title' => esc_html__( 'Social Links (Top Bar)', 'aasana' ),
									'type' => 'radio',
									'subtype' => 'images',
									'addrowclasses' => 'disable grid-col-12',
									'value' => array(
										'left' =>array( esc_html__( 'Left', 'aasana' ), false, '', '/img/hamb-left.png' ),
										'right' =>array( esc_html__( 'Right', 'aasana' ), false, '', '/img/hamb-right.png' ),
									),
								),
							),
						),
						'social_group' => array(
							'type' => 'group',
							'addrowclasses' => 'group sortable',
							'title' => esc_html__('Social Networks', 'aasana' ),
							'button_title' => esc_html__('Add new social network', 'aasana' ),
							'button_icon' => 'fa fa-plus',
							'layout' => array(
								'title' => array(
									'type' => 'text',
									'atts' => 'data-role="title"',
									'title' => esc_html__('Social account title', 'aasana' ),
								),
								'icon' => array(
									'type' => 'select',
									'addrowclasses' => 'fai',
									'source' => 'fa',
									'title' => esc_html__('Select the icon for this social contact', 'aasana' )
								),
								'url' => array(
									'type' => 'text',
									'title' => esc_html__('Url to your account', 'aasana' ),
								),
								'open' => array(
									'title' => esc_html__( 'Open in a new window', 'aasana' ),
									'type' => 'checkbox',
									'addrowclasses'	=> 'checkbox',
								),
							)
						),
					)
				),
			)
		), // end of sections

		'social_options' => array(
			'type' => 'section',
			'title' => esc_html__('Social Networks', 'aasana' ),
			'icon' => array('fa', 'share-alt'),
			'layout' => array(
				'social_option'	=> array(
					'type' => 'tab',
					'init'	=> 'open',
					'icon' => array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Social Options', 'aasana' ),
					'layout' => array(
						'socials' => array(
							'type' => 'fields',
							'addrowclasses' => 'inside-box groups grid-col-12 box',
							'layout' => array(
								'location' => array(
									'title' => esc_html__( 'Social Links Location', 'aasana' ),
									'type' => 'select',
									'atts' => 'multiple data-none="d:top_bar"',
									'addrowclasses' => 'grid-col-12 box',
									'source' => array(
										'top' => array( esc_html__( 'Top Bar', 'aasana' ), true, 'e:top_bar;'),
										'bottom' => array( esc_html__( 'Copyrights area', 'aasana' ), false, 'd:top_bar;'),
									),
								),
								'top_bar' => array(
									'title' => esc_html__( 'Social Links (Top Bar)', 'aasana' ),
									'type' => 'radio',
									'subtype' => 'images',
									'addrowclasses' => 'disable grid-col-12 clear float',
									'value' => array(
										'left' =>array( esc_html__( 'Left', 'aasana' ), false, '', '/img/hamb-left.png' ),
										'right' =>array( esc_html__( 'Right', 'aasana' ), false, '', '/img/hamb-right.png' ),
									),
								),
							),
						),
						'social_group' => array(
							'type' => 'group',
							'addrowclasses' => 'group sortable grid-col-12 box',
							'title' => esc_html__('Social Networks', 'aasana' ),
							'button_title' => esc_html__('Add new social network', 'aasana' ),
							'button_icon' => 'fa fa-plus',
							'layout' => array(
								'title' => array(
									'type' => 'text',
									'atts' => 'data-role="title"',
									'title' => esc_html__('Social account title', 'aasana' ),
								),
								'icon' => array(
									'type' => 'select',
									'addrowclasses' => 'fai',
									'source' => 'fa',
									'title' => esc_html__('Select the icon for this social contact', 'aasana' )
								),
								'url' => array(
									'type' => 'text',
									'title' => esc_html__('Url to your account', 'aasana' ),
								),
								'open' => array(
									'title' => esc_html__( 'Open in a new window', 'aasana' ),
									'type' => 'checkbox',
									'addrowclasses'	=> 'checkbox',
								),
							)
						),
					)
				),
			)
		), // end of sections
	);

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  {
		$settings['woo_options'] = array(
			'type'		=> 'section',
			'title'		=> esc_html__( 'WooCommerce', 'aasana' ),
			'icon'		=> array('fa', 'shopping-cart'),
			'layout'	=> array(
				'woo_options' => array(
					'type' 	=> 'tab',
					'init'	=> 'open',
					'icon' 	=> array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Woocommerce', 'aasana' ),
					'layout' => array(
						'woo_cart_enable'	=> array(
							'title'			=> esc_html__( 'Show WooCommerce Cart', 'aasana' ),
							'type'			=> 'checkbox',
							'addrowclasses'	=> 'checkbox alt',
							'atts' => 'checked data-options="e:woo_cart_place;"',
						),
						'woo_cart_place' => array(
							'title' => esc_html__( 'WooCommerce Cart position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'addrowclasses' => 'disable',
							'value' => array(
								'top' =>array( esc_html__( 'TopBar', 'aasana' ), false, '', '/img/woo-cart-top-right.png' ),
								'left' =>array( esc_html__( 'Menu (Left)', 'aasana' ), false, '', '/img/woo-cart-menu-left.png' ),
								'right' =>array( esc_html__( 'Menu (Right)', 'aasana' ), true, '', '/img/woo-cart-menu-right.png' ),
							),
						),
						'woo_sb_layout' => array(
							'title' => esc_html__('Sidebar Position', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'value' => array(
								'left' => 	array( esc_html__('Left', 'aasana' ), false, 'e:woo_sidebar;',	'/img/left.png' ),
								'right' => 	array( esc_html__('Right', 'aasana' ), true, 'e:woo_sidebar;', '/img/right.png' ),
								'none' => 	array( esc_html__('None', 'aasana' ), false, 'd:woo_sidebar;', '/img/none.png' )
							),
						),
						'woo_sidebar' => array(
							'title' => esc_html__('Select a sidebar', 'aasana' ),
							'type' => 'select',
							'addrowclasses' => 'disable',
							'source' => 'sidebars',
						),	
						'woo_sb_layout_single' => array(
							'title' => esc_html__('Sidebar Position Single', 'aasana' ),
							'type' => 'radio',
							'subtype' => 'images',
							'value' => array(
								'left' => 	array( esc_html__('Left', 'aasana' ), false, 'e:woo_sidebar_single;',	'/img/left.png' ),
								'right' => 	array( esc_html__('Right', 'aasana' ), false, 'e:woo_sidebar_single;', '/img/right.png' ),
								'none' => 	array( esc_html__('None', 'aasana' ), true, 'd:woo_sidebar_single;', '/img/none.png' )
							),
						),					
						'woo_sidebar_single' => array(
							'title' => esc_html__('Select a Single sidebar', 'aasana' ),
							'type' => 'select',
							'addrowclasses' => 'disable',
							'source' => 'sidebars',
						),
						'woo_columns' => array(
							'type' => 'select',
							'title' => esc_html__( 'Columns layout', 'aasana' ),
							'source' => array(
								'2' => array('Two Columns',false, ''),
								'3' => array('Three Columns',true, ''),
								'4' => array('Four Columns',false, '')
							),
						),

						'woo_num_products'	=> array(
							'title'			=> esc_html__( 'Products per page', 'aasana' ),
							'type'			=> 'number',
							'value'			=> get_option( 'posts_per_page' )
						),
						'woo_related_num_products'	=> array(
							'title'			=> esc_html__( 'Related products', 'aasana' ),
							'type'			=> 'number',
							'value'			=> get_option( 'posts_per_page' )
						),
						'shop-slider-type' => array(
							'title' => esc_html__('Slider', 'aasana' ),
							'type' => 'radio',
							'value' => array(
								'none' => 	array( esc_html__('None', 'aasana' ), true, 'd:shop-header-slider-options;d:shopslidersection-start;d:static_img_section' ),
								'img-slider'=>	array( esc_html__('Image Slider', 'aasana' ), false, 'e:shop-header-slider-options;d:shopslidersection-start;d:static_img_section' ),
								'video-slider' => 	array( esc_html__('Video Slider', 'aasana' ), false, 'd:shop-header-slider-options;e:shopslidersection-start;d:static_img_section' ),
								'stat-img-slider' => 	array( esc_html__('Static image', 'aasana' ), false, 'd:shop-header-slider-options;d:shopslidersection-start;e:static_img_section' ),
							),
						),
						'shop-header-slider-options' => array(
							'title' => esc_html__( 'Slider shortcode', 'aasana' ),
							'addrowclasses' => 'disable',
							'type' => 'text',
							'value' => '[rev_slider shoppage]',
						),
						'shopslidersection-start' => array(
							'title' => esc_html__( 'Video Slider Setting', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'disable groups',
							'layout' => array(
								'slider_switch' => array(
									'title' => esc_html__( 'Slider', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:slider_shortcode;"',
								),
								'slider_shortcode' => array(
									'title' => esc_html__( 'Slider shortcode', 'aasana' ),
									'addrowclasses' => 'disable box',
									'type' => 'text',
								),
								'set_video_header_height' => array(
									'title' => esc_html__( 'Set Video height', 'aasana' ),
									'type' => 'checkbox',
									'addrowclasses' => 'checkbox',
									'atts' => 'data-options="e:video_header_height"',
								),
								'video_header_height' => array(
									'title' => esc_html__( 'Video height', 'aasana' ),
									'addrowclasses' => 'disable box',
									'type' => 'number',
									'value' => '600',
								),
								'video_type' => array(
									'title' => esc_html__('Video type', 'aasana' ),
									'type' => 'radio',
									'value' => array(
										'self_hosted' => 	array( esc_html__('Self-hosted', 'aasana' ), true, 'e:sh_source;d:youtube_source;d:vimeo_source' ),
										'youtube'=>	array( esc_html__('Youtube clip', 'aasana' ), false, 'd:sh_source;e:youtube_source;d:vimeo_source' ),
										'vimeo' => 	array( esc_html__('Vimeo clip', 'aasana' ), false, 'd:sh_source;d:youtube_source;e:vimeo_source' ),
									),
								),
								'sh_source' => array(
									'title' => esc_html__( 'Add video', 'aasana' ),
									'addrowclasses' => 'box',
									'url-atts' => 'readonly',
									'type' => 'media',
								),
								'youtube_source' => array(
									'title' => esc_html__( 'Youtube video code', 'aasana' ),
									'addrowclasses' => 'disable box',
									'type' => 'text',
								),
								'vimeo_source' => array(
									'title' => esc_html__( 'Vimeo embed url', 'aasana' ),
									'addrowclasses' => 'disable box',
									'type' => 'text',
								),
								'color_overlay_type' => array(
									'title' => esc_html__( 'Overlay type', 'aasana' ),
									'type' => 'select',
									'source' => array(
										'none' => array( esc_html__( 'None', 'aasana' ), 	true, 'd:overlay_color;d:slider_gradient_settings;d:color_overlay_opacity;'),
										'color' => array( esc_html__( 'Color', 'aasana' ), 	false, 'e:overlay_color;d:slider_gradient_settings;e:color_overlay_opacity;'),
										'gradient' =>array( esc_html__( 'Gradient', 'aasana' ), false, 'd:overlay_color;e:slider_gradient_settings;e:color_overlay_opacity;'),
									),
								),
								'overlay_color' => array(
									'title' => esc_html__( 'Overlay Color', 'aasana' ),
									'atts' => 'data-default-color=""',
									'addrowclasses' => 'box',
									'type' => 'text',
								),
								'color_overlay_opacity' => array(
									'type' => 'number',
									'addrowclasses' => 'box',
									'title' => esc_html__( 'Opacity', 'aasana' ),
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'value' => '40'
								),
								'slider_gradient_settings' => array(
									'title' => esc_html__( 'Gradient settings', 'aasana' ),
									'type' => 'fields',
									'addrowclasses' => 'disable box groups',
									'layout' => array(
										'first_color' => array(
											'type' => 'text',
											'title' => esc_html__( 'From', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'second_color' => array(
											'type' => 'text',
											'title' => esc_html__( 'To', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'first_color_opacity' => array(
											'type' => 'number',
											'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'second_color_opacity' => array(
											'type' => 'number',
											'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'type' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'type' => 'radio',
											'value' => array(
												'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
												'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
											),
										),
										'linear_settings' => array(
											'title' => esc_html__( 'Linear settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'angle' => array(
													'type' => 'number',
													'title' => esc_html__( 'Angle', 'aasana' ),
													'value' => '45',
												),
											)
										),
										'radial_settings' => array(
											'title' => esc_html__( 'Radial settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'shape_settings' => array(
													'title' => esc_html__( 'Shape', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
														'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
													),
												),
												'shape' => array(
													'title' => esc_html__( 'Gradient type', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
														'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
													),
												),
												'size_keyword' => array(
													'type' => 'select',
													'title' => esc_html__( 'Size keyword', 'aasana' ),
													'addrowclasses' => 'disable',
													'source' => array(
														'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
														'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
														'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
														'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
													),
												),
												'size' => array(
													'type' => 'text',
													'addrowclasses' => 'disable',
													'title' => esc_html__( 'Size', 'aasana' ),
													'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
												),
											)
										)

									),
								),
								'use_pattern' => array(
									'title' => esc_html__( 'Use pattern image', 'aasana' ),
									'type' => 'checkbox',
									'addrowclasses' => 'checkbox',
									'atts' => 'data-options="e:pattern_image"',
								),
								'pattern_image' => array(
									'title' => esc_html__( 'Pattern image', 'aasana' ),
									'addrowclasses' => 'disable box',
									'url-atts' => 'readonly',
									'type' => 'media',
								),
							),
						),// end of video-section
						'static_img_section' => array(
							'title' => esc_html__( 'Static image Slider Setting', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'groups',
							'layout' => array(
								'shop_header_image_options' => array(
									'title' => esc_html__( 'Static image', 'aasana' ),
									'type' => 'media',
									'url-atts' => 'readonly',
									'layout' => array(
										'is_high_dpi' => array(
											'title' => esc_html__( 'High-Resolution image', 'aasana' ),
											'type' => 'checkbox',
											'addrowclasses' => 'checkbox',
										),
									),
								),
								'set_static_image_height' => array(
									'title' => esc_html__( 'Set Image height', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:static_image_height;"',
								),
								'static_image_height' => array(
									'title' => esc_html__( 'Static Image Height', 'aasana' ),
									'addrowclasses' => 'disable box',
									'type' => 'number',
									'default' => '600',
								),
								'static_customize_colors' => array(
									'title' => esc_html__( 'Customize colors', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:img_header_color_overlay_type;e:img_header_overlay_color;e:img_header_color_overlay_opacity;"',
								),
								'img_header_color_overlay_type'	=> array(
									'title'		=> esc_html__( 'Color overlay type', 'aasana' ),
									'type'	=> 'select',
									'addrowclasses' => 'box disable',
									'source'	=> array(
										'color' => array( esc_html__( 'Color', 'aasana' ),  true, 'e:img_header_overlay_color;d:img_header_gradient_settings;' ),
										'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'd:img_header_overlay_color;e:img_header_gradient_settings;' )
									),
								),
								'img_header_overlay_color'	=> array(
									'title'	=> esc_html__( 'Overlay color', 'aasana' ),
									'atts' => 'data-default-color="' . AASANA_COLOR . '"',
									'value' => AASANA_COLOR,
									'addrowclasses' => 'box disable',
									'type'	=> 'text',
								),
								'img_header_gradient_settings' => array(
									'title' => esc_html__( 'Gradient Settings', 'aasana' ),
									'type' => 'fields',
									'addrowclasses' => 'disable box groups',
									'layout' => array(
										'first_color' => array(
											'type' => 'text',
											'title' => esc_html__( 'From', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'second_color' => array(
											'type' => 'text',
											'title' => esc_html__( 'To', 'aasana' ),
											'atts' => 'data-default-color=""',
										),
										'first_color_opacity' => array(
											'type' => 'number',
											'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'second_color_opacity' => array(
											'type' => 'number',
											'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
											'value' => '100',
										),
										'type' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'type' => 'radio',
											'value' => array(
												'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:img_header_gradient_linear_settings;d:img_header_gradient_radial_settings' ),
												'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:img_header_gradient_linear_settings;e:img_header_gradient_radial_settings' ),
											),
										),
										'linear_settings' => array(
											'title' => esc_html__( 'Linear settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'angle' => array(
													'type' => 'number',
													'title' => esc_html__( 'Angle', 'aasana' ),
													'value' => '45',
												),
											)
										),
										'radial_settings' => array(
											'title' => esc_html__( 'Radial settings', 'aasana'  ),
											'type' => 'fields',
											'addrowclasses' => 'disable',
											'layout' => array(
												'shape_settings' => array(
													'title' => esc_html__( 'Shape', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:img_header_gradient_shape;d:img_header_gradient_size;d:img_header_gradient_size_keyword;' ),
														'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:img_header_gradient_shape;e:img_header_gradient_size;e:img_header_gradient_size_keyword;' ),
													),
												),
												'shape' => array(
													'title' => esc_html__( 'Gradient type', 'aasana' ),
													'type' => 'radio',
													'value' => array(
														'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
														'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
													),
												),
												'img_header_gradient_size_keyword' => array(
													'type' => 'select',
													'title' => esc_html__( 'Size keyword', 'aasana' ),
													'addrowclasses' => 'disable',
													'source' => array(
														'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
														'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
														'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
														'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
													),
												),
												'img_header_gradient_size' => array(
													'type' => 'text',
													'addrowclasses' => 'disable',
													'title' => esc_html__( 'Size', 'aasana' ),
													'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
												),
											)
										)
									)
								),
								'img_header_color_overlay_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'Opacity', 'aasana' ),
									'addrowclasses' => 'box disable',
									'placeholder' => esc_html__( 'In percents', 'aasana' ),
									'value' => '40'
								),
								'img_header_use_pattern' => array(
									'title' => esc_html__( 'Add pattern', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:img_header_pattern_image;"',
								),
								'img_header_pattern_image' => array(
									'title' => esc_html__( 'Pattern image', 'aasana' ),
									'type' => 'media',
									'addrowclasses' => 'disable box',
									'url-atts' => 'readonly',
								),
								'img_header_parallaxify' => array(
									'title' => esc_html__( 'Parallaxify image', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
									'atts' => 'data-options="e:img_header_parallax_options;"',
								),
								'img_header_parallax_options' => array(
									'title' => esc_html__( 'Parallax options', 'aasana' ),
									'type' => 'fields',
									'addrowclasses' => 'disable box groups',
									'layout' => array(
										'img_header_scalar-x' => array(
											'type' => 'number',
											'title' => esc_html__( 'x-axis parallax intensity', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '2'
										),
										'img_header_scalar-y' => array(
											'type' => 'number',
											'title' => esc_html__( 'y-axis parallax intensity', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '2'
										),
										'img_header_limit-x' => array(
											'type' => 'number',
											'title' => esc_html__( 'Maximum x-axis shift', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '15'
										),
										'img_header_limit-y' => array(
											'type' => 'number',
											'title' => esc_html__( 'Maximum y-axis shift', 'aasana' ),
											'placeholder' => esc_html__( 'Integer', 'aasana' ),
											'value' => '15'
										),
									),
								),
							),
						),// end of static img slider-section
					)
				),
				'woo_menu_options' => array(
					'type' 	=> 'tab',
					'icon' 	=> array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Menu', 'aasana' ),
					'layout' => array(
						'woo_customize_menu'	=> array(
							'title'	=> esc_html__( 'Customize WooCommerce Menu', 'aasana' ),
							'type'	=> 'checkbox',
							'addrowclasses' => 'checkbox alt grid-col-12',
							'atts' => 'data-options="e:show_menu_bg_color;e:woo_menu_font_color;e:woo_menu_border_color;e:woo_header_covers_slider"',		
						),							
						'show_menu_bg_color' => array(
							'title' => esc_html__( 'Add Background Color', 'aasana' ),
							'addrowclasses' => 'checkbox disable',
							'type' => 'checkbox',
							'atts' => 'data-options="e:woo_menu_opacity;e:woo_menu_bg_color"',
						),
						'woo_menu_opacity' => array(
							'title' 		=> esc_html__( 'Opacity', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Menu Opacity', 'aasana' ),
								'content' => esc_html__( 'This option will apply a transparent header when set to 0. Options available from 0 to 100', 'aasana' ),
							),								
							'type' 			=> 'number',
							'addrowclasses' => 'grid-col-6 disable',
							'atts' 			=> " min='0' max='100'",
							'value'			=> '100'
						),
						'woo_menu_bg_color' => array(
							'title' 		=> esc_html__( 'Background Color', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Background Color', 'aasana' ),
								'content' => esc_html__( 'Change the background color of the menu and logo area.', 'aasana' ),
							),							
							'type' 			=> 'text',
							'addrowclasses' => 'grid-col-6 disable',
							'atts' 			=> 'data-default-color="#f8f8f8"',
							'value'			=> '#f8f8f8'
						),
						'woo_menu_font_color' => array(
							'title' 		=> esc_html__( 'Override Font Color', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Override Font Color', 'aasana' ),
								'content' => esc_html__( 'This color is applied to the main menu only, sub-menu items will use the color which is set in Typography section.<br /> This option is very useful when menu and logo covers title area or slider.', 'aasana' ),
							),							
							'type' 			=> 'text',
							'addrowclasses' => 'grid-col-12 disable',
							'atts' 			=> 'data-default-color="#fff;"',
							'value'			=> '#fff'
						),						
						'woo_menu_border_color' => array(
							'title' 		=> esc_html__( 'Override Border Color', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Override Border Color', 'aasana' ),
								'content' => esc_html__( 'This color is applied to the main menu only, sub-menu items will use the color which is set in Typography section.<br /> This option is very useful when menu and logo covers title area or slider.', 'aasana' ),
							),							
							'type' 			=> 'text',
							'addrowclasses' => 'grid-col-12 disable',
							'atts' 			=> 'data-default-color="#fff;"',
							'value'			=> '#fff'
						),
						'woo_header_covers_slider' => array(
							'title' => esc_html__( 'Header Hover Slider', 'aasana' ),
							'tooltip' => array(
								'title' => esc_html__( 'Menu Overlays Slider', 'aasana' ),
								'content' => esc_html__( 'This option will force the menu and logo sections to overlay the title area. <br> It is useful when using transparent menu.', 'aasana' ),
							),							
							'type' => 'checkbox',
							'addrowclasses' => 'checkbox grid-col-12 disable'
						),		
						'woo_customize_logotype'	=> array(
							'title'	=> esc_html__( 'Customize WooCommerce Logotype', 'aasana' ),
							'type'	=> 'checkbox',
							'addrowclasses' => 'checkbox alt grid-col-12',
							'atts' => 'data-options="e:logo_woo"',		
						),
						'logo_woo' => array(
							'title' => esc_html__( 'Logotype Woocommerce', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-12 disable',
							'layout' => array(
								'is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),
					)
				),
				'woo_title_options' => array(
					'type' 	=> 'tab',
					'icon' 	=> array('fa', 'arrow-circle-o-up'),
					'title' => esc_html__( 'Title Area', 'aasana' ),
					'layout' => array(
						'woo_customize_title'	=> array(
							'title'	=> esc_html__( 'Customize WooCommerce Title Area', 'aasana' ),
							'type'	=> 'checkbox',
							'atts' => 'data-options="e:woo_hide_title"',
							'addrowclasses' => 'checkbox alt grid-col-12'		
						),	
						'woo_hide_title'	=> array(
							'title'	=> esc_html__( 'Switch on/off the title area', 'aasana' ),
							'type'	=> 'checkbox',
							'atts' => 'data-options="e:woo_page_title_spacings;e:woo_default_header_image;e:woo_header_font_color;e:woo_color_overlay_type;e:woo_header_center;e:woo_breadcrumbs_divider;e:woo_breadcrumbs_dimensions;e:woo_breadcrumbs-margin"',
							'addrowclasses' => 'checkbox alt grid-col-12 disable'		
						),	
						'woo_page_title_spacings' => array(
							'title' => esc_html__( 'Add Spacings (px)', 'aasana' ),
							'type' => 'margins',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '60'),
								'left' => array('placeholder' => esc_html__( 'left', 'aasana' ), 'value' => '0'),
								'right' => array('placeholder' => esc_html__( 'Right', 'aasana' ), 'value' => '0'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '60'),
							),
							'addrowclasses' => 'grid-col-6 disable'
						),
						'woo_default_header_image'	=> array(
							'title'	=> esc_html__( 'Add Background Image', 'aasana' ),
							'addrowclasses' => 'grid-col-6 disable',
							'type'	=> 'media'
						),
						'woo_header_font_color' => array(
							'title' 			=> esc_html__( 'Override Font Color', 'aasana' ),
							'atts' 				=> 'data-default-color=""',
							'type' 				=> 'text',
							'addrowclasses' 	=> 'grid-col-12 disable',
							'value'				=> 	""
						),
						'woo_color_overlay_type'	=> array(
							'title'		=> esc_html__( 'Color Overlay', 'aasana' ),
							'addrowclasses' => 'grid-col-12 disable',
							'type'	=> 'select',
							'source'	=> array(
								'none' => array( esc_html__( 'None', 'aasana' ),  true, 'd:woo_color_overlay_opacity;d:woo_overlay_color;d:woo_gradient_settings;' ),
								'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:woo_color_overlay_opacity;e:woo_overlay_color;d:woo_gradient_settings;' ),
								'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'e:woo_color_overlay_opacity;d:woo_overlay_color;e:woo_gradient_settings;' )
								),
							),
						'woo_color_overlay_opacity' => array(
							'type' => 'number',
							'addrowclasses' => 'disable grid-col-12',
							'title' => esc_html__( 'Opacity', 'aasana' ),
							'placeholder' => esc_html__( 'In percents', 'aasana' ),
							'value' => '40'
							),
						'woo_overlay_color'	=> array(
							'title'	=> esc_html__( 'Overlay color', 'aasana' ),
							'atts' => 'data-default-color="' . AASANA_COLOR . '"',
							'addrowclasses' => 'disable grid-col-12',
							'value' => AASANA_COLOR,
							'type'	=> 'text'
							),
						'woo_gradient_settings' => array(
							'title' => esc_html__( 'Gradient Settings', 'aasana' ),
							'type' => 'fields',
							'addrowclasses' => 'disable box inside-box groups grid-col-12',
							'layout' => array(
								'first_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'From', 'aasana' ),
									'atts' => 'data-default-color=""',
									),
								'second_color' => array(
									'type' => 'text',
									'title' => esc_html__( 'To', 'aasana' ),
									'atts' => 'data-default-color=""',
									),
								'first_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
									'value' => '100',
									),
								'second_color_opacity' => array(
									'type' => 'number',
									'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
									'value' => '100',
									),
								'type' => array(
									'title' => esc_html__( 'Gradient type', 'aasana' ),
									'type' => 'radio',
									'value' => array(
										'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear_settings;d:radial_settings' ),
										'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear_settings;e:radial_settings' ),
										),
									),
								'linear_settings' => array(
									'title' => esc_html__( 'Linear settings', 'aasana'  ),
									'type' => 'fields',
									'addrowclasses' => 'disable',
									'layout' => array(
										'angle' => array(
											'type' => 'number',
											'title' => esc_html__( 'Angle', 'aasana' ),
											'value' => '45',
											),
										)
									),
								'radial_settings' => array(
									'title' => esc_html__( 'Radial settings', 'aasana'  ),
									'type' => 'fields',
									'addrowclasses' => 'disable',
									'layout' => array(
										'shape_settings' => array(
											'title' => esc_html__( 'Shape', 'aasana' ),
											'type' => 'radio',
											'value' => array(
												'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:size_keyword;' ),
												'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:size_keyword;' ),
												),
											),
										'shape' => array(
											'title' => esc_html__( 'Gradient type', 'aasana' ),
											'type' => 'radio',
											'value' => array(
												'ellipse' => array( esc_html__( 'Ellipse', 'aasana' ),  true ),
												'circle' =>array( esc_html__( 'Circle', 'aasana' ), false ),
												),
											),
										'size_keyword' => array(
											'type' => 'select',
											'title' => esc_html__( 'Size keyword', 'aasana' ),
											'addrowclasses' => 'disable',
											'source' => array(
												'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
												'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
												'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
												'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
												),
											),
										'size' => array(
											'type' => 'text',
											'addrowclasses' => 'disable',
											'title' => esc_html__( 'Size', 'aasana' ),
											'atts' => 'placeholder="'.esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ).'"',
											),
										)
									),
								)
							),
						'woo_header_center' => array(
							'title' => esc_html__( 'Center Title & Breadcrumbs', 'aasana' ),
							'addrowclasses' => 'grid-col-6 disable',
							'type' => 'checkbox',
						),
						'woo_breadcrumbs_divider' => array(
							'title' => esc_html__( 'Breadcrumbs Divider', 'aasana' ),
							'type' => 'media',
							'url-atts' => 'readonly',
							'addrowclasses' => 'grid-col-12',
								// 'value' => array( 'id' => '', 'src' => get_template_directory_uri() . '\img\logo_the8_128x128.png') ,
							'layout' => array(
								'is_high_dpi' => array(
									'title' => esc_html__( 'High-Resolution logo', 'aasana' ),
									'addrowclasses' => 'checkbox',
									'type' => 'checkbox',
								),
							),
						),

						'woo_breadcrumbs_dimensions' => array(
							'title' => esc_html__( 'Breadcrumbs Divider Dimensions', 'aasana' ),
							'type' => 'dimensions',
							'addrowclasses' => 'disable grid-col-12',
							'value' => array(
								'width' => array('placeholder' => esc_html__( 'Width', 'aasana' ), 'value' => ''),
								'height' => array('placeholder' => esc_html__( 'Height', 'aasana' ), 'value' => ''),
								),
							),
						'woo_breadcrumbs-margin' => array(
							'title' => esc_html__( 'Margins (px)', 'aasana' ),
							'type' => 'margins',
							'addrowclasses' => 'disable grid-col-4',
							'value' => array(
								'top' => array('placeholder' => esc_html__( 'Top', 'aasana' ), 'value' => '0'),
								'left' => array('placeholder' => esc_html__( 'left', 'aasana' ), 'value' => '0'),
								'right' => array('placeholder' => esc_html__( 'Right', 'aasana' ), 'value' => '0'),
								'bottom' => array('placeholder' => esc_html__( 'Bottom', 'aasana' ), 'value' => '0'),
								),
							),
					)
				)
			)
		);
	}
	if (function_exists('cws_core_build_settings')) {
		cws_core_build_settings($settings, $g_components);
	}
	return $settings;
}

/*
	here local or overrided components can be added/changed
*/
function cwsfw_get_local_components() {
	return array();
}
?>