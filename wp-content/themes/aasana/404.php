<?php
	get_header ();
	global $aasana_theme_funcs;
?>
<div class="page_content">
	<?php
	$home_url = esc_url(get_home_url());
	?>
	<main>
		<div class="grid_row clearfix">
			<div class="grid_col grid_col_12">
				<div class="ce">
					<div class="not_found">
						<div class="banner_404">
							<img src="<?php echo AASANA_URI . "/img/404.png"; ?>" alt="404" />
						</div>
						<div class="desc_404">
							<div class="msg_404">
								<?php
									echo esc_html__( 'Sorry', 'aasana' ) . "<br />" . esc_html__( "This page doesn't exist.", "aasana" );
								?>
							</div>
							<div class="link">
								<?php
									echo esc_html__( 'Please, proceed to our ', 'aasana' ) . "<a href='$home_url'>" . esc_html__( 'Home page', 'aasana' ) . "</a>";
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</div>

<?php
get_footer ();
?>