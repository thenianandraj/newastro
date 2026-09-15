<?php
	global $aasana_theme_funcs;
	$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	$main_secondary_color = esc_attr( $aasana_theme_funcs->cws_get_option('theme-main-secondary-color') );
	// Map Shortcode in Visual Composer
	vc_map( array(
		"name"				=> esc_html__( 'CWS Pricing Plan', 'aasana' ),
		"base"				=> "cws_sc_pricing_plan",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Image', 'aasana' ),
				"param_name"	=> "plan_img",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Currency', 'aasana' ),
				"param_name"	=> "currency",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Price', 'aasana' ),
				"description"	=> esc_html__( 'Split integer and decimal part by dot symbol', 'aasana' ),
				"param_name"	=> "price",
				"value"			=> "29.99",
				"save_always"	=> true
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Price description', 'aasana' ),
				"param_name"	=> "price_desc",
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "highlighted",
				"value"			=> array( esc_html__( 'Highlighted', 'aasana' ) => true )			
			),							
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_hover",
				"value"			=> array( esc_html__( 'Add Hover', 'aasana' ) => true )			
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "use_custom_color",
				"value"			=> array( esc_html__( 'Use Custom Color', 'aasana' ) => true )			
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Custom Color', 'aasana' ),
				"param_name"	=> "custom_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Main Color', 'aasana' ),
				"param_name"	=> "main_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $main_secondary_color,
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_button",
				"value"			=> array( esc_html__( 'Add Button', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Button Text', 'aasana' ),
				"param_name"	=> "button_text",
				"dependency"	=> array(
					"element"	=> "add_button",
					"not_empty"	=> true
				),
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Button Font Color', 'aasana' ),
				"param_name"	=> "btn_font_color",
				"dependency"	=> array(
					"element"	=> "add_button",
					"not_empty"	=> true
				),
				"value"			=> "#fff",
			),		
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Url', 'aasana' ),
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
				"type"			=> "textarea_html",
				"heading"		=> esc_html__( 'Content', 'aasana' ),
				"param_name"	=> "content",
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

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Pricing_Plan extends WPBakeryShortCode {
	    }
	}
?>