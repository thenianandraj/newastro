<?php
/*
Plugin Name: CWS MegaMenu
Plugin URI:  http://creaws.com
Description: Internal use for creaws/cwsthemes themes only.
Version:     1.1
Author:      Creative Web Solutions
Author URI:  http://creaws.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: cws-megamenu
*/

if ( !class_exists( "CWS_Megamenu" ) ){
    class CWS_Megamenu {
        public static $megamenu_content_redered = false;
        public $config;
        public $opts;
        public function __construct (){
            require_once ( 'config/config.php' );
            if ( !class_exists( 'CWS_Megamenu_Config' ) ){
                return false;
            }
            $this->config = new CWS_Megamenu_Config ();
            $this->opts = $this->config->get_options ();
            register_activation_hook ( __FILE__, array( $this, 'activation_hook' ) );
            register_deactivation_hook( __FILE__, array( $this, 'deactivation_hook' ) );
            add_action( 'init', array( $this, 'setup_post_type' ) );
            /* for composer */
            if ( $this->check_for_plugin( 'js_composer/js_composer.php' ) ){
                add_action( 'init', array( $this, 'vc_add_column_title' ) );
            }
            /* \for composer */
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );           
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) ); 
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
            add_filter ( 'walker_nav_menu_start_el', array( $this, 'start_el_filter' ), 10, 4 );
            add_action( 'wp_update_nav_menu_item', array( $this, 'update_fields' ), 10, 3 );
            add_action( 'wp_ajax_cws_megamenu_items_data', array( $this, 'get_items_data' ) );
            add_filter( 'nav_menu_item_id', array( $this, 'add_menu_item_uniqid_for_secondary_usage' ), 20, 4 ); /* because of wordpress use filter for single use id of menu item */
            add_filter( 'wp_nav_menu',   array( $this, 'wp_nav_menu_remove_attributes' ) );
         }

         function wp_nav_menu_remove_attributes($menu){
              return $menu = preg_replace('/ id=\"(.*)\"/iU', '', $menu );
         }
         /* for composer */
         function vc_add_column_title (){
            vc_add_param( 'vc_column', array(
                "type"          => "textfield",
                "admin_label"   => true,
                "heading"       => esc_html__( 'Title', 'cws_megamenu' ),
                "description"   => esc_html__( 'Will be shown as column title and used as an opener for column content on mobile', 'cws_megamenu' ),
                "param_name"    => "mm_column_title",
                "weight"        => "999",
                "value"         => esc_html__( 'Title Must be Entered', 'cws_megamenu' )
            ));
         }
         /* \for composer */

         function add_menu_item_uniqid_for_secondary_usage ( $id, $item, $args, $depth = null ){
            if ( $item->object == 'megamenu_item' && empty( $id ) ){
                $id_base = 'menu-item-'. $item->ID . "-";
                $id = uniqid( $id_base );
            }
            return $id;
         }
        public function activation_hook (){
            flush_rewrite_rules ();
        }
        public function deactivation_hook (){
            flush_rewrite_rules();
        }
        public function setup_post_type (){
            register_post_type( 'megamenu_item', array(
                'label'                 => __( 'Megamenu Items', 'cws_megamenu' ),
                'labels'                => array(
                    'name'                  => __( 'Megamenu Items', 'cws_megamenu' ),
                    'singular_name'         => __( 'Megamenu Item', 'cws_megamenu' ),
                    'add_new'               => __( 'Add New', 'cws_megamenu' ),
                    'add_new_item'          => __( 'Add New Megamenu Item', 'cws_megamenu' ),
                    'edit_item'             => __( 'Edit Megamenu Item', 'cws_megamenu' ),
                    'new_item'              => __( 'New Megamenu Item', 'cws_megamenu' ),
                    'view_item'             => __( 'View Megamenu Item', 'cws_megamenu' ),
                    'search_items'          => __( 'Search Megamenu Items', 'cws_megamenu' ),
                    'not_found'             => __( 'No items found', 'cws_megamenu' ),
                    'not_found_in_trash'    => __( 'No items found in Trash', 'cws_megamenu' ),
                    'all_items'             => __( 'All Megamenu Items', 'cws_megamenu' ),
                ),
                'description'           => __( 'Post, which you can add to menu in menu panel and it\'s content will be shown as megamenu item', 'cws_megamenu'  ),
                'public'                => true,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'menu_icon'             => 'dashicons-welcome-widgets-menus',
                'show_in_nav_menus'     => true,
                'supports'              => array( 'title', 'editor', 'author' ),
                'pages'                 => false
            ));            
        }
        public function enqueue_scripts (){
            wp_register_script ( 'cws_megamenu_front',  plugin_dir_url( __FILE__ ) . 'assets/js/cws_megamenu_front.js', array( 'jquery' ) );
            wp_enqueue_script ( 'cws_megamenu_front' );            
        }
        public function enqueue_styles (){
            $is_rtl = is_rtl();
            wp_register_style ( 'cws_theme_default_megamenu',  plugin_dir_url( __FILE__ ) . 'assets/css/cws_theme_default.css', array( 'main' ) );
            wp_enqueue_style ( 'cws_theme_default_megamenu' );
            if ( $is_rtl ){
                wp_register_style( 'cws_theme_default_megamenu-rtl', plugin_dir_url( __FILE__ ) . 'assets/css/cws_theme_default-rtl.css', array( 'main', 'cws_theme_default_megamenu' ) );
                wp_enqueue_style( 'cws_theme_default_megamenu-rtl' );
            }
        }
        public function enqueue_admin_scripts (){
            wp_register_script ( 'cws_megamenu',  plugin_dir_url( __FILE__ ) . 'assets/js/cws_megamenu.js' );
            wp_localize_script ( 'cws_megamenu', 'cws_megamenu_opts', $this->opts );
            wp_enqueue_script ( 'cws_megamenu' );
        }
        public function enqueue_admin_styles (){
            wp_register_style( 'cws_megamenu_base', plugin_dir_url( __FILE__ ) . 'assets/css/base.css', false );
            wp_enqueue_style ( 'cws_megamenu_base' );
        }
        public function start_el_filter ( $item_output, $item, $depth, $args ){
            if ( !isset( $item->object ) || $item->object != "megamenu_item" || !isset( $item->object_id ) ){
                return $item_output;
            }
            $item_text = $item_output;
            $post_id = $item->object_id;
            $item_id = $item->ID;
            $post_object = get_post( $post_id );
            $item_meta = get_post_meta( $item_id );

            /** This filter is documented in wp-includes/post-template.php */
            $title = apply_filters( 'the_title', $item->title, $item->ID );

            /**
             * Filter a menu item's title.
             *
             * @since 4.4.0
             *
             * @param string $title The menu item's title.
             * @param object $item  The current menu item.
             * @param array  $args  An array of {@see wp_nav_menu()} arguments.
             * @param int    $depth Depth of menu item. Used for padding.
             */
            $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

            $post_content = $post_object->post_content;
            self::$megamenu_content_redered = true;
            $processed_content = apply_filters( 'the_content', $post_content );
            self::$megamenu_content_redered = false;
            if ( empty( $processed_content ) ){
                return $item_output;
            }
            $walker_class = isset( $args->walker ) && !empty( $args->walker ) ? $args->walker : "Walker_Nav_Menu";
            $walker_instance = new $walker_class();
            $megamenu_item_output = "";
            $megamenu_item_output .= "<span class='pointer'></span>";
            $walker_instance->start_lvl( $megamenu_item_output, $depth, $args );        
            /*** for composer ***/
            $vc_shortcodes_custom_css = get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );
            if ( ! empty( $vc_shortcodes_custom_css ) ) {
                $vc_shortcodes_custom_css = preg_replace( "#\.(vc_custom_\d+)#", ".cws_megamenu_item .$1", $vc_shortcodes_custom_css );
                $vc_shortcodes_custom_css = strip_tags( $vc_shortcodes_custom_css );
                $megamenu_item_output .= "<style type='text/css' data-type='vc_shortcodes-custom-css-cws_megamenu-item-{$item_id}'>";
                $megamenu_item_output .= $vc_shortcodes_custom_css;
                $megamenu_item_output .= '</style>';
            }
            $vc_post_custom_css = get_post_meta( $post_id, '_wpb_post_custom_css', true );
            if ( ! empty( $vc_post_custom_css ) ) {
                $vc_post_custom_css = strip_tags( $vc_post_custom_css );
                $megamenu_item_output .= "<style type='text/css' data-type='vc_custom-css-cws_megamenu-item-{$item_id}'>";
                $megamenu_item_output .= $vc_post_custom_css;
                $megamenu_item_output .= '</style>';
            }
            /*** \for composer ***/

            $post_meta = get_post_meta( $post_id, 'cws_mb_post' );
            $post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
            $dis_fw_menu = !empty($post_meta['fw_mgmenu']) ? $post_meta['fw_mgmenu'] : '';
            $mgmenu_pos = !empty($post_meta['mgmenu_pos']) ? $post_meta['mgmenu_pos'] : '';
            $mgmenu_width = !empty($post_meta['mgmenu_width']) ? $post_meta['mgmenu_width'] : "";
            $classes = $dis_fw_menu ? 'dis_full_width' : "";
            //$classes .= !empty($mgmenu_pos) ? ' ' . $mgmenu_pos . '' : "";
            $data_w = !empty($mgmenu_width) ? " data-width='$mgmenu_width'" : ""; 
            $data_pos = !empty($mgmenu_pos) ? " data-pos='$mgmenu_pos'" : ""; 

            $megamenu_item_output .= "<li class='cws_megamenu_item $classes' $data_w $data_pos>";
            $megamenu_item_output .= $processed_content;   
            $megamenu_item_output .= "</li>";
            $walker_instance->end_lvl( $megamenu_item_output, $depth, $args );

            $custom_url = isset( $item_meta['_menu_item_custom_url'] ) ? $item_meta['_menu_item_custom_url'][0] : "";
            $custom_url = esc_url( $custom_url );
            $match = preg_match( "#<a.+href=\"(.+)\".*>(.*)</a>#", $item_text, $matches, PREG_OFFSET_CAPTURE );
            if ( $match ){
                $link_matches = $matches[0];
                $url_matches = $matches[1];
                $title_matches = $matches[2];
                if ( !empty( $custom_url ) ){
                    $item_text = substr_replace( $item_text, $custom_url, $url_matches[1], strlen( $url_matches[0] ) );
                }
                else{
                    $item_text = substr_replace( $item_text, "<span class='cws_megamenu_item_title'>" . esc_html( $title ) . "</span><span class='button_open'></span>", $link_matches[1], strlen( $link_matches[0] ) );
                }
            }

            return $item_text . $megamenu_item_output; 
        }
        function megamenu_item_pointer ( $item_output, $item ){
            if ( $item->object === "megamenu_item" ){
                $item_output .= "<span class='pointer'></span>";
            }
            return $item_output;
        }
        public function update_fields( $menu_id, $menu_item_db_id, $args ) {
            foreach ( $this->opts as $opt_key => $opt_val ) {
                if ( isset( $_REQUEST["menu-item-$opt_key"] ) && is_array( $_REQUEST["menu-item-$opt_key"] ) && isset( $_REQUEST["menu-item-$opt_key"][$menu_item_db_id] ) ) {
                    $opt_val = $_REQUEST["menu-item-$opt_key"][$menu_item_db_id];
                    update_post_meta( $menu_item_db_id, "_menu_item_$opt_key", $opt_val );
                }                   
            }
        }

        public function get_items_data (){
            if ( isset( $_POST['ids'] ) && !empty( $_POST['ids'] ) ){
                $ids = json_decode( stripcslashes( $_POST['ids'] ) );
                $items_data = array();
                for ( $i = 0; $i < count( $ids ); $i++ ){
                    $item_id = $ids[$i];
                    $match = preg_match( "#^menu-item-(\d+)$#", $item_id, $matches );
                    $pid = isset( $matches[1] ) ? $matches[1] : "";
                    $items_data[$item_id] = $this->get_item_data( $pid );
                }
            }
            echo json_encode( $items_data );
            wp_die();
        }

        public function get_item_data ( $id = "" ){
            $item_meta = get_post_meta( $id );
            $data = array();
            foreach ( $this->opts as $opt_key => $opt_settings ){
                $meta_key = "_menu_item_$opt_key";
                if ( isset( $item_meta[$meta_key] ) ){
                    $data[$opt_key] = $item_meta[$meta_key];
                }
                else if ( isset( $opt_settings['value'] ) ){
                    $data[$opt_key] = $opt_settings['value'];
                }
            }
            return $data;           
        }

        public function check_for_plugin ( $plugin ){   /* $plugin - folder/file  */
            return in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
        }
    }
}

$megamenu = new CWS_Megamenu ();

add_filter( 'hidden_meta_boxes', 'foo_hidden_meta_boxes', 10, 2 );
function foo_hidden_meta_boxes( $hidden, $screen ){
    if ( $screen->id === "nav-menus" ){
        $ind = array_search( "add-post-type-megamenu_item", $hidden );
        if ( $ind !== false ){
            array_splice( $hidden, $ind, 1 );
        }
    }
    return $hidden;
}

add_action( 'user_register', 'show_megamenuItems_in_navMenus', 10, 1 );

function show_megamenuItems_in_navMenus( $user_id ) {
    // echo "<h1>Hello</h1>";
    $user_meta = get_user_meta( $user_id );  
    $metaboxhidden_key = "metaboxhidden_nav-menus";
    $hidden_meta_boxes_arr = $user_meta[$metaboxhidden_key];
    $hidden_meta_boxes_serialized = isset( $hidden_meta_boxes_arr[0] ) ? $hidden_meta_boxes_arr[0] : "";
    $hidden_meta_boxes = maybe_unserialize( $hidden_meta_boxes_serialized );
    if ( $hidden_meta_boxes !== null && is_array($hidden_meta_boxes) ){
        if ( in_array( "add-post-type-megamenu_item", $hidden_meta_boxes ) ){
            array_splice( $hidden_meta_boxes, array_search( "add-post-type-megamenu_item", $hidden_meta_boxes ), 1 );
            update_user_meta( $user_id, $metaboxhidden_key, $hidden_meta_boxes );
        }
    }
}

/* ! Must be fired when user is registered or plugin installed */

?>