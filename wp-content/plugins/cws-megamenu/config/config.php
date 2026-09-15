<?php
	if ( !class_exists( "CWS_Megamenu_Config" ) ){
		class CWS_Megamenu_Config {
			public $opts = array(
				"custom_url" => array(
					"title" => "URL",
					"type" => "text",
					"value" => ""
				)
			);
			public $defaults = array(
				"url" => ""
			);
			public function __construct ( $opts = array() ){
				$this->get_defaults();
			}
			public function get_defaults (){
				foreach ( $this->defaults as $key => $value ){
					if ( isset( $this->opts[$key] ) && !isset( $this->opts[$key]['value'] ) ){
						$this->opts[$key]['value'] = $value;
					}
				}
			}
			public function get_options (){
				return $this->opts;
			}
		}
	}
?>