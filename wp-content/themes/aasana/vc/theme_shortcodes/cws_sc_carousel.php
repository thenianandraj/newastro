<?php
	// Map Shortcode in Visual Composer
	global $aasana_theme_funcs;
	vc_map( array(
		"name"				=> esc_html__( 'CWS Carousel', 'aasana' ),
		"base"				=> "cws_sc_carousel",
		'content_element' 	=> true,
		'as_parent'			=> array('only' => 'cws_sc_milestone, vc_column_text, cws_sc_vc_testimonial, cws_sc_button, cws_sc_msg_box, cws_sc_progress_bar, cws_sc_services, cws_sc_widget_text, cq_vc_hotspot, cws_sc_tips'),
		'category'			=> "By CWS",
		"weight"			=> 80,
		'js_view' 			=> 'VcColumnView',
		"params"			=> array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> esc_html__( 'Title', 'aasana' ),
				"param_name"	=> "title",
			),
			array(
				"type"			=> "dropdown",
				"heading"		=> esc_html__( 'Carousel Columns', 'aasana' ),
				"param_name"	=> "columns",
				"value"			=> array(
					esc_html__( "One", 'aasana' ) 	=> '1',
					esc_html__( "Two", 'aasana' )		=> '2',
					esc_html__( "Three", 'aasana' )	=> '3',
					esc_html__( "Four", 'aasana' )	=> '4'
				)		
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "bullets_nav",
				"value"			=> array( esc_html__( 'Add Navigation Bullets', 'aasana' ) => true )
			),			
			array(
				"type"			=> "checkbox",
				"param_name"	=> "arrows_nav",
				"value"			=> array( esc_html__( 'Add Navigation Arrows', 'aasana' ) => true )
			),
			array(
				"type"			=> "checkbox",
				"param_name"	=> "customize_colors",
				"value"			=> array( esc_html__( 'Edit Colors', 'aasana' ) => true )
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Arrows Color', 'aasana' ),
				"param_name"	=> "custom_arrow_color",
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ""
			),
			array(
				"type"			=> "colorpicker",
				"heading"		=> esc_html__( 'Bullets Color', 'aasana' ),
				"param_name"	=> "custom_pagination_color",
				"dependency"	=> array(
					"element"	=> "customize_colors",
					"not_empty"	=> true
				),
				"value"			=> ""
			),
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Navigation Margins Offset', 'aasana' ),
				"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
				"param_name"	=> "margins_navigation",
				"value"			=> "0px 0px 0px 0px"
			),			
			array(
				"type"			=> "textfield",
				"heading"		=> esc_html__( 'Pagination Margins Offset', 'aasana' ),
				"description"	=> esc_html__( '1, 2( top/bottom, left/right ) or 4, space separated, values with units', 'aasana' ),
				"param_name"	=> "margins_pagination",
				"value"			=> "0px 0px 0px 0px"
			),
		)
	));

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Carousel extends WPBakeryShortCodesContainer {
	    }
	}
?>