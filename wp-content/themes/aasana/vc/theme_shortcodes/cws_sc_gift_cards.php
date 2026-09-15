<?php
	global $aasana_theme_funcs;
	$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	// Map Shortcode in Visual Composer
	vc_map( array(
		"name"				=> esc_html__( 'CWS Gift Cards', 'aasana' ),
		"base"				=> "cws_sc_gift_cards",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
				"value"			=> esc_html__( 'Gift Voucher', 'aasana' ),
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_divider",
				'std'			=> true,
				"value"			=> array( esc_html__( 'Add Divider', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Currency', 'aasana' ),
				"param_name"	=> "currency",
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Currency Location', 'aasana' ),
				"param_name"	=> "curency_alignment",
				"value"			=> array(
					esc_html__( 'Before', 'aasana' )		=> 'before',
					esc_html__( 'After', 'aasana' )		=> 'after',
				) 
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
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Logo', 'aasana' ),
				"param_name"	=> "cards_logo",
			),	
			array(
				"type"			=> "checkbox",
				"param_name"	=> "use_custom_color",
				"value"			=> array( esc_html__( 'Customize', 'aasana' ) => true )			
			),			
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Background Image', 'aasana' ),
				"param_name"	=> "cards_img",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
			),	
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Text Color', 'aasana' ),
				"param_name"	=> "custom_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Background Overlay', 'aasana' ),
				"param_name"	=> "bg_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_discount",
				"value"			=> array( esc_html__( 'Add Discount', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"param_name"	=> "discount_text",
				"dependency"	=> array(
					"element"	=> "add_discount",
					"not_empty"	=> true
				),
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Discount Color', 'aasana' ),
				"param_name"	=> "discount_color",
				"dependency"	=> array(
					"element"	=> "add_discount",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_url",
				"value"			=> array( esc_html__( 'Add Url', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Url', 'aasana' ),
				"param_name"	=> "button_url",
				"dependency"	=> array(
					"element"	=> "add_url",
					"not_empty"	=> true
				)
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_url_new_tab",
				"dependency"	=> array(
					"element"	=> "add_url",
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
	    class WPBakeryShortCode_CWS_Sc_Gift_Cards extends WPBakeryShortCode {
	    }
	}
?>