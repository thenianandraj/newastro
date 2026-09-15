<?php
	get_header ();

	$p_id = get_queried_object_id();

	global $aasana_theme_funcs;
	if(!empty($aasana_theme_funcs)){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);

	}else{
		global $aasana_theme_standard;
	}

?>
<div class="<?php echo (isset($sb) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';
	$section_class = "news single";
	?>
	<main>
		<div class="grid_row clearfix">
			<section class="<?php echo esc_attr($section_class) ?>">
				<div class="cws_wrapper">
					<div class="grid">
						<article class="item clearfix">
						<?php
							while ( have_posts() ):
								the_post();
								$c_post = get_post( get_the_id() );
								$title = get_the_title();
								$permalink = get_permalink();
								echo "<div class='ce_title'>" . "<span>$title</span>" . "</div>";
								?>
								<div class="post_info_part">
									<div class="post_info_box">
										<div class="post_info_header">

											<div class="post_info">
												<?php
													$author = esc_html(get_the_author());
													if($aasana_theme_funcs){
														$special_pf = $aasana_theme_funcs->cws_is_special_post_format();
													}else{
														$special_pf = $aasana_theme_standard->cws_is_special_post_format();
													}
													if ( !empty($author) || $special_pf ){
														echo "<div class='info'>";
															echo !empty($author) ? " by $author" : "";
															if($special_pf){
																echo !empty($author) ? AASANA_V_SEP : "";
															}else{
																echo sprintf("%s", $aasana_theme_funcs->cws_post_format_mark());
															}
														echo "</div>";
													}
													$comments_n = get_comments_number();
													if ( (int)$comments_n > 0 ){
														$permalink .= "#comments";
														echo "<div class='comments_link'><a href='$permalink'><i class='fa fa-comment'></i> $comments_n</a></div>";
													}
												?>
											</div>
										</div>
										<?php

											$thumbnail = wp_get_attachment_image_src( get_the_id(), 'full' );
											$thumbnail = !empty( $thumbnail ) ? $thumbnail[0] : "";
											echo "<div class='media_part'>";
											?>						
												<div class="date new_style">
													<?php
														$meta_date_arr = array();
														/* Date */

														array_push( $meta_date_arr, "<div class='date-content'>" );
														$date = get_the_time( get_option("date_format") );
														
														if ( !empty( $date ) ){
															$date = explode(" ", $date);
															foreach ($date as $key => $value) {
																array_push( $meta_date_arr, "<span class='date-c'>".$value."</span>" );
															}						
														}
														array_push( $meta_date_arr, "</div>" );
														
														$meta_date = !empty( $meta_date_arr ) ? implode( " ", $meta_date_arr ) : "";
														if ( !empty( $meta_date ) ){
															echo "<div class='meta_date'>";
																echo sprintf("%s", $meta_date);
															echo "</div>";		
														}
													?>
												</div>
											<?php
												echo "<div class='pic'>";
											?>
												<?php
													$thumbnail_obj = cws_thumb( $thumbnail ,array(), false );
													$thumbnail_url = esc_url( $thumbnail_obj[0] );
													$thumbnail_retina_thumb_exists = $thumbnail_obj[3]['retina_thumb_exists'];
													$thumbnail_retina_thumb_url = $thumbnail_obj[3]['retina_thumb_url'];
													if ( $thumbnail_retina_thumb_exists ){
														echo "<img src='$thumbnail_url' data-at2x='$thumbnail_retina_thumb_url' alt = '" . get_post_meta( get_the_id(), '_wp_attachment_image_alt', true ) . "' />";
													}
													else{
														echo "<img src='$thumbnail_url' data-no-retina alt = '" . get_post_meta( get_the_id(), '_wp_attachment_image_alt', true ) . "' />";
													}
												echo "</div>";
											echo "</div>";
										?>
									</div>
								</div>
							<?php
							$content = get_the_content();
							if ( !empty( $content ) ) echo "<div class='post_content'>" . apply_filters( 'the_content', $content ) . "</div>";
							if($aasana_theme_funcs){
								$aasana_theme_funcs->cws_page_links();
							}else{
								$aasana_theme_standard->cws_page_links();
							}
							

							/* ATTACHMENTS NAVIGATION */

							?>
							<?php
								ob_start();
								previous_image_link( false, "<span class='prev'></span><span>" . esc_html__( 'Previous Image', 'aasana' ) . "</span>" );
								$prev_img_link = ob_get_clean();
								ob_start();
								next_image_link( false, "<span>" . esc_html__( 'Next Image', 'aasana' ) . "</span><span class='next'></span>" );
								$next_img_link = ob_get_clean();
								if ( !empty( $prev_img_link ) || !empty( $next_img_link ) ){
									echo "<nav class='cws_img_navigation carousel_nav_panel clearfix'>";
										echo !empty( $prev_img_link ) ? "<div class='prev_section'>$prev_img_link</div>" : "";
										echo !empty( $next_img_link ) ? "<div class='next_section'>$next_img_link</div>" : "";
									echo "</nav>";
								}

							/* \ATTACHMENTS NAVIGATION */

							endwhile;
							wp_reset_postdata();
						?>
						</article>
					</div>
				</div>
			</section>
		</div>
		<?php comments_template(); ?>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : ''; ?>
</div>

<?php

get_footer ();
?>