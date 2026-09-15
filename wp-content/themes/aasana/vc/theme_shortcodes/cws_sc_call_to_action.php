<?php
	global $aasana_theme_funcs;
	$theme_color 			= esc_attr( $aasana_theme_funcs->cws_get_option( "theme-main-one-color" ) );
	$theme_color_2 			= esc_attr( $aasana_theme_funcs->cws_get_option( "theme-main-secondary-color" ) );
	// Map Shortcode in Visual Composer
	$icon_params 			= cws_ext_icon_vc_sc_config_params();
	$params 				= cws_ext_merge_arrs( array(
		array(
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Subtitle', 'aasana' ),
				"param_name"	=> "subtitle",
			),			

			array(
				"type"			=> "textarea",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),			
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Description', 'aasana' ),
				"param_name"	=> "desc_subtitle",
			),
		),
		$icon_params,
		array(
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Display', 'aasana' ),
				"param_name"	=> "display_settings",
				"value"			=> array(
					esc_html__( 'Button', 'aasana' )		=> 'button_s',
					esc_html__( 'Banner', 'aasana' )	=> 'banner',
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_button",
				"dependency"	=> array(
					"element"	=> "display_settings",
					"value"	=> array("button_s")
				),
				"value"			=> array( esc_html__( 'Add Button', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Button Text', 'aasana' ),
				"param_name"	=> "button_title",
				"dependency"	=> array(
					"element"	=> "add_button",
					"not_empty"	=> true
				),
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Button Url', 'aasana' ),
				"param_name"	=> "button_url",
				"dependency"	=> array(
					"element"	=> "add_button",
					"not_empty"	=> true
				)
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "button_new_tab",
				"dependency"	=> array(
					"element"	=> "add_button",
					"not_empty"	=> true
				),
				"value"			=> array( esc_html__( 'Open Link in New Tab', 'aasana' ) => true )
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_banner",
				"dependency"	=> array(
					"element"	=> "display_settings",
					"value"	=> array("banner")
				),
				"value"			=> array( esc_html__( 'Add Banner', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Banner Title', 'aasana' ),
				"param_name"	=> "banner_title",
				"dependency"	=> array(
					"element"	=> "add_banner",
					"not_empty"	=> true
				),
			),			
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Banner Discount', 'aasana' ),
				"param_name"	=> "banner_price",
				"dependency"	=> array(
					"element"	=> "add_banner",
					"not_empty"	=> true
				),
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Banner Discount Description', 'aasana' ),
				"param_name"	=> "banner_description",
				"dependency"	=> array(
					"element"	=> "add_banner",
					"not_empty"	=> true
				),
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Banner Url', 'aasana' ),
				"param_name"	=> "banner_url",
				"dependency"	=> array(
					"element"	=> "add_banner",
					"not_empty"	=> true
				)
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "banner_new_tab",
				"dependency"	=> array(
					"element"	=> "add_banner",
					"not_empty"	=> true
				),
				"value"			=> array( esc_html__( 'Open Link in New Tab', 'aasana' ) => true )
			),
			array(
				"type"			=> "css_editor",
				"param_name"	=> "custom_styles",
				"group"			=> esc_html__( "Styling", 'aasana' )
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_colors",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Customize Colors', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Featured Color', 'aasana' ),
				"param_name"	=> "featured_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $theme_color_2
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Overlay Color', 'aasana' ),
				"param_name"	=> "overlay_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ""
			),		
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Featured Link Color', 'aasana' ),
				"param_name"	=> "display_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $theme_color_2
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Featured Link Font Color', 'aasana' ),
				"param_name"	=> "display_font_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $theme_color_2
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
				"value"			=> "rgba(0,0,0,0.065)"
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
		"name"				=> esc_html__( 'CWS Call To Action', 'aasana' ),
		"base"				=> "cws_sc_call_to_action",
		'category'			=> "By CWS",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Call_To_Action extends WPBakeryShortCode {
	    }
	}
?>