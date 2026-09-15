<?php
	global $aasana_theme_funcs;
	global $aasana_theme_standard;
	if ($aasana_theme_funcs){

		if (is_page()){
			$blogtype = $aasana_theme_funcs->cws_get_meta_option( "blogtype" );
		} elseif (is_front_page() || is_category() || is_tag() || is_archive()) {
			$blogtype = $aasana_theme_funcs->cws_get_option( "def_blogtype" );
		}
	} else {
		$blogtype = 'large';
	}
	$taxonomy = "category";
	$terms  = array();

	if ( is_page() ) {
		if($aasana_theme_funcs){
			$cats = $aasana_theme_funcs->cws_get_meta_option( 'category' );
			$terms = !empty($cats) ? explode(',', $cats) : '';			
		}
	}	else if ( is_category() ) {
		$term_id = get_query_var( 'cat' );
		$term = get_term_by( 'id', $term_id, 'category' );
		$term_slug = $term->slug;
		$terms = array( $term_slug );
	} else if ( is_tag() ) {
		$taxonomy = 'post_tag';
		$term_slug = get_query_var( 'tag' );
		$terms = array( $term_slug );
	}

	$post_type_array = array("post");
	$posts_per_page = (int)get_option('posts_per_page');
	$ajax = isset( $_POST['ajax'] ) ? (bool)$_POST['ajax'] : false;
	$paged_var = get_query_var( 'paged' );
	$paged = $ajax && isset( $_POST['paged'] ) ? $_POST['paged'] : ( $paged_var ? $paged_var : 1 );
	$args = array(
		'post_type' => $post_type_array,
		'post_status' => 'publish',
		'hide' => array(),
		'content_divider' => (in_array($blogtype, array("2", "3", "4")) ? '0' : '1'),
		'post_text_length' => (!empty($post_text_length) ? $post_text_length : ''),
		'this_shortcode' => false,
		'custom_layout' => '1',
		'is_related' => '0',
		'button_name' => (in_array($blogtype, array("2", "3", "4")) ? '' : 'Read more'),
		'posts_per_page' => $posts_per_page,
		'paged' => $paged,
		'full_width' => '0',
		'aspect_ratio' => '0',
	);

	if ( !empty( $terms ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $terms
			)
		);
	}
	if ( is_date() ) {
		if($aasana_theme_funcs){
			$args = array_merge( $args, $aasana_theme_funcs->cws_get_date_parts() );
		}else{
			$args = array_merge( $args, $aasana_theme_standard->cws_get_date_parts() );
		}
		
	}
	$query = new WP_Query( $args );
	$max_paged = ceil( $query->found_posts / $posts_per_page );

	$blogtype = sanitize_html_class( $blogtype );

	$news_class = !empty( $blogtype ) ? ( preg_match( '#^\d+$#', $blogtype ) ? "news-pinterest" : "news-$blogtype" ) : "news-medium";
	$grid_class = $news_class == "news-pinterest" ? "grid-$blogtype isotope" : "";

	if ($news_class == "news-pinterest") {
		wp_enqueue_script ('isotope');
	}

	if ( !$ajax ): // not ajax request

		?>
		<div class="grid_row">
			<section class="news <?php echo esc_attr($news_class) ?>">
				<div class="cws_wrapper">
					<div class="grid <?php echo esc_attr($grid_class) ?>">
					<?php

						endif;							
						if ($aasana_theme_funcs){
							$aasana_theme_funcs->cws_blog_output( $query ); // output posts
						} else {


							global $wp_query;
							$query = $query ? $query : $wp_query;

							if ($query->have_posts()):
								ob_start();
								while($query->have_posts()):
									$query->the_post();

										?>
										<article <?php post_class(array( 'item', 'col-'.$blogtype, (is_sticky(get_the_id()) ? ' sticky-post ': ''))); ?>>

											<?php
												$aasana_theme_standard->cws_single_post_output();
											?>
										</article>
										<?php
									
								endwhile;
								wp_reset_postdata();
								ob_end_flush();
							endif;
						}
						if ( !$ajax ): // not ajax request

					?>
					</div>
					<?php
						if ( $news_class == "news-pinterest" && $paged < $max_paged ) {
							$template = 'content-blog';
							?>
							<div class="div_load_more"><a class="cws_button large cws_load_more alt" href="#" data-paged="<?php echo esc_attr($paged + 1) ?>" data-max-paged="<?php echo esc_attr($max_paged) ?>" data-template="<?php echo esc_attr($template); ?>"><?php echo esc_html__( "Load More",  'aasana' ); ?></a></div>

							<?php
							//aasana_load_more( $paged + 1, $template, $max_paged );
						}
						else if ( in_array( $news_class, array( "news-small", "news-medium", "news-large" ) ) && $max_paged > 1 ) {
							if ($aasana_theme_funcs){
								$aasana_theme_funcs->cws_pagination( $paged, $max_paged, 'paged', 'Load More');
							} else {
								$pagenum_link = html_entity_decode( get_pagenum_link() );
								$query_args   = array();
								$url_parts	= explode( '?', $pagenum_link );
								$style = 'paged';
								$pagination_text = 'Load More';

								if ( isset( $url_parts[1] ) ) {
									wp_parse_str( $url_parts[1], $query_args );
								}

								$permalink_structure = get_option('permalink_structure');

								$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
								$pagenum_link = $permalink_structure ? trailingslashit( $pagenum_link ) . '%_%' : trailingslashit( $pagenum_link ) . '?%_%';
								$pagenum_link = add_query_arg( $query_args, $pagenum_link );

								$format  = $permalink_structure && preg_match( '#^/*index.php#', $permalink_structure ) && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
								$format .= $permalink_structure ? user_trailingslashit( 'page/%#%', 'paged' ) : 'paged=%#%';
								?>
								<div class='pagination <?php if($style == 'load_more'){ echo ("pagination_load_more"); }?> separated'>
									<div class='page_links'>
									<?php
									$pagination_args = array( 'base' => $pagenum_link,
										'format' => $format,
										'current' => $paged,
										'total' => $max_paged,
										"prev_text" => "<i class='fa fa-angle-left'></i>",
										"next_text" => ($style == 'paged' ? "<i class='fa fa-angle-right'></i>" : $pagination_text),
										"link_before" => '',
										"link_after" => '',
										"before" => '',
										"after" => '',
										"mid_size" => 2,
									);
									echo paginate_links($pagination_args);
									?>
									</div>
								</div>
								<?php														
							}
						}
					?>
				</div>
			</section>
		</div>
		<?php

	endif;
?>