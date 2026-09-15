<?php
	/**
	 * CWS Contact Widget Class
	 */

class CWS_Contact extends WP_Widget {
	public $fields = array();
	public function init_fields() {
		$this->fields = array(
			'title' => array(
				'title' => esc_html__( 'Widget title', 'cws-essentials' ),
				'atts' => 'id="widget-title"',
				'type' => 'text',
				'value' => '',
			),
			'show_icon_opts' => array(
				'title' => esc_html__( 'Title icon options', 'cws-essentials' ),
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
				'atts' => 'data-default-color="#ffffff"',
			),
			'icon_bg_type' => array(
				'title' => esc_html__( 'Background', 'cws-essentials' ),
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

			'description_part' => array(
				'title' => esc_html__('Description', 'cws-essentials' ),
				'type' => 'checkbox',
				'atts' => 'data-options="e:logo_description;e:text_description;"',
			),
				'logo_description' => array(
					'title' => esc_html__( 'Logo', 'cws-essentials' ),
					'type' => 'media',
					'addrowclasses' => 'disable box',
					'layout' => array(
						'logo_is_high_dpi' => array(
							'title' => esc_html__( 'High-Resolution logo', 'cws-essentials' ),
							'addrowclasses' => 'checkbox',
							'type' => 'checkbox',
						),
					),
				),
				'text_description' => array(
					'title' => esc_html__( 'Text', 'cws-essentials' ),
					'type' => 'textarea',
					'atts' => 'rows="10"',
					'addrowclasses' => 'disable box',
					'value' => '',
				),
				'information_part' => array(
					'title' => esc_html__('Contact info', 'cws-essentials' ),
					'type' => 'checkbox',
					'atts' => 'data-options="e:information_group;e:icons_color;"',
				),
				'information_group' => array(
					'type' => 'group',
					'addrowclasses' => 'group expander sortable disable box',
					'title' => esc_html__('Information', 'cws-essentials' ),
					'button_title' => esc_html__('Add new field', 'cws-essentials' ),
					'layout' => array(
						'title' => array(
							'type' => 'text',
							'atts' => 'data-role="title"',
							'title' => esc_html__('Description', 'cws-essentials' ),
						),
						'icon' => array(
							'type' => 'select',
							'addrowclasses' => 'fai',
							'source' => 'fa',
							'title' => esc_html__('Icon', 'cws-essentials' )
						),
					),
				),
				'icons_color' => array(
					'type'      => 'text',
					'title'     => esc_html__( 'Icons color', 'cws-essentials' ),
					'addrowclasses' => 'disable box',
					'atts' => 'data-default-color="'.AASANA_COLOR.'"',
				),
				'social_part' => array(
					'title' => esc_html__('Social links', 'cws-essentials' ),
					'type' => 'checkbox',
					'atts' => 'data-options="e:icons_count;e:social_icons_shape_type;e:social_icons_size;e:social_icons_alignment;"',
				),
				'icons_count' => array(
					'title' => esc_html__( 'Icons to show', 'cws-essentials' ),
					'type' => 'number',
					'addrowclasses' => 'disable box',
					'value' => '5'
				),
				'social_icons_shape_type' => array(
					'title' => esc_html__( 'Shape', 'cws-essentials' ),
					'type' => 'radio',
					'addrowclasses' => 'disable box',
					'value' => array(
						'none' => array( esc_html__( 'None', 'cws-essentials' ), 	true, 'd:social_icons_hover'),
						'squared' => array( esc_html__( 'Squared', 'cws-essentials' ), 	false, 'd:social_icons_hover'),
						'round' =>array( esc_html__( 'Round', 'cws-essentials' ), false, 'd:social_icons_hover'),
						'hexagon' =>array( esc_html__( 'Hexagon', 'cws-essentials' ), false, 'e:social_icons_hover'),
					),
				),
				'social_icons_hover' => array(
					'title' => esc_html__( 'Hover effect', 'cws-essentials' ),
					'addrowclasses' => 'disable box',
					'type' => 'checkbox',
				),
				'social_icons_size' => array(
					'type' => 'select',
					'title' => esc_html__( 'Size', 'cws-essentials' ),
					'addrowclasses' => 'disable box',
					'source' => array(
						'1x' => array(esc_html__( 'Mini', 'cws-essentials' ), false),
						'lg' => array(esc_html__( 'Small', 'cws-essentials' ), false),
						'2x' => array(esc_html__( 'Medium', 'cws-essentials' ), true),
						'3x' => array(esc_html__( 'Big', 'cws-essentials' ), false),
					)
				),
				'social_icons_alignment' => array(
					'type' => 'select',
					'title' => esc_html__( 'Alignment', 'cws-essentials' ),
					'addrowclasses' => 'disable box',
					'source' => array(
						'left' => array( esc_html__( 'Left', 'cws-essentials' ), false),
						'center' => array( esc_html__( 'Center', 'cws-essentials' ), true),
						'right' => array( esc_html__( 'Right', 'cws-essentials' ), false),
					)
				),
		);
	}

	function __construct() {
		$widget_ops = array( 'classname' => 'widget-cws-contact', 'description' => esc_html__( 'Add description, information, social links about site', 'cws-essentials' ) );
		parent::__construct( 'cws-contact', esc_html__( 'CWS Contact info', 'cws-essentials' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		extract( shortcode_atts( array(
			'title' => '',
			'show_icon_opts' => '0',
			'information_part' => '',
			'description_part' => '',
			'social_part' => '',
			'information_group' => '',
			'icons_color' => '',
			'logo_description' => '',
			'text_description' => '',
			'icons_count' => '5',
			'social_icons_shape_type' => '',
			'social_icons_hover' => '',
			'social_icons_size' => '',
			'social_icons_alignment' => '',
		), $instance));
		global $aasana_theme_funcs;
		
		$social = $aasana_theme_funcs->cws_get_option( 'social_group' );

		$show_icon_opts = ($show_icon_opts === 'on') ? '1' : $show_icon_opts;

		$widget_title_icon = $show_icon_opts === '1' ? $aasana_theme_funcs->cws_widget_title_icon_rendering( $instance ) : '';

		$title = esc_html($title);

		if (isset($logo_description['src']) && !empty($logo_description['src'])) {

			$logo = $logo_description;
			$logo_is_high_dpi = $logo['logo_is_high_dpi'];
			$logo_image_src = wp_get_attachment_image_src($logo['id'], 'full');
			$logo_height = $logo_image_src[2];
			$logo_width = $logo_image_src[1];

			if ( $logo_is_high_dpi ) {
				$thumb_obj = cws_thumb( $logo['src'],array( 'width' => floor( (int) $logo_width / 2 ), 'crop' => true ),false );

				$thumb_path_hdpi = $thumb_obj[3]['retina_thumb_exists'] ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_url( $thumb_obj[3]['retina_thumb_url'] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
				$logo_src = $thumb_path_hdpi;
			} else {
				$logo_src = " src='" . esc_url( $logo['src'] ) . "' data-no-retina";
			}

		}

	echo sprintf('%s',$before_widget);
			if ( !empty( $widget_title_icon ) ){
				echo sprintf("%s", $before_title) . "<div class='widget_title_box'><div class='widget_title_icon_section'>$widget_title_icon</div><div class='widget_title_text_section'>$title</div></div>" . $after_title;
			}
			else if (!empty( $title )){
				echo sprintf("%s", $before_title) . esc_html($title) . $after_title;
			}

		echo "<div class='cws_textwidget_content'>";

			if ($description_part == '1') {
				ob_start();
				?>
					<div class="text_description">
						<?php

						if (!empty($logo_description['src'])) { ?>

							<div class="logo_description">
								<?php if(!empty($logo_src)){ ?>
									<img <?php echo (esc_url($logo_src) . " alt = '" . get_post_meta( $logo['id'], '_wp_attachment_image_alt', true ) . "'"); ?>  />
								<?php } else { ?>
									<h1 class='header_site_title'><?php echo get_bloginfo( 'name' ); ?></h1>
								<?php } ?>
	                    	</div>

						<?php } ?>
						<?php echo (!empty( $text_description ) ? '<p>'.esc_html($text_description).'</p>': ''); ?>
					</div>
				<?php
				echo ob_get_clean();
			}

			if ($information_part == '1') {
				if (!empty( $information_group )){
				ob_start();
				?>
					<div class="information_group">
						<?php foreach ($information_group as $key => $value) { ?>
							<p><i style="color: <?php echo esc_attr($icons_color) ?>" class="<?php echo esc_attr($value['icon']) ?>"></i><?php echo esc_html($value['title']) ?></p>
						<?php } ?>
					</div>
				<?php
				echo ob_get_clean();
				} else {
					echo "<div class='cws_textwidget_content'>No info.</div>";
				}
			}

			if ($social_part == '1') {
				if(!empty( $social )) {
					$i = 0;
					$icons_count = intval($icons_count);
					$hexagon = "<svg class='svg-hexagon' xmlns='http://www.w3.org/2000/svg'><g><path stroke-width='1' stroke-opacity='null' stroke='#e8e8e8' fill-opacity='0' fill='#fff' d='m40.00001,0.63895l38,23.36105l0,42l-38,23.30561l-37.99999,-23.30561c0,-17.50011 0,-25.11111 0,-42l37.99999,-23.36105z'></path></g></svg>";
					ob_start();
					?>
						<div class="cws_social_links position-<?php echo esc_attr($social_icons_alignment) ?> size-<?php echo esc_attr($social_icons_size) ?>" style="text-align: <?php echo esc_attr($social_icons_alignment) ?>;">
							<?php foreach ($social as $key => $value) { if($i < $icons_count && !empty($value['icon']) ){ ?>
								<a href="<?php echo esc_url($value['url']) ?>" class="cws_social_link <?php echo esc_attr($social_icons_shape_type) ?>" title="<?php echo esc_html($value['title']) ?>" target="_blank">
									<i class="cws_fa <?php echo esc_attr(($social_icons_hover == '1' ? 'hexagon-hover ' : '')) ?>fa <?php echo esc_attr($value['icon']) ?> <?php echo esc_attr((!empty($social_icons_size) ? 'fa-'.$social_icons_size : '')) ?> simple_icon"><?php echo ($social_icons_shape_type == 'hexagon' ? "<span class='container-hexagon'>$hexagon</span>" : '');?></i>
								</a>
							<?php } $i++;} ?>
						</div>
					<?php
					echo ob_get_clean();
				} else {
					echo "<div class='cws_textwidget_content'>No Social Icons found.</div>";
				}
			}
		echo "</div>";

	echo sprintf('%s',$after_widget);
	}

	function update( $new_instance, $old_instance ) {
		$instance = (array)$new_instance;
		foreach ($new_instance as $key => $v) {
			if ($v == 'on') {
				$v = '1';
			}
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