<?php
function cws_ext_merge_arrs ( $arrs = array() ){
	$r = array();
	for ( $i = 0; $i < count( $arrs ); $i++ ){
		$r = array_merge( $r, $arrs[$i] );
	}
	return $r;
}

function cws_ext_post_terms_str ( $pid = "", $tax = "", $delim = ", " ){
	$terms_str = "";
	$terms_arr = wp_get_post_terms( $pid, $tax, array( "fields" => "names" ) );
	if ( is_wp_error( $terms_arr ) ){
		return $terms_str;
	}
	else{
		$terms_str .= implode( $delim, $terms_arr );
	}
	return $terms_str;
}

/**/
/**/
/* Composer Icon Params Group */
/**/
function cws_ext_icon_vc_sc_config_params ( $dep_el = "", $dep_val = false, $value_el = false ){
	global $aasana_theme_funcs;
	$libs_param = array(
		'type' => 'dropdown',
		'heading' => __( 'Icon library', 'aasana' ),
		'value' => array(
			__( 'Font Awesome', 'aasana' ) => 'fontawesome',
			__( 'Open Iconic', 'aasana' ) => 'openiconic',
			__( 'Typicons', 'aasana' ) => 'typicons',
			__( 'Entypo', 'aasana' ) => 'entypo',
			__( 'Linecons', 'aasana' ) => 'linecons',
			__( 'Mono Social', 'aasana' ) => 'monosocial',
		),
		'param_name' => 'icon_lib',
		'description' => __( 'Select icon library.', 'aasana' ),
	);
	if ( !empty( $dep_el ) ){
		$libs_param['dependency'] = array(
			"element"	=> $dep_el
		);
		if ( is_bool( $dep_val ) ){
			$libs_param['dependency']['not_empty'] = $dep_val;
		}
		else{
			$libs_param['dependency']['value'] = $dep_val;
		}
		if(!empty($value_el)){
			$libs_param['dependency']['value'] = $value_el;
		}
	}
	$iconpickers = array(
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_fontawesome',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true,
				// default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'fontawesome',
			),
			'description' => __( 'Select icon from library.', 'aasana' ),
		),
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_openiconic',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'type' => 'openiconic',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'openiconic',
			),
			'description' => __( 'Select icon from library.', 'aasana' ),
		),
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_typicons',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'type' => 'typicons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'typicons',
			),
			'description' => __( 'Select icon from library.', 'aasana' ),
		),
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_entypo',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'type' => 'entypo',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'entypo',
			),
		),
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_linecons',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'type' => 'linecons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'linecons',
			),
			'description' => __( 'Select icon from library.', 'aasana' ),
		),
		array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_monosocial',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'type' => 'monosocial',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'monosocial',
			),
			'description' => __( 'Select icon from library.', 'aasana' ),
		)
	);

	$fi_icons = call_user_func('aasana' . '_get_all_flaticon_icons');
	$fi_firsticon = "";
	$fi_exists = is_array( $fi_icons ) && !empty( $fi_icons );
	$fi_lib_key = esc_html__( 'CWS Flaticons', 'aasana' );
	if ( $fi_exists ){
		$fi_firsticon = $fi_icons[0];
		$libs_param['value'][$fi_lib_key] = 'cws_flaticons';
		array_push( $iconpickers, array(
			'type' => 'iconpicker',
			'heading' => __( 'Icon', 'aasana' ),
			'param_name' => 'icon_cws_flaticons',
			'value' => '', // default value to backend editor admin_label
			'settings' => array(
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'type' => 'cws_flaticons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'icon_lib',
				'value' => 'cws_flaticons',
			),
			'description' => __( 'Select icon from library.', 'aasana' ),
		));
	}

	$svg_lib_key = esc_html__( 'CWS SVG', 'aasana' );
	$libs_param['value'][$svg_lib_key] = 'cws_svg';
	array_push( $iconpickers, array(
		"type"			=> "cws_svg",
		"heading"		=> esc_html__( 'SVG Icon', 'aasana' ),
		"param_name"	=> "icon_cws_svg",
		'dependency' => array(
			'element' => 'icon_lib',
			'value' => 'cws_svg',
		),
		'description' => __( 'Select icon from library.', 'aasana' ),
	));
	
	$params = array_merge( array( $libs_param ), $iconpickers );
	return $params;
}
/**/
/* \Composer Icon Params Group */
/**/
/**/
/* Get Selected Icons from Composer Attributes */
/**/
function cws_ext_vc_sc_get_icon ( $atts ){
	$defaults = array(
		'icon_lib' 				=> 'fontawesome',
		'icon_fontawesome'		=> '',
		'icon_openiconic'		=> '',
		'icon_typicons'			=> '',
		'icon_entypo'			=> '',
		'icon_linecons'			=> '',
		'icon_monosocial'		=> '',
		'icon_cws_flaticons'	=> '',
		'icon_cws_svg'		=> '',
	);
	$proc_atts 	= wp_parse_args( $atts, $defaults );
	$lib 		= $proc_atts['icon_lib'];
	$icon_key 	= "icon_$lib";
	$icon 		= isset( $atts[$icon_key] ) ? $atts[$icon_key] : "";
	return $icon;
}

function cws_render_builder_gradient_rules_hover( $options ) {
	extract(shortcode_atts(array(
		'cws_gradient_color_from' => "#000000",
		'cws_gradient_color_to' => '#0eecbd',
		'cws_gradient_type' => 'linear',
		'cws_gradient_angle' => '45',
		'cws_gradient_shape_variant_type' => 'simple',
		'cws_gradient_shape_type' => 'ellipse',
		'cws_gradient_size_keyword_type' => 'farthest-corner',
		'cws_gradient_size_type' => '',		
	), $options));

	//var_dump($options);

	$cws_gradient_color_from = isset($options['cws_bg_hover_gradient_color_from']) ? $options['cws_bg_hover_gradient_color_from'] : $cws_gradient_color_from;
	$cws_gradient_color_to = isset($options['cws_bg_hover_gradient_color_to']) ? $options['cws_bg_hover_gradient_color_to'] : $cws_gradient_color_to;
	$cws_gradient_type = isset($options['cws_bg_hover_gradient_type']) ? $options['cws_bg_hover_gradient_type'] : $cws_gradient_type;
	$cws_gradient_angle = isset($options['cws_bg_hover_gradient_angle']) ? $options['cws_bg_hover_gradient_angle'] : $cws_gradient_angle;
	
	$cws_gradient_shape_variant_type = isset($options['cws_bg_hover_gradient_shape_variant_type']) ? $options['cws_bg_hover_gradient_shape_variant_type'] : $cws_gradient_shape_variant_type;
	$cws_gradient_shape_type = isset($options['cws_bg_hover_gradient_shape_type']) ? $options['cws_bg_hover_gradient_shape_type'] : $cws_gradient_shape_type;
	$cws_gradient_size_keyword_type = isset($options['cws_bg_hover_gradient_size_keyword_type']) ? $options['cws_bg_hover_gradient_size_keyword_type'] : $cws_gradient_size_keyword_type;
	$cws_gradient_size_type = isset($options['cws_bg_hover_gradient_size_type']) ? $options['cws_bg_hover_gradient_size_type'] : $cws_gradient_size_type;
	
	$out = '';
	if ( $cws_gradient_type == 'linear' ) {
		$out .= "background: -webkit-linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
		$out .= "background: -o-linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
		$out .= "background: -moz-linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
		$out .= "background: linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
	}
	else if ( $cws_gradient_type == 'radial' ) {
		if ( $cws_gradient_shape_variant_type == 'simple' ) {
			$out .= "background: -webkit-radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -o-radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -moz-radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
		}
		else if ( $cws_gradient_shape_variant_type == 'extended' ) {
		
			$out .= "background: -webkit-radial-gradient(" . ( !empty( $cws_gradient_size_type ) ? " " . $cws_gradient_size_type . "," : "" ) . ( !empty( $cws_gradient_size_keyword_type ) ? " " . $cws_gradient_size_keyword_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -o-radial-gradient(" . ( !empty( $cws_gradient_size_type ) ? " " . $cws_gradient_size_type . "," : "" ) . ( !empty( $cws_gradient_size_keyword_type ) ? " " . $cws_gradient_size_keyword_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -moz-radial-gradient(" . ( !empty( $cws_gradient_size_type ) ? " " . $cws_gradient_size_type . "," : "" ) . ( !empty( $cws_gradient_size_keyword_type ) ? " " . $cws_gradient_size_keyword_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: radial-gradient(" . ( !empty( $cws_gradient_size_keyword_type ) && !empty( $cws_gradient_size_type ) ? " $cws_gradient_size_keyword_type at $cws_gradient_size_type" : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
		}
	}
	$out .= "border-color: transparent;-webkit-background-clip: border;-moz-background-clip: border;background-clip: border-box;-webkit-background-origin: border;-moz-background-origin: border;background-origin: border-box;";
	return preg_replace('/\s+/',' ', $out);
}

function cws_render_builder_gradient_rules( $options ) {
	extract(shortcode_atts(array(
		'cws_gradient_color_from' => "#000000",
		'cws_gradient_color_to' => '#0eecbd',
		'cws_gradient_type' => 'linear',
		'cws_gradient_angle' => '45',
		'cws_gradient_shape_variant_type' => 'simple',
		'cws_gradient_shape_type' => 'ellipse',
		'cws_gradient_size_keyword_type' => 'farthest-corner',
		'cws_gradient_size_type' => '',
	), $options));
	$out = '';
	if ( $cws_gradient_type == 'linear' ) {
		$out .= "background: -webkit-linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
		$out .= "background: -o-linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
		$out .= "background: -moz-linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
		$out .= "background: linear-gradient(" . $cws_gradient_angle . "deg, $cws_gradient_color_from, $cws_gradient_color_to);";
	}
	else if ( $cws_gradient_type == 'radial' ) {
		if ( $cws_gradient_shape_variant_type == 'simple' ) {
			$out .= "background: -webkit-radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -o-radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -moz-radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: radial-gradient(" . ( !empty( $cws_gradient_shape_type ) ? " " . $cws_gradient_shape_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
		}
		else if ( $cws_gradient_shape_variant_type == 'extended' ) {
		
			$out .= "background: -webkit-radial-gradient(" . ( !empty( $cws_gradient_size_type ) ? " " . $cws_gradient_size_type . "," : "" ) . ( !empty( $cws_gradient_size_keyword_type ) ? " " . $cws_gradient_size_keyword_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -o-radial-gradient(" . ( !empty( $cws_gradient_size_type ) ? " " . $cws_gradient_size_type . "," : "" ) . ( !empty( $cws_gradient_size_keyword_type ) ? " " . $cws_gradient_size_keyword_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: -moz-radial-gradient(" . ( !empty( $cws_gradient_size_type ) ? " " . $cws_gradient_size_type . "," : "" ) . ( !empty( $cws_gradient_size_keyword_type ) ? " " . $cws_gradient_size_keyword_type . "," : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
			$out .= "background: radial-gradient(" . ( !empty( $cws_gradient_size_keyword_type ) && !empty( $cws_gradient_size_type ) ? " $cws_gradient_size_keyword_type at $cws_gradient_size_type" : "" ) . " $cws_gradient_color_from, $cws_gradient_color_to);";
		}
	}
	$out .= "border-color: transparent;-webkit-background-clip: border;-moz-background-clip: border;background-clip: border-box;-webkit-background-origin: border;-moz-background-origin: border;background-origin: border-box;";
	return preg_replace('/\s+/',' ', $out);
}


?>