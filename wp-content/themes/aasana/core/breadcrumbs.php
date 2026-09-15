<?php
function aasana_dimox_breadcrumbs($animate_title_options = '') {
	/* === OPTIONS === */
	global $aasana_theme_funcs;

	$post_id = get_the_id();
	$post_meta = get_post_meta( $post_id, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();

	$apply_color = isset($post_meta['apply_color']) && !empty($post_meta['apply_color']) ? $post_meta['apply_color'] : "";
		
	$post_font_color = isset($post_meta['post_font_color']) && !empty($post_meta['post_font_color']) ? $post_meta['post_font_color'] : "";
	$post_font_sec_color = isset($post_meta['post_font_sec_color']) && !empty($post_meta['post_font_sec_color']) ? $post_meta['post_font_sec_color'] : "";
	if($apply_color == 'list_color' ){
		$post_font_color = '';
		$post_font_sec_color = '';
	}
	$post_font_color = !empty($post_font_color) ? $post_font_color : "";
	$post_font_sec_color = !empty($post_font_sec_color) ? $post_font_sec_color : "";

	$text['home']	 = esc_html__( 'Home', 'aasana' ); // text for the 'Home' link
	$text['category'] = esc_html__( 'Archive by Category "%s"', 'aasana' ); // text for a category page
	$text['search']   = esc_html__( 'Search for "%s"', 'aasana' ); // text for a search results page
	$text['taxonomy'] = esc_html__( 'Archive by %s "%s"', 'aasana' );
	$text['tag']	  = esc_html__( 'Posts Tagged "%s"', 'aasana' ); // text for a tag page
	$text['author'] = esc_html__( 'Articles Posted by %s', 'aasana' ); // text for an author page
	$text['404']	  = esc_html__( 'Error 404', 'aasana' ); // text for the 404 page

	$show_current   = 1; // 1 - show current post/page/category title in breadcrumbs, 0 - don't show
	$show_on_home   = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
	$show_on_404   	= 0; // 1 - show breadcrumbs on the 404, 0 - don't show
	$show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
	$show_title	 = 1; // 1 - show the title for the links, 0 - don't show
	// $delimiter	  = ' <i class="delimiter fa fa-angle-' . ( is_rtl() ? 'left' : 'right' ) . '"></i> '; // delimiter between crumbs
	$delimiter	  = "<span class='delimiter'".(!empty($post_font_color) ? " style='color:".$post_font_color."'"  : '').">".( is_rtl() ? ' \\ ' : ' / ' )."</span>";
	$before		 = '<span class="current"'.(!empty($post_font_sec_color) ? ' style="color:'.$post_font_sec_color.'"' : "").'>'; // tag before the current crumb
	$after		  = '</span>'; // tag after the current crumb
	/* === END OF OPTIONS === */

	global $post;
	$home_link	= esc_url( home_url( '/' ) );
	$link_before  = '<span typeof="v:Breadcrumb"'.(!empty($post_font_sec_color) ? ' style="color:'.$post_font_sec_color.'"' : "").'>';
	$link_after   = '</span>';
	$link_attr	= ' rel="v:url" property="v:title"';
	$link		 = $link_before . '<a' . $link_attr . ' href="%1$s"'.(!empty($post_font_color) ? ' style="color:'.$post_font_color.'"' : "").'>%2$s</a>' . $link_after;
	if ( isset( $post->post_parent ) ) {
		$parent_id	= $parent_id_2 = $post->post_parent;
	}

	$frontpage_id = get_option( 'page_on_front' );

	if ( ! $show_on_404 && is_404() ) {
		return;
	}

	if ( is_home() || is_front_page() || (empty( $post )) ) {

		if ( $show_on_home == 1 ) {
			echo '<nav class="bread-crumbs"><a href="' . $home_link . '"'.(!empty($post_font_color) ? ' style="color:'.$post_font_color.'"' : "").'>' . $text['home'] . '</a></nav>'; }
	} else {

		echo '<nav class="bread-crumbs"'.(!empty( $animate_title_options ) ? " ".$animate_title_options : "" ).'>';
		if ( $show_home_link == 1 ) {
			echo '<a href="' . $home_link . '"  rel="v:url" property="v:title"'.(!empty($post_font_color) ? ' style="color:'.$post_font_color.'"' : "").'>' . $text['home'] . '</a>';
			if ( $frontpage_id == 0 || $parent_id != $frontpage_id ) { echo sprintf("%s", $delimiter ); }
		}

		if ( is_category() ) {
			$cat = get_category( get_query_var( 'cat' ) );
			$cat_name = isset( $cat->name ) ? $cat->name : '';
			$parent_cats = array();
			$has_parent_cat = false;
			$temp_cat = $cat;
			while ( true ) {
				if ( isset( $temp_cat->parent ) && $temp_cat->parent ) {
					array_push( $parent_cats, $temp_cat->parent );
					$temp_cat = get_category( $temp_cat->parent );
				} else {
					break;
				}
			}
			$parent_cats = array_reverse( $parent_cats );
			for ( $i = 0; $i < count( $parent_cats ); $i++ ) {
				$cur_cat_obj = get_category( $parent_cats[ $i ] );
				$cur_cat_name = isset( $cur_cat_obj->name ) ? $cur_cat_obj->name : '';
				if ( ! empty( $cur_cat_name ) && isset( $cur_cat_obj->term_id ) ) {
					$cur_cat_link = get_category_link( $cur_cat_obj->term_id );
					if($has_parent_cat){
						echo sprintf("%s", $delimiter);
					}
					printf( $link, $cur_cat_link, $cur_cat_name );
					$has_parent_cat = true;
				}
			}
			if ( $show_current == 1 ) {
				if($has_parent_cat){
					echo sprintf("%s", $delimiter);
				}
				echo sprintf("%s", $before) . sprintf( $text['category'], $cat_name );
			}
		} elseif ( is_tag() ) {
			echo sprintf("%s", $before) . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after;

		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			echo sprintf("%s", $before) . esc_html( sprintf( $text['author'], $userdata->display_name ) ) . $after;

		} elseif ( is_day() ) {
			echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
			echo sprintf( $link, get_month_link( get_the_time( 'Y' ),get_the_time( 'm' ) ), get_the_time( 'F' ) ) . $delimiter;
			echo sprintf("%s", $before) . get_the_time( 'd' ) . $after;

		} elseif ( is_month() ) {
			echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . $delimiter;
			echo sprintf("%s", $before) . get_the_time( 'F' ) . $after;

		} elseif ( is_year() ) {
			echo sprintf("%s", $before) . get_the_time( 'Y' ) . $after;

		} elseif ( has_post_format() && ! is_singular() ) {
			echo get_post_format_string( get_post_format() );
		} else if ( is_tax( array( 'cws_portfolio_cat', 'cws_staff_member_department', 'cws_staff_member_position' ) ) ) {
			$tax_slug = get_query_var( 'taxonomy' );
			$term_slug = get_query_var( $tax_slug );
			$tax_obj = get_taxonomy( $tax_slug );
			$term_obj = get_term_by( 'slug', $term_slug, $tax_slug );
			$parent_terms = array();
			$has_parent_term = false;
			if ( isset( $tax_obj->hierarchical ) && $tax_obj->hierarchical ) {
				$temp_term_obj = $term_obj;
				while ( true ) {
					if ( isset( $temp_term_obj->parent ) && $temp_term_obj->parent ) {
						array_push( $parent_terms, $temp_term_obj->parent );
						$temp_term_obj = get_term_by( 'id', $temp_term_obj->parent, $tax_slug );
					} else {
						break;
					}
				}
				$parent_terms = array_reverse( $parent_terms );
				for ( $i = 0; $i < count( $parent_terms ); $i++ ) {
					$cur_term = get_term_by( 'id', $parent_terms[ $i ], $tax_slug );
					$cur_term_name = isset( $cur_term->name ) ? $cur_term->name : '';
					if ( ! empty( $cur_term_name ) && isset( $cur_term->term_id ) ) {
						$cur_term_link = get_term_link( $cur_term->term_id, $tax_slug );
						if($has_parent_term){
							echo sprintf("%s", $delimiter);
						}
						printf( $link, $cur_term_link, $cur_term_name );
						$has_parent_term = true;
					}
				}
			}
			if ( $show_current == 1 ) {
				$singular_tax_label = isset( $tax_obj->labels ) && isset( $tax_obj->labels->singular_name ) ? $tax_obj->labels->singular_name : '';
				$term_name = isset( $term_obj->name ) ? $term_obj->name : '';
				if($has_parent_term){
					echo sprintf("%s", $delimiter);
				}
				echo sprintf("%s", $before) . esc_html( sprintf( $text['taxonomy'], $singular_tax_label, $term_name ) );
			}
		} elseif ( function_exists('tribe_is_event_query') && tribe_is_event_query()) {
			$post_type_name = get_post_type_object('tribe_events')->labels->name ;
			printf( $link, tribe_get_events_link(), $post_type_name );
		} elseif ( is_archive() ) {
			if ( $show_current ) {
				$post_type = get_post_type();
				$post_type_obj = get_post_type_object( $post_type );
				$post_type_name = isset( $post_type_obj->label ) ? $post_type_obj->label : '';
				echo sprintf("%s", $before) . esc_html($post_type_name) . $after;
			}
		} elseif ( is_search() ) {
			echo sprintf("%s", $before) . sprintf( $text['search'], get_search_query() ) . $after;
		} elseif ( is_single() ) {
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );
			$post_type_label = isset( $post_type_obj->label ) ? $post_type_obj->label : '';
			$post_type_link = get_post_type_archive_link( $post_type );
			if ( $post_type_obj->has_archive ) {
				printf( $link, $post_type_link, $post_type_label  );
				echo sprintf("%s", $delimiter);
			}

			if ( $show_current ) { echo sprintf("%s", $before) . esc_html(get_the_title()) . $after; }
		} elseif ( is_page() && ! $parent_id ) {
			echo sprintf("%s", $before) . esc_html(get_the_title()) . $after;
		} elseif ( is_page() && $parent_id ) {
			if ( $parent_id != $frontpage_id ) {
				$breadcrumbs = array();
				while ( $parent_id ) {
					$page = get_page( $parent_id );
					if ( $parent_id != $frontpage_id ) {
						$breadcrumbs[] = sprintf( $link, get_permalink( $page->ID ), esc_html(get_the_title($page->ID)) );
					}
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse( $breadcrumbs );
				for ( $i = 0; $i < count( $breadcrumbs ); $i++ ) {
					echo sprintf("%s", $breadcrumbs[ $i ]);
					if ( $i != count( $breadcrumbs ) -1 ) { echo sprintf("%s", $delimiter); }
				}
			}
			if ( $show_current == 1 ) {
				if ( $show_home_link == 1 || ($parent_id_2 != 0 && $parent_id_2 != $frontpage_id) ) { echo sprintf("%s", $delimiter); }
				echo sprintf("%s", $before) . esc_html(get_the_title()) . $after;
			}
		} elseif ( is_404() ) {
			echo sprintf("%s", $before) . esc_html($text['404']) . $after;
		}

		if ( get_query_var( 'paged' ) ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) { echo ' ('; }
			echo sprintf("%s", $delimiter) . esc_html__( 'Page', 'aasana' ) . ' ' . get_query_var( 'paged' );
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) { echo ')'; }
		}
		echo '</nav><!-- .breadcrumbs -->';
	}
}
?>