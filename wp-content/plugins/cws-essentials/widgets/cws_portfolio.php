<?php
	/**
	 * Latest Posts Widget Class
	 */
class CWS_Portfolio extends WP_Widget {

	function init_fields() {
		$this->fields = array(
			'title' => array(
				'title' => esc_html__( 'Widget Title', 'cws-essentials' ),
				'atts' => 'id="widget-title"',
				'type' => 'text',
			),
			'show_icon_opts' => array(
				'title' => esc_html__( 'Show icon options', 'cws-essentials' ),
				'type' => 'checkbox',
				'atts' => 'data-options="e:icon_type"',
			),
			'icon_type' => array(
				'title' => esc_html__( 'Icon type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'subtype' => 'images',
				'value' => array(
					'fa' => array( esc_html__( 'icon', 'cws-essentials' ), 	true, 	'e:icon_fa;e:icon_color;e:icon_bg_type;d:icon_img', '/img/align-left.png' ),
					'img' =>array( esc_html__( 'image', 'cws-essentials' ), false,	'd:icon_fa;d:icon_color;d:icon_bg_type;e:icon_img', '/img/align-right.png' ),
				),
			),
			'icon_fa' => array(
				'title' => esc_html__( 'Font Awesome character', 'cws-essentials' ),
				'type' => 'select',
				'addrowclasses' => 'disable fai',
				'source' => 'fa',
			),
			'icon_img' => array(
				'title' => esc_html__( 'Custom icon', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'type' => 'media',
			),
			'icon_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Icon color', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="'.AASANA_COLOR.'"',
			),
			'icon_bg_type' => array(
				'title' => esc_html__( 'Background type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'none' => array( esc_html__( 'None', 'cws-essentials' ), 	true, 	'd:icon_bgcolor;d:gradient_first_color;d:gradient_second_color;d:gradient_type' ),
					'color' => array( esc_html__( 'Color', 'cws-essentials' ), 	false, 	'e:icon_bgcolor;d:gradient_first_color;d:gradient_second_color;d:gradient_type' ),
					'gradient' =>array( esc_html__( 'Gradient', 'cws-essentials' ), false,'d:icon_bgcolor;e:gradient_first_color;e:gradient_second_color;e:gradient_type' ),
				),
			),
			'icon_bgcolor' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Icon background color', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="'.AASANA_COLOR.'"',
			),

			'gradient_first_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'From', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="'.AASANA_COLOR.'"',
			),
			'gradient_second_color' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'To', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'data-default-color="#0eecbd"',
			),
			'gradient_type' => array(
				'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'linear' => array( esc_html__( 'Linear', 'cws-essentials' ), 	true, 'e:gradient_linear_angle;d:gradient_radial_shape' ),
					'radial' =>array( esc_html__( 'Radial', 'cws-essentials' ), false,	'd:gradient_linear_angle;e:gradient_radial_shape' ),
				),
			),
			'gradient_linear_angle' => array(
				'type'      => 'number',
				'title'     => esc_html__( 'Angle', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'value' => '45',
			),
			'gradient_radial_shape' => array(
				'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'simple' => array( esc_html__( 'Simple', 'cws-essentials' ), 	true, 'e:gradient_radial_type;d:gradient_radial_size_key;d:gradient_radial_size' ),
					'extended' =>array( esc_html__( 'Extended', 'cws-essentials' ), false, 'd:gradient_radial_type;e:gradient_radial_size_key;e:gradient_radial_size' ),
				),
			),
			'gradient_radial_type' => array(
				'title' => esc_html__( 'Gradient type', 'cws-essentials' ),
				'type' => 'radio',
				'addrowclasses' => 'disable',
				'value' => array(
					'ellipse' => array( esc_html__( 'Ellipse', 'cws-essentials' ), 	true ),
					'circle' =>array( esc_html__( 'Cirle', 'cws-essentials' ), false ),
				),
			),
			'gradient_radial_size_key' => array(
				'type' => 'select',
				'title' => esc_html__( 'Size keyword', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'source' => array(
					'closest-side' => array(esc_html__( 'Closest side', 'cws-essentials' ), false),
					'farthest-side' => array(esc_html__( 'Farthest side', 'cws-essentials' ), false),
					'closest-corner' => array(esc_html__( 'Closest corner', 'cws-essentials' ), false),
					'farthest-corner' => array(esc_html__( 'Farthest corner', 'cws-essentials' ), true),
				),
			),
			'gradient_radial_size' => array(
				'type'      => 'text',
				'title'     => esc_html__( 'Size', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'atts' => 'placeholder="'.esc_html__('Two space separated percent values, for example (60% 55%)', 'cws-essentials').'"',
			),
			'layout' => array(
				'title' => esc_html__( 'Layout', 'cws-essentials' ),
				'type' => 'radio',
				'value' => array(
					'grid' => array( esc_html__( 'Grid', 'cws-essentials' ),  true, 'd:visible_count;' ),
					'carousel' =>array( esc_html__( 'Carousel', 'cws-essentials' ), false, 'e:visible_count;' ),
				),
			),
			'cats' => array(
				'type' => 'taxonomy',
				'title' => esc_html__( 'Categories', 'cws-essentials' ),
				'atts' => 'multiple',
				'taxonomy' => 'cws_portfolio_cat',
				'source' => array(),
			),
			'spacings' => array(
				'title' => esc_html__( 'Spacings (px)', 'cws-essentials' ),
				'type' => 'number',
				'value' => '1'
			),
			'columns' => array(
				'title' => esc_html__( 'Columns', 'cws-essentials' ),
				'type' => 'select',
				'source' => array(
					'1' => array('One Column', true, ''),
					'2' => array('Two Columns',false, ''),
					'3' => array('Three Columns',false, ''),
					'4' => array('Four Columns',false, '')
				),
			),
			'count' => array(
				'title' => esc_html__( 'Items Count', 'cws-essentials' ),
				'type' => 'number',
			),
			'visible_count' => array(
				'title' => esc_html__( 'Items per slide', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'type' => 'number',
			),
		);
	}

	function __construct() {
		$widget_ops = array( 'description' => esc_html__( 'Portfolio Items', 'cws-essentials' ) );
		parent::__construct( 'cws-portfolio-widget', esc_html__( 'CWS Portfolio', 'cws-essentials' ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract( $args );

		extract( shortcode_atts( array(
			'title' => '',
			'show_icon_opts' => '0',
			'layout' => 'grid',
			'cats' => array(),
			'spacings' => '1',
			'columns' => '3',
			'count' => get_option( 'posts_per_page' ),
			'visible_count' => get_option( 'posts_per_page' ),
		), $instance));

		global $aasana_theme_funcs;

		$carousel_mode = ($count > $visible_count) && $layout == 'carousel';
		$counter = 0;

		$use_blur = $aasana_theme_funcs->cws_get_option( 'use_blur' );
		$use_blur = isset($use_blur) && !empty($use_blur) && ($use_blur == '1') ? true : false;

		$title = esc_html($title);

		$count = empty( $count ) ? get_option( 'posts_per_page' ) : $count;
		$show_icon_opts = ($show_icon_opts === 'on') ? '1' : $show_icon_opts;
		$widget_title_icon = $show_icon_opts === '1' ? $aasana_theme_funcs->cws_widget_title_icon_rendering( $instance ) : '';

		$query_args = array(
			'post_type' => 'cws_portfolio',
			'ignore_sticky_posts' => true,
			'post_status' => 'publish',
			'posts_per_page' => $count
		);

		$tax_query = array();
		if ( !empty( $cats ) ){
			$tax_query[] = array(
				'taxonomy' => 'cws_portfolio_cat',
				'field' => 'slug',
				'terms' => $cats
			);
		}

		if ( !empty( $tax_query ) ) $query_args['tax_query'] = $tax_query;

		$q = new WP_Query( $query_args );

		$several = $q->post_count > 1 ? true : false;
		$gallery_id = esc_attr(uniqid( 'cws-portfolio-gallery-' ));

		echo sprintf('%s',$before_widget);
			if ( !empty( $widget_title_icon ) ){
				if ( !empty( $title ) ){
					echo sprintf("%s", $before_title) . "<div class='widget_title_box'><div class='widget_title_icon_section'>$widget_title_icon</div><div class='widget_title_text_section'>$title</div></div>" . $after_title;
				}
				else{
					echo sprintf("%s", $before_title)  . $widget_title_icon . $after_title;
				}
			}
			else if ( !empty( $title ) ){
				echo sprintf("%s", $before_title)  . esc_html($title) . $after_title;
			}
			if ( $q->have_posts() ):

				if ( $carousel_mode ){
					wp_enqueue_script ('owl_carousel');
				?>
					<div <?php post_class(array( 'widget_carousel','portfolio_columns', "col-".$columns )); ?>>
				<?php
				} else{ ?>
					<div <?php post_class(array( 'portfolio_item_thumbs','clearfix','portfolio_columns', "col-".$columns )); ?>>
				<?php }

					while ( $q->have_posts() ):
						$q->the_post();
						if ( has_post_thumbnail() ){
							$dims = array();

							$img_url = esc_url(wp_get_attachment_url( get_post_thumbnail_id() ));

							$dims['width'] = 290;
							$dims['height'] = 290;
							$dims['crop'] = array(
								$aasana_theme_funcs->cws_get_option( 'crop_x' ),
								$aasana_theme_funcs->cws_get_option( 'crop_y' )
							);

							$thumb_obj = cws_thumb( $img_url, $dims, true );
							$thumb_url = esc_url($thumb_obj[0]);
							$retina_thumb_url = esc_url($thumb_obj[3]);

								if ( $carousel_mode && $counter <= 0 ){
									echo "<div class='item'>";
								}

								echo "<div class='portfolio_item_thumb' style='padding:".$spacings."px;'>";
									echo "<div class='pic'>";
										echo "<a href='$img_url' class='fancy'" . ( $several ? " data-fancybox-group='$gallery_id'" : "" ) . ">";
											if ( isset($thumb_obj[3]) ){
												echo "<img src='$thumb_url' data-at2x='$retina_thumb_url' alt = '" . get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) . "' />";
											}
											else{
												echo "<img src='$thumb_url' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) . "' />";
											}
											if ( $use_blur ){
												echo "<img src='$thumb_url' class='blured-img' alt = '" . get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) . "' />";
											}
											echo "<div class='hover-effect'></div>";
										echo "</a>";
									echo "</div>";
								echo "</div>";

							if ( $carousel_mode ){
								if ( $counter >= $visible_count-1 || $q->current_post >= $q->post_count-1 )
								{
									echo "</div>";
									$counter = 0;
								}
								else
								{
									$counter ++;
								}
							}
						}
					endwhile;
					wp_reset_postdata();
				echo "</div>";
			else:
				echo do_shortcode( "[cws_sc_msg_box text='" . esc_html__( 'There are no posts matching the query', 'cws-essentials' ) . "'][/cws_sc_msg_box]" );
			endif;
		echo sprintf('%s',$after_widget);
	}

	function update( $new_instance, $old_instance ) {
		$instance = (array)$new_instance;
		foreach ($new_instance as $key => $v) {
			switch ($this->fields[$key]['type']) {
				case 'text':
					$instance[$key] = strip_tags($v);
					break;
			}
		}
		return $instance;
	}

	function form( $instance ) {
		$this->init_fields();
		if (function_exists('cws_core_build_layout') ) {
			echo cws_core_build_layout($instance, $this->fields, 'widget-' . $this->id_base . '[' . $this->number . '][');
		}
	}
}

?>