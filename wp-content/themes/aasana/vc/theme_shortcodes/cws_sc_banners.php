<?php
	global $aasana_theme_funcs;
	$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	// Map Shortcode in Visual Composer
	vc_map( array(
		"name"				=> esc_html__( 'CWS Banners', 'aasana' ),
		"base"				=> "cws_sc_banners",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> array(
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Select Style Type', 'aasana' ),
				"param_name"	=> "style_type",
				"value"			=> array(
					esc_html__( 'Style 1', 'aasana' )		=> 'style1',
					esc_html__( 'Style 2', 'aasana' )		=> 'style2',
				) 
			),			
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Text Alignment', 'aasana' ),
				"param_name"	=> "text_alignment",
				"value"			=> array(
					esc_html__( 'Left', 'aasana' )		=> 'left',
					esc_html__( 'Center', 'aasana' )		=> 'center',
					esc_html__( 'Right', 'aasana' )		=> 'right',
				) 
			),
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Image', 'aasana' ),
				"param_name"	=> "banners_img",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title_banners",
				"value"			=> "25%",
				"save_always"	=> true
			),							
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Font Size Title', 'aasana' ),
				"param_name"	=> "f_size_title_banners",
				"value"			=> "45px",
				"save_always"	=> true
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Title description', 'aasana' ),
				"param_name"	=> "desc_banners",
				"value"			=> "Best offer"
			),				
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Font Size Description', 'aasana' ),
				"param_name"	=> "f_size_desc_banners",
				"value"			=> "28px",
				"save_always"	=> true
			),	
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Discount', 'aasana' ),
				"param_name"	=> "discount_banners",
				"value"			=> "-25%",
				"save_always"	=> true,
				"dependency"	=> array(
					"element"	=> "style_type",
					"value"		=> array('style2')
				),
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "use_custom_color",
				"value"			=> array( esc_html__( 'Customize', 'aasana' ) => true )			
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Font Color', 'aasana' ),
				"param_name"	=> "custom_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Skew Background', 'aasana' ),
				"param_name"	=> "trianlge_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),				
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Overlay Color', 'aasana' ),
				"param_name"	=> "overlay_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> $theme_color,
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_divider",
				'std'			=> true,
				"value"			=> array( esc_html__( 'Add Divider', 'aasana' ) => true )
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_button",
				'std'			=> true,
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
				"value"			=> esc_html__( 'Buy Now', 'aasana' ),
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Url', 'aasana' ),
				"param_name"	=> "button_url",
				"dependency"	=> array(
					"element"	=> "add_button",
					"not_empty"	=> true
				),
				"value"			=> "#",
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
	    class WPBakeryShortCode_CWS_Sc_Banners extends WPBakeryShortCode {
	    }
	}
?>