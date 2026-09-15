<?php
	$paged = !empty($_POST['paged']) ? (int)$_POST['paged'] : (!empty($_GET['paged']) ? (int)$_GET['paged'] : ( get_query_var("paged") ? get_query_var("paged") : 1 ) );
	$posts_per_page = (int)get_option('posts_per_page');
	$search_terms = get_query_var( 'search_terms' );

	get_header();
	global $aasana_theme_funcs;
	global $aasana_theme_standard;

	$p_id = get_queried_object_id();
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
?>
<div class="page_content search_results <?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : ''); ?>">
	<?php
	echo (isset($sb['content']) ? $sb['content'] : '<div class="container">');
	$blogtype = !empty($aasana_theme_funcs) ? $aasana_theme_funcs->cws_get_meta_option('blogtype') : "";
	$news_class = !empty( $blogtype ) ? ( preg_match( '#^\d+$#', $blogtype ) ? "news-pinterest" : "news-$blogtype" ) : "news-medium";
	$grid_class = $news_class == "news-pinterest" ? "grid-$blogtype isotope" : "";

	?>
	<main>
		<div class="grid_row clearfix">
			<?php
			global $wp_query;
			$total_post_count = $wp_query->found_posts;
			$max_paged = ceil( $total_post_count / $posts_per_page );
			if ( 0 === strlen($wp_query->query_vars['s'] ) ){
				$message_title = esc_html__( 'Empty search string', 'aasana' );
				$message = esc_html__( 'Please, enter some characters to search field', 'aasana' );
				if(!empty($aasana_theme_funcs)){
					echo sprintf("%s", $aasana_theme_funcs->cws_print_search_form($message_title, $message));
				}else{
					echo sprintf("%s", $aasana_theme_standard->cws_print_search_form($message_title, $message));
				}
				
			} else {

				if(have_posts()){
					?>
					<section class="news <?php echo esc_attr($news_class); ?>">
						<div class='cws_wrapper'>
							<div class="grid <?php echo esc_attr($grid_class); ?>">
							<?php
								wp_enqueue_script ('isotope');
								$use_pagination = $max_paged > 1;
									while( have_posts() ) : the_post();
										$content = get_the_content();
										$content = preg_replace( '/\[.*?(\"title\":\"(.*?)\").*?\]/', '$2', $content );
										$content = preg_replace( '/\[.*?(|title=\"(.*?)\".*?)\]/', '$2', $content );
										$content = strip_tags( $content );
										$content = preg_replace( '|\s+|', ' ', $content );
										$title = get_the_title();

										$cont = '';
										$bFound = false;
										$contlen = strlen( $content );
										foreach ($search_terms as $term) {
											$pos = 0;
											$term_len = strlen($term);
											do {
												if ( $contlen <= $pos ) {
													break;
												}
												$pos = stripos( $content, $term, $pos );
												if ( $pos ) {
													$start = ($pos > 50) ? $pos - 50 : 0;
													$temp = substr( $content, $start, $term_len + 100 );
													$cont .= ! empty( $temp ) ? $temp . ' ... ' : '';
													$pos += $term_len + 50;
												}
											} while ($pos);
										}

										if (strlen($cont) > 0) {
											$bFound = true;
										}
										else {
											$cont = mb_substr( $content, 0, $contlen < 100 ? $contlen : 100 );
											if ( $contlen > 100 ) {
												$cont .= '...';
											}
											$bFound = true;
										}
										$pattern = "#\[[^\]]+\]#";
										$replace = "";
										$cont = preg_replace($pattern, $replace, $cont);
										$cont = preg_replace('/('.implode('|', $search_terms) .')/iu', '<mark>\0</mark>', $cont);
										$permalink = esc_url( get_the_permalink() );
										$title = get_the_title();
										$title = preg_replace( '/('.implode( '|', $search_terms ) .')/iu', '<mark>\0</mark>', $title );
										echo "<article class='item small'>";
											echo !empty( $title ) ? (!empty($aasana_theme_funcs) ? $aasana_theme_funcs::THEME_BEFORE_CE_TITLE : $aasana_theme_standard::THEME_BEFORE_CE_TITLE) . "<span><a href='$permalink'>$title</a></span>" . (!empty($aasana_theme_funcs) ? $aasana_theme_funcs::THEME_AFTER_CE_TITLE : $aasana_theme_standard::THEME_AFTER_CE_TITLE) : "";


											echo "<div class='post_content'>" . apply_filters( 'the_content', $cont ) . "</div>";
											if ( has_tag() ){
												echo "<div class='post_tags'>";
												the_tags ( "", " ", "");
												echo "</div>";
											}
											if ( has_category() ){
												echo "<div class='post_category'>";
												the_category (' ');
												echo "</div>";
											}
											$button_word = esc_html__( 'Read More', 'aasana' );
											echo "<div class='right_alight'><a href='$permalink' class='cws_vc_shortcode_button add_hover'>".$button_word.'</a></div>';
											echo "<hr>";
										echo "</article>";
									endwhile;
									wp_reset_postdata();
								?>
							</div>
						</div>
					</section>
					<?php
					//global $wp_query;
					if ( $use_pagination ) {
						if(!empty($aasana_theme_funcs)){
							$aasana_theme_funcs->cws_pagination($paged, $max_paged);
						}else{
							$aasana_theme_standard->cws_pagination($paged, $max_paged);
						}
						
					}
				}
				else {
					$message_title = esc_html__( 'No search Results', 'aasana' );
					$message = esc_html__( 'There are no posts matching your query', 'aasana' );
					if(!empty($aasana_theme_funcs)){
						echo sprintf("%s",  $aasana_theme_funcs->cws_print_search_form($message_title, $message));
					}else{
						echo sprintf("%s", $aasana_theme_standard->cws_print_search_form($message_title, $message));
					}
				}
			}
			?>
		</div>
	</main>
	<?php echo (isset($sb['content']) ? $sb['content'] : '</div>'); ?>
</div>

<?php

get_footer ();
?>