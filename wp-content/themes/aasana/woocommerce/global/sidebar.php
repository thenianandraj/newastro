<?php
/**
 * Sidebar
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version	 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) { exit; // Exit if accessed directly
}
	global $aasana_theme_funcs;
	if($aasana_theme_funcs){
		$woo_sidebar = $aasana_theme_funcs->cws_get_option( 'woo_sidebar' );
		$woo_sidebar_single = $aasana_theme_funcs->cws_get_option( 'woo_sidebar_single' );
		if(is_single()){
			if ( is_active_sidebar( $woo_sidebar_single ) ) {
				dynamic_sidebar( $woo_sidebar_single );
			}			
		}else{
			if ( is_active_sidebar( $woo_sidebar ) ) {
				dynamic_sidebar( $woo_sidebar );
			}			
		}


		
	}

?>
