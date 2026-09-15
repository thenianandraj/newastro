<?php
/*
Plugin Name: CWS Essentials
Plugin URI:  http://cwsthemes.com
Description: Internal use for cwsthemes only.
Text Domain: cws-essentials
Version: 1.1.2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author:      Creative Web Solutions
*/


/**
 * Load plugin textdomain.
 */
function cws_load_textdomain() {
  load_plugin_textdomain( 'cws-essentials', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'cws_load_textdomain' );


//Add Widgets
add_action( "widgets_init", "cws_register_widgets" );
function cws_register_widgets() {

	require_once('widgets/cws_text.php');
	require_once('widgets/cws_latest_posts.php');
	require_once('widgets/cws_testimonials.php');
	require_once('widgets/cws_portfolio.php');
	require_once('widgets/cws_contact.php');
	require_once('widgets/cws_about.php');
	require_once('widgets/cws_gallery.php');

	register_widget('CWS_Text');
	register_widget('CWS_Latest_Posts');
	register_widget('CWS_Testimonials');
	register_widget('CWS_Portfolio');
	register_widget('CWS_Contact');
	register_widget('CWS_About');
	register_widget('CWS_Gallery');
}

function cws_load_metaboxes() {
	require_once('cws-metaboxes.php' );
}
add_action( 'plugins_loaded', 'cws_load_metaboxes' );



//Get custom post types slugs
function cws_get_slug($slug) {
	$new_slug = '';
	global $aasana_theme_funcs;
	if(!empty($aasana_theme_funcs)){
		$new_slug = $aasana_theme_funcs->cws_get_option($slug.'_slug');
	}
	$new_slug = !empty( $new_slug ) ? $new_slug : $slug;

	return sanitize_title($new_slug);
}

//Regenerate permalinks, if slug (blog / portfolio / staff / testimonials) changed
add_action( "init", "cws_rewrite_slug", 11 );

function cws_rewrite_slug() {
	$cws_rewrite_slug = get_option('cws_rewrite_slug');
	if ($cws_rewrite_slug){
		flush_rewrite_rules();
		update_option('cws_rewrite_slug', false);
	}
}
//-------------------------

/*------------------------------------
-------------- PORTFOLIO -------------
------------------------------------*/
if (!defined('CWS_SHORTCODES_PLUGIN_NAME'))
	define('CWS_SHORTCODES_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CWS_SHORTCODES_PLUGIN_DIR'))
	define('CWS_SHORTCODES_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CWS_SHORTCODES_PLUGIN_NAME);


if (!defined('CWS_SHORTCODES_PLUGIN_URL'))
	define('CWS_SHORTCODES_PLUGIN_URL', WP_PLUGIN_URL . '/' . CWS_SHORTCODES_PLUGIN_NAME);

$theme = wp_get_theme();
if ($theme->get( 'Template' )) {
	if ( ! defined( 'THEME_SLUG' ) ) {
  		define('THEME_SLUG', $theme->get( 'Template' ));
  	}
} else {
	if ( ! defined( 'THEME_SLUG' ) ) {
  		define('THEME_SLUG', $theme->get( 'TextDomain' ));
  	}
}

add_action( "init", "register_cws_portfolio_cat", 1 );
add_action( "init", "register_cws_portfolio", 2 );

function register_cws_portfolio_cat(){
	$rewrite_slug = cws_get_slug('portfolio');

	register_taxonomy( 'cws_portfolio_cat', 'cws_portfolio', array(
		'hierarchical' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => $rewrite_slug . '_cat' )
	));
}

function register_cws_portfolio (){
	$rewrite_slug = cws_get_slug('portfolio');

	$labels = array(
		'name' => esc_html__( 'Portfolio items', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Portfolio item', 'cws-essentials' ),
		'menu_name' => esc_html__( 'Portfolio', 'cws-essentials' ),
		'add_new' => esc_html__( 'Add New', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add New Portfolio Item', 'cws-essentials' ),
		'edit_item' => esc_html__('Edit Portfolio Item', 'cws-essentials' ),
		'new_item' => esc_html__( 'New Portfolio Item', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Portfolio Item', 'cws-essentials' ),
		'search_items' => esc_html__( 'Search Portfolio Item', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Portfolio Items found', 'cws-essentials' ),
		'not_found_in_trash' => esc_html__( 'No Portfolio Items found in Trash', 'cws-essentials' ),
		'parent_item_colon' => '',
	);

	register_post_type( 'cws_portfolio', array(
		'label' => esc_html__( 'Portfolio items', 'cws-essentials' ),
		'labels' => $labels,
		'public' => true,
		'rewrite' => array( 'slug' => $rewrite_slug ),
		'capability_type' => 'post',
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'page-attributes',
			'thumbnail'
			),
		'menu_position' => 23,
		'menu_icon' => 'dashicons-format-gallery',
		'taxonomies' => array( 'cws_portfolio_cat' ),
		'has_archive' => true
	));
}

//Add thumbnail image to portfolio posts
function add_cws_portfolio_thumb_name ($columns) {
	$columns = array_slice($columns, 0, 1, true) +
				array('cws_portfolio_thumbnail' => esc_html__('Thumbnails', 'cws-essentials')) +
				array_slice($columns, 1, NULL, true);
	return $columns;
}
add_filter('manage_cws_portfolio_posts_columns', 'add_cws_portfolio_thumb_name');

function add_cws_portfolio_thumb ($column, $id) {
	if ('cws_portfolio_thumbnail' === $column) {
		echo the_post_thumbnail('thumbnail');
	}
}
add_action('manage_cws_portfolio_posts_custom_column', 'add_cws_portfolio_thumb', 5, 2);
//Add thumbnail image to portfolio posts

/*------------------------------------
---------------- STAFF ---------------
------------------------------------*/

add_action( "init", "register_cws_staff_department", 3 );
add_action( "init", "register_cws_staff_position", 4 );
add_action( "init", "register_cws_staff", 5 );

function register_cws_staff (){
	$rewrite_slug = cws_get_slug('staff');

	$labels = array(
		'name' => esc_html__( 'Staff members', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Staff member', 'cws-essentials' ),
		'menu_name' => esc_html__( 'Our team', 'cws-essentials' ),
		'all_items' => esc_html__( 'All', 'cws-essentials' ),
		'add_new' => esc_html__( 'Add new', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add New Staff Member', 'cws-essentials' ),
		'edit_item' => esc_html__('Edit Staff Member\'s info', 'cws-essentials' ),
		'new_item' => esc_html__( 'New Staff Member', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Staff Member\'s info', 'cws-essentials' ),
		'search_items' => esc_html__( 'Find Staff Member', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Staff Members found', 'cws-essentials' ),
		'not_found_in_trash' => esc_html__( 'No Staff Members found in Trash', 'cws-essentials' ),
		'parent_item_colon' => '',
	);

	register_post_type( 'cws_staff', array(
		'label' => esc_html__( 'Staff members', 'cws-essentials' ),
		'labels' => $labels,
		'public' => true,
		'rewrite' => array( 'slug' => $rewrite_slug ),
		'capability_type' => 'post',
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'page-attributes',
			'thumbnail'
			),
		'menu_position' => 24,
		'menu_icon' => 'dashicons-groups',
		'taxonomies' => array( 'cws_staff_member_position' ),
		'has_archive' => true
	));
}

function register_cws_staff_department(){
	$rewrite_slug = cws_get_slug('staff');

	$labels = array(
		'name' => esc_html__( 'Departments', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Staff department', 'cws-essentials' ),
		'all_items' => esc_html__( 'All Staff departments', 'cws-essentials' ),
		'edit_item' => esc_html__( 'Edit Staff department', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Staff department', 'cws-essentials' ),
		'update_item' => esc_html__( 'Update Staff department', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add Staff department', 'cws-essentials' ),
		'new_item_name' => esc_html__( 'New Staff department name', 'cws-essentials' ),
		'parent_item' => esc_html__( 'Parent Staff department', 'cws-essentials' ),
		'parent_item_colon' => esc_html__( 'Parent Staff department:', 'cws-essentials' ),
		'search_items' => esc_html__( 'Search Staff departments', 'cws-essentials' ),
		'popular_items' => esc_html__( 'Popular Staff departments', 'cws-essentials' ),
		'separate_items_width_commas' => esc_html__( 'Separate with commas', 'cws-essentials' ),
		'add_or_remove_items' => esc_html__( 'Add or Remove Staff departments', 'cws-essentials' ),
		'choose_from_most_used' => esc_html__( 'Choose from the most used Staff departments', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Staff departments found', 'cws-essentials' )
	);
	register_taxonomy( 'cws_staff_member_department', 'cws_staff', array(
		'labels' => $labels,
		'hierarchical' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => $rewrite_slug . '_cat' )
	));
}

function register_cws_staff_position(){
	$rewrite_slug = cws_get_slug('staff');

	$labels = array(
		'name' => esc_html__( 'Positions', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Staff Member position', 'cws-essentials' ),
		'all_items' => esc_html__( 'All Staff Member positions', 'cws-essentials' ),
		'edit_item' => esc_html__( 'Edit Staff Member position', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Staff Member position', 'cws-essentials' ),
		'update_item' => esc_html__( 'Update Staff Member position', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add Staff Member position', 'cws-essentials' ),
		'new_item_name' => esc_html__( 'New Staff Member position name', 'cws-essentials' ),
		'search_items' => esc_html__( 'Search Staff Member positions', 'cws-essentials' ),
		'popular_items' => esc_html__( 'Popular Staff Member positions', 'cws-essentials' ),
		'separate_items_width_commas' => esc_html__( 'Separate with commas', 'cws-essentials' ),
		'add_or_remove_items' => esc_html__( 'Add or Remove Staff Member positions', 'cws-essentials' ),
		'choose_from_most_used' => esc_html__( 'Choose from the most used Staff Member positions', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Staff Member positions found', 'cws-essentials' )
	);
	register_taxonomy( 'cws_staff_member_position', 'cws_staff', array(
		'labels' => $labels,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => $rewrite_slug . '_tag' ),
		'show_tagcloud' => false
	));
}
// =====================================================================================================================================================

/* Testimonials */
add_action( "init", "register_cws_testimonial_department", 6 );
add_action( "init", "register_cws_testimonial_position", 7 );
add_action( "init", "register_cws_testimonials", 8 );

//Categories
function register_cws_testimonial_department(){
	$rewrite_slug = cws_get_slug('testimonials');
	
	$labels = array(
		'name' => esc_html__( 'Departments', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Department', 'cws-essentials' ),
		'all_items' => esc_html__( 'All departments', 'cws-essentials' ),
		'edit_item' => esc_html__( 'Edit department', 'cws-essentials' ),
		'view_item' => esc_html__( 'View department', 'cws-essentials' ),
		'update_item' => esc_html__( 'Update department', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add department', 'cws-essentials' ),
		'new_item_name' => esc_html__( 'New department name', 'cws-essentials' ),
		'parent_item' => esc_html__( 'Parent department', 'cws-essentials' ),
		'parent_item_colon' => esc_html__( 'Parent department:', 'cws-essentials' ),
		'search_items' => esc_html__( 'Search departments', 'cws-essentials' ),
		'popular_items' => esc_html__( 'Popular departments', 'cws-essentials' ),
		'separate_items_width_commas' => esc_html__( 'Separate with commas', 'cws-essentials' ),
		'add_or_remove_items' => esc_html__( 'Add or Remove departments', 'cws-essentials' ),
		'choose_from_most_used' => esc_html__( 'Choose from the most used departments', 'cws-essentials' ),
		'not_found' => esc_html__( 'No departments found', 'cws-essentials' )
	);

	register_taxonomy( 'cws_testimonial_department', 'cws_testimonial', array(
		'labels' => $labels,
		'hierarchical' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => $rewrite_slug . '_cat' )
	));
}

//Tags
function register_cws_testimonial_position(){
	$rewrite_slug = cws_get_slug('testimonials');

	$labels = array(
		'name' => esc_html__( 'Positions', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Member position', 'cws-essentials' ),
		'all_items' => esc_html__( 'All Member positions', 'cws-essentials' ),
		'edit_item' => esc_html__( 'Edit Member position', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Member position', 'cws-essentials' ),
		'update_item' => esc_html__( 'Update Member position', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add Member position', 'cws-essentials' ),
		'new_item_name' => esc_html__( 'New Member position name', 'cws-essentials' ),
		'search_items' => esc_html__( 'Search Member positions', 'cws-essentials' ),
		'popular_items' => esc_html__( 'Popular Member positions', 'cws-essentials' ),
		'separate_items_width_commas' => esc_html__( 'Separate with commas', 'cws-essentials' ),
		'add_or_remove_items' => esc_html__( 'Add or Remove Member positions', 'cws-essentials' ),
		'choose_from_most_used' => esc_html__( 'Choose from the most used Member positions', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Member positions found', 'cws-essentials' )
	);

	register_taxonomy( 'cws_testimonial_position', 'cws_testimonial', array(
		'labels' => $labels,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => $rewrite_slug . '_tag' ),
		'show_tagcloud' => false
	));
}


function register_cws_testimonials (){
	$rewrite_slug = cws_get_slug('testimonials');

	$labels = array(
		'name' => esc_html__( 'Testimonials items', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Testimonials', 'cws-essentials' ),
		'menu_name' => esc_html__( 'Testimonials', 'cws-essentials' ),
		'all_items' => esc_html__( 'All', 'cws-essentials' ),
		'add_new' => esc_html__( 'Add New', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add New', 'cws-essentials' ),
		'edit_item' => esc_html__('Edit Testimonials Item', 'cws-essentials' ),
		'new_item' => esc_html__( 'New Testimonials Item', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Testimonials Item', 'cws-essentials' ),
		'search_items' => esc_html__( 'Search Testimonials Item', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Testimonials Items found', 'cws-essentials' ),
		'not_found_in_trash' => esc_html__( 'No Testimonials Items found in Trash', 'cws-essentials' ),
		'parent_item_colon' => '',
	);

	register_post_type( 'cws_testimonial', array(
		'label' => esc_html__( 'Testimonials items', 'cws-essentials' ),
		'labels' => $labels,
		'public' => true,
		'rewrite' => array( 'slug' => $rewrite_slug ),
		'capability_type' => 'post',
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'page-attributes', // Sortable column "Order"
			'thumbnail'
			),
		'menu_position' => 22,
		'menu_icon' => 'dashicons-format-quote',
		'taxonomies' => array( 'cws_testimonial_department' ),
		'has_archive' => true
	));
}


function add_order_column( $columns ) {
  $columns['menu_order'] = /*esc_html__( */"Order"/*, "unilearn" )*/;
  return $columns;
}
add_action('manage_edit-cws_staff_columns', 'add_order_column');
add_action('manage_edit-cws_portfolio_columns', 'add_order_column');
add_action('manage_edit-cws_testimonial_columns', 'add_order_column');

/**
* show custom order column values
*/
function show_order_column($name){
  global $post;
  switch ($name) {
    case 'menu_order':
      $order = $post->menu_order;
      echo $order;
      break;
   default:
      break;
   }
}
add_action('manage_cws_staff_posts_custom_column','show_order_column');
add_action('manage_cws_portfolio_posts_custom_column','show_order_column');
add_action('manage_cws_testimonial_posts_custom_column','show_order_column');

/**
* make column sortable
*/
function order_column_register_sortable( $columns ){
	$new_columns = array(
		"menu_order" 	=> "menu_order",
		"date"			=> "date",
		"title"			=> "title"
	);
	return $new_columns;
}
add_filter('manage_edit-cws_staff_sortable_columns','order_column_register_sortable');
add_filter('manage_edit-cws_portfolio_sortable_columns','order_column_register_sortable');
add_filter('manage_edit-cws_testimonial_sortable_columns','order_column_register_sortable');


add_action( "init", "register_cws_classes", 9 );
add_action( "init", "register_cws_classes_cat", 10 );

function register_cws_classes_cat(){
	$rewrite_slug = cws_get_slug('classes');

	register_taxonomy( 'cws_classes_cat', 'cws_classes', array(
		'hierarchical' => true,
		'show_admin_column' => true,
		'rewrite' => array( 'slug' => $rewrite_slug . '_cat' )
		));
}

function register_cws_classes (){
	$rewrite_slug = cws_get_slug('classes');

	$labels = array(
		'name' => esc_html__( 'Classes', 'cws-essentials' ),
		'singular_name' => esc_html__( 'Classes', 'cws-essentials' ),
		'menu_name' => esc_html__( 'Our Classes', 'cws-essentials' ),
		'all_items' => esc_html__( 'All', 'cws-essentials' ),
		'add_new' => esc_html__( 'Add new', 'cws-essentials' ),
		'add_new_item' => esc_html__( 'Add New Classes', 'cws-essentials' ),
		'edit_item' => esc_html__('Edit Classes\'s info', 'cws-essentials' ),
		'new_item' => esc_html__( 'New Classes', 'cws-essentials' ),
		'view_item' => esc_html__( 'View Classes\'s info', 'cws-essentials' ),
		'search_items' => esc_html__( 'Find Classes', 'cws-essentials' ),
		'not_found' => esc_html__( 'No Classess found', 'cws-essentials' ),
		'not_found_in_trash' => esc_html__( 'No Classess found in Trash', 'cws-essentials' ),
		'parent_item_colon' => '',
	);

	register_post_type( 'cws_classes', array(
		'label' => esc_html__( 'Classes', 'cws-essentials' ),
		'labels' => $labels,
		'public' => true,
		'rewrite' => array( 'slug' => $rewrite_slug ),
		'capability_type' => 'post',
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'page-attributes',
			'thumbnail'
			),
		'menu_position' => 24,
		'menu_icon' => 'dashicons-clipboard',
		'taxonomies' => array( 'cws_classes_member_position' ),
		'has_archive' => true
	));
}

// =====================================================================================================================================================



function add_order_column_classes( $columns ) {
  $columns['menu_order'] = /*esc_html__( */"Order"/*, "unilearn" )*/;
  return $columns;
}
add_action('manage_edit-cws_classes_columns', 'add_order_column_classes');

/**
* show custom order column values
*/
function show_order_column_classes($name){
  global $post;
  switch ($name) {
    case 'menu_order':
      $order = $post->menu_order;
      echo $order;
      break;
   default:
      break;
   }
}
add_action('manage_cws_classes_posts_custom_column','show_order_column_classes');

/**
* make column sortable
*/
function order_column_register_sortable_classes( $columns ){
	$new_columns = array(
		"menu_order" 	=> "menu_order",
		"date"			=> "date",
		"title"			=> "title"
	);
	return $new_columns;
}
add_filter('manage_edit-cws_classes_sortable_columns','order_column_register_sortable_classes');

function Cws_shortcode_css() {
	return Cws_shortcode_css::instance();
}
class Cws_shortcode_css{
	public $settings;
	protected static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}    
    public function enqueue_cws_css( $style ) {
    	if(!empty($style)){
	 		ob_start(); 			
				echo $style;
			$css = ob_get_clean();
			$css = apply_filters( 'cws_enqueue_shortcode_css', $css, $style );

			wp_register_style( 'cws-footer', false );
			wp_enqueue_style( 'cws-footer' );
			wp_add_inline_style( 'cws-footer', $css ); 		
    	}

	}
}

if(!function_exists('cws_Hex2RGBA')){
	function cws_Hex2RGBA( $color, $opacity ) {
		$output = '';
		if (!empty($color)){
			//Sanitize $color if "#" is provided 
			if (mb_substr($color, 0, 4) === 'rgba') {
				if(!empty($opacity)){
					$rgba_o = str_replace("rgba(", "", $color);
					$rgba_o = explode(",", $rgba_o);
					return "rgba(".$rgba_o[0].",".$rgba_o[1].",".$rgba_o[2].", ".$opacity.")";
				}
				return $color;
			}

		    if ($color[0] == '#' ) {
		    	$color = mb_substr( $color, 1 );
		    }
		    //Check if color has 6 or 3 characters and get values
		    if (strlen($color) == 6) {
		            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		    } elseif ( strlen( $color ) == 3 ) {
		            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		    } else {
		            return $default;
		    }

		    //Convert hexadec to rgb
		    $rgb =  array_map('hexdec', $hex);

		    //Check if opacity is set(rgba or rgb)
		    if($opacity){
		    	if(abs($opacity) > 1)
		    		$opacity = 1.0;
		    	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		    } else {
		    	$output = 'rgb('.implode(",",$rgb).')';
		    }

		    //Return rgb(a) color string
		    return $output;
		}

	}	
}


/****************** POSTS GRID AJAX *******************/

function cws_vc_shortcode_posts_grid_dynamic_pagination (){
	extract( wp_parse_args( $_POST['data'], array(
		'section_id'				=> '',
		'post_type' 				=> '',
		'post_hide_meta'			=> array(),
		'cws_portfolio_data_to_show'=> '',
		'cws_staff_data_to_hide'	=> array(),
		'cws_testimonial_data_to_hide'	=> array(),
		'change_title'              => '',
		'title_btn'					=> '',
		'massonry'					=> '',
		'layout'					=> '1',
		'sb_layout'					=> '',
		'total_items_count'			=> get_option( 'posts_per_page' ),
		'items_pp'					=> get_option( 'posts_per_page' ),
		'page'						=> '1',
		'tax'						=> '',
		'terms'						=> array(),
		'filter'					=> 'false',
		'current_filter_val'		=> '',
		'req_page_url'				=> '',
		'crop_images'				=> '',
		'post_hide_meta'			=> '',
		'pagination_grid'			=> '',
		'full_width'				=> '',
		'addl_query_args'			=> array(),
		'post_hide_meta_override'	=> '',
		'post_hide_meta'			=> '',						
		'info_align'				=> '',
		'aligning'					=> '',
		'display_style'				=> '',
		'portfolio_style'			=> '',
		'info_pos'					=> '',
		'masonry'					=> '',
		'anim_style'				=> '',
		'item_shadow'				=> '',
		'en_hover_color'			=> '',
		'en_cat_color'				=> '',
		'hover_color'				=> '',
		'title_color'				=> '',
		'cat_color'					=> '',
		'appear_style'				=> '',
		'link_show'					=> '',
		'isotope_line_count'		=> '',
		'isotope_col_count'			=> '',
		'chars_count'				=> '',
		'add_divider'				=> '',
		'tax'						=> '',
		'filter_vals'				=> '',
		'hover_bg_color'			=> '',
		'proc_atts'					=> '',
		'bg_hover_color'			=> '',
	)));
	$req_page = $page;
	if ( !empty( $req_page_url ) ){
		$match = preg_match( "#paged?(=|/)(\d+)#", $req_page_url, $matches );
		$req_page = $match ? $matches[2] : '1';								// if page parameter absent show first page
	};

	$not_in = ( 1 == $req_page ) ? array() : get_option( 'sticky_posts' );
	$query_args = array('post_type'			=> array( $post_type ),
						'post_status'		=> 'publish',
						'post__not_in'		=> $not_in
						);
	$query_args['posts_per_page']		= $items_pp;
	$query_args['paged']				= $paged = $req_page;
	$old_terms = $terms;
	if ( $filter == 'true' && $current_filter_val != '_all_' && !empty( $current_filter_val ) ){
		$terms = array( $current_filter_val );

		if($post_type == 'cws_portfolio' && $display_style == 'filter' && !empty($old_terms)){
			$terms = $old_terms;
		}	
	}
	if ( !empty( $terms ) ){
		$query_args['tax_query'] = array(
			array(
				'taxonomy'		=> $tax,
				'field'			=> 'slug',
				'terms'			=> $terms
			)
		);
	}
	if ( in_array( $post_type, array( "cws_portfolio", "cws_staff", "cws_testimonial", "tribe_events", "cws_classes" ) ) ){
		$query_args['orderby'] 	= "menu_order date title";
		$query_args['order']	= "ASC";
	}
	$query_args = array_merge( $query_args, $addl_query_args );
	$q = new WP_Query( $query_args );
	$found_posts = $q->found_posts;
	$max_paged = $found_posts > $total_items_count ? ceil( $total_items_count / $items_pp ) : ceil( $found_posts / $items_pp );
	$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
		'post_type'					=> $post_type,
		'layout'					=> $layout,
		'massonry'					=> $massonry,
		'sb_layout'					=> $sb_layout,
		'post_hide_meta'			=> $post_hide_meta,
		'cws_portfolio_data_to_show'=> $cws_portfolio_data_to_show,
		'cws_staff_data_to_hide'	=> $cws_staff_data_to_hide,
		'cws_testimonial_data_to_hide'	=> $cws_testimonial_data_to_hide,
		'change_title'              => $change_title,
		'title_btn'					=> $title_btn,
		'crop_images'				=> $crop_images,
		'total_items_count'			=> $total_items_count,
		'full_width'				=> $full_width,
		'pagination_grid'			=> $pagination_grid,
		'post_hide_meta_override'	=> $post_hide_meta_override,
		'info_align'				=> $info_align,
		'aligning'					=> $aligning,
		'display_style'				=> $display_style,
		'portfolio_style'			=> $portfolio_style,
		'info_pos'					=> $info_pos,
		'masonry'					=> $masonry,
		'anim_style'				=> $anim_style,
		'item_shadow'				=> $item_shadow,
		'en_hover_color'			=> $en_hover_color,
		'en_cat_color'				=> $en_cat_color,
		'hover_color'				=> $hover_color,
		'title_color'				=> $title_color,
		'cat_color'					=> $cat_color,
		'appear_style'				=> $appear_style,
		'link_show'					=> $link_show,
		'isotope_line_count'		=> $isotope_line_count,
		'isotope_col_count'			=> $isotope_col_count,
		'chars_count'				=> $chars_count,
		'add_divider'				=> $add_divider,
		'tax'						=> $tax,
		'filter_vals'				=> $filter_vals,
		'hover_bg_color'			=> $hover_bg_color,
		'proc_atts'					=> $proc_atts,
		'bg_hover_color'			=> $bg_hover_color,

	);
	if ( function_exists( "cws_vc_shortcode_{$post_type}_posts_grid_posts" ) ){
		call_user_func_array( "cws_vc_shortcode_{$post_type}_posts_grid_posts", array( $q ) );
	}
	
	if ( $pagination_grid == 'load_more' ){
		echo cws_vc_shortcode_load_more ();
	}
	elseif($pagination_grid == 'standard_with_ajax'){
		echo cws_vc_shortcode_pagination($paged, $max_paged, true);
	}
	else{
		echo cws_vc_shortcode_pagination($paged, $max_paged, false);
	}
	unset ( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] );
	echo "<input type='hidden' id='{$section_id}_dynamic_pagination_page_number' name='{$section_id}_dynamic_pagination_page_number' class='cws_vc_shortcode_posts_grid_dynamic_pagination_page_number' value='$req_page' />";
	wp_die();
}
add_action( 'wp_ajax_cws_vc_shortcode_posts_grid_dynamic_pagination', 'cws_vc_shortcode_posts_grid_dynamic_pagination' );
add_action( 'wp_ajax_nopriv_cws_vc_shortcode_posts_grid_dynamic_pagination', 'cws_vc_shortcode_posts_grid_dynamic_pagination' );

function cws_vc_shortcode_posts_grid_dynamic_filter (){
	extract( wp_parse_args( $_POST['data'], array(
		'section_id'				=> '',
		'post_type' 				=> '',
		'post_hide_meta'			=> array(),
		'massonry'					=> '',
		'cws_portfolio_data_to_show'=> '',
		'cws_classes_data_to_show'	=> '',
		'cws_staff_data_to_hide'	=> array(),
		'cws_testimonial_data_to_hide'	=> array(),
		'layout'					=> '1',
		'sb_layout'					=> '',
		'total_items_count'			=> get_option( 'posts_per_page' ),
		'items_pp'					=> get_option( 'posts_per_page' ),
		'page'						=> '1',
		'tax'						=> '',
		'terms'						=> array(),
		'filter'					=> 'false',
		'current_filter_val'		=> '',
		'crop_images' 				=> '',
		'pagination_grid'			=> '',
		'full_width'				=> '',
		'customize_colors'			=> '',
		'custom_color'				=> '',
		'font_color'				=> '',
		'bg_color'					=> '',
		'addl_query_args'			=> array(),
		'info_align'				=> '',
		'aligning'					=> '',
		'display_style'				=> '',
		'portfolio_style'			=> '',
		'info_pos'					=> '',
		'masonry'					=> '',
		'anim_style'				=> '',
		'item_shadow'				=> '',
		'en_hover_color'			=> '',
		'en_cat_color'				=> '',
		'hover_color'				=> '',
		'title_color'				=> '',
		'cat_color'					=> '',
		'appear_style'				=> '',
		'link_show'					=> '',
		'isotope_line_count'		=> '',
		'isotope_col_count'			=> '',
		'chars_count'				=> '',
		'add_divider'				=> '',
		'filter_vals'				=> '',
		'hover_bg_color'			=> '',
		'proc_atts'					=> '',
		'bg_hover_color'			=> '',
		'custom_title_color'		=> '',
		'cws_gradient_color_from'   => '',
		'cws_gradient_color_to'     => '',


	)));
	$not_in = ( 1 == $req_page ) ? array() : get_option( 'sticky_posts' );
	$query_args = array('post_type'			=> array( $post_type ),
						'post_status'		=> 'publish',
						'post__not_in'		=> $not_in
						);
	$query_args['posts_per_page']		= $items_pp;
	$query_args['paged']		= $page;
	if ( $current_filter_val != '_all_' && !empty( $current_filter_val ) ){
		$terms = array( $current_filter_val );
	}
	if ( !empty( $terms ) ){
		$query_args['tax_query'] = array(
			array(
				'taxonomy'		=> $tax,
				'field'			=> 'slug',
				'terms'			=> $terms
			)
		);
	}
	if ( in_array( $post_type, array( "cws_portfolio", "cws_staff", "cws_testimonial", "tribe_events", "cws_classes" ) ) ){
		$query_args['orderby'] 	= "menu_order date title";
		$query_args['order']	= "ASC";
	}
	$query_args = array_merge( $query_args, $addl_query_args );
	$q = new WP_Query( $query_args );
	$found_posts = $q->found_posts;
	$max_paged = $found_posts > $total_items_count ? ceil( $total_items_count / $items_pp ) : ceil( $found_posts / $items_pp );
	$is_pagination = $max_paged > 1;
	$GLOBALS['cws_vc_shortcode_posts_grid_atts'] = array(
		'post_type'						=> $post_type,
		'layout'						=> $layout,
		'customize_colors'				=> $customize_colors,
		'custom_color'					=> $custom_color,
		'font_color'					=> $font_color,
		'bg_color'						=> $bg_color,
		'sb_layout'						=> $sb_layout,
		'massonry'						=> $massonry,
		'post_hide_meta'				=> $post_hide_meta,
		'cws_portfolio_data_to_show'	=> $cws_portfolio_data_to_show,
		'cws_classes_data_to_show'		=> $cws_classes_data_to_show,
		'cws_staff_data_to_hide'		=> $cws_staff_data_to_hide,
		'cws_testimonial_data_to_hide'	=> $cws_testimonial_data_to_hide,
		'crop_images'					=> $crop_images,
		'total_items_count'				=> $total_items_count,
		'pagination_grid'				=> $pagination_grid,
		'full_width'					=> $full_width,
		'info_align'					=> $info_align,
		'aligning'						=> $aligning,
		'display_style'					=> $display_style,
		'portfolio_style'				=> $portfolio_style,
		'info_pos'						=> $info_pos,
		'masonry'						=> $masonry,
		'anim_style'					=> $anim_style,
		'item_shadow'					=> $item_shadow,
		'en_hover_color'				=> $en_hover_color,
		'en_cat_color'					=> $en_cat_color,
		'hover_color'					=> $hover_color,
		'title_color'					=> $title_color,
		'cat_color'						=> $cat_color,
		'appear_style'					=> $appear_style,
		'link_show'						=> $link_show,
		'isotope_line_count'			=> $isotope_line_count,
		'isotope_col_count'				=> $isotope_col_count,
		'chars_count'					=> $chars_count,
		'add_divider'					=> $add_divider,
		'filter_vals'					=> $filter_vals,
		'tax'							=> $tax,
		'hover_bg_color'				=> $hover_bg_color,
		'proc_atts'						=> $proc_atts,
		'bg_hover_color'				=> $bg_hover_color,
		'custom_title_color'			=> $custom_title_color,
		'cws_gradient_color_from'   	=> $cws_gradient_color_from,
		'cws_gradient_color_to'     	=> $cws_gradient_color_to
	);
	if ( function_exists( "cws_vc_shortcode_{$post_type}_posts_grid_posts" ) ){
		call_user_func_array( "cws_vc_shortcode_{$post_type}_posts_grid_posts", array( $q ) );
	}
	
	if ( $is_pagination ){
		if ( $pagination_grid == 'load_more' ){
			echo cws_vc_shortcode_load_more ();
		}
		else{
			echo cws_vc_shortcode_pagination ( $page, $max_paged,true );
		}
	}
	unset ( $GLOBALS['cws_vc_shortcode_posts_grid_atts'] );
	wp_die();
}
add_action( 'wp_ajax_cws_vc_shortcode_posts_grid_dynamic_filter', 'cws_vc_shortcode_posts_grid_dynamic_filter' );
add_action( 'wp_ajax_nopriv_cws_vc_shortcode_posts_grid_dynamic_filter', 'cws_vc_shortcode_posts_grid_dynamic_filter' );

/****************** \POSTS GRID AJAX ******************/

function cws_portfolio_single(){
	$data = isset( $_POST['data'] ) ? $_POST['data'] : array();
	extract( shortcode_atts( array(
			'initial_id' => '',
			'requested_id' => ''
		), $data));
	if ( empty( $initial_id ) || empty( $requested_id ) ) die();

	$pid = $requested_id;
	$post_meta = get_post_meta( $pid, 'cws_mb_post' );
	$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
	$full_width = isset( $post_meta['full_width'] ) ? $post_meta['full_width'] : "";	
	
	ob_start();
		cws_vc_shortcode_cws_portfolio_single_post_post_media ($requested_id);
	$media = ob_get_clean();
	if ( !empty($full_width) ) {
		echo "<div class='cws_ajax_response_media_full'>";
			echo "<div class='cws_ajax_media'>";
				echo $media;
			echo "</div>";
		echo "</div>";
	}

	echo "<div class='cws_ajax_response'>";
		$pid = $requested_id;
		echo "<article id='cws_portfolio_post_{$pid}' class='cws_portfolio_post post_single item clearfix'>";
		if ( empty($full_width) ) {
			ob_start();
			cws_vc_shortcode_cws_portfolio_single_post_post_media ($requested_id);
			$media = ob_get_clean();
			$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] : false;
			unset( $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] );
			if ( $floated_media ){
				echo "<div class='floated_media cws_portfolio_floated_media single_post_floated_media'>";
				echo "<div class='floated_media_wrapper cws_portfolio_floated_media_wrapper single_post_floated_media_wrapper'>";
				echo $media;
				echo "</div>";
				echo "</div>";						
			}
			else{
				echo $media;
			}
		}
		ob_start();
		cws_vc_shortcode_cws_portfolio_single_post_title ( $pid );
		cws_vc_shortcode_cws_portfolio_single_post_content ($pid);
		$content_terms = ob_get_clean();
		if ( !empty( $content_terms ) ){
			if ( $floated_media ){
				echo "<div class='clearfix'>";
				echo $content_terms;
				echo "</div>";
			}
			else{
				echo $content_terms;
			}
		}
		echo "</article>";
	echo "</div>";
	die();
}
add_action( "wp_ajax_cws_portfolio_single", "cws_portfolio_single" );
add_action( "wp_ajax_nopriv_cws_portfolio_single", "cws_portfolio_single" );

function cws_classes_single(){
	$data = isset( $_POST['data'] ) ? $_POST['data'] : array();
	extract( shortcode_atts( array(
			'initial_id' => '',
			'requested_id' => ''
		), $data));
	if ( empty( $initial_id ) || empty( $requested_id ) ) die();

	echo "<div class='cws_ajax_response'>";
		$pid = $requested_id;
		$post_meta = get_post_meta( $pid, 'cws_mb_post' );
		$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
		$price = isset( $post_meta['price'] ) ? $post_meta['price'] : '';
		$date_events = isset( $post_meta['date_events'] ) ? $post_meta['date_events'] : '';
		$destinations = isset( $post_meta['destinations'] ) ? $post_meta['destinations'] : '';
		$time_events = isset( $post_meta['time_events'] ) ? $post_meta['time_events'] : '';
		if(!empty($price)){
			preg_match('/(.*[^0-9])(\d+)([\.,]\d+)/', $price, $matches);
			list(, $currency, $price, $pfraction) = $matches;
		}
		echo "<article id='cws_classes_post_{$pid}' class='cws_classes_post post_single item clearfix'>";
		ob_start();
		cws_vc_shortcode_cws_classes_single_post_post_media ($requested_id);
		$media = ob_get_clean();
		$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] : false;
		unset( $GLOBALS['cws_vc_shortcode_cws_classes_single_post_floated_media'] );
		if ( $floated_media ){
			echo "<div class='floated_media cws_classes_floated_media single_post_floated_media'>";
			echo "<div class='floated_media_wrapper cws_classes_floated_media_wrapper single_post_floated_media_wrapper'>";
			echo $media;
			echo "</div>";
			echo "</div>";						
		}
		else{
			echo $media;
		}

		ob_start();
		echo "<div class='wrap_title'>";
		echo "<div class='title_single_classes'>";
		cws_vc_shortcode_title($pid);
		echo "</div>";
		if(!empty($price)){
			echo "<div class='price_single_classes'>";
			echo "<span class='currency_price'>";
			echo esc_html($currency);
			echo "</span>";
			echo "<span class='price'>";
			echo esc_html($price);
			echo "</span>";
			echo "<span class='pfraction'>";
			echo esc_html($pfraction);
			echo "</span>";
			echo "</div>";						
		}
		echo "</div>";
		if(!empty($date_events)){
			echo "<div class='date_ev_single_classes'>";
			echo esc_html($date_events);
			echo "</div>";								
		}
		if(!empty($time_events) || !empty($destinations)){
			echo "<div class='wrap_desc_info'>";
			if(!empty($time_events)){
				echo "<div class='time_ev_single_classes'>";
				echo esc_html($time_events);
				echo "</div>";								
			}
			if(!empty($destinations)){
				echo "<div class='destinations_single_classes'>";
				echo esc_html($destinations);
				echo "</div>";	
			}
			echo "</div>";
		}

		cws_vc_shortcode_cws_classes_single_post_content ($pid);
		cws_vc_shortcode_cws_classes_teacher ($pid);
		$content_terms = ob_get_clean();
		if ( !empty( $content_terms ) ){
			if ( $floated_media ){
				echo "<div class='clearfix'>";
				echo $content_terms;
				echo "</div>";
			}
			else{
				echo $content_terms;
			}
		}
		echo "</article>";
	echo "</div>";
	die();
}
add_action( "wp_ajax_cws_classes_single", "cws_classes_single" );
add_action( "wp_ajax_nopriv_cws_classes_single", "cws_classes_single" );

function cws_vc_shortcode_single_portfolio_ajax_load () {
	$query_args = array('post_type'			=> 'cws_portfolio',
						'p' 				=> $_POST['post_id']
						);
	$post_query = new WP_Query( $query_args );
	while( $post_query->have_posts() ) : $post_query->the_post();

		$sb = cws_vc_shortcode_render_sidebars( get_queried_object_id() );
		$fixed_header = cws_get_meta_option( 'fixed_header' );
		$class = $sb['layout_class'].' '. $sb['sb_class'];
		$sb['sb_class'] = apply_filters('cws_print_single_class', $class);

		$p_id = get_queried_object_id ();
		$post_meta = get_post_meta( get_the_ID(), 'cws_mb_post' );
		$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
		$def_row_fw_atts = array(
						'full_width'				=> false,
					);
		$shot = isset( $GLOBALS['cws_row_atts'] ) ? $GLOBALS['cws_row_atts'] : $def_row_fw_atts;
		extract($shot);
		extract( wp_parse_args( $post_meta, array(
			'show_related' 		=> false,
			'rpo_title'			=> '',
			'rpo_cols'			=> '4',
			'carousel'			=> false,
			'img_size'			=> '1',
			'rpo_items_count'	=> get_option( 'posts_per_page' ),
		)));
		if ($full_width == 'stretch_row_content' || $full_width == 'stretch_row_content_no_spaces') {
			$full_width = true;
		}else{
			$full_width = '';
		} 
		$ajax_width = 1920;
		$show_related = isset( $post_meta['show_related'] ) ? $post_meta['show_related'] : false;
		$rpo_title = isset( $post_meta['rpo_title'] ) ? esc_html( $post_meta['rpo_title'] ) : "";
		$rpo_items_count = isset( $post_meta['rpo_items_count'] ) ? esc_textarea( $post_meta['rpo_items_count'] ) : esc_textarea( get_option( "posts_per_page" ) );
		$rpo_cols = isset( $post_meta['rpo_cols'] ) ? esc_textarea( $post_meta['rpo_cols'] ) : 4;
		$title = get_the_title();
		ob_start();
		cws_vc_shortcode_cws_portfolio_single_post_post_media ();
		$media = ob_get_clean();
		$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] : false;
		unset( $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] );
		if ( $img_size == 2 ) {
			echo $media;
		}

		echo (isset($sb['content']) ? $sb['content'] : '');
		$GLOBALS['cws_vc_shortcode_single_ajax_atts'] = array(
			'sb_layout'						=> $sb_layout_class,
			'display_style'					=> 'showcase',
		);
		$pid = get_the_id();
		echo "<div id='cws_portfolio_post_{$pid}' class='cws_portfolio_post post_single clearfix'>";
			ob_start();
			cws_vc_shortcode_cws_portfolio_single_post_post_media (false,$ajax_width);
			$media = ob_get_clean();
			$floated_media = isset( $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] ) ? $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] : false;
			unset( $GLOBALS['cws_vc_shortcode_cws_portfolio_single_post_floated_media'] );
			if ( $img_size == 1 ) {
				if ( $floated_media ){
					echo "<div class='floated_media cws_portfolio_floated_media single_post_floated_media'>";
						echo "<div class='floated_media_wrapper cws_portfolio_floated_media_wrapper single_post_floated_media_wrapper'>";
							echo $media;
						echo "</div>";
					echo "</div>";						
				}
				else{
					echo $media;
				}
			}
			ob_start();
			cws_vc_shortcode_cws_portfolio_single_post_terms ();
			cws_vc_shortcode_cws_portfolio_single_post_content ();
			$content_terms = ob_get_clean();
			echo "<div class='container'>";
				if ( !empty( $content_terms ) ){
					if ( $floated_media && $img_size == 1 ){
						echo "<div class='clearfix floated_media_content cws_portfolio_single_content'>";
							echo $content_terms;
						echo "</div>";
					}
					else{
						echo "<div class='cws_portfolio_single_content'>";
							echo $content_terms;
						echo "</div>";
					}
				}

				if ( wp_get_referer() )
				{
					$previous = wp_get_referer();
					echo "<div class='back_link_case'><a href='$previous'><i class='fa fa-long-arrow-left'></i>" . esc_html__('Back to selected work' , 'cws-essentials') . "</a></div>";
				}
			echo "</div>";
			global $aasana_theme_funcs;
			$aasana_theme_funcs->cws_page_links ();
		echo "</div>";
		wp_reset_postdata();
		unset( $GLOBALS['cws_vc_shortcode_single_post_atts'] );
		echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : '';
		if ( $show_related ){
			$terms = wp_get_post_terms( $p_id, 'cws_portfolio_cat' );
			$term_slugs = array();
			for ( $i=0; $i < count( $terms ); $i++ ){
				$term = $terms[$i];
				$term_slug = $term->slug;
				array_push( $term_slugs, $term_slug );
			}
			$term_slugs = implode( ",", $term_slugs );
			if ( !empty( $term_slugs ) ){
				$rp_args = array(
					'title'							=> $rpo_title,
					'post_type'						=> 'cws_portfolio',
					'total_items_count'				=> $rpo_items_count,
					'display_style'					=> 'carousel',
					'cws_portfolio_layout_override'	=> true,
					'cws_portfolio_layout'			=> $rpo_cols,
					'tax'							=> 'cws_portfolio_cat',
					'terms'							=> $term_slugs,
					'addl_query_args'				=> array(
						'post__not_in'					=> array( $p_id ),
					),
				);
				$related_projects = cws_vc_shortcode_posts_grid( $rp_args );
				if ( !empty( $related_projects ) ){
					echo "<hr />";
					echo $related_projects;
				}
			}
		}
	endwhile;
	exit;
}
add_action ( 'wp_ajax_cws_vc_shortcode_single_portfolio_ajax_load', 'cws_vc_shortcode_single_portfolio_ajax_load' );
add_action ( 'wp_ajax_nopriv_cws_vc_shortcode_single_portfolio_ajax_load', 'cws_vc_shortcode_single_portfolio_ajax_load' );

add_action ( 'wp_ajax_cws_vc_shortcode_page_load', 'cws_vc_shortcode_page_load' );
add_action ( 'wp_ajax_nopriv_cws_vc_shortcode_page_load', 'cws_vc_shortcode_page_load' );

function cws_vc_shortcode_page_load(){
	$data = isset( $_POST['data'] ) ? $_POST['data'] : array();
	echo "<div class='cws_ajax_response'>";

	$sb = cws_vc_shortcode_render_sidebars( get_queried_object_id() );
	$fixed_header = cws_get_meta_option( 'fixed_header' );
	$class = $sb['layout_class'].' '. $sb['sb_class'];
	$sb['sb_class'] = apply_filters('cws_print_single_class', $class);

	echo '<div class="'.(isset($sb['sb_class']) ? $sb['sb_class'] : '').'">';
		echo (isset($sb['content']) ? $sb['content'] : ''); 
		echo '<main'.($fixed_header == '1' ? ' class="header_shadow"' : '').' >';
			echo apply_filters('the_content', get_post_field('post_content', 40));

				$is_blog = cws_get_meta_option( 'is_blog' ) == '1';
				if ( $is_blog ) get_template_part( 'content', 'blog' );
				comments_template();
		
		echo '</main>';
		echo (isset($sb['content']) && !empty($sb['content']) ) ? '</div>' : '';
	echo '</div>';
	echo "</div>";
	die();
}

if(!function_exists('cws_vc_shortcode_load_more')){
	function cws_vc_shortcode_load_more ( $paged = 1, $max_paged = PHP_INT_MAX ){
		$aligning = isset($GLOBALS['aligning_more'] ) ?  $GLOBALS['aligning_more']  : "";
	?>	
		<div class='aligning_more<?php echo !empty($aligning) ? " ".$aligning : " center"; ?>'>
		<a class="cws_button cws_vc_shortcode_load_more" href="#"><?php esc_html_e( "Load More", 'cws-essentials' ); ?></a>
		</div>
	<?php
	}
}

if(!function_exists('register_scripts')){
	function register_scripts (){
		$common_scripts = array(	
			'jquery-ajax-shortcode'					    => plugin_dir_url( __FILE__ ) . 'assets/js/ajax_plugin.js',
			'jquery-shortcode-velocity'					    => plugin_dir_url( __FILE__ ) . 'assets/js/velocity.min.js',
			'jquery-shortcode-velocity-ui'					    => plugin_dir_url( __FILE__ ) . 'assets/js/velocity.ui.min.js',
			);

		foreach ( $common_scripts as $handle => $src ) {
			wp_enqueue_script( $handle, $src, array( 'jquery' ), '', true );
		}
		wp_localize_script('jquery-ajax-shortcode', 'cws_vc_sh', array(
			'ajax_nonce' => wp_create_nonce('cws_vc_sh_nonce'),
		));	

		wp_register_style ( 'cws_front_css',  plugin_dir_url( __FILE__ ) . 'assets/css/main.css' );   
		wp_enqueue_style ( 'cws_front_css' );		
	}	
}
add_action( 'wp_enqueue_scripts', 'register_scripts' );

if(!function_exists('cws_vc_shortcode_loader_html')){
	function cws_vc_shortcode_loader_html ( $args = array() ){
		extract( wp_parse_args( $args, array(
			'holder_id'		=> '',
			'holder_class' 	=> '',
			'loader_id'		=> '',
			'loader_class'	=> ''
		)));
		$holder_class 	.= " cws_loader_holder";
		$loader_class 	.= " cws_loader";
		$holder_id		= esc_attr( $holder_id );
		$holder_class 	= esc_attr( trim( $holder_class ) );
		$loader_id		= esc_attr( $loader_id );
		$loader_class 	= esc_attr( trim( $loader_class ) );
		echo "<div " . ( !empty( $holder_id ) ? " id='$holder_id'" : "" ) . " class='$holder_class'>";
			echo "<div " . ( !empty( $loader_id ) ? " id='$loader_id'" : "" ) . " class='$loader_class'>";
				?>
				<svg width='104px' height='104px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(0 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(30 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.08333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(60 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.16666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(90 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.25s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(120 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.3333333333333333s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(150 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.4166666666666667s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(180 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(210 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5833333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(240 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.6666666666666666s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(270 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.75s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(300 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.8333333333333334s' repeatCount='indefinite'/></rect><rect  x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(330 50 50) translate(0 -30)'>  <animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.9166666666666666s' repeatCount='indefinite'/></rect></svg>
				<?php
			echo "</div>";
		echo "</div>";
	}	
}

if(!function_exists('cws_vc_shortcode_pagination')){
	function cws_vc_shortcode_pagination ( $paged=1, $max_paged=1, $dynamic = true ){
		$is_rtl = is_rtl();

		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts	= explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$permalink_structure = get_option('permalink_structure');

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = $permalink_structure ? trailingslashit( $pagenum_link ) . '%_%' : trailingslashit( $pagenum_link ) . '?%_%';
		$pagenum_link = add_query_arg( $query_args, $pagenum_link );

		$format  = $permalink_structure && preg_match( '#^/*index.php#', $permalink_structure ) && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $permalink_structure ? user_trailingslashit( 'page/%#%', 'paged' ) : 'paged=%#%';
		?>

		<div class="pagination<?php echo $dynamic ? ' dynamic' : ''; ?>">
			<div class='page_links'>
			<?php
			$pagination_args = array( 'base' => $pagenum_link,
				'format' => $format,
				'current' => $paged,
				'total' => $max_paged,
				"prev_text" => "<i class='fa fa-angle-" . ( $is_rtl ? "right" : "left" ) . "'></i>",
				"next_text" => "<i class='fa fa-angle-" . ( $is_rtl ? "left" : "right" ) . "'></i>",
				"link_before" => '',
				"link_after" => '',
				"before" => '',
				"after" => '',
				"mid_size" => 2,
			);
			$pagination = paginate_links($pagination_args);
			print $pagination;
			?>
			</div>
		</div>
		<?php

	}
}

if(!function_exists('cws_vc_shortcode_render_sidebars')){
	function cws_vc_shortcode_render_sidebars($pid) {
		// !!! this must be in superclass
		$out = '';
		$sb = cws_vc_shortcode_get_sidebars( $pid );

		$layout_class = $sb && $sb['layout_class'] != 'none' ? $sb['layout_class'].'_sidebar' : '';
		$sb1_class = $sb && $sb['layout'] == 'right' ? 'sb_right' : 'sb_left';
		$sbl = $sb['sbl'];
		if ( $sbl ){
			$out .= '<div class="container">';
			if ( !empty($sb['sb1']) ) {
				$out .= sprintf('<aside class="%s">', sanitize_html_class($sb1_class));
				ob_start();
					dynamic_sidebar( $sb['sb1'] );
				$out .= ob_get_clean();
				$out .= '</aside>';
			}
			if ( !empty($sb['sb2']) ){
				$out .= '<aside class="sb_right">';
				ob_start();
					dynamic_sidebar( $sb['sb2'] );
				$out .= ob_get_clean();
				$out .= '</aside>';
			}
		}
		return array(
			'layout_class' => $layout_class,
			'sb_class' => $sb1_class,
			'content' => $out,
		);
	}
}

if(!function_exists('cws_vc_shortcode_get_option')){
	function cws_vc_shortcode_get_option($name){
		$ret = null;
		if (is_customize_preview()) {
			global $cwsfw_settings;
			if (isset($cwsfw_settings[$name])) {
				$ret = $cwsfw_settings[$name];
				if (is_array($ret)) {
					$theme_options = get_option( THEME_SLUG );
					if (isset($theme_options[$name])) {
						$to = $theme_options[$name];
						foreach ($ret as $key => $value) {
							$to[$key] = $value;
						}
						$ret = $to;
					}
				}
				return $ret;
			}
		}
		$theme_options = get_option( THEME_SLUG );
		$ret = isset($theme_options[$name]) ? $theme_options[$name] : null;
		$ret = stripslashes_deep( $ret );
		return $ret;
	}
}

if(!function_exists('cws_vc_shortcode_get_post_term_links_str')){
	function cws_vc_shortcode_get_post_term_links_str ( $tax = "", $delim = "" ){
		$pid = get_the_id();
		$terms_arr = wp_get_post_terms( $pid, $tax );
		$terms = "";
		if ( is_wp_error( $terms_arr ) ){
			return $terms;
		}
		for( $i = 0; $i < count( $terms_arr ); $i++ ){
			$term_obj	= $terms_arr[$i];
			$term_slug	= $term_obj->slug;
			$term_name	= esc_html( $term_obj->name );
			$term_link	= esc_url( get_term_link( $term_slug, $tax ) );
			$terms		.= "<a href='$term_link'>$term_name</a>" . ( $i < ( count( $terms_arr ) - 1 ) ? $delim : "" );
		}
		return $terms;
	}
}

if(!function_exists('cws_print_img_html')){
	function cws_print_img_html($img, $img_args, &$img_height = null) {
		$src = '';
		$img_h = 0;
		if ($img && !is_array($img) ) {
			$attach = wp_get_attachment_image_src( $img, 'full' );
			if ($attach) {
				list($src, $width, $height) = $attach;
				$img = array('src'=> $src, 'width' => $width, 'height' => $height, 'is_high_dpi' => '1');
			} else {
				return $src;
			}
		} else if ($img && !isset($img['is_high_dpi'] ) ) {
			$img['is_high_dpi'] = '1';
		} else if (empty($img['width']) && empty($img['height'])) {
			$attach = wp_get_attachment_image_src( $img['id'], 'full' );
			if ($attach) {
				list($src, $width, $height) = $attach;
				$img['width'] = $width;
				$img['height'] = $height;
			}
		}

		$is_high_dpi = (isset($img['is_high_dpi']) && $img['is_high_dpi'] == '1');

		if ( $is_high_dpi ) {
			if ( empty($img_args['width']) && empty($img_args['height']) ) {
				if (isset($img['width']) && isset($img['height'])) {
					$img_args = array(
						'width' => floor( (int) $img['width'] / 2 ),
						'height' => floor( (int) $img['height'] / 2 ),
						'crop' => true,
						);
				}
			}
			$thumb_obj = cws_thumb( $img['src'],$img_args,false );
			if ($thumb_obj) {
				$img_h = !empty($img_args["height"]) ? $img_args["height"] : '';
				$thumb_path_hdpi = !empty($thumb_obj[3]) ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_attr( $thumb_obj[3] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
				$src = $thumb_path_hdpi;
			}
		} else {
			$img_h = $img['height'];
			$src = " src='".esc_url( $img['src'] )."' data-no-retina";
		}
		if ($img_height) {
			$img_height = $img_h;
		}
		return $src;
	}
}

if(!function_exists('cws_get_meta_option')){
	function cws_get_meta_option($name = '', $check_first_key = false) {
		global $aasana_theme_funcs;
		$value = '';
		if(!empty($aasana_theme_funcs)){
			$value = isset($aasana_theme_funcs::$options[$name]) ? $aasana_theme_funcs::$options[$name] : null;
			while (is_string($value) && '{' === mb_substr($value, 0, 1)) {
				$g_name = mb_substr($value, 1, -1);
				$value = isset($aasana_theme_funcs::$options[$g_name]) ? $aasana_theme_funcs::$options[$g_name] : null;
			}
			if ($check_first_key && is_array($value)) {
				// it's better to set $check_first_key specifically when there's a chance
				// like in case of sidebars processing
				// check if need to replace value with theme option array
				reset($value);
				$first_key = key($value);
				$val = $value[$first_key];
				if (is_string($val) && '{' === mb_substr($val, 0, 1)) {
					$g_name = mb_substr($val, 1, -1);
					$value = isset($aasana_theme_funcs::$options[$g_name]) ? $aasana_theme_funcs::$options[$g_name] : null;
				}
			}
		}

		return $value;
				
	}
}

if(!function_exists('cws_vc_shortcode_get_sidebars')){
	function cws_vc_shortcode_get_sidebars( $p_id = null ) { /*!*/
		$page_type = 'page';
		$sb = null;
		$post_type = get_post_type($p_id);
		if ($p_id && !is_home() ) {
			switch ($post_type) {
				case 'page':	
				$page_type = 'page';
				break;
				case 'post':
				case 'attachment':
				case 'cws_portfolio':
				case 'cws_staff':
				$page_type = 'post';
				break;
			}
		} else if (is_home()) {
			/* default home page have no ID */
			$page_type = 'home';
		}

		if (!$sb) {
			$sb = cws_get_meta_option("{$page_type}_sidebars", true);
		}

		if ($sb){
			$ret = $sb;

			$sb_enabled = isset($sb['layout']) && $sb['layout'] != 'none';
			$sbl = 0;
			if ($sb_enabled) {
				$sbl = (int)!empty($sb['sb1']) | ((int)!empty($sb['sb2'])*2);
			}
			$class = '';
			switch ($sbl) {
				case 1:
				case 2:
				$class = 'single';
				break;
				case 3:
				$class = 'double';
				break;
			}

			$ret['layout_class'] = $class;
			$ret['sbl'] = $sbl;
			return $ret;
		}

	}
}


require_once( CWS_SHORTCODES_PLUGIN_DIR . '/templates-modules/cws_sc_blog.php' );
require_once( CWS_SHORTCODES_PLUGIN_DIR . '/templates-modules/cws_sc_portfolio.php' );
require_once( CWS_SHORTCODES_PLUGIN_DIR . '/templates-modules/cws_sc_staff.php' );
require_once( CWS_SHORTCODES_PLUGIN_DIR . '/templates-modules/cws_sc_testimonials.php' );
require_once( CWS_SHORTCODES_PLUGIN_DIR . '/templates-modules/cws_sc_classes.php' );
require_once( CWS_SHORTCODES_PLUGIN_DIR . '/templates-modules/cws_sc_events.php' );

add_action('wp_ajax_cws_vc_shortcode_tribe_events_posts_grid', 'cws_vc_shortcode_tribe_events_posts_grid');
add_action( 'wp_ajax_nopriv_cws_vc_shortcode_tribe_events_posts_grid', 'cws_vc_shortcode_tribe_events_posts_grid' );

function cws_vc_shortcode_msg_box ( $atts = array(), $content = "" ){
	$body_font_options 		= cws_vc_shortcode_get_option( 'body_font' );
	$body_font_color 		= !empty($body_font_options['color']) ? esc_attr( $body_font_options['color'] ) : '';
	extract( shortcode_atts( array(
		'type'					=> '',
		'title'					=> '',
		'text'					=> '',
		'is_closable'			=> '',
		'customize'				=> '',
		'icon_lib'				=> '',
		'custom_fill_color'		=> '#e6eaed',
		'custom_font_color'		=> $body_font_color,
		'el_class'				=> ''
	), $atts));
	$out = "";
	$type 			= esc_html( $type );
	$is_closable 	= (bool)$is_closable;
	$customize 		= (bool)$customize;
	$icon_lib 		= esc_attr( $icon_lib );
	$icon 			= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$el_class 		= esc_attr( $el_class );
	$content 		= !empty( $text ) ? $text : $content;
	$section_id 	= uniqid( "cws_vc_shortcode_msg_box_" );
	ob_start();
	if ( $customize ){
		echo !empty( $custom_fill_color ) ? "background-color: $custom_fill_color;" : "";
		echo !empty( $custom_font_color ) ? "color: $custom_font_color;" : "";
	}
	$section_styles = ob_get_clean();
	$icon_class = "msg_icon";
	$icon_html = "";
	if ( $customize && !empty( $icon ) ){
		if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
			vc_icon_element_fonts_enqueue( $icon_lib );
		}
		if($icon_lib == 'cws_svg'){
			$svg_icon = json_decode(str_replace("``", "\"", $icon), true);
			$upload_dir = wp_upload_dir();
			$this_folder = $upload_dir['basedir'] . '/cws-svgicons/' . md5($svg_icon['collection']) . '/';				
			$icon_html .= '<i class="svg" style="width:'.$svg_icon['width'].'px;height:'.$svg_icon['height'].'px">'.file_get_contents($this_folder . $svg_icon['name']).'</i>';
		}else{
			$icon_class .= " $icon custom";
		}
		
	}
	if ( !empty( $title ) || !empty( $content ) ){
		$out .= "<div id='$section_id' class='cws_vc_shortcode_msg_box cws_vc_shortcode_module" . ( !empty( $type ) ? " $type" : "" ) . ( $is_closable ? " closable" : "" ) . ( !empty( $el_class ) ? " $el_class" : "" ) . "'" . ( !empty( $section_styles ) ? " style='$section_styles'" : "" ) . ">";
			$out .= "<div class='icon_part".($icon_lib == 'cws_svg' ? " svg_icon" : "")."'>";
				if($icon_lib == 'cws_svg'){
					$out .= $icon_html;
				}else{
					$out .= "<i class='$icon_class'></i>";
				}
				
			$out .= "</div>";
			$out .= "<div class='content_part'>";
				$out .= !empty( $title ) ? "<div class='title'>$title</div>" : "";
				$out .= !empty( $content ) ? "<p>$content</p>" : "";
			$out .= "</div>";
			$out .= $is_closable ? "<a class='close_button'></a>" : "";
		$out .= "</div>";
	}
	return $out;
}

add_shortcode( 'cws_sc_msg_box', 'cws_vc_shortcode_msg_box' );

add_shortcode( 'cws_sc_portfolio_posts_grid', 'cws_vc_shortcode_cws_portfolio_posts_grid' );

add_shortcode( 'cws_sc_classes_posts_grid', 'cws_vc_shortcode_cws_classes_posts_grid' );

add_shortcode( 'cws_sc_staff_posts_grid', 'cws_vc_shortcode_cws_staff_posts_grid' );

add_shortcode( 'cws_sc_testimonial_posts', 'cws_vc_shortcode_cws_testimonial_posts_grid' );

add_shortcode( 'cws_sc_events_posts_grid', 'cws_vc_shortcode_tribe_events_posts_grid' );

function cws_vc_shortcode_sc_vc_blog ( $atts = array(), $content = "" ){
	$post_type = "post";
	$def_blog_layout = cws_vc_shortcode_get_option( 'def_blog_layout' );
	$def_blog_layout = isset( $def_blog_layout ) ? $def_blog_layout : "";
	$def_chars_count = cws_vc_shortcode_get_option( 'def_blog_chars_count' );
	$def_chars_count = isset( $def_chars_count ) && is_numeric( $def_chars_count ) ? $def_chars_count : '200';
	$defaults = array(
		'title'						=> '',
		'title_align'				=> 'left',
		'total_items_count'			=> '',
		'layout'					=> $def_blog_layout,
		'post_hide_meta_override'	=> false,
		'post_hide_meta'			=> '',
		'chars_count'				=> $def_chars_count,
		'display_style'				=> 'grid',
		'items_pp'					=> esc_html( get_option( 'posts_per_page' ) ),
		'el_class'					=> '',
		'auto_play_carousel'        => '',
		'navigation_carousel'       => '',
		'hover_effects'				=> '',
		'pagination_carousel'       => '',
		'link_show'      			=> '',
		'pagination_grid'       	=> '',
		'aligning'      			=> 'center',
		'hover_effects_more_btn'    => 'style_1',
	);
	$proc_atts = shortcode_atts( $defaults, $atts );
	extract( $proc_atts );
	$out = "";
	$tax = isset( $atts[$post_type . '_tax'] ) ? $atts[$post_type . '_tax'] : '';
	$terms = isset( $atts["{$post_type}_{$tax}_terms"] ) ? $atts["{$post_type}_{$tax}_terms"] : "";
	$proc_atts = array_merge( $proc_atts, array(
		'post_hide_meta_override'				=> $post_hide_meta_override,
		'post_hide_meta'						=> $post_hide_meta,
		'tax'									=> $tax,
		'terms'									=> $terms
	));
	$out .= function_exists( "cws_sc_blog" ) ? cws_sc_blog( $proc_atts ) : "";
	return $out;
}
add_shortcode( 'cws_sc_vc_blog', 'cws_vc_shortcode_sc_vc_blog' );
add_shortcode( 'cws_sc_blog', 'cws_sc_blog' );


function cws_vc_shortcode_carousel ( $atts, $content ){
	extract( shortcode_atts( array(
		'title' => '',
		'columns' => '1',
		'bullets_nav' => '',
		'arrows_nav' => '',
		'custom_pagination_color' => '',
		'custom_arrow_color' 	=> '',
		'margins_navigation' 	=> '',
		'margins_pagination' 	=> '',
	), $atts));
	$has_title = !empty( $title );
	$section_class = "cws_vc_shortcode_sc_carousel cws_sc_carousel cws_vc_shortcode_module";
	$section_class .= !empty($has_title) ? " has_title" : "";
	$section_class .= !empty($arrows_nav) ? " arrows_nav" : "";
	$section_class .= !empty($bullets_nav) ? " bullets_nav" : "";
	$section_atts = " data-columns='$columns'";
	$out = "";
	$module_id 			= uniqid( "cws_vc_shortcode_carousel_" );
	ob_start();
	if ( $custom_pagination_color ){	
		echo "#{$module_id} .owl-pagination .owl-page,#{$module_id} .owl-pagination .owl-page.active{
			    -webkit-box-shadow: 0px 0px 0px 3px {$custom_pagination_color};
    			-moz-box-shadow: 0px 0px 0px 3px {$custom_pagination_color};
   				box-shadow: 0px 0px 0px 3px {$custom_pagination_color};
			} ";	
		echo "#{$module_id} .owl-pagination .owl-page,#{$module_id}  .owl-pagination .owl-page.active:before{
   				background-color: {$custom_pagination_color};
			} ";	
	}
	if($custom_arrow_color){
		echo "#{$module_id} .carousel_nav_panel .prev,#{$module_id} .carousel_nav_panel .next{
   			border-color: {$custom_arrow_color};
   			opacity: 1;
		} ";	
		echo "#{$module_id} .carousel_nav_panel .prev:before,#{$module_id} .carousel_nav_panel .next:before{
   			color: {$custom_arrow_color};
		} ";
		echo "#{$module_id} .carousel_nav_panel .prev:after,#{$module_id} .carousel_nav_panel .next:after{
   			background: {$custom_arrow_color};
		} ";
	}
	if(!empty($margins_navigation)){
		echo "#{$module_id} .carousel_nav_panel .prev,#{$module_id} .carousel_nav_panel .next{
   			margin: {$margins_navigation};
		} ";
	}	
	if(!empty($margins_pagination)){
		echo "#{$module_id} .owl-controls{
   			margin: {$margins_pagination};
		} ";
	}
	$styles = ob_get_clean();
	if ( !empty( $content ) ){
		$out .= "<div id='{$module_id}'  class='$section_class'" . ( !empty( $section_atts ) ? $section_atts : "" ) . ">";
			if ( !empty( $styles ) ){
				/*echo "<style id='carousel_{$module_id}' type='text/css'>";
					echo $styles;
				echo "</style>";*/
				Cws_shortcode_css()->enqueue_cws_css($styles);
			}
			if ( !empty($arrows_nav) ){
				$out .= "<div class='cws_vc_shortcode_sc_carousel_header clearfix'>";
					if($has_title){
						$out .= "<h4>$title</h4>";
					}
					$out .= "<div class='carousel_nav_panel'>";
						$out .= "<span class='prev'></span>";
						$out .= "<span class='next'></span>";
					$out .= "</div>";				
				$out .= "</div>";
			}
			$out .= "<div class='cws_vc_shortcode_wrapper cws_wrapper'>";
				$out .= do_shortcode( $content );
			$out .= "</div>";
		$out .= "</div>";
	}
	wp_enqueue_script( 'owl_carousel' );
	return $out;
}
add_shortcode( 'cws_sc_carousel', 'cws_vc_shortcode_carousel' );

function cws_vc_shortcode_sc_icon ( $atts = array(), $content = "" ){
	$theme_color 			= esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	extract( shortcode_atts( array(
		"icon_lib"			=> "",
		"url"				=> "",
		"new_tab"			=> "",
		"title"				=> "",
		"type"				=> "simple",
		"shape"				=> "square",
		"size"				=> "2x",
		"aligning"			=> "",
		"customize_size"	=> "",
		"size_i"			=> "",
		"add_hover"			=> "",
		"customize_colors"	=> "",
		"size_b"			=> "2px",
		"fill_color"		=> "#fff",
		"font_color"		=> $theme_color,
		"el_class"			=> "",
		"hover_i_style"		=> "style_1",
	), $atts));

	$out = "";
	$icon_lib 			= esc_attr( $icon_lib );
	$icon 				= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$icon 				= esc_attr( $icon );
	$size_b 			= esc_attr( $size_b );
	$size_b 			= (int)$size_b."px";
	$url  				= esc_url( $url );
	$new_tab			= (bool)$new_tab;
	$title 				= esc_html( $title );
	$type 				= esc_html( $type );
	$shape				= esc_html( $shape );
	$size				= esc_html( $size );
	$aligning			= esc_html( $aligning );
	$add_hover			= (bool)$add_hover;
	$customize_colors	= (bool)$customize_colors;
	$fill_color			= esc_html( $fill_color );
	$font_color			= esc_html( $font_color );
	$el_class			= esc_attr( $el_class );
	if ( empty( $icon ) ) return $out;
	if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
		vc_icon_element_fonts_enqueue( $icon_lib );
	}
	$icon_id = uniqid( "cws_vc_shortcode_icon_" );
	ob_start();	
	if ( $customize_colors && !empty( $fill_color ) && !empty( $font_color ) ){
		//echo "<style type='text/css'>";
			echo "#$icon_id{";
				if ( $type == "simple" ){
					echo "color: $font_color;";
				}
				else if ( $type == "bordered" ){
					if(!empty($add_hover) && $hover_i_style == 'style_1' ){
						echo "}";
						echo "#$icon_id:after{";
						echo "background-color: $fill_color;";
						echo "}#$icon_id{";
					}else{
						echo "background-color: $fill_color;";
					}
					echo "color: $font_color;";
					echo "border-color: $font_color;";
					echo "border-width: $size_b;";
				}
				else if ( $type == "alt" ){
					echo "background-color: $font_color;";
					echo "color: $fill_color;";
					echo "border-color: $font_color;";
				}
			echo "}";
			if ( $add_hover ){
				echo "#$icon_id.hovered:after{";
					if ( $type == "simple" ){
						echo "color: $font_color;";
					}
					else if ( $type == "bordered" ){
						echo "background-color: $fill_color;";
						echo "color: $font_color;";
						echo "border-color: $font_color;";
						echo "border-width: $size_b;";
					}
					else if ( $type == "alt" ){
						echo "background-color: $font_color;";
						echo "color: $fill_color;";
						echo "border-color: $font_color;";
					}
				echo "}";	
				echo "#$icon_id.hovered:hover{";
					if ( $type == "bordered" ){
						if($hover_i_style != 'style_1' && $hover_i_style != 'style_3'){
							echo "background-color: $font_color;";						
							echo "color: $fill_color;";
							echo "border-color: $font_color;";
						}
					}
					else if ( $type == "alt" ){
						if($hover_i_style != 'style_1'  && $hover_i_style != 'style_3'){
							echo "background-color: $fill_color;";
							echo "color: $font_color;";
							echo "border-color: $font_color;";
						}
					}
				echo "}";				
			}
		//echo "</style>";
		
	}
	$styles = ob_get_clean();
	if(!empty($styles)){
		Cws_shortcode_css()->enqueue_cws_css($styles);
	}
	
	$al_class = !empty( $aligning ) ? "align{$aligning}" : "";
	
	$wrapper_tag = "span";
	$wrapper_classes = "icon-wrapper";
	$wrapper_classes .= !empty( $styles ) ? " $al_class" : "";
	
	$tag = !empty( $url ) ? "a" : ($icon_lib == 'cws_svg' ? "span" : "i");
	$wrapper_tag_atts = $wrapper_tag;
	$wrapper_tag_atts .= " class='$wrapper_classes'";

	$classes =  $icon_lib !== 'cws_svg' ? "cws_vc_shortcode_icon $icon $type cws_vc_shortcode_icon_{$size}" : "cws_vc_shortcode_icon $type cws_vc_shortcode_icon_{$size}";
	$classes .= $type != "simple" ? " $shape" : "";
	$classes .= $add_hover ? " hovered" : "";
	$classes .= $add_hover ? " {$hover_i_style}" : "";
	$classes .= !empty( $al_class ) ? " $al_class" : "";
	$classes .= !empty( $el_class ) ? " $el_class" : "";
	
	$tag_atts = $tag == 'a' ? "$tag href='$url'" : $tag;
	$tag_atts .= " id='$icon_id'";
	$tag_atts .= " class='$classes'";
	$tag_atts .= !empty( $url ) && $new_tab ? " target='_blank'" : "";
	$tag_atts .= !empty( $title ) ? " title='$title'" : "";
	$tag_atts .= !empty( $customize_size ) && !empty($size_i) ? " style='font-size:".(int)$size_i."px'" : "";
	
	$out .= !empty( $styles ) ? "<$wrapper_tag_atts>" : "";
		$out .= "<$tag_atts>";
		if($icon_lib == 'cws_svg'){
			$svg_icon = json_decode(str_replace("``", "\"", $icon), true);
			$out .= function_exists('cwssvg_shortcode') ? cwssvg_shortcode($svg_icon) : "";			
		}
		$out .= "</$tag>";
	$out .= !empty( $styles ) ? "</$wrapper_tag>" : "";
	return $out;
}
add_shortcode( 'cws_sc_icon', 'cws_vc_shortcode_sc_icon' );

function cws_vc_shortcode_sc_button ( $atts = array(), $content = "" ){
	$theme_color = esc_attr( cws_vc_shortcode_get_option( "theme-main-one-color" ) );
	$theme_color_2 = esc_attr( cws_vc_shortcode_get_option( "theme-main-secondary-color" ) );
	extract( shortcode_atts( array(
		"title"					 => "",
		"url"					 => "",
		"new_tab"				 => "",
		"size"					 => "regular",
		"ofs"					 => "",
		"aligning"				 => "",
		"fw"					 => "",
		"icon_lib"				 => "",
		"icon_pos"				 => "right",
		"bordered"				 => "",
		"alt"					 => "",
		"customize_colors"		 => "",
		"add_hover"				 => "",
		"fill_color"			 => $theme_color,
		"font_color"			 => "#fff",
		"skew"					 => "",
		"disable_border"		 => "",
		"hovered_fill_color" 	 => "#fff",
		"customize_size_title"   => "",
		"size_t" 				 => "22px",
		"weight_t" 				 => "400",
		"hover_effects_more_btn" => "style_1",
		"hovered_font_color" 	 => $theme_color,
		"el_class"				 => ""
	), $atts));
	$out = "";
	$title 				= esc_html( $title );
	$url  				= esc_url( $url );
	$new_tab			= (bool)$new_tab;
	$disable_border		= (bool)$disable_border;
	$size 				= esc_html( $size );
	$ofs 				= esc_attr( $ofs );
	$aligning			= esc_html( $aligning );
	$fw					= (bool)$fw;
	$icon_lib 			= esc_attr( $icon_lib );
	$icon				= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$icon				= esc_attr( $icon );
	$bordered			= (bool)$bordered;
	$alt				= (bool)$alt;
	$add_hover			= (bool)$add_hover;
	$skew				= esc_html( $skew );
	$customize_colors	= (bool)$customize_colors;
	$customize_size_title	= (bool)$customize_size_title;
	$fill_color			= esc_attr( $fill_color );
	$font_color			= esc_attr( $font_color );
	$hovered_fill_color	= esc_attr( $hovered_fill_color );
	$hovered_font_color	= esc_attr( $hovered_font_color );
	$el_class			= esc_attr( $el_class );
	$size_t				= esc_attr( $size_t );
	$weight_t			= esc_attr( $weight_t );
	$hover_effects_more_btn		= esc_attr( $hover_effects_more_btn );
	$button_id = uniqid( "cws_vc_shortcode_button_" );
	$ofs_arr = explode( " ", $ofs );
	$ofs_left = $ofs_right = "";
	/* styles */
	ob_start();
	if ( !empty( $ofs_arr ) && isset( $ofs_arr[0] ) && !empty( $ofs_arr[0] ) ){
		echo "#$button_id{";
			if ( count( $ofs_arr ) > 1 ){
				$ofs_left = (int) $ofs_arr[0];
				$ofs_right = (int) $ofs_arr[1];
			}
			else{
				$ofs_left = (int) $ofs_right = (int) $ofs_arr[0];
			}
			echo "padding-left: $ofs_left".'px'.";";
			echo "padding-right: $ofs_right".'px'.";";
		echo "}";
	}
	if ( $customize_colors ){

		echo "#$button_id{";
			if ( $alt ){
				echo !empty( $hovered_fill_color ) ? "background-color:$hovered_fill_color;" : "";
				echo !empty( $hovered_fill_color ) ? "border-color:$hovered_fill_color;" : "";				
				echo !empty( $hovered_font_color ) ? "color:$hovered_font_color;" : "";
			}
			else{
				echo !empty( $fill_color ) ? "background-color:$fill_color;" : "";
				echo !empty( $fill_color ) ? "border-color:$fill_color;" : "";				
				echo !empty( $font_color ) ? "color:$font_color;" : "";
			}	
		echo "}";
		
		echo "#$button_id:before{";
			if ( $alt ){
				echo !empty( $hovered_font_color ) ? "border-color:$fill_color;" : "";	
			}
			else{
				echo !empty( $fill_color ) ? "border-color:$fill_color;" : "";
			}	
		echo "}";
	
	}	
	if(!empty($add_hover)){
		echo "#$button_id:hover{";
		if ( $alt ){
			echo !empty( $fill_color ) ? "background-color:$hovered_font_color;" : "";
			echo !empty( $fill_color ) ? "border-color:$hovered_font_color;" : "";				
			echo !empty( $font_color ) ? "color:$hovered_fill_color;" : "";
		}
		else{
			echo !empty( $hovered_fill_color ) ? "background-color:$hovered_fill_color;" : "";
			echo !empty( $hovered_fill_color ) ? "border-color:$hovered_font_color;" : "";				
			echo !empty( $hovered_font_color ) ? "color:$hovered_font_color;" : "";	
		}
		echo "}";

		echo "#$button_id:hover:before{";
		if ( $alt ){
			echo !empty( $fill_color ) ? "border-color:$fill_color;" : "";	
		}
		else{
			echo !empty( $hovered_fill_color ) ? "border-color:$fill_color;" : "";
		}	
		echo "}";			
	}
	if ( $customize_size_title ){

		echo "#$button_id{";
			if ( !empty($size_t) ){
				echo "font-size:".(int) $size_t."px;";
			}			
			if ( !empty($weight_t) ){
				echo "font-weight:{$weight_t};";
			}
			
		echo "}";
		
	
	}

	if(!empty($disable_border)){
		echo "#$button_id.cws_vc_shortcode_button:hover:before{border:0;}";	
		echo "#$button_id.cws_vc_shortcode_button:hover{border:0;}";	
		echo "#$button_id.cws_vc_shortcode_button{border:0;}";	
	}
	$styles = ob_get_clean();
	/* \styles */
	$al_class = !empty( $aligning ) ? " align{$aligning}" : "";
	$wrapper_tag = "div";
	$wrapper_classes = "cws_vc_shortcode_button_wrapper";
	$wrapper_classes .= !empty( $al_class ) ? $al_class : "";
	$wrapper_tag_atts = $wrapper_tag;
	$wrapper_tag_atts .= !empty( $wrapper_classes ) ? " class='$wrapper_classes'" : "";

	$tag = "a";
	$tag_atts = !empty( $url ) ? "$tag href='$url'" : $tag;
	$tag_atts .= " id='$button_id'";
	$classes = "cws_vc_shortcode_button $size";
	$classes .= $fw ? " fw" : "";
	$classes .= $bordered ? " bordered" : "";
	$classes .= $disable_border ? " dis_border" : "";
	$classes .= $alt ? " alt" : "";
	$classes .= $add_hover ? " add_hover" : "";
	$classes .= !empty($hover_effects_more_btn) ? " add_hover".$hover_effects_more_btn : "";
	$classes .= !empty( $skew ) ? " skew" : "";
	switch ( $skew ){
		case "left":
			$classes .= " skew_left";
			break;
		case "right":
			$classes .= " skew_right";
			break;
	}
	$classes .= !empty( $el_class ) ? " $el_class" : "";
	$tag_atts .= " class='$classes'";
	$tag_atts .= !empty( $url ) && $new_tab ? " target='_blank'" : "";

	Cws_shortcode_css()->enqueue_cws_css($styles);
	$out .= "<$wrapper_tag_atts>";
		//$out .= !empty( $styles ) ? "<style type='text/css'>$styles</style>" : "";
		$out .= "<$tag_atts>";
			$out .= !empty( $skew ) ? "<span class='cws_vc_shortcode_button_content'>" : "";
				if ( !empty( $icon ) && $icon_pos == 'left' ){
					if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
						vc_icon_element_fonts_enqueue( $icon_lib );
					}
					$out .= "<i class='$icon mr-10'></i>";
				}
				$out .= $title;
				if ( !empty( $icon ) && $icon_pos == 'right' ){
					if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
						vc_icon_element_fonts_enqueue( $icon_lib );
					}
					$out .= "<i class='$icon ml-10'></i>";
				}
			$out .= !empty( $skew ) ? "</span>" : "";		
		$out .= "</$tag>";
	$out .= "</$wrapper_tag>";
	return $out;
}
add_shortcode( 'cws_sc_button', 'cws_vc_shortcode_sc_button' );

function cws_vc_shortcode_sc_dropcap ( $atts = array(), $content = "" ){
	return "<span class='dropcap'>$content</span>";
}
add_shortcode( 'cws_sc_dropcap', 'cws_vc_shortcode_sc_dropcap' );

function cws_vc_shortcode_sc_mark ( $atts = array(), $content = "" ){
	$theme_color = esc_attr( cws_vc_shortcode_get_option( 'theme_color' ) );
	extract( shortcode_atts( array(
		'font_color'	=> '#fff',
		'bg_color'		=> $theme_color
	), $atts));
	return "<mark style='color: $font_color;background-color: $bg_color;'>$content</mark>";
}
add_shortcode( 'cws_sc_mark', 'cws_vc_shortcode_sc_mark' );

function cws_vc_shortcode_sc_embed ( $atts, $content ) {
	extract( shortcode_atts( array(
		'url' => '',
		'width' => '',
		'height' => ''
	), $atts));
	$url = esc_url( $url );
	return !empty( $url ) ? apply_filters( "the_content", "[embed" . ( !empty( $width ) && is_numeric( $width ) ? " width='$width'" : "" ) . ( !empty( $height ) && is_numeric( $height ) ? " height='$height'" : "" ) . "]" . $url . "[/embed]" ) : "";
}
add_shortcode( 'cws_sc_embed', 'cws_vc_shortcode_sc_embed' );

function cws_vc_shortcode_sc_call_to_action ( $atts = array(), $content = "" ){
	$theme_color 			= esc_attr( cws_vc_shortcode_get_option( "theme-first-color" ) );
	$theme_color_2 			= esc_attr( cws_vc_shortcode_get_option( "theme-second-color" ) );
	extract( shortcode_atts( array(
		"subtitle"			=> "",
		"title"				=> "",
		"desc_subtitle"		=> "",
		"icon_lib"			=> "",
		"add_button"		=> "",
		"add_banner"		=> "",
		"button_title"		=> "",
		"button_url"		=> "",
		"button_new_tab"	=> "",		
		"banner_new_tab"	=> "",		
		"banner_title"		=> "",
		"banner_price"		=> "",
		"banner_description"=> "",
		"banner_url"		=> "",
		"custom_styles"		=> "",
		"customize_colors"	=> "",
		"overlay_color"		=> "",
		"title_color"		=> "",
		"featured_color"	=> $theme_color_2,
		"display_color"		=> $theme_color_2,
		"display_font_color"=> $theme_color_2,
		"icon_color"		=> "rgba(255,255,255,0.5)",
		"el_class"			=> ""
	), $atts));

	$out = "";

	$custom_styles = esc_attr( $custom_styles );
	

	if ( is_plugin_active('js_composer/js_composer.php') ){
		$custom_css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $custom_styles, ' ' ), 'cws_sc_call_to_action', $atts );
	} else {
		$custom_css_class = '';
	}
	
	$bg_color 			= $theme_color;
	if ( !empty( $custom_styles ) ){
		$match = preg_match_all( "@background(-color)?:\s+((\#\w+)|(rgba\((\d|\,|\.)+\)))@", $custom_styles, $matches );
		if ( $match && isset( $matches[2][0] ) ){
			$bg_color = $matches[2][0];
		}
	}	

	$subtitle 			= esc_html( $subtitle );
	$desc_subtitle 			= esc_html( $desc_subtitle );
	$title 				= wp_kses( $title, array(
		"span"		=> array(),
		"mark"		=> array(),
		"strong"	=> array(),
		"b"			=> array(),
		"br"		=> array()
	));
	$icon_lib			= esc_attr ( $icon_lib );
	$icon 				= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$icon 				= esc_attr( $icon );
	$add_button 		= (bool)$add_button;
	$button_new_tab 	= (bool)$button_new_tab;
	$banner_new_tab 	= (bool)$banner_new_tab;
	$customize_colors	= (bool)$customize_colors;
	$featured_color		= esc_attr( $featured_color );
	$display_color		= esc_attr( $display_color );
	$display_font_color	= esc_attr( $display_font_color );
	$title_color		= esc_attr( $title_color );
	$icon_color			= esc_attr( $icon_color );
	$el_class			= esc_attr( $el_class );
	$module_id 			= uniqid( "cta_" );
	if ( empty( $subtitle ) && empty( $title ) && empty( $icon ) ){
		return $out;
	}
	$classes 	= "cws_vc_shortcode_cta cws_vc_shortcode_module";
	$classes	.= !empty( $el_class ) ? " $el_class" : "";
	$classes 	.= !empty( $custom_css_class ) ? " $custom_css_class" : "";
	$tag = "a";
	$styles = "";
	ob_start();
	echo $custom_styles;
	if ( $customize_colors ){
		echo "
		#{$module_id} .cta_subtitle,
		#{$module_id} .cta_desc_subtitle{
			color: $featured_color;
		}
		#{$module_id} .cta_title{
			color: $featured_color;
		}
		#{$module_id} .cta_icon{
			color: $icon_color;
		}
		#{$module_id} .cta_button .cws_vc_shortcode_button,#{$module_id} .cta_offer + .cta_banner .cws_vc_shortcode_cta_banner{
			color: $display_font_color;
		}
		#{$module_id} .cta_button .cws_vc_shortcode_button.skew:before,
		#{$module_id} .cta_button .cws_vc_shortcode_button.skew:after{
			border-color: $featured_color;
		}
		#{$module_id} .cta_button .cws_vc_shortcode_button.skew:hover:before{
			background-color: $featured_color;
		}
		#{$module_id} mark{
			color: $featured_color;
		}
		#{$module_id} .cws_vc_shortcode_button:before{
			border-color:$display_color;
		}
		#{$module_id} .cta_button .cws_vc_shortcode_button,#{$module_id} .cta_offer + .cta_banner .cws_vc_shortcode_cta_banner{
			background:$display_color;
		}

		
		";
	}
	$styles = ob_get_clean();

	$button_html = "";
	if ( $add_button && !empty( $button_title ) && !empty( $button_url ) ){
		$button_html = "<a".(!empty($button_new_tab) ? " target='_blank'" : "")." href='" . esc_url( $button_url ) . "' class='cws_vc_shortcode_button'><span class='cws_vc_shortcode_button_content'>" . esc_html( $button_title ) . "</span></a>";
	}	

	$banner_html = "";
	if ( $add_banner && !empty( $banner_title ) && !empty( $banner_url ) ){
		$banner_html = "<a".(!empty($banner_new_tab) ? " target='_blank'" : "")." href='" . esc_url( $banner_url ) . "' class='cws_vc_shortcode_cta_banner'>";
		$banner_html .= "<span class='cws_vc_shortcode_banner_title'>" . esc_html( $banner_title ) . "</span>";
		$banner_html .= !empty($banner_price) ? "<span class='cws_vc_shortcode_banner_price'>" . esc_html( $banner_price ) . "</span>" : "";
		$banner_html .= !empty($banner_description) ? "<span class='cws_vc_shortcode_banner_desc'>" . esc_html( $banner_description ) . "</span>" : "";
		$banner_html .= "</a>";
	}

	$text_content = "";
	$text_content .= !empty( $subtitle ) ? "<div class='cta_subtitle'>$subtitle</div>" : "";
	$text_content .= !empty( $title ) ? "<div class='cta_title'>$title</div>" : "";			
	$text_content .= !empty( $desc_subtitle ) ? "<div class='cta_desc_subtitle'>$desc_subtitle</div>" : "";			
	$out .= "<div id='$module_id' class='$classes'>";
		if(!empty($overlay_color) && !empty($customize_colors)){
			$out .= "<div class='overlay_cta_color' style='background:".$overlay_color."'></div>";
		}
		
		if ( !empty( $styles ) ){
			//echo "<style id='cta_{$module_id}' type='text/css'>";
			//	echo $styles;
			//echo "</style>";
			Cws_shortcode_css()->enqueue_cws_css($styles);
		}
		$out .= "<div class='cta_holder'>";
			if ( !empty( $icon ) || !empty( $text_content ) ){
				$out .= "<div class='cta_offer'>";
					$out .= !empty( $icon )	 ? "<div class='cta_icon'><i class='$icon'></i></div>" : "";
					$out .= !empty( $text_content ) ? "<div class='cta_text'>$text_content</div>" : "";
				$out .= "</div>";
			}
			$out .= !empty( $button_html ) ? "<div class='cta_button'>$button_html</div>" : "";
			$out .= !empty( $banner_html ) ? "<div class='cta_banner'>$banner_html</div>" : "";
		$out .= "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_call_to_action', 'cws_vc_shortcode_sc_call_to_action' );

function cws_vc_shortcode_sc_progress_bar ( $atts = array(), $content = "" ){
	$theme_color 			= esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	extract( shortcode_atts( array(
		'title'				=> '',
		'progress'			=> '',
		'use_custom_color'	=> '',
		'custom_fill_color'	=> $theme_color,
		'custom_title_color' => '#fff',
		'custom_percents_color' => $theme_color,
		'el_class'			=> ''
	), $atts));
	$title 				= esc_html( $title );
	$progress 			= esc_html( $progress );
	$use_custom_color 	= (bool)$use_custom_color;
	$custom_fill_color 	= esc_attr( $custom_fill_color );
	$el_class			= esc_attr( $el_class );
	$out = "";
	$out .= "<div class='cws_vc_shortcode_pb cws_vc_shortcode_module" . ( !empty( $el_class ) ? " $el_class" : "" ) . "'>";
		$out .= !empty( $title ) ? "<p class='cws_vc_shortcode_pb_title' style='" . ( $use_custom_color && !empty( $custom_title_color ) ? "color: $custom_title_color;" : "" ) . "'>$title</p>" : "";
		$out .= "<div class='pb_bar_title' style='" . ( $use_custom_color && !empty( $custom_percents_color ) ? "color: $custom_percents_color;" : "" ) . "'>";
		$out .= (int)$progress."%";
		$out .= "</div>";
		$out .= "<div class='cws_vc_shortcode_pb_bar'>";
			$out .= "<div class='cws_vc_shortcode_pb_progress' data-value='$progress' style='width:0%;" . ( $use_custom_color && !empty( $custom_fill_color ) ? "background-color: $custom_fill_color;" : "" ) . "'>";
			$out .= "</div>";
		$out .= "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_progress_bar', 'cws_vc_shortcode_sc_progress_bar' );

function cws_vc_shortcode_sc_milestone ( $atts = array(), $content = "" ){
	extract( shortcode_atts( array(
		'icon_lib'			=> '',
		'icon_pos'			=> 'left',
		'number'			=> '',
		'title'				=> '',
		'speed'				=> '',
		'module_alignment'	=> 'center',
		'text_alignment'	=> '',
		'custom_color_milestone'	=> '',
		'custom_color_m'	=> '',	
		'i_color_m'			=> '',	
		'milestone_img'		=> '',	
		'use_custom_color'	=> '',
		'size'				=> '',
		'custom_size_i'		=> '',
		'size_i'			=> '',
		'overlay_color'		=> '',
		'custom_color'		=> '',
		'paddings'			=> '',
		'desc'				=> '',
		'el_class'			=> ''
	), $atts));
	$icon_html = '';
	$icon_lib 			= esc_attr( $icon_lib );
	$icon_pos 			= esc_attr( $icon_pos );
	$icon 				= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$icon 				= esc_attr( $icon );
	$i_color_m 			= esc_attr( $i_color_m );
	$number				= esc_html( $number );
	$title 				= esc_html( $title );
	$desc 				= esc_html( $desc );
	$speed				= esc_html( $speed );
	$module_alignment 	= esc_attr( $module_alignment );
	$text_alignment 	= esc_attr( $text_alignment );
	$use_custom_color 	= (bool)$use_custom_color;
	$custom_color 		= esc_attr( $custom_color );
	$el_class			= esc_attr( $el_class );
	$overlay_color		= esc_attr( $overlay_color );
	$size				= esc_attr( $size );
	$custom_size_i		= esc_attr( $custom_size_i );
	$size_i 			= esc_html($size_i);
	$size_i 			= !empty($size_i) ? (int) $size_i : "";

	$out = $styles = "";
	$module_id 			= uniqid( "cws_vc_shortcode_milestone_" );

	wp_enqueue_script( 'odometer' );
	if ( !empty( $icon ) ){
		if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
			vc_icon_element_fonts_enqueue( $icon_lib );
		}
		if($icon_lib == 'cws_svg'){
			$svg_icon = json_decode(str_replace("``", "\"", $icon), true);
			$upload_dir = wp_upload_dir();
			$this_folder = $upload_dir['basedir'] . '/cws-svgicons/' . md5($svg_icon['collection']) . '/';				
			$icon_html .= '<i class="svg cws_vc_shortcode_milestone_icon" style="width:'.$svg_icon['width'].'px;height:'.$svg_icon['height'].'px">'.file_get_contents($this_folder . $svg_icon['name']).'</i>';
		}
	}
	if ( $use_custom_color && !empty( $custom_color ) ){
		$styles .= "
			#{$module_id}{
				color: $custom_color;
			}
			#{$module_id} .cws_vc_shortcode_milestone_number{
				color: inherit;
			}
		";
	}
	if ( $custom_color_milestone && !empty( $custom_color_m ) ){
		$styles .= "
			#{$module_id}{
				color: $custom_color_m;
			}
			#{$module_id} .cws_vc_shortcode_milestone_number{
				color: inherit;
			}
		";
	}	
	if ( $custom_color_milestone && !empty( $i_color_m ) ){
		$styles .= "
			#{$module_id} .cws_vc_shortcode_milestone_icon{
				color: $i_color_m;
			}
		";
	}
	if(!empty($paddings)){
		$styles .= "
			#{$module_id}{
				padding: $paddings;
			}
		";
	}
	if(!empty($custom_size_i) && !empty($size_i)){
		$styles .= "
			#{$module_id} .cws_vc_shortcode_milestone_icon{
				font-size: {$size_i}px;
			}
		";
	}

	$plan_img_data = array();
	$plan_img_src = "";
	$milestone_img_html = "";
	$milestone_overlay_html = "";
	if ( !empty( $milestone_img ) ){
		$plan_img_data = wp_get_attachment_image_src( $milestone_img, 'full' );
		if ( is_array( $plan_img_data ) ){
 			$plan_img_src = $plan_img_data[0];
 			//$src_img = cws_print_img_html(array('src' => $plan_img_src), array( 'width'=>270, 'height' => 270, 'crop' => true) );
 			$milestone_img_html = "background:url(".$plan_img_src.") no-repeat center center;";
		}
	}	
	if ( !empty( $overlay_color ) ){
 		$milestone_overlay_html .= "background-color:$overlay_color;";
		
	}
	$milestone_head = '';
	ob_start();
	if(!empty($milestone_img_html)){
		echo "<div class='milestone_prlx_section' style='".$milestone_img_html."'></div>";		
	}	
	if(!empty($milestone_overlay_html)){
		echo "<div class='milestone_overlay_section' style='".$milestone_overlay_html."'></div>";		
	}
	$milestone_head = ob_get_clean();	
	$classes = "cws_vc_shortcode_milestone cws_vc_shortcode_module";
	$classes .= !empty( $module_alignment ) ? " a-{$module_alignment}" : "" ;
	$classes .= !empty( $icon_pos ) ? " cws_vc_shortcode_milestone_icon_{$icon_pos}" : "";
	$classes .= !empty( $el_class ) ? " $el_class" : "" ;

	$out .= "<div id='$module_id' class='{$classes}'>";
		//$out .= !empty( $styles ) ? "<style id='{$module_id}_style' type='text/css'>$styles</style>" : "";
		!empty( $styles ) ? Cws_shortcode_css()->enqueue_cws_css($styles) : "";
		$out .= !empty($milestone_head) ? $milestone_head : "";
		$out .= "<div class='milestone_wrapper'>";
		$out .= "<div class='cws_vc_shortcode_milestone_wrapper" . ( !empty( $text_alignment ) ? " a-{$text_alignment}" : "" ) . "'>";
			if(!empty($icon)){
				if($icon_lib == 'cws_svg'){
					$out .= $icon_html;
				}else{
					$out .= "<div class='cws_vc_shortcode_milestone_icon".(!empty( $size ) ? " cws_vc_shortcode_icon_$size" : " cws_vc_shortcode_icon_3x")."'><i class='$icon'></i></div>";
				}					
			}

			$out .= "<div class='cws_vc_shortcode_milestone_data'>";
				$out .= "<div class='cws_vc_shortcode_milestone_number'" . ( !empty( $speed ) && is_numeric( $speed ) ? " data-speed='$speed'" : "" ) . ">$number</div>";
				$out .= !empty( $title ) ? "<h6 class='cws_vc_shortcode_milestone_title'>$title</h6>" : "";
				if($icon_pos == 'left' || $icon_pos == 'right'){
					if(!empty($desc)){
						$out .= "<div class='cws_vc_shortcode_milestone_desc'>";
							$out .= $desc;
						$out .= "</div>";				
					}
				}
			$out .= "</div>";
			if($icon_pos == 'center'){
				if(!empty($desc)){
					$out .= "<div class='cws_vc_shortcode_milestone_desc'>";
						$out .= $desc;
					$out .= "</div>";				
				}
			}

		$out .= "</div>";
		$out .= "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_milestone', 'cws_vc_shortcode_sc_milestone' );

function cws_vc_shortcode_sc_services ( $atts = array(), $content = "" ){
	$def_fill_color			= "rgba(255, 255, 255, 0.95)";
	$body_font_options		= cws_vc_shortcode_get_option( 'body_font' );
	$body_font_color		= empty($body_font_options) ? '' : esc_attr( $body_font_options['color'] );	
	$heading_font_options 	= cws_vc_shortcode_get_option( 'header_font' );
	$heading_font_color 	= empty($heading_font_options ) ? '' : esc_attr( $heading_font_options['color'] );	
	$theme_color 			= esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	$def_border_color 		= "rgba(255, 255, 255, 0.95)";	
	extract( shortcode_atts( array(
		'title'						=> '',
		'icon_lib'					=> '',
		'url'						=> '',
		'new_tab'					=> '',
		'alignment'					=> '',
		'paddings'					=> '',
		'title_paddings'			=> '',
		'divider'					=> '',
		'bg_img_id'					=> '',
		'customize_colors'			=> '',
		'hover_effects'				=> '',
		'size'						=> '',
		'plan_img'					=> '',
		'size_i'					=> '',
		'size_t'					=> '22px',
		'weight_t'					=> '700',
		'size_border'				=> '5px',
		'shape'						=> 'square',
		'customize_size'			=> '',
		'customize_size_title'			=> '',
		'add_hover'					=> '',
		'draw_border'				=> '',
		'hover_i_style'				=> 'style_1',
		'add_icon_hover'			=> '',
		'title_spacing'					=> '',
  		'hover_fill_color' 			=> "",
  		'custom_f_color'  			=> "",
  		'custom_icon_color'  		=> "#fff",
  		'custom_bg_color'  			=> $theme_color,
		'custom_fill_color'			=> $def_fill_color,
		'custom_font_color'			=> $body_font_color,
		'custom_title_color'		=> $heading_font_color,
		'custom_border_color'		=> $theme_color,
		'custom_selection_color'	=> $theme_color,
		'icon_color'				=> $theme_color,
		'bg_icon_color'				=> "",
		'el_class'					=> '',
		'custom_styles'				=> '',
		'add_animation'				=> '',
		'add_line_animation'		=> '',
	), $atts));

	$custom_styles = esc_attr( $custom_styles );
	$custom_css_class = "";
	$icon_html  = '';

	if ( is_plugin_active('js_composer/js_composer.php') ){
		$custom_css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $custom_styles, ' ' ), 'cws_sc_call_to_action', $atts );
	} else {
		$custom_css_class = '';
	}
			
	$title 					= esc_html( $title );
	$size_i 				= esc_html( $size_i );
	$size_t 				= esc_html( $size_t );
	$weight_t 				= esc_html( $weight_t );
	$icon_lib 				= esc_attr( $icon_lib );
	$icon 					= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$icon 					= esc_attr( $icon );
	$new_tab 				= $new_tab ? " target='_blank'" : "";
	$alignment				= esc_attr( $alignment );
	$paddings				= esc_attr( $paddings );
	$title_paddings			= esc_attr( $title_paddings );
	$title_spacing			= esc_attr( $title_spacing );
	$customize_size			= esc_attr( $customize_size );
	$customize_size_title			= esc_attr( $customize_size_title );
	$divider				= esc_attr( $divider );
	$customize_colors		= (bool)$customize_colors;
	$add_hover				= (bool)$add_hover;
	$draw_border			= (bool)$draw_border;
	$add_icon_hover			= (bool)$add_icon_hover;
	$custom_fill_color		= esc_attr( $custom_fill_color );
	$size_border			= esc_attr( $size_border );
	$custom_font_color		= esc_attr( $custom_font_color );
	$size					= !empty($size) ? esc_attr( $size ) : "3х";
	$custom_title_color		= esc_attr( $custom_title_color );
	$custom_border_color	= esc_attr( $custom_border_color );
	$icon_color				= esc_attr( $icon_color );
	$hover_effects			= esc_attr( $hover_effects );
	$shape					= esc_attr( $shape );
	$hover_i_style			= esc_attr( $hover_i_style );
	$bg_icon_color			= esc_attr( $bg_icon_color );
	$custom_selection_color	= esc_attr( $custom_selection_color );
	$hover_fill_color		= esc_attr( $hover_fill_color );
	$custom_f_color			= esc_attr( $custom_f_color );
	$custom_icon_color		= esc_attr( $custom_icon_color );
	$plan_img				= esc_attr( $plan_img );
	$custom_bg_color		= esc_attr( $custom_bg_color );
	$el_class				= esc_attr( $el_class );
	$add_animation			= esc_attr( $add_animation );
	$add_line_animation		= esc_attr( $add_line_animation );
	$content 				= apply_filters( 'the_content', $content );
	$out = $styles = "";
	if ( empty( $title ) && empty( $content ) && empty( $icon )) return $out;
	$module_id = uniqid( 'cws_service_item_' );
	if ( $customize_colors ){
		$styles .= "
			#{$module_id} .cws_service_item_wrapper{
				" . ( !empty( $custom_fill_color ) ? "background-color:$custom_fill_color;" : "" ) .  "
			}
			#{$module_id} .cws_service_item_wrapper{
				" . ( !empty( $custom_font_color ) ? "color:$custom_font_color;" : "" ) .  "
			}
			/* font color typography overriding */
				#{$module_id} .cws_service_item_wrapper input[type=\"checkbox\"],
				#{$module_id} .cws_service_item_wrapper input[type*=\"radio\"],
				#{$module_id} .cws_service_item_wrapper .owl-pagination .owl-page{
					border-color: $custom_font_color;
				}
				#{$module_id} .cws_service_item_wrapper .owl-pagination .owl-page,
				#{$module_id} .cws_service_item_wrapper .cws_inline_sep{
					background-color: $custom_font_color;
				}
				#{$module_id} .cws_service_item_wrapper .wp-playlist-light .wp-playlist-tracks{
					color: $custom_font_color;
				}			
			/* \\font color typography overriding */
			#{$module_id} .cws_service_item_wrapper .cws_service_title{
				" . ( !empty( $custom_title_color ) ? "color:$custom_title_color;" : "" ) .  "
			}
			/* heading font color typography overriding */
				#{$module_id} .cws_service_item_wrapper h1,
				#{$module_id} .cws_service_item_wrapper h2,
				#{$module_id} .cws_service_item_wrapper h3,
				#{$module_id} .cws_service_item_wrapper h4,
				#{$module_id} .cws_service_item_wrapper h5,
				#{$module_id} .cws_service_item_wrapper h6,
				#{$module_id} .cws_service_item_wrapper blockquote,
				#{$module_id} .cws_service_item_wrapper .wp-playlist-light .wp-playlist-tracks .wp-playlist-playing{
					" . ( !empty( $custom_title_color ) ? "color:$custom_title_color;" : "" ) .  "
				}
			/* \\heading font color typography overriding */		
			#{$module_id} .cws_service_item_wrapper .cws_textmodule_icon{
				" . ( !empty( $bg_icon_color ) ? "color:$bg_icon_color;" : "" ) .  "
			}
			
			#{$module_id} .cws_service_item_wrapper i{
				" . ( !empty( $icon_color ) ? "color:$icon_color;" : "" ) .  "			
			}

			#{$module_id} .cws_service_item_wrapper i svg
			{
				" . ( !empty( $icon_color ) ? "fill:$icon_color;" : "" ) .  "			
			}

			#{$module_id} .cws_searvice_icon_wrapper:after{
				" . ( !empty( $bg_icon_color ) ? "background-color:$bg_icon_color;" : "" ) .  "
			}
			#{$module_id} .divider > svg{
				" . ( !empty( $bg_icon_color ) ? "fill:$bg_icon_color;" : "" ) .  "
			}  
			/* theme color typography overriding */
				#{$module_id} .cws_service_item_wrapper ul.dot_style > li:before,
				#{$module_id} .cws_service_item_wrapper mark,
				#{$module_id} .cws_service_item_wrapper input[type=\"radio\"]:checked:before,
				#{$module_id} .cws_service_item_wrapper input[type='submit'],
				#{$module_id} .cws_service_item_wrapper button,
				#{$module_id} .cws_service_item_wrapper hr{
				" . ( !empty( $custom_selection_color ) ? "background-color:$custom_selection_color;" : "" ) .  "
				}
				#{$module_id} .cws_service_item_wrapper ul > li:before,
				#{$module_id} .cws_service_item_wrapper ul.custom_icon_style > li > .list_list,
				#{$module_id} .cws_service_item_wrapper a,
				#{$module_id} .cws_service_item_wrapper input[type=\"checkbox\"]:before,
				#{$module_id} .cws_service_item_wrapper .wp-playlist-light .wp-playlist-current-item,
				#{$module_id} .cws_service_item_wrapper .wp-playlist-light .wp-playlist-current-item .wp-playlist-caption{
				" . ( !empty( $custom_selection_color ) ? "color:$custom_selection_color;" : "" ) .  "
				}
				#{$module_id} .cws_service_item_wrapper input[type='submit'],
				#{$module_id} .cws_service_item_wrapper button,
				#{$module_id} .cws_service_item_wrapper .owl-pagination .owl-page.active{
				" . ( !empty( $custom_selection_color ) ? "border-color:$custom_selection_color;" : "" ) .  "
				}
				#{$module_id} .cws_service_item_wrapper blockquote,
				#{$module_id} .cws_service_item_wrapper abbr[title],
				#{$module_id} .cws_service_item_wrapper acronym[title]{
					" . ( !empty( $custom_selection_color ) ? "border-bottom-color:$custom_selection_color;" : "" ) .  "
				}
			/* \\theme color typography overriding */
		";
	}
	if(!empty($customize_size)){
		$styles .= "
			#{$module_id} .cws_searvice_icon_wrapper .cws_service_icon{
				" . ( !empty( $customize_size ) && !empty($size_i) ? "font-size: ".(int)$size_i ."px;" : "" ) .  "
			}
		";
	}	
	if(!empty($customize_size_title)){
		$styles .= "
			#{$module_id} .cws_service_title{
				" . ( !empty( $customize_size_title ) && !empty($size_t) ? "font-size: ".(int)$size_t ."px;" : "" ) .  "
				" . ( !empty( $customize_size_title ) && !empty($weight_t) ? "font-weight: ".$weight_t .";" : "" ) .  "
			}
		";
	}
			
	if(!empty($draw_border)){
		$styles .= "
			#{$module_id} .cws_searvice_icon_wrapper{
				" . ( !empty( $custom_border_color ) && !empty($draw_border) ? "box-shadow: 0 0 0 ".(!empty($size_border) ? (int)$size_border."px" : "5px")." $custom_border_color;" : "" ) .  "
			}

		";
		if ( !empty($custom_border_color) ){
			$styles .= "#{$module_id} .cws_service_item_wrapper .cws_service_icon{
				" . ( !empty( $custom_border_color ) && !empty($draw_border) ? "border-color:$custom_border_color;" : "" ) .  "
			}";	
		}
	}
	if ( !empty( $add_hover ) ){
		$styles .= "
			#{$module_id} .cws_service_item_wrapper:hover{
				" . ( !empty( $hover_fill_color ) ? "background-color:$hover_fill_color;" : "" ) .  "
			}
			#{$module_id} a.cws_service_item_wrapper:hover .cws_service_title,
			#{$module_id} .cws_service_item_wrapper:hover .cws_service_title{
				" . ( !empty( $custom_selection_color ) ? "color:$custom_selection_color;" : "" ) .  "
			}	
			#{$module_id} .cws_service_item_wrapper:hover .cws_service_desc{
				" . ( !empty( $custom_f_color ) ? "color:$custom_f_color;" : "" ) .  "
			}	
			#{$module_id} .cws_service_item_wrapper ul > li:before,
				#{$module_id} .cws_service_item_wrapper ul.custom_icon_style > li > .list_list,
				#{$module_id} .cws_service_item_wrapper a,
				#{$module_id} .cws_service_item_wrapper h1 > a:hover,
				#{$module_id} .cws_service_item_wrapper h2 > a:hover,
				#{$module_id} .cws_service_item_wrapper h3 > a:hover,
				#{$module_id} .cws_service_item_wrapper h4 > a:hover,
				#{$module_id} .cws_service_item_wrapper h5 > a:hover,
				#{$module_id} .cws_service_item_wrapper h6 > a:hover,
				#{$module_id} .cws_service_item_wrapper input[type=\"checkbox\"]:before,
				#{$module_id} .cws_service_item_wrapper input[type='submit']:hover,
				#{$module_id} .cws_service_item_wrapper button:hover,
				#{$module_id} .cws_service_item_wrapper .wp-playlist-light .wp-playlist-current-item,
				#{$module_id} .cws_service_item_wrapper .wp-playlist-light .wp-playlist-current-item .wp-playlist-caption{
				" . ( !empty( $custom_selection_color ) ? "color:$custom_selection_color;" : "" ) .  "
			}
		";		
	}
	if(!empty($add_icon_hover)){
		if($hover_i_style == 'style_1' || $hover_i_style == 'style_2'){
			$styles .= "
				#{$module_id} .cws_service_item_wrapper:hover .cws_searvice_icon_wrapper i{
					" . ( !empty( $custom_icon_color ) ? "color:$custom_icon_color;" : "" ) .  "
				}
			";			
		}
		if($hover_i_style == 'style_1'){
			$styles .= "
				#{$module_id} .cws_service_item_wrapper:hover .cws_searvice_icon_wrapper:after{
					" . ( !empty( $custom_bg_color ) ? "box-shadow:inset 0 0 0 65px $custom_bg_color;" : "" ) .  "
					" . ( !empty( $custom_bg_color ) ? "background:transparent;" : "" ) .  "
				}
			";			
		}
		if($hover_i_style == 'style_2' && $icon_lib != 'cws_svg' ){
			$styles .= "
				#{$module_id} .cws_service_item_wrapper:hover .cws_searvice_icon_wrapper:after{
					" . ( !empty( $custom_bg_color ) ? "background:$custom_bg_color;" : "" ) .  "
				}
			";		
		}			
	}	

	if ( !empty( $paddings ) ){
		$styles .= "
			#{$module_id} .cws_service_item_wrapper{
				" . ( !empty( $paddings ) ? "padding:$paddings;" : "" ) .  "
			}
		";		
	}	
	if ( !empty( $title_paddings ) ){
		$styles .= "
			#{$module_id} .cws_service_info{
				" . ( !empty( $title_paddings ) ? "padding:$title_paddings;" : "" ) .  "
			}
		";		
	}	
	if ( !empty( $title_spacing ) ){
		$styles .= "
			#{$module_id} .cws_service_title{
				" . ( !empty( $title_spacing ) ? "padding:$title_spacing;" : "" ) .  "
			}
		";		
	}

	if ( !empty( $bg_img_id ) ){
		$thumb_src_obj = wp_get_attachment_image_src( $bg_img_id, 'full' );
		$thumb_src = isset( $thumb_src_obj[0] ) ? $thumb_src_obj[0] : "";
		if ( !empty( $thumb_src ) ){
			$styles .= "
				#{$module_id}{
					background-image: url($thumb_src);
				}
			";	
		}
	}
	
	$wrapper_open_tag = !empty( $url ) ? "<a href='" . esc_url( $url ) . "' " . $new_tab . " class='cws_service_item_wrapper clearfix'>" : "<div class='cws_service_item_wrapper clearfix'>";
	$wrapper_close_tag = !empty( $url ) ? "</a>" : "</div>";
	ob_start();
	echo "<div id='$module_id' class='cws_service_item cws_vc_shortcode_module" . ( !empty( $alignment ) ? " a-{$alignment}" : "" ) . ( !empty( $divider ) ? " divider-{$divider}" : "" ) . ( !empty($hover_i_style) ? " {$hover_i_style}" : " style_1" ) . ( !empty($add_hover) ? " add_hover" : "" ) .( !empty($add_icon_hover) ? " add_icon_hover" : "" ) . ( !empty( $el_class ) ? " $el_class" : "" ) . ( !empty( $custom_css_class ) ? " $custom_css_class" : "" ) . "'>";
		if ( !empty( $styles ) ){
			Cws_shortcode_css()->enqueue_cws_css($styles);
		}
		echo $wrapper_open_tag;
			if ( !empty( $icon ) ){
				if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
					vc_icon_element_fonts_enqueue( $icon_lib );		
				}
				echo "<span class='cws_searvice_icon_wrapper cws_searvice_side'>";
					if($icon_lib == 'cws_svg'){
						$svg_icon = json_decode(str_replace("``", "\"", $icon), true);
						$upload_dir = wp_upload_dir();
						$this_folder = $upload_dir['basedir'] . '/cws-svgicons/' . md5($svg_icon['collection']) . '/';				
						$icon_html .= '<i class="svg '.(!empty($add_animation) ? " add_animation_icon" : "").(!empty($add_line_animation) ? " add_line_animation" : ""). '" style="width:'.$svg_icon['width'].'px;height:'.$svg_icon['height'].'px">'.file_get_contents($this_folder . $svg_icon['name']).'</i>';
					}else{
						$icon_html .= !empty( $icon ) ? "<i class='cws_service_icon cws_vc_shortcode_icon_{$size} " . esc_attr( $icon ) . "".(!empty($add_animation) ? ' add_animation_icon' : "").(!empty($add_line_animation) ? ' add_line_animation' : "")."'></i>" : "";
					}
					echo $icon_html;
				echo "</span>";
			}
			if(!empty($plan_img)){
				$plan_img_data = wp_get_attachment_image_src( $plan_img, 'full' );
				if ( is_array( $plan_img_data ) ){
		 			$plan_img_src = $plan_img_data[0];
		 			if($shape == 'square'){
		 				$src_img = cws_print_img_html(array('src' => $plan_img_src), array( 'height' => 120, 'width' => 120, 'crop' => true ) );
		 			}else{
		 				$src_img = cws_print_img_html(array('src' => $plan_img_src), array( 'height' => 150, 'width' => 150, 'crop' => true ) );
		 			}
		 			
		 			echo "<span class='cws_searvice_image_wrapper".($shape != 'square' ? " cws_searvice_image_circle" : "")." cws_searvice_side'>";
						echo "<img {$src_img} alt />";
					echo "</span>";			
				}
			}
			
			if ( !empty( $title ) || !empty( $content ) ){
				echo "<div class='cws_service_info'>";
					if ( !empty( $title ) ){
						echo "<h3 class='cws_service_title".(empty($title_paddings) ? " standard_spacing" : "")."'>";
							echo $title;
						echo "</h3>";
					}
					if ( !empty( $content ) ){
						echo "<div class='cws_service_desc clearfix'>";
							echo $content; 
						echo "</div>";
					}
				echo "</div>"; 
			}
		echo $wrapper_close_tag;
		if(!empty($divider)){
			echo "<div class='divider'>";
				echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 45"><path d="M11,0A2,2,0,0,0,9,2V43a2,2,0,0,0,4,0V2A2,2,0,0,0,11,0ZM2,10a2,2,0,0,0-2,2V33a2,2,0,0,0,4,0V12A2,2,0,0,0,2,10Zm17,0a2,2,0,0,0-2,2V33a2,2,0,0,0,4,0V12A2,2,0,0,0,19,10Z"/></svg>';			
			echo "</div>";
			if($divider === 'both'){
				echo "<div class='divider divider-right'>";
				echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 45"><path d="M11,0A2,2,0,0,0,9,2V43a2,2,0,0,0,4,0V2A2,2,0,0,0,11,0ZM2,10a2,2,0,0,0-2,2V33a2,2,0,0,0,4,0V12A2,2,0,0,0,2,10Zm17,0a2,2,0,0,0-2,2V33a2,2,0,0,0,4,0V12A2,2,0,0,0,19,10Z"/></svg>';			
				echo "</div>";
			}
		}

	echo "</div>";
	$out .= ob_get_clean();
	return $out;
}
add_shortcode( 'cws_sc_services', 'cws_vc_shortcode_sc_services' );

/******************** TESTIMONIAL ********************/

function cws_vc_shortcode_testimonial_renderer( $atts ) {
	extract( shortcode_atts( array(
		'thumbnail'		=> null,
		'quote'			=> '',
		'author_name'	=> '',
		'mark'			=> '',
		'url'   		=> '',
		'plan_img'      => null,
		'param_name'    => '',
		'custom_styles'	=> '',
		'customize_colors'=> '',
		'custom_author_color'=> '',
		'custom_title_color'=> '',
		'custom_qoute_color'=> '',
		'custom_overlay_color'=> '',
		'author_status'	=> '',
		'el_class'		=> ''
	), $atts));
	$quote        	=  $quote ;
	$author_name 	= esc_html( $author_name );
	$author_status	= esc_html( $author_status );
	$el_class    	= esc_attr( $el_class );
	$custom_styles = esc_attr( $custom_styles );
	$theme_color 			= esc_attr( cws_vc_shortcode_get_option( "theme_color" ) );
	$custom_css_class = "";

	if ( is_plugin_active('js_composer/js_composer.php') ){
		$custom_css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $custom_styles, ' ' ), 'cws_sc_vc_testimonial', $atts );
	} else {
		$custom_css_class = '';
	}
	
	$module_id = uniqid( 'cws_testimonial_item_' );
	$bg_color 			= $theme_color;
	if ( !empty( $custom_styles ) ){
		$match = preg_match_all( "@background(-color)?:\s+((\#\w+)|(rgba\((\d|\,|\.)+\)))@", $custom_styles, $matches );
		if ( $match && isset( $matches[2][0] ) ){
			$bg_color = $matches[2][0];
		}
	}	
	$styles = "";
	ob_start();
	echo $custom_styles;
	if ( $customize_colors ){
		if(!empty($custom_author_color)){
		echo "#{$module_id} .author_name{
			color:$custom_author_color;
			} ";
		}		
		if(!empty($custom_title_color)){
			echo "#{$module_id} .author_desc{
			color:$custom_title_color;
			} ";
		}		
		if(!empty($custom_qoute_color)){
			echo "#{$module_id} .testimonial-content{
			color:$custom_qoute_color;
			} ";
		}
		
	}
	$styles = ob_get_clean();

	ob_start();
	$author_section = $quote_section = '';
	if(!empty($plan_img) || !empty($thumbnail)){
		$author_section .= "<div class='wrapper-author'>";

		if ( !empty( $thumbnail )) {
			$author_section .= "<figure class='author'>";
				$src_img = cws_print_img_html(array('src' => wp_get_attachment_url( $thumbnail )), array( 'width' => 93, 'height' => 93, 'crop' => true ) );
				$author_section .= "<span class='thumb'><img {$src_img} alt = '" . get_post_meta( $thumbnail, '_wp_attachment_image_alt', true ) . "' /></span>";
			$author_section .= "</figure>";
		}
		$author_section .= "</div>";
	}


	$quote_section_class = "quote";
	$quote_section_atts = '';
	$quote_section_atts .= !empty( $quote_section_class ) ? " class='" . trim( $quote_section_class ) . "'" : '';
	if ( !empty( $quote ) ){
		$quote_section .= "<div" . ( !empty( $quote_section_atts ) ? $quote_section_atts : "" ) . ">";

			if ( !empty( $author_name ) || !empty( $author_status ) ){
				if(!empty($author_name)){
					$arr = explode(' ',trim($author_name));
					$arr_all = str_replace($arr[0], "", $author_name);
				}

				$quote_section .= "<div class='author_info_box'>";
					if(!empty($author_name)){
						$quote_section .= "<h5 class='author_name author_info'>";
						if(!empty($url)){
							$quote_section .= "<a href='".$url."'>";
						}
						$quote_section .= "<span>".$arr[0]."</span>". esc_html( $arr_all ) . "</h5>";
						if(!empty($url)){
							$quote_section .= "</a>";	
						}
					}

					$quote_section .= !empty( $author_status ) ? "<h6 class='author_desc author_info'>" . esc_html( $author_status ) . "</h6>" : "";
				$quote_section .= "</div>";
			}
			if ( !empty( $mark ) && is_numeric( $mark ) ){
				$mark_percents = floatval($mark)*20;
				$quote_section .= "<div class='pricing_plan_mark'>";
				$quote_section .= "<div class='cws_vc_shortcode_stars_wrapper'>";
				$quote_section .= "<div class='cws_vc_shortcode_inactive_stars cws_vc_shortcode_stars'>";
				$quote_section .= "</div>";
				$quote_section .= "<div class='cws_vc_shortcode_active_stars cws_vc_shortcode_stars' style='width:{$mark_percents}%;'>";
				$quote_section .= "</div>";
				$quote_section .= "</div>";
				$quote_section .= "</div>";
			}
			$quote_section .= "<div class='testimonial-content'>$quote</div>";
			
		$quote_section .= "</div>";
	}


	?>
	<div id='<?php echo $module_id;?>' class="testimonial cws_vc_shortcode_module clearfix <?php echo $thumbnail ? '' : 'without_image'; echo !empty( $el_class ) ? " $el_class" : ""; echo !empty($custom_css_class) ? $custom_css_class : ""; ?>">
		<?php		
		if( !empty($custom_overlay_color)) {
			echo "<div class='overlay_testimonial' style='background:".$custom_overlay_color."'></div>";
		}
		echo "<div class='container-testimonial'>";
		if ( !empty( $thumbnail ) ) {
			echo $author_section . $quote_section;
		}
		else{
			echo $quote_section;
		}	
		if ( !empty( $styles ) ){
			Cws_shortcode_css()->enqueue_cws_css($styles);
		}
		echo "</div>";
		?>
	</div>
	<?php
	return ob_get_clean();
}

function cws_vc_shortcode_quote_renderer( $atts ) {
	extract( shortcode_atts( array(
		'thumbnail'		=> null,
		'quote'			=> '',
		'author_name'	=> '',
		'author_status'	=> ''
	), $atts));
	$quote        	= esc_html( $quote );
	$author_name 	= esc_html( $author_name );
	$author_status	= esc_html( $author_status );
	ob_start();
	$author_section = $quote_section = '';


	if(!empty($thumbnail)){
		$thumbnail = has_post_thumbnail( ) ? wp_get_attachment_image_src( get_post_thumbnail_id( ),'full' ) : '';
		$thumbnail = $thumbnail[0];
	}


	$quote_section_class = "quote";
	$quote_section_atts = '';
	$quote_section_atts .= !empty( $quote_section_class ) ? " class='" . trim( $quote_section_class ) . "'" : '';

	if ( !empty( $quote ) ){
		$quote_section .= "<div" . ( !empty( $quote_section_atts ) ? $quote_section_atts : "" ) . ">";

			$quote_section .= "<div class='content-quote'>$quote</div>";			
			if ( !empty( $author_name ) || !empty( $author_status ) ){
				if(!empty($author_name)){
					$arr = explode(' ',trim($author_name));
					$arr_all = str_replace($arr[0], "", $author_name);
				}
				$quote_section .= "<div class='author_info_box-quote'>";
					
					$quote_section .= !empty( $author_status ) ? "<span class='author_status author_info'>" . esc_html( $author_status ) . "</span>" : "";
					$quote_section .= !empty( $author_name ) ? "<p class='author_name author_info'> - "."<span>".$arr[0]."</span>". esc_html( $arr_all ) . "</p>" : "";
				$quote_section .= "</div>";
			}
			$quote_section .= "<div class='quote_bg_c'></div>";
		$quote_section .= "</div>";
		if(!empty($thumbnail)){
		$quote_section .= '<div class="quote_bg" style="background-image: url('.esc_attr($thumbnail).');background-position: center center;"></div>';
		}

	}

	if(!empty($url)){
		$quote_section .= "<div class='link-testimonials'>";
		$quote_section .= "<a class='testimonial-button' href='".$url."'>".esc_html__('Read more', 'cws-essentials')."</a>";
		$quote_section .= "</div>";
	}

	?>
	<div class="cws_vc_shortcode_module clearfix <?php echo $thumbnail ? '' : 'without_image'; echo !empty( $el_class ) ? " $el_class" : ""; ?>">
		<?php
		if ( !empty( $thumbnail ) ) {
			echo $author_section . $quote_section;
		}
		else{
			echo $quote_section;
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

/******************** \TESTIMONIAL ********************/


function cws_vc_shortcode_sc_vc_testimonial ( $atts = array(), $content = "" ){
	// $atts['thumbnail'] = isset( $atts['thumbnail'] ) && !empty( $atts['thumbnail'] ) ? wp_get_attachment_url( $atts['thumbnail'] ) : "";
	return  function_exists( 'cws_vc_shortcode_testimonial_renderer' ) ? cws_vc_shortcode_testimonial_renderer( $atts, $content ) : '';
}
add_shortcode( 'cws_sc_vc_testimonial', 'cws_vc_shortcode_sc_vc_testimonial' );
function cws_vc_shortcode_sc_testimonial ( $atts = array(), $content = "" ){
	if ( !empty( $atts['thumbnail'] ) ){
		$thumbnail_data = json_decode( $atts['thumbnail'], true );
		$atts['thumbnail'] = ( isset( $thumbnail_data['@'] ) && isset( $thumbnail_data['@']['src'] ) ) ? $thumbnail_data['@']['src'] : "";
	}
	return function_exists( 'cws_vc_shortcode_testimonial_renderer' ) ? cws_vc_shortcode_testimonial_renderer( $atts, $content ) : '';
}
add_shortcode( 'cws_sc_testimonial', 'cws_vc_shortcode_sc_testimonial' );

function cws_vc_shortcode_sc_pricing_plan ( $atts = array(), $content = "" ){
	$theme_color = esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	$theme_secondary_color = esc_attr( cws_vc_shortcode_get_option( 'theme-main-secondary-color' ) );
	extract( shortcode_atts( array(
		'title'				=> '',
		'plan_img'			=> '',
		'currency'			=> '',
		'price'				=> '59.99',
		'price_desc'		=> '',
		'add_button'		=> '',
		'add_hover'			=> '',
		'button_text'		=> '',
		'button_url'		=> '',
		'button_new_tab'	=> '',
		'highlighted'		=> '',
		'use_custom_color'	=> '',
		'custom_color'		=> $theme_color,
		'main_color'		=> $theme_secondary_color,
		'btn_font_color'		=> "#fff",
		'el_class'			=> ''
 	), $atts));
 	
 	$title 				= wp_kses( $title, array(
 							'span'		=> array(),
 							'mark' 		=> array(),
 							'b'			=> array(),
 							'strong'	=> array(),
 							'br'		=> array()
 						), $title );
 	$plan_img 			= esc_html( $plan_img );
 	$currency 			= esc_html( $currency );
 	$price 				= esc_html( $price );
 	$price_desc 		= esc_html( $price_desc );
 	$add_button 		= (bool)$add_button;
 	$button_text 		= esc_html( $button_text );
 	$button_url 		= esc_url( $button_url );
 	$button_new_tab 	= (bool)$button_new_tab;
 	$highlighted		= (bool)$highlighted;
 	$use_custom_color 	= (bool)$use_custom_color;
 	$custom_color 		= esc_attr( $custom_color );
 	$main_color 		= esc_attr( $main_color );
 	$btn_font_color 	= esc_attr( $btn_font_color );
 	$add_hover 			= (bool)$add_hover;
 	$el_class			= esc_attr( $el_class );
 	$out = "";
	$section_id = uniqid( 'cws_vc_shortcode_pricing_plan_' );
	$plan_img_data = array();
	$plan_img_src = "";
	$plan_img_dims = array( 'height' => 216 );
	$plan_img_html = "";
	if ( !empty( $plan_img ) ){
		$plan_img_data = wp_get_attachment_image_src( $plan_img, 'full' );
		if ( is_array( $plan_img_data ) ){
 			$plan_img_src = $plan_img_data[0];
 			$plan_img_thumb_data = cws_thumb( $plan_img_src, $plan_img_dims, false );
 			$plan_img_thumb_src = isset( $plan_img_thumb_data[0] ) ? $plan_img_thumb_data[0] : "";
 			$retina_thumb = isset( $plan_img_thumb_data[3] ) ? $plan_img_thumb_data[3] : false;

 			if ( !empty( $plan_img_thumb_src ) ){
 				$plan_img_html .= "<img src='".esc_url($plan_img_thumb_src)."' alt='".$title."' class='pricing_plan_img'" . ( !empty( $retina_thumb ) ? " data-at2x='".esc_url($retina_thumb)."'" : " data-no-retina" ) . " />";
 			}
		}
	}
	ob_start();
	echo "<div class='pricing_plan_prlx_section'>";
		echo $plan_img_html;
	echo "</div>";
 	if ( !empty( $price ) ){
		preg_match( "/(\.|,)(\d+)$/", $price, $matches );
		$fract_price_part = isset( $matches[2] ) ? $matches[2] : '';
		$main_price_part = !empty( $fract_price_part ) ? esc_html( mb_substr( $price, 0, strpos( $price, $fract_price_part ) ) ) : esc_html( $price ); 		
 		echo "<div class='pricing_plan_price_wrapper'>";
	 		echo "<div class='pricing_plan_price'>";
	 			echo "<span class='price'>";
		 			echo !empty( $currency ) ? "<span class='currency'>$currency</span>" : "";
		 			echo "<span class='main_price_part'>$main_price_part</span>";
		 			echo !empty( $fract_price_part ) ? "<span class='fract_price_part'>$fract_price_part</span>" : "";
	 			echo "</span>";
	 			echo !empty( $price_desc ) ? "<span class='price_desc'>$price_desc</span>" : "";
	 		echo "</div>";
	 	echo "</div>";
	 	echo !empty( $title ) ? "<h3 class='pricing_plan_title'>$title</h3>" : "";
 	}
	$plan_head = ob_get_clean();
 	ob_start();
	$content = apply_filters( 'the_content', $content );
	echo !empty( $content ) ? "<div class='pricing_plan_content'>$content</div>" : "";
 	$plan_body = ob_get_clean();
 	ob_start();
 	if ( !empty( $plan_head ) ){
 		echo "<div class='pricing_plan_head'>";
 			echo "<div class='pricing_plan_head_wrapper'>";
 				echo $plan_head;
 			echo "</div>";
 		echo "</div>";
 	}
 	if ( !empty( $plan_body ) ){
 		echo "<div class='pricing_plan_body'>";
 			echo $plan_body;
		 	ob_start();
		 	if ( $add_button && !empty( $button_text ) && !empty( $button_url ) ){
		 		echo "<a href='$button_url' class='pricing_plan_button'" . ( $button_new_tab ? " target='_blank'" : "" ) . ">$button_text</a>";
		 	}
		 	$button = ob_get_clean();		
		 	if ( !empty( $button ) ){
				echo "<div class='pricing_plan_button_holder'>";
					echo $button;
				echo "</div>";
			} 		
 		echo "</div>";
 	}		 	

 	$plan = ob_get_clean();

	/* styles */
	$styles = "
		#{$section_id}.cws_vc_shortcode_pricing_plan.add_hover:hover  .pricing_plan_title,
		#{$section_id}.cws_vc_shortcode_pricing_plan.highlighted .pricing_plan_title{
			background-color: $custom_color;
		}
		#{$section_id}.cws_vc_shortcode_pricing_plan.add_hover:hover  .pricing_plan_price_wrapper,
		#{$section_id}.cws_vc_shortcode_pricing_plan.highlighted  .pricing_plan_price_wrapper{
			background-color:  ".cws_Hex2RGBA($main_color,.85).";
		}
		#{$section_id}.cws_vc_shortcode_pricing_plan.add_hover:hover  .pricing_plan_button,
		#{$section_id}.cws_vc_shortcode_pricing_plan.highlighted  .pricing_plan_button{
			background-color: $custom_color;
			border-color: $custom_color;
		}		
		#{$section_id}.cws_vc_shortcode_pricing_plan.add_hover  .pricing_plan_button:hover,
		#{$section_id}.cws_vc_shortcode_pricing_plan.highlighted  .pricing_plan_button:hover{
			color: $custom_color;
			background:transparent;
		}

		#{$section_id}.cws_vc_shortcode_pricing_plan  .pricing_plan_price_wrapper{
			background-color: ".cws_Hex2RGBA($custom_color,.85).";
		}
		.cws_vc_shortcode_pricing_plan#{$section_id} .pricing_plan_title mark{
			color: $custom_color;
		}
		.cws_vc_shortcode_pricing_plan#{$section_id}  .pricing_plan_button,
		#{$section_id} .pricing_plan_title{
			background-color: $main_color;
			border-color: $main_color;
		}
		.cws_vc_shortcode_pricing_plan#{$section_id}  .pricing_plan_button{
			color:$btn_font_color;
		}
		#{$section_id} .cws_vc_shortcode_stars.cws_vc_shortcode_active_stars{
			color: $custom_color;			
		}
	";
	/* \styles */
	$section_class = "cws_vc_shortcode_pricing_plan cws_vc_shortcode_module";
	$section_class .= !empty( $plan ) && !empty( $button ) ? " cws_vc_shortcode_flex_column_sb" : "";
	$section_class .= $highlighted ? " highlighted" : "";
	$section_class .= $add_hover ? " add_hover" : "";
	$section_class .= !empty( $el_class ) ? " $el_class" : "";
	//$out .= $use_custom_color && !empty( $custom_color ) ? "<style type='text/css'>$styles</style>" : "";
	Cws_shortcode_css()->enqueue_cws_css($styles);
	$out .= "<div id='$section_id' class='$section_class'>";
		if ( !empty( $plan ) ){
			$out .= "<div class='pricing_plan'>";
				$out .= $plan;		
			$out .= "</div>";
		}

	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_pricing_plan', 'cws_vc_shortcode_sc_pricing_plan' );

function cws_vc_shortcode_sc_gifts_cards ( $atts = array(), $content = "" ){
	$theme_color = esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	extract( shortcode_atts( array(
		'add_divider'		=> true,
		'curency_alignment'	=> 'before',
		'price'				=> '',		
		'currency'			=> '',
		'price_desc'		=> '',
		'add_button'		=> '',
		'add_discount'		=> '',
		'button_text'		=> '',
		'button_url'		=> '',
		'button_new_tab'	=> '',
		'highlighted'		=> '',
		'use_custom_color'	=> '',
		'add_url'			=> '',
		'add_url_new_tab'	=> '',
		'title'				=> 'Gift Voucher',
		'cards_img'			=> '',
		'cards_logo'		=> '',
		'discount_text'		=> '',
		'add_hover'			=> '',
		'discount_color'	=> $theme_color,
		'custom_color'		=> $theme_color,
		'main_color'		=> $theme_color,
		'bg_color'			=> $theme_color,
		'btn_font_color'	=> $theme_color,
		'el_class'			=> ''
 	), $atts));
 	$title 				= wp_kses( $title, array(
 							'span'		=> array(),
 							'mark' 		=> array(),
 							'b'			=> array(),
 							'strong'	=> array(),
 							'br'		=> array()
 						), $title );
 	$cards_logo 			= esc_html( $cards_logo );
 	$currency 			= esc_html( $currency );
 	$price 				= esc_html( $price );
 	$discount_text 		= esc_html( $discount_text );
 	$price_desc 		= esc_html( $price_desc );
 	$add_button 		= (bool)$add_button;
 	$button_text 		= esc_html( $button_text );
 	$button_url 		= esc_url( $button_url );
 	$button_new_tab 	= (bool)$button_new_tab;
 	$highlighted		= (bool)$highlighted;
 	$use_custom_color 	= (bool)$use_custom_color;
 	$custom_color 		= esc_attr( $custom_color );
 	$add_divider 		= esc_attr( $add_divider );
 	$main_color 		= esc_attr( $main_color );
 	$curency_alignment 	= esc_attr( $curency_alignment );
 	$cards_img 			= esc_attr( $cards_img );
 	$bg_color 			= esc_attr( $bg_color );
 	$discount_color 	= esc_attr( $discount_color );
 	$add_discount 		= esc_attr( $add_discount );
 	$add_url_new_tab 	= esc_attr( $add_url_new_tab );
 	$add_url 			= esc_attr( $add_url );
 	$btn_font_color 	= esc_attr( $btn_font_color );
 	$add_hover 			= (bool)$add_hover;
 	$el_class			= esc_attr( $el_class );
 	$out = "";
	$section_id = uniqid( 'cws_vc_shortcode_gifts_cards_' );
	$plan_img_data = array();
	$plan_img_src = "";
	$plan_img_dims = array( 'height' => 125, 'width' => 125, 'crop' => true );
	$plan_img_html = "";
	if ( !empty( $cards_logo ) ){
		$plan_img_data = wp_get_attachment_image_src( $cards_logo, 'full' );
		if ( is_array( $plan_img_data ) ){
 			$plan_img_src = $plan_img_data[0];
 			$plan_img_thumb_data = cws_thumb( $plan_img_src, $plan_img_dims, false );
 			$plan_img_thumb_src = isset( $plan_img_thumb_data[0] ) ? $plan_img_thumb_data[0] : "";
 			$retina_thumb = isset( $plan_img_thumb_data[3] ) ? $plan_img_thumb_data[3] : false;

 			if ( !empty( $plan_img_thumb_src ) ){
 				$plan_img_html .= "<img src='".esc_url($plan_img_thumb_src)."' alt = '" . get_post_meta( $cards_logo, '_wp_attachment_image_alt', true ) . "' class='gifts_cards_img'" . ( !empty( $retina_thumb ) ? " data-at2x='".esc_url($retina_thumb)."'" : " data-no-retina" ) . " />";
 			}
		}
	}
	$cards_data = '';	
	if ( !empty( $cards_img ) ){
		$cards_data = wp_get_attachment_image_src( $cards_img, 'full' );
		if ( is_array( $cards_data ) ){
 			$cards_data = $cards_data[0];
		}
	}
	ob_start();
	echo !empty( $title ) ? "<h3 class='gifts_cards_title'>".$title."</h3>" : "";
	echo !empty($add_divider) ? "<span class='separator_css'></span>" : "";
 	if ( !empty( $price ) ){
		preg_match( "/(\.|,)(\d+)$/", $price, $matches );
		$fract_price_part = isset( $matches[2] ) ? $matches[2] : '';
		$main_price_part = !empty( $fract_price_part ) ? esc_html( mb_substr( $price, 0, strpos( $price, $fract_price_part ) ) ) : esc_html( $price ); 		
 		echo "<div class='gifts_cards_price_wrapper'>";
	 		echo "<div class='gifts_cards_price'>";
	 			echo "<span class='price'>";
	 				if($curency_alignment == 'before'){
	 					echo !empty( $currency ) ? "<span class='currency'>$currency</span>" : "";
	 				}
		 			echo "<span class='main_price_part'>$main_price_part</span>";
		 			echo !empty( $fract_price_part ) ? "<span class='fract_price_part'>$fract_price_part</span>" : "";
		 			if($curency_alignment == 'after'){
	 					echo !empty( $currency ) ? "<span class='currency'>$currency</span>" : "";
	 				}
	 			echo "</span>";
	 			echo !empty( $price_desc ) ? "<span class='price_desc'>$price_desc</span>" : "";
	 		echo "</div>";
	 	echo "</div>";
	 	
 	}	
 	$plan_body = ob_get_clean();
 	
 	ob_start();
 	if ( !empty( $plan_img_html ) ){
 		echo "<div class='gifts_cards_left'>";
 			echo "<div class='gifts_cards_head_wrapper'>";
 				echo $plan_img_html;
 			echo "</div>";
 		echo "</div>";
 	}
 	if ( !empty( $plan_body ) ){
 		echo "<div class='gifts_cards_right'>";
 			echo $plan_body;
		 	ob_start();
		 	if ( $add_button && !empty( $button_text ) && !empty( $button_url ) ){
		 		echo "<a href='$button_url' class='gifts_cards_button'" . ( $button_new_tab ? " target='_blank'" : "" ) . ">$button_text</a>";
		 	}
		 	$button = ob_get_clean();		
		 	if ( !empty( $button ) ){
				echo "<div class='gifts_cards_button_holder'>";
					echo $button;
				echo "</div>";
			} 		
 		echo "</div>";
 	}		 	

 	$gifts_cards_html = ob_get_clean();

	$styles = "";
	ob_start();
	if ( $use_custom_color ){
		if(!empty($custom_color)){
		echo "#{$section_id}{
			color:$custom_color;
			} ";
		}			
	}
	if(!empty($discount_color)){
		echo "#{$section_id} .discount_gifts_cards{
			color:$discount_color;
		} ";	
	}
	$styles = ob_get_clean();

	ob_start();
	$content = apply_filters( 'the_content', $content );
	echo !empty( $content ) ? "<div class='gifts_cards_content'>$content</div>" : "";
	$content_back = ob_get_clean();

	$section_class = "cws_vc_shortcode_gifts_cards cws_vc_shortcode_module";
	$section_class .= !empty( $gifts_cards_html ) && !empty( $button ) ? " cws_vc_shortcode_flex_column_sb" : "";
	$section_class .= $highlighted ? " highlighted" : "";
	$section_class .= $add_hover ? " add_hover" : "";
	$section_class .= !empty( $el_class ) ? " $el_class" : "";
	//$out .= !empty( $styles ) ? "<style type='text/css'>$styles</style>" : "";
	!empty( $styles ) ? Cws_shortcode_css()->enqueue_cws_css($styles) : "";
	$out .= "<div id='$section_id' class='$section_class'>";
		if(!empty($content_back)){
			$out .= "<div class='flip'>";
		}else{
			$out .= "<div class='no-flip'>";
		}
		
		if ( !empty( $gifts_cards_html ) ){
			$out .= "<div class='gifts_cards perspective_gifts front'".(!empty($bg_color) ? ' style="background-color:'.$bg_color.'"' : "").">";
			if(!empty($cards_data)){
				$out .= "<span class='bg_gifts_cards' style='background-repeat:no-repeat;background-image:url(".$cards_data.")'></span>";
			}
			if(!empty($add_discount) && !empty($discount_text)){
				$out .= "<div class='discount_gifts_cards'>";
					$out .= $discount_text;		
				$out .= "</div>";
			}	
				$out .= $gifts_cards_html;		
			$out .= "</div>";
		}	
		if(!empty($content_back)){
			$out .= "<div class='perspective_gifts back'".(!empty($bg_color) ? ' style="background-color:'.$bg_color.'"' : "").">";
			$out .= $content_back;
			$out .= "</div>";
		}
		$out .= "</div>";
		if(!empty($add_url) && !empty($button_url)){
			$out .= "<a href='{$button_url}' class='gifts_btn_url'".(!empty($add_url_new_tab) ? " target='_blank'" : "")."></a>";
		}

	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_gift_cards', 'cws_vc_shortcode_sc_gifts_cards' );

function cws_vc_shortcode_sc_banners ( $atts = array(), $content = "" ){
	$theme_color = esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	extract( shortcode_atts( array(
		'style_type'			=> '',
		'text_alignment'		=> 'left',
		'title_banners'			=> '25%',
		'desc_banners'			=> 'Best offer',
		'f_size_title_banners'	=> '',
		'f_size_desc_banners'	=> '',
		'banners_img'			=> '',
		'add_divider'			=> true,
		'add_button'			=> true,
		'button_text'			=> 'Buy Now',
		'button_url'			=> '#',
		'button_new_tab'		=> '',
		'highlighted'			=> '',
		'use_custom_color'		=> '',
		'custom_color'			=> $theme_color,
		'overlay_color'			=> $theme_color,
		'trianlge_color'		=> $theme_color,
		'discount_banners'		=> '',
		'content'				=> '',
		'title'					=> '',
		'el_class'				=> ''
 	), $atts));
 	$title 				= wp_kses( $title, array(
 							'span'		=> array(),
 							'mark' 		=> array(),
 							'b'			=> array(),
 							'strong'	=> array(),
 							'br'		=> array()
 						), $title );
 	
 	$banners_img 		= esc_html( $banners_img );
 	$title_banners 		= esc_html( $title_banners );
 	$desc_banners 		= esc_html( $desc_banners );
 	$add_button 		= (bool)$add_button;
 	$add_divider 		= (bool)$add_divider;
 	$button_text 		= esc_html( $button_text );
 	$button_url 		= esc_url( $button_url );
 	$button_new_tab 	= (bool)$button_new_tab;
 	$use_custom_color 	= (bool)$use_custom_color;
 	$custom_color 		= esc_attr( $custom_color );	
 	$text_alignment 	= esc_attr( $text_alignment );
 	$style_type			= esc_attr( $style_type );
 	$trianlge_color		= esc_attr( $trianlge_color );
 	$el_class			= esc_attr( $el_class );
 	$out = "";
	$section_id = uniqid( 'cws_vc_shortcode_banners_' );
	$plan_img_data = array();
	$plan_img_src = "";
	$plan_img_dims = array( 'height' => 195 );
	$banners_img_html = "";
	if ( !empty( $banners_img ) ){
		$plan_img_data = wp_get_attachment_image_src( $banners_img, 'full' );
		if ( is_array( $plan_img_data ) ){
 			$plan_img_src = $plan_img_data[0];
 			$src_img = cws_print_img_html(array('src' => $plan_img_src), array( 'height' => 195 ) );
 			$banners_img_html = "<span class='bg_banner_img' style='background-image:url({$plan_img_src})'></span></span>";
		}
	}
	ob_start();
	echo "<div class='banners_prlx_section'>";
		echo $banners_img_html;
	echo "</div>";
	$banners_head = ob_get_clean();
 	ob_start();
 	echo !empty( $title_banners ) ? "<p class='banners_title'>$title_banners</p>" : "";
 	echo !empty( $desc_banners ) ? "<p class='banners_desc'>$desc_banners</p>" : "";
	$content = apply_filters( 'the_content', $content );
	echo !empty( $content ) ? "<div class='banners_content'>$content</div>" : "";

 	$banners_body = ob_get_clean();
 	ob_start();
 	if ( !empty( $banners_head ) ){
 		echo "<div class='banners_head'>";
 			echo "<div class='banners_head_wrapper'>";
 				if(!empty($overlay_color)){
 					echo "<div class='ov_color_banner' style='background:".$overlay_color."'></div>";
 				}
 				echo $banners_head; 				
 				if(!empty($trianlge_color) && !empty($use_custom_color)){
 					echo "<div class='ov_color_triangle' style='background:".$trianlge_color."'></div>";
 				}
 			echo "</div>";
 		echo "</div>";
 	}
 	if ( !empty( $banners_body ) ){
 		echo "<div class='banners_body'>";
 			echo $banners_body;
 			if($style_type == 'style2'){
 				echo "<div class='wrapper-skew'>";
 			}
 			echo !empty($discount_banners) ? "<div class='discount_price'>$discount_banners</div>" : ""; 	
 			if ( $add_button && !empty( $button_text ) && !empty( $button_url ) ){
 				echo "<a href='$button_url' class='banners_button'" . ( $button_new_tab ? " target='_blank'" : "" ) . ">$button_text</a>";
 			}
 			if($style_type == 'style2'){
 				echo "</div>";
 			}
 		echo "</div>";
 	}
 	$banners = ob_get_clean();
	/* styles */
	$styles = "
		#{$section_id} .banners_price,
		#{$section_id} .banners_content ul li:before,
		.cws_vc_shortcode_banners#{$section_id} .banners_title mark{
			color: $custom_color;
		}
		#{$section_id}.cws_vc_shortcode_banners.cws_vc_shortcode_module .banners_body{
			color: $custom_color;
			text-align:$text_alignment;
		}
		#{$section_id}.cws_vc_shortcode_banners.cws_vc_shortcode_module .banners_body .banners_title{		
			font-size: $f_size_title_banners;
		}		
		#{$section_id}.cws_vc_shortcode_banners.cws_vc_shortcode_module .banners_body .banners_desc{
			font-size: $f_size_desc_banners;
		}

	";
	/* \styles */
	$section_class = "cws_vc_shortcode_banners cws_vc_shortcode_module";
	$section_class .= !empty( $el_class ) ? " $el_class" : "";
	$section_class .= !empty( $style_type ) ? " $style_type" : " style1";
	$section_class .= !empty( $add_divider ) ? " add_divider" : "";
	$section_class .= !empty( $text_alignment ) ? " a-$text_alignment" : "";
	//$out .= $use_custom_color ? "<style type='text/css'>$styles</style>" : "";
	$use_custom_color ? Cws_shortcode_css()->enqueue_cws_css($styles) : "";
	$out .= "<div id='$section_id' class='$section_class'>";
		$out .= !empty($banners) ? $banners : "";
	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_banners', 'cws_vc_shortcode_sc_banners' );

function cws_vc_shortcode_sc_divider ( $atts = array(), $content = "" ){
	$theme_color = esc_attr( cws_vc_shortcode_get_option( 'theme-main-one-color' ) );
	extract( shortcode_atts( array(
		"type"				=> "",
		"height_divider"	=> "3",
		"mtop"				=> "",
		"mbottom"			=> "",
		"customize_colors"	=> false,
		"plan_img"			=> '',
		"icon_lib"			=> "",
		"custom_color_icon"	=> $theme_color,
		"size"				=> "",
		"custom_color"		=> $theme_color,
		"el_class"			=> ""
 	), $atts));
 	$type 				= esc_attr( $type );
  	$mtop 				= esc_attr( $mtop );
  	$mbottom 			= esc_attr( $mbottom );
  	$customize_colors 	= (bool)$customize_colors;
  	$custom_color 		= esc_attr( $custom_color );
 	$el_class			= esc_attr( $el_class );
	$classes = "separator-wrapper";
	$classes .= !empty( $type ) ? " $type" : "";
	$classes .= !empty( $el_class ) ? " $el_class" : "";
	$classes = trim( $classes );
	
	$id = uniqid( "cws_divider_" );
	ob_start();
	if ( !empty( $mtop ) || !empty( $mbottom ) ){
		echo "
			#{$id}{
			" . ( !empty( $mtop ) ? "margin-top:{$mtop}px;" : "" ) . "
			" . ( !empty( $mbottom ) ? "margin-bottom:{$mbottom}px;" : "" ) . "
			}
		";
	}
	if ( $customize_colors && !empty( $custom_color ) ){
		echo "
			#{$id} .separator-line,#{$id} hr{
				background-color: $custom_color;
			}
		";
	}else{
		echo "
			#{$id} .separator-line,#{$id} hr{
				background-color: $custom_color;
			}
		";	
	}
	$styles = ob_get_clean();
	$out = "";
	if ( !empty( $styles ) ){
		Cws_shortcode_css()->enqueue_cws_css($styles);
	}

	$separator_css = '';
	$separator_css .= !empty($height_divider) ? "height:".(int) $height_divider."px;" : "";	
	
	$result = '';
	if(!empty($plan_img)){
		$thumb_src_obj = wp_get_attachment_image_src( $plan_img, 'full' );
		$thumb_src = isset( $thumb_src_obj[0] ) ? $thumb_src_obj[0] : "";
		$src_img = cws_print_img_html(array('src' => $thumb_src), array( 'width' => 40, 'height' => 40, 'crop' => true ) );
		$result .= "<span class='thumb'><img {$src_img} alt = '" . get_post_meta( $plan_img, '_wp_attachment_image_alt', true ) . "' /></span>";
	}
	
	if(!empty($icon_lib)){
		$icon = "icon_".$icon_lib;
		if($icon_lib == 'cws_svg'){
			$svg_icon = json_decode(str_replace("``", "\"", $atts[$icon]), true);
			$upload_dir = wp_upload_dir();
			$this_folder = $upload_dir['basedir'] . '/cws-svgicons/' . md5($svg_icon['collection']) . '/';				
			$result .= '<span class="icon"><i class="svg" style="width:'.$svg_icon['width'].'px;height:'.$svg_icon['height'].'px">'.file_get_contents($this_folder . $svg_icon['name']).'</i></span>';
		}else{
			
			if($icon != 'icon_'){
				$icon_css = !empty($custom_color_icon) ? "color:{$custom_color_icon};" : "";
				$result .=  "<span class='icon'><i class='".(!empty($size) ? "cws_vc_shortcode_icon_$size" : "cws_vc_shortcode_icon_3x")." {$atts[$icon]}'".(!empty($icon_css) ? " style='{$icon_css}'" : "")."></i></span>";
			}			
		}

	}

	$out .= "<div" . ( !empty( $styles ) ? " id='{$id}'" : "" ) . ( !empty( $classes ) ? " class='$classes'" : "" ) . " />";
		if(!empty($result)){
			$out .= "<div class='separator-line separator-container-left-line'".(!empty($separator_css) ? " style='{$separator_css}'" : "" )."></div>";
			$out .= !empty($result) ? $result : "";
			$out .= "<div class='separator-line separator-container-right-line'".(!empty($separator_css) ? " style='{$separator_css}'" : "" )."></div>";			
		}else{
			$out .= "<hr".(!empty($separator_css) ? " style='{$separator_css}'" : "" ).">";
		}

	$out .= "</div>";
	return $out;
}
add_shortcode( 'cws_sc_divider', 'cws_vc_shortcode_sc_divider' );

function cws_vc_shortcode_sc_spacing ( $atts = array(), $content = "" ){
	extract( shortcode_atts( array(
		"spacing"			=> "30px",
		"el_class"			=> ""
 	), $atts));
 	$spacing = esc_attr( $spacing );
 	$el_class = esc_attr( $el_class );
 	$out = "";
 	if ( !empty( $spacing ) ){
 		$out .= "<div class='cws_spacing" . ( !empty( $el_class ) ? " $el_class" : "" ) . "' style='height: $spacing;'></div>";
 	}
	return $out;
}
add_shortcode( 'cws_sc_spacing', 'cws_vc_shortcode_sc_spacing' );

function cws_vc_shortcode_sc_text ( $atts = array(), $content = "" ){
	$defaults = array(
		'title'					=> '',
		'subtitle'				=> '',
		'title_underlined'		=> false,
		'title_alignment'		=> '',
		'width'					=> '',
		'icon_lib'				=> '',
		'icon_position'			=> '',
		'customize_colors'		=> false,
		'custom_font_color'		=> '',
		'custom_subtitle_color'	=> '',
		'custom_title_color'	=> '',
		'custom_icon_color'		=> '',
		'size'					=> '3x',
		'customize_size'		=> '',
		'size_i'				=> '',
		'el_class'				=> '',
		'custom_styles'			=> '',
		'add_animation'			=> '',
		'add_line_animation'	=> '',
		'line_width'			=> '4px',
		'margins_line'			=> '60px',
		'margins'				=> '0 0 0 0',
 	);
	$proc_atts = shortcode_atts( $defaults, $atts );
	extract( $proc_atts );
	$title_underlined		= (bool)$title_underlined;
	$title_alignment 		= esc_attr( $title_alignment );
	$icon 					= function_exists('cws_ext_vc_sc_get_icon') ? cws_ext_vc_sc_get_icon( $atts ) : "";
	$icon 					= esc_attr( $icon );
	$icon_position 			= esc_attr( $icon_position );
	$customize_colors 		= (bool)$customize_colors;
	$custom_font_color 		= esc_attr( $custom_font_color );
	$line_width 			= esc_attr( $line_width );
	$custom_subtitle_color 	= esc_attr( $custom_subtitle_color );
	$custom_title_color 	= esc_attr( $custom_title_color );
	$custom_icon_color 		= esc_attr( $custom_icon_color );
	$el_class				= esc_attr( $el_class );
	$margins				= esc_attr( $margins );
	$add_animation			= esc_attr( $add_animation );
	$margins_line			= esc_attr( $margins_line );
	$add_line_animation		= esc_attr( $add_line_animation );
	$size					= !empty($size) ? esc_attr( $size ) : "5х";

	$custom_styles = esc_attr( $custom_styles );
	$custom_css_class = "";

	if ( is_plugin_active('js_composer/js_composer.php') ){
		$custom_css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $custom_styles, ' ' ), 'cws_sc_call_to_action', $atts );
	} else {
		$custom_css_class = '';
	}

	$title = wp_kses( $title, array(
		"b"			=> array(),
		"strong"	=> array(),
		"mark"		=> array(),
		"br"		=> array()
	));
	$content = apply_filters( "the_content", $content );
	$module_id = uniqid( "cws_textmodule_" );
	$out = $titles = $icon_html = $styles = "";
	ob_start();
	echo !empty( $subtitle ) ? "<h4 class='widgetsubtitle'><span>" . esc_html( $subtitle ) . "</span></h4>" : "";
	echo !empty( $title ) ? "<h2 class='widgettitle'><span>$title</span></h2>" : "";	
	$titles .= ob_get_clean();
		
		if($icon_lib == 'cws_svg'){
			$svg_icon = json_decode(str_replace("``", "\"", $icon), true);
			$upload_dir = wp_upload_dir();
			$this_folder = $upload_dir['basedir'] . '/cws-svgicons/' . md5($svg_icon['collection']) . '/';				
			if($data = file_get_contents($this_folder . $svg_icon['name'])){
				$icon_html .= '<i class="svg cws_textmodule_icon'.(!empty($add_animation) ? " add_animation_icon" : "").(!empty($add_line_animation) ? " add_line_animation" : ""). '" style="width:'.$svg_icon['width'].'px;height:'.$svg_icon['height'].'px">'.file_get_contents($this_folder . $svg_icon['name']).'</i>';	
			}
			
		}else{
			$icon_html .= !empty( $icon ) ? "<i class='cws_service_icon cws_vc_shortcode_icon_{$size} cws_textmodule_icon " . esc_attr( $icon ) . "".(!empty($add_animation) ? ' add_animation_icon' : "").(!empty($add_line_animation) ? ' add_line_animation' : "")."'></i>" : "";
		}
	ob_start();
	if ( !empty( $titles ) || !empty( $content ) || !empty( $icon ) ){
		echo "<div class='cws_textmodule cws_vc_shortcode_module" . ( $icon_position ? " icon_{$icon_position}" : "" ) . ( $title_underlined ? " title_underlined" : "" ) . ( !empty( $el_class ) ? " $el_class" : "" ) . ( !empty( $custom_css_class ) ? " $custom_css_class" : "" ) . "' id='$module_id'>";
			if(!empty($customize_size)){
				$styles .= "
					#{$module_id} .cws_service_icon{
						" . ( !empty( $customize_size ) && !empty($size_i) ? "font-size: ".(int)$size_i ."px;" : "" ) .  "
					}
					";
			}
			if(!empty($margins)){
				$styles .= "
					#{$module_id} .cws_textmodule_icon_wrapper{
						" . ( !empty( $margins ) ? "margin: ".$margins .";" : "" ) .  "
					}
					";
			}			
			if(!empty($margins_line)){
				$styles .= "
					#{$module_id} .cws_textmodule_icon_wrapper.add_animation_icon.icon_init .cws_separator_icon{
						" . ( !empty( $margins_line ) ? "top: ".(int) $margins_line ."px;" : "" ) .  "
					}
					";
			}
			if ( $customize_colors ){
				$styles .= "
					#{$module_id}{
						" . ( !empty( $custom_font_color ) ? "color:$custom_font_color;" : "" ) .  "
					}
					#{$module_id} .widgettitle,
					#{$module_id} h1,
					#{$module_id} h2,
					#{$module_id} h3,
					#{$module_id} h4,
					#{$module_id} h5,
					#{$module_id} h6,
					#{$module_id} blockquote,
					#{$module_id} a:hover,
					#{$module_id} .wp-playlist-light .wp-playlist-tracks .wp-playlist-playing{
						" . ( !empty( $custom_title_color ) ? "color:$custom_title_color;" : "" ) .  "
					}
					#{$module_id} .widgetsubtitle{
						" . ( !empty( $custom_subtitle_color ) ? "color:$custom_subtitle_color;" : "" ) .  "
					}
					#{$module_id} .widgettitle mark{
						" . ( !empty( $custom_subtitle_color ) ? "color:$custom_subtitle_color;" : "" ) .  "
					}
					#{$module_id} .cws_textmodule_titles{
						" . ( !empty( $custom_subtitle_color ) ? "border-bottom-color:$custom_subtitle_color;" : "" ) .  "
					}
					#{$module_id} .cws_textmodule_icon{
						" . ( !empty( $custom_icon_color ) ? "color:$custom_icon_color;" : "" ) .  "
						" . (  $icon_lib == 'cws_svg' ? "fill:$custom_icon_color;" : "" ) .  "
					}					
					#{$module_id} .cws_textmodule_icon_wrapper.add_animation_icon .cws_separator_icon{
						" . ( !empty( $custom_icon_color ) ? "border-top:{$line_width} solid {$custom_icon_color};" : "" ) .  "
					}
				";
			}
			if ( !empty( $styles ) ){
				/*echo "<style id='{$module_id}_style' type='text/css'>
						$styles			
					</style>";*/
				Cws_shortcode_css()->enqueue_cws_css($styles);
			}
			echo "<div class='cws_textmodule_wrapper'>";				
				if ( !empty( $icon ) ){
					if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
						vc_icon_element_fonts_enqueue( $icon_lib );		
					}
					echo "<div class='cws_textmodule_icon_wrapper".(!empty($add_animation) ? ' add_animation_icon' : "").(!empty($add_line_animation) ? ' add_line_animation' : "")."'>";
						if(!empty($add_line_animation)){
							echo "<span class='cws_separator_icon cws_separator_left'></span>";
						}
						echo $icon_html;
						if(!empty($add_line_animation)){
							echo "<span class='cws_separator_icon cws_separator_right'></span>";
						}
					echo "</div>";
				}
				if ( !empty( $titles ) || !empty( $content ) ){
					echo "<div class='cws_textmodule_text'" . ( !empty( $width ) ? " style='max-width:{$width}px;'" : "" ) . ">";
						if ( !empty( $titles ) ){
							echo "<div class='cws_textmodule_titles" . ( !empty( $title_alignment ) ? " text_align{$title_alignment}" : "" ) . "'>";
								echo $titles;
							echo "</div>";
						}
						if ( !empty( $content ) ){
							echo "<div class='cws_textmodule_content'>";
								echo $content;
							echo "</div>";
						}
					echo "</div>";
				}

			echo "</div>";
		echo "</div>";
	}
	$out .= ob_get_clean();
	return $out;
}
add_shortcode( 'cws_sc_text', 'cws_vc_shortcode_sc_text' );


function cws_vc_shortcode_sc_tips($atts, $content, $tag) {
	extract( shortcode_atts( array(
		'image' => '',
		'width' => '',
		'color' => '',
		'ispulse' => 'yes',
		'pulsecolor' => 'pulse-white',
		'icon' => '',
		'iconsize' => '',
		'tooltipstyle' => 'shadow',
		'iconbackground' => 'rgba(0,0,0,0.8)',
		'tooltipanimation' => 'grow',
		'circlecolor' => '#FFFFFF',
		'opacity' => '1',
		'arrowposition' => '',
		'trigger' => '',
		'links' => '',
		'maxwidth' => '240',
		'custom_links_target' => '',
		'position' => '25%|30%,35%|20%,45%|60%,75%|20%',
		'containerwidth' => '',
		'marginoffset' => '',
		'icontype' => 'dot',
		'fonticon' => '',
		'isdisplayall' => 'off',
		'displayednum' => '1',
		'startnumber' => '1',
		'extra_class' => ''
		), $atts ) );

	$image_full = wp_get_attachment_image_src($image, 'full');
	$position = explode(',', $position);
	$color = explode(',', $color);
	$arrowposition = explode(',', $arrowposition);
	$links = explode(',', $links);
	$fonticon = explode(',', $fonticon);
	$i = -1;
	$is_new_tag = false;
          $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
          if(strpos($content, '[/cwstips]')===false){
          	$content = str_replace('</div>', '', trim($content));
          	$contentarr = explode('<div class="tooltip-content">', trim($content));
          }else{
          	$content = str_replace('[/cwstips]', '', trim($content));
          	$contentarr = explode('[cwstips]', trim($content));
          	$is_new_tag = true;
          }
          $pulseborder = "";
          $ispulse = $ispulse == "yes" ? $pulsecolor : "";
          array_shift($contentarr);
          $output = $tooltipcontent = '';
          $output .= '<div style="width:'.$containerwidth.';" class="cwstooltip-wrapper '.$extra_class.'" data-opacity="'.$opacity.'" data-tooltipanimation="'.$tooltipanimation.'" data-tooltipstyle="'.$tooltipstyle.'" data-trigger="'.$trigger.'" data-maxwidth="'.$maxwidth.'" data-marginoffset="'.$marginoffset.'" data-isdisplayall="'.$isdisplayall.'" data-displayednum="'.$displayednum.'">';

          $image_temp = $imagethumb = "";
          $fullimage = $image_full[0];
          $imagethumb = $fullimage;
          $attachment = get_post($image);
          if($width!=""){
          	if(function_exists('wpb_resize')){
          		$image_temp = wpb_resize($image, null, $width, null);
          		$imagethumb = $image_temp['url'];
          		if($imagethumb=="") $imagethumb = $fullimage;
          	}
          }

          $output .= '<img src="'.$imagethumb.'" alt="'.get_post_meta($attachment->ID, '_wp_attachment_image_alt', true ).'" />';
          $output .= '<div class="cws-hotspots">';
          foreach ($contentarr as $key => $thecontent) {
          	$i++;
          	$tooltipcontent = '';
          	if(!isset($position[$i])) $position[$i] = '25%|25%';
          	if(!isset($fonticon[$i])) $fonticon[$i] = '';
          	$iconposition = explode('|', trim($position[$i]));
          	if(!isset($iconposition[0])) $iconposition[0] = '25%';
          	if(!isset($iconposition[1])) $iconposition[1] = '25%';
          	if(!isset($color[$i])) $color[$i] = '';
          	if(!isset($arrowposition[$i])) $arrowposition[$i] = 'top';
          	if(!isset($links[$i])) $links[$i] = '';
          	if($color[$i]!="") {
          		$iconcolor = $color[$i];
          	}else{
          		$iconcolor = $iconbackground;
          	}
          	$tooltipcontent = trim($thecontent); 
          	$tooltipcontent = preg_replace("/(^)?(<br\s*\/?>\s*)+$/", "", $tooltipcontent);
          	$tooltipcontent = preg_replace('/^(<br \/>)*/', "", $tooltipcontent);
          	$tooltipcontent = preg_replace('/^(<\/p>)*/', "", $tooltipcontent);
          	$output .= '<div class="hotspot-item '.$ispulse.' '.$pulseborder.'" style="top:'.$iconposition[0].';left:'.$iconposition[1].';" data-top="'.$iconposition[0].'" data-left="'.$iconposition[1].'">';
          	if($links[$i]!=""){
          		$output .= '<a href="'.$links[$i].'" class="cws-tooltip" style="background:'.$iconcolor.';" data-tooltip="'.htmlspecialchars($tooltipcontent).'" data-arrowposition="'.trim($arrowposition[$i]).'" target="'.$custom_links_target.'">';
          	}else{
          		$output .= '<a href="#" class="cws-tooltip" style="background:'.$iconcolor.';" data-tooltip="'.htmlspecialchars($tooltipcontent).'" data-arrowposition="'.trim($arrowposition[$i]).'">';
          	}
          	if($icontype=="number"){
          		if($startnumber!=1){
          			$output .= '<i>';
          			$output .= $startnumber+$i;
          			$output .= '</i>';
          		}else{
          			$output .= '<i>';
          			$output .= $i+1;
          			$output .= '</i>';
          		}
          	}else if($icontype=="icon"){
          		if($fonticon[$i]!=""){
          			$output .= '<i class="fa '.$fonticon[$i].'" style="color:'.$circlecolor.';"></i>';
          		}else{
          			$output .= '<span style="background:'.$circlecolor.';">';
          			$output .= '</span>';
          		}
          	}else{
          		$output .= '<span style="background:'.$circlecolor.';">';
          		$output .= '</span>';
          	}

          	$output .= '</a>';
          	$output .= '</div>';
          }
          $output .= '</div>';
          $output .= '</div>';

          return $output;
}

add_shortcode( 'cws_sc_tips', 'cws_vc_shortcode_sc_tips' );

/***********
* LEARNPRESS
***********/
function cws_vc_shortcode_sc_vc_lp_course_posts_grid ( $atts = array(), $content = "" ){
	$post_type = defined( "LP_COURSE_CPT" ) ? LP_COURSE_CPT : "lp_course";	
	$defaults = array(
		'title'				=> '',
		'title_align'		=> 'left',
		'total_items_count'	=> '',
		'display_style'		=> 'grid',
		'layout'			=> '3',
		'items_pp'			=> esc_html( get_option( 'posts_per_page' ) ),
		'el_class'			=> '',
	);
	$proc_atts = shortcode_atts( $defaults, $atts );
	extract( $proc_atts );
	$out = "";
	$tax = isset( $atts[$post_type . '_tax'] ) ? $atts[$post_type . '_tax'] : '';
	$terms = isset( $atts["{$post_type}_{$tax}_terms"] ) ? $atts["{$post_type}_{$tax}_terms"] : "";
	$proc_atts = array_merge( $proc_atts, array(
		'tax'									=> $tax,
		'terms'									=> $terms
	));
	$out .= function_exists( "cws_vc_shortcode_sc_lp_course_posts_grid" ) ? cws_vc_shortcode_sc_lp_course_posts_grid( $proc_atts ) : "";
	return $out;
}

//Check if plugin active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active('learnpress/learnpress.php') ) {
	add_shortcode( 'cws_sc_vc_lp_course_posts_grid', 'cws_vc_shortcode_sc_vc_lp_course_posts_grid' );
}
/************
* \LEARNPRESS
************/

?>