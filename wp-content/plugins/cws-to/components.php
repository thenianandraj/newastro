<?php
function cws_core_merge_components($g, $l) {
	return array_merge($g, $l);
}

/*
	build resulting array with replaced %name% layouts with their real components
	nothing to return as we work with a reference
*/
function cws_core_build_settings (&$layout, $components) {
	foreach ($layout as $key => &$v) {
		if (isset($v['layout'])) {
			$llayout = $v['layout'];
			if (is_string($llayout) && '%' === substr($llayout, 0, 1)) {
				$name = substr($llayout, 1, -1);
				if (isset($components[$name])) {
					$v['layout'] = $components[$name]; // replace keyword with actual array
				}
			} else {
				/*foreach ($llayout as $key => $vl) {
					if ('%' === substr($key, 0, 1) && '%' === substr($key, -1, 1)) {
						// replacing template with real array
						$name = substr($key, 1, -1);
						var_dump($name);
						if (isset($components[$name])) {
							unset($v['layout'][$key]);
							var_dump($v['layout']);
							$v['layout'] = array_merge($v['layout'], $components[$name]);
							var_dump($v['layout']);
							die;
						}
					}
				}*/
				cws_core_build_settings ($v['layout'], $components);
			}
		}
	}
}

function cws_core_get_base_components() {
	return
	array(
		'icon_options' => array(
			'icon_type' => array(
				'title' => esc_html__( 'Icon type', 'aasana' ),
				'type' => 'radio',
				'subtype' => 'images',
				'value' => array(
					'fa' => array( esc_html__( 'icon', 'aasana' ), 	true, 	'e:icon_fa;e:icon_color;e:icon_bg_type;d:icon_img', '/img/align-left.png' ),
					'img' =>array( esc_html__( 'image', 'aasana' ), false,	'd:icon_fa;d:icon_color;d:icon_bg_type;e:icon_img', '/img/align-right.png' ),
				),
			),
			'icon_fa' => array(
				'title' => esc_html__( 'Font Awesome character', 'aasana' ),
				'type' => 'select',
				'addrowclasses' => 'disable fai',
				'source' => 'fa',
			),
			'icon_img' => array(
				'title' => esc_html__( 'Custom icon', 'aasana' ),
				'addrowclasses' => 'disable',
				'type' => 'media',
			),
			'icon_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Icon color', 'aasana' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="#ffffff"',
			),
			'icon_bg_type' => array(
				'title' => esc_html__( 'Background type', 'aasana' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'none' => array( esc_html__( 'None', 'aasana' ), 	true, 	'd:icon_bg_color;d:gradient_settings;' ),
					'color' => array( esc_html__( 'Color', 'aasana' ), 	true, 	'e:icon_bg_color;d:gradient_settings;' ),
					'gradient' =>array( esc_html__( 'Gradient', 'aasana' ), false,'d:icon_bg_color;e:gradient_settings;' ),
				),
			),
			'icon_bg_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Icon background color', 'aasana' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color=""',
			),
			'gradient_settings' => array(
				'type' => 'fields',
				'addrowclasses' => 'disable groups',
				'layout' => array(							
					'c1' => array(
						'type' => 'text',
						'title' => esc_html__( 'From', 'aasana' ),
						'atts' => 'data-default-color=""',
						'addrowclasses' => 'grid-col-6',
					),
					'op1' => array(
						'type' => 'number',
						'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
						'value' => '100',
						'addrowclasses' => 'grid-col-6',
					),				
					'c2' => array(
						'type' => 'text',
						'title' => esc_html__( 'To', 'aasana' ),
						'atts' => 'data-default-color=""',
						'addrowclasses' => 'grid-col-6',
					),
					'op2' => array(
						'type' => 'number',
						'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
						'value' => '100',
						'addrowclasses' => 'grid-col-6',
					),
					'type' => array(
						'title' => esc_html__( 'Gradient type', 'aasana' ),
						'type' => 'radio',
						'addrowclasses' => 'grid-col-6',
						'value' => array(
							'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear;d:radial' ),
							'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear;e:radial' ),
						),
					),
					'linear' => array(
						'title' => esc_html__( 'Linear settings', 'aasana'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-6',
						'layout' => array(
							'angle' => array(
								'type' => 'number',
								'title' => esc_html__( 'Angle', 'aasana' ),
								'value' => '45',
							),
						)
					),
					'radial' => array(
						'title' => esc_html__( 'Radial settings', 'aasana'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-12',
						'layout' => array(
							'shape_type' => array(
								'title' => esc_html__( 'Shape', 'aasana' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-4',
								'value' => array(
									'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:keyword' ),
									'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:keyword' ),
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
							'size' => array(
								'type' => 'text',
								'addrowclasses' => 'disable grid-col-4',
								'title' => esc_html__( 'Size', 'aasana' ),
								'atts' => 'placeholder="'.esc_html__('Two space separated percent values, for example (60% 55%)', 'aasana').'"',
							),
							'keyword' => array(
								'type' => 'select',
								'title' => esc_html__( 'Size keyword', 'aasana' ),
								'addrowclasses' => 'disable grid-col-4',
								'source' => array(
									'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
									'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
									'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
									'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
								),
							),
						)
					)
				),
			),
		),
		'gradient_layout' => array(
			'c1' => array(
				'type' => 'text',
				'title' => esc_html__( 'From', 'aasana' ),
				'atts' => 'data-default-color=""',
				'addrowclasses' => 'grid-col-6',
			),
			'op1' => array(
				'type' => 'number',
				'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
				'value' => '100',
				'addrowclasses' => 'grid-col-6',
			),				
			'c2' => array(
				'type' => 'text',
				'title' => esc_html__( 'To', 'aasana' ),
				'atts' => 'data-default-color=""',
				'addrowclasses' => 'grid-col-6',
			),
			'op2' => array(
				'type' => 'number',
				'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
				'value' => '100',
				'addrowclasses' => 'grid-col-6',
			),
			'type' => array(
				'title' => esc_html__( 'Gradient type', 'aasana' ),
				'type' => 'radio',
				'addrowclasses' => 'grid-col-6',
				'value' => array(
					'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear;d:radial' ),
					'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear;e:radial' ),
				),
			),
			'linear' => array(
				'title' => esc_html__( 'Linear settings', 'aasana'  ),
				'type' => 'fields',
				'addrowclasses' => 'disable grid-col-6',
				'layout' => array(
					'angle' => array(
						'type' => 'number',
						'title' => esc_html__( 'Angle', 'aasana' ),
						'value' => '45',
					),
				)
			),
			'radial' => array(
				'title' => esc_html__( 'Radial settings', 'aasana'  ),
				'type' => 'fields',
				'addrowclasses' => 'disable grid-col-12',
				'layout' => array(
					'shape_type' => array(
						'title' => esc_html__( 'Shape', 'aasana' ),
						'type' => 'radio',
						'addrowclasses' => 'grid-col-4',
						'value' => array(
							'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:keyword' ),
							'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:keyword' ),
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
					'size' => array(
						'type' => 'text',
						'addrowclasses' => 'disable grid-col-4',
						'title' => esc_html__( 'Size', 'aasana' ),
						'atts' => 'placeholder="'.esc_html__('Two space separated percent values, for example (60% 55%)', 'aasana').'"',
					),
					'keyword' => array(
						'type' => 'select',
						'title' => esc_html__( 'Size keyword', 'aasana' ),
						'addrowclasses' => 'disable grid-col-4',
						'source' => array(
							'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
							'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
							'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
							'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
						),
					),
				)
			),
			'custom_css' => array(
				'title' => esc_html__( 'Custom CSS rules', 'aasana' ),
				'subtitle' => esc_html__( 'Enter styles', 'aasana' ),
				'atts' => 'rows="10"',
				'type' => 'textarea',
				'addrowclasses' => 'grid-col-12 full_row',
			),
		),
		'overlay_layout' => array(
			'type'	=> array(
				'title'		=> esc_html__( 'Color overlay', 'aasana' ),
				'addrowclasses' => 'grid-col-4',
				'type' => 'select',
				'source'	=> array(
					'none' => array( esc_html__( 'None', 'aasana' ),  true, 'd:opacity;d:color;d:gradient' ),
					'color' => array( esc_html__( 'Color', 'aasana' ),  false, 'e:opacity;e:color;d:gradient' ),
					'gradient' => array( esc_html__( 'Gradient', 'aasana' ), false, 'e:opacity;d:color;e:gradient' )
				),
			),
			'opacity' => array(
				'type' => 'number',
				'title' => esc_html__( 'Opacity', 'aasana' ),
				'placeholder' => esc_html__( 'In percents', 'aasana' ),
				'value' => '40',
				'addrowclasses' => 'disable grid-col-4',
			),
			'color'	=> array(
				'title'	=> esc_html__( 'Overlay color', 'aasana' ),
				'addrowclasses' => 'disable grid-col-4',
				'atts' => 'data-default-color="#f0f0f0"',
				'value' => '#f0f0f0',
				'type'	=> 'text',
			),
			'gradient' => array(
				'title' => esc_html__( 'Gradient Settings', 'aasana' ),
				'type' => 'fields',
				'addrowclasses' => 'disable grid-col-12 groups',
				'layout' => array(
					'c1' => array(
						'type' => 'text',
						'title' => esc_html__( 'From', 'aasana' ),
						'atts' => 'data-default-color=""',
						'addrowclasses' => 'grid-col-6',
					),
					'op1' => array(
						'type' => 'number',
						'title' => esc_html__( 'From (Opacity %)', 'aasana' ),
						'value' => '100',
						'addrowclasses' => 'grid-col-6',
					),				
					'c2' => array(
						'type' => 'text',
						'title' => esc_html__( 'To', 'aasana' ),
						'atts' => 'data-default-color=""',
						'addrowclasses' => 'grid-col-6',
					),
					'op2' => array(
						'type' => 'number',
						'title' => esc_html__( 'To (Opacity %)', 'aasana' ),
						'value' => '100',
						'addrowclasses' => 'grid-col-6',
					),
					'type' => array(
						'title' => esc_html__( 'Gradient type', 'aasana' ),
						'type' => 'radio',
						'addrowclasses' => 'grid-col-6',
						'value' => array(
							'linear' => array( esc_html__( 'Linear', 'aasana' ),  true, 'e:linear;d:radial' ),
							'radial' =>array( esc_html__( 'Radial', 'aasana' ), false,  'd:linear;e:radial' ),
						),
					),
					'linear' => array(
						'title' => esc_html__( 'Linear settings', 'aasana'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-6',
						'layout' => array(
							'angle' => array(
								'type' => 'number',
								'title' => esc_html__( 'Angle', 'aasana' ),
								'value' => '45',
							),
						)
					),
					'radial' => array(
						'title' => esc_html__( 'Radial settings', 'aasana'  ),
						'type' => 'fields',
						'addrowclasses' => 'disable grid-col-12',
						'layout' => array(
							'shape_type' => array(
								'title' => esc_html__( 'Shape', 'aasana' ),
								'type' => 'radio',
								'addrowclasses' => 'grid-col-4',
								'value' => array(
									'simple' => array( esc_html__( 'Simple', 'aasana' ),  true, 'e:shape;d:size;d:keyword' ),
									'extended' =>array( esc_html__( 'Extended', 'aasana' ), false, 'd:shape;e:size;e:keyword' ),
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
							'size' => array(
								'type' => 'text',
								'addrowclasses' => 'disable grid-col-4',
								'title' => esc_html__( 'Size', 'aasana' ),
								'atts' => 'placeholder="'.esc_html__('Two space separated percent values, for example (60% 55%)', 'aasana').'"',
							),
							'keyword' => array(
								'type' => 'select',
								'title' => esc_html__( 'Size keyword', 'aasana' ),
								'addrowclasses' => 'disable grid-col-4',
								'source' => array(
									'closest-side' => array(esc_html__( 'Closest side', 'aasana' ), false),
									'farthest-side' => array(esc_html__( 'Farthest side', 'aasana' ), false),
									'closest-corner' => array(esc_html__( 'Closest corner', 'aasana' ), false),
									'farthest-corner' => array(esc_html__( 'Farthest corner', 'aasana' ), true),
								),
							),
						)
					),
					'custom_css' => array(
						'title' => esc_html__( 'Custom CSS rules', 'aasana' ),
						'subtitle' => esc_html__( 'Enter styles', 'aasana' ),
						'atts' => 'rows="10"',
						'type' => 'textarea',
						'addrowclasses' => 'grid-col-12 full_row',
					),
				),
			),
		),
		'border_layout' => array(
			'border_box'	=> array(
				'title'		=> esc_html__( 'Border', 'aasana' ),
				'addrowclasses' => 'grid-col-3',
				'type'	=> 'select',
				'atts' => 'multiple data-none="d:width;d:type;d:color;d:line;"',
				'source'	=> array(
					'top' => array( esc_html__( 'Top', 'aasana' ),  false, 'e:width;e:type;e:color;e:line;' ),
					'bottom' => array( esc_html__( 'Bottom', 'aasana' ), false, 'e:width;e:type;e:color;e:line;' ),
				),
			),
			'width' => array(
				'title' => esc_html__( 'Width', 'aasana' ),
				'addrowclasses' => 'disable grid-col-3',
				'type' => 'number',
				'atts' => ' min="1" step="1"',
				'value' => '2'
			),		
			'type'	=> array(
				'title'		=> esc_html__( 'Type', 'aasana' ),
				'addrowclasses' => 'disable grid-col-3',
				'type'	=> 'select',
				'source'	=> array(
					'dotted' => array( esc_html__( 'Dotted', 'aasana' ),  false, '' ),
					'dashed' => array( esc_html__( 'Dashed', 'aasana' ),  false, '' ),
					'solid' => array( esc_html__( 'Solid', 'aasana' ), true, '' ),
				),
			),
			'color'	=> array(
				'title'	=> esc_html__( 'Color', 'aasana' ),
				'atts' => 'data-default-color="#f2f2f2"',
				'addrowclasses' => 'disable grid-col-3',
				'value' => '#f2f2f2',
				'type'	=> 'text',
			),
			'line' => array(
				'type' => 'checkbox',
				'title' => esc_html__('Add Extra line', 'aasana' ),
				'addrowclasses' => 'disable grid-col-12',
			),		
		),
		'parallax_layout' => array(
			'scalar-x' => array(
				'type' => 'number',
				'title' => esc_html__( 'x-axis parallax intensity', 'aasana' ),
				'placeholder' => esc_html__( 'Integer', 'aasana' ),
				'addrowclasses' => 'grid-col-6',
				'value' => '2'
			),
			'scalar-y' => array(
				'type' => 'number',
				'title' => esc_html__( 'y-axis parallax intensity', 'aasana' ),
				'placeholder' => esc_html__( 'Integer', 'aasana' ),
				'addrowclasses' => 'grid-col-6',
				'value' => '2'
			),
			'limit-x' => array(
				'type' => 'number',
				'title' => esc_html__( 'Maximum x-axis shift', 'aasana' ),
				'placeholder' => esc_html__( 'Integer', 'aasana' ),
				'addrowclasses' => 'grid-col-6',
				'value' => '15'
			),
			'limit-y' => array(
				'type' => 'number',
				'title' => esc_html__( 'Maximum y-axis shift', 'aasana' ),
				'placeholder' => esc_html__( 'Integer', 'aasana' ),
				'addrowclasses' => 'grid-col-6',
				'value' => '15'
			),
		),
		'image_layout' => array(
			'image' => array(
				'title' => esc_html__( 'Image', 'aasana' ),
				'addrowclasses' => 'grid-col-2_5',
				'type' => 'media',
			),
			'size' => array(
				'title' => esc_html__( 'Size', 'aasana' ),
				'addrowclasses' => 'grid-col-2_5',
				'type' => 'radio',
				'value' => array(
					'initial' =>array( esc_html__( 'Initial', 'aasana' ), true,  '' ),
					'cover' => array( esc_html__( 'Cover', 'aasana' ),  false, '' ),
					'contain' =>array( esc_html__( 'Contain', 'aasana' ), false,  '' ),
				),
			),
			'repeat' => array(
				'title' => esc_html__( 'Repeat', 'aasana' ),
				'addrowclasses' => 'grid-col-2_5',
				'type' => 'radio',
				'value' => array(
					'no-repeat' => array( esc_html__( 'No repeat', 'aasana' ),  false, '' ),
					'repeat' => array( esc_html__( 'Tile', 'aasana' ),  true, '' ),
					'repeat-x' => array( esc_html__( 'Tile Horizontally', 'aasana' ),  false, '' ),
					'repeat-y' =>array( esc_html__( 'Tile Vertically', 'aasana' ), false,  '' ),
				),
			),
			'attachment' => array(
				'title' => esc_html__( 'Attachment', 'aasana' ),
				'addrowclasses' => 'grid-col-2_5',
				'type' => 'radio',
				'value' => array(
					'scroll' => array( esc_html__( 'Scroll', 'aasana' ),  true, '' ),
					'fixed' =>array( esc_html__( 'Fixed', 'aasana' ), false, '' ),
					'local' =>array( esc_html__( 'Local', 'aasana' ), false, '' ),
				),
			),
			'position' => array(
				'title' => esc_html__( 'Position', 'aasana' ),
				'addrowclasses' => 'grid-col-2_5',
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


	);
}
?>
