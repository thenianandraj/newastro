<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	$theme_color 			= $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' );
	$theme_color 			= esc_attr( $theme_color ); 
	$icon_params 			= cws_ext_icon_vc_sc_config_params();	
	$params 				= cws_ext_merge_arrs( array(
		$icon_params,
		array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),			
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Description', 'aasana' ),
				"param_name"	=> "desc",
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Icon Size', 'aasana' ),
				"param_name"	=> "size",
				"value"			=> array(
					esc_html__( 'Regular', 'aasana' )		=> '3x',
					esc_html__( 'Mini', 'aasana' )			=> 'lg',
					esc_html__( 'Small', 'aasana' )		=> '2x',
					esc_html__( 'Large', 'aasana' )		=> '4x',
					esc_html__( 'X Large', 'aasana' )		=> '5x',
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "custom_size_i",
				"value"			=> array( esc_html__( 'Customize Size Icon', 'aasana' ) => true )
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Size Icon', 'aasana' ),
				"param_name"	=> "size_i",
				"dependency"	=> array(
					"element"	=> "custom_size_i",
					"not_empty"	=> true
				),
				"value"			=> "48px"
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Icon Position', 'aasana' ),
				"param_name"	=> "icon_pos",
				"value"			=> array(
					esc_html__( 'Left', 'aasana' )		=> 'left',
					esc_html__( 'Center', 'aasana' )	=> 'center',
					esc_html__( 'Right', 'aasana' )	=> 'right'
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "custom_color_milestone",
				"value"			=> array( esc_html__( 'Custom Color', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Font Color', 'aasana' ),
				"param_name"	=> "custom_color_m",
				"dependency"	=> array(
					"element"	=> "custom_color_milestone",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),			
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Icon Color', 'aasana' ),
				"param_name"	=> "i_color_m",
				"dependency"	=> array(
					"element"	=> "custom_color_milestone",
					"not_empty"	=> true
				),
				"value"			=> $theme_color
			),
			array(
				"type"			=> "attach_image",
				"heading"		=> esc_html__( 'Background Image', 'aasana' ),
				"param_name"	=> "milestone_img",
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Overlay Color', 'aasana' ),
				"param_name"	=> "overlay_color",
				"value"			=> $theme_color,
			),	
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Number', 'aasana' ),
				"description"	=> esc_html__( 'Integer', 'aasana' ),
				"param_name"	=> "number",
				"value"			=> "356",
				"save_always"	=> true
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Speed', 'aasana' ),
				"description"	=> esc_html__( 'Integer', 'aasana' ),
				"param_name"	=> "speed",
				"value"			=> "2000",
				"save_always"	=> true
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Module Alignment', 'aasana' ),
				"param_name"	=> "module_alignment",
				"value"			=> array(
					esc_html__( 'Center', 'aasana' )	=> 'center',
					esc_html__( 'Left', 'aasana' )		=> 'left',
					esc_html__( 'Right', 'aasana' )	=> 'right'
				) 
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Paddings', 'aasana' ),
				"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
				"param_name"	=> "paddings",
				"value"			=> "43px 45px 75px 45px"
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
		"name"				=> esc_html__( 'CWS Milestone', 'aasana' ),
		"base"				=> "cws_sc_milestone",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Milestone extends WPBakeryShortCode {
	    }
	}
?>