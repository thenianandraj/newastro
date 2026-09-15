<?php

if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class CWSFW_Section extends WP_Customize_Control {

	/**
	 * @access public
	 * @var    string
	 */
	public $type = 'cwsfw_section';

	/**
	 * @access public
	 * @var    array
	 */
	public $args = array();
	/**
	 * Constructor.
	 *
	 * If $args['settings'] is not defined, use the $id as the setting ID.
	 *
	 * @since   11/14/2012
	 * @uses    WP_Customize_Control::__construct()
	 * @param   WP_Customize_Manager $manager
	 * @param   string $id
	 * @param   array $args
	 * @return  void
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since   11/14/2012
	 * @return  void
	 */
	public function render_content() {
		$first_element = reset($this->args);
		$tab_count = count($this->args);
		$isCustomizer = is_customize_preview();
		if ('tab' === $first_element['type']) {
			echo '<ul class="cwsfw-subsections">';
			echo '<li></li>';
			foreach ($this->args as $tabs => $v) {
				if (isset($v['customizer']) && !$v['customizer']['show']) continue;
				echo (($tab_count == 1 && $isCustomizer) ? '' : '<li id="accordion-subsection-'. $tabs .'" class="accordion-subsection"><h3 class="accordion-section-title open">'.$v['title'].'</h3>');
				echo '<ul class="accordion-subsection-content'.(($tab_count == 1 && $isCustomizer) ? ' single_tab' : '').'">';
				echo cws_core_cwsfw_print_layout($v['layout'], '');
				echo '</ul>';
				echo (($tab_count == 1 && $isCustomizer) ? '' : '</li>');
			}
			echo '</ul>';
		} else {
			echo cws_core_cwsfw_print_layout($this->args, '');
		}
	}

}
