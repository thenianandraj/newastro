<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	$body_font_options 		= $aasana_theme_funcs->cws_get_option( 'body-font' );
	$body_font_color 		= esc_attr( $body_font_options['color'] );
	$icon_params 			= cws_ext_icon_vc_sc_config_params( "customize", true );
	$params 				= cws_ext_merge_arrs( array(
		array(
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Type', 'aasana' ),
				"param_name"	=> "type",
				"value"			=> array(
					esc_html__( 'Normal', 'aasana' )				=> '',
					esc_html__( 'Success', 'aasana' )				=> 'success',
					esc_html__( 'Warning', 'aasana' )				=> 'warn',
					esc_html__( 'Error', 'aasana' )				=> 'error',
					esc_html__( 'Informational', 'aasana' )		=> 'info',
				) 
			),
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),
			array(
				"type"			=> "textarea",
				"heading"		=> esc_html__( 'Text', 'aasana' ),
				"param_name"	=> "text",
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "is_closable",
				"value"			=> array(
					esc_html__( 'Closable', 'aasana' ) => true
				)
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize",
				"value"			=> array( esc_html__( 'Customize', 'aasana' ) => true )
			)
		),
		$icon_params,
		array(
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Fill Color', 'aasana' ),
				"param_name"	=> "custom_fill_color",
				"dependency"	=> array(
					"element"	=> "customize",
					"not_empty"	=> true
				),
				"value"			=> "#e6eaed"
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Font Color', 'aasana' ),
				"param_name"	=> "custom_font_color",
				"dependency"	=> array(
					"element"	=> "customize",
					"not_empty"	=> true
				),
				"value"			=> $body_font_color
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
		"name"				=> esc_html__( 'CWS Message Box', 'aasana' ),
		"base"				=> "cws_sc_msg_box",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Msg_Box extends WPBakeryShortCode {
	    }
	}
?>