<?php
	/**
	 * CWS Text Widget Class
	 */
class CWS_Text extends WP_Widget {
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
			'text' => array(
				'title' => esc_html__( 'Text', 'cws-essentials' ),
				'type' => 'textarea',
				'atts' => 'rows="10"',
				'value' => '',
			),
			'with_paragraphs' => array(
				'title' => esc_html__( 'Automatically add paragraphs', 'cws-essentials' ),
				'type' => 'checkbox',
			),
			'add_link' => array(
				'title' => esc_html__( 'Add link', 'cws-essentials' ),
				'type' => 'checkbox',
				'atts' => 'data-options="e:link_url;e:link_text"',
			),
			'link_url' => array(
				'title' => esc_html__( 'Url', 'cws-essentials' ),
				'addrowclasses' => 'disable',
				'type' => 'text',
			),
			'link_text' => array(
				'title' => esc_html__( 'Link text', 'cws-essentials' ),
				'type' => 'text',
				'addrowclasses' => 'disable',
				'default' => '',
			),
		);
	}
	function __construct() {
		$widget_ops = array( 'classname' => 'widget-cws-text', 'description' => esc_html__( 'Modified WP Text widget', 'cws-essentials' ) );
		parent::__construct( 'cws-text', esc_html__( 'CWS Text', 'cws-essentials' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		extract( shortcode_atts( array(
			'title' => '',
			'show_icon_opts' => '0',
			'text' => '',
			'add_link' => '0',
			'link_opts' => '0',
			'link_url' => '',
			'link_text' => '',
		), $instance));
		global $aasana_theme_funcs;

		$show_icon_opts = ($show_icon_opts === 'on') ? '1' : $show_icon_opts;

		$widget_title_icon = $show_icon_opts === '1' ? $aasana_theme_funcs->cws_widget_title_icon_rendering( $instance ) : '';

		$title = esc_html($title);

		$add_link = $add_link === '1' ? true	: false;

		echo sprintf("%s", $before_widget);

			if ( !empty( $widget_title_icon ) ){
				echo sprintf("%s", $before_title) . "<div class='widget_title_box'><div class='widget_title_icon_section'>$widget_title_icon</div><div class='widget_title_text_section'>$title</div></div>" . $after_title;
			}
			else if (!empty( $title )){
				echo sprintf("%s", $before_title) . esc_html($title) . $after_title;
			}

			$text = do_shortcode( $text );
			$text_section = !empty( $text ) ? "<div class='text'>$text</div>" : "";
			$link_text = esc_html($link_text);
			$link_section = !empty( $link_text ) ? "<div class='link'><a href='" . ( !empty( $link_url ) ? esc_url($link_url) : "#" ) . "' class='cws_button mini'>$link_text</a></div>" : "";

			if ( !empty( $text_section ) || !empty( $link_section ) ){
				echo "<div class='cws_textwidget_content'>";
					echo sprintf("%s", $text_section);
					echo sprintf("%s", $link_section);
				echo "</div>";
			}

		echo sprintf("%s", $after_widget);
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