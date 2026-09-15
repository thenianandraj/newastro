<?php
	global $aasana_theme_funcs;
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
								/*esc_html__( 'Grid with Filter', 'aasana' ) => 'filter',*/
								esc_html__( 'Carousel', 'aasana' ) => 'carousel'
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
				esc_html__( 'Three Columns', 'aasana' ) => '3'
			)
		),
	);


	$taxes = get_object_taxonomies ( 'cws_testimonial', 'object' );
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
				esc_html__( 'Add Navigation Arrows', 'aasana' ) => true
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
			"type"			=> "checkbox",
			"param_name"	=> "customize_colors",
			"dependency" 	=> array(
				"element"	=> "display_style",
				"value"		=> array( "carousel" )
				),
			"value"			=> array( esc_html__( 'Customize Colors Carousel', 'aasana' ) => true )
			),
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Navigation Color', 'aasana' ),
			"param_name"	=> "custom_arrow_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
				),
			"value"			=> ""
			),
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Pagination Color', 'aasana' ),
			"param_name"	=> "custom_pagination_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
				),
			"value"			=> ""
			),

		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'hide_data_override',
			'value'			=> array(
				esc_html__( 'Hide Meta Data', 'aasana' ) => true
			)
		),
		array(
			'type'				=> 'cws_dropdown',
			'multiple'			=> "true",
			'heading'			=> esc_html__( 'Hide', 'aasana' ),
			'param_name'		=> 'data_to_hide',
			'dependency'		=> array(
				'element'			=> 'hide_data_override',
				'not_empty'			=> true
			),
			'value'				=> array(
				esc_html__( 'None', 'aasana' )			=> '',
				esc_html__( 'Positions', 'aasana' )	=> 'poss',
				esc_html__( 'Background', 'aasana' )	=> 'background',
				esc_html__( 'Raiting', 'aasana' )		=> 'raiting',
				esc_html__( 'Excerpt', 'aasana' )		=> 'excerpt',
			)
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'change_title',
			'value'			=> array(
				esc_html__( 'Change Details Button', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Title Button', 'aasana' ),
			"param_name"	=> "title_btn",
			'dependency'		=> array(
				'element'			=> 'change_title',
				'not_empty'			=> true
			),
			"value"			=> esc_html__( 'Read More', 'aasana' )
		),
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
		"name"				=> esc_html__( 'CWS Testimonials Grid', 'aasana' ),
		"base"				=> "cws_sc_testimonial_posts",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));
	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Testimonials_Posts extends WPBakeryShortCode {
	    }
	}
?>