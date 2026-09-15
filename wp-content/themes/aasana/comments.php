<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
global $aasana_theme_funcs;
ob_start();
	
	if ( have_comments() ) {
			$comments_number = number_format_i18n( get_comments_number() );
			echo "<div class='comments_title ce_title und-title themecolor'> " . esc_html__( "Comments", 'aasana' ) . " <span class='comments-count'>($comments_number)&#x200E;</span>" . "</div>";

			wp_list_comments( array(
				'walker' => new AASANA_Walker_Comment(),
				'avatar_size' => 80,
			) );

			if ($aasana_theme_funcs){
				$aasana_theme_funcs->cws_comment_nav();
			} else {

				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
				?>
				<div class="comments_nav carousel_nav_panel clearfix">
					<?php
						if ( $prev_link = get_previous_comments_link( "<span class='prev'></span><span>" . esc_html__( 'Older Comments', 'aasana' ) . "</span>" ) ) {
							printf( '<div class="prev_section">%s</div>', esc_html($prev_link) );
						}

						if ( $next_link = get_next_comments_link( "<span>" . esc_html__( 'Newer Comments', 'aasana' ) . "</span><span class='next'></span>" ) ) {
							printf( '<div class="next_section">%s</div>', esc_html($next_link) );
						}
					?>
				</div><!-- .comment-navigation -->
				<?php
				}

			}

	} // have_comments()

	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
		echo apply_filters( 'the_content', "[cws_sc_msg_box type='info' is_closable='1' text='" . esc_html__( 'Comments are closed.', 'aasana' ) . "'][/cws_sc_msg_box]" );
	}

	$comment_form_args = array(
		'label_submit' => esc_html__( 'Send', 'aasana' )
	);
	ob_start();
	comment_form( $comment_form_args );
	$comment_form = ob_get_clean();
	echo trim( $comment_form );

$comments_section_content = ob_get_clean();
echo !empty( $comments_section_content ) ? "<div class='grid_row'><div class='grid_col grid_col_12'><div class='cols_wrapper'><div id='comments' class='comments-area'>$comments_section_content</div></div></div></div>" : "";
?>
