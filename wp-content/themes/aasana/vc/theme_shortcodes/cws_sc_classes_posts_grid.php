<?php
	global $aasana_theme_funcs;
	$def_chars_count = $aasana_theme_funcs->cws_get_option( 'def_blog_chars_count' );
	$def_chars_count = isset( $def_chars_count ) && is_numeric( $def_chars_count ) ? $def_chars_count : '';
	$post_type = "cws_classes";
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
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Class Variant', 'aasana' ),
			"param_name"	=> "display_screen",
			"value"			=> array(
								esc_html__( 'Coach Avatar Bellow Content', 'aasana' ) => 'style_1',
								esc_html__( 'Coach Avatar Above Content', 'aasana' ) => 'style_2',
								esc_html__( 'Small Coach Avatar Bellow Content + Date', 'aasana' ) => 'style_3'
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
				esc_html__( 'Four Columns', 'aasana' ) => '4'
			)
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'crop_images',
			'value'			=> array(
				esc_html__( 'Crop Images', 'aasana' ) => true
			)
		),
	);
	$taxes = get_object_taxonomies ( 'cws_classes', 'object' );
	$avail_taxes = array(
		esc_html__( 'None', 'aasana' )	=> '',
		esc_html__( 'Staff', 'aasana' )	=> 'staff',
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
		if ($tax == 'staff'){
			$custom_post_type = 'cws_staff';
			global $wpdb;
    		$results = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type LIKE %s and post_status = 'publish'", $custom_post_type ) );
    		$titles_arr = array();
		    foreach( $results as $index => $post ) {
		    	$post_title = $post->post_title;
		        $titles_arr[$post_title] =  $post->ID;
		    }
			array_push( $params, array(
				"type"			=> "cws_dropdown",
				"multiple"		=> "true",
				"heading"		=> esc_html__( 'Titles', 'aasana' ),
				"param_name"	=> "titles",
				"dependency"	=> array(
									"element"	=> "tax",
									"value"		=> 'staff'
								),
				"value"			=> $titles_arr
			));		
		} else {
			$terms = get_terms( $tax );
			$avail_terms = array(
				''				=> ''
			);
			if ( !is_a( $terms, 'WP_Error' ) ){
				foreach ( $terms as $term ) {
					$avail_terms[$term->name] = $term->slug;
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
		}			
	}
		array_push( $params, array(
		
			"type"			=> "checkbox",
			"param_name"	=> "customize_colors",
			"value"			=> array( esc_html__( 'Customize Colors', 'aasana' ) => true )				
		)	
	);
	array_push( $params, array(	
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Title and More Button Color', 'aasana' ),
			"param_name"	=> "custom_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> $theme_color
		)
	);
	array_push( $params, array(			
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Fill Color', 'aasana' ),
			"param_name"	=> "bg_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> AASANA_COLOR
		)
	);		
	array_push( $params, array(			
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Image Hover', 'aasana' ),
			"param_name"	=> "hover_bg_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> ""
		)
	);		

		array_push( $params, array(	
			"type" => "dropdown",
			"heading" => __("Avatar Border", 'aasana'),
			"param_name"		=> "bg_hover_color",
			"value" => array(
				__("None", 'aasana') => "none",
				__("Color", 'aasana') => "color",
				__("Gradient", 'aasana') => "gradient",
			),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
		)
	);
	array_push( $params, array(	
			"type" => "colorpicker",
			"class" => "",
			"heading"		=> esc_html__( 'Color', 'aasana' ),
			"param_name" => "hover_fill_color",
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'color',

			),
			"value"			=> $theme_color
		)
	);
	array_push( $params, array(	
			"type" => "colorpicker",
			"class" => "",
			"heading"		=> esc_html__( 'From', 'aasana' ),
			"param_name" => "cws_gradient_color_from",
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'gradient',

				),
			"value"			=> $theme_color
		)
	);					
	array_push( $params, array(	
			"type" => "colorpicker",
			"class" => "",
			"heading"		=> esc_html__( 'To', 'aasana' ),
			"param_name" => "cws_gradient_color_to",
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'gradient',

				),
			"value"			=> $theme_color
		)
	);
	array_push( $params, array(	
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Type", 'aasana'),
			"param_name"		=> "cws_gradient_type",
			"value" => array(
				__("Linear", 'aasana') => "linear",
				__("Radial", 'aasana') => "radial",
			),
			"dependency"	=> array(
				"element"	=> "bg_hover_color",
				'value' => 'gradient',

			),
		)
	);
	array_push( $params, array(	
			"type"			=> "textfield",
			"class" => "",
			"heading"		=> esc_html__( 'Angle', 'aasana' ),
			"param_name"	=> "cws_gradient_angle",
			"value" => '360',
			"description"	=> esc_html__( 'Degrees: -360 to 360', 'aasana' ),
			"dependency"	=> array(
				"element"	=> "cws_gradient_type",
				'value' => 'linear',						
				),
		)
	);
	array_push( $params, array(	
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Shape variant", 'aasana'),
			"param_name"		=> "cws_gradient_shape_variant_type",
			"value" => array(
				__("Simple", 'aasana') => "simple",
				__("Extended", 'aasana') => "extended",
				),
			"dependency"	=> array(
				"element"	=> "cws_gradient_type",
				'value' => 'radial',	
				),
		)
	);					
	array_push( $params, array(	
			"type" => "dropdown",
			"class" => "",
			"heading" => __("Shape", 'aasana'),
			"param_name"		=> "cws_gradient_shape_type",
			"value" => array(
				__("Ellipse", 'aasana') => "ellipse",
				__("Circle", 'aasana') => "circle",
				),
			"dependency"	=> array(
				"element"	=> "cws_gradient_shape_variant_type",
				'value' => 'simple',	
				),
		)
	);						
	array_push( $params, array(	
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
			"dependency"	=> array(
				"element"	=> "cws_gradient_shape_variant_type",
				'value' => 'extended',	
				),
		)
	);						
	array_push( $params, array(	
			"type" => "textfield",
			"class" => "",
			"heading" => __("Size", 'aasana'),
			"param_name"		=> "cws_gradient_size_type",
			"value" => '60% 55%',
			"description"	=> esc_html__( 'Two space separated percent values, for example (60% 55%)', 'aasana' ),
			"dependency"	=> array(
				"element"	=> "cws_gradient_shape_variant_type",
				'value' => 'extended',	
				),
		)
	);
	$params2 = array(	
		array(
			'type'			=> 'checkbox',
			'param_name'	=> $post_type . '_hide_meta_override',
			'value'			=> array(
				esc_html__( 'Hide Meta Data', 'aasana' ) => true
			)
		),		
		array(
			'type'				=> 'cws_dropdown',
			'multiple'			=> "true",
			'heading'			=> esc_html__( 'Hide', 'aasana' ),
			'param_name'		=> $post_type . '_hide_meta',
			'dependency'		=> array(
				'element'			=> $post_type . '_hide_meta_override',
				'not_empty'			=> true
			),
			'value'				=> array(
				esc_html__( 'None', 'aasana' )		=> 'none',
				esc_html__( 'Title', 'aasana' )		=> 'title',
				esc_html__( 'Excerpt', 'aasana' )		=> 'excerpt',
				esc_html__( 'Categories', 'aasana' )	=> 'cats',
				esc_html__( 'Teacher', 'aasana' )	=> 'teach',
				esc_html__( 'Working Days', 'aasana' )	=> 'working_days',
				esc_html__( 'Time Events', 'aasana' )	=> 'time_events',
				esc_html__( 'Venue Events', 'aasana' )	=> 'venue_events',
				esc_html__( 'Read More Button', 'aasana' )	=> 'read_more',
			)
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'change_btn',
			'value'			=> array(
				esc_html__( 'Change Details Button', 'aasana' ) => true
			),
		),		
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Title Button', 'aasana' ),
			"param_name"	=> "title_btn",
			"dependency" 	=> array(
								"element"	=> "change_btn",
								"not_empty"	=> true
							),
			"value"			=> esc_html__( 'Book Class', 'aasana' ),
		),
	);
	$params = array_merge($params, $params2);
	array_push( $params, array(
		'type'			=> 'textfield',
		'heading'		=> esc_html__( 'Content Character Limit', 'aasana' ),
		'param_name'	=> 'chars_count',
		'dependency'	=> array(
				'element'	=> 'show_data_override',
				'not_empty'	=> true
		),
		'value'			=> 	$def_chars_count	
	));	
	array_push( $params, array(
		
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Pagination', 'aasana' ),
			"param_name"	=> "pagination_grid",
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "grid", "filter" )
							),
			"value"			=> array(
				esc_html__( "None", 'aasana' ) 	=> 'none',
				esc_html__( "Standard", 'aasana' ) 	=> 'standard',
				esc_html__( "Load More", 'aasana' )	=> 'load_more',
			)		
		)
	);
	array_push( $params, array(	
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Items to display', 'aasana' ),
			"param_name"	=> "total_items_count",
			"value"			=> esc_html( get_option( 'posts_per_page' ) )
		)
	);	
	array_push( $params, array(	
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Items per Page', 'aasana' ),
			"param_name"	=> "items_pp",
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "grid", "filter" )
							),
			"value"			=> esc_html( get_option( 'posts_per_page' ) )
		)
	);				
	array_push( $params, array(
		"type"				=> "textfield",
		"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
		"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
		"param_name"		=> "el_class",
		"value"				=> ""
	));
	vc_map( array(
		"name"				=> esc_html__( 'CWS Classes', 'aasana' ),
		"base"				=> "cws_sc_classes_posts_grid",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));
	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Classes_Posts_Grid extends WPBakeryShortCode {
	    }
	}
?>