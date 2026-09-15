<?php
function cws_vc_shortcode_cws_classes_posts_grid ( $atts = array(), $content = "" ){
	$out = "";
	$theme_color = function_exists("cws_vc_shortcode_get_option") ? esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) ) : "";
	$defaults = array(
		'title'									=> '',
		'title_align'							=> 'left',
		'total_items_count'						=> '',
		'display_style'							=> 'grid',
		'layout'								=> 'def',
		'massonry'								=> false,
		'items_pp'								=> esc_html( get_option( 'posts_per_page' ) ),
		'paged'									=> 1,
		'tax'									=> '',
		'display_screen'						=> '',
		'addl_query_args'						=> array(),
		'el_class'								=> '',
		'crop_images'							=> '',
		'customize_colors'						=> '',
		'custom_color'							=> $theme_color,
		'font_color'							=> '',
		'chars_count'							=> '',
		'bg_color'								=> $theme_color,
		'full_width'							=> false,
		'navigation_carousel'					=> "",
		'auto_play_carousel'					=> "",
		'pagination_carousel'					=> "",
		'hover_bg_color'						=> "",
		'meta_key_value'						=> "",
		'pagination_grid'						=> 'none',
		'bg_hover_color'						=> '',
		'change_btn'							=> '',

		'cws_classes_hide_meta_override'		=> false,
		'cws_classes_hide_meta'					=> '',
		'title_btn'								=> 'Book Class',
		'hover_fill_color'						=> $theme_color,
		'cws_gradient_color_from'               => $theme_color,
		'cws_gradient_color_to'               	=> $theme_color,
		'cws_gradient_angle'					=> '360',
	);
	$proc_atts = shortcode_atts( $defaults, $atts );
	extract( $proc_atts );

	$terms = isset( $atts[ $tax . "_terms" ] ) ? $atts[ $tax . "_terms" ] : "";
	$section_id = uniqid( 'cws_classes_posts_grid_' );
	$ajax_data = array();
	$total_items_count = !empty( $total_items_count ) ? (int)$total_items_count : PHP_INT_MAX;
	$items_pp = !empty( $items_pp ) ? (int)$items_pp : esc_html( get_option( 'posts_per_page' ) );
	$paged = (int)$paged;

	$def_layout = function_exists("cws_vc_shortcode_get_option") ? cws_vc_shortcode_get_option( 'def_cws_classes_layout' ) : "";
	$def_layout = isset( $def_layout ) ? $def_layout : "2";
	$layout = ( empty( $layout ) || $layout === "def" ) ? $def_layout : $layout; 
	$massonry 			= (bool)$massonry;

	$cws_classes_hide_meta_override = !empty( $cws_classes_hide_meta_override ) ? true : false;
	$cws_classes_hide_meta = explode( ",", $cws_classes_hide_meta );
	$cws_classes_def_hide_meta = function_exists("cws_vc_shortcode_get_option") ? cws_vc_shortcode_get_option( 'def_cws_classes_data_to_hide' ) : "";
	$cws_classes_def_hide_meta  = is_array( $cws_classes_def_hide_meta ) ? $cws_classes_def_hide_meta : array();
	$post_hide_meta = $cws_classes_hide_meta_override ? $cws_classes_hide_meta : $cws_classes_def_hide_meta;
	$el_class = esc_attr( $el_class );
	$sb =  function_exists('cws_vc_shortcode_get_sidebars') ? cws_vc_shortcode_get_sidebars() : "";
	$sb_layout = isset( $sb['layout_class'] ) ? $sb['layout_class'] : '';	

	$terms = explode( ",", $terms );	
	$terms_temp = array();
	foreach ( $terms as $term ) {
		if ( !empty( $term ) ){
			array_push( $terms_temp, $term );
		}
	}
	$terms = $terms_temp;
	$all_terms = array();
	$all_terms_temp = !empty( $tax ) ? get_terms( $tax ) : array();
	$all_terms_temp = !is_wp_error( $all_terms_temp ) ? $all_terms_temp : array();
	foreach ( $all_terms_temp as $term ){
		array_push( $all_terms, $term->slug );
	}
	$terms = !empty( $terms ) ? $terms : $all_terms;
	$not_in = (1 == $paged) ? array() : get_option( 'sticky_posts' );
	$query_args = array('post_type'			=> 'cws_classes',
						'post_status'		=> 'publish',
						'post__not_in'		=> $not_in,
						);

	if(isset($_GET['filterStaff']) && !empty($_GET['filterStaff'])){
		$meta_key_value = $_GET['filterStaff'];
	}

	if(isset($tax) && $tax == 'staff'){
		$meta_key_value = $atts['titles'];
	}
	if(isset($meta_key_value) && !empty($meta_key_value)){
		$query_args['meta_query'] = array(
			array(
				'key' => 'cws_mb_post',
				'value'   => $meta_key_value,
				'compare'  => 'LIKE'
				),
		);		
	}

	if ( in_array( $display_style, array( 'grid', 'filter' ) ) ){
		$query_args['posts_per_page']		= $items_pp;
		$query_args['paged']		= $paged;
	}
	else{
		$query_args['nopaging']				= true;
		$query_args['posts_per_page']		= -1;
	}
	if ( !empty( $terms ) ){
		$query_args['tax_query'] = array(
			array(
				'taxonomy'		=> $tax,
				'field'			=> 'slug',
				'terms'			=> $terms
			)
		);
	}

	$full_width = isset($GLOBALS['cws_row_atts']) && !empty($GLOBALS['cws_row_atts']) ? $GLOBALS['cws_row_atts'] : "";
	$query_args['orderby'] 	= "menu_order date title";
	$query_args['order']	= "ASC";
	
	$query_args = array_merge( $query_args, $addl_query_args );
	$q = new WP_Query( $query_args );
	$found_posts = $q->found_posts;
	$requested_posts = $found_posts > $total_items_count ? $total_items_count : $found_posts;
	$max_paged = $found_posts > $total_items_count ? ceil( $total_items_count / $items_pp ) : ceil( $found_posts / $items_pp );
	$cols = in_array( $layout, array( 'medium', 'small' ) ) ? 1 : (int)$layout;
	$is_carousel = $display_style == 'carousel' && $requested_posts > $cols;
	wp_enqueue_script( 'fancybox' );
	$is_filter = in_array( $display_style, array( 'filter' ) ) && !empty( $terms ) ? true : false;
	$filter_vals = array();
	$use_pagination = in_array( $display_style, array( 'grid', 'filter' ) ) && $max_paged > 1;
	$pagination_grid = isset($pagination_grid) && !empty($pagination_grid) && $pagination_grid != 'none' ? $pagination_grid : '';
	$isotope_init = $display_style != 'carousel' ? " isotope_init" : "";
	$dynamic_content = $is_filter || $use_pagination;
	if ( $is_carousel ){
		wp_enqueue_script( 'owl_carousel' );
	}
	else if ( in_array( $layout, array( "2", "3", "4" ) ) || $dynamic_content ){
		wp_enqueue_script( 'isotope' );
	}
	if ( $dynamic_content ){
		wp_enqueue_script( 'owl_carousel' ); // for dynamically loaded gallery posts
		
	}
	wp_enqueue_script( 'imagesloaded' );
	ob_start ();
	$data_attr = '';
	if ( $is_carousel ){
		$data_attr .= isset($auto_play_carousel) && !empty($auto_play_carousel) ? ' auto_play_owl' : "";
		$data_attr .= isset($navigation_carousel) && !empty($navigation_carousel) ? ' navigation_owl' : "";
		$data_attr .= isset($pagination_carousel) && !empty($pagination_carousel) ? ' pagination_owl' : "";
	}
	$data_attr .= !empty($full_width) ? ' full_width_style' : ' wide_style_classes';
	/********/
	echo "<section id='$section_id' class='posts_grid".(!empty($display_screen) ? " display_sc_".$display_screen : " display_sc_1")." cws_classes_posts_grid{$isotope_init} ".(!empty($data_attr) ? $data_attr : "")." posts_grid_{$layout} posts_grid_{$display_style}" . ( $dynamic_content ? " dynamic_content" : "" ) . ( !empty( $el_class ) ? " $el_class" : "" ) . " clearfix'>";
		if ( $is_carousel ){
			echo "<div class='widget_header clearfix'>";
				echo !empty( $title ) ? "<h2 class='widgettitle'>" . esc_html( $title ) . "</h2>" : "";				
				echo "<div class='carousel_nav'>";
					echo "<span class='prev'>";
					echo "</span>";
					echo "<span class='next'>";
					echo "</span>";
				echo "</div>";
			echo "</div>";			
		}
		else{
			echo !empty( $title ) ? "<h2 class='widgettitle text_align{$title_align}'>" . esc_html( $title ) . "</h2>" : "";				
			if ( $is_filter && count( $terms ) > 1 ){
				foreach ( $terms as $term ) {
					if ( empty( $term ) ) continue;
					$term_obj = get_term_by( 'slug', $term, $tax );
					if ( empty( $term_obj ) ) continue;
					$term_name = $term_obj->name;
					$filter_vals[$term] = $term_name;
				}
				if ( $filter_vals > 1 ){
					echo "<nav class='nav cws_classes_nav posts_grid_nav text_align{$title_align}'>";
						echo "<ul class='dots'>";
						echo "<li class='cws_post_select_dots circle'></li>";
						echo "<li class='dot'>";
						echo "<a href class='nav_item cws_classes_nav_item posts_grid_nav_item active' data-nav-val='_all_'>";
						echo "<span class='title_nav_classes'><span class='txt_title'>" . esc_html__( 'All', 'cws-essentials' );
						echo "</span>";
						echo "</span>";
						echo "<span class='circle'></span>";
						echo "</a>";
						echo "</li>";
						foreach ( $filter_vals as $term_slug => $term_name ){
							echo "<li class='dot'>";
							echo "<a href class='nav_item cws_classes_nav_item posts_grid_nav_item' data-nav-val='" . esc_html( $term_slug ) . "'>";
							echo "<span class='title_nav_classes'>" . esc_html( $term_name );
							echo "</span>";
							echo "<span class='circle'></span>";
							echo "</a>";
							echo "</li>";
						}
						echo "</ul>";
						echo "<span class='magicline'></span>";						
					echo "</nav>";
				}
			}
		}

		echo "<div class='cws_vc_shortcode_wrapper".($massonry ? " layout-masonry" : (!$is_carousel ? " layout-isotope" : "") )."'>";
			echo "<div class='" . ( $is_carousel ? "cws_vc_shortcode_carousel classes_carousel grid-".( is_numeric( $layout ) ? $layout : "4"  ) : "cws_vc_shortcode_grid" . ( ( in_array( $layout, array( "2", "3", "4" ) ) || $dynamic_content ) ? " isotope" : "" ) ) . "'" . ( $is_carousel ? " data-cols='" . ( !is_numeric( $layout ) ? "1" : $layout ) . "'" : "" ) . ">";
				$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
					'post_type'						=> 'cws_classes',
					'layout'						=> $layout,
					'post_hide_meta'				=> $post_hide_meta,
					'massonry'						=> $massonry,
					'sb_layout'						=> $sb_layout,
					'crop_images'					=> $crop_images,
					'full_width'					=> $full_width,
					'customize_colors'				=> $customize_colors,
					'custom_color'					=> $custom_color,
					'font_color'					=> $font_color,
					'chars_count'					=> $chars_count,
					'bg_color'						=> $bg_color,
					'display_screen'				=> $display_screen,
					'hover_bg_color'				=> $hover_bg_color,
					'total_items_count'				=> $total_items_count,
					'proc_atts'						=> $proc_atts,
					'bg_hover_color'				=> $bg_hover_color,
					'hover_fill_color'				=> $hover_fill_color,
					'cws_gradient_color_from'       => $cws_gradient_color_from,
					'cws_gradient_color_to'         => $cws_gradient_color_to,
					'cws_gradient_angle'			=> $cws_gradient_angle,
					'change_btn'					=> $change_btn,
					'title_btn'						=> $title_btn,
					);
				if(!$is_carousel){
					echo "<div class='grid-sizer'></div>";
				}
				if ( function_exists( "cws_vc_shortcode_cws_classes_posts_grid_posts" ) ){
					call_user_func_array( "cws_vc_shortcode_cws_classes_posts_grid_posts", array( $q ) );
				}
				unset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] );
			echo "</div>";
			if ( $dynamic_content ){
				cws_vc_shortcode_loader_html();
			}
		echo "</div>";
		if ( $use_pagination ){
			if ( $pagination_grid == 'load_more' ){
				cws_vc_shortcode_load_more ();
			}
			else{
				cws_vc_shortcode_pagination ( $paged, $max_paged );
			}
		}
		if ( $dynamic_content ){
			$ajax_data['section_id']						= $section_id;
			$ajax_data['post_hide_meta']					= $post_hide_meta;
			$ajax_data['bg_color']							= $bg_color;	
			$ajax_data['chars_count']						= $chars_count;	
			$ajax_data['customize_colors']					= $customize_colors;
			$ajax_data['custom_color']						= $custom_color;
			$ajax_data['font_color']						= $font_color;
			$ajax_data['post_type']							= 'cws_classes';
			$ajax_data['layout']							= $layout;
			$ajax_data['massonry']							= $massonry;
			$ajax_data['sb_layout']							= $sb_layout;
			$ajax_data['total_items_count']					= $total_items_count;
			$ajax_data['items_pp']							= $items_pp;
			$ajax_data['page']								= $paged;
			$ajax_data['max_paged']							= $max_paged;
			$ajax_data['tax']								= $tax;
			$ajax_data['terms']								= $terms;
			$ajax_data['filter']							= $is_filter;
			$ajax_data['current_filter_val']				= '_all_';
			$ajax_data['addl_query_args']					= $addl_query_args;
			$ajax_data['crop_images']						=  $crop_images;
			$ajax_data['full_width']						=  $full_width;
			$ajax_data['hover_bg_color']					=  $hover_bg_color;
			$ajax_data['pagination_grid']					=  $pagination_grid;
			$ajax_data['proc_atts']							=  $proc_atts;
			$ajax_data['bg_hover_color']					=  $bg_hover_color;
			$ajax_data['hover_fill_color']					=  $hover_fill_color;
			$ajax_data['cws_gradient_color_from']			=  $cws_gradient_color_from;
			$ajax_data['cws_gradient_color_to']				=  $cws_gradient_color_to;
			$ajax_data['cws_gradient_angle']				=  $cws_gradient_angle;
			$ajax_data['change_btn']						=  $change_btn;
			$ajax_data['title_btn']							=  $title_btn;
			$ajax_data_str = json_encode( $ajax_data );
			echo "<form id='{$section_id}_data' class='ajax_data_form cws_classes_ajax_data_form posts_grid_ajax_data_form'>";
				echo "<input type='hidden' id='{$section_id}_ajax_data' class='ajax_data cws_classes_ajax_data posts_grid_ajax_data' name='{$section_id}_ajax_data' value='$ajax_data_str' />";
			echo "</form>";
		}
	echo "</section>";
	$out = ob_get_clean();
	return $out;
}

function cws_vc_shortcode_cws_classes_posts_grid_posts ( $q = null ){
	if ( !isset( $q ) ) return;
	$def_grid_atts = array(
					'layout'						=> '1',
					'post_hide_meta'	=> array(),
					'full_width'					=> '',
					'total_items_count'				=> PHP_INT_MAX
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$paged = $q->query_vars['paged'];
	if ( $paged == 0 && $total_items_count < $q->post_count ){
		$post_count = $total_items_count;
	}
	else{
		$ppp = $q->query_vars['posts_per_page'];
		$posts_left = $total_items_count - ( $paged - 1 ) * $ppp;
		$post_count = $posts_left < $ppp ? $posts_left : $q->post_count;
	}
	if ( $q->have_posts() ):
		ob_start();
		while( $q->have_posts() && $q->current_post < $post_count - 1 ):
			$q->the_post();
			cws_vc_shortcode_cws_classes_posts_grid_post ();
		endwhile;
		wp_reset_postdata();
		ob_end_flush();
	endif;				
}
function cws_vc_shortcode_get_cws_classes_thumbnail_dims ( $eq_thumb_height = false, $real_dims = array() ) {
	$def_atts = array(
					'layout'				=> '1',
					'sb_layout'				=> '',
					'full_width'			=> '',
					'massonry'				=> false
				);
	$atts = $def_atts;
	$single = false;
	if ( isset( $GLOBALS['cws_vc_shortcode_single_post_atts'] ) ){
		$atts = $GLOBALS['cws_vc_shortcode_single_post_atts'];
		$single = true;
	}
	else if ( isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ){
		$atts = $GLOBALS['cws_vc_shortcode_posts_grid_atts'];
	}
	extract( $atts );
	
	$pid = get_the_id();
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$enable_hover = isset( $post_meta['enable_hover'] ) ? $post_meta['enable_hover'] : false;

	$sb_layout_isotope 		= isset( $post_meta['isotope_layout'] ) ? $post_meta['isotope_layout'] : false;
	$cell_count = isset( $post_meta['isotope_count'] ) ? $post_meta['isotope_count'] : false;

	$massonry = isset($massonry) && !empty($massonry) && $massonry !== 'false' ? true : false;

	$full_width = isset($full_width) && !empty($full_width) ? true : false;
	$dims = array( 'width' => 0, 'height' => 0 );
	if ($single){
		if ( empty( $sb_layout ) ){
			if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 1170 ) ) || $eq_thumb_height ){
				$dims['width'] = 1170;
			}
		}
		else if ( $sb_layout === "single" ){
			if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 870 ) ) || $eq_thumb_height ){
				$dims['width'] = 870;
			}
		}
		else if ( $sb_layout === "double" ){
			if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 570 ) ) || $eq_thumb_height ){
				$dims['width'] = 570;
			}
		}
	}else if ($full_width){
		switch ($layout){
			case "1":	
				$dims['width'] = 1920;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 1080;
				}		
				break;
			case '2':
				$dims['width'] = 1000;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 208;
				}		
				break;
			case '3':
				$dims['width'] = 750;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 208;
				}		
				break;
			case '4':
				$dims['width'] = 500;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 152;
				}
				break;
		}
	} 
	else{
		switch ($layout){
			case "1":
				if ( empty( $sb_layout ) ){
					$dims['width'] = 1170;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 979;
					}	
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 770;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 644;
					}		
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 370;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 310;
					}	
				}
				break;
			case '2':
				if ( empty( $sb_layout ) ){
					$dims['width'] = 570;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 477;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 370;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 310;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 170;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 142;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}
				break;
			case '3':
				if ( empty( $sb_layout ) ){
					$dims['width'] = 370;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 370;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 236;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 197;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 123;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 103;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}			
				break;
			case '4':
				if ( empty( $sb_layout ) ){
					$dims['width'] = 270;

					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 226;
					}
					else{
						$dims['height'] = $dims['width'];
					}	
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 170;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 142;
					}
					else{
						$dims['height'] = $dims['width'];
					}		
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 70;
					if ( !$massonry || !isset( $real_dims['height'] ) ){
						$dims['height'] = 58;
					}	
					else{
						$dims['height'] = $dims['width'];
					}	
				}			
				break;
		}
	}
	return $dims;
}
function cws_vc_shortcode_get_cws_classes_chars_count ( $cols = null ){
	$sb_layout = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts']['sb_layout'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts']['sb_layout'] : "";
	$number = 155;
	switch ( $cols ){
		case '1':
			if ( $sb_layout === "single" ){
				$number = 140;
			}
			else{
				$number = 280;
			}
			break;
		case '2':
			if ( $sb_layout === "single" ){
				$number = 60;
			}
			else{
				$number = 110;				
			}
			break;
		case '3':
			if ( $sb_layout === "single" ){
				$number = 26;	
			}
			else{
				$number = 60;					
			}
			break;
		case '4':
			if ( $sb_layout === "single" ){
				$number = 16;
			}
			else{
				$number = 40;				
			}
			break;
	}
	return $number;
}
function cws_vc_shortcode_cws_classes_posts_grid_post (){
	$def_grid_atts = array(
		'layout'						=> '1',
		'post_hide_meta'						=> array(),
	);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();
	$pid 		= get_the_id();
	$uniq_pid 	= uniqid( "cws_classes_post_" );
	$post_url = esc_url(get_the_permalink());
	$post_meta 	= get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta 	= isset( $post_meta[0] ) ? $post_meta[0] : array();
	$our_staff = isset( $post_meta['our_staff'] ) ? $post_meta['our_staff'] : "";
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
	$show_staff = isset( $post_meta['show_staff'] ) ? $post_meta['show_staff']: false;
	$date_events = isset( $post_meta['date_events'] ) ? $post_meta['date_events']: false;
	$add_btn = isset( $post_meta['add_btn'] ) ? $post_meta['add_btn']: false;
	$title_btn_post = isset( $post_meta['title_btn'] ) ? $post_meta['title_btn']: "";
	$time_events = isset( $post_meta['time_events'] ) && !in_array( 'time_events', $post_hide_meta ) ? $post_meta['time_events']: false;
	$destinations = isset( $post_meta['destinations'] ) && !in_array( 'venue_events', $post_hide_meta ) ? $post_meta['destinations']: false;

	$staff_thumbnail_props = array();
	if (!empty($our_staff)){
		foreach ($our_staff as $key => $value) {
			$id = array('id' => $value);
			if(has_post_thumbnail($value)){
				$staff_img = wp_get_attachment_image_src(get_post_thumbnail_id( $value ),'full');
				$staff_thumbnail_props[] = !empty($staff_img) ? wp_get_attachment_image_src(get_post_thumbnail_id( $value ),'full') + $id : array();
			} else {
				$staff_thumbnail_props[] = array();
			}
		}		
	}
	$link_url = '';

	$work_days = isset($post_meta['work_days_group']) ? $post_meta['work_days_group'] : "";
	$link_to 	= isset( $post_meta['link_to'] ) ? $post_meta['link_to'] : "";
	$link_custom_url 	= isset( $post_meta['link_custom_url'] ) ? $post_meta['link_custom_url'] : "";
	$sb_layout_isotope 		= isset( $post_meta['isotope_layout'] ) ? $post_meta['isotope_layout'] : false;
	$cell_count = isset( $post_meta['isotope_count'] ) ? $post_meta['isotope_count'] : false;

	$link_custom_url	= esc_url( $link_custom_url );
	$fancybox_frame 	= ( $link_to == 'custom_url' && !empty( $link_custom_url ) ) || ( $link_to == 'post' && !empty( $post_url ) );
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';	
	$link_atts = "";
	$link_url = "";
	switch( $link_to ){
		case "post":
			$link_url = $post_url;
			break;
		case "custom_url":
			$link_url = $link_custom_url;
			break;
	}


	$hover_zoom		= !empty( $link_url ); 	
	$link_icon 		= 'cws_fa flaticon-link-symbol';
	$link_class 	= "post_link cws_classes_post_link posts_grid_post_link";
	$link_class 	.= !empty($add_btn) && !empty($link_url) && $display_screen == 'style_2' && !in_array( 'read_more', $post_hide_meta ) ? " link_btn" : "";
	$link_atts 		.= 	!empty( $link_class ) ? " class='$link_class'" : "";
	$link_atts 		.= 	!empty( $link_url ) ? " href='" . esc_url( $link_url ) . "'" : "";

	ob_start();
	if(!empty($add_btn) && !empty($link_url) ){	
		if ( !in_array( 'read_more', $post_hide_meta ) ){
			if(!empty($change_btn) ){
				echo "<a{$link_atts}>".$title_btn."</a>";
			}else{
				echo "<a{$link_atts}>".$title_btn_post."</a>";
			}
			
		}
	}
	$link_btn = ob_get_clean();
	ob_start();
	ob_start();
		//cws_vc_shortcode_cws_classes_posts_grid_post_title ();
		cws_vc_shortcode_cws_classes_posts_grid_post_terms ();	
	$prim_post_data = ob_get_clean();
	if ( !empty( $prim_post_data ) ){
		echo "<div class='prim_post_data'>";
			echo $prim_post_data;
		echo "</div>";
	}
	ob_start();
	cws_vc_shortcode_cws_classes_posts_grid_post_title($link_url, $link_atts);
	cws_vc_shortcode_cws_classes_posts_grid_post_content ();
	$sec_post_data = ob_get_clean();
	ob_start();
	if($display_screen == 'style_3'){
		echo "<div class='wrap_footer_classes'>";
		if(!empty($date_events)){
			echo "<div class='post_date_meta'>";
				echo esc_html($date_events);
			echo "</div>";		
		}
	}
	if ( !in_array( 'teach', $post_hide_meta ) ){
		if(!empty($staff_thumbnail_props) && !empty($staff_thumbnail_props[0]['id'])){
			echo "<div class='staff_classes".(count($staff_thumbnail_props) > 1 ? ' tooltip' : "")."'>";
				foreach ($staff_thumbnail_props as $key => $value) {
					if($display_screen == 'style_3'){
						echo  "<span class='staff_posts_wrapper'>";
					}
					$post_url = esc_url(get_the_permalink($value['id']));
					$thumbnail_staff = !empty( $value ) ? $value[0] : '';
					$src_img = cws_print_img_html(array('src' => $thumbnail_staff), array( 'width' => 70, 'height' => 70, 'crop' => true ) );
					if($display_screen == 'style_3'){
						$src_img = cws_print_img_html(array('src' => $thumbnail_staff), array( 'width' => 44, 'height' => 44, 'crop' => true ) );
					}
					echo  "<span class='thumb_staff_posts'>";
					echo $show_staff && !is_single() ? "<a href='".$post_url."'>" : "";
					echo "<img {$src_img} alt = '" . get_post_meta( get_post_thumbnail_id( $value['id'] ), '_wp_attachment_image_alt', true ) . "' />";		
					echo $show_staff && !is_single() ? "</a>" : "";
					echo "</span>";
					if($display_screen == 'style_3'){
						echo "<span class='thumb_staff_posts_title'>";
						if(count($staff_thumbnail_props) <= 1){
							echo esc_html__('by ', 'cws-essentials');
						}
						echo $show_staff && !is_single() ? "<a href='".$post_url."'>" : "";
						echo esc_html( get_the_title($value['id']) );
						echo $show_staff && !is_single() ? "</a>" : "";
						echo "</span>";
					echo "</span>";
					}
				}
			echo "</div>";
		}
	}
	if($display_screen == 'style_3'){
		echo "</div>";
	}
	$staff_thumb = ob_get_clean();
	ob_start();
	if(!empty($work_days)){
		if ( !in_array( 'working_days', $post_hide_meta ) ){
			echo "<div class='working_day_classes'>";
				echo "<ul class='tabs_classes' role='tablist'>";
				foreach ($work_days as $key => $value) {			
					echo "<li role='tab' tabindex='".$key."'><span class='tabs-item'><span>".$value['title']."</span></span></li>";
				}
				echo "</ul>";
				echo '<div class="tab">';
					foreach ($work_days as $key => $value) {	
						echo "<div data-key-id='tab-".$key."' class='tab_content_classes tab_sections' role='tabpanel' tabindex='".$key."'>";
						echo $value['from']. " - " .$value['to'];
						echo "</div>";
					}	
				echo '</div>';
			echo "</div>";
		}
	}
	$work_days = ob_get_clean();
	
	if(!empty($display_screen)){
		if($display_screen == 'style_2'){
			if ( !empty( $staff_thumb ) ){
				echo "<div class='staff_thumb_data'>";
					echo $staff_thumb;
				echo "</div>";
			}				
			if ( !empty( $sec_post_data ) ){
				echo "<div class='sec_post_data'>";
					echo $sec_post_data;
				echo "</div>";
			}				
		}
		if($display_screen == 'style_3'){
			if ( !empty( $sec_post_data ) ){
				echo "<div class='sec_post_data'>";
					echo $sec_post_data;
				echo "</div>";
			}	
			if ( !empty( $staff_thumb ) ){
				echo "<div class='staff_thumb_data'>";
					echo $staff_thumb;
				echo "</div>";
			}	
			if ( !empty( $work_days ) ){
				echo "<div class='work_days_data'>";
					echo $work_days;
				echo "</div>";
			}	
			if(!empty($link_btn)){
				echo $link_btn;
			}			
		}
	}
	else{
		if(is_single()){
			if(!empty($date_events)){
				echo "<div class='post_date_meta'>";
					echo esc_html($date_events);
				echo "</div>";		
			}
		}
		if ( !empty( $sec_post_data ) ){
			echo "<div class='sec_post_data'>";
				echo $sec_post_data;
			echo "</div>";
		}	
		if ( !empty( $staff_thumb ) ){
			echo "<div class='staff_thumb_data'>";
				echo $staff_thumb;
			echo "</div>";
		}	
		if ( !empty( $work_days ) ){
			echo "<div class='work_days_data'>";
				echo $work_days;
			echo "</div>";
		}	
		if(is_single()){
			if(!empty($time_events)){
				echo "<div class='post_time_meta'>";
					echo esc_html($time_events);
				echo "</div>";		
			}
		}		
		if(is_single()){
			if(!empty($destinations)){
				echo "<div class='post_destinations_meta'>";
					echo esc_html($destinations);
				echo "</div>";		
			}
		}
		if(!empty($link_btn)){
			echo $link_btn;
		}
	}


	$post_data 		= ob_get_clean();
	$dims = get_thumb_dims();
	$styles = "";
		ob_start();
	if ( $customize_colors ){

		if(!empty($custom_color)){
		echo "#{$uniq_pid} .cws_classes_post_title{
			color:$custom_color;
			} ";
		echo "#{$uniq_pid} .working_day_classes{
			color:$custom_color;
			} ";
		echo "#{$uniq_pid} .tabs_classes li{
			background: $custom_color;
			} ";
		echo "#{$uniq_pid} .wrap_footer_classes{
			background: $custom_color;
			} ";
		echo "#{$uniq_pid} .staff_classes.tooltip .thumb_staff_posts_title{
			background: ".cws_Hex2RGBA($custom_color,.7).";
			} ";
		echo "#{$uniq_pid} .staff_classes.tooltip .thumb_staff_posts_title:after{
			border-color:".cws_Hex2RGBA($custom_color,.7)." transparent transparent  transparent;
			} ";
		}
		

		if($bg_hover_color == 'color' && !empty($hover_fill_color)){
		echo "#{$uniq_pid} .thumb_staff_posts{
			background:".$hover_fill_color.";
			} ";
		}			
		if($bg_hover_color == 'gradient'){
		echo "#{$uniq_pid} .thumb_staff_posts{
			background:".(function_exists('cws_render_builder_gradient_rules') ? cws_render_builder_gradient_rules($grid_atts['proc_atts']) : "").";
			} ";
			
		}	


		if(!empty($custom_color) && $display_screen != 'style_2'){
		echo "#{$uniq_pid} .tab_content_classes{
			color:$custom_color;
			} ";
		echo "#{$uniq_pid} .cws_classes_post_wrapper > .cws_classes_post_link{
			color:$custom_color;
			} ";
		echo "#{$uniq_pid} .cws_classes_post_wrapper > .cws_classes_post_link:before{
			border-color:$custom_color;
			} ";

		echo "#{$uniq_pid} .cws_classes_post_wrapper > .cws_classes_post_link:hover{
			color:#fff;
			background:$custom_color;
			border-color:$custom_color;
			} ";

		}
		if(!empty($hover_bg_color)){
		echo "#{$uniq_pid} .post_wrapper.cws_classes_post_wrapper .post_media:after{
				background: $hover_bg_color;
		} ";
		echo "#{$uniq_pid} .post_media > a.link_btn:hover{
				color: $custom_color;
		} ";
		
		}	
		if(!empty($bg_color)){
			echo "#{$uniq_pid} .posts_grid_post_wrapper{
			background: $bg_color;
			} ";
		}		
	}
	if($display_screen == 'style_2'){
		echo "#{$uniq_pid} .cws_classes_post_wrapper .post_media .pic img{
			min-width: ".$dims['width']."px;
		} ";
	}
	

	$styles = ob_get_clean();

	echo "<article id='{$uniq_pid}' class='item post cws_classes_post posts_grid_post" . ( $hover_zoom ? " hover_zoom" : "" ) . ( $sb_layout_isotope ? " layout-masonry-".$sb_layout_isotope : "" ) . ( $cell_count ? " layout-cell-".$cell_count : "" ). " clearfix'".($cell_count ? "data-layout-cell='".$cell_count."'" : "").">";
		if ( !empty( $styles ) ){
			echo "<style id='{$uniq_pid}_hover_style' type='text/css'>";
				echo $styles;
				//Cws_shortcode_css()->enqueue_cws_css($styles);
			echo "</style>";
		}
		echo "<div class='post_wrapper cws_classes_post_wrapper posts_grid_post_wrapper'>";
			cws_vc_shortcode_cws_classes_posts_grid_post_media ($link_url, $link_atts, $link_btn);
			if($display_screen == 'style_2'){
				if ( !empty( $work_days ) ){
					echo "<div class='work_days_data'>";
						echo $work_days;
					echo "</div>";
				}	
			}
			echo $post_data;
		echo "</div>";
	echo "</article>";
}
function get_thumb_dims(){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_url = esc_url(get_the_permalink());

	$post_meta 	= get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta 	= isset( $post_meta[0] ) ? $post_meta[0] : array();
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];

	$thumbnail_dims = cws_vc_shortcode_get_cws_classes_thumbnail_dims( false, $real_thumbnail_dims );
	return $thumbnail_dims;
}

function cws_vc_shortcode_cws_classes_posts_grid_post_media ($link_url = null, $link_atts = null, $link_btn = null){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_url = esc_url(get_the_permalink());

	$post_meta 	= get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta 	= isset( $post_meta[0] ) ? $post_meta[0] : array();
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];

	$thumbnail_dims = cws_vc_shortcode_get_cws_classes_thumbnail_dims( false, $real_thumbnail_dims );
	if(isset($crop_images) && !empty($crop_images)){
		$thumbnail_dims['crop'] = true;
	}
	$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
	$retina_thumb_exists = false;
	$retina_thumb_url = "";	
	if ( isset( $thumb_obj[3]) && is_array($thumb_obj[3])){
		extract( $thumb_obj[3] );
	}		
	$retina_thumb_url = esc_attr($retina_thumb_url);
	if ( !empty( $thumb_url ) ){	

		?>
		<div class="post_media  cws_classes_post_media posts_grid_post_media">
			<?php
			echo "<div class='pic'>";
			if ( $retina_thumb_exists ) {			
				echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
			}
			else{
				echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
				
			}
			echo "</div>";
	}	
			if(!empty( $link_url )){
				if(!empty($link_btn) && !empty($display_screen) && $display_screen == 'style_2'){
					echo $link_btn;
				}else{
					echo "<a{$link_atts}></a>";
				}
				
			}
			?>
		</div>
		<?php
}

function cws_vc_shortcode_title ($pid_ajax = null){
	$title = get_the_title($pid_ajax);
	echo !empty( $title ) ?	"<h3 class='post_title cws_classes_post_title posts_grid_post_title'>$title</h3>" : "";	
}

function cws_vc_shortcode_cws_classes_posts_grid_post_title ($link_url = null, $link_atts = null){
	$pid = get_the_id ();
	$def_grid_atts = array(
					'layout'						=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_meta 	= get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta 	= isset( $post_meta[0] ) ? $post_meta[0] : array();
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;

	if( ! (is_single() || is_archive())){
		if ( !in_array( 'title', $post_hide_meta ) ){
			echo "<div class='prim_post_data_title'>";
			$title = get_the_title($pid);
			if(!empty( $link_url )){
				echo "<a{$link_atts}>";
			}
			echo !empty( $title ) ?	"<h3 class='post_title cws_classes_post_title posts_grid_post_title'>$title</h3>" : "";	
			if(!empty( $link_url )){
				echo "</a>";
			}	
			echo "</div>";
		}	
	}	
	if(is_single() || is_archive()){
		$post_url = esc_url(get_the_permalink());
		$title = get_the_title($pid);
		echo !empty( $title ) ?	"<h3 class='post_title cws_classes_post_title posts_grid_post_title'><a href='".$post_url."'>$title</a></h3>" : "";	
	}
}

function cws_vc_shortcode_cws_classes_posts_grid_post_content (){
	$pid = get_the_id ();
	$post = get_post( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );

	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();

	$chars_count = isset($chars_count) && !empty( $chars_count ) ? $chars_count : cws_vc_shortcode_get_cws_classes_chars_count($layout);
	$content = $proc_content = $excerpt = $proc_excerpt = "";

	$content = $post->post_content;
	$excerpt = $post->post_excerpt;
	if ( !empty( $excerpt ) ){
		$proc_content = get_the_excerpt();
	}
	else if ( strpos( (string) $content, '<!--more-->' ) ){
		$proc_content = get_the_content( "" );
	}
	else if ( !empty( $content ) && !empty( $chars_count ) ){
		$proc_content = get_the_content( "" );
		$proc_content = trim( preg_replace( '/[\s]{2,}/u', ' ', strip_shortcodes( strip_tags( $proc_content ) ) ) );
		$chars_count = (int)$chars_count;
		$proc_content = mb_substr( $proc_content, 0, $chars_count );
	}
	else{
		$proc_content = get_the_content( "" );		
	}
	if(!in_array( 'excerpt', $post_hide_meta )){
		echo "<div class='post_content cws_classes_post_content posts_grid_post_content'>";	
			echo apply_filters( 'the_content', $proc_content );
		echo "</div>";		
	}
}
function cws_vc_shortcode_cws_classes_posts_grid_post_terms (){
	$pid = get_the_id ();
	$def_grid_atts = array(
					'layout'						=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();


	if (! in_array( 'cats', $post_hide_meta ) ){
		$p_category_terms = wp_get_post_terms( $pid, 'cws_classes_cat' );
		$p_cats = "";
		for ( $i=0; $i < count( $p_category_terms ); $i++ ){
			$p_category_term = $p_category_terms[$i];
			$p_cat_permalink = get_term_link( $p_category_term->term_id, 'cws_classes_cat' );
			$p_cat_name = $p_category_term->name;
			$p_cats .= "<a href='$p_cat_permalink'>$p_cat_name</a>";
			$p_cats .= $i < count( $p_category_terms ) - 1 ? esc_html__( "&#x2c;&#x20;", 'cws-essentials' ) : "";
		}
		echo !empty($p_cats) ? "<div class='post_terms cws_classes_post_terms posts_grid_post_terms'>
			{$p_cats}
		</div>" : "";		
	}			
	

}
function cws_vc_shortcode_cws_classes_single_post_post_media ($pid_ajax = null){
	$pid_ajax = !empty($pid_ajax) ? $pid_ajax : get_the_id ();
	$post_url = esc_url(get_the_permalink($pid_ajax));
	$post_meta = get_post_meta( ($pid_ajax ? $pid_ajax : get_the_ID()), 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$thumbnail_props = has_post_thumbnail( $pid_ajax ) ? wp_get_attachment_image_src(get_post_thumbnail_id( $pid_ajax ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];

	$thumbnail_dims = cws_vc_shortcode_get_cws_classes_thumbnail_dims( false, $real_thumbnail_dims );
	$crop_thumb = isset( $thumbnail_dims['width'] ) && $thumbnail_dims['width'] > 0;
	$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
	$retina_thumb_exists = false;
	$retina_thumb_url = "";	
	if ( isset( $thumb_obj[3] ) ){
		extract( $thumb_obj[3] );
	}			
	$retina_thumb_url = esc_attr($retina_thumb_url);
	$enable_hover = isset( $post_meta['enable_hover'] ) ? $post_meta['enable_hover'] : false;
	$custom_url = isset( $post_meta['link_options_url'] ) ? $post_meta['link_options_url'] : "";
	$fancybox = isset( $post_meta['link_options_fancybox'] ) ? $post_meta['link_options_fancybox'] : false;
	if ( $fancybox ){
		wp_enqueue_script( 'fancybox' );
	}
	$link_atts = "";
	$link_url = $custom_url ? $custom_url : $thumbnail;
	$link_icon = $fancybox ? ( $custom_url ? 'magic' : 'plus' ) : 'share';
	$link_class = $fancybox ? "fancy fa fa-{$link_icon}" : "fa fa-{$link_icon}";
	$link_atts .= !empty( $link_class ) ? " class='$link_class'" : "";
	$link_atts .= !empty( $link_url ) ? " href='$link_url'" : "";
	$link_atts .= !$fancybox ? " target='_blank'" : "";
	$link_atts .= $fancybox && $custom_url ? " data-fancybox-type='iframe'" : "";
	if ( !empty( $thumb_url ) ){
	?>
		<div class="post_media cws_classes_post_media post_single_post_media">
			<?php
				echo "<div class='pic" . ( !$enable_hover || ( !$crop_thumb && !$custom_url ) ? " wth_hover" : "" ) . "'>";
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid_ajax ), '_wp_attachment_image_alt', true ) . "' />";
					}
					else{
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid_ajax ), '_wp_attachment_image_alt', true ) . "' />";
					}
					if ( $enable_hover && ( $crop_thumb || $custom_url ) ){
						echo "<div class='hover-effect'></div>";
						echo "<div class='links'>
								<a{$link_atts}></a>
						</div>";
					}
				echo "</div>";
			?>
		</div>
	<?php
	$GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] = !empty( $thumb_url ) && !$crop_thumb;
	}
}
function cws_vc_shortcode_cws_classes_single_post_content ($pid = null){
	if(class_exists('WPBMap')){
		WPBMap::addAllMappedShortcodes();
	}
	
	$content =  apply_filters('the_content', get_post_field('post_content', $pid));
	if ( !empty( $content ) ){
		echo "<div class='post_content cws_classes_post_content post_single_post_content'>";
			echo $content;
		echo "</div>";
	}
}
function cws_vc_shortcode_cws_classes_single_post_terms ($pid = null){
	$pid = !empty($pid) ? $pid : get_the_id ();
	$out = "";
	$p_category_terms = wp_get_post_terms( $pid, 'cws_classes_cat' );
	$p_cats = "";
	for ( $i=0; $i < count( $p_category_terms ); $i++ ){
		$p_category_term = $p_category_terms[$i];
		$p_cat_permalink = get_term_link( $p_category_term->term_id, 'cws_classes_cat' );
		$p_cat_name = $p_category_term->name;
		$p_cat_name = esc_html( $p_cat_name );
		$p_cats .= "<a href='$p_cat_permalink'>$p_cat_name</a>";
		$p_cats .= $i < count( $p_category_terms ) - 1 ? esc_html__( ",&#x20;", 'cws-essentials' ) : "";
	}
	if ( !empty( $p_cats ) ){
		echo "<div class='post_terms cws_classes_post_terms post_single_post_terms'>";
		echo "<i class='fa fa-bookmark-o'></i>&#x20;{$p_cats}";
		echo "</div>";
	}
}

function cws_vc_shortcode_cws_classes_teacher($pid_ajax = null){

	$pid_ajax = !empty($pid_ajax) ? $pid_ajax : get_the_id ();
	$post_url = esc_url(get_the_permalink($pid_ajax));
	$post_meta = get_post_meta( ($pid_ajax ), 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$our_staff = isset( $post_meta['our_staff'] ) ? $post_meta['our_staff'] : "";	
	if(!empty($our_staff)){

		$staff_props = array();
		foreach ($our_staff as $key => $value) {
			$id = array('id' => $value);
			$staff_props[] = has_post_thumbnail($value) ? wp_get_attachment_image_src(get_post_thumbnail_id( $value ),'full') + $id : array();
		}
		echo "<div class='staff_classes_single'>";
			foreach ($staff_props as $key => $value) {

				$post_meta = get_post_meta( $value['id'], 'cws_mb_post' );
				$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
				$experience = isset( $post_meta['experience'] ) ? $post_meta['experience']: array();
				$social_group = isset( $post_meta['social_group'] ) ? $post_meta['social_group']: array();
				$icons = "";
				foreach ( $social_group as $social ) {
					$title = isset( $social['title'] ) ? $social['title'] : "";
					$icon = isset( $social['icon'] ) ? $social['icon'] : "";
					$url = isset( $social['url'] ) ? $social['url'] : "";
					if ( !empty( $icon ) && !empty( $url ) ){
						$icons .= "<a href='$url' target='_blank' class='{$icon}'" . ( !empty( $title ) ? " title='$title'" : "" ) . "></a>";
					}
				}
				echo  "<div class='staff_post_wrapper'>";
				
				echo "<div class='post_media single_media_classes'>";
				$post_url = esc_url(get_the_permalink($value['id']));
				$thumbnail_staff = !empty( $value ) ? $value[0] : '';
				$src_img = cws_print_img_html(array('src' => $thumbnail_staff), array( 'width' => 250, 'height' => 250, 'crop' => true ) );
				echo  "<span class='thumb_staff_single_posts'>";
				echo "<img {$src_img} alt = '" . get_post_meta( get_post_thumbnail_id( $value['id'] ), '_wp_attachment_image_alt', true ) . "' />";		
				echo "</span>";
				if ( !empty( $icons ) ){
					echo "<span class='post_social_links_classes cws_staff_post_social_links posts_grid_post_social_links'>";
						echo $icons;	
					echo "</span>";
				}
				echo "</div>";

				echo "<div class='post_content_single_classes'>";
				echo "<h4 class='staff_single_posts_classes'>";
				echo "<span class='thumb_staff_single_posts_title'>";
					echo esc_html( get_the_title($value['id']) );	
				echo "</span>";
				echo "</h4>";
				$post = get_post( $value['id'] );

				$content = $proc_content = $excerpt = $proc_excerpt = "";
				$content = $post->post_content;
				$excerpt = $post->post_excerpt;
				if ( !empty( $excerpt ) ){
					$proc_content = $post->post_excerpt;
				}
				else{
					$proc_content = $post->post_content;	
				}				
				echo !empty( $experience ) ? "<span class='post_excerpt cws_classes_post_content single_posts_classes posts_grid_post_excerpt'><div class='experience'><span>".(esc_html__('Experience', 'cws-essentials')).":</span><span>".esc_html($experience)."</span></div></span>" : "";

				echo !empty( $proc_content ) ? "<span class='post_content cws_classes_post_content single_posts_classes posts_grid_post_content'>".(apply_filters( 'the_content', $proc_content ))."</span>" : "";				
					
				$post_url = esc_url(get_the_permalink($value['id']));
				echo "<a href='".$post_url."' class='permalink_author_post_classes'>";
					echo esc_html__('All Events', 'cws-essentials');
				echo "</a>";
				echo "</div>";
				
				echo "</div>";
				
			}
		echo "</div>";
	}
}