<?php
function cws_sc_blog($p){
	return function_exists( "cws_vc_shortcode_cws_blog_posts_grid" ) ? cws_vc_shortcode_cws_blog_posts_grid( $p ) : "";
}
function cws_vc_shortcode_cws_blog_posts_grid ( $atts = array(), $content = "" ){
	$out = "";
	$defaults = array(
		'title'									=> '',
		'title_align'							=> 'left',
		'total_items_count'						=> '',
		'layout'								=> 'def',
		'post_hide_meta_override'				=> false,
		'post_hide_meta'						=> '',
		'chars_count'							=> '',
		'display_style'							=> 'grid',		
		'auto_play_carousel'                    => '',
		'navigation_carousel'                   => '',
		'pagination_carousel'                   => '',
		'items_pp'								=>  esc_html( get_option( 'posts_per_page' ) ),
		'tax'									=> '',
		'terms'									=> '',
		'addl_query_args'						=> array(),
		'full_width'							=> '',
		'hover_effects'							=> '',
		'pagination_grid'						=> '',
		'el_class'								=> '',
		'related_items'							=> '',
		'link_show'								=> '',

	);

	$atts = shortcode_atts( $defaults, $atts );

	extract( $atts );
	global $display_post;
	$display_post = isset($display_style) && !empty($display_style) ? $display_style : "";
	$post_type = "post";
	$section_id = uniqid( 'posts_grid_' );
	$total_items_count = !empty( $total_items_count ) ? (int)$total_items_count : PHP_INT_MAX;
	$items_pp = !empty( $items_pp ) ? (int)$items_pp : esc_html( get_option( 'posts_per_page' ) );
	$paged = get_query_var( 'paged' );
	$home_paged = get_query_var('page');
	if(isset($home_paged) && !empty($home_paged)){
		$paged = empty( $home_paged ) ? 1 : get_query_var('page');
	}
	else{
		$paged = empty( $paged ) ? 1 : $paged;
	}
	
	$def_post_layout = cws_vc_shortcode_get_option( 'def_blogtype' );
	$def_post_layout = isset( $def_post_layout ) ? $def_post_layout : "";
	$layout = ( empty( $layout ) || $layout === "def" ) ? $def_post_layout : $layout; 
	$post_hide_meta_override = !empty( $post_hide_meta_override ) ? true : false;
	$post_hide_meta = explode( ",", $post_hide_meta );
	$post_def_hide_meta = cws_vc_shortcode_get_option( 'def_post_hide_meta' );
	$post_def_hide_meta  = is_array( $post_def_hide_meta ) ? $post_def_hide_meta : array();
	$post_hide_meta = $post_hide_meta_override ? $post_hide_meta : $post_def_hide_meta;
	$full_width = isset($GLOBALS['cws_row_atts']) && !empty($GLOBALS['cws_row_atts']) ? $GLOBALS['cws_row_atts'] : "";
	
	$el_class = esc_attr( $el_class );
	$sb = function_exists("cws_vc_shortcode_get_sidebars") ? cws_vc_shortcode_get_sidebars() : "";
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
	$query_args = array('post_type'			=> array( $post_type ),
						'post_status'		=> 'publish',
						'post__not_in'		=> $not_in
						);
	if ( in_array( $display_style, array( 'grid' ) ) ){
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
	$query_args = array_merge( $query_args, $addl_query_args );
	$q = new WP_Query( $query_args );
	$found_posts = $q->found_posts;
	$requested_posts = $found_posts > $total_items_count ? $total_items_count : $found_posts;
	$max_paged = $found_posts > $total_items_count ? ceil( $total_items_count / $items_pp ) : ceil( $found_posts / $items_pp );
	$cols = in_array( $layout, array( 'medium', 'small', 'checkerboard', 'fw_img' ) ) ? 1 : (int)$layout;
	$is_carousel = $display_style == 'carousel' && $requested_posts > $cols;
	if ( $is_carousel ){
		wp_enqueue_script( 'owl_carousel' );
	}
	else if ( is_numeric( $layout ) ){
		wp_enqueue_script( 'isotope' );
	}
	wp_enqueue_script( 'imagesloaded' );

	wp_enqueue_script( 'fancybox' );
	$use_pagination = in_array( $display_style, array( 'grid' ) ) && $max_paged > 1;
	$use_carousel = in_array( $display_style, array( 'carousel') );
	$data_attr = '';
	if ( $use_carousel ){
		$data_attr .= isset($auto_play_carousel) && !empty($auto_play_carousel) ? ' auto_play_owl' : "";
		$data_attr .= isset($navigation_carousel) && !empty($navigation_carousel) ? ' navigation_owl' : "";
		$data_attr .= isset($pagination_carousel) && !empty($pagination_carousel) ? ' pagination_owl' : "";
	}
	$hover_effects = !empty($hover_effects) ? " hover".$hover_effects : " hover1";

	ob_start ();
	echo "<section id='$section_id' class='news{$hover_effects} blog_post posts_grid {$post_type}_posts_grid posts_grid_{$layout} posts_grid_{$display_style}".(!empty($data_attr) ? $data_attr : "").( !empty( $el_class ) ? " $el_class" : "" ) . "'>";
		if ( $is_carousel && !empty($navigation_carousel) ){
			echo "<div class='widget_header clearfix'>";
				echo !empty( $title ) ? "<h2 class='widgettitle'>" . esc_html( $title ) . "</h2>" : "";				
				echo "<div class='carousel_nav'>";
					echo "<span class='prev'>";
						if(!empty($related_items)){
							echo esc_html__("Previous", 'cws-essentials');
						}
					echo "</span>";
					echo "<span class='next'>";
						if(!empty($related_items)){
							echo esc_html__("Next", 'cws-essentials');
						}
					echo "</span>";
				echo "</div>";
			echo "</div>";			
		}
		else{
			echo !empty( $title ) ? "<h2 class='widgettitle text_align{$title_align}'>" . esc_html( $title ) . "</h2>" : "";
		}

		echo "<div class='cws_vc_shortcode_wrapper'>";
			echo "<div class='" . ( $is_carousel ? "cws_vc_shortcode_carousel" : "cws_vc_shortcode_grid grid" . ( is_numeric( $layout ) ? " isotope" : "" ) ) . ( !empty($layout ) ? " layout-{$layout}" : " layout-def" ) . "'" . ( $is_carousel ? " data-cols='" . ( !is_numeric( $layout ) ? "1" : $layout ) . "'" : "" ) . ">";
				$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
					'layout'						=> $layout,
					'sb_layout'						=> $sb_layout,
					'post_hide_meta'				=> $post_hide_meta,
					'chars_count'					=> $chars_count,
					'hover_effects'					=> $hover_effects,
					'full_width'					=> $full_width,
					'full_width'					=> $full_width,
					'related_items'					=> $related_items,
					'link_show'						=> $link_show,
					'total_items_count'				=> $total_items_count
					);

				if ( function_exists( "cws_vc_shortcode_post_posts_grid_posts" ) ){
					call_user_func_array( "cws_vc_shortcode_post_posts_grid_posts", array( $q ) );
				}
				unset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] );
				unset( $GLOBALS['display_post'] );
			echo "</div>";
			if ( $use_pagination ){
				cws_vc_shortcode_loader_html();
			}
		echo "</div>";
		if ( $use_pagination ){
			if ( $pagination_grid == 'load_more' ){
				echo cws_vc_shortcode_load_more ();
			}
			elseif($pagination_grid == 'standard_with_ajax'){
				echo cws_vc_shortcode_pagination($paged, $max_paged, true);
			}
			else{
				echo cws_vc_shortcode_pagination($paged, $max_paged, false);
			}
			
		}
		if ( $use_pagination ){
			$ajax_data['section_id']						= $section_id;
			$ajax_data['post_type']							= 'post';
			$ajax_data['post_hide_meta']					= $post_hide_meta;
			$ajax_data['layout']							= $layout;
			$ajax_data['sb_layout']							= $sb_layout;
			$ajax_data['total_items_count']					= $total_items_count;
			$ajax_data['hover_effects']						= $hover_effects;
			$ajax_data['items_pp']							= $items_pp;
			$ajax_data['page']								= $paged;
			$ajax_data['max_paged']							= $max_paged;
			$ajax_data['tax']								= $tax;
			$ajax_data['terms']								= $terms;
			$ajax_data['current_filter_val']				= '_all_';
			$ajax_data['addl_query_args']					= $addl_query_args;
			$ajax_data['full_width']						=  $full_width;
			$ajax_data['pagination_grid']					=  $pagination_grid;
			$ajax_data['related_items']						=  $related_items;
			$ajax_data['link_show']							=  $link_show;
			$ajax_data['chars_count']						=  $chars_count;
			$ajax_data_str = json_encode( $ajax_data );
			echo "<form id='{$section_id}_data' class='ajax_data_form cws_blog_ajax_data_form posts_grid_ajax_data_form'>";
			echo "<input type='hidden' id='{$section_id}_ajax_data' class='ajax_data cws_blog_ajax_data posts_grid_ajax_data' name='{$section_id}_ajax_data' value='$ajax_data_str' />";
			echo "</form>";
		}
		
	echo "</section>";
	$out = ob_get_clean();

	return $out;
}

function cws_vc_shortcode_get_special_post_formats (){
	return array( "status" );
}
function cws_vc_shortcode_is_special_post_format (){
	global $post;
	$sp_post_formats = cws_vc_shortcode_get_special_post_formats ();
	if ( isset($post) ){
		return in_array( get_post_format(), $sp_post_formats );
	}
	else{
		return false;
	}
}
function cws_vc_shortcode_post_format_mark (){
	global $post;
	if ( isset( $post ) ){
		$pf = get_post_format ();
		$icon = "book";
		switch ( $pf ){
			case "aside":
				$icon = "bullseye";
				break;
			case "gallery":
				$icon = "bullseye";
				break;
			case "link":
				$icon = "chain";
				break;
			case "image":
				$icon = "image";
				break;
			case "quote":
				$icon = "quote-left";
				break;
			case "status":
				$icon = "flag";
				break;
			case "video":
				$icon = "video-camera";
				break;
			case "audio":
				$icon = "music";
				break;
			case "chat":
				$icon = "wechat";
				break;
		}
		$out = "<i class='fa fa-$icon'></i> $pf";
		return $out;
	}
	else{
		return "";
	}
}

function cws_vc_shortcode_get_post_thumbnail_dims ( $eq_thumb_height = false, $real_dims = array(), $post_format = null ) {
	$def_grid_atts = array(
					'layout'				=> '1',
					'sb_layout'				=> '',
					'full_width'			=> ''
				);
	$def_single_atts = array(
					'sb_layout'				=> '',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	$single_atts = isset( $GLOBALS['cws_vc_shortcode_single_post_atts'] ) ? $GLOBALS['cws_vc_shortcode_single_post_atts'] : $def_single_atts;
	$display_post_style = isset( $GLOBALS['display_post'] ) ? $GLOBALS['display_post'] : "";
	$single = is_single();
	extract( $grid_atts );
	if ( $single ){
		extract( $single_atts );
	}
	$sb_layout = !empty($sb_layout) ? str_replace("_sidebar","", $sb_layout) : $sb_layout;

	$dims = array( 'width' => 0, 'height' => 0 );
	if ( $single && empty($related_items)){
		if ( empty( $sb_layout ) ){
			if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 1170 ) ) || $eq_thumb_height ){
				$dims['width'] = 1170;
				if ( $eq_thumb_height ) $dims['height'] = 659;
			}
		}
		else if ( $sb_layout === "single" ){
			if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 870 ) ) || $eq_thumb_height ){
				$dims['width'] = 870;				
				if ( $eq_thumb_height ) $dims['height'] = 490;
			}
		}
		else if ( $sb_layout === "double" ){
			if ( ( empty( $real_dims ) || ( isset( $real_dims['width'] ) && $real_dims['width'] > 570 ) ) || $eq_thumb_height ){
				$dims['width'] = 570;
				if ( $eq_thumb_height ) $dims['height'] = 321;
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
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 659;
					}	
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 870;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 490;
					}	
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 570;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 321;
					}	
				}
				break;			
			case "large":
				if ( empty( $sb_layout ) ){
					$dims['width'] = 1170;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 659;
					}	
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 870;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 490;
					}	
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 570;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 321;
					}	
				}
				break;
			case "fw_img":
				$dims['width'] = 570;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 290;
				}	
				break;
			case "checkerboard":
				$dims['width'] = 570;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 290;
				}	
				break;	
			case "medium":
				$dims['width'] = 570;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 321;
				}	
				break;
			case "small":		
				$dims['width'] = 420;
				$dims['height'] = 420;
				break;
			case '2':
				if ( empty( $sb_layout ) ){	
					$dims['width'] = 570;
					$dims['height'] = 351;
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 570;
					$dims['height'] = 351;	
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 270;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 152;
					}	
				}
				break;
			case '3': 

				if ( empty( $sb_layout ) ){
					$dims['width'] = 370;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 208;
					}else{
						$dims['height'] = 370;
					}	
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 270;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 152;
					}
					else{
						$dims['height'] = 270;
					}	
				}
				else if ( $sb_layout === "double" ){
					$dims['width'] = 270;
					if ( !isset( $real_dims['height'] ) ){
						$dims['height'] = 152;
					}	
					else{
						$dims['height'] = 270;
					}
				}
			
				break;
			case '4':
				$dims['width'] = 270;
				$dims['height'] = 270;
				break;
		}
	}
	return $dims;
}

function cws_vc_shortcode_post_posts_grid_posts ( $q = null ){
	if ( !isset( $q ) ) return;
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
					'total_items_count'		=> PHP_INT_MAX
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
			cws_vc_shortcode_post_posts_grid_post ();
		endwhile;
		wp_reset_postdata();
		ob_end_flush();
	endif;				
}

function cws_vc_shortcode_post_posts_grid_post (){
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$pid = get_the_id();
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();
	$uniq_pid = uniqid( "post_post_" );

	$floated_media = in_array( $layout, array( 'medium', 'small', 'checkerboard', 'fw_img' ) );
	$post_id = get_the_id();
	$post_meta = get_post_meta( $post_id, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$image_from_post = isset($post_meta['post_header_box_image']) ? $post_meta['post_header_box_image'] : '';
	$post_title_color = isset($post_meta['post_title_color']) ? $post_meta['post_title_color'] : '';
	$post_font_meta_color = isset($post_meta['post_font_meta_color']) ? $post_meta['post_font_meta_color'] : '';
	$post_font_color = isset($post_meta['post_font_color']) ? $post_meta['post_font_color'] : '';
	$post_font_sec_color = isset($post_meta['post_font_sec_color']) ? $post_meta['post_font_sec_color'] : '';
	$custom_title_overlay = isset($post_meta['custom_title_overlay']) ? $post_meta['custom_title_overlay'] : '';
	$post_custom_color = isset($post_meta['post_custom_color']) ? $post_meta['post_custom_color'] : '';
	$apply_color = isset($post_meta['apply_color']) ? $post_meta['apply_color'] : '';
	$apply_bg_color = isset($post_meta['apply_bg_color']) ? $post_meta['apply_bg_color'] : '';
	$title_overlay = isset($post_meta['title_overlay']) ? $post_meta['title_overlay'] : '';
	$title_bg_opacity = isset($post_meta['title_bg_opacity']) ? $post_meta['title_bg_opacity'] : '';
	$title_bg_opacity = $title_bg_opacity !== "" ? strval( (int)$title_bg_opacity / 100 ) : "";
		/* styles */
	ob_start();
	if ( $post_custom_color && ( $apply_color == 'list_color' || $apply_color == 'both_color' ) && empty($related_items) ) {
		echo "#{$uniq_pid} .post_title a,
			#{$uniq_pid} .post_title,
			#{$uniq_pid} .post_title a:hover,
			#{$uniq_pid} .post_info .info:hover, 
			#{$uniq_pid} .sl-icon:hover:before, 
			#{$uniq_pid} .info span.post_author a:hover, 
			#{$uniq_pid} .comments_link i:hover, 
			#{$uniq_pid} .comments_link:hover, 
			#{$uniq_pid} .sl-count:hover, 
			#{$uniq_pid} .like:hover, 
			#{$uniq_pid} .post_post_info > .post_meta .social_share a:hover, 
			#{$uniq_pid} .post_info .info:hover:before{
				color: $post_title_color;
			}

			#{$uniq_pid} .post_info .info, 
			#{$uniq_pid} .sl-icon:before, 
			#{$uniq_pid} .info span.post_author a, 
			#{$uniq_pid} .comments_link i, 
			#{$uniq_pid} .comments_link, 
			#{$uniq_pid} .sl-count, 
			#{$uniq_pid} .like, 
			#{$uniq_pid} .post_post_info > .post_meta .social_share a, 
			#{$uniq_pid} .post_info .info:before{
				color: ".cws_Hex2RGBA($post_title_color,.6).";
			}

			#{$uniq_pid} .post_media .hover-effect{
				background: ".cws_Hex2RGBA($post_title_color,.8).";
			}			
			#{$uniq_pid} .post_post_info.posts_grid_post_info > hr{
				background: ".cws_Hex2RGBA($post_title_color,.2).";
			}
			#{$uniq_pid} div.post_tags a,
			#{$uniq_pid} .btn-read-more a,
			#{$uniq_pid} div.post_category a{
				".(!empty($post_font_meta_color) ? "background: ".cws_Hex2RGBA($post_font_meta_color,.6).";" : "background: ".cws_Hex2RGBA($post_title_color,.6).";")."
				
			}
			#{$uniq_pid} div.post_tags a:hover,
			
			#{$uniq_pid} div.post_category a:hover{
				".(!empty($post_font_meta_color) ? "background: ".$post_font_meta_color.";" : "background: ".$post_title_color.";")."
			}
			#{$uniq_pid} .btn-read-more a:hover{
				".(!empty($post_font_meta_color) ? "color: ".$post_font_meta_color.";" : "color: ".$post_title_color.";")."
			}
			#{$uniq_pid} .btn-read-more a.more-link:hover{
				background: transparent;
			}			
			#{$uniq_pid} .btn-read-more a:before{
				border-color: ".$post_title_color.";
			}


			#{$uniq_pid} .post_content{
				color: $post_font_color;
			}
			#{$uniq_pid} .date-content{
				background-color: $post_font_sec_color;
			}
			";
	}
	if ( $post_custom_color && ( $apply_color == 'single_color' ) && empty($related_items) ) {
		echo ".page_title_content #page_title{
				color: $post_title_color;
			}
			.page_title_content .bread-crumbs{
				color: $post_font_color;
			}";
	}
	if ($custom_title_overlay == 1 && ( $apply_bg_color == 'list_color' || $apply_bg_color == 'both_color' ) && empty($related_items) ) {
		echo "#{$uniq_pid}:before{
			background-color: $title_overlay;
			opacity: $title_bg_opacity;
		}";
	}
	/* \styles */
	$styles = ob_get_clean();
	echo "<article id='$uniq_pid' ";
	post_class( array( 'item', 'post', 'post_post', 'posts_grid_post' ) );
	echo ">";
		if ( !empty( $styles ) ){
			echo "<style type='text/css'>";
				echo $styles;
				//Cws_shortcode_css()->enqueue_cws_css($styles);
			echo "</style>";
		}
		if (!empty($image_from_post) && $layout == 'fw_img') {
			$back_img_src = $image_from_post['src'];
			echo "<div class='back_img' style='background-image: url($back_img_src);'></div>";
		}
		echo "<div class='post_wrapper post_post_wrapper posts_grid_post_wrapper clearfix'>";
				cws_vc_shortcode_post_posts_grid_post_media( $uniq_pid );
				echo "<div class='post_post_info posts_grid_post_info'>";
					cws_vc_shortcode_post_posts_grid_post_title ();
					cws_vc_shortcode_post_posts_grid_post_meta ();

					cws_vc_shortcode_post_posts_grid_post_content ();	
					if(!in_array( 'cats', $post_hide_meta ) || !in_array( 'tags', $post_hide_meta ) || !in_array( 'social', $post_hide_meta )){	
						echo "<hr>";				
						echo "<div class='post_meta'>";
					}
					
					
					if ( !in_array( 'cats', $post_hide_meta ) ){
						if ( has_category() ) {
							echo "<div class='post_category'>";
								cws_vc_shortcode_post_posts_grid_post_cats ();
							echo "</div>";
						}
					}
					if ( !in_array( 'tags', $post_hide_meta ) ){
						if ( has_tag() ) {
							echo "<div class='post_tags'>";
							cws_vc_shortcode_post_posts_grid_post_tags ();
							echo "</div>";
						}
					}				
					if ( !in_array( 'social', $post_hide_meta ) ){	
						if (( in_array( 'wordpress-social-login/wp-social-login.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) || (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network( 'wordpress-social-login/wp-social-login.php' ) )) {
							echo "<div class='social_share'>".do_shortcode('[wordpress_social_login]')."</div>";
						};

					}
					if(!in_array( 'cats', $post_hide_meta ) || !in_array( 'tags', $post_hide_meta ) || !in_array( 'social', $post_hide_meta )){
						echo "</div>";
					}
				echo "</div>";
		echo "</div>";	
	echo "</article>";
}
function cws_vc_shortcode_post_posts_grid_post_title (){
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();

	$pid = get_the_id();
	$title = get_the_title();
	$permalink = get_the_permalink();
	$is_special_post_format = cws_vc_shortcode_is_special_post_format();
	if ( !in_array( 'title', $post_hide_meta ) ){
		echo !$is_special_post_format && !empty( $title ) ?	"<h3 class='post_title post_post_title posts_grid_post_title'><a href='$permalink'>" . $title . "</a></h3>" : "";
	}
}

function cws_vc_shortcode_post_posts_grid_post_media ( $uniq_pid ){
	$pid = get_the_id();
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
					'sb_layout'				=> '',
					'full_width'			=> ''
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );	
	$post_url = esc_url(get_the_permalink());
	$post_format = get_post_format( );
	$eq_thumb_height = in_array( $post_format, array( 'gallery' ) );
	$media_meta = get_post_meta( get_the_ID(), 'cws_mb_post' );
	$media_meta = isset( $media_meta[0] ) ? $media_meta[0] : array();
	$thumbnail_props = has_post_thumbnail( ) ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];
	$thumbnail_dims = cws_vc_shortcode_get_post_thumbnail_dims( $eq_thumb_height, $real_thumbnail_dims, $post_format );
	$full_width = isset($GLOBALS['cws_row_atts']) && !empty($GLOBALS['cws_row_atts']) ? $GLOBALS['cws_row_atts'] : "";

	$buf1 = "";
	$thumb_media = false;
	$allow_cut_media = false;
	$some_media = false;
	ob_start();
	switch ($post_format) {
		case 'link':
			$link = isset( $media_meta['link'] ) ? esc_url( $media_meta['link'] ) : "";
			
			$link_title = isset( $media_meta['link_title'] ) ? esc_html( $media_meta['link_title'] ) : "";
			
			
			if ( !empty($thumbnail) ) {
				$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
				$thumb_url = isset( $thumb_obj[0] ) ? esc_url( $thumb_obj[0] ) : "";
				$thumbnail = esc_url($thumbnail);
				$retina_thumb_exists = false;
					$retina_thumb_url = "";
					if ( isset( $thumb_obj[3] ) && is_array($thumb_obj[3]) ){
						extract( $thumb_obj[3] );
					}
				?>
				<div class="pic <?php echo !empty( $link ) ? 'link_post' : ''; ?>">
					<?php
						echo "<span class='link_post_src' style='background-image:url(".$thumbnail.");'></span>";
					?>
					<div class="hover-effect"></div>
					<?php
					if ( !empty( $link ) ){
						if ( !empty( $link_title ) ){
							echo "<span class='post_media_link_title overlay'>$link_title</span>";
						}
						echo "<a class='post_media_link post_post_media_link posts_grid_post_media_link' href='$link'></a>";
					}
					else{
						echo "<a class='fancy post_media_link post_post_media_link posts_grid_post_media_link' href='$thumbnail'></a>";					
					}
					?>
				</div>
				<?php
				$thumb_media = true;
			}
			else{
				if ( !empty( $link ) ) {
					if ( !empty( $link_title ) ){
						echo "<div class='pic'>";
							echo "<span class='post_media_link_title'>$link_title</span>";
							echo "<a class='post_media_link post_post_media_link posts_grid_post_media_link' href='$link'></a>";
						echo "</div>";
					}
				}
			}
			break;
		case 'video':
			$video = isset($media_meta[$post_format]) ? $media_meta[$post_format] : "";
			if ( !empty( $video ) ) {
				echo "<div class='video'>" . apply_filters('the_content',"[embed width='" . $thumbnail_dims['width'] . "']" . $video . "[/embed]") . "</div>";
			}
			break;
		case 'audio':
			$audio = isset($media_meta[$post_format]) ? esc_attr( $media_meta[$post_format]) : "";
			$is_soundcloud = is_int( strpos( (string) $audio, 'https://soundcloud' ) );
			if ( !empty( $thumbnail ) && !$is_soundcloud ){
				$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
				$thumb_url = isset( $thumb_obj[0] ) ? esc_url( $thumb_obj[0] ) : "";
				$thumbnail = esc_url($thumbnail);
				$retina_thumb_exists = false;
				$retina_thumb_url = "";
				if ( isset( $thumb_obj[3] ) ){
					extract( $thumb_obj[3] );
				}
				echo "<div class='pic'>";
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}	else {
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
				echo "</div>";
				if ( empty( $audio ) ){
					echo "<div class='hover-effect'></div>";
					echo "<a class='fancy post_media_link post_post_media_link posts_grid_post_media_link' href='$thumbnail'></a>";					
					$thumb_media = true;				
				}					
			}
			if ( !empty( $audio ) ){
				echo "<div class='audio" . ( $is_soundcloud ? " soundcloud" : "" ) . "'>";
					echo apply_filters( 'the_content', $audio );
				echo "</div>";
			}
			break;
		case 'quote':
			$quote = isset( $media_meta['quote_text'] ) ? $media_meta['quote_text'] : '';
			$author_name = isset( $media_meta['quote_author'] ) ? $media_meta['quote_author'] : '';				
			if ( !empty( $quote ) ) {
				echo "<div class='post_format_quote_media_wrapper'>";
					echo cws_vc_shortcode_quote_renderer( array(
						'thumbnail'			=> $thumbnail,
						'quote'				=> $quote,
						'author_name'		=> $author_name,
					));
				echo "</div>";
			}
			break;
		case 'gallery':
			$gallery = isset( $media_meta[$post_format] ) ? $media_meta[$post_format] : "";
			if ( !empty( $gallery ) ) {
				$match = preg_match_all("/\d+/",$gallery,$images);
				if ($match){
					$images = $images[0];
					$image_srcs = array();
					foreach ( $images as $image ) {
						$image_src = wp_get_attachment_image_src($image,'full');
						if ( $image_src ){
							$image_url = $image_src[0];
							array_push( $image_srcs, $image_url );
						}
					}
					$carousel = count($image_srcs) > 1 ? true : false;
					$gallery_id = uniqid( 'cws-gallery-' );
					echo  $carousel ? "<div class='gallery_post_carousel_wrapper'><a class='gallery_post_carousel_nav carousel_nav prev fa fa-long-arrow-left'>
										<span></span>
										</a>
										<a class='gallery_post_carousel_nav carousel_nav next fa fa-long-arrow-right'>
										<span></span>
										</a>
										<div class='gallery_post_carousel'>" : '';
					foreach ( $image_srcs as $image_src ) {
						$img_obj = cws_thumb( $image_src, $thumbnail_dims , false );
						$img_url = isset( $img_obj[0] ) ? esc_url( $img_obj[0] ) : "";
						$retina_thumb_exists = false;
						$retina_thumb_url = "";
						if ( isset( $img_obj[3] ) && is_array($img_obj[3]) ){
							extract( $img_obj[3] );
						}
						?>
						<div class='pic'>
							<?php
							if ( $retina_thumb_exists ) {
								echo "<img src='$img_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
							}
							else{
								echo "<img src='$img_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
							}
							echo "<div class='hover-effect'></div>";
							?>
							<?php
								echo !$carousel ? "<div class='hover-effect'></div>" : "";
								echo "<a href='$image_src'" . ( $carousel ? " data-fancybox-group='$gallery_id'" : "" ) . " class='fancy post_media_link post_post_media_link posts_grid_post_media_link" . ( $carousel ? " fancy_gallery" : "" ) . "'></a>";
							?>
						</div>
						<?php
					}
					echo  $carousel ? "</div></div>" : '';
				}
			}
			break;
	}
	$buf1 = ob_get_contents();
	$meta_date_arr = array();
	/* Date */
	if ( !in_array( 'date', $post_hide_meta ) ){
		array_push( $meta_date_arr, "<div class='date-content'>" );
		$date = get_the_time( get_option("date_format") );
		if ( !empty( $date ) ){
			$date = explode(" ", $date);
			foreach ($date as $key => $value) {
				array_push( $meta_date_arr, "<span class='date-c'>".$value."</span>" );
			}						
		}
		array_push( $meta_date_arr, "</div>" );
	}
	$meta_date = !empty( $meta_date_arr ) ? implode( " ", $meta_date_arr ) : "";
	if ( !empty( $meta_date ) ){
		echo "<div class='meta_date'>";
			echo $meta_date;
		echo "</div>";		
	}

	if ( empty( $buf1 ) && !empty( $thumbnail ) ) {
		$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
		$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
		$retina_thumb_exists = false;
		$retina_thumb_url = "";	
		if ( isset( $thumb_obj[3] ) && is_array($thumb_obj[3]) ){
			extract( $thumb_obj[3] );
		}			
		$retina_thumb_url = esc_attr($retina_thumb_url);
		echo "<div class='pic'>";
			if ( $retina_thumb_exists ) {
				echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
			}
			else{
				echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
			}
			if($link_show != 'none'){
				echo "<div class='hover-effect'></div>";
			}
			if (!empty($media_meta['enable_lightbox']) && !empty($link_show) && $link_show == 'single_link' ){
				echo "<a class='fancy post_media_link post_post_media_link posts_grid_post_media_link' href='$thumbnail'></a>";
				
			}
			if($link_show != 'single_link'){
				echo "<a class='link_area_post_media post_media_link post_post_media_link posts_grid_post_media_link' href='$post_url'></a>";
			}
			
		echo "</div>";
		$thumb_media = true;
		$allow_cut_media = true;
	}
	$media_content = ob_get_clean();
	$some_media = !empty( $media_content );
	$floated_media = in_array( $layout, array( "medium", "small", 'checkerboard', 'fw_img' ) ) || ( $layout === "2" && empty( $sb_layout ) );	
	$cut_media = ( in_array( $layout, array( "small" ) ) || ( $layout === "2" && empty( $sb_layout ) ) ) && $allow_cut_media;
	$media_classes = array( "post_media", "post_post_media", "posts_grid_post_media" );
	$media_classes_str = implode( " ", $media_classes );
	if ( !empty( $media_content ) ){
		if ( $floated_media ){
			?>
				<div class="floated_media post_floated_media posts_grid_floated_media">
					<div class="floated_media_wrapper post_floated_media_wrapper posts_grid_floated_media_wrapper<?php echo $cut_media ? " cut_post_post_media" : ""; ?>">
					<?php
						echo "<div class='$media_classes_str'>";
							echo $media_content;
						echo "</div>";
					?>
					</div>
				</div>
			<?php
		}
		else{
			echo "<div class='$media_classes_str'>";
				echo $media_content;
			echo "</div>";			
		}
	}
	if ( $thumb_media ){
		wp_enqueue_script( 'fancybox' );
	}
}

function cws_vc_shortcode_post_posts_grid_post_content (){
	if(class_exists('WPBMap')){
		WPBMap::addAllMappedShortcodes();
	}
	global $post;
	global $more;
	$id = get_the_ID();
	$permalink = get_the_permalink( $id );
	$more = 0;
	$is_rtl = is_rtl();
	$def_grid_atts = array(
					'post_hide_meta'		=> array(),
					'chars_count'			=> '',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$chars_count = isset($chars_count) && !empty( $chars_count ) ? $chars_count : cws_vc_shortcode_post_posts_grid_get_chars_count();
	$content = $proc_content = $excerpt = $proc_excerpt = "";
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();
	$show_read_more = !in_array( 'read_more', $post_hide_meta );
	$text_excerpt = !in_array( 'excerpt', $post_hide_meta );
	$read_more = esc_html__( "READ MORE", 'cws-essentials' );
	$content = $post->post_content;
	$excerpt = $post->post_excerpt;
	$read_more_exists = false;
	if ( !empty( $excerpt ) ){
		$proc_content = get_the_excerpt();
		$read_more_exists = !empty( $content );
	}
	else if ( strpos( (string) $content, '<!--more-->' ) ){
		$proc_content = get_the_content( "" );
		$read_more_exists = true;
	}
	else if ( !empty( $content ) && !empty( $chars_count ) ){
		$proc_content = get_the_content( "" );
		$proc_content = trim( preg_replace( '/[\s]{2,}/u', ' ', strip_shortcodes( strip_tags( $proc_content ) ) ) );
		$chars_count = (int)$chars_count;
		$proc_content = mb_substr( $proc_content, 0, $chars_count );
		$read_more_exists = strlen( $proc_content ) < strlen( $content );
	}
	else{
		$proc_content = get_the_content( "" );		
	}
	if($text_excerpt){
		echo "<div class='post_content post_post_content posts_grid_post_content clearfix'>";	
			echo apply_filters( 'the_content', $proc_content );

		echo "</div>";		
	}
			
	if ( $read_more_exists && $show_read_more ){
		echo "<div class='clearfix btn-read-more'>";
			echo "<a href='$permalink' class='more-link'>$read_more</a>";
		echo "</div>";
	}
}
function cws_vc_shortcode_post_posts_grid_get_chars_count() {
	
	$def_blog_layout = "1";
	$def_grid_atts = array(
					'layout'	=> $def_blog_layout
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$number 	= NULL;
	$p_id 		= get_queried_object_id();
	$sb 		= function_exists("cws_vc_shortcode_get_sidebars") ? cws_vc_shortcode_get_sidebars( $p_id ) : "";
	$sb_layout 	= isset( $sb['layout_class'] ) ? $sb['layout_class'] : '';
	switch ( $layout ) {
		case '1':
		case 'medium':
		case 'small':
			switch ( $sb_layout ) {
				case 'double':
					$number = NULL;
					break;
				case 'single':
					$number = NULL;
					break;
				default:
					$number = NULL;
			}
			break;
		case '2':
			switch ( $sb_layout ) {
				case 'double':
					$number = 55;
					break;
				case 'single':
					$number = 90;
					break;
				default:
					$number = 130;
			}
			break;
		case '3':
			switch ( $sb_layout ) {
				case 'double':
					$number = 60;
					break;
				case 'single':
					$number = 60;
					break;
				default:
					$number = 70;
			}
			break;
	}
	return $number;
}

function cws_vc_shortcode_post_post_single_post_media (){
	$pid = get_the_id();
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );	
	$post_url = esc_url( get_the_permalink() );
	$post_format = get_post_format( );
	$eq_thumb_height = in_array( $post_format, array( 'gallery' ) );
	$media_meta = get_post_meta( get_the_ID(), 'cws_mb_post' );
	$media_meta = isset( $media_meta[0] ) ? $media_meta[0] : array();
	$thumbnail_props = has_post_thumbnail( ) ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];
	$thumbnail_dims = cws_vc_shortcode_get_post_thumbnail_dims( $eq_thumb_height, $real_thumbnail_dims, $post_format );
	$crop_thumb = isset( $thumbnail_dims['width'] ) && $thumbnail_dims['width'] > 0;
	$thumb_media = false;
	$some_media = false;
	ob_start();
	switch ($post_format) {
		case 'link':
			$link = isset( $media_meta['link'] ) ? esc_url( $media_meta['link'] ) : "";
			$link_title = isset( $media_meta['link_title'] ) ? esc_html( $media_meta['link_title'] ) : "";
			if ( !empty($thumbnail) ) {
				$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
				$thumb_url = isset( $thumb_obj[0] ) ? esc_url( $thumb_obj[0] ) : "";
				$thumbnail = esc_url($thumbnail);
				$retina_thumb_exists = false;
					$retina_thumb_url = "";
					if ( isset( $thumb_obj[3] ) ){
						extract( $thumb_obj[3] );
					}
				?>
				<div class="pic <?php echo !empty( $link ) ? 'link_post' : ''; ?>">
					<?php
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}	else {
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
					?>
					<div class="hover-effect"></div>
					<?php
					if ( !empty( $link ) ){
						if ( !empty( $link_title ) ){
							echo "<span class='post_media_link_title overlay'>$link_title</span>";
						}
						echo "<a class='post_media_link post_post_media_link post_single_post_media_link' href='$link'></a>";
					}
					else{
						echo "<a class='fancy post_media_link post_post_media_link post_single_post_media_link' href='$thumbnail'></a>";					
					}
					?>
				</div>
				<?php
				$thumb_media = true;
			}
			else{
				if ( !empty( $link ) ) {
					if ( !empty( $link_title ) ){
						echo "<div class='pic'>";
							echo "<span class='post_media_link_title'>$link_title</span>";
							echo "<a class='post_media_link post_post_media_link post_single_post_media_link' href='$link'></a>";
						echo "</div>";
					}
				}
			}
			break;
		case 'video':
			$video = isset($media_meta[$post_format]) ? $media_meta[$post_format] : "";
			if ( !empty( $video ) ) {
				echo "<div class='video'>" . apply_filters('the_content',"[embed width='" . $thumbnail_dims['width'] . "']" . $video . "[/embed]") . "</div>";
			}
			break;
		case 'audio':
			$audio = isset($media_meta[$post_format]) ? esc_attr( $media_meta[$post_format] ) : "";
			$is_soundcloud = is_int( strpos( (string) $audio, 'https://soundcloud' ) );
			if ( !empty( $thumbnail ) && !$is_soundcloud ){
				$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
				$thumb_url = isset( $thumb_obj[0] ) ? esc_url( $thumb_obj[0] ) : "";
				$thumbnail = esc_url($thumbnail);
				$retina_thumb_exists = false;
				$retina_thumb_url = "";
				if ( isset( $thumb_obj[3] ) ){
					extract( $thumb_obj[3] );
				}
				echo "<div class='pic'>";
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}	else {
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
				echo "</div>";
				if ( empty( $audio ) ){
					echo "<div class='hover-effect'></div>";
					echo "<a class='fancy post_media_link post_post_media_link post_single_post_media_link' href='$thumbnail'></a>";					
					$thumb_media = true;				
				}					
			}
			if ( !empty( $audio ) ){
				echo "<div class='audio" . ( $is_soundcloud ? " soundcloud" : "" ) . "'>";
					echo apply_filters( 'the_content', $audio );
				echo "</div>";
			}
			break;
		case 'quote':
			$quote = isset( $media_meta[$post_format]['quote'] ) ? $media_meta[$post_format]['quote'] : '';
			$author_name = isset( $media_meta[$post_format]['author_name'] ) ? $media_meta[$post_format]['author_name'] : '';				
			if ( !empty( $quote ) ) {
				echo "<div class='post_format_quote_media_wrapper'>";
					echo cws_vc_shortcode_quote_renderer( array(
						'thumbnail'			=> $thumbnail,
						'quote'				=> $quote,
						'author_name'		=> $author_name,
						'author_status'		=> $author_status
					));
				echo "</div>";
			}
			break;
		case 'gallery':
			$gallery = isset( $media_meta[$post_format] ) ? $media_meta[$post_format] : "";
			if ( !empty( $gallery ) ) {
				$match = preg_match_all("/\d+/",$gallery,$images);
				if ($match){
					$images = $images[0];
					$image_srcs = array();
					foreach ( $images as $image ) {
						$image_src = wp_get_attachment_image_src($image,'full');
						if ( $image_src ){
							$image_url = $image_src[0];
							array_push( $image_srcs, $image_url );
						}
					}
					$carousel = count($image_srcs) > 1 ? true : false;
					$gallery_id = uniqid( 'cws-gallery-' );
					echo  $carousel ? "<div class='gallery_post_carousel_wrapper'><a class='gallery_post_carousel_nav carousel_nav prev fa fa-long-arrow-left'>
										<span></span>
										</a>
										<a class='gallery_post_carousel_nav carousel_nav next fa fa-long-arrow-right'>
										<span></span>
										</a>
										<div class='gallery_post_carousel'>" : '';
					foreach ( $image_srcs as $image_src ) {
						$img_obj = cws_thumb( $image_src, $thumbnail_dims , false );
						$img_url = isset( $img_obj[0] ) ? esc_url( $img_obj[0] ) : "";
						$retina_thumb_exists = false;
						$retina_thumb_url = "";
						if ( isset( $img_obj[3] ) ){
							extract( $img_obj[3] );
						}
						?>
						<div class='pic'>
							<?php
							if ( $retina_thumb_exists ) {
								echo "<img src='$img_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
							}
							else{
								echo "<img src='$img_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
							}
							?>
							<?php
								echo !$carousel ? "<div class='hover-effect'></div>" : "";
								echo "<a href='$image_src'" . ( $carousel ? " data-fancybox-group='$gallery_id'" : "" ) . " class='fancy post_media_link post_post_media_link post_single_post_media_link" . ( $carousel ? " fancy_gallery" : "" ) . "'></a>";
							?>
						</div>
						<?php
					}
					echo  $carousel ? "</div></div>" : '';
				}
			}
			break;
	}
	$buf1 = ob_get_contents();
	if ( empty( $buf1 ) && !empty( $thumbnail ) ) {
		$thumb_obj = cws_thumb( $thumbnail, $thumbnail_dims, false );
		$thumb_url = isset( $thumb_obj[0] ) ? esc_url($thumb_obj[0]) : "";
		$retina_thumb_exists = false;
		$retina_thumb_url = "";	
		if ( isset( $thumb_obj[3] ) ){
			extract( $thumb_obj[3] );
		}			
		$retina_thumb_url = esc_attr($retina_thumb_url);
		echo "<div class='pic'>";
			if ( $retina_thumb_exists ) {
				echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
			}
			else{
				echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
			}
			echo "<div class='hover-effect'></div>";
			echo "<a class='fancy post_media_link post_post_media_link post_single_post_media_link' href='$thumbnail'></a>";
		echo "</div>";
		$thumb_media = true;
	}
	$media_content = ob_get_clean();
	$some_media = !empty( $media_content );
	$floated_media = $thumb_media && !$crop_thumb;
	$media_classes = array( "post_media", "post_post_media", "post_single_post_media" );
	$media_classes_str = implode( " ", $media_classes );
	if ( !empty( $media_content ) ){
		if ( $floated_media ){
			?>
				<div class="floated_media post_floated_media post_single_floated_media">
					<div class="floated_media_wrapper post_floated_media_wrapper post_single_floated_media_wrapper">
					<?php
						echo "<div class='$media_classes_str'>";
							echo $media_content;
						echo "</div>";
					?>
					</div>
				</div>
			<?php
		}
		else{
			echo "<div class='$media_classes_str'>";
				echo $media_content;
			echo "</div>";			
		}
	}
	if ( $thumb_media ){
		wp_enqueue_script( 'fancybox' );
	}
}

function cws_vc_shortcode_post_single_post_meta (){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$meta = "";
	$meta_arr = array();
	/* Author */
	$author = get_the_author();
	if ( !empty($author) ){
		array_push( $meta_arr, "<i class='cwsicon-people'></i><span class='author'>$author</span>" );
	}
	/* Categories */
	$cats = cws_vc_shortcode_get_post_term_links_str( "category" );
	if ( !empty( $cats ) ){
		array_push( $meta_arr, "<span class='cats'>on $cats</span>" );
	}
	/* Date */
	$date = get_the_time( get_option("date_format") );
	if ( !empty( $date ) ){
		array_push( $meta_arr, "<span class='date'>$date</span>" );
	}
	echo "<div class='post_meta post_post_meta post_single_post_meta'>";
		echo implode( "&#x20;", $meta_arr );
	echo "</div>";
}

function cws_vc_shortcode_post_single_post_secondary_meta (){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$tags_content = $social_content = "";
	$tags = cws_vc_shortcode_get_post_term_links_str( "post_tag" );
	if ( !empty( $tags ) ){
		$tags_content .= "<i class='cwsicon-aasana-tag'></i><b>$tags</b>";
	}
	if ( cws_vc_shortcode_check_for_plugin('social-warfare/social-warfare.php') ){
		$social_content = do_shortcode( "[social_warfare]" );
	}
	if ( !empty( $tags ) || !empty( $social_content ) ){
		echo "<div class='post_sec_meta post_post_sec_meta post_single_post_sec_meta'>";
			echo !empty( $tags ) ? "<div class='post_tags post_post_tags post_single_post_tags'>$tags_content</div>" : "";
			echo !empty( $social_content ) ? "<div class='post_socials post_post_socials post_single_post_socials'>$social_content</div>" : "";
		echo "</div>";
	}	
}

function cws_vc_shortcode_post_posts_grid_post_meta (){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'				=> '1',
					'post_hide_meta'		=> array(),
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	$meta = "";
	$meta_arr = array();
	$post_hide_meta = isset($post_hide_meta) && !empty($post_hide_meta) ? $post_hide_meta : array();
	/* Author */
	if ( !in_array( 'author', $post_hide_meta ) ){
		$author = get_the_author();
		if ( !empty($author) ){
			ob_start();
				the_author_posts_link();
			$author_link = ob_get_clean();

			array_push( $meta_arr,"<div class='info'>".esc_html__('by ', 'cws-essentials')."<span class='post_author'>".$author_link."</span></div>" );
		}
	}
	if ( !in_array( 'likes', $post_hide_meta ) ){
		array_push( $meta_arr, "<div class='like new_style'>".cws_vc_shortcode_get_simple_likes_button( get_the_ID() )."</div>" );
	}	
	/* Special PF */
	$special_pf = cws_vc_shortcode_is_special_post_format();
	if ( $special_pf ){
		array_push( $meta_arr, "<span class='pf'>" . cws_vc_shortcode_post_format_mark() . "</span>" );
	}
	/* Comments */
	if ( !in_array( 'comments', $post_hide_meta ) ){
		$comments_n = get_comments_number();
		if ( (int) $comments_n > 0 ) {
			$permalink .= "#comments";
			array_push( $meta_arr, "<a href='$permalink' class='comments_link'><i class='fa fa-comment-o'></i> $comments_n <span> ".esc_html__('comments', 'cws-essentials')."</span></a>" );
		}
	}
	/* Output */
	$meta_date = !empty( $meta_date_arr ) ? implode( " ", $meta_date_arr ) : "";
	if ( !empty( $meta_date ) ){
		echo "<div class='meta_date'>";
			echo $meta_date;
		echo "</div>";		
	}

	$meta = !empty( $meta_arr ) ? implode( $meta_arr ) : "";
	if ( !empty( $meta ) ){
		echo "<div class='post_meta post_info post_post_meta posts_grid_post_meta'>";
			echo $meta;
		echo "</div>";		
	}
}
function cws_vc_shortcode_post_posts_grid_post_cats (){
	$cats = "";
	if ( has_category() ) {
		ob_start();
		the_category ( " " );
		$cats .= ob_get_clean();
	}
	if ( !empty( $cats ) ){
		echo "<div class='post_terms post_meta post_post_terms posts_grid_post_terms'>";
			echo $cats;
		echo "</div>";
	}
}
function cws_vc_shortcode_post_posts_grid_post_tags (){
	$tags = "";
	if ( has_tag() ) {
		ob_start();
		the_tags ( "", " ", "" );
		$tags .= ob_get_clean();
	}
	if ( !empty( $tags ) ){
		echo "<div class='post_terms post_meta post_post_terms posts_grid_post_terms'>";
			echo $tags;
		echo "</div>";
	}	
}

add_action( 'wp_enqueue_scripts', 'cws_vc_shortcode_sl_enqueue_scripts' );
function cws_vc_shortcode_sl_enqueue_scripts() {
	wp_enqueue_script( 'simple-likes-public-js', CWS_SHORTCODES_PLUGIN_URL . '/assets/js/simple-likes-public.js', array( 'jquery' ), '0.5', false );
	wp_localize_script( 'simple-likes-public-js', 'simpleLikes', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'like' => esc_html__( 'Like', 'cws-essentials' ),
		'unlike' => esc_html__( 'Unlike', 'cws-essentials' )
		) ); 
}
/**
 * Processes like/unlike
 * @since    0.5
 */
add_action( 'wp_ajax_nopriv_cws_vc_shortcode_process_simple_like', 'cws_vc_shortcode_process_simple_like' );
add_action( 'wp_ajax_cws_vc_shortcode_process_simple_like', 'cws_vc_shortcode_process_simple_like' );
function cws_vc_shortcode_process_simple_like() {
	// Security
	$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( $_REQUEST['nonce'] ) : 0;
	if ( !wp_verify_nonce( $nonce, 'simple-likes-nonce' ) ) {
		exit( esc_html__( 'Not permitted', 'cws-essentials' ) );
	}
	// Test if javascript is disabled
	$disabled = ( isset( $_REQUEST['disabled'] ) && $_REQUEST['disabled'] == true ) ? true : false;
	// Test if this is a comment
	$is_comment = ( isset( $_REQUEST['is_comment'] ) && $_REQUEST['is_comment'] == 1 ) ? 1 : 0;
	// Base variables
	$post_id = ( isset( $_REQUEST['post_id'] ) && is_numeric( $_REQUEST['post_id'] ) ) ? $_REQUEST['post_id'] : '';
	$result = array();
	$post_users = NULL;
	$like_count = 0;
	// Get plugin options
	if ( $post_id != '' ) {
		$count = ( $is_comment == 1 ) ? get_comment_meta( $post_id, "_comment_like_count", true ) : get_post_meta( $post_id, "_post_like_count", true ); // like count
		$count = ( isset( $count ) && is_numeric( $count ) ) ? $count : 0;
		if ( !cws_vc_shortcode_already_liked( $post_id, $is_comment ) ) { // Like the post
			if ( is_user_logged_in() ) { // user is logged in
				$user_id = get_current_user_id();
				$post_users = cws_vc_shortcode_post_user_likes( $user_id, $post_id, $is_comment );
				if ( $is_comment == 1 ) {
					// Update User & Comment
					$user_like_count = get_user_option( "_comment_like_count", $user_id );
					$user_like_count =  ( isset( $user_like_count ) && is_numeric( $user_like_count ) ) ? $user_like_count : 0;
					update_user_option( $user_id, "_comment_like_count", ++$user_like_count );
					if ( $post_users ) {
						update_comment_meta( $post_id, "_user_comment_liked", $post_users );
					}
				} else {
					// Update User & Post
					$user_like_count = get_user_option( "_user_like_count", $user_id );
					$user_like_count =  ( isset( $user_like_count ) && is_numeric( $user_like_count ) ) ? $user_like_count : 0;
					update_user_option( $user_id, "_user_like_count", ++$user_like_count );
					if ( $post_users ) {
						update_post_meta( $post_id, "_user_liked", $post_users );
					}
				}
			} else { // user is anonymous
				$user_ip = cws_vc_shortcode_sl_get_ip();
				$post_users = cws_vc_shortcode_post_ip_likes( $user_ip, $post_id, $is_comment );
				// Update Post
				if ( $post_users ) {
					if ( $is_comment == 1 ) {
						update_comment_meta( $post_id, "_user_comment_IP", $post_users );
					} else { 
						update_post_meta( $post_id, "_user_IP", $post_users );
					}
				}
			}
			$like_count = ++$count;
			$response['status'] = "liked";
			$response['icon'] = cws_vc_shortcode_get_liked_icon();
		} else { // Unlike the post
			if ( is_user_logged_in() ) { // user is logged in
				$user_id = get_current_user_id();
				$post_users = cws_vc_shortcode_post_user_likes( $user_id, $post_id, $is_comment );
				// Update User
				if ( $is_comment == 1 ) {
					$user_like_count = get_user_option( "_comment_like_count", $user_id );
					$user_like_count =  ( isset( $user_like_count ) && is_numeric( $user_like_count ) ) ? $user_like_count : 0;
					if ( $user_like_count > 0 ) {
						update_user_option( $user_id, "_comment_like_count", --$user_like_count );
					}
				} else {
					$user_like_count = get_user_option( "_user_like_count", $user_id );
					$user_like_count =  ( isset( $user_like_count ) && is_numeric( $user_like_count ) ) ? $user_like_count : 0;
					if ( $user_like_count > 0 ) {
						update_user_option( $user_id, '_user_like_count', --$user_like_count );
					}
				}
				// Update Post
				if ( $post_users ) {	
					$uid_key = array_search( $user_id, $post_users );
					unset( $post_users[$uid_key] );
					if ( $is_comment == 1 ) {
						update_comment_meta( $post_id, "_user_comment_liked", $post_users );
					} else { 
						update_post_meta( $post_id, "_user_liked", $post_users );
					}
				}
			} else { // user is anonymous
				$user_ip = cws_vc_shortcode_sl_get_ip();
				$post_users = cws_vc_shortcode_post_ip_likes( $user_ip, $post_id, $is_comment );
				// Update Post
				if ( $post_users ) {
					$uip_key = array_search( $user_ip, $post_users );
					unset( $post_users[$uip_key] );
					if ( $is_comment == 1 ) {
						update_comment_meta( $post_id, "_user_comment_IP", $post_users );
					} else { 
						update_post_meta( $post_id, "_user_IP", $post_users );
					}
				}
			}
			$like_count = ( $count > 0 ) ? --$count : 0; // Prevent negative number
			$response['status'] = "unliked";
			$response['icon'] = cws_vc_shortcode_get_unliked_icon();
		}
		if ( $is_comment == 1 ) {
			update_comment_meta( $post_id, "_comment_like_count", $like_count );
			update_comment_meta( $post_id, "_comment_like_modified", date( 'Y-m-d H:i:s' ) );
		} else { 
			update_post_meta( $post_id, "_post_like_count", $like_count );
			update_post_meta( $post_id, "_post_like_modified", date( 'Y-m-d H:i:s' ) );
		}
		$response['count'] = get_like_count( $like_count );
		$response['testing'] = $is_comment;
		if ( $disabled == true ) {
			if ( $is_comment == 1 ) {
				wp_redirect( get_permalink( get_the_ID() ) );
				exit();
			} else {
				wp_redirect( get_permalink( $post_id ) );
				exit();
			}
		} else {
			wp_send_json( $response );
		}
	}
}

function cws_vc_shortcode_already_liked( $post_id, $is_comment ) {
	$post_users = NULL;
	$user_id = NULL;
	if ( is_user_logged_in() ) { // user is logged in
		$user_id = get_current_user_id();
		$post_meta_users = ( $is_comment == 1 ) ? get_comment_meta( $post_id, "_user_comment_liked" ) : get_post_meta( $post_id, "_user_liked" );
		if ( count( $post_meta_users ) != 0 ) {
			$post_users = $post_meta_users[0];
		}
	} else { // user is anonymous
		$user_id = cws_vc_shortcode_sl_get_ip();
		$post_meta_users = ( $is_comment == 1 ) ? get_comment_meta( $post_id, "_user_comment_IP" ) : get_post_meta( $post_id, "_user_IP" ); 
		if ( count( $post_meta_users ) != 0 ) { // meta exists, set up values
			$post_users = $post_meta_users[0];
		}
	}
	if ( is_array( $post_users ) && in_array( $user_id, $post_users ) ) {
		return true;
	} else {
		return false;
	}
}

function cws_vc_shortcode_get_simple_likes_button( $post_id, $is_comment = NULL ) {
	$is_comment = ( NULL == $is_comment ) ? 0 : 1;
	$output = '';
	$nonce = wp_create_nonce( 'simple-likes-nonce' ); // Security
	if ( $is_comment == 1 ) {
		$post_id_class = esc_attr( ' sl-comment-button-' . $post_id );
		$comment_class = esc_attr( ' sl-comment' );
		$like_count = get_comment_meta( $post_id, "_comment_like_count", true );
		$like_count = ( isset( $like_count ) && is_numeric( $like_count ) ) ? $like_count : 0;
	} else {
		$post_id_class = esc_attr( ' sl-button-' . $post_id );
		$comment_class = esc_attr( '' );
		$like_count = get_post_meta( $post_id, "_post_like_count", true );
		$like_count = ( isset( $like_count ) && is_numeric( $like_count ) ) ? $like_count : 0;
	}
	$count = get_like_count( $like_count );
	$icon_empty = cws_vc_shortcode_get_unliked_icon();
	$icon_full = cws_vc_shortcode_get_liked_icon();
	// Loader
	$loader = '<span class="sl-loader"></span>';
	// Liked/Unliked Variables
	if ( cws_vc_shortcode_already_liked( $post_id, $is_comment ) ) {
		$class = esc_attr( ' liked' );
		$title = esc_html__( 'Unlike', 'cws-essentials' );
		$icon = $icon_full;
	} else {
		$class = '';
		$title = esc_html__( 'Like', 'cws-essentials' );
		$icon = $icon_empty;
	}
	$output = '<span class="sl-wrapper"><a href="' . admin_url( 'admin-ajax.php?action=cws_vc_shortcode_process_simple_like' . '&post_id=' . $post_id . '&nonce=' . $nonce . '&is_comment=' . $is_comment . '&disabled=true' ) . '" class="sl-button' . $post_id_class . $class . $comment_class . '" data-nonce="' . $nonce . '" data-post-id="' . $post_id . '" data-iscomment="' . $is_comment . '" title="' . $title . '">' . $icon . $count . '</a>' . $loader . '</span>';
	return $output;
} 

add_shortcode( 'jmliker', 'cws_vc_shortcode_sl_shortcode' );
function cws_vc_shortcode_sl_shortcode() {
	return cws_vc_shortcode_get_simple_likes_button( get_the_ID(), 0 );
}

function cws_vc_shortcode_post_user_likes( $user_id, $post_id, $is_comment ) {
	$post_users = '';
	$post_meta_users = ( $is_comment == 1 ) ? get_comment_meta( $post_id, "_user_comment_liked" ) : get_post_meta( $post_id, "_user_liked" );
	if ( count( $post_meta_users ) != 0 ) {
		$post_users = $post_meta_users[0];
	}
	if ( !is_array( $post_users ) ) {
		$post_users = array();
	}
	if ( !in_array( $user_id, $post_users ) ) {
		$post_users['user-' . $user_id] = $user_id;
	}
	return $post_users;
}

function cws_vc_shortcode_post_ip_likes( $user_ip, $post_id, $is_comment ) {
	$post_users = '';
	$post_meta_users = ( $is_comment == 1 ) ? get_comment_meta( $post_id, "_user_comment_IP" ) : get_post_meta( $post_id, "_user_IP" );
	// Retrieve post information
	if ( count( $post_meta_users ) != 0 ) {
		$post_users = $post_meta_users[0];
	}
	if ( !is_array( $post_users ) ) {
		$post_users = array();
	}
	if ( !in_array( $user_ip, $post_users ) ) {
		$post_users['ip-' . $user_ip] = $user_ip;
	}
	return $post_users;
}

function cws_vc_shortcode_sl_get_ip() {
	if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	}
	$ip = filter_var( $ip, FILTER_VALIDATE_IP );
	$ip = ( $ip === false ) ? '0.0.0.0' : $ip;
	return $ip;
}

function cws_vc_shortcode_get_liked_icon() {
	/* If already using Font Awesome with your theme, replace svg with: <i class="fa fa-heart"></i> */
	$icon = '<span class="sl-icon unliked"></span>';
	return $icon;
}

function cws_vc_shortcode_get_unliked_icon() {
	/* If already using Font Awesome with your theme, replace svg with: <i class="fa fa-heart-o"></i> */
	$icon = '<span class="sl-icon liked"></span>';
	return $icon;
}

function cws_vc_shortcode_sl_format_count( $number ) {
	$precision = 2;
	if ( $number >= 1000 && $number < 1000000 ) {
		$formatted = number_format( $number/1000, $precision ).'K';
	} else if ( $number >= 1000000 && $number < 1000000000 ) {
		$formatted = number_format( $number/1000000, $precision ).'M';
	} else if ( $number >= 1000000000 ) {
		$formatted = number_format( $number/1000000000, $precision ).'B';
	} else {
		$formatted = $number; // Number is less than 1000
	}
	$formatted = str_replace( '.00', '', $formatted );
	return $formatted;
}

function get_like_count( $like_count ) {
	$like_text = esc_html__( '0', 'cws-essentials' );
	if ( is_numeric( $like_count ) && $like_count > 0 ) { 
		$number = cws_vc_shortcode_sl_format_count( $like_count );
	} else {
		$number = $like_text;
	}
	$count = '<span class="sl-count">' . $number .esc_html__('likes', 'cws-essentials'). '</span>';
	return $count;
}
// User Profile List
add_action( 'show_user_profile', 'cws_vc_shortcode_show_user_likes' );
add_action( 'edit_user_profile', 'cws_vc_shortcode_show_user_likes' );
function cws_vc_shortcode_show_user_likes( $user ) { ?>        
	<table class="form-table">
		<tr>
			<th><label for="user_likes"><?php esc_html_e( 'You Like:', 'cws-essentials' ); ?></label></th>
			<td>
				<?php
				$types = get_post_types( array( 'public' => true ) );
				$args = array(
					'numberposts' => -1,
					'post_type' => $types,
					'meta_query' => array (
						array (
							'key' => '_user_liked',
							'value' => $user->ID,
							'compare' => 'LIKE'
							)
						) );		
				$sep = '';
				$like_query = new WP_Query( $args );
				if ( $like_query->have_posts() ) : ?>
					<p>
						<?php while ( $like_query->have_posts() ) : $like_query->the_post(); 
						echo sprintf('%s', $sep); ?><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						<?php
						$sep = ' &middot; ';
						endwhile; 
						?>
					</p>
				<?php else : ?>
				<p><?php esc_html_e( 'You do not like anything yet.', 'cws-essentials' ); ?></p>
				<?php 
				endif; 
				wp_reset_postdata(); 
				?>
			</td>
		</tr>
	</table>
<?php }
?>