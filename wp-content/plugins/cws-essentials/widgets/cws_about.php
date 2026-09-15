<?php
	/**
	 * CWS About Widget Class
	 */

class CWS_About extends WP_Widget {
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
				'avatar' => array(
					'title' => esc_html__( 'Avatar', 'cws-essentials' ),
					'addrowclasses' => 'wide_picture',
					'type' => 'media',
				),
				'border' => array(
					'title' => esc_html__( 'Add border', 'cws-essentials' ),
					'type' => 'checkbox',
				),
				'shape' => array(
					'title' => esc_html__( 'Shape type', 'cws-essentials' ),
					'type' => 'radio',
					'value' => array(
						'rectangle' => array( esc_html__( 'Rectangle', 'cws-essentials' ),  true, 'e:width;e:height' ),
						'round' =>array( esc_html__( 'Round', 'cws-essentials' ), false,  'e:width;d:height' ),
					),
				),
				'width' => array(
					'type' => 'number',
					'title' => esc_html__( 'Width (px)', 'cws-essentials' ),
					'addrowclasses' => 'disable',
					'value' => '150',
				),
				'height' => array(
					'type' => 'number',
					'title' => esc_html__( 'Height (px)', 'cws-essentials' ),
					'addrowclasses' => 'disable',
					'value' => '150',
				),
				'link' => array(
					'type'      => 'text',
					'title'     => esc_html__( 'Avatar link', 'cws-essentials' ),
					'atts' => 'placeholder="'.esc_html__('http://', 'cws-essentials').'"',
				),
				'grayscale_avatar' => array(
					'title' => esc_html__( 'Grayscale effect', 'cws-essentials' ),
					'type' => 'checkbox',
				),
				'name' => array(
					'type'      => 'text',
					'title'     => esc_html__( 'Name', 'cws-essentials' ),
				),
				'position' => array(
					'type'      => 'text',
					'title'     => esc_html__( 'Position', 'cws-essentials' ),
				),
				'description' => array(
					'title' => esc_html__( 'About Me', 'cws-essentials' ),
					'type' => 'textarea',
					'atts' => 'rows="10" placeholder="'.esc_html__('Enter information about self', 'cws-essentials').'"',
					'value' => '',
				),
				'signature' => array(
					'title' => esc_html__( 'Signature', 'cws-essentials' ),
					'addrowclasses' => 'wide_picture',
					'type' => 'media',
				),
		);
	}
	function __construct() {
		$widget_ops = array( 'classname' => 'widget-cws-about', 'description' => esc_html__( 'Add information about yourself', 'cws-essentials' ) );
		parent::__construct( 'cws-about', esc_html__( 'CWS About', 'cws-essentials' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		extract( shortcode_atts( array(
			'title' => '',
			'show_icon_opts' => '0',
			'avatar' => '',
			'border' => '0',
			'shape' => 'round',
			'width' => '',
			'height' => '',
			'link' => '',
			'grayscale_avatar' => '0',
			'name' => '',
			'position' => '',
			'description' => '',
			'signature' => '',
		), $instance));
		global $aasana_theme_funcs;

		$show_icon_opts = ($show_icon_opts === 'on') ? '1' : $show_icon_opts;

		$widget_title_icon = $show_icon_opts === '1' ? $aasana_theme_funcs->cws_widget_title_icon_rendering( $instance ) : '';

		$title = esc_html($title);

		$avatar_src = !empty($avatar) ? $avatar['src'] : '';

		if ( !empty($width) || !empty($height) ) {
			if ($shape == 'round') $height = $width;
			$thumb_obj = cws_thumb( $avatar_src, array( 'width' => $width, 'height' => $height, 'crop' => true ), false );
			$avatar_src = $thumb_obj[0];
		}

		$signature_src = !empty($signature) ? $signature['src'] : '';

		echo sprintf('%s',$before_widget);
			if ( !empty( $widget_title_icon ) ){
				echo sprintf("%s", $before_title) . "<div class='widget_title_box'><div class='widget_title_icon_section'>$widget_title_icon</div><div class='widget_title_text_section'>$title</div></div>" . $after_title;
			}
			else if (!empty( $title )){
				echo sprintf("%s", $before_title) . esc_html($title) . $after_title;
			}

		echo "<div class='cws_textwidget_content'>";
			ob_start();
			?>
				<div class="about_me">
					<?php if (!empty($avatar_src)) { ?>
						<div class="user_avatar<?php echo ( (!empty($width) && $shape == 'round') && $width == $height ? ' round' : '');?><?php echo ( $border = '1' ? ' user_avatar_border' : '');?>">
						<?php if (!empty($link)) { ?> <a href="<?php echo esc_url($link) ?>"> <?php } ?>
							<img <?php echo ($grayscale_avatar == '1' ? ' class="grayscale" ': '') ?>src="<?php echo esc_url($avatar_src) ?>" alt="<?php echo (!empty($name) ? $name : '') ?>" />
						<?php if (!empty($link)) { ?> </a> <?php } ?>
						</div>
					<?php } ?>
					<?php echo (!empty($name) ? '<h4 class="user_name">'.$name.'</h4>' : '') ?>
					<?php echo (!empty($position) ? '<h5 class="user_position">'.$position.'</h5>' : '') ?>
					<?php echo (!empty($description) ? '<p class="user_description">'.esc_html($description).'</p>' : '') ?>
					<?php if (!empty($signature_src)) { ?>
						<div class="user_signature">
							<img src="<?php echo esc_url($signature_src) ?>" alt="Signature" />
						</div>
					<?php } ?>
				</div>
			<?php
			echo ob_get_clean();
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