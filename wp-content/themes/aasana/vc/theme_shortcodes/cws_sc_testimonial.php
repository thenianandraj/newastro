<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	vc_map( array(
		"name"				=> esc_html__( 'Custom Testimonials', 'aasana' ),
		"base"				=> "cws_sc_vc_testimonial",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> array(
			array(
				"type"			=> "textarea",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Quote', 'aasana' ),
				"param_name"	=> "quote",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Link', 'aasana' ),
				"param_name"	=> "url",
			),
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Thumbnail', 'aasana' ),
				"param_name"	=> "thumbnail",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Author Name', 'aasana' ),
				"param_name"	=> "author_name",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "author_status",
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
				"heading"		=> esc_html__( 'Author Color', 'aasana' ),
				"param_name"	=> "custom_author_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ''
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
				"value"			=> ''
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Quote Color', 'aasana' ),
				"param_name"	=> "custom_qoute_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ''
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Overlay Color', 'aasana' ),
				"param_name"	=> "custom_overlay_color",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ''
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
	    class WPBakeryShortCode_CWS_Sc_Testimonial extends WPBakeryShortCode {
	    }
	}
?>