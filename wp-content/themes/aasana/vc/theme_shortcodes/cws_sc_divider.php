<?php
	global $aasana_theme_funcs;
	$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	$icon_params 		= cws_ext_icon_vc_sc_config_params("separate_type", false, array( "iconic" ) );
	// Map Shortcode in Visual Composer
	$params 				= cws_ext_merge_arrs( array(
		array(
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Height', 'aasana' ),
				"description"	=> esc_html__( 'Integer', 'aasana' ),
				"param_name"	=> "height_divider",
				"value"			=> "3"
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Margin Top', 'aasana' ),
				"description"	=> esc_html__( 'in pixels', 'aasana' ),
				"param_name"	=> "mtop",
				"value"			=> ""
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Margin Bottom', 'aasana' ),
				"description"	=> esc_html__( 'in pixels', 'aasana' ),
				"param_name"	=> "mbottom",
				"value"			=> ""
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_separate",
				"value"			=> array( esc_html__( 'Add Icon', 'aasana' ) => true )
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Separate Type', 'aasana' ),
				"param_name"	=> "separate_type",
				"dependency"	=> array(
					"element"	=> "add_separate",
					"not_empty"	=> true
				),
				"value"			=> array(
					esc_html__( 'Icon', 'aasana' )		=> 'iconic',
					esc_html__( 'Image', 'aasana' )		=> 'image',
				) 
			),
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Image', 'aasana' ),
				"param_name"	=> "plan_img",
				"dependency"	=> array(
					"element"	=> "separate_type",
					"value"		=> array( "image" )
				),
			),

		),
		$icon_params,
		array(			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( "Icon Color", 'aasana' ),
				"param_name"	=> "custom_color_icon",
				"dependency"	=> array(
					"element"	=> "separate_type",
					"value"		=> array( "iconic" )
				),
				"value"			=> $theme_color
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Icon Size', 'aasana' ),
				"param_name"	=> "size",
				"dependency"	=> array(
					"element"	=> "separate_type",
					"value"		=> array( "iconic" )
				),
				"value"			=> array(
					esc_html__( 'Small', 'aasana' )		=> '2x',
					esc_html__( 'Mini', 'aasana' )		=> 'lg',
					esc_html__( 'Medium', 'aasana' )		=> '3x',
					esc_html__( 'Large', 'aasana' )		=> '4x',
					esc_html__( 'Extra Large', 'aasana' )	=> '5x'
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_colors",
				"value"			=> array( esc_html__( 'Customize Divider', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"param_name"	=> "custom_color",
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
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
		"name"				=> esc_html__( 'CWS Divider', 'aasana' ),
		"base"				=> "cws_sc_divider",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Divider extends WPBakeryShortCode {
	    }
	}
?>