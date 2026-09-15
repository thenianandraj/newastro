<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	vc_map( array(
		"name"				=> esc_html__( 'CWS Progress Bar', 'aasana' ),
		"base"				=> "cws_sc_progress_bar",
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
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Progress', 'aasana' ),
				"description"	=> esc_html__( 'In Percents', 'aasana' ),
				"param_name"	=> "progress",
				"value"			=> "50",
				"save_always"	=> true
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "use_custom_color",
				"value"			=> array( esc_html__( 'Use Custom Color', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Fill Color', 'aasana' ),
				"param_name"	=> "custom_fill_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> AASANA_COLOR
			),				
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Percents Color', 'aasana' ),
				"param_name"	=> "custom_percents_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
					"not_empty"	=> true
				),
				"value"			=> AASANA_COLOR
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Title Color', 'aasana' ),
				"param_name"	=> "custom_title_color",
				"dependency"	=> array(
					"element"	=> "use_custom_color",
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

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Progress_Bar extends WPBakeryShortCode {
	    }
	}
?>