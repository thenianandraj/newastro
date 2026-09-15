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
								esc_html__( 'Grid with Filter', 'aasana' ) => 'filter',
								esc_html__( 'Grid with Filter(Ajax)', 'aasana' ) => 'filter_with_ajax',
								esc_html__( 'Carousel', 'aasana' ) => 'carousel',
								esc_html__( 'Showcase', 'aasana' ) => 'showcase',
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
				esc_html__( 'Five Columns', 'aasana' ) => '5'
			)
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "crop_images",
			"dependency" 	=> array(
								"element"	=> 'layout',
								"value"		=> array( "def","2","3","4","5" )
							),
			'value'			=> array(
				esc_html__( 'Crop images', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "masonry",
			"dependency" 	=> array(
								"element"	=> 'crop_images',
								'not_empty'	=> true
							),
			'value'			=> array(
				esc_html__( 'Masonry', 'aasana' ) => true
			)
		),
	);
	$taxes = get_object_taxonomies ( 'cws_portfolio', 'object' );
	$avail_taxes = array(
		esc_html__( 'None', 'aasana' )	=> '',
		esc_html__( 'Titles', 'aasana' )	=> 'title',
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
		if ($tax == 'title'){
			$custom_post_type = 'cws_portfolio';
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
									"value"		=> 'title'
								),
				"value"			=> $titles_arr
			));		
		} else {
			$terms = get_terms( $tax );
			$avail_terms =  array(
				''				=> ''
			);
			$hierarchy = _get_term_hierarchy($tax);
			if ( !is_a( $terms, 'WP_Error' ) ){
				foreach($terms as $term) {
					if(isset($term)){
						if($term->parent) {
							continue;
						} 			
						$avail_terms[] = $term->name;  
						if(isset($hierarchy[$term->term_id])) {	
							$children = _get_term_children($term->term_id, $terms, $tax);										
							foreach($children as $child) {
								$child = get_term($child, $tax);
								$ancestors = get_ancestors( $child->term_id, $child->taxonomy );
								$depth = $ancestors = count($ancestors);
								if($child->count > 0){
									if($depth <= $ancestors){							
										$avail_terms[] =  str_repeat("-", $depth) . ' ('.$term->name.') '.$child->slug;
									}
								}
							}
						}					
					}				
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
				"value"			=> $avail_terms, 
			));	
		} 		
	}
	$params2 = array(
		array(
			"type"			=> "checkbox",
			"param_name"	=> "en_isotope",
			"dependency" 	=> array(
					"element"	=> 'display_style',
					"value"		=> 'grid'
				),
			'value'			=> array(
				esc_html__( 'Use Isotope', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "carousel_auto",
			"dependency" 	=> array(
								"element"	=> 'display_style',
								"value"		=> array( "carousel" )
							),
			'value'			=> array(
				esc_html__( 'AutoPlay Carousel', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "carousel_pagination",
			"dependency" 	=> array(
								"element"	=> 'display_style',
								"value"		=> array( "carousel" )
							),
			'value'			=> array(
				esc_html__( 'Pagination', 'aasana' ) => true
			)
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Hover', 'aasana' ),
			"param_name"	=> "anim_style",
			"value"			=> array(
								esc_html__( 'Default Hover', 'aasana' ) => 'hoverdef',
								esc_html__( 'Solid Color with Zoomed Border', 'aasana' ) => 'hoverbi',
								esc_html__( 'Solid Color with Border', 'aasana' ) => 'hoverbi2',
								esc_html__( 'Mouse Follow', 'aasana' ) => 'hoverdir',
								// esc_html__( '3D Hover', 'aasana' ) => 'hover3d',
								
								esc_html__( 'Zoom With Rotation', 'aasana' ) => 'hoversr',
								esc_html__( 'Zoom With Blur', 'aasana' ) => 'hoverzb',
								esc_html__( 'No Hover With Link', 'aasana' ) => 'hover_none_link',
								esc_html__( 'No Hover', 'aasana' ) => 'hover_none',
							),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency" 	=> array(
					"element"	=> 'display_style',
					"value"		=> array( "carousel" ,"grid" , "filter", "filter_with_ajax" )
				),
		),
		array(
			'type'				=> 'cws_dropdown',
			'multiple'			=> "true",
			'heading'			=> esc_html__( 'Show', 'aasana' ),
			'param_name'		=> 'link_show',					
			"dependency" 	=> array(
					"element"	=> 'anim_style',
					"value"		=> array( "hoverdef" ,"hoverdir" , "hoverbi", "hoverbi2", "hoversr", "hoverzb" )
				),
			'value'				=> array(
				esc_html__( 'Make Image Clickable', 'aasana' )	=> 'area_link',
				esc_html__( 'Show Image PoPup Icon', 'aasana' )	=> 'single_link',
				esc_html__( 'Show Project Details Icon', 'aasana' )	=> 'popup_link',
			)
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "en_hover_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Edit Fill Color', 'aasana' ) => true
			),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency" 	=> array(
					"element"	=> 'anim_style',
					"value"		=> array( "hoverdef" ,"hoverdir" , "hoverbi", "hoverbi2", "hoversr", "hoverzb" )
				),
		),		
		array(
			"type"			=> "colorpicker",
			"param_name"	=> "hover_color",
			"dependency"	=> array(
				"element"	=> "en_hover_color",
				"not_empty"	=> true
			),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> "rgba(0,0,0,0.7)"
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "en_title_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Edit Title Color', 'aasana' ) => true
			),
			"dependency" 	=> array(
					"element"	=> 'anim_style',
					"value"		=> array( "hoverdef" ,"hoverdir" , "hoverbi", "hoverbi2", "hoversr", "hoverzb" )
				),
		),
		array(
			"type"			=> "colorpicker",
			"param_name"	=> "title_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "en_title_color",
				"not_empty"	=> true
			),
			"value"			=> "#ffffff"
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "en_cat_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Edit Category Color', 'aasana' ) => true
			),
			"dependency" 	=> array(
					"element"	=> 'anim_style',
					"value"		=> array( "hoverdef" ,"hoverdir" , "hoverbi", "hoverbi2", "hoversr", "hoverzb" )
				),
		),
		array(
			"type"			=> "colorpicker",
			"param_name"	=> "cat_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "en_cat_color",
				"not_empty"	=> true
			),
			"value"			=> ""
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Animation', 'aasana' ),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"param_name"	=> "appear_style",
			"value"			=> array(
								esc_html__( 'None', 'aasana' ) => 'none',
								esc_html__( 'Bounce', 'aasana' ) => 'callout.bounce',
								esc_html__( 'Shake', 'aasana' ) => 'callout.shake',
								esc_html__( 'Flash', 'aasana' ) => 'callout.flash',
								esc_html__( 'Pulse', 'aasana' ) => 'callout.pulse',
								esc_html__( 'Swing', 'aasana' ) => 'callout.swing',
								esc_html__( 'Tada', 'aasana' ) => 'callout.tada',
								esc_html__( 'Fade In', 'aasana' ) => 'transition.fadeIn',
								esc_html__( 'Flip X In', 'aasana' ) => 'transition.flipXIn',
								esc_html__( 'Flip Y In', 'aasana' ) => 'transition.flipYIn',
								esc_html__( 'Shrink In', 'aasana' ) => 'transition.shrinkIn',
								esc_html__( 'Expand In', 'aasana' ) => 'transition.expandIn',
								esc_html__( 'Grow', 'aasana' ) => 'transition.grow',
								esc_html__( 'Slide Up', 'aasana' ) => 'transition.slideUpBigIn',
								esc_html__( 'Slide Down', 'aasana' ) => 'transition.slideDownBigIn',
								esc_html__( 'Slide Left', 'aasana' ) => 'transition.slideLeftBigIn',
								esc_html__( 'Slide Right', 'aasana' ) => 'transition.slideRightBigIn',
								esc_html__( 'Perspective Up', 'aasana' ) => 'transition.perspectiveUpIn',
								esc_html__( 'Perspective Down', 'aasana' ) => 'transition.perspectiveDownIn',
								esc_html__( 'Perspective Left', 'aasana' ) => 'transition.perspectiveLeftIn',
								esc_html__( 'Perspective Right', 'aasana' ) => 'transition.perspectiveRightIn',
							),
			"dependency" 	=> array(
					"element"	=> 'display_style',
					"value"		=> array( "carousel" ,"grid" , "filter", "filter_with_ajax" )
				),
		),

		array(
			'type'			=> 'dropdown',
			'heading'		=> esc_html__( 'Show Title and Description', 'aasana' ),
			'param_name'	=> 'info_pos',
			'value'			=> array(
					esc_html__( 'On Image Hover', 'aasana' ) => 'inside_img',
					esc_html__( 'Under Image', 'aasana' ) => 'under_img',
				),
			"dependency" 	=> array(
								"element"	=> 'layout',
								"value"		=> array( "def","1","2","3","4","5" )
				),
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "add_divider",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Add Divider', 'aasana' ) => true
			),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency" 	=> array(
					"element"	=> 'info_pos',
					"value"		=> "under_img"
				),
		),		
		array(
			"type"			=> "checkbox",
			"param_name"	=> "customize_carousel",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			'value'			=> array(
				esc_html__( 'Customize Carousel', 'aasana' ) => true
			),
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency" 	=> array(
					"element"	=> 'display_style',
					"value"		=> "carousel"
				),
		),
		array(
			"type"			=> "colorpicker",
			"param_name"	=> "pagination_carousel",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"heading"		=> esc_html__( 'Pagination Color', 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_carousel",
				"not_empty"	=> true
			),
			"value"			=> ""
		),		
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Info Alignment', 'aasana' ),
			"param_name"	=> "info_align",
			"value"			=> array(
					esc_html__( 'Center', 'aasana' ) 	=> 'center',
					esc_html__( 'Left', 'aasana' )	=> 'left',
					esc_html__( 'Right', 'aasana' ) 	=> 'right',
							),
			"dependency" 	=> array(
								"element"	=> 'info_pos',
								"value"		=> 'under_img'
				),
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Shape', 'aasana' ),
			"param_name"	=> "portfolio_style",
			"value"			=> array(
					esc_html__( 'Square', 'aasana' ) => 'def_style',
					esc_html__( 'Square with no spacing', 'aasana' ) => 'wide_style',
							),
			"dependency" 	=> array(
								"element"	=> 'info_pos',
								"value"		=> 'inside_img'
				),
			"group"			=> esc_html__( "Styling", 'aasana' ),
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
								"value"		=> array( "grid", "filter", "filter_with_ajax" )
							),
			"value"			=> esc_html( get_option( 'posts_per_page' ) )
		),
		array(
			'type'			=> 'checkbox',
			'param_name'	=> 'cws_portfolio_show_data_override',
			'value'			=> array(
				esc_html__( 'Show Meta Data', 'aasana' ) => true
			)
		),		
		array(
			'type'				=> 'cws_dropdown',
			'multiple'			=> "true",
			'param_name'		=> 'cws_portfolio_data_to_show',
			'dependency'		=> array(
				'element'			=> 'cws_portfolio_show_data_override',
				'not_empty'			=> true
			),
			'value'				=> array(
				esc_html__( 'None', 'aasana' )			=> '',
				esc_html__( 'Title', 'aasana' )		=> 'title',
				esc_html__( 'Excerpt', 'aasana' )		=> 'excerpt',
				esc_html__( 'Categories', 'aasana' )	=> 'cats'
			)
		),	
		array(
			'type'			=> 'textfield',
			'heading'		=> esc_html__( 'Content Character Limit', 'aasana' ),
			'param_name'	=> 'chars_count',
			'dependency'	=> array(
				'element'		=> 'cws_portfolio_show_data_override',
				'not_empty'		=> true
			),
			'value'			=> 	''	
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Pagination', 'aasana' ),
			"param_name"	=> "pagination_grid",
			"dependency" 	=> array(
								"element"	=> "display_style",
								"value"		=> array( "grid", "filter", "filter_with_ajax" )
							),
			"value"			=> array(
				esc_html__( "Standard", 'aasana' ) 	=> 'standard_with_ajax',
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
		"name"				=> esc_html__( 'CWS Portfolio', 'aasana' ),
		"base"				=> "cws_sc_portfolio_posts_grid",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));
	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Portfolio_Posts_Grid extends WPBakeryShortCode {
	    }
	}
?>