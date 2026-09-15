<?php
	global $aasana_theme_funcs;
	$def_chars_count = $aasana_theme_funcs->cws_get_option( 'def_blog_chars_count' );
	$def_chars_count = isset( $def_chars_count ) && is_numeric( $def_chars_count ) ? $def_chars_count : '';
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
			'type'			=> 'dropdown',
			'heading'		=> esc_html__( 'Columns', 'aasana' ),
			'param_name'	=> 'layout',
			'save_always'	=> true,
			'value'			=> array(
				esc_html__( 'Default', 'aasana' ) => 'def',
				esc_html__( 'Large', 'aasana' ) => '1',
				esc_html__( 'Small', 'aasana' ) => 'small',
				esc_html__( 'Two Columns', 'aasana' ) => '2',
				esc_html__( 'Three Columns', 'aasana' ) => '3',
				esc_html__( 'Four Columns', 'aasana' ) => '4'
			)
		),
	);

	$taxes = get_object_taxonomies ( 'tribe_events', 'object' );
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
	$params2 = array(
		array(
			"type"			=> "checkbox",
			"param_name"	=> "customize_colors",
			"value"			=> array( esc_html__( 'Customize Colors', 'aasana' ) => true )				
		),		
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Custom Color', 'aasana' ),
			"param_name"	=> "custom_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> AASANA_COLOR
		),				
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Background Color', 'aasana' ),
			"param_name"	=> "bg_color",
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> "#fff"
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "add_shadow",
			"value"			=> array( esc_html__( 'Add Shadow', 'aasana' ) => true )				
		),	
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'tribe_events_hide_meta_override',
			'value'			=> array(
				esc_html__( 'Hide Meta Data', 'aasana' ) => true
			)
		),
		array(
			'type'			=> 'cws_dropdown',
			'multiple'		=> "true",
			'heading'		=> esc_html__( 'Hide', 'aasana' ),
			'param_name'	=> 'tribe_events_hide_meta',
			'dependency'	=> array(
					'element'	=> 'tribe_events_hide_meta_override',
					'not_empty'	=> true
			),
			'value'			=> array(
				esc_html__( 'None', 'aasana' )			=> '',
				esc_html__( 'Title', 'aasana' )		=> 'title',
				esc_html__( 'Date', 'aasana' )	=> 'date',
				esc_html__( 'Excerpt', 'aasana' )	=> 'excerpt',
				esc_html__( 'Time Events', 'aasana' )		=> 'time_events',
				esc_html__( 'Venue Events', 'aasana' )		=> 'venue_events',	
			)
		),	
		array(
			'type'			=> 'textfield',
			'heading'		=> esc_html__( 'Content Character Limit', 'aasana' ),
			'param_name'	=> 'chars_count',
			'dependency'	=> array(
					'element'	=> 'tribe_events_hide_meta_override',
					'not_empty'	=> true
			),
			'value'			=> 	$def_chars_count	
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
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Pagination', 'aasana' ),
			"param_name"	=> "pagination_grid",
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "grid", "filter" )
							),
			"value"			=> array(
				esc_html__( "Standard", 'aasana' ) 	=> 'standard',
				esc_html__( "Standard With Ajax", 'aasana' ) 	=> 'standard_with_ajax',
				esc_html__( "Load More", 'aasana' )	=> 'load_more',
			)		
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
		"name"				=> esc_html__( 'CWS Events Grid', 'aasana' ),
		"base"				=> "cws_sc_events_posts_grid",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));
	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Events_Posts_Grid extends WPBakeryShortCode {
	    }
	}
?>