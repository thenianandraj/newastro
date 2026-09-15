<?php
	/**
	 * Testimonials Widget Class
	 */
class CWS_Testimonials extends WP_Widget {
	function init_fields() {
		$this->fields = array(
			'title' => array(
				'title' => esc_html__( 'Widget Title', 'cws-essentials' ),
				'atts' => 'id="widget-title"',
				'type' => 'text',
				),
			'show_icon_opts' => array(
				'title' => esc_html__( 'Show icon options', 'cws-essentials' ),
				'type' => 'checkbox',
				'atts' => 'data-options="e:icon_type"',
			),
			'icon_type' => array(
				'title' => esc_html__( 'Icon type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'subtype' => 'images',
				'value' => array(
					'fa' => array( esc_html__( 'icon', 'cws-essentials' ), 	true, 	'e:icon_fa;e:icon_color;e:icon_bg_type;d:icon_img', '/img/align-left.png' ),
					'img' =>array( esc_html__( 'image', 'cws-essentials' ), false,	'd:icon_fa;d:icon_color;d:icon_bg_type;e:icon_img', '/img/align-right.png' ),
				),
			),
			'icon_fa' => array(
				'title' => esc_html__( 'Font Awesome character', 'cws-essentials' ),
				'type' => 'select',
				'addrowclasses' => 'disable fai',
				'source' => 'fa',
			),
			'icon_img' => array(
				'title' => esc_html__( 'Custom icon', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'type' => 'media',
			),
			'icon_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Icon color', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="#ffffff"',
			),
			'icon_bg_type' => array(
				'title' => esc_html__( 'Background type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'none' => array( esc_html__( 'None', 'cws-essentials' ), 	true, 	'd:icon_bgcolor;d:gradient_first_color;d:gradient_second_color;d:gradient_type' ),
					'color' => array( esc_html__( 'Color', 'cws-essentials' ), 	true, 	'e:icon_bgcolor;d:gradient_first_color;d:gradient_second_color;d:gradient_type' ),
					'gradient' =>array( esc_html__( 'Gradient', 'cws-essentials' ), false,'d:icon_bgcolor;e:gradient_first_color;e:gradient_second_color;e:gradient_type' ),
				),
			),
			'icon_bgcolor' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Icon background color', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="'.AASANA_COLOR.'"',
			),

			'gradient_first_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'From', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="'.AASANA_COLOR.'"',
			),
			'gradient_second_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'To', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="#0eecbd"',
			),
			'gradient_type' => array(
				'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'linear' => array( esc_html__( 'Linear', 'cws-essentials' ), 	true, 'e:gradient_linear_angle;d:gradient_radial_shape' ),
					'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,	'd:gradient_linear_angle;e:gradient_radial_shape' ),
				),
			),
			'gradient_linear_angle' => array(
				'type'      => 'number',
				'title'     => esc_html__( 'Angle', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'value' => '45',
			),
			'gradient_radial_shape' => array(
				'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'simple' => array( esc_html__( 'Simple', 'cws-essentials' ), 	true, 'e:gradient_radial_type;d:gradient_radial_size_key;d:gradient_radial_size' ),
					'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:gradient_radial_type;e:gradient_radial_size_key;e:gradient_radial_size' ),
				),
			),
			'gradient_radial_type' => array(
				'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ), 	true ),
					'circle' =>array( esc_html__( 'Cirle', 'cws-essentials' ), false ),
				),
			),
			'gradient_radial_size_key' => array(
				'type' => 'select',
				'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'source' => array(
					'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
					'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
					'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
					'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
				),
			),
			'gradient_radial_size' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Size', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'placeholder="'.esc_html__('Two space separated percent values, for example (60% 55%)', 'cws-essentials').'"',
			),

			'sel_posts_by' => array(
				'title' => esc_html__( 'Filter by', 'cws-essentials' ),
				'type' => 'select',
				'source' => array(
					'none' => array('None', true, 'd:categories;d:post_title;d:tags;'),
					'titles' => array('Titles', false, 'd:categories;e:post_title;d:tags;'),
					'cats' => array('Categories', false, 'e:categories;d:post_title;d:tags;'),
					'tags' => array('Tags', false, 'd:categories;d:post_title;e:tags;'),
				),
			),
			'categories' => array(
				'title' => esc_html__( 'Categories', 'cws-essentials' ),
				'type' => 'taxonomy',
				'addrowclasses' => 'disable',
				'taxonomy' => 'cws_testimonials_department',
				'atts' => 'multiple',
				'source' => array(),
			),
			'tags' => array(
				'title' => esc_html__( 'Tags', 'cws-essentials' ),
				'type' => 'taxonomy',
				'addrowclasses' => 'disable',
				'taxonomy' => 'cws_testimonials_position',
				'atts' => 'multiple',
				'source' => array(),
			),
			'post_title' => array(
				'title' => esc_html__( 'Posts', 'cws-essentials' ),
				'type' => 'select',
				'addrowclasses' => 'disable',
				'taxonomy' => 'cws_testimonials',
				'atts' => 'multiple',
				'source' => 'titles cws_testimonials',
			),
			'count' => array(
				'type' => 'number',
				'title' => esc_html__( 'Post count', 'cws-essentials' ),
				'value' => '3',
				),
			'visible_count' => array(
				'type' => 'number',
				'title' => esc_html__( 'Posts per slide', 'cws-essentials' ),
				'value' => '3',
				),
			'chars_count' => array(
				'type' => 'number',
				'title' => esc_html__( 'Count of chars from post content', 'cws-essentials' ),
				'value' => '50',
				),
			'show_part' => array(
				'title' => esc_html__( 'Show', 'cws-essentials' ),
				'type' => 'select',
				'source' => array(
					'both' => array( 'Categories & Tags' ), // Title, isselected, data-options
					'categories' => array( 'Categories' ),
					'tags' => array( 'Tags' ),
					'none' => array( 'None', true ),
				),
			),
			'show_content' => array(
				'title' => esc_html__( 'Content', 'cws-essentials' ),
				'type' => 'radio',
				'value' => array(
					'content' => array( esc_html__( 'Content', 'cws-essentials' ), true),
					'exerpt' => array( esc_html__( 'Exerpt', 'cws-essentials' )),
				),
			),
			'controls' => array(
				'title' => esc_html__( 'Controls style', 'cws-essentials' ),
				'type' => 'radio',
				'value' => array(
					'none' => array( esc_html__( 'Standart', 'cws-essentials' ), true),
					'controls_round' => array( esc_html__( 'Round', 'cws-essentials' )),
					'controls_square' => array( esc_html__( 'Square', 'cws-essentials' )),
				),
			),
			'show_date' => array(
				'type' => 'checkbox',
				'title' => esc_html__( 'Show date', 'cws-essentials' ),
			),
		);
	}
	function __construct() {
		$widget_ops = array( 'classname' => 'widget_cws_testimonials', 'description' => esc_html__( 'CWS testimonials posts', 'cws-essentials' ) );
		parent::__construct( 'cws-testimonials', esc_html__( 'CWS Testimonials', 'cws-essentials' ), $widget_ops );
	}

	function filter_by_empty ( $arr = array() ) {

		if ( empty( $arr ) || !is_array( $arr ) ) return false;

		for ( $i=0; $i<count( $arr ); $i++ ) {
			if ( empty( $arr[$i]) ) {	
				array_splice( $arr, $i, 1 );
			}
		}	
		if ('!!!dummy!!!' === $arr[0]) {
			array_shift($arr);
		}

		return $arr;
	}

	function widget( $args, $instance ) {
		extract( $args );

		extract( shortcode_atts( array(
			'title' => '',
			'show_icon_opts' => '0',
			'sel_posts_by' => 'none',
			'categories' => array(),
			'tags' => array(),
			'post_title' => array(),
			'count' => get_option( 'posts_per_page' ),
			'visible_count' => get_option( 'posts_per_page' ),
			'chars_count' => '50',
			'show_part' => '',
			'show_content' => 'content',
			'controls' => 'round',
			'show_date' => '0'
		), $instance));
		global $aasana_theme_funcs;
		
		$use_blur = $aasana_theme_funcs->cws_get_option( 'use_blur' );

		$title = esc_html($title);

		// for ( $i=0; $i<count($cats); $i++ ){
		// 	$term_obj = get_term_by( 'slug', $cats[$i], 'category' );
		// 	$cats[$i] = $term_obj->term_id;
		// }

		$footer_is_rendered = isset( $GLOBALS['footer_is_rendered'] );

		/* defaults for empty text fields with number values */
		$count = empty( $count ) ? (int)get_option( 'posts_per_page' ) : (int)$count;
		$visible_count = empty( $visible_count ) ? $count : (int)$visible_count;
		$chars_count = empty( $chars_count ) ? 50 : (int)$chars_count;
		/* \defaults for empty text fields with number values */


		$q_args = array(
			'post_type' => 'cws_testimonial',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'post_status' => 'publish',
			'orderby'    => 'menu_order date title',
			'order'      => 'ASC',
		);
		$post_title = $this->filter_by_empty( $post_title );
		$categories = $this->filter_by_empty( $categories );
		$tags = $this->filter_by_empty( $tags );

		$tax_query = array();
		if ( ($sel_posts_by == 'cats') && !empty( $categories )){
			$tax_query[] = array(
				'taxonomy' => 'cws_testimonial_department',
				'field' => 'slug',
				'terms' => $categories
			);
		} else if ( ($sel_posts_by == 'tags') && !empty( $tags )){
			$tax_query[] = array(
				'taxonomy' => 'cws_testimonial_position',
				'field' => 'slug',
				'terms' => $tags
			);
		} else if ( $sel_posts_by == 'titles' && !empty( $post_title ) ) {
			$q_args['post__in'] = $post_title;
		}
		if ( !empty( $tax_query ) ) $q_args['tax_query'] = $tax_query;

		$q = new WP_Query( $q_args );

		$show_icon_opts = ($show_icon_opts === 'on') ? '1' : $show_icon_opts;
		$widget_title_icon = $show_icon_opts === '1' ? $aasana_theme_funcs->cws_widget_title_icon_rendering( $instance ) : '';
		$carousel_mode = $count > $visible_count;
		$counter = 0;

		echo sprintf("%s", $before_widget);

			if ( !empty( $widget_title_icon ) ){
				echo sprintf("%s", $before_title) . "<div class='widget_title_box'><div class='widget_title_icon_section'>$widget_title_icon</div><div class='widget_title_text_section'>$title</div></div>" . $after_title;
			}
			else if (!empty( $title )){
				echo sprintf("%s", $before_title) . esc_html($title) . $after_title;
			}

			if ( $q->have_posts() ){
				$blur_class = esc_attr($use_blur ? ' blurred' : '');
				if ( $carousel_mode ){
					wp_enqueue_script ('owl_carousel');
					echo "<div class='widget_carousel testimonials_widget".($controls != 'none' ? " ".$controls : '')."'>";
				}
				else if ( $footer_is_rendered ){
					echo "<div class='post_items'>";
				}
				while ( $q->have_posts() ):
					$q->the_post();
					$pid = get_the_id();
					$post = get_post( $pid );
					$cur_post = get_queried_object();
					$date_format = get_option( 'date_format' );
					$date = esc_html( get_the_time( $date_format ) );
					$permalink = esc_url(get_permalink());

					$meta = get_post_meta( $pid, 'cws_mb_post' );
					$meta = isset( $meta[0] ) ? $meta[0] : array();

					$p_category_terms = wp_get_post_terms( $pid, 'cws_testimonials_department' );
					$p_tag_terms = wp_get_post_terms( $pid, 'cws_testimonials_position' );

					$taxonomy = '';

						if($show_part == 'categories' || $show_part == 'both'){
							$p_cats = "";
							for ( $i=0; $i<count( $p_category_terms ); $i++ ){
								$p_category_term = $p_category_terms[$i];
								$p_cat_permalink = get_term_link( $p_category_term->term_id, 'cws_testimonials_department' );
								$p_cat_name = $p_category_term->name;
								$p_cats .= "<a class='testimonials_category' href='$p_cat_permalink'>$p_cat_name</a>";
								$p_cats .= $i < count( $p_category_terms ) - 1 ? esc_html__( ", ", 'cws-essentials' ) : "";
							}
							$taxonomy = $p_cats;
						}
						if ($show_part == 'tags' || $show_part == 'both'){
							$p_tags = "";
							for ( $i=0; $i<count( $p_tag_terms ); $i++ ){
								$p_tag_term = $p_tag_terms[$i];
								$p_tag_permalink = get_term_link( $p_tag_term->term_id, 'cws_testimonials_position' );
								$p_tag_name = $p_tag_term->name;
								$p_tags .= "<a class='testimonials_tags' href='$p_tag_permalink'>$p_tag_name</a>";
								$p_tags .= $i < count( $p_tag_terms ) - 1 ? esc_html__( ", ", 'cws-essentials' ) : "";
							}
							$p_tags = ($show_part == 'both' ? esc_html__( "/ ", 'cws-essentials' ) : '').$p_tags;
							$taxonomy = $p_tags;
						}
						if ($show_part == 'both'){
							$taxonomy = $p_cats.' '.$p_tags;
						}

						if ( $carousel_mode && $counter <= 0 ){ /* open carousel item tag */
							echo "<div class='item $blur_class'>";
						}
							echo "<div class='post_item $blur_class'>";
								echo "<div class='post_preview clearfix'>";
									if ( has_post_thumbnail() ):
										$featured_img_url = wp_get_attachment_url( get_post_thumbnail_id() );
										$thumb_obj = cws_thumb( $featured_img_url, array( 'width' => 70, 'height' => 70 , 'crop' => true ), false );
										$thumb_url = esc_url($thumb_obj[0]);
										echo "<div class='post_thumb'>";
											echo "<a href='$permalink'>";
												$thumb_path_hdpi = !empty($thumb_obj[3]) ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_attr( $thumb_obj[3] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
												echo "<img $thumb_path_hdpi alt = '" . get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) . "' />";
											echo "</a>";
										echo "</div>";
									endif;
									$post_title = esc_html( get_the_title() );
									echo !empty( $post_title ) ? "<div class='post_title'><a href='$permalink'>$post_title</a></div>" : "";
									$content = !empty( $cur_post->post_excerpt ) ? $cur_post->post_excerpt : get_the_content( '' );
									$content = trim( preg_replace( "/[\s]{2,}/", " ", strip_shortcodes( strip_tags( $content ) ) ) );

									$is_content_empty = empty( $content );
									if ( !$is_content_empty){
										if($show_content == 'exerpt'){
											$content = !empty( $post->post_excerpt ) ? $post->post_excerpt : $content;
										}

										if ( strlen( $content ) > $chars_count ){
												$content = mb_substr( $content, 0, $chars_count );
												$content = wptexturize( $content ); /* apply wp filter */
												echo "<div class='post_content'>$content <a href='$permalink'>" . esc_html__( "...", 'cws-essentials' ) . "</a></div>";
												echo "<div class='quote_author'><a href='$permalink'>$taxonomy</a></div>";
										}
										else{
											$content = wptexturize( $content ); /* apply wp filter */
											echo "<div class='post_content'>$content</div>";
											echo "<div class='quote_author'><a href='$permalink'>$taxonomy</a></div>";
										}
									}
									if ( $show_date ){
										echo "<div class='post_date'>$date</div>";
									}

								echo "</div>";
							echo "</div>";
						if ( $carousel_mode ){
							if ( $counter >= $visible_count-1 || $q->current_post >= $q->post_count-1 ){
								echo "</div>";
								$counter = 0;
							}
							else{
								$counter ++;
							}
						}

				endwhile;
				wp_reset_postdata();
				if($carousel_mode || $footer_is_rendered){
					echo "</div>";
				}
			}
			else{
				echo do_shortcode( "[cws_sc_msg_box text='" . esc_html__( 'There are no posts matching the query', 'cws-essentials' ) . "'][/cws_sc_msg_box]" );
			}
		echo sprintf("%s", $after_widget);
	}

	function update( $new_instance, $old_instance ) {
		$instance = (array)$new_instance;
		foreach ($new_instance as $key => $v) {
			switch ($this->fields[$key]['type']) {
				case 'text':
					$instance[$key] = strip_tags($v);
					break;
			}
		}
		return $instance;
	}

	function form( $instance ) {
		$this->init_fields();
		if (function_exists('cws_core_build_layout') ) {
			echo cws_core_build_layout($instance, $this->fields, 'widget-' . $this->id_base . '[' . $this->number . '][');
		}
	}
}
?>