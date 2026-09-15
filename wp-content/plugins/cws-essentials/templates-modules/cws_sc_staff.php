<?php

function cws_vc_shortcode_cws_staff_posts_grid ( $atts = array(), $content = "" ){
	$out = "";
	$theme_color 			= esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	$defaults = array(
		'title'									=> '',
		'title_align'							=> 'left',
		'total_items_count'					    => '4',
		'hide_data_override'					=> false,
		'data_to_hide'							=> '',
		'display_style'						    => 'grid',
		'layout'								=> 'def',
		'massonry'								=> false,
		'items_pp'								=> esc_html( get_option( 'posts_per_page' ) ),
		'paged'									=> 1,
		'tax'										=> '',
		'addl_query_args'						=> array(),
		'auto_play_carousel'                    => '',
		'navigation_carousel'                   => '',
		'pagination_carousel'                   => '',
		'change_btn'                  			=> '',
		'customize_colors'                  	=> '',
		'hover_fill_color'                  	=> $theme_color,
		'cws_gradient_color_from'               => $theme_color,
		'cws_gradient_color_to'               	=> $theme_color,
		'custom_title_color'                  	=> '',
		'custom_font_color'                  	=> '',
		'custom_social_color'                  	=> '',
		'custom_btn_color'                  	=> '',
		'bg_hover_color'                  		=> 'none',
		'title_btn'                   			=> 'read more',
		'el_class'								=> '',
		'cws_gradient_angle'					=> '360',
	);
	$proc_atts = shortcode_atts( $defaults, $atts );
	extract( $proc_atts );
	$terms = isset( $atts[ $tax . "_terms" ] ) ? $atts[ $tax . "_terms" ] : "";
	$section_id = uniqid( 'cws_staff_posts_grid_' );
	$ajax_data = array();
	$total_items_count = !empty( $total_items_count ) ? (int)$total_items_count : PHP_INT_MAX;
	$items_pp = !empty( $items_pp ) ? (int)$items_pp : esc_html( get_option( 'posts_per_page' ) );
	$paged = (int)$paged;
	$pid = get_the_id();
	$def_layout = function_exists('cws_vc_shortcode_get_option') ? cws_vc_shortcode_get_option( 'def_cws_staff_layout' ) : "";
	$def_layout = isset( $def_layout ) ? $def_layout : "4";
	$layout = ( empty( $layout ) || $layout === "def" ) ? $def_layout : $layout; 
	$hide_data_override = (bool)$hide_data_override;
	$massonry 			= (bool)$massonry;
	$data_to_hide = explode( ",", $data_to_hide );
	$def_data_to_hide = function_exists('cws_vc_shortcode_get_option') ? cws_vc_shortcode_get_option( 'def_cws_staff_data_to_hide' ) : "";
	$def_data_to_hide  = isset( $def_data_to_hide ) ? $def_data_to_hide : array();
	if(isset($def_data_to_hide[0]) && !empty($def_data_to_hide[0])){
		$def_data_to_hide = explode(",", $def_data_to_hide[0]);
	}

	$data_to_hide = $hide_data_override ? $data_to_hide : $def_data_to_hide;
	$el_class = esc_attr( $el_class );
	$sb = function_exists('cws_vc_shortcode_get_sidebars') ? cws_vc_shortcode_get_sidebars($pid) : "";
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
	$query_args = array('post_type'			=> 'cws_staff',
						'post_status'		=> 'publish',
						'post__not_in'		=> $not_in
						);
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
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
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
	$pagination_type = "pagination";
	if ( !$is_filter && in_array( $layout, array( '2', '3', '4' ) ) ){
		$pagination_type = "load_more";
	}
	$dynamic_content = $is_filter || $use_pagination;
	if ( $is_carousel ){
		wp_enqueue_script( 'owl_carousel' );
	}
	else if ( in_array( $layout, array( "2", "3", "4" ) ) || $dynamic_content ){
		wp_enqueue_script( 'isotope' );
	}
	if ( $dynamic_content ){
		wp_enqueue_script( 'owl_carousel' ); // for dynamically loaded gallery posts
		wp_enqueue_script( 'imagesloaded' );
	}

	$data_attr = '';
	if ( $is_carousel ){
		$data_attr .= isset($auto_play_carousel) && !empty($auto_play_carousel) ? ' auto_play_owl' : "";
		$data_attr .= isset($navigation_carousel) && !empty($navigation_carousel) ? ' navigation_owl' : "";
		$data_attr .= isset($pagination_carousel) && !empty($pagination_carousel) ? ' pagination_owl' : "";
	}
	ob_start();
	if(!empty($customize_colors)){

		if($bg_hover_color == 'color'){
			if(!empty($hover_fill_color)){
				echo "#{$section_id} .posts_grid_post:hover .cws_staff_photo.clickable .link_author:after{background: $hover_fill_color;}";
			}						
		}

		if(function_exists('cws_render_builder_gradient_rules') && $bg_hover_color == 'gradient'  ){
			echo "#{$section_id} .posts_grid_post:hover .cws_staff_photo.clickable .link_author:after{".cws_render_builder_gradient_rules($proc_atts)."}";
		}

		if($bg_hover_color == 'none' && !empty($clickable)){
			if(!empty($hover_fill_color)){
				echo "#{$section_id} .posts_grid_post:hover .cws_staff_photo .link_author:after{background: transparent;}";
			}						
		}

		if(!empty($custom_title_color)){
			echo "#{$section_id} .posts_grid_post.item .post_title a,
						#{$section_id} .posts_grid_post.item .post_title{color: $custom_title_color;}";
		}
		if(!empty($custom_font_color)){
			echo "#{$section_id} .post_content {color: $custom_font_color;}";
		}
		if(!empty($custom_social_color)){
			echo "#{$section_id} .post_social_links.cws_staff_post_social_links a {color: $custom_social_color;border:3px solid {$custom_social_color};}";
			echo "#{$section_id} .post_social_links.cws_staff_post_social_links a:hover {background: $custom_social_color;}";
			echo "#{$section_id} .post_social_links.cws_staff_post_social_links a:hover:after {background: $custom_social_color;}";
		}
		if(!empty($custom_btn_color)){
			echo "#{$section_id} .post_media .cws_staff_photo .btn_staff_details {color: $custom_btn_color;}";
		}
		if(!empty($custom_btn_color)){
			echo "#{$section_id} .post_media .cws_staff_photo .btn_staff_details {color: $custom_btn_color;border-color: $custom_btn_color}";
		}
	}
	$styles = ob_get_clean();
	$styles = json_encode($styles);

	ob_start ();
	/********/
	echo "<section id='$section_id' class='posts_grid render_styles cws_staff_posts_grid posts_grid_{$layout} posts_grid_{$display_style}" . ( $dynamic_content ? " dynamic_content" : "" ).(!empty($data_attr) ? $data_attr : "") . ( !empty( $el_class ) ? " $el_class" : "" ) . " clearfix'  data-style='".esc_attr($styles)."' >";
		if ( $is_carousel && !empty($navigation_carousel) ){
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
					echo "<nav class='nav cws_staff_nav posts_grid_nav text_align{$title_align}'>";
						echo "<ul class='dots'>";
						echo "<li class='cws_post_select_dots circle'></li>";
						echo "<li class='dot'>";
						echo "<a href class='nav_item cws_staff_nav_item posts_grid_nav_item active' data-nav-val='_all_'>";
						echo "<span class='title_nav_staff'><span class='txt_title'>" . esc_html__( 'All', 'cws-essentials' );
						echo "</span>";
						echo "</span>";
						echo "<span class='circle'></span>";
						echo "</a>";
						echo "</li>";
						foreach ( $filter_vals as $term_slug => $term_name ){
							echo "<li class='dot'>";
							echo "<a href class='nav_item cws_staff_nav_item posts_grid_nav_item' data-nav-val='" . esc_html( $term_slug ) . "'>";
							echo "<span class='title_nav_staff'><span class='txt_title'>" . esc_html( $term_name );
							echo "</span>";
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
		echo "<div class='cws_vc_shortcode_wrapper'>";
			echo "<div class='" . ( $is_carousel ? "cws_vc_shortcode_carousel" : "cws_vc_shortcode_grid" . ( ( in_array( $layout, array( "2", "3", "4" ) ) || $dynamic_content ) ? " isotope" : "" ) ) . "'" . ( $is_carousel ? " data-cols='" . ( !is_numeric( $layout ) ? "1" : $layout ) . "'" : "" ) . ">";
				$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
					'post_type'						=> 'cws_staff',
					'layout'						=> $layout,
					'title_btn'						=> $title_btn,
					'hover_fill_color'              => $hover_fill_color,
					'custom_title_color'            => $custom_title_color,
					'custom_font_color'             => $custom_font_color,
					'custom_social_color'           => $custom_social_color,
					'custom_btn_color'              => $custom_btn_color,
					'customize_colors'				=> $customize_colors,
					'change_btn'					=> $change_btn,
					'massonry'						=> $massonry,
					'sb_layout'						=> $sb_layout,
					'cws_staff_data_to_hide'		=> $data_to_hide,
					'bg_hover_color'				=> $bg_hover_color,
					'total_items_count'				=> $total_items_count,
					'proc_atts'						=> $proc_atts,
					'custom_title_color'			=> $custom_title_color,
					'cws_gradient_color_from'       => $cws_gradient_color_from,
					'cws_gradient_color_to'         => $cws_gradient_color_to,
					);
				if ( function_exists( "cws_vc_shortcode_cws_staff_posts_grid_posts" ) ){
					call_user_func_array( "cws_vc_shortcode_cws_staff_posts_grid_posts", array( $q ) );
				}
				unset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] );
			echo "</div>";
			if ( $dynamic_content ){
				cws_vc_shortcode_loader_html();
			}
		echo "</div>";
		if ( $use_pagination ){
			if ( $pagination_type == 'load_more' ){
				cws_vc_shortcode_load_more ();
			}
			else{
				cws_vc_shortcode_pagination ( $paged, $max_paged );
			}
		}
		if ( $dynamic_content ){
			$ajax_data['section_id']						= $section_id;
			$ajax_data['post_type']							= 'cws_staff';
			$ajax_data['cws_staff_data_to_hide']			= $data_to_hide;
			$ajax_data['layout']							= $layout;
			$ajax_data['massonry']							= $massonry;
			$ajax_data['sb_layout']							= $sb_layout;
			$ajax_data['total_items_count']					= $total_items_count;
			$ajax_data['items_pp']							= $items_pp;
			$ajax_data['page']								= $paged;
			$ajax_data['max_paged']							= $max_paged;
			$ajax_data['tax']								= $tax;
			$ajax_data['terms']								= $terms;
			$ajax_data['custom_title_color']				= $custom_title_color;
			$ajax_data['filter']							= $is_filter;
			$ajax_data['current_filter_val']				= '_all_';
			$ajax_data['addl_query_args']					= $addl_query_args;
			$ajax_data['bg_hover_color']					= $bg_hover_color;
			$ajax_data['proc_atts']							= $proc_atts;
			$ajax_data['custom_title_color']				= $custom_title_color;
			$ajax_data['cws_gradient_color_from']			= $cws_gradient_color_from;
			$ajax_data['cws_gradient_color_to']				= $cws_gradient_color_to;

			$ajax_data_str = json_encode( $ajax_data );
			echo "<form id='{$section_id}_data' class='ajax_data_form cws_staff_ajax_data_form posts_grid_ajax_data_form'>";
				echo "<input type='hidden' id='{$section_id}_ajax_data' class='ajax_data cws_staff_ajax_data posts_grid_ajax_data' name='{$section_id}_ajax_data' value='$ajax_data_str' />";
			echo "</form>";
		}
	echo "</section>";
	$out = ob_get_clean();
	return $out;
}

function cws_vc_shortcode_cws_staff_posts_grid_posts ( $q = null ){
	if ( !isset( $q ) ) return;
	$def_grid_atts = array(
					'layout'						=> '2',
					'cws_staff_data_to_hide'		=> array(),
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
			cws_vc_shortcode_cws_staff_posts_grid_post ();
		endwhile;
		wp_reset_postdata();
		ob_end_flush();
	endif;		
}

function cws_vc_shortcode_get_cws_staff_thumbnail_dims ( $real_dims = array() ) {
	$def_grid_atts = array(
					'layout'				=> '2',
					'sb_layout'				=> '',
				);
	$def_single_atts = array(
					'sb_layout'				=> '',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	$single_atts = isset( $GLOBALS['cws_vc_shortcode_single_post_atts'] ) ? $GLOBALS['cws_vc_shortcode_single_post_atts'] : $def_single_atts;
	$single = is_single();
	if ( $single ){
		extract( $single_atts );
	}
	else{
		extract( $grid_atts );
	}
	$dims = array( 'width' => 0, 'height' => 0 );
	if ($single){
		//if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 1170 ) ) ) {
			//$dims['width']	= 1170;
		//}
		$dims['width']		= 440;
	}
	else{
		$dims['width']		= 270;
		$dims['height']		= 270;
	}
	return $dims;
}

function cws_vc_shortcode_get_cws_staff_chars_count ( $cols = null ){
	$number = 155;
	switch ( $cols ){
		case '1':
			$number = 270;
			break;
		case '2':
			$number = 90;
			break;
		case '3':
			$number = 40;
			break;
	}
	return $number;
}

function cws_vc_shortcode_cws_staff_posts_grid_post (){
	$def_grid_atts = array(
					'layout'					=> '2',
					'cws_staff_data_to_hide'	=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$pid = get_the_id();
	$cws_staff_data_to_hide = $cws_staff_data_to_hide ? $cws_staff_data_to_hide : array();
	$item_id = uniqid( "cws_staff_post_" );
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$experience = isset( $post_meta['experience'] ) ? $post_meta['experience']: "";
	$email = isset( $post_meta['email'] ) ? $post_meta['email']: "";
	$biography = isset( $post_meta['biography'] ) ? $post_meta['biography']: "";
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;


	echo "<article id='$item_id' data-pid='$pid' class='item post cws_staff_post posts_grid_post'>";

		echo "<div class='post_wrapper cws_staff_post_wrapper posts_grid_post_wrapper'>";
			ob_start();
			cws_vc_shortcode_cws_staff_posts_grid_post_title ();
			if ( !in_array( 'deps', $cws_staff_data_to_hide ) ) {
				$deps = cws_vc_shortcode_get_post_term_links_str( 'cws_staff_member_department' );
				if ( !empty( $deps ) ){
					echo "<div class='post_terms cws_staff_post_terms posts_grid_post_terms'>";
					echo $deps;
					echo "</div>";	
				}
			}				
		
			if ( !in_array( 'poss', $cws_staff_data_to_hide ) ) {
				$poss = cws_vc_shortcode_get_post_term_links_str( 'cws_staff_member_position' );
				if ( !empty( $poss ) ){
					echo "<div class='post_terms cws_staff_post_terms posts_grid_post_terms'>";
						echo $poss;
					echo "</div>";	
				}
			}

			if( !in_array( 'experience', $cws_staff_data_to_hide ) && !empty($experience)){
				echo "<div class='experience'><span>".(esc_html__('Experience', 'cws-essentials')).":</span><span>".esc_html($experience)."</span></div>";
			}		
			if( !in_array( 'email', $cws_staff_data_to_hide ) && !empty($email)){
				echo "<div class='email'><span>".(esc_html__('Email', 'cws-essentials')).":</span><a href='mailto:".esc_html($email)."'>".esc_html($email)."</a></div>";
			}		
			if(!in_array( 'biography', $cws_staff_data_to_hide ) && !empty($biography)){
				echo "<div class='biography'><span>".(esc_html__('Biography', 'cws-essentials')).":</span><p>".esc_html($biography)."</p></div>";
			}

			if ( !in_array( 'excerpt', $cws_staff_data_to_hide ) ) {
				cws_vc_shortcode_cws_staff_posts_grid_post_content ();
			}			

			
			$prim_post_data = ob_get_clean();

			ob_start();
			$sec_post_data = ob_get_clean();
			ob_start();
			if ( !empty( $prim_post_data ) ){
				echo "<div class='prim_post_data cws_staff_prim_post_data posts_grid_prim_post_data'>";
					echo $prim_post_data;			
				echo "</div>";
			}
			$post_data = ob_get_clean();
			cws_vc_shortcode_cws_staff_posts_grid_post_media ();
			echo "<div class='post_data cws_staff_post_data posts_grid_post_data'>";
				echo $post_data;
			echo "</div>";
			if ( !empty( $sec_post_data ) ){
				echo "<div class='sec_post_data cws_staff_sec_post_data posts_grid_sec_post_data'>";
					echo $sec_post_data;
				echo "</div>";			
			}				
			if ( !in_array( 'socials', $cws_staff_data_to_hide ) ) {
				cws_vc_shortcode_cws_staff_posts_grid_social_links ();
			}	
		echo "</div>";
	echo "</article>";
}
function cws_vc_shortcode_cws_staff_posts_grid_post_media (){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'					=> '2',
					'cws_staff_data_to_hide'	=> array()
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_url = esc_url(get_the_permalink());
	$cws_staff_data_to_hide = $cws_staff_data_to_hide ? $cws_staff_data_to_hide : array();
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$thumbnail_dims = cws_vc_shortcode_get_cws_staff_thumbnail_dims();
	$thumbnail_dims['crop'] = true;
	$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
	$retina_thumb_exists = !empty($thumb_obj[3]) ? true : false;
	$retina_thumb_url = "";	
	if ( isset( $thumb_obj[3] ) &&  is_array($thumb_obj[3])){
		extract( $thumb_obj[3] );
	}			
	$retina_thumb_url = esc_attr($retina_thumb_url);
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
	$add_btn = isset( $post_meta['add_btn'] ) ? $post_meta['add_btn']: false;
	$title_btn_post = isset( $post_meta['title_btn'] ) ? $post_meta['title_btn']: false;
	
	$title_button = '';
	if(!empty($add_btn)){
		if(!empty($change_btn)){
			$title_button = $title_btn;
		}
		else if(!empty($title_btn_post)){
			$title_button = $title_btn_post;
		}
	}
	if ( !empty( $thumb_url ) ){
	?>
		<div class="post_media cws_staff_post_media posts_grid_post_media<?php echo (!empty($add_btn) && !empty($title_button) ? ' add_btn' : "");?>">
			<?php
				echo "<div class='cws_staff_photo".(!empty($clickable) ? ' clickable' : "")."'>";
					echo $clickable ? "<a href='$permalink' class='link_author'>" : "";
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$thumb_obj[3]' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
					else{
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
					echo $clickable ? "</a>" : "";
					if(!in_array( 'link_button', $cws_staff_data_to_hide )){
						echo !empty($add_btn) && !empty($title_button) ?  "<a class='btn_staff_details' href='$permalink'>{$title_button}</a>" : "";
					}
					
				echo "</div>";
			?>
		</div>
	<?php
	}	
}
function cws_vc_shortcode_cws_staff_posts_grid_post_title (){
	$def_grid_atts = array(
					'layout'					=> '2'
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$title 		= get_the_title();
	$pid 		= get_the_id();
	$permalink 	= get_the_permalink( $pid );
	$post_meta 	= get_post_meta( $pid, 'cws_mb_post' );
	$post_meta 	= isset( $post_meta[0] ) ? $post_meta[0] : array();
	$clickable 	= isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
	if ( !empty( $title ) ){
		echo "<h3 class='post_title cws_staff_post_title posts_grid_post_title'>";
			if ( $clickable ){
				if(!is_single()){
					echo "<a href='$permalink'>";
				}			
			}
				$fword_boundary = strpos( $title, " " );
				$name = $another = "";
				if ( $fword_boundary !== false ){
					$name = mb_substr( $title, 0, strpos( $title, " " ) );
					$another = mb_substr( $title, $fword_boundary );
					echo "<span class='name'>$name</span>$another";
				}
				else{
					echo $title;
				}
			if ( $clickable ){
				if(!is_single()){
					echo "</a>";
				}
			}
		echo "</h3>";
	}
}
function cws_vc_shortcode_cws_staff_posts_grid_post_content (){
	if(class_exists('WPBMap')){
		WPBMap::addAllMappedShortcodes();
	}
	$pid = get_the_id();
	$post = get_post( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
					'cws_staff_data_to_hide'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$data_to_hide = $cws_staff_data_to_hide ? $cws_staff_data_to_hide : array();
	$out = "";
	if ( !in_array( 'excerpt', $data_to_hide ) ){
		$chars_count = cws_vc_shortcode_get_cws_staff_chars_count( $layout );
		$out = !empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
		$out = trim( preg_replace( "/[\s]{2,}/", " ", strip_shortcodes( strip_tags( $out ) ) ) );
		$out = wptexturize( $out );
		$out = mb_substr( $out, 0, $chars_count );
		echo !empty( $out ) ? "<div class='post_content cws_staff_post_content posts_grid_post_content'>$out</div>" : "";
	}	
}
function cws_vc_shortcode_cws_staff_posts_grid_social_links (){
	$pid = get_the_id();
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$social_group = isset( $post_meta['social_group'] ) ? $post_meta['social_group']: array();
	$icons = "";
	foreach ( $social_group as $social ) {
		$title = isset( $social['title'] ) ? $social['title'] : "";
		$icon = isset( $social['icon'] ) ? $social['icon'] : "";
		$url = isset( $social['url'] ) ? $social['url'] : "";
		if ( !empty( $icon ) && !empty( $url ) ){
			$icons .= "<a href='$url' target='_blank'" . ( !empty( $title ) ? " title='$title'" : "" ) . "><i class='{$icon}'></i><i class='{$icon}'></i></a>";
		}
	}
	if ( !empty( $icons ) ){
		echo "<div class='post_social_links cws_staff_post_social_links posts_grid_post_social_links'>";
			echo $icons;	
		echo "</div>";
	}
}
function cws_vc_shortcode_cws_staff_single_social_links (){
	$pid = get_the_id();
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$social_group = isset( $post_meta['social_group'] ) ? $post_meta['social_group']: array();
	$icons = "";
	foreach ( $social_group as $social ) {
		$title = isset( $social['title'] ) ? $social['title'] : "";
		$icon = isset( $social['icon'] ) ? $social['icon'] : "";
		$url = isset( $social['url'] ) ? $social['url'] : "";
		if ( !empty( $icon ) && !empty( $url ) ){
			$icons .= "<a href='$url' target='_blank'" . ( !empty( $title ) ? " title='$title'" : "" ) . "><i class='{$icon}'></i><i class='{$icon}'></i></a>";
		}
	}
	if ( !empty( $icons ) ){
		echo "<div class='post_social_links cws_staff_post_social_links post_single_post_social_links'>";
			echo $icons;	
		echo "</div>";
	}	
}

function cws_vc_shortcode_cws_staff_single_post_media (){
	$pid = get_the_id();
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];
	$thumbnail_dims = cws_vc_shortcode_get_cws_staff_thumbnail_dims( $real_thumbnail_dims );
	$crop_thumb = isset( $thumbnail_dims['width'] ) && $thumbnail_dims['width'] > 0;
	$thumbnail_dims['crop'] = true;
	$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
	$retina_thumb_exists = !empty($thumb_obj[3]) ? true : false;
	$retina_thumb_url = "";	
	if ( isset( $thumb_obj[3] ) && is_array($thumb_obj[3]) ){
		extract( $thumb_obj[3] );
	}			
	$retina_thumb_url = esc_attr($retina_thumb_url);
	if ( !empty( $thumb_url ) ){
	?>
		<div class="post_media cws_staff_post_media post_single_post_media">
			<?php
				echo "<div class='cws_staff_photo'>";
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$thumb_obj[3]' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
					else{
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
				echo "</div>";
			?>
		</div>
	<?php
		$GLOBALS['cws_vc_shortcode_cws_staff_single_post_floated_media'] = !$crop_thumb ? true : false;
	}	
}

function cws_vc_shortcode_cws_staff_single_post_content (){
	if(class_exists('WPBMap')){
		WPBMap::addAllMappedShortcodes();
	}
	$pid = get_the_id();
	$post = get_post( $pid );
	$post_content =  apply_filters( 'the_content', $post->post_content );
	if ( !empty( $post_content ) ){
		echo "<div class='post_content cws_staff_post_content post_single_post_content'>";
			echo $post_content;
		echo "</div>";
	}
}

?>