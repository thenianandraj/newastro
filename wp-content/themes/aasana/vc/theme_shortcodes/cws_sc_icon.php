<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	$icon_params = cws_ext_icon_vc_sc_config_params ();

	$settings = array(
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Link', 'aasana' ),
			"param_name"	=> "url",
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "new_tab",
			"value"			=> array( esc_html__( 'Open in New Tab', 'aasana' ) => true )
		),
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Title', 'aasana' ),
			"param_name"	=> "title",
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Type', 'aasana' ),
			"param_name"	=> "type",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> array(
				esc_html__( 'Simple', 'aasana' )			=> 'simple',
				esc_html__( 'Bordered', 'aasana' )		=> 'bordered',
				esc_html__( 'Alternative', 'aasana' )		=> 'alt'
			) 
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Shape', 'aasana' ),
			"param_name"	=> "shape",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
								"element"	=> "type",
								"value"		=> array( "bordered", "alt" )
							),
			"value"			=> array(
				esc_html__( 'Square', 'aasana' ) => 'square',
				esc_html__( 'Round', 'aasana' ) => 'round'
			) 
		),
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Size Border', 'aasana' ),
			"param_name"	=> "size_b",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
					"element"	=> "type",
					"value"		=> "bordered"
			),
			"value"			=> "2px"
		),	
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Size Icon', 'aasana' ),
			"param_name"	=> "size",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> array(
				esc_html__( 'Medium', 'aasana' )		=> '3x',
				esc_html__( 'Small', 'aasana' )		=> '2x',
				esc_html__( 'Mini', 'aasana' )		=> 'lg',				
				esc_html__( 'Large', 'aasana' )		=> '4x',
				esc_html__( 'Extra Large', 'aasana' )	=> '5x'
			) 
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Aligning', 'aasana' ),
			"param_name"	=> "aligning",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> array(
				esc_html__( 'None', 'aasana' )		=> '',
				esc_html__( 'Left', 'aasana' )		=> 'left',
				esc_html__( 'Right', 'aasana' )		=> 'right',
				esc_html__( 'Center', 'aasana' )		=> 'center'
			) 
		),
		array(
			"type"			=> "checkbox",
			"param_name"	=> "customize_colors",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> array( esc_html__( 'Customize Colors', 'aasana' ) => true )				
		),		
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Fill Color', 'aasana' ),
			"param_name"	=> "fill_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> "#fff"
		),		
		array(
			"type"			=> "colorpicker",
			"heading"		=> esc_html__( 'Font Color', 'aasana' ),
			"param_name"	=> "font_color",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_colors",
				"not_empty"	=> true
			),
			"value"			=> AASANA_COLOR
		),				
		array(
			"type"			=> "checkbox",
			"param_name"	=> "customize_size",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> array( esc_html__( 'Customize Size', 'aasana' ) => true )				
		),
		array(
			"type"			=> "textfield",
			"heading"		=> esc_html__( 'Size Icon', 'aasana' ),
			"param_name"	=> "size_i",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "customize_size",
				"not_empty"	=> true
			),
			"value"			=> "15px"
		),	
		array(
			"type"			=> "checkbox",
			"param_name"	=> "add_hover",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"value"			=> array( esc_html__( 'Add Hover', 'aasana' ) => true )				
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> esc_html__( 'Hover Effect', 'aasana' ),
			"param_name"	=> "hover_i_style",
			"group"			=> esc_html__( "Styling", 'aasana' ),
			"dependency"	=> array(
				"element"	=> "add_hover",
				"not_empty"	=> true
				),
			"value"			=> array(
				esc_html__( 'Style 1', 'aasana' ) => 'style_1',
				esc_html__( 'Style 2', 'aasana' ) => 'style_2',
				esc_html__( 'Style 3', 'aasana' ) => 'style_3',
				) 
			),


		array(
			"type"				=> "textfield",
			"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
			"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
			"param_name"		=> "el_class",
			"value"				=> ""
		)
	);

	$params = array_merge( $icon_params, $settings );
	
	$args = array(
		"name"				=> esc_html__( 'CWS Icon', 'aasana' ),
		"base"				=> "cws_sc_icon",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	);
	vc_map( $args );

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Icon extends WPBakeryShortCode {
	    }
	}
?>