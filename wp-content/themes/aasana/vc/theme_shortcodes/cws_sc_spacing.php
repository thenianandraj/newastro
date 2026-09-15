<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	vc_map( array(
		"name"				=> esc_html__( 'CWS Spacing', 'aasana' ),
		"base"				=> "cws_sc_spacing",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> array(
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Spacing', 'aasana' ),
				"param_name"	=> "spacing",
				"value"			=> "30px"
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
	    class WPBakeryShortCode_CWS_Sc_Spacing extends WPBakeryShortCode {
	    }
	}
?>