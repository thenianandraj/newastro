<?php
get_header();

	global $aasana_theme_funcs;
	$fixed_header = '';
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
?>
<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
	<?php echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '<div class="container">'; ?>
	<main <?php if($fixed_header == '1') echo (' class="header_shadow"'); ?>>
	<?php

		while ( have_posts() ) : the_post();			
			the_content();
			if ($aasana_theme_funcs){
				$aasana_theme_funcs->cws_page_links();
			}
		endwhile;

		wp_reset_postdata();
		if ($aasana_theme_funcs){
			$is_blog = $aasana_theme_funcs->cws_get_meta_option( 'is_blog' ) == '1';
			if ( $is_blog ) get_template_part( 'content', 'blog' );
		}
		comments_template();
	?>
	</main>
	<?php echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : '</div>'; ?>
</div>
<?php get_footer (); ?>