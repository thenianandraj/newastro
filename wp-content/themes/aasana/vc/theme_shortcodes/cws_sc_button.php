<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	$theme_color 		= esc_attr( $aasana_theme_funcs->cws_get_option( "theme-main-one-color" ) );
	$theme_color_2 		= esc_attr( $aasana_theme_funcs->cws_get_option( "theme-main-one-color" ) );
	$icon_params 		= cws_ext_icon_vc_sc_config_params();
	$params 			= cws_ext_merge_arrs( array(
		array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Link', 'aasana' ),
				"param_name"	=> "url",
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "new_tab",
				"value"			=> array( esc_html__( 'Open in New Tab', 'aasana' ) => true )
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Size', 'aasana' ),
				"param_name"	=> "size",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array(
					esc_html__( 'Regular', 'aasana' )		=> 'regular',
					esc_html__( 'Mini', 'aasana' )			=> 'mini',
					esc_html__( 'Small', 'aasana' )		=> 'small',
					esc_html__( 'Large', 'aasana' )		=> 'large',
					esc_html__( 'X Large', 'aasana' )		=> 'xlarge',
				) 
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Left/Right Padding', 'aasana' ),
				"description"	=> esc_html__( 'Units Required. Separate with space if needed.', 'aasana' ),
				"param_name"	=> "ofs",
				"group"			=> esc_html__( "Styling", 'aasana' ),
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Aligning', 'aasana' ),
				"param_name"	=> "aligning",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array(
					esc_html__( 'None', 'aasana' )		=> '',
					esc_html__( 'Left', 'aasana' )		=> 'left',
					esc_html__( 'Right', 'aasana' )		=> 'right',
					esc_html__( 'Center', 'aasana' )		=> 'center'
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "fw",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Full Width', 'aasana' ) => true )
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "disable_border",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Disable Border', 'aasana' ) => true )
			),
		),
		$icon_params,
		array(
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Icon Position', 'aasana' ),
				"param_name"	=> "icon_pos",
				"value"			=> array(
					esc_html__( 'Right', 'aasana' )		=> 'right',
					esc_html__( 'Left', 'aasana' )		=> 'left'
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_size_title",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Edit Title', 'aasana' ) => true )				
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Font Size Title', 'aasana' ),
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
				"heading"		=> esc_html__( 'Font Weight Title', 'aasana' ),
				"param_name"	=> "weight_t",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_size_title",
					"not_empty"	=> true
				),
				"value"			=> "400"
			),	
			array(
				"type"			=> "checkbox",
				"param_name"	=> "alt",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Alternative', 'aasana' ) => true )
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_hover",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Add Hover', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Hovered Fill Color', 'aasana' ),
				"param_name"	=> "hovered_fill_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_hover",
					"not_empty"	=> true
				),
				"value"			=> "#fff"
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Hovered Font Color', 'aasana' ),
				"param_name"	=> "hovered_font_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_hover",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Button Effects', 'aasana' ),
				"param_name"	=> "hover_effects_more_btn",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_hover",
					"not_empty"	=> true
				),
				"value"			=> array(
					esc_html__( "Style 1", 'aasana' ) 	=> 'style_1',
					esc_html__( "Style 2", 'aasana' ) 	=> 'style_2',
				)		
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
				"param_name"	=> "fill_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Font Color', 'aasana' ),
				"param_name"	=> "font_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> "#fff"
			),
			array(
				"type"				=> "textfield",
				"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
				"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
				"param_name"		=> "el_class",
				"value"				=> ""
			)
		)
	));
	vc_map( array(
		"name"				=> esc_html__( 'CWS Button', 'aasana' ),
		"base"				=> "cws_sc_button",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Button extends WPBakeryShortCode {
	    }
	}
?>