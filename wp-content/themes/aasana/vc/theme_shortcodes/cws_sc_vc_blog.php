<?php
	global $aasana_theme_funcs;
	$post_type = "post";
	$post_type_obj = get_post_type_object( $post_type );
	$post_type_name = isset( $post_type_obj->labels->name ) && !empty( $post_type_obj->labels->name ) ? $post_type_obj->labels->name : $post_type;
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
			"heading"		=> esc_html__( 'Blog View', 'aasana' ),
			"param_name"	=> "display_style",
			"value"			=> array(
				esc_html__( 'Grid', 'aasana' ) => 'grid',
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
	);
	$taxes = get_object_taxonomies ( $post_type, 'object' );
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
		"param_name"		=> $post_type . "_tax",
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
			"param_name"	=> "{$post_type}_{$tax}_terms",
			"dependency"	=> array(
				"element"	=> $post_type . "_tax",
				"value"		=> $tax
				),
			"value"			=> $avail_terms
			));				
	}
	$params2 = array(
		array(
			'type'			=> 'dropdown',
			'heading'		=> esc_html__( 'Layout', 'aasana' ),
			'param_name'	=> 'layout',
			'save_always'	=> true,
			'value'			=> array(
				esc_html__( 'Default', 'aasana' ) => 'def',
				esc_html__( 'Blog Large', 'aasana' ) => '1',
				esc_html__( 'Blog Medium', 'aasana' ) => 'medium',
				esc_html__( 'Blog Small', 'aasana' ) => 'small',
				esc_html__( 'Two Columns', 'aasana' ) => '2',
				esc_html__( 'Three Columns', 'aasana' ) => '3',
				esc_html__( 'Four Columns', 'aasana' ) => '4',
				esc_html__( 'Checkerboard', 'aasana' ) => 'checkerboard',
				esc_html__( 'Full Width Background', 'aasana' ) => 'fw_img',
			)
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Hover Effects', 'aasana' ),
			"param_name"	=> "hover_effects",
			"value"			=> array(
				esc_html__( "None", 'aasana' ) 	=> 'none',
				esc_html__( "One", 'aasana' ) 	=> '01',
				esc_html__( "Two", 'aasana' )	=> '02',
				esc_html__( "Three", 'aasana' )	=> '03',
				esc_html__( "Four", 'aasana' )	=> '04',
				esc_html__( "Five", 'aasana' )	=> '05',
				esc_html__( "Six", 'aasana' )	=> '06',
				esc_html__( "Seven", 'aasana' )	=> '07',
				esc_html__( "Eight", 'aasana' )	=> '08',
				esc_html__( "Nine", 'aasana' )	=> '09',
				esc_html__( "Ten", 'aasana' )	=> '10',
				esc_html__( "Eleven", 'aasana' )	=> '11',
				esc_html__( "Twelve", 'aasana' )	=> '12',
				esc_html__( "Flashing", 'aasana' )	=> '13',
				esc_html__( "Shine", 'aasana' )	=> '14',
				esc_html__( "Circle", 'aasana' )	=> '15',
			)		
		),		
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'More Button Hover Effects', 'aasana' ),
			"param_name"	=> "hover_effects_more_btn",
			"value"			=> array(
				esc_html__( "Style 1", 'aasana' ) 	=> 'style_1',
				esc_html__( "Style 2", 'aasana' ) 	=> 'style_2',
			)		
		),
		array(
			'type'				=> 'cws_dropdown',
			'multiple'			=> "true",
			'heading'			=> esc_html__( 'Show', 'aasana' ),
			'param_name'		=> 'link_show',
			'value'				=> array(
				esc_html__( 'Make Image Clickable', 'aasana' )	=> 'none',
				esc_html__( 'Show Image PoPup Icon', 'aasana' )	=> 'single_link',
				esc_html__( 'Show Project Details Icon', 'aasana' )	=> 'popup_link'
			)
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> $post_type . '_hide_meta_override',
			'value'			=> array(
				esc_html__( 'Hide Meta Data', 'aasana' ) => true
			)
		),	
		array(
			'type'			=> 'cws_dropdown',
			'multiple'		=> "true",
			'heading'		=> esc_html__( 'Hide', 'aasana' ),
			'param_name'	=> $post_type . '_hide_meta',
			'dependency'	=> array(
					'element'	=> $post_type . '_hide_meta_override',
					'not_empty'	=> true
			),
			'value'			=> array(
				esc_html__( 'None', 'aasana' )			=> '',
				esc_html__( 'Title', 'aasana' )		=> 'title',
				esc_html__( 'Categories', 'aasana' )	=> 'cats',
				esc_html__( 'Tags', 'aasana' )			=> 'tags',
				esc_html__( 'Author', 'aasana' )		=> 'author',
				esc_html__( 'Likes', 'aasana' )		=> 'likes',
				esc_html__( 'Date', 'aasana' )			=> 'date',
				esc_html__( 'Comments', 'aasana' )		=> 'comments',
				esc_html__( 'Read More', 'aasana' )	=> 'read_more',
				esc_html__( 'Social Icons', 'aasana' )	=> 'social',
				esc_html__( 'Excerpt', 'aasana' )	=> 'excerpt',		
			)
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
								"value"		=> array( "grid" )
							),
			"value"			=> esc_html( get_option( 'posts_per_page' ) )
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Pagination', 'aasana' ),
			"param_name"	=> 'pagination_grid',
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "grid" )
							),
			"value"			=> array(
				esc_html__( "Standard", 'aasana' ) 	=> 'standard',
				esc_html__( "Load More", 'aasana' )	=> 'load_more',
				esc_html__( "Standard With Ajax", 'aasana' )	=> 'standard_with_ajax',
			)		
		),	
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Aligning', 'aasana' ),
			"param_name"	=> "aligning",
			"dependency"	=> array(
					"element"	=> "pagination_grid",
					"value"		=> "load_more"
			),	
			"value"			=> array(
				esc_html__( 'Center', 'aasana' )		=> 'center',
				esc_html__( 'Left', 'aasana' )		=> 'left',
				esc_html__( 'Right', 'aasana' )		=> 'right'
			),
		),			

	);

	$params = array_merge($params, $params2);

	$def_chars_count = $aasana_theme_funcs->cws_get_option( 'def_blog_chars_count' );
	$def_chars_count = isset( $def_chars_count ) && is_numeric( $def_chars_count ) ? $def_chars_count : '';
	array_push( $params, array(
		'type'			=> 'textfield',
		'heading'		=> esc_html__( 'Content Character Limit', 'aasana' ),
		'param_name'	=> 'chars_count',
		'dependency'	=> array(
				'element'	=> $post_type . '_hide_meta_override',
				'not_empty'	=> true
		),
		'value'			=> 	$def_chars_count	
	));
	array_push( $params, array(
		"type"				=> "textfield",
		"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
		"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
		"param_name"		=> "el_class",
		"value"				=> ""
	));

	vc_map( array(
		"name"				=> esc_html__( 'CWS Blog', 'aasana' ),
		"base"				=> "cws_sc_vc_blog",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));

if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_CWS_Sc_Vc_Blog extends WPBakeryShortCode {
    }
}

?>