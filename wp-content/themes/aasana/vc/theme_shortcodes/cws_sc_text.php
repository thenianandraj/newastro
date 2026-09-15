<?php
	global $aasana_theme_funcs;
	$body_font_options = $aasana_theme_funcs->cws_get_option( 'body-font' );
	$body_font_color = esc_attr( $body_font_options['color'] );
	$subtitle_font_options = $aasana_theme_funcs->cws_get_option( 'subtitle_font' );
	$subtitle_font_color = empty($subtitle_font_options) ? '' : esc_attr( $subtitle_font_options['color'] );
	$heading_font_options = $aasana_theme_funcs->cws_get_option( 'header_font' );
	$heading_font_color = empty($heading_font_options ) ? '' : esc_attr( $heading_font_options['color'] );	
	$theme_color_helper = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme_color_helper' ) );	
	$icon_params 			= cws_ext_icon_vc_sc_config_params();	
	$params 			= cws_ext_merge_arrs( array(
		array(
			array(
				"type"			=> "textarea",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Title Alignment', 'aasana' ),
				"param_name"	=> "title_alignment",
				"value"			=> array(
					esc_html__( "Default", 'aasana' ) 		=> '',
					esc_html__( "Left", 'aasana' ) 		=> 'left',
					esc_html__( "Center", 'aasana' )		=> 'center',
					esc_html__( "Right", 'aasana' )		=> 'right'
				)	
			),
		),
		$icon_params,
		array(
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Icon Spacing', 'aasana' ),
				"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
				"param_name"	=> "margins",
				"value"			=> "0 0 0 0"
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Icon Position', 'aasana' ),
				"param_name"	=> "icon_position",
				"value"			=> array(
					esc_html__( "Default", 'aasana' ) 		=> '',
					esc_html__( "Left", 'aasana' ) 		=> 'left',
					esc_html__( "Right", 'aasana' )		=> 'right'
				)	
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Size', 'aasana' ),
				"param_name"	=> "size",
				"value"			=> array(
					esc_html__( 'Extra Large', 'aasana' )	=> '5x',
					esc_html__( 'Large', 'aasana' )		=> '4x',
					esc_html__( 'Small', 'aasana' )		=> '2x',
					esc_html__( 'Medium', 'aasana' )		=> '3x',
					esc_html__( 'Mini', 'aasana' )		=> 'lg',					
				) 
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_size",
				"value"			=> array( esc_html__( 'Customize Size Icon', 'aasana' ) => true )				
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Size Icon', 'aasana' ),
				"param_name"	=> "size_i",
				"dependency"	=> array(
					"element"	=> "customize_size",
					"not_empty"	=> true
				),
				"value"			=> "15px"
			),	
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_colors",
				"value"			=> array( esc_html__( 'Customize Colors', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Font Color', 'aasana' ),
				"param_name"	=> "custom_font_color",
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $body_font_color
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Title Color', 'aasana' ),
				"param_name"	=> "custom_title_color",
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $heading_font_color
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Icon Color', 'aasana' ),
				"param_name"	=> "custom_icon_color",
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> $theme_color_helper
			),
			array(
				"type"				=> "textfield",
				"heading"			=> esc_html__( 'Extra class name', 'aasana' ),
				"description"		=> esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'aasana' ),
				"param_name"		=> "el_class",
				"value"				=> ""
			),	
			array(
				"type"			=> "textarea_html",
				"heading"		=> esc_html__( 'Text', 'aasana' ),
				"param_name"	=> "content",
			),
			array(
				"type"			=> "css_editor",
				"param_name"	=> "custom_styles",
				"group"			=> esc_html__( "Styling", 'aasana' )
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_animation",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> array( esc_html__( 'Add Animation Icon', 'aasana' ) => true )				
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "add_line_animation",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_animation",
					"not_empty"	=> true
				),
				"value"			=> array( esc_html__( 'Add Line', 'aasana' ) => true )	
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Line Width', 'aasana' ),
				"param_name"	=> "line_width",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"dependency"	=> array(
					"element"	=> "add_line_animation",
					"not_empty"	=> true
				),
				"value"			=> '4px'
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Line Offset Top', 'aasana' ),
				"param_name"	=> "margins_line",
				"group"			=> esc_html__( "Styling", 'aasana' ),
				"value"			=> "60",
				"dependency"	=> array(
					"element"	=> "add_line_animation",
					"not_empty"	=> true
				),
			),
		)
	));
	vc_map( array(
		"name"				=> esc_html__( 'CWS Text', 'aasana' ),
		"base"				=> "cws_sc_text",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
		"params"			=> $params
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Text extends WPBakeryShortCode {
	    }
	}
?>