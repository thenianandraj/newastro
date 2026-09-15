<?php
	global $aasana_theme_funcs;
	$def_fill_color			= "rgba(255, 255, 255, 0.95)";
	$body_font_options		= $aasana_theme_funcs->cws_get_option( 'body-font' );
	$body_font_color		= esc_attr( $body_font_options['color'] );	
	$heading_font_options 	= $aasana_theme_funcs->cws_get_option( 'header-font' );
	$heading_font_color 	= esc_attr( $heading_font_options['color'] );	
	$theme_color 			= esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	$def_border_color 		= "rgba(255, 255, 255, 0.95)";
	$icon_params 			= cws_ext_icon_vc_sc_config_params("services_type", false, array( "iconic" ) );
	$params 				= cws_ext_merge_arrs( array(
		array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			)
		),
		array(
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Type', 'aasana' ),
			"param_name"	=> "services_type",
			"value"			=> array(
				esc_html__( 'Icon', 'aasana' )		=> 'iconic',
				esc_html__( 'Image', 'aasana' )		=> 'image',
				) 
			),
		array(
			"type"			=> "attach_image",
			"heading"		=> esc_html__( 'Image', 'aasana' ),
			"param_name"	=> "plan_img",
			"dependency"	=> array(
				"element"	=> "services_type",
				"value"		=> array( "image" )
			),
		),
		),
		$icon_params,
		array(
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Shape', 'aasana' ),
				"param_name"	=> "shape",
				"dependency"	=> array(
					"element"	=> "services_type",
					"value"		=> array( "image" )
				),
				"value"			=> array(
					esc_html__( 'Square', 'aasana' ) => 'square',
					esc_html__( 'Round', 'aasana' ) => 'round'
				) 
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Size', 'aasana' ),
				"param_name"	=> "size",
				"dependency"	=> array(
					"element"	=> "services_type",
					"value"		=> array( "iconic" )
				),
				"value"			=> array(
					esc_html__( 'Small', 'aasana' )		=> '2x',
					esc_html__( 'Mini', 'aasana' )		=> 'lg',
					esc_html__( 'Medium', 'aasana' )		=> '3x',
					esc_html__( 'Large', 'aasana' )		=> '4x',
					esc_html__( 'Extra Large', 'aasana' )	=> '5x'
				) 
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Url', 'aasana' ),
				"param_name"	=> "url",
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "new_tab",
				"value"			=> array( esc_html__( 'Open in a New Tab', 'aasana' ) => true )				
			),			
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Alignment', 'aasana' ),
				"param_name"	=> "alignment",
				"value"			=> array(
					esc_html__( "Left", 'aasana' ) 	=> "left",
					esc_html__( "Center", 'aasana' ) 	=> "center",
					esc_html__( "Right", 'aasana' ) 	=> "right",
				)
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Divider', 'aasana' ),
				"param_name"	=> "divider",
					"value"			=> array(
						esc_html__( 'None', 'aasana' )		=> '',
						esc_html__( 'Left', 'aasana' )		=> 'left',
						esc_html__( 'Right', 'aasana' )	=> 'right',
						esc_html__( 'Both', 'aasana' )		=> 'both'
					) 
			),	
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Text Spacing', 'aasana' ),
				"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
				"param_name"	=> "title_paddings",
				"value"			=> "0px 0px"
			),				
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Title Spacing', 'aasana' ),
				"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
				"param_name"	=> "title_spacing",
				"value"			=> "0px 0px"
			),			
			array(
				"type"			=> "css_editor",
				"param_name"	=> "custom_styles",
				"group"			=> esc_html__( "Styling", 'aasana' )
			),
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Background Image', 'aasana' ),
				"param_name"	=> "bg_img_id",
				"group"			=> esc_html__( "Styling", 'aasana' )
			),

			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_size",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Edit Icon\'s Size', 'aasana' ) => true )				
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Size Icon', 'aasana' ),
				"param_name"	=> "size_i",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_size",
					"not_empty"	=> true
				),
				"value"			=> "15px"
			),		
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_size_title",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Edit Title\'s Size', 'aasana' ) => true )				
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Font Size', 'aasana' ),
				"param_name"	=> "size_t",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_size_title",
					"not_empty"	=> true
				),
				"value"			=> "22px"
			),				
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Font Weight', 'aasana' ),
				"param_name"	=> "weight_t",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_size_title",
					"not_empty"	=> true
				),
				"value"			=> "700"
			),				
			array(
				"type"			=> "checkbox",
				"param_name"	=> "draw_border",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Add Border', 'aasana' ) => true )				
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Border Color', 'aasana' ),
				"param_name"	=> "custom_border_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "draw_border",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),	
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Width', 'aasana' ),
				"param_name"	=> "size_border",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "draw_border",
					"not_empty"	=> true
				),
				"value"			=> "5px"
			),		
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_colors",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Customize Colors', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Fill Color', 'aasana' ),
				"param_name"	=> "custom_fill_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $def_fill_color
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Icon Color', 'aasana' ),
				"param_name"	=> "icon_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ""
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Icon Background Color', 'aasana' ),
				"param_name"	=> "bg_icon_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ""
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
				"value"			=> $heading_font_color
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
				"value"			=> $body_font_color
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_hover",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Add Background Hover', 'aasana' ) => true )
			),									
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Fill Color', 'aasana' ),
				"param_name"	=> "hover_fill_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_hover",
					"not_empty"	=> true
				),
				"value"			=> $def_fill_color
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Title Color', 'aasana' ),
				"param_name"	=> "custom_selection_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_hover",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Font Color', 'aasana' ),
				"param_name"	=> "custom_f_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_hover",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_icon_hover",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Add Icon Hover', 'aasana' ) => true )
			),			

			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Hover Effect', 'aasana' ),
				"param_name"	=> "hover_i_style",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_icon_hover",
					"not_empty"	=> true
				),
				"value"			=> array(
					esc_html__( 'Style 1', 'aasana' ) => 'style_1',
					esc_html__( 'Style 2', 'aasana' ) => 'style_2',
					esc_html__( 'Style 3', 'aasana' ) => 'style_3',
				) 
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Icon Color', 'aasana' ),
				"param_name"	=> "custom_icon_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "hover_i_style",
					"value"		=> array( "style_1", "style_2" )
				),
				"value"			=> "#fff"
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Icon Background Color', 'aasana' ),
				"param_name"	=> "custom_bg_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "hover_i_style",
					"value"		=> array( "style_1", "style_2" )
				),
				"value"			=> $theme_color
			),

			array(
				"type"				=> "textfield",
				"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
				"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
				"param_name"		=> "el_class",
				"value"				=> ""
			),			
			array(
				"type"			=> "textarea_html",
				"heading"		=> esc_html__( 'Description', 'aasana' ),
				"param_name"	=> "content",
			)
		)
	));
	// Map Shortcode in Visual Composer
	vc_map( array(
		"name"				=> esc_html__( 'CWS Services', 'aasana' ),
		"base"				=> "cws_sc_services",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Services extends WPBakeryShortCode {
	    }
	}
?>