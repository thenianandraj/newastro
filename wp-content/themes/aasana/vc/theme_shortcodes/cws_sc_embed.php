<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	vc_map( array(
		"name"				=> esc_html__( 'CWS Embed', 'aasana' ),
		"base"				=> "cws_sc_embed",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Link', 'aasana' ),
				"param_name"	=> "url",
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Width in pixels', 'aasana' ),
				"param_name"	=> "width"
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Height in pixels', 'aasana' ),
				"param_name"	=> "height"
			),
		)
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Embed extends WPBakeryShortCode {
	    }
	}
?>