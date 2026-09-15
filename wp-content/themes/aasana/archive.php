<?php
/**
 * Archive template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Aasana
 * @since Aasana 1.0
 */
	get_header();

	global $aasana_theme_funcs;
	if(!empty($aasana_theme_funcs)){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);

	}

	?>
	<div class="<?php echo (isset($sb) ? $sb['sb_class'] : 'page_content'); ?>">
		<?php
			echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';
		?>
		<main>
			<?php get_template_part( 'content', 'blog' ); ?>
		</main>
		<?php echo (isset($sb['content']) && !empty($sb['content'])) ? "</div>" : ''; ?>
	</div>
<?php 
get_footer(); 