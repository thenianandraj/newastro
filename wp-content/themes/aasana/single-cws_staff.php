<?php
	get_header();
	$p_id = get_queried_object_id();


	global $aasana_theme_funcs;
	if ($aasana_theme_funcs){
		$sb = $aasana_theme_funcs->cws_render_sidebars( get_queried_object_id() );
		$fixed_header = $aasana_theme_funcs->cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);
	}
	$post_meta = get_post_meta( get_the_ID(), 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$experience = isset( $post_meta['experience'] ) ? $post_meta['experience']: "";
	$email = isset( $post_meta['email'] ) ? $post_meta['email']: "";
	$biography = isset( $post_meta['biography'] ) ? $post_meta['biography']: "";

	$add_view_btn = isset( $post_meta['add_view_btn'] ) ? $post_meta['add_view_btn']: "";
	$title_btn_view = isset( $post_meta['title_btn_view'] ) ? $post_meta['title_btn_view']: "";
	$link_to = isset( $post_meta['link_to_view'] ) ? $post_meta['link_to_view']: "";
	$link_custom_url = isset( $post_meta['link_custom_url'] ) ? $post_meta['link_custom_url']: "";
	ob_start();
	cws_vc_shortcode_cws_staff_single_social_links ();
	$social_links = ob_get_clean();	
	?>
	<div class="<?php echo (isset($sb['sb_class']) ? $sb['sb_class'] : 'page_content'); ?>">
		<?php
		echo (isset($sb['content']) && !empty($sb['content'])) ? $sb['content'] : '';
		echo "<main id='page_content'>";
		echo "<div class='grid_row'>";
		$GLOBALS['cws_vc_shortcode_single_post_atts'] = array(
			'sb_layout'						=> $sb['layout_class'],
			);
		while ( have_posts() ) : the_post();
		echo "<article id='cws_staff_post_{$p_id}' class='cws_staff_post post_single clearfix'>";
		ob_start();
		echo "<div class='single_staff_wrapper'>";
		echo "<div class='wrapp_media_staff'>";
		cws_vc_shortcode_cws_staff_single_post_media ();
		if(!empty($social_links)){
			echo sprintf("%s", $social_links);
		}	
		echo "</div>";
		$media = ob_get_clean();
		$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_staff_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_staff_single_post_floated_media'] : false;
		unset( $GLOBALS['cws_vc_shortcode_cws_staff_single_post_floated_media'] );						
		if($floated_media){
			echo "<div class='clearfix'>";
		}
		if ( $floated_media ){
			echo "<div class='floated_media cws_staff_floated_media single_post_floated_media'>";
			echo "<div class='floated_media_wrapper cws_staff_floated_media_wrapper single_post_floated_media_wrapper'>";
			echo sprintf("%s", $media);									
			echo "</div>";
			echo "</div>";						
		}
		else{
			echo sprintf("%s", $media);
		}		
		$deps = function_exists('cws_vc_shortcode_get_post_term_links_str') ? cws_vc_shortcode_get_post_term_links_str( 'cws_staff_member_department' ) : "";
		$poss = function_exists('cws_vc_shortcode_get_post_term_links_str') ? cws_vc_shortcode_get_post_term_links_str( 'cws_staff_member_position' ) : "";
		$terms = "";
		$terms .= !empty( $deps ) ? "$deps" : "";
		$terms .= !empty( $poss ) ? "$poss" : "";		
							// ob_start();
		cws_vc_shortcode_cws_staff_posts_grid_post_title ();		
		if ( !empty( $terms ) ){
			echo "<div class='post_terms cws_staff_post_terms post_single_post_terms'>";
			echo sprintf("%s", $terms);
			echo "</div>";
		}
		if(!empty($experience) || !empty($email) || !empty($biography)){
			echo "<div class='wrapp_info_staff'>";
		}
		if(!empty($experience)){
			echo "<div class='experience'><span>".(esc_html__('Experience', 'aasana')).":</span><p>".esc_html($experience)."</p></div>";
		}		
		if(!empty($email)){
			echo "<div class='email'><span>".(esc_html__('E-mail', 'aasana')).":</span><a href='mailto:".esc_html($email)."'>".esc_html($email)."</a></div>";
		}		
		if(!empty($biography)){
			echo "<div class='biography'><span>".(esc_html__('Biography', 'aasana')).":</span><p>".esc_html($biography)."</p></div>";
		}
		if(!empty($experience) || !empty($email) || !empty($biography)){
			echo "</div>";
		}
		

		
		$link_view = '';
		if(!empty($link_to) && $link_to == 'archive'){
			$link_view = esc_url( add_query_arg( array('filterStaff'=>get_the_id()), get_post_type_archive_link( "cws_classes" ) ) ); 
		}elseif(!empty($link_to) && $link_to == 'custom_url' && !empty($link_custom_url)){
			$link_view = esc_url($link_custom_url);
		}
		if(!empty($add_view_btn) && !empty($title_btn_view) && !empty($link_view)){
			
			echo "<div class='post_atts cws_staff_post_atts post_single_post_atts'>";
			echo '<a class="cws_vc_shortcode_button small add_hover" href="'.$link_view.'">'.$title_btn_view.'</a>';
			echo "</div>";
		}
		if($floated_media){
			echo "</div>";
		}
		
		echo "</div>";
		cws_vc_shortcode_cws_staff_single_post_content ();
		$aasana_theme_funcs->cws_page_links ();
		echo "</article>";
		endwhile;
		wp_reset_postdata();
		unset( $GLOBALS['cws_vc_shortcode_single_post_atts'] );
		echo "</div>";
		echo "</main>";
		echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : '';
		?>
	</div>

<?php
get_footer();
?>
