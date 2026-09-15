<?php
	global $aasana_theme_funcs;
	$theme_color  = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	$body_font_options = $aasana_theme_funcs->cws_get_option( 'body-font' );
	$body_font_color = esc_attr( $body_font_options['color'] );
	$params = array(
		array(
			"type"			=> "textfield",
			"admin_label"	=> true,
			"heading"		=> esc_html__( 'Title', 'aasana' ),
			"param_name"	=> "title",
			"value"			=> ""
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Title Alignment', 'aasana' ),
			"param_name"	=> "title_align",
			"value"			=> array(
				esc_html__( "Left", 'aasana' ) 	=> 'left',
				esc_html__( "Right", 'aasana' )	=> 'right',
				esc_html__( "Center", 'aasana' )	=> 'center'
			)		
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Layout', 'aasana' ),
			"param_name"	=> "display_style",
			"value"			=> array(
								esc_html__( 'Grid', 'aasana' ) => 'grid',
								esc_html__( 'Grid with Filter', 'aasana' ) => 'filter',
								esc_html__( 'Carousel', 'aasana' ) => 'carousel'
							)
		),
			array(
			'type'			=> 'checkbox',
			'param_name'	=> 'auto_play_carousel',
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "carousel" )
							),
			'value'			=> array(
				esc_html__( 'AutoPlay Carousel', 'aasana' ) => true
			)
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'navigation_carousel',
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "carousel" )
							),
			'value'			=> array(
				esc_html__( 'Add Navigation Controls', 'aasana' ) => true
			)
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'pagination_carousel',
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "carousel" )
							),
			'value'			=> array(
				esc_html__( 'Add Navigation Bullets', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Columns', 'aasana' ),
			"param_name"	=> "layout",
			"value"			=> array(
				esc_html__( 'Default', 'aasana' ) => 'def',
				esc_html__( 'One Column', 'aasana' ) => '1',
				esc_html__( 'Two Columns', 'aasana' ) => '2',
				esc_html__( 'Three Columns', 'aasana' ) => '3',
				esc_html__( 'Four Columns', 'aasana' ) => '4',
			)
		),
	);
		$taxes = get_object_taxonomies ( 'cws_staff', 'object' );
		$avail_taxes = array(
			esc_html__( 'None', 'aasana' )	=> ''
		);
		foreach ( $taxes as $tax => $tax_obj ){
			$tax_name = isset( $tax_obj->labels->name ) && !empty( $tax_obj->labels->name ) ? $tax_obj->labels->name : $tax;
			$avail_taxes[$tax_name] = $tax;
		}
		array_push( $params, array(
			"type"				=> "dropdown",
			"heading"			=> esc_html__( 'Filter by', 'aasana' ),
			"param_name"		=> "tax",
			"value"				=> $avail_taxes
		));
		foreach ( $avail_taxes as $tax_name => $tax ) {
			$terms = get_terms( $tax );
			$avail_terms = array(
				''				=> ''
			);
			if ( !is_a( $terms, 'WP_Error' ) ){
				foreach ( $terms as $term ) {
					$avail_terms[$term->name] = $term->slug;
				}
			}
			array_push( $params, array(
				"type"			=> "cws_dropdown",
				"multiple"		=> "true",
				"heading"		=> $tax_name,
				"param_name"	=> "{$tax}_terms",
				"dependency"	=> array(
									"element"	=> "tax",
									"value"		=> $tax
								),
				"value"			=> $avail_terms
			));				
		}
	$params2 = array(
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Items to display', 'aasana' ),
			"param_name"	=> "total_items_count",
			"value"			=> esc_html( get_option( 'posts_per_page' ) )
		),
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Items per Page', 'aasana' ),
			"param_name"	=> "items_pp",
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "grid", "filter" )
							),
			"value"			=> esc_html( get_option( 'posts_per_page' ) )
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'change_btn',
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Edit Read More Title', 'aasana' ) => true
			)
		),		
		array(
			"type"			=> "textfield",
			"param_name"	=> "title_btn",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency" 	=> array(
								"element"	=> "change_btn",
								"not_empty"	=> true
							),
			"value"			=> esc_html__( 'Read More', 'aasana' ),
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'customize_colors',
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Customize Colors', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Title Color', 'aasana' ),
			"param_name"	=> "custom_title_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> ""
		),
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Font Color', 'aasana' ),
			"param_name"	=> "custom_font_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> ""
		),		
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Social Color', 'aasana' ),
			"param_name"	=> "custom_social_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> ""
		),		
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Button Color', 'aasana' ),
			"param_name"	=> "custom_btn_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> ""
		),
		array(
			"type" => "dropdown",
			"heading" => __("Image Hover", 'aasana'),
			"param_name"		=> "bg_hover_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value" => array(
				__("None", 'aasana') => "none",
				__("Color", 'aasana') => "color",
				__("Gradient", 'aasana') => "gradient",
			),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading"		=> esc_html__( 'Color', 'aasana' ),
			"param_name" => "hover_fill_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'color',

			),
			"value"			=> $theme_color
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading"		=> esc_html__( 'From', 'aasana' ),
			"param_name" => "cws_gradient_color_from",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'gradient',

				),
			"value"			=> $theme_color
		),					
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading"		=> esc_html__( 'To', 'aasana' ),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"param_name" => "cws_gradient_color_to",
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'gradient',

				),
			"value"			=> $theme_color
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Type", 'aasana'),
			"param_name"		=> "cws_gradient_type",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value" => array(
				__("Linear", 'aasana') => "linear",
				__("Radial", 'aasana') => "radial",
			),
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'gradient',

			),
		),
		array(
			"type"			=> "textfield",
			"class" => "",
			"heading"		=> esc_html__( 'Angle', 'aasana' ),
			"param_name"	=> "cws_gradient_angle",
			"value" => '360',
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"description"	=> esc_html__( 'Degrees: -360 to 360', 'aasana' ),
			"dependency"	=> array(
				"element"	=> "cws_gradient_type",
				'value' => 'linear',						
				),
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Shape variant", 'aasana'),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"param_name"		=> "cws_gradient_shape_variant_type",
			"value" => array(
				__("Simple", 'aasana') => "simple",
				__("Extended", 'aasana') => "extended",
				),
			"dependency"	=> array(
				"element"	=> "cws_gradient_type",
				'value' => 'radial',	
				),
		),					
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Shape", 'aasana'),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"param_name"		=> "cws_gradient_shape_type",
			"value" => array(
				__("Ellipse", 'aasana') => "ellipse",
				__("Circle", 'aasana') => "circle",
				),
			"dependency"	=> array(
				"element"	=> "cws_gradient_shape_variant_type",
				'value' => 'simple',	
				),
		),						
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Size keyword", 'aasana'),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"param_name"		=> "cws_gradient_size_keyword_type",
			"value" => array(
				__("Closest side", 'aasana') => "closest_side",
				__("Farthest side", 'aasana') => "farthest_side",
				__("Closest corner", 'aasana') => "closest_corner",
				__("Farthest corner", 'aasana') => "farthest_corner",
				),
			"dependency"	=> array(
				"element"	=> "cws_gradient_shape_variant_type",
				'value' => 'extended',	
				),
		),						
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => __("Size", 'aasana'),
			"param_name"		=> "cws_gradient_size_type",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value" => '60% 55%',
			"description"	=> esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ),
			"dependency"	=> array(
				"element"	=> "cws_gradient_shape_variant_type",
				'value' => 'extended',	
				),
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'hide_data_override',
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Hide Meta Data', 'aasana' ) => true
			)
		),
		array(
			'type'				=> 'cws_dropdown',
			'multiple'			=> "true",
			'heading'			=> esc_html__( 'Hide', 'aasana' ),
			'param_name'		=> 'data_to_hide',
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'dependency'		=> array(
				'element'			=> 'hide_data_override',
				'not_empty'			=> true
			),
			'value'				=> array(
				esc_html__( 'None', 'aasana' )			=> '',
				esc_html__( 'Departments', 'aasana' ) 	=> 'deps',
				esc_html__( 'Positions', 'aasana' )	=> 'poss',
				esc_html__( 'Excerpt', 'aasana' )		=> 'excerpt',
				esc_html__( 'Social Links', 'aasana' )	=> 'socials',
				esc_html__( 'Link Button', 'aasana' )	=> 'link_button',
				esc_html__( 'Email', 'aasana' )			=> 'email',
				esc_html__( 'Experience', 'aasana' )	=> 'experience',
				esc_html__( 'Biography', 'aasana' )		=> 'biography',
			)
		)				
	);
	$params = array_merge($params, $params2);
	array_push( $params, array(
		"type"				=> "textfield",
		"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
		"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
		"param_name"		=> "el_class",
		"value"				=> ""
	));
	vc_map( array(
		"name"				=> esc_html__( 'CWS Staff', 'aasana' ),
		"base"				=> "cws_sc_staff_posts_grid",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));
	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Staff_Posts_Grid extends WPBakeryShortCode {
	    }
	}
?>