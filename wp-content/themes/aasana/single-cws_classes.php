<?php
	get_header();

	$p_id = get_queried_object_id();

	global $aasana_theme_funcs;
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
		$hide_meta_single = $aasana_theme_funcs->cws_get_meta_option( 'def_cws_classes_data_to_hide_related' );
		$hide_meta_single = implode(",", $hide_meta_single);
	}
	$page_classes = "";
	
	$page_classes .= !empty( $sb['layout_class'] ) ? " " . $sb['layout_class'] . "_sidebar" : "";
	$page_classes = trim( $page_classes );
	
	$post_meta = get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	extract( shortcode_atts( array(
			'show_format' => '1',
			'wide_featured' => '0',
			'carousel' => '0',
			'show_related' => '0',
			'related_projects_options' => array(),
			'enable_hover' => '0',
			'link_options_fancybox' => '0',
			'show_featured' => '0',
		), $post_meta) );
?>

<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php

	if ($show_featured == '1' && $wide_featured == '1'){
		$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id( $p_id ), 'full' );
		$thumb_path_hdpi = $featured_img[3]['retina_thumb_exists'] ? " src='". $featured_img[0] ."' data-at2x='" . $featured_img[3]['retina_thumb_url'] ."'" : " src='". $featured_img[0] . "' data-no-retina";
		echo "<div class='wide_featured_img'><img $thumb_path_hdpi alt = '" . get_post_meta( get_post_thumbnail_id( $p_id ), '_wp_attachment_image_alt', true ) . "' /></div>";
	}

	echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';

	$related_projects_title = isset( $post_meta['rpo_title'] ) ? $post_meta['rpo_title'] : '';
	$related_projects_cols = isset( $post_meta['rpo_cols'] ) ? (int) $post_meta['rpo_cols'] : '4';
	$related_projects_count = isset( $post_meta['rpo_items_count'] ) ? (int) $post_meta['rpo_items_count'] : '4';
	$categories = isset( $post_meta['rpo_categories'] ) ? $post_meta['rpo_categories'] : '';
	$price = isset( $post_meta['price'] ) ? $post_meta['price'] : '';
	$date_events = isset( $post_meta['date_events'] ) ? $post_meta['date_events'] : '';
	$time_events = isset( $post_meta['time_events'] ) ? $post_meta['time_events'] : '';
	
	$destinations = isset( $post_meta['destinations'] ) ? $post_meta['destinations'] : '';
	if(!empty($price)){
		preg_match('/(.*[^0-9])(\d+)([\.,]\d+)/', $price, $matches);
		if(!empty($matches)){
			list(, $currency, $price, $pfraction) = $matches;
		}	
	}

		


	if(is_array($categories)){
		if(empty($categories[0])){
			unset($categories[0]);
		}
	}  

	$link_options = isset( $link_options['@'] ) ? $link_options['@'] : array();

	$cats = get_the_terms( $p_id, 'cws_classes_cat' );
	$cats = $cats ? $cats : array(); 
	$cat_slugs = array();
	foreach( $cats as $cat ){
		$cat_slugs[] = $cat->slug;
	}
	$has_cats = !empty( $cat_slugs );

	$related_posts = array();
	$has_related = false;

	if ( $has_cats ){
		$query_args = array(
			'post_type' => 'cws_classes',
		);
		$query_args['tax_query'] = array( array(
			'taxonomy' => 'cws_classes_cat',
			'field' => 'slug',
			'terms' => $cat_slugs
		));
		$query_args["post__not_in"] = array( $p_id );
		if ( $related_projects_count ){
			$query_args['posts_per_page'] = $related_projects_count;
		}
		$q = new WP_Query( $query_args );
		$related_posts = $q->posts;
		if ( count( $related_posts ) > 0 ){
			$has_related = true;
		}
	}

	$use_related_carousel = $carousel == 1 && $has_related;
	$show_related_items  = $show_related == 1 && $has_related;

	$section_class = "cws_classes single";
	$section_class .= $use_related_carousel ? " related" : "";

	?>
	<main>
		<div class="grid_row">
			<section class="<?php echo esc_attr($section_class) ?>">
				<div class="cws_wrapper cws_classes_items">
					<?php

					$GLOBALS['cws_vc_shortcode_single_post_atts'] = array(
						'sb_layout'						=> $sb['layout_class'],
						);
					while ( have_posts() ) : the_post();
					$pid = get_the_id();
					echo "<article id='cws_classes_post_{$pid}' class='cws_classes_post post_single item clearfix'>";
					ob_start();
					cws_vc_shortcode_cws_classes_single_post_post_media ();
					$media = ob_get_clean();
					$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] : false;
					unset( $GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] );
					if ( $floated_media ){
						echo "<div class='floated_media cws_classes_floated_media single_post_floated_media'>";
						echo "<div class='floated_media_wrapper cws_classes_floated_media_wrapper single_post_floated_media_wrapper'>";
						echo sprintf("%s", $media);
						echo "</div>";
						echo "</div>";						
					}
					else{
						echo sprintf("%s", $media);
					}
					ob_start();
					echo "<div class='wrap_title'>";
					echo "<div class='title_single_classes'>";
					cws_vc_shortcode_title();
					echo "</div>";
					if(!empty($price)){
						echo "<div class='price_single_classes'>";
							if(isset($currency)){
								echo "<span class='currency_price'>";
									echo esc_html($currency);
								echo "</span>";								
							}
							if(isset($price)){
								echo "<span class='price'>";
								echo esc_html($price);
								echo "</span>";								
							}

							if(isset($pfraction)){
								echo "<span class='pfraction'>";
								echo esc_html($pfraction);
								echo "</span>";								
							}

						echo "</div>";						
					}
					echo "</div>";
					if(!empty($date_events)){
						echo "<div class='date_ev_single_classes'>";
							echo esc_html($date_events);
						echo "</div>";								
					}
					if(!empty($time_events) || !empty($destinations)){
						echo "<div class='wrap_desc_info'>";
						if(!empty($time_events)){
							echo "<div class='time_ev_single_classes'>";
								echo esc_html($time_events);
							echo "</div>";								
						}
						if(!empty($destinations)){
							echo "<div class='destinations_single_classes'>";
								echo esc_html($destinations);
							echo "</div>";	
						}
						echo "</div>";
					}

					cws_vc_shortcode_cws_classes_single_post_content ();
					
					//cws_vc_shortcode_cws_classes_single_post_terms ();
					cws_vc_shortcode_cws_classes_teacher ();
					$content_terms = ob_get_clean();
					if ( !empty( $content_terms ) ){
						if ( $floated_media ){
							echo "<div class='clearfix'>";
							echo sprintf("%s", $content_terms);
							echo "</div>";
						}
						else{
							echo sprintf("%s", $content_terms);
						}
					}
					$aasana_theme_funcs->cws_page_links ();
					echo "</article>";
					endwhile;
					wp_reset_postdata();
					unset( $GLOBALS['cws_vc_shortcode_single_post_atts'] );
					?>
				</div>
				<?php
					echo "<div class='single_svg_divider'>";
					echo "<div class='separator-line single_classes_divider separator-container-left-line'></div>";
					echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39.99 40"><path d="M40,19.71a14.09,14.09,0,0,0-6-5.57A13.89,13.89,0,0,0,34.22,6l0-.16L34,5.77a14.26,14.26,0,0,0-8.24.28,14.06,14.06,0,0,0-5.62-6L20,0l-0.15.08a14.06,14.06,0,0,0-5.62,6A14.25,14.25,0,0,0,6,5.77l-0.16,0L5.78,6a13.88,13.88,0,0,0,.29,8.16,14.09,14.09,0,0,0-6,5.57,0.27,0.27,0,0,0,0,.29,0.27,0.27,0,0,0,0,.29,14.09,14.09,0,0,0,6,5.57A13.88,13.88,0,0,0,5.78,34l0,0.16,0.16,0a14.25,14.25,0,0,0,8.24-.29,14.06,14.06,0,0,0,5.62,6L20,40l0.15-.08a14.06,14.06,0,0,0,5.62-6,14.26,14.26,0,0,0,8.24.28l0.16,0,0-.16a13.89,13.89,0,0,0-.29-8.16,14.09,14.09,0,0,0,6-5.57,0.27,0.27,0,0,0,0-.29A0.27,0.27,0,0,0,40,19.71Zm-13.31,8a13.2,13.2,0,0,1-1.18,5.45,13.44,13.44,0,0,1-4.73-3l-0.35-.37a13.91,13.91,0,0,0,2.21-3.46,14.18,14.18,0,0,0,4,.9C26.64,27.42,26.65,27.59,26.65,27.76ZM14.54,33.21a13.21,13.21,0,0,1-1.18-5.45c0-.17,0-0.34,0-0.51a14.16,14.16,0,0,0,4-.9,13.92,13.92,0,0,0,2.21,3.46l-0.35.37A13.45,13.45,0,0,1,14.54,33.21Zm-1.18-21a13.21,13.21,0,0,1,1.18-5.45,13.45,13.45,0,0,1,4.73,3l0.35,0.37a13.92,13.92,0,0,0-2.21,3.46,14.16,14.16,0,0,0-4-.9C13.36,12.58,13.35,12.41,13.35,12.24ZM25.46,6.79a13.21,13.21,0,0,1,1.18,5.45c0,0.17,0,.34,0,0.51a14.18,14.18,0,0,0-4,.9,13.91,13.91,0,0,0-2.21-3.46l0.35-.37A13.45,13.45,0,0,1,25.46,6.79Zm4,13.21a13.5,13.5,0,0,1-3.43,2.19A14,14,0,0,0,24.87,20a14,14,0,0,0,1.2-2.19A13.5,13.5,0,0,1,29.49,20ZM20,29.39a13.35,13.35,0,0,1-2.06-3.24A14.22,14.22,0,0,0,20,25.05a14.2,14.2,0,0,0,2.06,1.09A13.34,13.34,0,0,1,20,29.39Zm0.16-4.92L20,24.36l-0.16.11a13.61,13.61,0,0,1-2.12,1.13A13.38,13.38,0,0,1,17,23.31l0-.19-0.19,0a13.64,13.64,0,0,1-2.31-.68,13.36,13.36,0,0,1,1.14-2.1,0.27,0.27,0,0,0,0-.3,0.27,0.27,0,0,0,0-.3,13.37,13.37,0,0,1-1.14-2.1,13.64,13.64,0,0,1,2.31-.68l0.19,0,0-.19a13.38,13.38,0,0,1,.69-2.29,13.6,13.6,0,0,1,2.12,1.13L20,15.64l0.16-.11a13.59,13.59,0,0,1,2.12-1.13A13.3,13.3,0,0,1,23,16.69l0,0.19,0.19,0a13.66,13.66,0,0,1,2.31.69,13.42,13.42,0,0,1-1.14,2.1,0.27,0.27,0,0,0,0,.3,0.27,0.27,0,0,0,0,.3,13.42,13.42,0,0,1,1.14,2.1,13.67,13.67,0,0,1-2.31.68l-0.19,0,0,0.19a13.32,13.32,0,0,1-.69,2.29A13.62,13.62,0,0,1,20.16,24.47ZM10.51,20a13.49,13.49,0,0,1,3.43-2.19A14,14,0,0,0,15.13,20a14,14,0,0,0-1.2,2.19A13.5,13.5,0,0,1,10.51,20Zm6.66-5.84a14,14,0,0,0-.68,2.22,14.23,14.23,0,0,0-2.24.67,13.26,13.26,0,0,1-.85-3.74A13.59,13.59,0,0,1,17.17,14.16Zm-2.92,8.78a14.22,14.22,0,0,0,2.24.67,14,14,0,0,0,.68,2.22,13.59,13.59,0,0,1-3.77.84A13.26,13.26,0,0,1,14.25,22.94ZM20,10.61a13.34,13.34,0,0,1,2.06,3.24A14.22,14.22,0,0,0,20,14.95a14.19,14.19,0,0,0-2.06-1.09A13.34,13.34,0,0,1,20,10.61Zm2.83,15.22a13.91,13.91,0,0,0,.68-2.22,14.28,14.28,0,0,0,2.24-.67,13.3,13.3,0,0,1,.85,3.74A13.61,13.61,0,0,1,22.83,25.83Zm2.92-8.78a14.28,14.28,0,0,0-2.24-.67,13.91,13.91,0,0,0-.68-2.22,13.61,13.61,0,0,1,3.77-.85A13.3,13.3,0,0,1,25.75,17.06Zm-12,.23a14.08,14.08,0,0,0-3.49,2.19l-0.37-.34a13.31,13.31,0,0,1-3.06-4.68,13.54,13.54,0,0,1,5.5-1.17l0.51,0A13.82,13.82,0,0,0,13.73,17.28Zm-3.49,3.24a14.07,14.07,0,0,0,3.49,2.19,13.81,13.81,0,0,0-.9,4l-0.51,0a13.54,13.54,0,0,1-5.5-1.17,13.31,13.31,0,0,1,3.06-4.68Zm16,2.19a14.07,14.07,0,0,0,3.49-2.19l0.37,0.35a13.31,13.31,0,0,1,3.05,4.68,13.54,13.54,0,0,1-5.5,1.17l-0.51,0A13.86,13.86,0,0,0,26.27,22.72Zm3.49-3.24a14.07,14.07,0,0,0-3.49-2.19,13.86,13.86,0,0,0,.91-4l0.51,0a13.54,13.54,0,0,1,5.5,1.17,13.31,13.31,0,0,1-3.06,4.68Zm3.64-5.58a14.12,14.12,0,0,0-5.72-1.2h0l6.13-6.07A13.31,13.31,0,0,1,33.41,13.89Zm0-7.67-6.13,6.07v0A13.78,13.78,0,0,0,26,6.58,13.67,13.67,0,0,1,33.36,6.22Zm-8.14,0a14,14,0,0,0-4.9,3.16l0,0V0.86A13.47,13.47,0,0,1,25.22,6.25ZM19.71,0.86V9.44l0,0a14,14,0,0,0-4.9-3.16A13.47,13.47,0,0,1,19.71.86ZM14,6.58a13.78,13.78,0,0,0-1.21,5.66v0L6.64,6.22A13.67,13.67,0,0,1,14,6.58Zm-7.75,0,6.13,6.07h0a14.13,14.13,0,0,0-5.72,1.2A13.3,13.3,0,0,1,6.23,6.63ZM0.55,20a13.52,13.52,0,0,1,5.71-5.31,13.88,13.88,0,0,0,3.19,4.85c0.17,0.16.33,0.32,0.5,0.46-0.16.15-.33,0.3-0.5,0.46a13.88,13.88,0,0,0-3.19,4.85A13.52,13.52,0,0,1,.55,20Zm6,6.11a14.13,14.13,0,0,0,5.72,1.2h0L6.23,33.37A13.3,13.3,0,0,1,6.59,26.11Zm0.05,7.68,6.13-6.07v0A13.78,13.78,0,0,0,14,33.42,13.67,13.67,0,0,1,6.64,33.78Zm8.14,0a14,14,0,0,0,4.9-3.16l0,0v8.58A13.47,13.47,0,0,1,14.78,33.75Zm5.51,5.39V30.56l0,0a14,14,0,0,0,4.9,3.16A13.47,13.47,0,0,1,20.29,39.14ZM26,33.42a13.78,13.78,0,0,0,1.21-5.66v0l6.13,6.07A13.67,13.67,0,0,1,26,33.42Zm7.75,0L27.64,27.3h0a14.12,14.12,0,0,0,5.72-1.2A13.31,13.31,0,0,1,33.77,33.37Zm0-8.06a13.88,13.88,0,0,0-3.19-4.85c-0.17-.17-0.34-0.32-0.5-0.46,0.16-.14.33-0.29,0.5-0.46a13.88,13.88,0,0,0,3.19-4.85A13.51,13.51,0,0,1,39.45,20,13.51,13.51,0,0,1,33.74,25.31Z" transform="translate(-0.01)"/></svg>';
					echo "<div class='separator-line single_classes_divider separator-container-right-line'></div>";
					echo "</div>";
					if ( $use_related_carousel ){
						$related_ids = array();
						foreach ( $related_posts as $related_post ) {
							$related_ids[] = $related_post->ID;
						}
						array_unshift( $related_ids, $p_id );
						$ajax_data = array(
							'current' => $p_id,
							'initial' => $p_id,
							'related_ids' => $related_ids
						);
						echo "<input type='hidden' id='cws_classes_single_ajax_data' value='" . esc_attr(json_encode( $ajax_data ) ) . "' />";
						?>
						<div class='carousel_nav_panel clearfix'>
							<div class='prev_section'>
								<div class='prev'>
									<div class="wrap">
										<span><?php esc_html_e( 'Prev' , 'aasana' ); ?></span>
									</div>
									<i class="fa fa-long-arrow-left"></i>
								</div>
							</div>
							<div class='next_section'>
								<div class='next'>
									<div class="wrap">
										<span><?php esc_html_e( 'Next' , 'aasana' ); ?></span>
									</div>
									<i class="fa fa-long-arrow-right"></i>
								</div>
							</div>
						</div>
						<?php
					}
				?>
			</section>
		</div>
		<div class="grid_row related_classes single_classes">
			<?php
			if ( $show_related ){
				$terms = wp_get_post_terms( $p_id, 'cws_classes_cat' );
				$term_slugs = array();
				for ( $i=0; $i < count( $terms ); $i++ ){
					$term = $terms[$i];
					$term_slug = $term->slug;
					array_push( $term_slugs, $term_slug );
				}
				$term_slugs = implode( ",", $term_slugs );

				if ( !empty( $term_slugs ) ){
					$rp_args = array(
						'title'							=> $related_projects_title,
						'total_items_count'				=> $related_projects_count,
						'display_style'					=> 'carousel',
						'navigation_carousel'			=> true,
						'cws_classes_hide_meta_override'=> true,
						'cws_classes_hide_meta'			=> isset($hide_meta_single) ? $hide_meta_single : "",
						'layout'						=> $related_projects_cols,
						'tax'							=> 'cws_classes_cat',
						'cws_classes_cat_terms'		=> !empty($categories) ? implode( ",", $categories ) : $term_slugs,
						'addl_query_args'				=> array(
							'post__not_in'					=> array( $p_id )
							),
						);
					$related_projects = cws_vc_shortcode_cws_classes_posts_grid( $rp_args );
					if ( !empty( $related_projects ) ){
						echo sprintf("%s", $related_projects);
					}
				}
			}
			?>
		</div>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : ''; ?>
</div>

<?php
	get_footer();
?>





