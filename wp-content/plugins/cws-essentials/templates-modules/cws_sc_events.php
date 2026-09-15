<?php
function cws_vc_shortcode_tribe_events_posts_grid ( $atts = array(), $content = "" ){
	/**
		* Check if events calendar plugin method exists
	*/
	if ( !function_exists( 'tribe_get_events' ) ) {
		return;
	}
	
	if (!isset($_REQUEST['nonce']) || !wp_verify_nonce( $_REQUEST['nonce'], "cws_vc_sh_nonce")) {
		$full_width = isset($GLOBALS['cws_row_atts']) && !empty($GLOBALS['cws_row_atts']) ? $GLOBALS['cws_row_atts'] : "";
		if(!empty($full_width)){
			$atts['full_width'] = $full_width;
		}
		wp_localize_script('jquery-ajax-shortcode', 'cws_vc_sh_atts', array(
			'cws_events' => json_encode($atts),
		));	
		ob_start();
			echo cws_vc_shortcode_loader_html();
		$loader = ob_get_clean();

    	return "<div class='cws_wrapper_events'>".$loader."</div>";
   	}   

	$out = "";	

	global $wp_query, $post;

	$output = '';
	$atts = isset($_POST['data']) && !empty($_POST['data']) ? $_POST['data'] : $atts;

	$defaults = shortcode_atts( apply_filters( 'cws_shortcode_atts', array(		
		'title'									=> '',
		'title_align'							=> 'left',
		'total_items_count'						=> '10',
		'display_style'							=> 'grid',
		'layout'								=> 'def',
		'items_pp'								=> esc_html( get_option( 'posts_per_page' ) ),
		'paged'									=> 1,
		'hide_data_override'					=> false,
		'data_to_hide'							=> '',
		'tax'									=> '',
		'addl_query_args'						=> array(),
		'el_class'								=> '',
		'crop_images'							=> '',
		'full_width'							=> false,
		'pagination_grid'						=> '',
		'cat' 									=> '',
		'month' 								=> '',
		'customize_colors' 						=> '',
		'custom_color' 							=> '',
		'bg_color' 								=> '',
		'limit' 								=> 10,
		'eventdetails' 							=> 'true',
		'time' 									=> null,
		'past' 									=> null,
		'venue' 								=> 'true',
		'author' 								=> null,
		'message' 								=> 'There are no upcoming events at this time.',
		'key' 									=> 'End Date',
		'order' 								=> 'ASC',
		'orderby' 								=> 'startdate',
		'viewall' 								=> 'false',
		'excerpt' 								=> 'true',
		'thumb' 								=> 'true',
		'thumbsize' 							=> '',
		'thumbwidth' 							=> '',
		'thumbheight' 							=> '',
		'time' 									=> '',
		'add_shadow' 							=> '',
		'meta_date' 							=> '',
		'month' 								=> '',
		'tribe_events_hide_meta_override' 		=> '',
		'tribe_events_hide_meta' 				=> '',
		'chars_count' 							=> '',
		'contentorder' 							=> apply_filters( 'cws_default_contentorder', 'title, thumbnail, excerpt, date, venue', $atts ),
		'event_tax' 							=> '',
		), $atts ), $atts, 'cws-list-events' );

	$atts = shortcode_atts( $defaults, $atts );
	extract( $atts );

		// Past Event
	$meta_date_compare = '>=';
	$meta_date_date = current_time( 'Y-m-d H:i:s' );

	if ( isset($atts['time']) && $atts['time'] == 'past' || !empty( $atts['past'] ) ) {
		$meta_date_compare = '<';
	}
		// Key
	if ( str_replace( ' ', '', trim( strtolower( $atts['key'] ) ) ) == 'startdate' ) {
		$atts['key'] = '_EventStartDate';
	} else {
		$atts['key'] = '_EventEndDate';
	}
		// Orderby
	if ( str_replace( ' ', '', trim( strtolower( $atts['orderby'] ) ) ) == 'enddate' ) {
		$atts['orderby'] = '_EventEndDate';
	} else {
		$atts['orderby'] = '_EventStartDate';
	}

		// Date
	$atts['meta_date'] = array(
		array(
			'key' => $atts['key'],
			'value' => $meta_date_date,
			'compare' => $meta_date_compare,
			'type' => 'DATETIME'
			)
		);

		// Specific Month
	if ( 'current' == $atts['month'] ) {
		$atts['month'] = current_time( 'Y-m' );
	}
	if ( 'next' == $atts['month'] ) {
		$atts['month'] = date( 'Y-m', strtotime( '+1 months', current_time( 'timestamp' ) ) );
	}
	if ($atts['month']) {
		$month_array = explode("-", $atts['month']);

		$month_yearstr = $month_array[0];
		$month_monthstr = $month_array[1];
		$month_startdate = date( "Y-m-d", strtotime( $month_yearstr . "-" . $month_monthstr . "-01" ) );
		$month_enddate = date( "Y-m-01", strtotime( "+1 month", strtotime( $month_startdate ) ) );

		$atts['meta_date'] = array(
			array(
				'key' => $atts['key'],
				'value' => array($month_startdate, $month_enddate),
				'compare' => 'BETWEEN',
				'type' => 'DATETIME'
				)
			);
	}

	$tribe_events_hide_meta_override = !empty( $tribe_events_hide_meta_override ) ? true : false;
	$data_to_hide = explode( ",", $tribe_events_hide_meta );
	$data_to_hide = $tribe_events_hide_meta_override ? $data_to_hide : array();

	$terms = isset( $atts[ $tax . "_terms" ] ) ? $atts[ $tax . "_terms" ] : "";

	$section_id = uniqid( 'cws_events_posts_grid_' );
	$ajax_data = array();
	$total_items_count = !empty( $total_items_count ) ? (int)$total_items_count : PHP_INT_MAX;
	$items_pp = !empty( $items_pp ) ? (int)$items_pp : esc_html( get_option( 'posts_per_page' ) );
	$paged = (int)$paged;

	$def_layout = "1";
	$layout = ( empty( $layout ) || $layout === "def" ) ? $def_layout : $layout; 

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
	$query_args = array('post_type'			=> 'tribe_events',
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
	$full_width = isset($GLOBALS['cws_row_atts']) && !empty($GLOBALS['cws_row_atts']) ? $GLOBALS['cws_row_atts'] : "";
	$full_width = isset($atts['full_width']) && !empty($atts['full_width']) ? $atts['full_width'] : $full_width;
	$query_args['orderby'] 	= "menu_order date title";
	$query_args['order']	= "ASC";
	$query_args = array_merge( $query_args, $addl_query_args );

	
	$query_args_events = array(
		'hide_upcoming' => true,
		'meta_key' => ( trim( $atts['orderby'] ) ? $atts['orderby'] : $atts['key'] ),
		'orderby' => 'meta_value',
		'order' => $atts['order'],
	);

	$query_args = array_merge($query_args, $query_args_events);
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
	$pagination_grid = isset($pagination_grid) && !empty($pagination_grid) ? $pagination_grid : '';
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
	/********/
	echo "<section id='$section_id' class='posts_grid tribe_events_posts_grid{$isotope_init} posts_grid_{$layout} posts_grid_{$display_style}" . ( $dynamic_content ? " dynamic_content" : "" ) . ( !empty( $el_class ) ? " $el_class" : "" ) . " clearfix'>";
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
					echo "<nav class='nav tribe_events_nav posts_grid_nav text_align{$title_align}'>";
						echo "<ul class='dots'>";
						echo "<li class='cws_post_select_dots circle'></li>";
						echo "<li class='dot'>";
						echo "<a href class='nav_item tribe_events_nav_item posts_grid_nav_item active' data-nav-val='_all_'>";
						echo "<span class='title_nav_events'><span class='txt_title'>" . esc_html__( 'All', 'cws-essentials' );
						echo "</span>";
						echo "</span>";
						echo "<span class='circle'></span>";
						echo "</a>";
						echo "</li>";
						foreach ( $filter_vals as $term_slug => $term_name ){
							echo "<li class='dot'>";
							echo "<a href class='nav_item tribe_events_nav_item posts_grid_nav_item' data-nav-val='" . esc_html( $term_slug ) . "'>";
							echo "<span class='title_nav_events'>" . esc_html( $term_name );
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
			echo "<div class='" . ( $is_carousel ? "cws_vc_shortcode_carousel events_carousel grid-".( is_numeric( $layout ) ? $layout : "4"  ) : "cws_vc_shortcode_grid" . ( ( in_array( $layout, array( "2", "3", "4" ) ) || $dynamic_content ) ? " isotope" : "" ) ) . "'" . ( $is_carousel ? " data-cols='" . ( !is_numeric( $layout ) ? "1" : $layout ) . "'" : "" ) . ">";
				
				echo apply_filters( 'cws_start_tag', '<div class="cws-event-list">', $atts );			
				$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
					'post_type'						=> 'tribe_events',
					'tribe_events_data_to_hide'		=> $data_to_hide,
					'layout'						=> $layout,
					'sb_layout'						=> $sb_layout,
					'crop_images'					=> $crop_images,
					'full_width'					=> $full_width,
					'total_items_count'				=> $total_items_count,
					'customize_colors' 				=> $customize_colors,
					'custom_color' 					=> $custom_color,
					'bg_color' 						=> $bg_color,
					'add_shadow' 					=> $add_shadow,
					);
				if(!$is_carousel){
					echo "<div class='grid-sizer'></div>";
				}
				if ( function_exists( "cws_vc_shortcode_tribe_events_posts_grid_posts" ) ){
					call_user_func_array( "cws_vc_shortcode_tribe_events_posts_grid_posts", array( $q,$atts ) );
				}
				unset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] );
				echo apply_filters( 'cws_end_tag', '</div>', $atts );

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
			$ajax_data['post_type']							= 'tribe_events';
			$ajax_data['tribe_events_data_to_hide']			= $data_to_hide;
			$ajax_data['layout']							= $layout;
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
			$ajax_data['pagination_grid']					=  $pagination_grid;
			$ajax_data['add_shadow']						=  $add_shadow;
			$ajax_data_str = json_encode( $ajax_data );
			echo "<form id='{$section_id}_data' class='ajax_data_form tribe_events_ajax_data_form posts_grid_ajax_data_form'>";
				echo "<input type='hidden' id='{$section_id}_ajax_data' class='ajax_data tribe_events_ajax_data posts_grid_ajax_data' name='{$section_id}_ajax_data' value='$ajax_data_str' />";
			echo "</form>";
		}
	echo "</section>";
	$out = ob_get_clean();
	echo json_encode(array('result' => $out ) );
	wp_die(); //Added
}

function cws_vc_shortcode_tribe_events_posts_grid_posts ( $q = null, $atts = null ){
	if ( !isset( $q ) ) return;
	$def_grid_atts = array(
					'layout'						=> '1',
					'full_width'					=> '',
					'total_items_count'				=> PHP_INT_MAX
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	global $wp_query, $post;
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
			cws_vc_shortcode_tribe_events_posts_grid_post ($atts);
		endwhile;
		wp_reset_postdata();
		ob_end_flush();
	endif;				
}
function cws_vc_shortcode_get_tribe_events_thumbnail_dims ( $eq_thumb_height = false, $real_dims = array() ) {
	$def_atts = array(
					'layout'				=> '1',
					'sb_layout'				=> '',
					'full_width'			=> '',
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
	$full_width = isset($full_width['full_width']) && ($full_width['full_width'] == 'stretch_row_content' || $full_width['full_width'] == 'stretch_row_content_no_spaces')  ? true : false;


	$dims = array( 'width' => 0, 'height' => 0 );
	if ($full_width){
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
			case "medium":
				$dims['width'] = 570;
				if ( !isset( $real_dims['height'] ) ){
					$dims['height'] = 321;
				}	
				break;
			case "small":		
				$dims['width'] = 180;
				$dims['height'] = 180;
				$dims['crop'] = true;
				break;
			case '2':
				if ( empty( $sb_layout ) ){	
					$dims['width'] = 270;
					$dims['height'] = 270;
				}
				else if ( $sb_layout === "single" ){
					$dims['width'] = 420;
					$dims['height'] = 420;	
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
function cws_vc_shortcode_get_tribe_events_chars_count ( $cols = null ){
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
				$number = 80;				
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
		case 'small':
			if ( $sb_layout === "single" ){
				$number = 16;
			}
			else{
				$number = 90;				
			}
			break;
	}
	return $number;
}
function cws_vc_shortcode_tribe_events_posts_grid_post ($atts = null){
	$def_grid_atts = array(
		'layout'						=> '1',
		'tribe_events_data_to_hide'		=> array(),
	);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;
	extract( $grid_atts );
	global $wp_query, $post;
	$pid 		= get_the_id();
	$uniq_pid 	= uniqid( "tribe_events_post_" );
	$post_url 	= get_the_permalink( $pid );
	$post_url 	= esc_url( $post_url );
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';	

	$data_to_hide = $tribe_events_data_to_hide ? $tribe_events_data_to_hide : array();
	ob_start();
		//cws_vc_shortcode_tribe_events_posts_grid_post_title ();
		cws_vc_shortcode_tribe_events_posts_grid_post_terms ();	
	$prim_post_data = ob_get_clean();
	ob_start();
	if ( !empty( $prim_post_data ) ){
		echo "<div class='prim_post_data'>";
			echo $prim_post_data;
		echo "</div>";
	}
	ob_start();
	cws_vc_shortcode_tribe_events_posts_grid_post_content ();
	$sec_post_data = ob_get_clean();
	if ( !empty( $sec_post_data ) ){
		echo "<div class='sec_post_data'>";
			echo $sec_post_data;
		echo "</div>";
	}
	$post_data 		= ob_get_clean();
	$has_post_data 	= true;
	$link_class 	= "post_link tribe_events_post_link posts_grid_post_link";

	echo "<article id='{$uniq_pid}' class='item post tribe_events_post posts_grid_post" . ( $has_post_data ? " has_post_data" : "" ) . " clearfix'>";
		if ( $has_post_data ){
			
			echo "<style id='{$uniq_pid}_hover_style' type='text/css' scoped>";
				if(!empty($customize_colors)){
					if(!empty($bg_color)){
						echo "#{$uniq_pid} .post_wrapper{background: $bg_color;}";
					}
					if(!empty($custom_color)){
						echo "#{$uniq_pid} .posts_grid.tribe_events_posts_grid .entry-title,#{$uniq_pid} .posts_grid.tribe_events_posts_grid .entry-title a,#{$uniq_pid} .duration.time:before,#{$uniq_pid} .duration.venue:before,
						 {color: $custom_color;}";
					}
				}
			echo "</style>";

			/*if(!empty($styles)){
				Cws_shortcode_css()->enqueue_cws_css($styles);
			}*/
			
		}
		echo "<div class='post_wrapper tribe_events_post_wrapper posts_grid_post_wrapper".(!empty($add_shadow) ? ' add_shadow' : "")."'>";
			cws_vc_shortcode_tribe_events_posts_grid_post_media ($atts);
			echo "<div class='post_content_events'>";
			$atts['contentorder'] = explode( ',', $atts['contentorder'] );
			$event_output = '';
			if ( !in_array( 'date', $data_to_hide ) ){
				if ( isValid( $atts['eventdetails'] ) ) {
					$event_output .= apply_filters( 'cws_event_date_thumb', '<div class="date_thumb"><div class="day">' . tribe_get_start_date( null, false, 'j' ) . '</div><div class="month">' . tribe_get_start_date( null, false, 'M' ) . ',</div><div class="year">' . tribe_get_start_date( null, false, 'Y' ) . '</div></div>', $atts, $pid );
				}
			}
			if ( !in_array( 'title', $data_to_hide ) ){
				$event_output .= apply_filters( 'cws_event_title_tag_start', '<h4 class="entry-title summary">', $atts, $pid ) .
				apply_filters( 'cws_event_list_title_link_start', '<a href="' . tribe_get_event_link() . '" rel="bookmark">', $atts, $pid ) . apply_filters( 'cws_event_list_title', get_the_title(), $atts, $pid ) . apply_filters( 'cws_event_list_title_link_end', '</a>', $atts, $pid ) .
				apply_filters( 'cws_event_title_tag_end', '</h4>', $atts, $pid );
			}

			if ( !in_array( 'excerpt', $data_to_hide ) ){
				if ( isValid( $atts['excerpt'] ) ) {
					$excerptLength = isset($atts['chars_count']) && !empty($atts['chars_count']) ? $atts['chars_count'] : cws_vc_shortcode_get_tribe_events_chars_count($atts['layout']);
					$event_output .= apply_filters( 'cws_event_excerpt_tag_start', '<p class="cws-excerpt">', $atts, $pid ) .
					apply_filters( 'cws_event_excerpt', get_excerpt_events( $excerptLength ), $atts, $pid, $excerptLength ) .
					apply_filters( 'cws_event_excerpt_tag_end', '</p>', $atts, $pid );
				}
			}
			if ( (!in_array( 'time_events', $data_to_hide ) && isValid( $atts['eventdetails'] ) ) || (!in_array( 'venue_events', $data_to_hide ) && isValid( $atts['venue']) ) ){
				$event_output .= "<div class='events_duration'>";
			}
			if ( !in_array( 'time_events', $data_to_hide ) ){
				if ( isValid( $atts['eventdetails'] ) ) {
					$event_schedule_details = tribe_events_event_schedule_details( get_the_ID() );
					if(!empty($event_schedule_details)){
						$event_output .= apply_filters( 'cws_event_date_tag_start', '<div class="duration time">', $atts, $pid ) .
						apply_filters( 'cws_event_list_details', tribe_events_event_schedule_details(), $atts, $pid ) .
						apply_filters( 'cws_event_date_tag_end', '</div>', $atts, $pid );
					}
				}
			}

			if ( !in_array( 'venue_events', $data_to_hide ) ){
				if ( isValid( $atts['venue'] ) ) {
					$venue = tribe_get_venue( get_the_ID() );
					if(!empty($venue)){
						$event_output .= apply_filters( 'cws_event_venue_tag_start', '<div class="duration venue">', $atts, $pid ) .
						apply_filters( 'cws_event_venue_at_tag_start', '<em> ', $atts, $pid ) .
						apply_filters( 'cws_event_venue_at_tag_end', ' </em>', $atts, $pid ) .
						apply_filters( 'cws_event_list_venue', tribe_get_venue(), $atts, $pid ) .
						apply_filters( 'cws_event_venue_tag_end', '</div>', $atts, $pid );						
					}

				}		
			}
			if ( (!in_array( 'time_events', $data_to_hide ) && isValid( $atts['eventdetails'] ) ) || (!in_array( 'venue_events', $data_to_hide ) && isValid( $atts['venue']) ) ){
				$event_output .= "</div>";
			}
		
			echo apply_filters( 'cws_single_event_output', $event_output, $atts, $pid );
			echo "</div>";

		echo "</div>";
		
		
	echo "</article>";
}
function cws_vc_shortcode_tribe_events_posts_grid_post_media ($atts = null){
	$pid = get_the_id();
	$permalink = get_the_permalink( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$post_url = esc_url(get_the_permalink());
	$thumbnail_props = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id( ),'full') : array();
	$thumbnail = !empty( $thumbnail_props ) ? $thumbnail_props[0] : '';
	$real_thumbnail_dims = array();
	if ( !empty( $thumbnail_props ) && isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
	if ( !empty(  $thumbnail_props ) && isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];

	$thumbnail_dims = cws_vc_shortcode_get_tribe_events_thumbnail_dims( false, $real_thumbnail_dims );
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
		<div class="post_media  tribe_events_post_media posts_grid_post_media">
			<?php
				echo "<div class='pic'>";
					echo apply_filters( 'cws_event_thumbnail_link_start', '<a href="' . tribe_get_event_link() . '">', $atts, $pid );
					if ( $retina_thumb_exists ) {
						echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
					else{
						echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
					}
					echo apply_filters( 'cws_event_thumbnail_link_end', '</a>', $atts, $pid );
				echo "</div>";
			?>
		</div>
	<?php
	}
}

function cws_vc_shortcode_tribe_events_posts_grid_post_title (){
	$pid = get_the_id ();
	$def_grid_atts = array(
					'layout'						=> '1',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );

	$title = get_the_title($pid);
	echo !empty( $title ) ?	"<h3 class='post_title tribe_events_post_title posts_grid_post_title'>$title</h3>" : "";	
}

function cws_vc_shortcode_tribe_events_posts_grid_post_content (){
	if(class_exists('WPBMap')){
		WPBMap::addAllMappedShortcodes();
	}
	$pid = get_the_id ();
	$post = get_post( $pid );
	$def_grid_atts = array(
					'layout'						=> '1',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	
	$out = "";
	$chars_count = cws_vc_shortcode_get_tribe_events_chars_count( $layout );
	$out = !empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
	$out = trim( preg_replace( "/[\s]{2,}/", " ", strip_shortcodes( strip_tags( $out ) ) ) );
	$out = wptexturize( $out );
	$out = mb_substr( $out, 0, $chars_count );
	echo !empty( $out ) ? "<div class='post_content tribe_events_post_content posts_grid_post_content'>$out</div>" : "";		
}
function cws_vc_shortcode_tribe_events_posts_grid_post_terms (){
	$pid = get_the_id ();
	$def_grid_atts = array(
					'layout'						=> '1',
				);
	$grid_atts = isset( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] ) ? $GLOBALS['cws_vc_shortcode_posts_grid_atts'] : $def_grid_atts;	
	extract( $grid_atts );
	$p_category_terms = wp_get_post_terms( $pid, 'tribe_events_cat' );
	$p_cats = "";
	for ( $i=0; $i < count( $p_category_terms ); $i++ ){
		$p_category_term = $p_category_terms[$i];
		$p_cat_permalink = get_term_link( $p_category_term->term_id, 'tribe_events_cat' );
		$p_cat_name = $p_category_term->name;
		$p_cats .= "<a href='$p_cat_permalink'>$p_cat_name</a>";
		$p_cats .= $i < count( $p_category_terms ) - 1 ? esc_html__( "&#x2c;&#x20;", 'cws-essentials' ) : "";
	}
	echo !empty($p_cats) ? "<div class='post_terms tribe_events_post_terms posts_grid_post_terms'>{$p_cats}</div>" : "";		
				
}

function isValid( $prop ){
	return ( $prop !== 'false' );
}

function get_excerpt_events( $limit, $source = null ){
	$excerpt = get_the_excerpt();
	if( $source == "content" ) {
		$excerpt = get_the_content();
	}

	$excerpt = preg_replace( " (\[.*?\])", '', $excerpt );
	$excerpt = strip_tags( strip_shortcodes($excerpt) );
	$excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
	if ( strlen( $excerpt ) > $limit ) {
		$excerpt = mb_substr( $excerpt, 0, $limit );
		$excerpt .= '...';
	}

	return $excerpt;
}