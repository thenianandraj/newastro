<?php
/*
 * Default CWS Events template file.
*/
	get_header();	

	global $aasana_theme_funcs;
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
	?>
	<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
		<?php
			echo (isset($sb['content']) ? $sb['content'] : '');
		?>	
			<main>
				<div class="grid_row cws_tribe_events">
					<?php tribe_events_before_html(); ?> 
					<?php tribe_get_view(); ?>   
					<?php tribe_events_after_html(); ?>		
				</div>
			</main>			
		<?php 
			echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : '';
		?>
	</div>

<?php
get_footer();
