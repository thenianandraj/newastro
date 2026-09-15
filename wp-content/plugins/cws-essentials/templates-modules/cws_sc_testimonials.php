<?php

function cws_vc_shortcode_cws_testimonial_posts_grid ( $atts = array(), $content = "" ){
	$out = "";
	$defaults = array(
		'title'									=> '',
		'title_align'							=> 'left',
		'total_items_count'						=> '',
		'hide_data_override'					=> false,
		'data_to_hide'							=> '',
		'display_style'							=> 'grid',
		'layout'								=> 'def',
		'massonry'								=> false,
		'items_pp'								=> esc_html( get_option( 'posts_per_page' ) ),
		'paged'									=> 1,
		'tax'									=> '',
		'addl_query_args'						=> array(),
		'el_class'								=> '',
		'title_btn'                             => '',
		'change_title'                          => '',
		'auto_play_carousel'                             => '',
		'navigation_carousel'                            => '',
		'pagination_carousel'                            => '',
	);
	$proc_atts = shortcode_atts( $defaults, $atts );
	extract( $proc_atts );
	$terms = isset( $atts[ $tax . "_terms" ] ) ? $atts[ $tax . "_terms" ] : "";
	$section_id = uniqid( 'cws_testimonial_posts_grid_' );
	$ajax_data = array();
	$total_items_count = !empty( $total_items_count ) ? (int)$total_items_count : PHP_INT_MAX;
	$items_pp = !empty( $items_pp ) ? (int)$items_pp : esc_html( get_option( 'posts_per_page' ) );
	$paged = (int)$paged;

	$def_layout = function_exists('cws_vc_shortcode_get_option') ? cws_vc_shortcode_get_option( 'def_cws_testimonial_layout' ) : "";
	$def_layout = isset( $def_layout ) ? $def_layout : "";
	$layout = ( empty( $layout ) || $layout === "def" ) ? $def_layout : $layout; 
	$hide_data_override = (bool)$hide_data_override;
	$massonry 			= (bool)$massonry;
	$data_to_hide = explode( ",", $data_to_hide );
	$def_data_to_hide = function_exists('cws_vc_shortcode_get_option') ? cws_vc_shortcode_get_option( 'def_cws_testimonial_data_to_hide' ) : "";
	$def_data_to_hide  = isset( $def_data_to_hide ) ? $def_data_to_hide : array();
	$data_to_hide = $hide_data_override ? $data_to_hide : $def_data_to_hide;

	$el_class = esc_attr( $el_class );
	$sb = function_exists('cws_vc_shortcode_get_sidebars') ? cws_vc_shortcode_get_sidebars() : "";
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
	$query_args = array('post_type'			=> 'cws_testimonial',
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

	$use_carousel = in_array( $display_style, array( 'carousel') );
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
	if ( $use_carousel ){
		$data_attr .= isset($auto_play_carousel) && !empty($auto_play_carousel) ? ' auto_play_owl' : "";
		$data_attr .= isset($navigation_carousel) && !empty($navigation_carousel) ? ' navigation_owl cws_sc_carousel' : "";
		$data_attr .= isset($pagination_carousel) && !empty($pagination_carousel) ? ' pagination_owl' : "";
	}
	ob_start ();
	/********/
	echo "<section id='$section_id' class='posts_grid cws_testimonials_posts_grid posts_grid_{$layout} posts_grid_{$display_style}" . ( $dynamic_content ? " dynamic_content" : "" ).(!empty($data_attr) ? $data_attr : "").(!empty( $el_class ) ? " $el_class" : "" )." clearfix'" . ( $is_carousel ? " data-columns='" . ( !is_numeric( $layout ) ? "1" : $layout ) . "'" : "" ) . ">";
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
					echo "<nav class='nav cws_testimonial_nav posts_grid_nav text_align{$title_align}'>";
						echo "<a href class='nav_item cws_testimonial_nav_item posts_grid_nav_item active' data-nav-val='_all_'>" . esc_html__( 'All', 'cws-essentials' ) . "</a>";
						foreach ( $filter_vals as $term_slug => $term_name ){
							echo "<a href class='nav_item cws_testimonial_nav_item posts_grid_nav_item' data-nav-val='" . esc_html( $term_slug ) . "'>" . esc_html( $term_name ) . "</a>";
						}
						echo "<span class='magicline'></span>";						
					echo "</nav>";
				}
			}
		}
		echo "<div class='cws_vc_shortcode_wrapper'>";
			echo "<div class='" . ( $is_carousel ? "cws_vc_shortcode_carousel" : "cws_vc_shortcode_grid" . ( ( in_array( $layout, array( "2", "3", "4" ) ) || $dynamic_content ) ? " isotope" : "" ) ) . "'" . ( $is_carousel ? " data-cols='" . ( !is_numeric( $layout ) ? "1" : $layout ) . "'" : "" ) . ">";
				$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
					'post_type'						=> 'cws_testimonial',
					'layout'						=> $layout,
					'massonry'						=> $massonry,
					'sb_layout'						=> $sb_layout,
					'cws_testimonial_data_to_hide'		=> $data_to_hide,
					'title_btn'		=>	$title_btn,
					'change_title'		=> $change_title,
					'total_items_count'				=> $total_items_count
					);
				if ( function_exists( "cws_vc_shortcode_cws_testimonial_posts_grid_posts" ) ){
					call_user_func_array( "cws_vc_shortcode_cws_testimonial_posts_grid_posts", array( $q ) );
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
			$ajax_data['post_type']							= 'cws_testimonial';
			$ajax_data['cws_testimonial_data_to_hide']		= $data_to_hide;
			$ajax_data['title_btn']							= $title_btn;
			$ajax_data['change_title']						= $change_title;
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
			$ajax_data_str = json_encode( $ajax_data );
			echo "<form id='{$section_id}_data' class='ajax_data_form cws_testimonial_ajax_data_form posts_grid_ajax_data_form'>";
				echo "<input type='hidden' id='{$section_id}_ajax_data' class='ajax_data cws_testimonial_ajax_data posts_grid_ajax_data' name='{$section_id}_ajax_data' value='$ajax_data_str' />";
			echo "</form>";
		}
	echo "</section>";
	$out = ob_get_clean();
	return $out;
}

function cws_vc_shortcode_cws_testimonial_posts_grid_posts ( $q = null ){
	if ( !isset( $q ) ) return;
	$def_grid_atts = array(
					'layout'						=> '2',
					'cws_testimonial_data_to_hide'		=> array(),
					'title_btn'		=>	'',
					'change_title'		=> '',
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
			cws_vc_shortcode_cws_testimonial_posts_grid_post ();
		endwhile;
		wp_reset_postdata();
		ob_end_flush();
	endif;		
}

function cws_vc_shortcode_get_cws_testimonial_thumbnail_dims ( $real_dims = array() ) {
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
	$dims = array( 'width' => 93, 'height' => 93 );
	return $dims;
}

function cws_vc_shortcode_get_cws_testimonial_chars_count ( $cols = null ){
	$number = 100000;
/*	switch ( $cols ){
		case '1':
			$number = 270;
			break;
		case '2':
			$number = 90;
			break;
		case '3':
			$number = 40;
			break;
	}*/
	return $number;
}

function cws_vc_shortcode_cws_testimonial_posts_grid_post (){
	$def_grid_atts = array(
					'layout'					=> '2',
					'cws_testimonial_data_to_hide'	=> array(),					
					'title_btn'		=>	'',
					'change_title'		=> ''
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$pid = get_the_id();
	$item_id = uniqid( "cws_testimonial_post_" );
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	echo "<article id='$item_id' data-pid='$pid' class='item post cws_testimonial_post posts_grid_post'>";
		echo "<div class='post_wrapper cws_testimonial_post_wrapper posts_grid_post_wrapper'>";
			ob_start();
			echo "<div class='wrapper-author'>";
				cws_vc_shortcode_cws_testimonial_posts_grid_post_media ();
			echo "</div>";
			echo "<div class='quote'>";
			cws_vc_shortcode_cws_testimonial_posts_grid_post_title ();			
			if ( !in_array( 'raiting', $cws_testimonial_data_to_hide ) ) {
				
				$mark = isset( $post_meta['mark'] ) ? $post_meta['mark']: array();
				if ( !empty( $mark ) && is_numeric( $mark ) ){
					$mark_percents = floatval($mark)*20;
					echo "<div class='pricing_plan_mark'>";
					echo "<div class='cws_vc_shortcode_stars_wrapper'>";
					echo "<div class='cws_vc_shortcode_inactive_stars cws_vc_shortcode_stars'>";
					echo "</div>";
					echo "<div class='cws_vc_shortcode_active_stars cws_vc_shortcode_stars' style='width:{$mark_percents}%;'>";
					echo "</div>";
					echo "</div>";
					echo "</div>";
				}
			}

			if ( !in_array( 'excerpt', $cws_testimonial_data_to_hide ) ) {
				cws_vc_shortcode_cws_testimonial_posts_grid_post_content ();
			}			
			if ( !in_array( 'poss', $cws_testimonial_data_to_hide ) ) {
				$poss = cws_vc_shortcode_get_post_term_links_str( 'cws_testimonial_position' );
				if ( !empty( $poss ) ){
					echo "<div class='post_terms cws_testimonial_post_terms posts_grid_post_terms'>";
						echo $poss;
					echo "</div>";	
				}
			}
			$prim_post_data = ob_get_clean();
			ob_start();
			if ( !empty( $prim_post_data ) ){
				echo "<div class='prim_post_data cws_testimonial_prim_post_data posts_grid_prim_post_data'>";
					echo $prim_post_data;			
				echo "</div>";
			}
			if ( !empty( $sec_post_data ) ){
				echo "<div class='sec_post_data cws_testimonial_sec_post_data posts_grid_sec_post_data'>";
					echo $sec_post_data;
				echo "</div>";			
			}
			/* \Quote Close */
			echo "</div>";			

			if ( !in_array( 'button', $cws_testimonial_data_to_hide ) ) {
				
				$txt_b = isset( $post_meta['txt_button_title'] ) ? $post_meta['txt_button_title'] : false;	
				$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
				$change_title = isset( $change_title ) ? $change_title : false;
				$title_btn = isset( $title_btn ) && !empty($title_btn) ? $title_btn : false;
				$permalink = get_the_permalink( $pid );
				if($clickable){
					echo "<div class='link-testimonials'>";
						echo "<a class='testimonial-button' href='".esc_url($clickable)."'>";
							if(!empty($change_title)){
								if(!empty($title_btn)){
									echo esc_html($title_btn, 'cws-essentials');
								}			
								else{
									echo esc_html__("Read More", 'cws-essentials');
								}
							}
							else{
								if(!empty($txt_b)){
									echo esc_html($txt_b, 'cws-essentials');
								}
								else{
									echo esc_html__("Read More", 'cws-essentials');
								}
								
							}
							
						echo "</a>";
					echo "</div>";	 
				}

			}
			$post_data = ob_get_clean();
			echo "<div class='post_data cws_testimonial_post_data posts_grid_post_data'>";
				echo $post_data;
			echo "</div>";
		echo "</div>";
	echo "</article>";
}
function cws_vc_shortcode_cws_testimonial_posts_grid_post_media (){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'					=> '2',
					'cws_testimonial_data_to_hide'	=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_url = esc_url(get_the_permalink());
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();

	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$thumbnail_dims = cws_vc_shortcode_get_cws_testimonial_thumbnail_dims();
	$thumbnail_dims['crop'] = true;
	$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
	$retina_thumb_exists = false;
	$retina_thumb_url = "";	
	if ( isset( $thumb_obj[3] ) && is_array($thumb_obj[3]) ){
		extract( $thumb_obj[3] );
	}			
	$retina_thumb_url = esc_attr($retina_thumb_url);
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;

	if(!is_array($cws_testimonial_data_to_hide)){
		$cws_testimonial_data_to_hide = array();
	}
	if ( !in_array( 'background', $cws_testimonial_data_to_hide ) ) {

		$gallery = isset( $post_meta['gallery'] ) ? $post_meta['gallery']: array();
		$url = isset( $post_meta['link'] ) ? $post_meta['link']: array();
		if ( !empty( $gallery['id'] ) ){
			$gallery = wp_get_attachment_url($gallery['id']);
			$thumb_obj_bg = cws_thumb( $gallery, array(), false );
			$thumb_url_bg = isset( $thumb_obj_bg[0] ) ? esc_url( $thumb_obj_bg[0] ) : "";
			if ( isset( $thumb_obj_bg[3] ) && is_array($thumb_obj[3]) ){
				extract( $thumb_obj_bg[3] );
			}
			else{
				$retina_thumb_exists = false;
				$retina_thumb_url = '';
			}
			if ( $retina_thumb_exists ) {
				echo $clickable ? "<a href='".esc_url($clickable)."'>" : "";
				echo "<span class='thumb_img'><img src='$thumb_url_bg' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' /></span>";
				echo $clickable ? "</a>" : "";
			}
			else{
				echo $clickable ? "<a href='".esc_url($clickable)."'>" : "";
				echo "<span class='thumb_img'><img src='$thumb_url_bg' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' /></span>";
				echo $clickable ? "</a>" : "";
			}

		}	
	}

	if ( !empty( $thumb_url ) ){
	?>
		<figure class='author'>
			<div class="post_media cws_testimonial_post_media posts_grid_post_media">
				<?php
					echo "<div class='cws_testimonial_photo'>";
						echo $clickable ? "<a href='".esc_url($clickable)."'>" : "";
						echo "<span class='thumb'>";
						if ( $retina_thumb_exists ) {
							echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
						}
						else{
							echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
						}
						echo "</span>";
						echo $clickable ? "</a>" : "";
					echo "</div>";
				?>
			</div>			
		</figure>

	<?php
	}	
}
function cws_vc_shortcode_cws_testimonial_posts_grid_post_title (){
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
		echo "<h5 class='post_title cws_testimonial_post_title posts_grid_post_title'>";
			if ( $clickable ){
				echo "<a href='".esc_url($clickable)."'>";
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
				echo "</a>";
			}
		echo "</h5>";
	}
}
function cws_vc_shortcode_cws_testimonial_posts_grid_post_content (){
	$pid = get_the_id();
	$post = get_post( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
					'cws_testimonial_data_to_hide'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$data_to_hide = $cws_testimonial_data_to_hide;
	$out = "";
	if ( !in_array( 'excerpt', $data_to_hide ) ){
		$chars_count = cws_vc_shortcode_get_cws_testimonial_chars_count( $layout );
		$out = !empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
		$out = trim( preg_replace( "/[\s]{2,}/", " ", strip_shortcodes( strip_tags( $out ) ) ) );
		$out = wptexturize( $out );
		$out = mb_substr( $out, 0, $chars_count );
		echo !empty( $out ) ? "<div class='post_content cws_testimonials_post_content posts_grid_post_content'>$out</div>" : "";
	}	
}

function cws_vc_shortcode_cws_testimonial_single_social_links (){
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
			$icons .= "<a href='$url' target='_blank' class='$icon'" . ( !empty( $title ) ? " title='$title'" : "" ) . "></a>";
		}
	}
	if ( !empty( $icons ) ){
		echo "<div class='post_social_links cws_testimonial_post_social_links post_single_post_social_links'>";
			echo $icons;	
		echo "</div>";
	}	
}

function cws_vc_shortcode_cws_testimonial_single_post_media (){
	$def_grid_atts = array(
					'layout'					=> '2',
					'cws_testimonial_data_to_hide'	=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$thumbnail_dims = cws_vc_shortcode_get_cws_testimonial_thumbnail_dims();
	$thumbnail_dims['crop'] = true;
	$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
	$retina_thumb_exists = false;
	$retina_thumb_url = "";	
	if ( isset( $thumb_obj[3] ) && is_array($thumb_obj[3]) ){
		extract( $thumb_obj[3] );
	}			
	$retina_thumb_url = esc_attr($retina_thumb_url);
	$clickable = isset( $post_meta['is_clickable'] ) ? $post_meta['is_clickable']: false;
	if ( !in_array( 'background', $cws_testimonial_data_to_hide ) ) {
		$gallery = isset( $post_meta['gallery'] ) ? $post_meta['gallery']: array();
		$url = isset( $post_meta['link'] ) ? $post_meta['link']: array();
		if ( !empty( $gallery['id'] ) ){
			$gallery = wp_get_attachment_url($gallery['id']);
			$thumb_obj_bg = cws_thumb( $gallery, array(), false );
			$thumb_url_bg = isset( $thumb_obj_bg[0] ) ? esc_url( $thumb_obj_bg[0] ) : "";
			if ( isset( $thumb_obj_bg[3] ) ){
				extract( $thumb_obj_bg[3] );
			}
			else{
				$retina_thumb_exists = false;
				$retina_thumb_url = '';
			}
			if ( $retina_thumb_exists ) {
				echo "<span class='thumb_img'><img src='$thumb_url_bg' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' /></span>";
			}
			else{
				echo "<span class='thumb_img'><img src='$thumb_url_bg' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' /></span>";
			}
		}	
	}
	if ( !empty( $thumb_url ) ){
	?>
		<figure class='author'>
			<div class="post_media cws_testimonial_post_media post_single_post_media">
				<?php
					echo "<div class='cws_testimonial_photo'>";
						echo "<span class='thumb'>";
						if ( $retina_thumb_exists ) {
							echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
						}
						else{
							echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
						}
						echo "</span>";
					echo "</div>";
				?>
			</div>			
		</figure>
		<?php
		
	}
}

function cws_vc_shortcode_cws_testimonial_single_post_content (){
	$pid = get_the_id();
	$post = get_post( $pid );
	$post_content =  apply_filters( 'the_content', $post->post_content );
	if ( !empty( $post_content ) ){
		echo "<div class='post_content cws_testimonial_post_content post_single_post_content'>";
			echo $post_content;
		echo "</div>";
	}
}

?>