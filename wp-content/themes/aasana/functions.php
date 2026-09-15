<?php

# CONSTANTS
define('AASANA_URI', get_template_directory_uri());
define('AASANA_THEME_DIR', get_template_directory());
defined('AASANA_COLOR') or define('AASANA_COLOR', '#7b6cd5');
defined('AASANA_FOOTER_COLOR') or define('AASANA_FOOTER_COLOR', '#fafafa');
defined('AASANA_SECONDARY_COLOR') or define('AASANA_SECONDARY_COLOR', '#ea8fca');

defined('IS_FOOTER') or define('IS_FOOTER', 1);

# \CONSTANTS

# TEXT DOMAIN
load_theme_textdomain( 'aasana' , get_template_directory() .'/languages' );
# \TEXT DOMAIN

global $aasana_theme_funcs;
global $aasana_theme_standard;
//Check if plugin active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (function_exists( 'cws_core_cwsfw_get_args' ) && get_option('aasana')){
	$aasana_theme_funcs = new Aasana_Funcs();
} else {
	$aasana_theme_standard = new Aasana_Funcs_default();
}


// CWS Theme Aasana Standard Settings
class Aasana_Funcs_default{
	const THEME_BEFORE_CE_TITLE = '<div class="ce_title">';
	const THEME_AFTER_CE_TITLE = '</div>';
	const THEME_V_SEP = '<span class="v_sep"></span>';
	public static $text_domain = 'aasana';
	protected static $cws_theme_config;


	public function __construct(){
		require_once(get_template_directory() . '/core/cws_thumb.php');
		require_once get_template_directory() . '/core/plugins.php';
		require_once(get_template_directory() . '/core/breadcrumbs.php');
		if (!is_admin()){
			add_action( 'wp_enqueue_scripts', array($this, 'cws_register_default') );
		}
		
		add_filter('embed_oembed_html', array($this, 'cws_oembed_wrapper'),10,3);
		$this->assign_constants();
		add_action('after_setup_theme', array($this, 'cws_after_setup_theme') );

		define('CWS_WOO_ACTIVE', class_exists( 'woocommerce' ));

		if (CWS_WOO_ACTIVE) {
			add_action( 'wp_ajax_woocommerce_remove_from_cart',array( $this, 'cws_woo_ajax_remove_from_cart' ),1000 );
			add_action( 'wp_ajax_nopriv_woocommerce_remove_from_cart', array( $this, 'cws_woo_ajax_remove_from_cart' ),1000 );

			require_once( get_template_directory() . '/woocommerce/wooinit.php' ); // WooCommerce Shop ini file

			add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'cws_woo_header_add_to_cart_fragment') );
			
			add_filter( 'woocommerce_output_related_products_args', array($this, 'cws_woo_related_products_args') );
			add_action( 'after_setup_theme', array($this, 'cws_theme_woo_setup') );
			add_filter( 'loop_shop_per_page', array( $this, 'loop_products_per_page' ));	
		}
		$this->add_cws_sh( 'cws_sc_msg_box', array($this, 'cws_msg_box') );
		add_action('widgets_init', array($this, 'cws_widgets_init') );
	}
	public function cws_oembed_wrapper( $html, $url, $args ) {
		return !empty( $html ) ? "<div class='cws_oembed_wrapper'>$html</div>" : '';
	}
	
	public function add_cws_sh($name, $callback)  {
		$short = 'shortcode';
		call_user_func('add_' . $short, $name, $callback);
	} 
	// Check if WooCommerce is active
	public function cws_woo_ajax_remove_from_cart() {
		global $woocommerce;

		$woocommerce->cart->set_quantity( $_POST['remove_item'], 0 );

		$ver = explode( '.', WC_VERSION );

		if ( $ver[1] == 1 && $ver[2] >= 2 ) :
			$wc_ajax = new WC_AJAX();
			$wc_ajax->get_refreshed_fragments();
		else :
			woocommerce_get_refreshed_fragments();
		endif;

		die();
	}

	public function cws_woo_header_add_to_cart_fragment( $fragments ) {
		ob_start();
		?>
			<i class='woo_mini-count flaticon-shopcart-icon-aasana'><?php echo ((WC()->cart->cart_contents_count > 0) ?  '<span>' . WC()->cart->cart_contents_count .'</span>' : '') ?></i>
		<?php
		$fragments['.woo_mini-count'] = ob_get_clean();

		ob_start();
		woocommerce_mini_cart();
		$fragments['div.woo_mini_cart'] = ob_get_clean();
		return $fragments;
	}
	public function cws_woo_related_products_args( $args ) {
		$args['posts_per_page'] = 4; // 4 related products
		$args['columns'] = 3; // arranged in 2 columns
		return $args;
	}

	public function cws_pagination ( $paged=1, $max_paged=1, $style = 'paged', $pagination_text = 'Load More') {
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
		<div class='pagination <?php if($style == 'load_more'){ echo ("pagination_load_more"); }?> separated'>
			<div class='page_links'>
			<?php
			$pagination_args = array( 'base' => $pagenum_link,
				'format' => $format,
				'current' => $paged,
				'total' => $max_paged,
				"prev_text" => "<i class='fa fa-angle-left'></i>",
				"next_text" => ($style == 'paged' ? "<i class='fa fa-angle-right'></i>" : $pagination_text),
				"link_before" => '',
				"link_after" => '',
				"before" => '',
				"after" => '',
				"mid_size" => 2,
			);
			echo paginate_links($pagination_args);
			?>
			</div>
		</div>
		<?php
	}

	function cws_msg_box ( $atts, $content ) {
		extract( shortcode_atts( array(
			'type'					=> '',
			'title'					=> '',
			'text'					=> '',
			'is_closable'			=> '',
			'customize'				=> '',
			'icon_lib'				=> '',
			'custom_fill_color'		=> '#e6eaed',
			'custom_font_color'		=> "#707273",
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
		if ( $customize && !empty( $icon ) ){
			if ( function_exists( 'vc_icon_element_fonts_enqueue' ) ){
				vc_icon_element_fonts_enqueue( $icon_lib );
			}
			$icon_class .= " $icon custom";
		}
		if ( !empty( $title ) || !empty( $content ) ){
			$out .= "<div id='$section_id' class='cws_vc_shortcode_msg_box cws_vc_shortcode_module" . ( !empty( $type ) ? " $type" : "" ) . ( $is_closable ? " closable" : "" ) . ( !empty( $el_class ) ? " $el_class" : "" ) . "'" . ( !empty( $section_styles ) ? " style='$section_styles'" : "" ) . ">";
				$out .= "<div class='icon_part'>";
					$out .= "<i class='$icon_class'></i>";
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
	public function cws_print_search_form($message_title = '', $message = '') {
		ob_start();
		echo shortcode_exists('cws_sc_msg_box') ? do_shortcode( "[cws_sc_msg_box type='info' title='{$message_title}' text='{$message}'][/cws_sc_msg_box]" ) : (!empty($message_title) ? "<h3>{$message_title}</h3><p>{$message}</p>" : '');
		get_search_form();
		$sc_content = ob_get_clean();
		return $sc_content;
	}
	public function cws_single_post_output() {
		$pid = get_the_id();
		$is_single = is_single( $pid );
		$title = esc_html( get_the_title() );
		$permalink = esc_url( get_the_permalink() );
		$show_author = true;
				// ================/META PART================

		$permalink = get_permalink();
		$date = get_the_time( get_option( 'date_format' ) );
		$first_word_boundary = strpos( $date, ' ' );
		ob_start();
		// ================MEDIA PART================
		$post_url = get_the_permalink();
		$single = is_single();
		$post_format = get_post_format();

		$thumbnail = has_post_thumbnail( ) ? wp_get_attachment_image_src( get_post_thumbnail_id( ),'full' ) : '';
		$thumbnail = ! empty( $thumbnail ) ? $thumbnail[0] : '';

		$thumb_media = false;
		$some_media = false;
		ob_start();
			$post_class = '';
			?>
			<div class="cws_default media_part<?php echo esc_attr($post_class); ?>"><?php
			if ( !$some_media && !empty( $thumbnail ) ) {
					?>
						<div class="date new_style">
							<div class="meta_date">
							<?php
								$meta_date_arr = array();
								/* Date */

								array_push( $meta_date_arr, "<div class='date-content'>" );
								$date = get_the_time( get_option("date_format") );
								if ( !empty( $date ) ){
									$date = explode(" ", $date);
									foreach ($date as $key => $value) {
										array_push( $meta_date_arr, "<span class='date-c'>".$value."</span>" );
									}						
								}
								array_push( $meta_date_arr, "</div>" );
								
								$meta_date = !empty( $meta_date_arr ) ? implode( " ", $meta_date_arr ) : "";
								if ( !empty( $meta_date ) ){
									echo "<div class='meta_date'>";
										echo sprintf("%s", $meta_date);
									echo "</div>";		
								}
							?>
							</div>
						</div>
					<?php				
					echo "<div class='pic'>";

					echo "<img src='".esc_url($thumbnail)."' alt='" . get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) . "' />";
					echo "<div class='hover-effect'></div>";
					echo "<a class='fancy post_media_link post_post_media_link posts_grid_post_media_link' href='".esc_url($thumbnail)."'></a>";
				echo '</div>';
				$thumb_media = true;
				$some_media = true;
				
			} ?>
			</div>
			<?php
		$some_media ? ob_end_flush() : ob_end_clean();
		// ================/MEDIA PART================
		$media_content = ob_get_clean();

		$section_class = "post_info_part";
		$section_class .= empty( $media_content ) ? " full_width" : '';
		$header_class = "post_info_header";
		$post_format = get_post_format();
		$header_class .= ( in_array( $post_format, array( 'quote', 'audio' ) ) || ( $post_format == 'link' && !has_post_thumbnail() ) ) ? " rounded" : '';
		if(! is_single()){
			if (!empty($media_content)) {		
				?><div class="<?php echo esc_attr($section_class); ?>"><?php echo sprintf("%s", $media_content);?></div><?php			
			}			
		}


			// ================TITLE PART================
			if ($is_single){
				$title_part = $title;			
			} else {
				$title_part = "<h3><a href='$permalink'>". $title ."</a></h3>";
			}
			if(!is_single()){
				echo !empty( $title ) ?	'<div class="ce_title"><div>' . $title_part . '</div></div>' : '';
			}
			
			// ================/TITLE PART================


		if( is_single()){
			if (!empty($media_content)) {		
				?><div class="<?php echo esc_attr($section_class); ?>"><?php echo sprintf("%s", $media_content);?></div><?php			
			}			
		}
		
		echo '<div class="post_info">';
					// ================AUTHOR PART================
		$author = '';
		$author .= $show_author ? esc_html(get_the_author()) : '';
		ob_start();
		the_author_posts_link();
		$author_link = ob_get_clean();

		if ( !empty($author) ) {
			echo "<div class='info'>";
			ob_start();
			echo !empty($author) ? (esc_html__('by ', 'aasana'))."<span class='post_author'>$author_link</span>" : '';
			$author_part = ob_get_clean();
			echo sprintf("%s", $author_part);
			echo '</div>';
		}
					// ================/AUTHOR PART================

					// ================COMMENTS PART================
		$comments_n = get_comments_number();
		if ( (int) $comments_n > 0 ) {
			$permalink .= "#comments";
			echo "<div class='comments_link'><a href='$permalink'><i class='fa fa-comment-o'></i> $comments_n</a></div>";
			$comments_part = "<a href='$permalink'>$comments_n <span> ".esc_html__('comments', 'aasana')."</span></a>";
		}
					// ================/COMMENTS PART================
		echo '</div>';


		// ================CONTENT PART================
		global $post;
		global $more;
		$old_version = 0;
		$more = 0;
		$content = '';
		$button_word = '';
		$button_add = false;

		if ( is_single() ) {
			if(strpos( (string) $post->post_content, '<!--more-->' )){
				$content .= apply_filters('the_content', $post->post_content);
			}
			else{
				$content .= apply_filters('the_content', get_the_content());
			}
		} else {
			if ( ! empty( $post->post_excerpt ) ) {
				$content .= $post->post_excerpt;
			} else {
				$button_word = esc_html__( 'Read More', 'aasana' );
				$pos = strpos( (string) $post->post_content, '<!--more-->' );
				if ( $pos ) {
					$button_add = true;
				}
				$content .= get_the_content( '[...]' );
			}
		}

		if (! empty( $content ) ) {
			echo "<div class='post_content clearfix'>" . apply_filters( 'the_content', $content );
			if ( $button_add ) {
				echo "<div class='button_cont'><a href='".esc_url( get_the_permalink() )."' class='cws_button read-more regular'>" . $button_word . '</a></div>';
			}
			echo '</div>';
		}else{
			if ( $button_add ) {
				echo "<div class='button_cont'><a href='".esc_url( get_the_permalink() )."' class='cws_button read-more icon-on regular'><i class='button-icon fa fa-link'></i>" . $button_word . '</a></div>';
			}
		}		

		$args = array(
			'before'		   => '',
			'after'			=> '',
			'link_before'	  => '<span>',
			'link_after'	   => '</span>',
			'next_or_number'   => 'number',
			'nextpagelink'	 =>  esc_html__("Next Page",'aasana'),
			'previouspagelink' => esc_html__("Previous Page",'aasana'),
			'pagelink'		 => '%',
			'echo'			 => 0
		);
		$pagination = wp_link_pages( $args );
		echo !empty( $pagination ) ? "<div class='pagination'><div class='page_links'>$pagination</div></div>" : '';
		// ================/CONTENT PART================

		// ================META PART================
			echo "<hr>";
			echo "<div class='post_meta'>";			
				if ( has_category() ) {
					echo "<div class='post_category'>";						
						$category_part = the_category (' ');
						echo sprintf("%s",$category_part );						
					echo '</div>';
				}
		
				if ( has_tag() ) {
					echo "<div class='post_tags'>";							
						$tags_part = the_tags ("", " ", "" );		
						echo sprintf("%s", $tags_part);		
					echo '</div>';
				}				
			echo '</div>';	

	}
	public function cws_register_default() {
		//Defaults Google fonts
		$url = $query_args = '';

		$fonts_opts = array(
			array(
			    'font-family' => 'PT Sans',
			    'font-weight' => array('regular','italic','700','700italic'),
			    'font-sub' => array('latin'),
			    'font-type' => '',
			    'color' => '#707273',
			    'font-size' => '18px',
			    'line-height' => '28px',
			),			
			array(
			    'font-family' => 'PT Sans',
			    'font-weight' => array('regular','italic','700','700italic'),
			    'font-sub' => array('latin'),
			    'font-type' => '',
			    'color' => '#707273',
			    'font-size' => '18px',
			    'line-height' => '35px',
			),
			array(
    			'font-family' => 'Poppins',
			    'font-weight' => array('300','regular','500','600','700'),
			    'font-sub' => array('latin'),
			    'font-type' => '',
			    'color' => '#7b6cd5',
			    'font-size' => '48px',
			    'line-height' => '36px',
			),					
			array(
				 'font-family' => 'Lato',
			    'font-weight' => array('300'),
			    'font-sub' => array('latin'),
			    'font-type' => '',
			    'color' => '#be9656',
			    'font-size' => '26px',
			    'line-height' => '36px',
			),	
		);		

		if ( !empty( $fonts_opts ) ) {
			$fonts_urls = array( count( $fonts_opts ) );
			$subsets_arr = array();
			$base_url = "//fonts.googleapis.com/css";

			for ( $i = 0; $i < count( $fonts_opts ); $i++ ){
				$fonts_urls[$i] = $fonts_opts[$i]['font-family'];
				$fonts_urls[$i] .= !empty( $fonts_opts[$i]['font-weight'] ) ? ':' . implode( ',', $fonts_opts[$i]['font-weight'] ) : '';
				if(!empty($fonts_opts[$i]['font-sub'])){
					for ( $j = 0; $j < count( $fonts_opts[$i]['font-sub'] ); $j++ ){
						if ( !in_array( $fonts_opts[$i]['font-sub'][$j], $subsets_arr ) ){
							array_push( $subsets_arr, $fonts_opts[$i]['font-sub'][$j] );
						}
					}
				}
			}
			$query_args = array(
				'family'	=> urlencode( implode( '|', $fonts_urls ) )
			);
			if ( !empty( $subsets_arr ) ) {
				$query_args['subset']	= urlencode( implode( ',', $subsets_arr ) );
			}
			$url = add_query_arg( $query_args, $base_url );
		}
		wp_enqueue_style( '', $url );

		// Scripts
		wp_enqueue_script("jquery");
		wp_register_script('cws_scripts', AASANA_URI . '/js/scripts.js', array( 'jquery' ) );
		wp_register_script('isotope', AASANA_URI . '/js/isotope.pkgd.min.js', array( 'jquery' ) );
		wp_register_script('fancybox', AASANA_URI . '/js/jquery.fancybox.js', array( 'jquery' ) );
		wp_register_script('select2_main', AASANA_URI . '/js/select2.min.js', array( 'jquery' ) );
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );		

		wp_enqueue_script('cws_scripts');
		wp_enqueue_script('isotope');
		wp_enqueue_script('fancybox');
		wp_enqueue_script('select2_main');

		wp_add_inline_script('cws_scripts', '
			var sticky_menu_enable = false,'.
			'page_loader = false,'.
			'stick_menu = false,'.
			'sticky_menu_mode = false,'.
			'sticky_on_mobile = false,'.
			'animation_curve_scrolltop = "easeInOutQuad",'.
			'sticky_sidebars = false;'
		);

		// Style
		wp_register_style( 'reset', AASANA_URI . '/css/reset.css' );
		wp_register_style( 'layout', AASANA_URI . '/css/layout.css' );
		wp_register_style( 'cws_font_awesome', AASANA_URI . '/css/font-awesome.css' );
		wp_register_style( 'cws-iconpack', AASANA_URI . '/fonts/cws-iconpack/flaticon.css' );
		wp_register_style( 'cws_main', AASANA_URI . '/css/main.css' );
		wp_register_style( 'fancybox', AASANA_URI . '/css/jquery.fancybox.css' );
		wp_register_style( 'cws_default', AASANA_URI . '/css/default.css' );
		wp_register_style( 'flaticon', AASANA_URI . '/fonts/flaticon/flaticon.css'  );
		wp_register_style( 'select2_main', AASANA_URI . '/css/select2.css'  );

		wp_enqueue_style( 'reset' );
		wp_enqueue_style( 'layout' );
		wp_enqueue_style( 'cws_font_awesome' );
		wp_enqueue_style( 'cws-iconpack' );
		wp_enqueue_style( 'select2_main' );
		wp_enqueue_style( 'cws_main' );
		wp_enqueue_style( 'fancybox' );
		wp_enqueue_style( 'cws_default' );

		wp_enqueue_style( 'flaticon' );
	}

	/* Some useful functions */
	public function cws_get_option($name) {
		// !!! this must be in superclass
		$ret = null;
		if (is_customize_preview()) {
			global $cwsfw_settings;
			if (isset($cwsfw_settings[$name])) {
				$ret = $cwsfw_settings[$name];
				if (is_array($ret)) {
				$theme_options = get_option( self::$text_domain );
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
		$theme_options = get_option( self::$text_domain );
		$ret = isset($theme_options[$name]) ? $theme_options[$name] : null;
		$ret = stripslashes_deep( $ret );
		return $ret;
	}
	public function cws_after_setup_theme() {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support(' widgets ');
		add_theme_support( 'title-tag' );

		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
		add_theme_support( 'post-formats', self::$cws_theme_config['post-formats'] );
		$nav_menus = self::$cws_theme_config['nav-menus'];
		foreach ($nav_menus as $key => $value) {
			register_nav_menu( $key, $value );
		}
		add_theme_support( 'woocommerce' );
		add_theme_support( 'custom-background', array('default-color' => '616262') );

		// Add Gutenberg Compatibility
		add_theme_support( 'align-wide' );


		$user = wp_get_current_user();
		$user_nav_adv_options = get_user_option( 'managenav-menuscolumnshidden', get_current_user_id() );
		if ( is_array($user_nav_adv_options) ) {
			$css_key = array_search('css-classes', $user_nav_adv_options);
			if (false !== $css_key) {
				unset($user_nav_adv_options[$css_key]);
				update_user_option($user->ID, 'managenav-menuscolumnshidden', $user_nav_adv_options,	true);
			}
		}

		add_editor_style();
	}
	
	private function assign_constants() {
		self::$cws_theme_config = array(

			'alt_breadcrumbs' => array('yoast_breadcrumb' => array( '<nav class="bread-crumbs">', '</nav>', false)), // alternative breadcrumbs function and its arguments
			'post-formats' => array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ),
			'nav-menus' => array(
				'header-menu' => esc_html__( 'Navigation Menu','aasana' ),
				'sidebar-menu' => esc_html__( 'SidePanel Menu', 'aasana'),
				'topbar-menu' => esc_html__( 'TopBar Menu', 'aasana' ),
				'copyrights-menu' => esc_html__( 'Copyrights Menu', 'aasana' )
			),		
			'sideBar' => array(
				'Footer',
				'Blog Right',
				'Blog Left',
				'Page Right',
				'Page Left',
				'Side Panel',
				'WooCommerce',
				'Portfolio Left',
				'Portfolio Right',
			),
			'category_colors' => array('567dbe', 'be5656', 'be9656', '62be56', 'be56b1', '56bebd'),
			'admin_pages' => array('widgets.php', 'edit-tags.php', 'term.php', 'user-edit.php', 'profile.php', 'nav-menus.php'), // pages cwsfw should be initialized on
		);
	} // self::$cws_theme_config

	public function cws_widgets_init() {
		if (function_exists('register_sidebars')) {
			foreach (self::$cws_theme_config['sideBar'] as $sb) {
				register_sidebar( array(
					'name'          => sprintf(__('%s','aasana' ), $sb ),
					'id' => strtolower(preg_replace("/[^a-z0-9\-]+/i", "_", $sb)),
					'description'   => '',
					'class'         => '',
					'before_widget' => '<div class="cws-widget">',
					'after_widget'  => '</div>',
					'before_title'  => '<div class="widget-title">',
					'after_title'   => '</div>',
				) );
			}
		}
	}
	
	public function cws_get_special_post_formats() {
		return array( 'aside' );
	}
	
	public function cws_is_special_post_format() {
		global $post;
		$sp_post_formats = $this->cws_get_special_post_formats();
		if ( isset($post) ) {
			return in_array( get_post_format(), $sp_post_formats );
		} else{
			return false;
		}
	}

	public function cws_page_links() {
		$args = array(
			'before'		   => '',
			'after'			=> '',
			'link_before'	  => '<span>',
			'link_after'	   => '</span>',
			'next_or_number'   => 'number',
			'nextpagelink'	 =>  esc_html__("Next Page",'aasana'),
			'previouspagelink' => esc_html__("Previous Page",'aasana'),
			'pagelink'		 => '%',
			'echo'			 => 0
		);
		$pagination = wp_link_pages( $args );
		echo !empty( $pagination ) ? "<div class='pagination'><div class='page_links'>$pagination</div></div>" : '';
	}
	private function cws_is_woo() {
		global $woocommerce;
		
		return !empty( $woocommerce ) ? is_woocommerce() || is_product_tag() || is_product_category() || is_account_page() || is_cart() || is_checkout() : false;
	}

	public function is_blog () {
		global  $post;
		$posttype = get_post_type($post );
		return ( ((is_archive()) || (is_author()) || (is_category()) || (is_home()) || (is_single()) || (is_tag())) && ( $posttype == 'post')  ) ? true : false ;
	}


	public function cws_site_header(){
		ob_start();
		$page_title_section_atts = "";
		$show_breadcrumbs = true;
		$page_title_section_class = "page_title default_page_title";
		$page_title_section_atts .= !empty( $page_title_section_class ) ? " class='$page_title_section_class'" : "";

		$text['home']	 = esc_html__( 'Home', 'aasana' ); // text for the 'Home' link
		$text['category'] = esc_html__( 'Category "%s"', 'aasana' ); // text for a category page
		$text['search']   = esc_html__( 'Search for "%s"', 'aasana' ); // text for a search results page
		$text['taxonomy'] = esc_html__( 'Archive by %s "%s"', 'aasana' );
		$text['tag']	  = esc_html__( 'Posts Tagged "%s"', 'aasana' ); // text for a tag page
		$text['author']   = esc_html__( 'Articles Posted by %s', 'aasana' ); // text for an author page
		$text['404']	  = esc_html__( 'Error 404', 'aasana' ); // text for the 404 page
		$text['cart']	  = esc_html__( 'Cart', 'aasana' ); // text for the cart page
		$text['checkout']	  = esc_html__( 'Checkout', 'aasana' ); // text for the checkout page

		$page_title = "";

		if ( is_404() ) {
			$page_title = esc_html__( '404 Page', 'aasana' );
		}
		else if ( is_search() ) {
			$page_title = esc_html__( 'Search', 'aasana' );
		} else if ( is_front_page() ) {
			$page_title = esc_html__( 'Home', 'aasana' );
		} else if ( is_category() ) {
			$cat = get_category( get_query_var( 'cat' ) );
			$cat_name = isset( $cat->name ) ? $cat->name : '';
			$page_title = sprintf( $text['category'], $cat_name );
		} else if ( is_tag() ) {
			$page_title = sprintf( $text['tag'], single_tag_title( '', false ) );
		} else if ( is_day() ) {
			echo sprintf( $link, get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) ) . " ";
			echo sprintf( $link, get_month_link( get_the_time( 'Y' ),get_the_time( 'm' ) ), get_the_time( 'F' ) ) . " ";
			$page_title = get_the_time( 'd' );

		} else if ( is_month() ) {
			$page_title = get_the_time( 'F' );

		} else if ( is_year() ) {
			$page_title = get_the_time( 'Y' );

		} else if ( has_post_format() && ! is_singular() ) {
			$page_title = get_post_format_string( get_post_format() );
		} else if ( is_tax( array( 'cws_portfolio_cat', 'cws_staff_member_department', 'cws_staff_member_position' ) ) ) {
			$tax_slug = get_query_var( 'taxonomy' );
			$term_slug = get_query_var( $tax_slug );
			$tax_obj = get_taxonomy( $tax_slug );
			$term_obj = get_term_by( 'slug', $term_slug, $tax_slug );

			$singular_tax_label = isset( $tax_obj->labels ) && isset( $tax_obj->labels->singular_name ) ? $tax_obj->labels->singular_name : '';
			$term_name = isset( $term_obj->name ) ? $term_obj->name : '';
			$page_title = $singular_tax_label . ' ' . $term_name ;
		} else if ( is_archive() ) {
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );
			$post_type_name = isset( $post_type_obj->label ) ? $post_type_obj->label : '';
			$page_title = $post_type_name ;
		} else if ( $this->cws_is_woo() ) {
			$page_title = woocommerce_page_title( false );
		} else if (get_post_type() == 'cws_portfolio') {
			$portfolio_slug = $this->cws_get_option('portfolio_slug');
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );
			$post_type_name = isset( $post_type_obj->labels->menu_name ) ? $post_type_obj->labels->menu_name : '';
			$page_title = !empty($portfolio_slug) ? $portfolio_slug : $post_type_name ;
		}else if (get_post_type() == 'cws_staff') {
			$stuff_slug = $this->cws_get_option('staff_slug');
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );
			$post_type_name = isset( $post_type_obj->labels->menu_name ) ? $post_type_obj->labels->menu_name : '';
			$page_title = !empty($stuff_slug) ? $stuff_slug : $post_type_name ;
		}else {
			$blog_title = $this->is_blog() ? get_the_title() : "";
			$page_title = (!is_page() && !empty($blog_title)) ? $blog_title : get_the_title();
		}
		$breadcrumbs = "";
		if ( $show_breadcrumbs ){
			ob_start();
			aasana_dimox_breadcrumbs();
			$breadcrumbs = ob_get_clean();		
		}

		$page_title = esc_html($page_title);

		if ( !empty( $page_title ) || (!empty( $breadcrumbs ) && $show_breadcrumbs ) ){
			echo "<section" . ( !empty( $page_title_section_atts ) ? $page_title_section_atts : "" ) . ">";
			echo "<div class='container header_center' style='padding-top:120px;padding-bottom:120px;'>";
			echo '<span class="logo_breadcrumbs svg_lotus">';
			echo '<span class="cws_logotype_svg" style="width:121px;height:62px">';
			echo '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			viewBox="0 0 140 70" style="enable-background:new 0 0 140 70;" xml:space="preserve">
			<path class="st55" d="M70.3,35.4c0.6-0.6,1.2-1.3,1.8-1.9c5.1-5.1,11.1-8.7,17.5-11c-4.1-8.7-10.9-16-19.3-20.9
			C61.9,6.4,55.2,13.7,51,22.5c6.4,2.3,12.3,5.9,17.5,11C69.1,34.1,69.7,34.7,70.3,35.4z"/>
			<path class="st54" d="M137.8,68.7c-4.9-8.3-12.2-15-21-19.1c-2.3,6.3-6,12.3-11.1,17.3c-0.6,0.6-1.3,1.2-1.9,1.8"/>
			<path class="st54" d="M36.8,68.7c-0.7-0.6-1.3-1.2-1.9-1.8c-5.1-5.1-8.8-11-11.1-17.3C15,53.6,7.6,60.3,2.8,68.7"/>
			<path class="st55" d="M46.6,45.1c0-0.9-0.1-1.8-0.1-2.6c0-7.2,1.6-13.9,4.5-20c-9.2-3.3-19.1-3.7-28.5-1.2
			c-2.5,9.3-2.1,19.2,1.2,28.3c6.1-2.9,13-4.5,20.2-4.5C44.8,45.1,45.7,45.1,46.6,45.1z"/>
			<path class="st55" d="M61.9,48.6c-4.8-1.9-9.9-3.1-15.3-3.4c0.3,5.4,1.5,10.5,3.4,15.2c2.9-1.2,6-2.2,9.1-2.8
			C59.7,54.5,60.7,51.5,61.9,48.6z"/>
			<path class="st54" d="M54.5,68.7c-1.8-2.6-3.2-5.4-4.5-8.3c-4.7,2-9.2,4.8-13.3,8.3"/>
			<path class="st55" d="M94,45.1c0.9,0,1.8-0.1,2.7-0.1c7.2,0,14,1.6,20.2,4.5c3.3-9.1,3.7-19,1.2-28.3c-9.4-2.5-19.3-2.1-28.5,1.2
			c2.9,6.1,4.5,12.9,4.5,20C94.1,43.4,94,44.3,94,45.1z"/>
			<path class="st55" d="M90.5,60.3c1.9-4.7,3.1-9.8,3.4-15.2c-5.4,0.3-10.5,1.5-15.3,3.4c1.2,2.9,2.2,5.9,2.8,9
			C84.6,58.2,87.6,59.1,90.5,60.3z"/>
			<path class="st54" d="M103.8,68.7c-4-3.6-8.5-6.4-13.3-8.3c-1.2,2.9-2.7,5.7-4.4,8.3"/>
			<path class="st55" d="M78.7,48.6c-2-4.7-4.8-9.2-8.4-13.2c-3.6,4-6.4,8.4-8.4,13.2c3,1.2,5.8,2.7,8.4,4.4
			C72.9,51.2,75.7,49.8,78.7,48.6z"/>
			<line class="st55" x1="70.3" y1="35.4" x2="70.3" y2="1.6"/>
			<line class="st55" x1="94" y1="45.1" x2="118" y2="21.3"/>
			<line class="st55" x1="46.6" y1="45.1" x2="22.6" y2="21.3"/>
			</svg>
			';			
			echo '</span>';	
			echo '</span>';

			echo !empty( $page_title ) ? "<div class='title'><h1>$page_title</h1></div>" : "";
			echo (!empty( $breadcrumbs ) && $show_breadcrumbs) ? $breadcrumbs : "";
			echo "</div>";
			echo "</section>";
		}

		$page_title_content = ob_get_clean();
		if($page_title_content){
			echo sprintf("%s", $page_title_content);
		}
	}
	public function cws_theme_woo_setup(){
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );		
			add_theme_support( 'wc-product-gallery-slider' );		
	}
	public function loop_products_per_page() {
		
		return 10;
	}
}


// CWS PB settings
class Aasana_Funcs {
	public static $text_domain = 'aasana';

	protected static $height_to_width_ratio = 0.78;

	protected static $cws_theme_config;

	public static $options;

	protected static $flags = 0;

	public $templates;

	protected static $to_exists = false;

	protected static $blog_thumb_dims = array(
		'large' => array(
			'none' => array(1170, 659),
			'left' => array(870, 490),
			'right' => array(870, 490),
			'both' => array(570, 321),
			),
		'medium' => array(
			'none' => array(570, 321),
			'left' => array(570, 321),
			'right' => array(570, 321),
			'both' => array(570, 321),
			),
		'small' => array(
			'none' => array(370, 208),
			'left' => array(370, 208),
			'right' => array(370, 208),
			'both' => array(370, 208),
			),
		'checkerboard' => array(
			'none' => array(585, 208),
			'left' => array(415, 245),
			'right' => array(415, 245),
			'both' => array(570, 321),
			),
		'1' => array(
			'none' => array(1170, 659),
			'left' => array(870, 490),
			'right' => array(870, 490),
			'both' => array(570, 321),
			),
		'2' => array(
			'none' => array(570, 321),
			'left' => array(420, 237),
			'right' => array(420, 237),
			'both' => array(270, 152),
			),
		'3' => array(
			'none' => array(370, 208),
			'left' => array(270, 152),
			'right' => array(270, 152),
			'both' => array(270, 152),
			),
		'4' => array(
			'none' => array(270, 152),
			'left' => array(270, 152),
			'right' => array(270, 152),
			'both' => array(270, 152),
			),
	);

	const THEME_BEFORE_CE_TITLE = '<div class="ce_title">';
	const THEME_AFTER_CE_TITLE = '</div>';
	const THEME_V_SEP = '<span class="v_sep"></span>';

	public function __construct() {
		$this->header = array(
			'drop_zone_start' => '',
			'drop_zone_end' => '</div><!-- /header_zone -->',
			'before_header' => '',
			'top_bar_box' => '',
			'logo_box' => '',
			'menu_box' => '',
			'header_box' => '',
			'after_header' => '',
		);

		// Check if JS_Composer is active
		if (class_exists('Vc_Manager')) {
			$vc_man = Vc_Manager::getInstance();
			$vc_man->disableUpdater(true);
			if (!isset($_COOKIE['vchideactivationmsg_vc11'])) {
				setcookie('vchideactivationmsg_vc11', WPB_VC_VERSION);
			}
			require_once( get_template_directory() . '/vc/cws_vc_config.php' ); // JS_Composer Theme config file
		}

		global $wpdb;
		$to_len = (int)$wpdb->get_var( sprintf('SELECT LENGTH(option_value) FROM '.$wpdb->prefix.'options WHERE option_name = "%s"', self::$text_domain) );
		self::$to_exists = $to_len > 0;

		$this->assign_constants();
		$this->init();
		$this->cws_customizer_init();
		
	}

	private function cws_read_options() {
		global $wp_query;
		$pid = get_the_id();

		$theme_options = get_option(self::$text_domain);
		if (empty($theme_options)) return;

		$besides_ooptions = is_search();
		if ($pid && ! $besides_ooptions) {
			$meta = $this->cws_get_post_meta($pid);
			if (!empty($meta)) {
				$meta = $meta[0];
				foreach ($theme_options as $key => $value) {
					if (!isset($meta[$key]) || ( isset($meta[$key]['layout']) && '{' === substr($meta[$key]['layout'], 0, 1) ) ) {
						$meta[$key] = $value;
					}
				}
			} else {
				$meta = $theme_options;
			}
			self::$options = $meta;
		} else {
			self::$options = $theme_options;
		}
	}
	private function assign_constants() {
		self::$cws_theme_config = array(
			'js_path' => get_template_directory_uri() .'/js/',
			'scripts' => array(
				'header' => array(
					'fancybox' => 'jquery.fancybox.js',
					'select2_init' => 'select2.min.js',
					'cws_scripts' => 'scripts.js',
					'fixed_sidebars' => 'sticky_sidebar.js',
					'tweenmax' => 'tweenmax.min.js',
				),
				'footer' => array(
					'owl_carousel' => 'owl.carousel.js',
					'isotope' => 'isotope.pkgd.min.js',
					'odometer' => 'odometer.js',
					'wow' => 'wow.min.js',
					'parallax' => 'parallax.js',
					'vimeo' => 'jquery.vimeo.api.min.js',
					'skrollr' => 'skrollr.min.js',
					'modernizr' => array( '0' != $this->cws_get_option('enable_mob_menu'),'modernizr.js', null),
					'yt_player_api' => 'https://www.youtube.com/player_api',
				),
			),
			'css_path' => get_template_directory_uri() . '/css/',
			'styles' => array(
				'reset' => 'reset.css',
				'layout' => 'layout.css',
				'cws_font_awesome' => 'font-awesome.css',
				'cwsfi' => array('cws_is_cwsfi', null),
				'cws-iconpack' => get_template_directory_uri() . '/fonts/cws-iconpack/flaticon.css',
				'fancybox' => 'jquery.fancybox.css',
				'select2_init' => 'select2.css',
				'animate' => 'animate.css',
			),
			'actions' => array(
				'cws_is_flaticon' => '',
				'cws_is_cwsfi' => '',
				),
			'gfonts' => array('body', 'menu', 'header', 'helper'), // body-font etc from theme options
			'def_char_number' => 155, // cws_blog_get_chars_count
			'char_counts' => array( // keys are columns
				array(
					'double' => 130,
					'single' => 200,
					'' 		 => 300
				), // empty dummy array
				array(
					'double' => 130,
					'single' => 200,
					'' 		 => 300,
				),
				array(
					'double' => 120,
					'single' => 140,
					''			 => 150,
				),
				array(
					'double' => 60,
					'single' => 80,
					''			 => 90,
				),
				array(
					'double' => 50,
					'single' => 70,
					''			 => 100,
				),
			),
			'strings' => array(
				'home' => esc_html__( 'Home','aasana'), // text for the 'Home' link
				'category' => esc_html__( 'Category "%s"','aasana' ), // text for a category page
				'search' => esc_html__( 'Search for "','aasana' ) .(isset($_GET['s']) ? $_GET['s'] : "") . '"', // text for a search results page
				'taxonomy' => esc_html__( 'Archive by %s "%s"', 'aasana'),
				'tag'	=> esc_html__( 'Posts Tagged "%s"','aasana' ), // text for a tag page
				'author' => esc_html__( 'Articles Posted by %s','aasana' ), // text for an author page
				'404' => esc_html__( 'Error 404','aasana' ),
				'cart' => esc_html__( 'Cart','aasana' ),
				'checkout' => esc_html__( 'Checkout','aasana' ),
			),
			'alt_breadcrumbs' => array('yoast_breadcrumb' => array( '<nav class="bread-crumbs">', '</nav>', false)), // alternative breadcrumbs function and its arguments
			'post-formats' => array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ),
			'nav-menus' => array(
				'header-menu' => esc_html__( 'Navigation Menu','aasana' ),
				'sidebar-menu' => esc_html__( 'SidePanel Menu', 'aasana'),
				'topbar-menu' => esc_html__( 'TopBar Menu', 'aasana' ),
				'copyrights-menu' => esc_html__( 'Copyrights Menu', 'aasana' )
			),
			'category_colors' => array('567dbe', 'be5656', 'be9656', '62be56', 'be56b1', '56bebd'),
			'admin_pages' => array('widgets.php', 'edit-tags.php', 'term.php', 'user-edit.php', 'profile.php', 'nav-menus.php'), // pages cwsfw should be initialized on
		);
	} // self::$cws_theme_config

	public function get_theme_config($name) {
		if (isset(self::$cws_theme_config[$name])) {
			return self::$cws_theme_config[$name];
		}
		return null;
	}

	public function cws_customizer_init() {
		if ( is_customize_preview() ) {
			if ( isset( $_POST['wp_customize'] ) && $_POST['wp_customize'] == "on" ) {
				if (strlen($_POST['customized']) > 10) {
					global $cwsfw_settings;
					global $cwsfw_mb_settings;
					$post_values = json_decode( stripslashes_deep( $_POST['customized'] ), true );
					if (isset($post_values['cwsfw_settings'])) {
						$cwsfw_settings = $post_values['cwsfw_settings'];
					}
					if (isset($post_values['cwsfw_mb_settings'])) {
						$cwsfw_mb_settings = $post_values['cwsfw_mb_settings'];
						$this->cws_meta_vars();
					}
				}
			}
		}
	}
	/* Woo Related functions */
	public function cws_getWooMiniCart() {
		ob_start();
		if ( class_exists( 'woocommerce' ) ) {	woocommerce_mini_cart(); }
		return ob_get_clean();
	}

	public function cws_getWooMiniIcon() {
		ob_start();
		if ( class_exists( 'woocommerce' ) ) { ?>
			<a class="woo_icon" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_html_e( 'View your shopping cart','aasana' ); ?>"><i class='woo_mini-count flaticon-shopcart-icon'><?php echo ((WC()->cart->cart_contents_count > 0) ?  '<span>' . esc_html( WC()->cart->cart_contents_count ) .'</span>' : '') ?></i></a>
		<?php
		}
		return ob_get_clean();
	}

	/* /Woo Related functions */

	/* Some useful functions */
	public function cws_get_option($name) {
		// !!! this must be in superclass
		$ret = null;
		if (is_customize_preview()) {
			global $cwsfw_settings;
			if (isset($cwsfw_settings[$name])) {
				$ret = $cwsfw_settings[$name];
				if (is_array($ret)) {
				$theme_options = get_option( self::$text_domain );
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
		$theme_options = get_option( self::$text_domain );
		$ret = isset($theme_options[$name]) ? $theme_options[$name] : null;
		$ret = stripslashes_deep( $ret );
		return $ret;
	}

	public function cws_get_meta_option($name = '', $check_first_key = false) {
		$value = isset(self::$options[$name]) ? self::$options[$name] : null;
		while (is_string($value) && '{' === substr($value, 0, 1)) {
			$g_name = substr($value, 1, -1);
			$value = isset(self::$options[$g_name]) ? self::$options[$g_name] : null;
		}
		if ($check_first_key && is_array($value) && !empty($value)) {
			// it's better to set $check_first_key specifically when there's a chance
			// like in case of sidebars processing
			// check if need to replace value with theme option array
			reset($value);
			$first_key = key($value);
			$val = $value[$first_key];
			if (is_string($val) && '{' === substr($val, 0, 1)) {
				$g_name = substr($val, 1, -1);
				$value = isset(self::$options[$g_name]) ? self::$options[$g_name] : null;
			}
		}
		return $value;
	}

	public function cws_get_post_meta($pid, $key = 'cws_mb_post') {
		$ret = get_post_meta($pid, $key);
		if (!empty($ret[0])) {
			$ret = $ret[0];
		}
		if (is_customize_preview()) {
			global $cwsfw_settings;
			global $cwsfw_mb_settings;
			if(!empty($cwsfw_settings)){
				$ret = array_merge($ret, $cwsfw_settings);
			}
			if (!empty($cwsfw_mb_settings) && !empty($ret)) {
				$ret = array_merge($ret, $cwsfw_mb_settings);
			} else if (!empty($cwsfw_mb_settings) && empty($ret)) {
				$ret = $cwsfw_mb_settings;
			}
		}

		$ret = array($ret);

		return $ret;
	}

	public function cws_print_border_box($box) {
		$out = $type_color = '';
		if ( !empty($box) && !empty($box['border']) ){
			if(isset($box['border_type']) && isset($box['border_color'])){
				$type_color = sprintf('%s %s', $box['border_type'], $box['border_color']);
			}
			
			if(isset($box['border']) && is_array($box['border'])){
				foreach ($box['border'] as $key => $value) {
					$out .= "border-{$value}:1px {$type_color};";
				}				
			}
		}
		return $out;
	}

	public function cws_print_img_html($img, $img_args, &$img_height = null) {
		$src = '';
		$img_h = 0;
		$pid = '';

		if ($img && !is_array($img) ) {
			$pid = $img;
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
			$pid = $img['id'];
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
			if ( empty($img_args['width']) && empty($img_args['height']) ) {
				if (isset($img['width']) && isset($img['height'])) {
					$img_args = array(
						'width' => floor( (int) $img['width'] ),
						'height' => floor( (int) $img['height'] ),
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
		}
		$src = $src . " alt = '" . get_post_meta( $pid , '_wp_attachment_image_alt', true ) . "'";
		if ($img_height) {
			$img_height = $img_h;
		}
		return $src;
	}	
	public function cws_print_svg_html($img, $img_args, &$img_height = null) {

		$svg = '';
		if ( !empty($img_args['width']) && !empty($img_args['height']) ) {
			$svg .= "<span class='cws_logotype_svg' style='width:{$img_args['width']}px;height:{$img_args['height']}px'>";
		}
		if(!empty($img['src'])){
			global $wp_filesystem;
			WP_Filesystem();
			$upload_dir = wp_upload_dir();
			$file_parts = pathinfo($img['src']);	
			$dir = str_replace($upload_dir['baseurl'], "", $file_parts['dirname']);
			$dir = $wp_filesystem->find_folder($upload_dir['basedir'] . $dir);			  		
		    $file = trailingslashit($dir) . $file_parts['basename'];
		    if($wp_filesystem->exists($file)){
		    	$svg .= $wp_filesystem->get_contents($file);
		    }
		}

		if ( !empty($img_args['width']) && !empty($img_args['height']) ) {
			$svg .= "</span>";
		}
		return $svg;
	}

	public function cws_print9positions($pos){
		$bg_pos = '';
		for ($i=0; $i<2;$i++) {
			$c = $pos[$i];
			switch ($c) {
				case 'l':
					$bg_pos .= ' left';
					break;
				case 'r':
					$bg_pos .= ' right';
					break;
				case 'c':
					$bg_pos .= ' center';
					break;
				case 'b':
					$bg_pos .= ' bottom';
					break;
				case 't':
					$bg_pos .= ' top';
					break;
			}
		}
		return trim($bg_pos);
	}

	// !!! this must be in superclass
	public function echo_ne($condition, $str, $str2 = '') {	echo !empty($condition) ? $str : $str2; }

	// !!! this must be in superclass
	public function echo_if($condition, $str, $str2 = '') {if($condition){echo sprintf("%s", $str);}else{echo sprintf("%s", $str2);}
	}

	public function print_if($condition, $str, $str2 = '') { return $condition ? $str : $str2; }

	public function print_ne($condition, $str, $str2 = '') { return !empty($condition) ? $str : $str2; }

	public function cws_print_search_form($message_title = '', $message = '') {
		ob_start();
		echo shortcode_exists('cws_sc_msg_box') ? do_shortcode( "[cws_sc_msg_box type='info' title='{$message_title}' text='{$message}'][/cws_sc_msg_box]" ) : (!empty($message_title) ? "<h3>{$message_title}</h3><p>{$message}</p>" : '');
		get_search_form();
		$sc_content = ob_get_clean();
		return $sc_content;
	}

	/* END of Some useful functions */

	public function cws_render_sidebars($pid) {
		// !!! this must be in superclass
		$out = '';
		$sb = $this->cws_get_sidebars( $pid );
		$layout_class = $sb && $sb['layout_class'] != 'none' ? $sb['layout_class'].'_sidebar' : '';
		$sb1_class = $sb && isset($sb['layout']) && $sb['layout'] == 'right' ? 'sb_right' : 'sb_left';
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

	private function cws_is_woo() {
		global $woocommerce;
		
		return !empty( $woocommerce ) ? is_woocommerce() || is_product_tag() || is_product_category() || is_account_page() || is_cart() || is_checkout() : false;
	}

	public function cws_get_sidebars( $p_id = null ) { /*!*/
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
				case 'cws_classes':
				case 'cws_staff':
					$page_type = 'post';
					break;
			}
		} else if (is_home()) {
			/* default home page have no ID */
			$page_type = 'home';
		}
		$sb = $this->cws_get_meta_option("{$page_type}_sidebars", true);

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

	public function cws_enqueue_script(){
		$scripts = self::$cws_theme_config['scripts'];
		$js_path = self::$cws_theme_config['js_path'];
		wp_enqueue_script("jquery");
		foreach ($scripts as $type => $v) {
			$is_footer = 'footer' === $type;
			foreach ($v as $alias => $value) {
				if (is_array($value)) {
					list($is_load, $path, $dependencies) = $value;
					if ($path) {
						$path = (0 === strrpos($path, 'http')) ? $path : $js_path . $path;
					}
					if (!is_bool($is_load)) {
						$is_load = do_action();
					}
				} else {
					$path = (0 === strrpos($value, 'http')) ? $value : $js_path . $value;
					$dependencies = array();
					$is_load = true;
				}
				if ($is_load) {
					wp_enqueue_script($alias, $path, $dependencies, '1.0', $is_footer);
				}
			}
		}

		wp_enqueue_style( '', $this->cws_render_fonts_url() );
	}

	public function cws_enqueue_styles(){
		$styles = self::$cws_theme_config['styles'];
		$css_path = self::$cws_theme_config['css_path'];

		foreach ($styles as $alias => $value) {
			if (is_array($value)) {
				list($is_load, $path) = $value;
				if ($path) {
					$path = (0 === strrpos($path, 'http')) ? $path : $css_path . $path;
				}
			} else {
				$path = (0 === strrpos($value, 'http')) ? $value : $css_path . $value;
				$is_load = true;
			}
			if ($is_load) {
				wp_enqueue_style($alias, $path);
			}
		}
		
		$this->cws_theme_enqueue_styles();	
		$this->cws_add_style();
	}

	private function cws_render_fonts_url() {
		$url = $query_args = '';
		$gfonts = self::$cws_theme_config['gfonts'];
		$fonts_opts = array();
		foreach ($gfonts as $value) {
			$fonts_opts[] = $this->cws_get_option( $value.'-font' );
		}

		if ( !empty( $fonts_opts ) ) {
			$fonts_urls = array( count( $fonts_opts ) );
			$subsets_arr = array();
			$base_url = "//fonts.googleapis.com/css";

			for ( $i = 0; $i < count( $fonts_opts ); $i++ ){
				$fonts_urls[$i] = $fonts_opts[$i]['font-family'];
				$fonts_urls[$i] .= !empty( $fonts_opts[$i]['font-weight'] ) ? ':' . implode( ',', $fonts_opts[$i]['font-weight'] ) : '';
				if(!empty($fonts_opts[$i]['font-sub'])){
					for ( $j = 0; $j < count( $fonts_opts[$i]['font-sub'] ); $j++ ){
						if ( !in_array( $fonts_opts[$i]['font-sub'][$j], $subsets_arr ) ){
							array_push( $subsets_arr, $fonts_opts[$i]['font-sub'][$j] );
						}
					}
				}
			}
			$query_args = array(
				'family'	=> urlencode( implode( '|', $fonts_urls ) )
			);
			if ( !empty( $subsets_arr ) ) {
				$query_args['subset']	= urlencode( implode( ',', $subsets_arr ) );
			}
			$url = add_query_arg( $query_args, $base_url );
		}
		return $url;
	}

	public function cws_wp_title_filter ( $title_text ) {
		$site_name = get_bloginfo( 'name' );
		return is_home() ? $site_name . " | " . get_bloginfo( 'description' ) : $site_name;
	}

	# UPDATE THEME
	public function cws_check_for_update($transient) {
		if (empty($transient->checked)) { return $transient; }

		$theme_pc = trim($this->cws_get_option('_theme_purchase_code'));
		if (empty($theme_pc)) {
			add_action( 'admin_notices', array($this, 'cws_an_purchase_code') );
		}

		$result = wp_remote_get('http://up.cwsthemes.com/products-updater.php?pc=' . $theme_pc . '&tname=' . self::$text_domain);
		if (!is_wp_error( $result ) ) {
			if (200 == $result['response']['code'] && 0 != strlen($result['body']) ) {
				$resp = json_decode($result['body'], true);
				$h = isset( $resp['h'] ) ? (float) $resp['h'] : 0;
				$theme = wp_get_theme(get_template());
				if (isset($resp['new_version']) && version_compare( $theme->get('Version'), $resp['new_version'], '<' ) ) {
					$transient->response[self::$text_domain] = $resp;
				}
				// request and save plugins info
				$opt_res = wp_remote_get('http://up.cwsthemes.com/plugins/update.php', array( 'timeout' => 1));
				if ( is_array( $opt_res ) && ! is_wp_error( $opt_res ) ) {
					update_option('cws_plugin_ver', array('data' => $opt_res['body'], 'lasttime' => date('U')));
				}
				// end of request and save plugins info
			} else {
				unset($transient->response[self::$text_domain]);
			}
		}
		return $transient;
	}

	// an stands for admin notice
	public function cws_an_purchase_code() {
		$cws_theme = wp_get_theme();
		echo "<div class='update-nag'>" . $cws_theme->get('Name') . esc_html__(' theme notice: Please insert your Item Purchase Code in Theme Options to get the latest theme updates!', 'aasana') .'</div>';
	}
	# \UPDATE THEME

	private function init() {
		global $wp_filesystem;
		if(empty( $wp_filesystem )) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		require_once get_template_directory() . '/core/plugins.php';

		// metaboxes
		require_once(get_template_directory() . '/core/cws_thumb.php');
		include_once(get_template_directory() . '/core/breadcrumbs.php');
		if (function_exists('cws_core_cwsfw_fillMbAttributes')) {
			load_template( trailingslashit( get_template_directory() ) . '/core/scg.php');
			new Aasana_SCG();
		}

		set_transient('update_themes', 48*3600);

		add_action('after_setup_theme', array($this, 'cws_after_setup_theme') );
		add_action( 'init', array($this, 'add_excerpts_to_pages') );

		add_filter('wp_title', array($this, 'cws_wp_title_filter') );
		add_filter('pre_set_site_transient_update_themes', array($this, 'cws_check_for_update') );
		add_action('admin_enqueue_scripts', array($this, 'cws_admin_init' ) );
		add_filter('the_content', array($this, 'fix_shortcodes_autop') );
		
		add_action('wp_enqueue_scripts', array($this, 'cws_enqueue_script') );

		add_action('wp_enqueue_scripts', array($this, 'cws_enqueue_styles') );

		add_action('wp_enqueue_scripts', array($this, 'cws_enqueue_theme_stylesheet'), 999 );
		add_action('widgets_init', array($this, 'cws_widgets_init') );
		add_filter('body_class', array($this, 'cws_layout_class') );

		add_action('menu_font_hook', array($this, 'cws_menu_font_action') );
		add_action('header_font_hook', array($this, 'cws_header_font_action') );
		add_action('body_font_hook', array($this, 'cws_body_font_action') );
		add_action('body_helper_hook', array($this, 'cws_body_helper_action') );

		add_action('theme_color_hook', array($this, 'cws_theme_color_action'), 1);
		add_action('theme_color_hook', array($this, 'cws_theme_rgba_color'), 1);

		//Custom block styles
		add_action('theme_color_hook', array($this, 'cws_custom_sticky_menu_styles_action'), 2);
		add_action('theme_color_hook', array($this, 'cws_custom_header_styles_action'), 3);
		add_action('theme_color_hook', array($this, 'cws_custom_top_bar_styles_action'), 4);
		add_action('theme_color_hook', array($this, 'cws_custom_page_title_styles_action'), 5);
		add_action('theme_color_hook', array($this, 'cws_custom_boxed_layout_styles_action'), 6);
		add_action('theme_color_hook', array($this, 'cws_custom_footer_styles_action'), 7);
		//Custom block styles

		add_action('theme_gradient_hook', array($this, 'cws_theme_gradient_action') );
		add_filter('body_class', array($this, 'cws_gradients_body_class') );
		add_filter('cws_dbl_to_sngl_quotes', array($this, 'cws_dbl_to_sngl_quotes') );

		add_action('wp_enqueue_scripts', array($this, 'cws_js_vars_init') );
		add_action('wp', array($this, 'cws_meta_vars') );
		add_action('template_redirect', array($this, 'cws_ajax_redirect') );
		add_filter('excerpt_length', array($this, 'cws_custom_excerpt_length'), 999 );
		add_action('wp_enqueue_scripts', array($this, 'cws_ajaxurl') );
		add_filter('embed_oembed_html', array($this, 'cws_oembed_wrapper'),10,3);
		add_filter('body_class', array($this, 'cws_loading_body_class') );
		add_filter('wp_list_categories', array($this, 'cws_custom_categories_postcount_filter'));
		add_filter('post_gallery', array($this, 'cws_custom_gallery'), 10, 2);
		add_filter('get_search_form', array($this, 'cws_custom_search'));

		// Add inline style

		add_filter('cws_print_single_class', array($this, 'cws_print_single_class'));

		/* tinymce related */
		add_filter( 'tiny_mce_before_init', array($this, 'cws_tiny_mce_before_init') );
		add_filter( 'mce_buttons_2', array($this, 'cws_mce_buttons_2') );
		/* /tinymce related */

		// comments
		add_filter('preprocess_comment', array($this, 'cws_comment_post'), '', 1);
		add_filter( 'comment_form_fields',array( $this, 'cws_move_comment_field_to_bottom' ) );

		// Add Svg support 
		add_filter('upload_mimes', array($this, 'cc_mime_types'));

		// Check if WPML is active
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( function_exists('wpml_init_language_switcher') ) {
			define('CWS_WPML_ACTIVE', true);
			$GLOBALS['wpml_settings'] = get_option('icl_sitepress_settings');
			global $icl_language_switcher;
		} else {
			define('CWS_WPML_ACTIVE', false);
		}

		define('CWS_WOO_ACTIVE', class_exists( 'woocommerce' ));

		if (CWS_WOO_ACTIVE) {
			add_action( 'wp_ajax_woocommerce_remove_from_cart',array( $this, 'cws_woo_ajax_remove_from_cart' ),1000 );
			add_action( 'wp_ajax_nopriv_woocommerce_remove_from_cart', array( $this, 'cws_woo_ajax_remove_from_cart' ),1000 );

			require_once( get_template_directory() . '/woocommerce/wooinit.php' ); // WooCommerce Shop ini file

			add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'cws_woo_header_add_to_cart_fragment') );
			
			add_filter( 'woocommerce_output_related_products_args', array($this, 'cws_woo_related_products_args') );
			add_action( 'after_setup_theme', array($this, 'cws_theme_woo_setup') );
			add_filter( 'loop_shop_per_page', array( $this, 'loop_products_per_page' ));	
		}
	}

	public function loop_products_per_page() {
		
		return (int) $this->cws_get_option( 'woo_num_products' );
	}

	public function cws_theme_woo_setup(){
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );		
			add_theme_support( 'wc-product-gallery-slider' );		
	}

	public function cc_mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	public function fix_shortcodes_autop($content){
		$array = array (
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']'
		);

		$content = strtr($content, $array);
		return $content;
	}

	public function add_excerpts_to_pages(){
		add_post_type_support( 'page', 'excerpt' );
	}

	public function cws_mce_buttons_2( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	public function cws_blog_get_chars_count( $cols = 0, $p_id = null ) {
		$number = self::$cws_theme_config['def_char_number'];
		$p_id = $p_id ? $p_id : get_queried_object_id();
		$sb = $this->cws_get_sidebars( $p_id );
		$sb_layout = isset( $sb['sb_layout_class'] ) ? $sb['sb_layout_class'] : '';
		$anums = self::$cws_theme_config['char_counts'];
		if ( $cols < count($anums) ) {
			$number = $anums[$cols][$sb_layout];
		}
		return $number;
	}

	public function cws_tiny_mce_before_init( $settings ) {
		$font_array = $this->cws_get_option( 'header-font' );

		$settings['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4';

		$style_formats = array(
		array( 'title' => 'Title', 'block' => 'div', 'classes' => 'ce_title' ),
		array( 'title' => 'Font-size', 'items' => array(
			array( 'title' => '50px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em', 'styles' => array( 'font-size' => '50px' , 'line-height' => '1em') ),
			array( 'title' => '40px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em', 'styles' => array( 'font-size' => '40px' , 'line-height' => '1.2em') ),
			array( 'title' => '30px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em', 'styles' => array( 'font-size' => '30px' , 'line-height' => '1.4em') ),
			array( 'title' => '20px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em', 'styles' => array( 'font-size' => '20px' , 'line-height' => '1.6em') ),
			array( 'title' => '16px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em', 'styles' => array( 'font-size' => '16px' , 'line-height' => '1.75em') ),
			array( 'title' => '14px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em', 'styles' => array( 'font-size' => '14px' , 'line-height' => '1.75em') ),
			)
		),
		array( 'title' => 'margin-top', 'items' => array(
			array( 'title' => '0px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '0' ) ),
			array( 'title' => '10px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '10px' ) ),
			array( 'title' => '15px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '15px' ) ),
			array( 'title' => '20px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '20px' ) ),
			array( 'title' => '25px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '25px' ) ),
			array( 'title' => '30px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '30px' ) ),
			array( 'title' => '40px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '40px' ) ),
			array( 'title' => '50px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '50px' ) ),
			array( 'title' => '60px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-top' => '60px' ) ),
			)
		),
		array( 'title' => 'margin-bottom', 'items' => array(
			array( 'title' => '0px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '0px' ) ),
			array( 'title' => '10px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '10px' ) ),
			array( 'title' => '15px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '15px' ) ),
			array( 'title' => '20px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '20px' ) ),
			array( 'title' => '25px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '25px' ) ),
			array( 'title' => '30px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '30px' ) ),
			array( 'title' => '40px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '40px' ) ),
			array( 'title' => '50px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '50px' ) ),
			array( 'title' => '60px', 'selector' => 'h1,h2,h3,h4,h5,h6,p,span,i,b,strong,em,div', 'styles' => array( 'margin-bottom' => '60px' ) ),
			)
		),
		array( 'title' => 'Underline title', 'items' => array(
			array( 'title' => 'gray line', 'selector' => '.ce_title:not(.und-title.white):not(.und-title.themecolor)', 'classes' => 'und-title gray' ),
			array( 'title' => 'white line', 'selector' => '.ce_title:not(.und-title.themecolor):not(.und-title.gray)', 'classes' => 'und-title white' ),
			array( 'title' => 'theme color line', 'selector' => '.ce_title:not(.und-title.white):not(.und-title.gray)', 'classes' => 'und-title themecolor' ),
			)
		),	
		array( 'title' => 'SVG Divider', 'selector' => 'h1,h2,h3,h4,h5,h6', 'classes' => 'div_title' ),	
		array( 'title' => 'Borderless image', 'selector' => 'img', 'classes' => 'noborder' ),
		array( 'title' => 'Animation On Hover', 'items' => array(
			array( 'title' => 'To top', 'selector' => 'a,img', 'classes' => 'shadow_image top' ),
			array( 'title' => 'To bottom', 'selector' => 'a,img', 'classes' => 'shadow_image bottom' ),
			)
		),
		array( 'title' => 'Border Radius Image', 'items' => array(
			array( 'title' => '1px', 'selector' => 'img', 'styles' => array( 'border-radius' => '1px' )),
			array( 'title' => '2px', 'selector' => 'img', 'styles' => array( 'border-radius' => '2px' )),
			array( 'title' => '3px', 'selector' => 'img', 'styles' => array( 'border-radius' => '3px' )),
			array( 'title' => '4px', 'selector' => 'img', 'styles' => array( 'border-radius' => '4px' )),
			array( 'title' => '5px', 'selector' => 'img', 'styles' => array( 'border-radius' => '5px' )),
			array( 'title' => '6px', 'selector' => 'img', 'styles' => array( 'border-radius' => '6px' )),
			array( 'title' => '7px', 'selector' => 'img', 'styles' => array( 'border-radius' => '7px' )),
			array( 'title' => '8px', 'selector' => 'img', 'styles' => array( 'border-radius' => '8px' )),
			array( 'title' => '9px', 'selector' => 'img', 'styles' => array( 'border-radius' => '9px' )),
			array( 'title' => '10px', 'selector' => 'img', 'styles' => array( 'border-radius' => '10px' )),
			array( 'title' => '11px', 'selector' => 'img', 'styles' => array( 'border-radius' => '11px' )),
			array( 'title' => '12px', 'selector' => 'img', 'styles' => array( 'border-radius' => '12px' )),
			array( 'title' => '13px', 'selector' => 'img', 'styles' => array( 'border-radius' => '13px' )),
			array( 'title' => '14px', 'selector' => 'img', 'styles' => array( 'border-radius' => '14px' )),
			array( 'title' => '15px', 'selector' => 'img', 'styles' => array( 'border-radius' => '15px' )),
			array( 'title' => '16px', 'selector' => 'img', 'styles' => array( 'border-radius' => '16px' )),
			array( 'title' => '17px', 'selector' => 'img', 'styles' => array( 'border-radius' => '17px' )),
			array( 'title' => '18px', 'selector' => 'img', 'styles' => array( 'border-radius' => '18px' )),
			array( 'title' => '19px', 'selector' => 'img', 'styles' => array( 'border-radius' => '19px' )),
			array( 'title' => '20px', 'selector' => 'img', 'styles' => array( 'border-radius' => '20px' )),
			
		))
		);
		// Before 3.1 you needed a special trick to send this array to the configuration.
		// See this post history for previous versions.
		$settings['style_formats'] = str_replace( '"', "'", json_encode( $style_formats ) );

		return $settings;
	}

	public function cws_print_single_class($class) {
		$class .= ' page_content';
		$footer = $this->cws_get_meta_option('footer');

		$class .= isset($footer['footer_fixed_style']) && $footer['footer_fixed_style'] == '1' ? ' fixed' : '';
		$class .= isset($footer['no_page_spacing']) && $footer['no_page_spacing'] == '1' ? ' no_page_spacing' : '';
		$class .= isset($footer['wide_featured']) && $footer['wide_featured'] == '1' ? ' wide_featured' : '';
		return $class;
	}

	public function cws_print_metas() {
		$this->echo_if( has_category(), '<div class="post_categories">' . get_the_category_list ( $this::THEME_V_SEP ) . '</div>');
		$this->echo_if( has_tag(), '<div class="post_tags">' . get_the_tag_list (null, $this::THEME_V_SEP, null ) . '</div>');
	}

	public function cws_get_page_meta_var ( $keys ) {
		$p_meta = array();
		if ( isset( $GLOBALS[self::$text_domain . '_page_meta'] ) && !empty($keys) ) {
			$p_meta = $GLOBALS[self::$text_domain . '_page_meta'];
			if ( is_string( $keys ) ) {
				if ( isset( $p_meta[$keys] ) ) {
					return $p_meta[$keys];
				}
			} else if ( is_array( $keys ) ) {
				for ( $i=0; $i < count($keys); $i++ ) {
					if ( isset( $p_meta[$keys[$i]] ) ) {
						if ( $i < count($keys) - 1 ) {
							if ( is_array( $p_meta[$keys[$i]] ) ) {
								$p_meta = $p_meta[$keys[$i]];
							}	else {
								return false;
							}
						}	else {
							return $p_meta[$keys[$i]];
						}
					}	else {
						return false;
					}
				}
			}
		}
		return false;
	}

	public function cws_set_page_meta_var($keys, $value = '') {
		$p_meta = array();
		if (isset($GLOBALS[self::$text_domain . '_page_meta']) && !empty($keys) ) {
			$p_meta = &$GLOBALS[self::$text_domain . '_page_meta'];

			if ( is_string( $keys ) ) {
				if ( isset($p_meta[$keys]) ) {
					$p_meta[$keys] = $value;
					return true;
				}
			} else if ( is_array( $keys ) && !empty( $keys ) ) {
				for ( $i=0; $i < count($keys); $i++ ) {
					if ( isset( $p_meta[$keys[$i]] ) ) {
						if ( $i < count($keys) - 1 ) {
							if ( is_array( $p_meta[$keys[$i]] ) ) {
								$p_meta = &$p_meta[$keys[$i]];
							} else {
								return false;
							}
						}	else {
							$p_meta[$keys[$i]] = $value;
							return true;
						}
					}	else {
						return false;
					}
				}
			}
		}
		return false;
	}

	/* HEDER LOADER */
	public function cws_page_loader() {
		$cws_enable_page_loader = $this->cws_get_meta_option( 'show_loader' );
		if (!empty($cws_enable_page_loader)) {

			$loader_logo = $this->cws_get_option( 'loader_logo' );
			if(!empty($loader_logo)){
				$logo_get = wp_get_attachment_image_src($loader_logo['id'], 'full');
				$loader_logo['height'] = $logo_get[2];
				$loader_logo['width'] = $logo_get[1];

				$logo_is_high_dpi = (!empty($loader_logo['logo_is_high_dpi']) ? $loader_logo['logo_is_high_dpi'] : '');
			}

			if ( isset( $loader_logo['src'] ) ) {
				$logo_src = '';
				$logo_class = ' class="loader_logo"';
				$main_logo_height = '';

				$logo_retina_thumb_exists = false;
				$logo_retina_thumb_url = "";
				if ( isset( $loader_logo['src'] ) && ( ! empty( $loader_logo['src'] ) ) ) {

					if ( $logo_is_high_dpi ) {
						$thumb_obj = cws_thumb( $loader_logo['src'], array( 'width' => floor( (int) $loader_logo['width'] / 2 ), 'crop' => true ) );
					} else {
						$thumb_obj = cws_thumb( $loader_logo['src'], array( 'width' => 60, 'height' => 60, 'crop' => false ) );
					}
					if ( isset( $thumb_obj ) && ( ! empty( $thumb_obj ) ) ) {
						$thumb_path_hdpi = !empty($thumb_obj[3]) ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_attr( $thumb_obj[3] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
						$logo_src = $thumb_path_hdpi;
					}

				}
			}
			return '<div id="cws_page_loader_container" class="cws_loader_container">
				<div id="cws_page_loader" class="cws_loader"><div class="inner"></div>'.( (!empty($logo_src)) ? "<img $logo_class $logo_src alt='" . get_post_meta( $loader_logo['id'], '_wp_attachment_image_alt', true ) . "' />" : '').'</div>
			</div>';
		}
	}
	/* END HEDER LOADER */

	/* THE HEADER META */
	public function cws_header_meta() {
			?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php
		$this->cws_read_options();
	}
	/* END THE HEADER META */

	/* THEME HEADER */
	public function cws_page_header() {
		$pid = get_the_id();
		global $post;
		$stick_menu = $this->cws_get_meta_option( 'menu-stick' ) == '1';
		$use_blur = $this->cws_get_meta_option( 'use_blur' );
		$custom_header_bg_color = false;
		$customize_title_area = $this->cws_get_meta_option('customize-title-area' ) == '1';
		$post_type = get_post_type();

		//Get metaboxes from post

		$header_bg_settings = $this->cws_get_option( 'header_bg_settings' );
		$bg_color_color = $this->cws_get_option( 'bg_color_color' );

		$bg_img = $this->cws_get_option( 'bg_img' );

		$img_section_atts = $img_section_styles = '';

		$img_section_atts .= ' class="header_bg_img"';

		// !!! placeholder for parallax/blur/gradient and other crap which potentially	should be here somewhere
		// probably we could use some hooking here

		$show_header_outside_slider = $this->cws_get_meta_option('show_header_outside_slider') == '1' && !is_single() && !is_archive() || $this->cws_get_option( 'shop-slider-type' ) != 'none' && $this->cws_is_woo();
		
		$show_header_shop_slider = $this->cws_get_option( 'shop-slider-type' ) != 'none' && $this->cws_is_woo() && is_shop();
		if($show_header_shop_slider){
			if($this->cws_get_option('woo_header_covers_slider')){
				$show_header_outside_slider = true;
			}else{
				$show_header_outside_slider = false;
			}
			
		}

		$show_page_title = true;
		/***** Boxed Layout *****/

		$boxed_layout = $this->cws_get_meta_option('boxed_layout') == '1';

		if($boxed_layout){ echo '<div class="page_boxed">'; }
		/***** \Boxed Layout *****/

		$top_panel_content = $this->cws_render_top_panel($pid);

		$this->header['top_bar_box'] = $top_panel_content;

		$header_box_color_overlay_opacity = $font_color_style = $header_box_color_overlay_type = $header_box_overlay_color = $animate_title = '';

		$header_box_spacings = $animate_options = array();
		$font_color = '';
		$woo_customize_title = $this->cws_get_option('woo_customize_title');
		$header_box = $this->cws_get_meta_option('header_box');
		
		if ($customize_title_area) {
			
			$header_box_color_overlay_opacity = is_array($header_box) && isset($header_box['color_overlay_opacity']) ? $header_box['color_overlay_opacity'] : "";

			$is_single_or_archive = is_single() || is_archive();
			$is_show_on_post = is_single() && $this->cws_get_option('show_on_posts') == '1' && !$this->cws_is_woo();
			$is_show_on_archive = is_archive() && $this->cws_get_option('show_on_archives') == '1' && !$this->cws_is_woo();

			if($is_single_or_archive){
				if($this->cws_is_woo()){
					$is_woo_breadcrumbs = false;
				}
				else{
					$is_woo_breadcrumbs = true;
				}
				
			}else{
				$is_woo_breadcrumbs = true;
			}

			if(!$is_show_on_archive && get_post_type() == 'tribe_events'){
				$is_woo_breadcrumbs = false;
			}
			if ($is_show_on_post && get_post_type() == 'tribe_events') {
				$is_show_on_post = false;
			}

			$is_customized_title = !$is_single_or_archive || $is_show_on_post || $is_show_on_archive || !$is_woo_breadcrumbs;

			$font_color = $is_customized_title ? isset($header_box['font_color']) ? $header_box['font_color'] : "" : '';

			if($this->cws_is_woo() && !empty($woo_customize_title)){
				$woo_header_font_color = $this->cws_get_option('woo_header_font_color');
				$font_color = !empty($woo_header_font_color) ? $woo_header_font_color : $font_color;
			}

			$font_color_style = $font_color ? ' style="' .esc_attr($font_color). '"' : '';

			$header_box_spacings = $is_customized_title ? isset($header_box['spacings']) ? $header_box['spacings'] : "" : array();
			$header_box_color_overlay_type = isset($header_box['color_overlay_type']) ? $header_box['color_overlay_type'] : "";

			if($this->cws_is_woo() && !empty($woo_customize_title ) ){
				$header_box_color_overlay_type = $this->cws_get_option('woo_color_overlay_type') ? $this->cws_get_option('woo_color_overlay_type') : "";
			}

			$header_box_overlay_color = isset($header_box['overlay_color']) ? $header_box['overlay_color'] : "";

			if($this->cws_is_woo() && !empty($woo_customize_title)){
				$header_box_overlay_color = $this->cws_get_option('woo_overlay_color') ? $this->cws_get_option('woo_overlay_color') : "";
			}

			$animate_title = $is_customized_title ? $this->cws_get_meta_option('animate_title' ) : '';
			$animate_options = $animate_title ? $this->cws_get_meta_option('animate_options' ) : array();
		}

		$header_spacings_posts =  $this->cws_get_meta_option('spacings');
		$header_box_spacings = !empty($header_spacings_posts) ? $this->cws_get_meta_option('spacings') : $header_box_spacings;
		
		if($this->cws_is_woo() && !empty($woo_customize_title)){
			$header_box_spacings = $this->cws_get_option('woo_page_title_spacings');
		}
		$page_title_content = $this->cws_build_page_title($font_color, $header_box_spacings, $animate_options);

		$title_area_switcher = $this->cws_get_meta_option('title_area_switcher');
		ob_start();
		echo "<!-- header_box -->";

			$header_box_use_blur = 0;
			$header_box_blur_intensity = $bg_header_parallaxify = $bg_header_scalar_x = $bg_header_scalar_y = $bg_header_limit_x = $bg_header_limit_y = $header_box_pattern_image = $header_box_use_blur_style = '';

			$bg_header_gradient = $parallax_opt_arr = array();

			$bg_header_parallaxify_atts = $bg_header_parallaxify_layer_atts = '';

			$slide_down_header = null;
			if ($customize_title_area) {
				$header_box_use_blur = isset($header_box['use_blur']) ? $header_box['use_blur'] : "";
				$hb_blur = isset($header_box['blur_intensity']) ? $header_box['blur_intensity'] : "";
				$parallax_opt_arr = isset($header_box['parallax_options']) ? $header_box['parallax_options'] : "";

				$header_box_effect = isset($header_box['effect']) ? $header_box['effect'] : "";
				$scroll_parallax =  isset($header_box['scroll_parallax']) ? $header_box['scroll_parallax'] : "";
				$slide_down_header = isset($header_box['slide_down_header']) ? $header_box['slide_down_header'] : "";
				$bg_header_parallaxify = $header_box_effect === 'parallaxify';

				$header_box_use_pattern = isset($header_box['header_box_use_pattern']) ? $header_box['header_box_use_pattern'] === '1' : "";
				if ($header_box_use_pattern) {
					$header_box_pattern_image = $header_box['header_box_pattern_image'];
				}

				$bg_header_options = isset($header_box['image']) ? $header_box['image'] : "";

				$header_box_use_blur_style = "-webkit-filter:blur({$hb_blur}px);-moz-filter:blur({$hb_blur}px);-o-filter: blur({$hb_blur}px);-ms-filter:blur({$hb_blur}px);filter: blur({$hb_blur}px);";
				$bg_header_gradient = isset($header_box['gradient_settings']) ? $header_box['gradient_settings'] : "";

				$this->cws_print_parallaxify_atts($parallax_opt_arr, $bg_header_parallaxify_atts, $bg_header_parallaxify_layer_atts);
			}

			$header_box_color_overlay_opacity = (int)$header_box_color_overlay_opacity / 100;

			$bg_header = $bg_header_feature = false;
			$bg_header_url = $bg_header_html = '';

			/* !!! Problems with logic
				*/
			if ( !$this->cws_is_woo() && !is_single() && $post_type !== 'post' && 'cws_' !== substr($post_type, 0, 4) ) {
				if ( has_post_thumbnail() ) {
					list($bg_header_url, $bg_header_width, $bg_header_height) = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
					$bg_header = $bg_header_feature = true;
				} elseif ( !empty( $bg_header_options['src'] )) {
				$bg_header_url = $bg_header_options['src'];
				$bg_header = true;
			}
			}

			

			$image_from_theme_options = isset($header_box['image']) ? $header_box['image'] : "";
			$image_from_theme_options = isset($image_from_theme_options['src']) && !empty($image_from_theme_options['src']) ? $image_from_theme_options : '';
			$image_from_post = $this->cws_get_meta_option('post_header_box_image');

			$header_post = $this->cws_get_meta_option('header_image' );
			if(!empty($header_post)){
				$image_from_post = '';
			}
			//Check post header image
			if ($customize_title_area) {
				if ($this->cws_get_option('show_on_posts') == '1') {
					if (!empty($image_from_theme_options)) {
						$post_header_image = wp_get_attachment_image_src($image_from_theme_options['id'], 'full');
					}
					if (isset($image_from_post['id']) && !empty($image_from_post['id'])) {
						$post_header_image = wp_get_attachment_image_src($image_from_post['id'], 'full');
					}
				}
			} else {
				if (isset($image_from_post['id']) && !empty($image_from_post['id'])) {
					$post_header_image = wp_get_attachment_image_src($image_from_post['id'], 'full');
				}
			}
			$header_image_src = isset($post_header_image[0]) ? $post_header_image[0] : '';

			if (is_single() && (get_post_type() == 'post') || is_archive() && $customize_title_area && $is_show_on_archive){
				$bg_header_url = $header_image_src;
			}
			//[!]
			wp_enqueue_script ('skrollr','jquery');
			if (
				((is_archive() && $customize_title_area) ? $is_show_on_archive : true ) &&
				((is_single() && (get_post_type() == 'post' && $customize_title_area)) ? $is_show_on_post : true ) &&
				($customize_title_area || isset($meta_title_area) || isset( $bg_header_url ) ) &&
				!$this->cws_is_woo() && ! (get_post_type() == 'cws_staff') && ! (get_post_type() == 'cws_portfolio')
			){
				if ( isset($bg_header_url) ) {

					if ( isset($header_box_use_pattern) && !empty( $header_box_pattern_image ) && isset( $header_box_pattern_image['src'] ) && !empty( $header_box_pattern_image['src'] ) ){
						$bg_header = true;
						$header_box_pattern_image_src = $header_box_pattern_image['src'];
						$bg_header_html .= "<div class='bg_layer' style='background-image:url(" . esc_url($header_box_pattern_image_src) . ");".($bg_header_parallaxify ? esc_attr($bg_header_parallaxify_layer_atts) : '')."'></div>";
					}
					if ( $header_box_color_overlay_type == 'color' && !empty( $header_box_overlay_color ) ){
						$bg_header = true;
						$bg_header_html .= "<div class='bg_layer' style='background-color:" .esc_attr($header_box_overlay_color) . ";" . ( !empty( $header_box_color_overlay_opacity ) ? "opacity:".esc_attr($header_box_color_overlay_opacity).";" : '' ) . ";".($bg_header_parallaxify ? esc_attr($bg_header_parallaxify_layer_atts) : '')."'></div>";
					}
					else if ( $header_box_color_overlay_type == 'gradient' ){
						$bg_header = true;
						if(isset($bg_header_gradient_settings) && !empty($bg_header_gradient_settings)){
						$bg_header_gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $bg_header_gradient_settings ) );
						$bg_header_html .= "<div class='bg_layer' style='$bg_header_gradient_rules" . ( !empty( $header_box_color_overlay_opacity ) ? "opacity:".esc_attr($header_box_color_overlay_opacity).";" : '' ) . ";".($bg_header_parallaxify ? esc_attr($bg_header_parallaxify_layer_atts) : '')."'></div>";	
						}

					}
				}

			}
// exit('Wrong Side of Heaven');	
				if ( !empty( $bg_header_url ) ) {
					$header_bg_atts = '';
					if(is_array($header_box_spacings)){
						foreach ( $header_box_spacings as $key => $value ) {
							switch ($key) {
								case 'top':
								case 'bottom':
								if ( !empty( $value ) ) {
									$header_bg_atts .= " data-$key='$value'";
								}
								break;
							}
						}						
					}

					echo "<div class='header_box bg_page_header".( $slide_down_header ? ' hide_header' : '')."'".$font_color_style.$header_bg_atts.">";
						if($page_title_content){ echo sprintf("%s", $page_title_content); }
						if($bg_header_parallaxify){ echo '<div class="cws_parallax_section" '.esc_attr($bg_header_parallaxify_atts).'>';}
							wp_enqueue_script ('parallax');
							if($bg_header_parallaxify){
								echo '<div class="layer" data-depth="1.00">';
							}
								echo sprintf("%s", $bg_header_html);
									echo "<div class='stat_img_cont".( (isset($meta_title_area) && $header_box_effect == 'scroll_parallax') || ($customize_title_area && $header_box_effect == 'scroll_parallax') ? ' title has_fixed_background' : '').( ((isset($meta_title_area) && $header_box_effect == 'scroll_parallax' && $scroll_parallax == '1') || ($customize_title_area && $header_box_effect == 'scroll_parallax' && $scroll_parallax == '1')) ? ' zoom_out' : '')."' style='". ($header_box_use_blur == 1 ? esc_attr($header_box_use_blur_style) : '') . ($bg_header_parallaxify ? esc_attr($bg_header_parallaxify_layer_atts) : '') ."background-image: url(".esc_url($bg_header_url).");".( (isset($meta_title_area) && $scroll_parallax == '1') || ($customize_title_area && $scroll_parallax == '1') ? 'background-size:'.esc_attr($bg_header_width."px auto;") : 'background-size: cover;')."background-repeat: no-repeat;background-position: center center;".((isset($meta_title_area) && $header_box_effect == 'fixed') || ($customize_title_area && $header_box_effect == 'fixed') ? 'background-attachment: fixed;' : '')."'></div>";

							if($bg_header_parallaxify){
								echo "</div></div>";
							}
					echo '</div>';
				}

				if ( empty( $bg_header_url ) && ($customize_title_area || isset($meta_title_area)) ) {
					echo "<div class='header_box product-image bg_page_header".((isset($meta_title_area) && $slide_down_header == '1') ? ' hide_header' : '')."'".(!empty($font_color) ? ' style="color:'.esc_attr($font_color).';"' : '').(!empty($custom_header_bg_spacings["top"]) ? " data-top='".esc_attr($custom_header_bg_spacings['top'])."'" : '').(!empty($custom_header_bg_spacings["bottom"]) ? " data-bottom='".esc_attr($custom_header_bg_spacings['bottom'])."'" : '').">";
						if($page_title_content){
							echo sprintf("%s", $page_title_content);
						}						
						echo sprintf("%s", $bg_header_html);
					echo '</div>';
				}
		echo "<!-- /header_box -->";
		$page_header_content = ob_get_clean();
		if($title_area_switcher){
			$this->header['header_box'] = $page_header_content;
		} else {
			$this->header['header_box'] = '';
		}

		ob_start();
			$is_revslider_active = function_exists('set_revslider_as_theme');
			$cws_revslider_content = '';
			$slider_type = "none";
			if ( is_front_page() ){
				$slider_type = $this->cws_get_option( 'home-slider-type' );
				switch( $slider_type ){
					case 'img-slider':
						$slider_settings = $this->cws_get_meta_option( 'slider_override' );
						$slider_shortcode = wp_specialchars_decode($this->cws_get_option( 'home-header-slider-options' ));
						
						if ( is_page() && !empty($slider_settings) && $slider_settings['is_override'] == '1' ){
							$slider_shortcode = wp_specialchars_decode($slider_settings['slider_shortcode']);
						}
						$slider_options = isset($slider_options) ? $slider_options : "";
						$slider_options = wp_specialchars_decode($slider_options, ENT_QUOTES);

						if (isset($slider_settings['is_wide']) && $slider_settings['is_wide'] !== '1'){
							echo "<div class='slider_bg'><div class='container'>" . do_shortcode( $slider_shortcode ) . "</div></div>";
						} else {
							echo do_shortcode( $slider_shortcode );
						}

						$slider_error = strpos(do_shortcode( $slider_shortcode ), 'Revolution Slider Error') ? true : false;

						$show_page_title = !empty( $slider_options ) ? false : $show_page_title;
						$bg_header = true;
						break;
					case 'video-slider':
						$bg_header = false;
						$video_slider_settings = $this->cws_get_option('slidersection-start');

						$slider_shortcode = isset($video_slider_settings[ 'slider_shortcode' ]) ? $video_slider_settings[ 'slider_shortcode' ] : "";
						$slider_switch = $video_slider_settings[ 'slider_switch' ];
						$video_type = $video_slider_settings[ 'video_type' ];
						$set_video_header_height = $video_slider_settings[ 'set_video_header_height' ];
						$video_header_height = $video_slider_settings[ 'video_header_height' ];
						$sh_source = isset($video_slider_settings[ 'sh_source' ]) ? $video_slider_settings[ 'sh_source' ] : "";
						$youtube_source = $video_slider_settings[ 'youtube_source' ];
						$vimeo_source = isset($video_slider_settings[ 'vimeo_source' ]) ? $video_slider_settings[ 'vimeo_source' ] : "";
						$color_overlay_type = $video_slider_settings[ 'color_overlay_type' ];
						$overlay_color = isset($video_slider_settings[ 'overlay_color' ]) ? $video_slider_settings[ 'overlay_color' ] : "";
						$color_overlay_opacity = isset($video_slider_settings[ 'color_overlay_opacity' ]) ? $video_slider_settings[ 'color_overlay_opacity' ] : "";
						$use_pattern = isset($video_slider_settings[ 'use_pattern' ]) ? $video_slider_settings[ 'use_pattern' ] : "";
						$pattern_image = isset($video_slider_settings[ 'pattern_image' ]) ? $video_slider_settings[ 'pattern_image' ] : "";

						$video_header_height = $set_video_header_height == "1" ? $video_header_height : false;
						$gradient_video_set = isset($video_slider_settings["slider_gradient_settings"]) ? $video_slider_settings["slider_gradient_settings"] : "";
						$gradient_settings = $this->cws_render_gradient($gradient_video_set);

						$sh_source = isset( $sh_source['src'] ) && !empty( $sh_source['src'] ) ? $sh_source['src'] : '';
						$color_overlay_opacity = (int)$color_overlay_opacity / 100;
						$has_video_src = false;
						$header_video_atts = '';
						$header_video_class = "fs_video_bg";
						$header_video_styles = '';
						$header_video_html = '';
						$uniqid = uniqid( 'video-' );
						$uniqid_esc = esc_attr( $uniqid );
						switch ( $video_type ){
							case 'self_hosted':
								if ( !empty( $sh_source ) ){
									$has_video_src = true;
									$header_video_class .= " cws_self_hosted_video";
									$header_video_html .= "<video class='self_hosted_video' src='$sh_source' autoplay='autoplay' loop='loop' muted='muted'></video>";
								}
								break;
							case 'youtube':
								if ( !empty( $youtube_source ) ){
									wp_enqueue_script ('cws_YT_bg');
									$has_video_src = true;
									$header_video_class .= " cws_Yt_video_bg loading";
									$header_video_atts .= " data-video-source='$youtube_source' data-video-id='$uniqid'";
									$header_video_html .= "<div id='$uniqid_esc'></div>";
								}
								break;
							case 'vimeo':
								if ( !empty( $vimeo_source ) ){
									wp_enqueue_script ('vimeo');
									wp_enqueue_script ('cws_self&vimeo_bg');
									$has_video_src = true;
									$header_video_class .= " cws_Vimeo_video_bg";
									$header_video_atts .= " data-video-source='$vimeo_source' data-video-id='$uniqid'";
									$header_video_html .= "<iframe id='$uniqid_esc' src='" . esc_url($vimeo_source) . "?api=1&player_id=$uniqid' frameborder='0'></iframe>";
								}
								break;
						}
						if ( $has_video_src ){
							$bg_header = true;
							if ( $use_pattern && !empty( $pattern_image ) && isset( $pattern_image['url'] ) && !empty( $pattern_image['url'] ) ){
								$pattern_img_src = $pattern_image['url'];
								$header_video_html .= "<div class='bg_layer' style='background-image:url(" . $pattern_img_src . ")'></div>";
							}
							if ( $color_overlay_type == 'color' && !empty( $overlay_color ) ){
								$header_video_html .= "<div class='bg_layer' style='background-color:" . $overlay_color . ";" . ( !empty( $color_overlay_opacity ) ? "opacity:$color_overlay_opacity;" : '' ) . "'></div>";
							}
							else if ( $color_overlay_type == 'gradient' ){
								$gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $gradient_settings ) );
								$header_video_html .= "<div class='bg_layer' style='$gradient_rules" . ( !empty( $color_overlay_opacity ) ? "opacity:$color_overlay_opacity;" : '' ) . "'></div>";
							}
						}

						$header_video_atts .= !empty( $header_video_class ) ? " class='" . trim( $header_video_class ) . "'" : '';
						$header_video_atts .= !empty( $header_video_styles ) ? " style='". esc_attr($header_video_styles) ."'" : '';


						if ( !empty( $slider_shortcode ) && $has_video_src && $slider_switch == 1 ){
							$bg_header = true;
							echo "<div class='fs_video_slider'>";
							if ( $is_revslider_active ) {
								echo  do_shortcode( $slider_shortcode );
							} else {
								echo do_shortcode( "[cws_sc_msg_box type='warning' is_closable='1' text='Install and activate Slider Revolution plugin'][/cws_sc_msg_box]" );
							}
								echo '<div ' . $header_video_atts . '>';
								echo sprintf("%s", $header_video_html);
								echo '</div>';
								echo '</div>';
						} elseif ( $has_video_src && $slider_switch == 0 ) {
							$bg_header = true;
							$header_video_fs_view = $video_header_height == false ? 'header_video_fs_view' : '';
							$video_height_coef = $video_header_height == false ? '' : " data-wrapper-height='".(960 / $video_header_height)."'";
							$video_header_height = $video_header_height == false ? '' : "style='height:" . $video_header_height ."px'";
							echo "<div class='fs_video_slider ". sanitize_html_class( $header_video_fs_view ) ."' " . $video_header_height . " ". $video_height_coef .">";
							echo '<div ' . $header_video_atts . '>';
							echo sprintf("%s", $header_video_html);
							echo '</div>';
							echo '</div>';
						}elseif ( ! empty( $slider_shortcode ) && $slider_switch == 1 && ! $has_video_src ) {
							$bg_header = true;
							if ( $is_revslider_active ) {
								echo  do_shortcode( $slider_shortcode );
							} else {
								echo do_shortcode( "[cws_sc_msg_box type='warning' is_closable='1' text='Install and activate Slider Revolution plugin'][/cws_sc_msg_box]" );
							}
						}else{
							$bg_header = true;
							if ( $has_video_src ){
								echo "<div class='fs_video_slider'></div>";
							}
						}

						break;
					case 'stat-img-slider':
						$bg_header = false;
						$static_img_section = $this->cws_get_option('static_img_section');
						$set_img_header_height = $static_img_section['set_static_image_height'];
						$img_header_height = $static_img_section[ 'static_image_height' ];


						$color_overlay_type = '';
						$overlay_color = '';
						$color_overlay_opacity = '';
						$gradient_settings = array();

						if ($static_img_section[ 'static_customize_colors' ] == "1"){
							$color_overlay_type = $static_img_section[ 'img_header_color_overlay_type' ];
							$overlay_color = $static_img_section[ 'img_header_overlay_color' ];
							$color_overlay_opacity = $static_img_section[ 'img_header_color_overlay_opacity' ];
							$color_overlay_opacity = (int)$color_overlay_opacity / 100;
							$gradient_settings = $this->cws_render_gradient( $static_img_section["img_header_gradient_settings"] );
						}

						$use_pattern = $static_img_section[ 'img_header_use_pattern' ];
						$pattern_image = $static_img_section[ 'img_header_pattern_image' ];

						$img_header_height = $set_img_header_height == "1" ? $img_header_height : false;

						$parallax_header_opt = $static_img_section['img_header_parallax_options'];

						$img_header_parallaxify = $static_img_section["img_header_parallaxify"];

						if ($img_header_parallaxify == '1'){
							$img_header_scalar_x = $parallax_header_opt["img_header_scalar_x"];
							$img_header_scalar_y = $parallax_header_opt["img_header_scalar_y"];
							$img_header_limit_x = $parallax_header_opt["img_header_limit_x"];
							$img_header_limit_y = $parallax_header_opt["img_header_limit_y"];

							$img_header_parallaxify_atts = ' data-scalar-x="'.$img_header_scalar_x.'" data-scalar-y="'.$img_header_scalar_y.'" data-limit-y="'.$img_header_limit_y.'" data-limit-x="'.$img_header_limit_x.'"';
							$img_header_parallaxify_layer_atts = 'position: absolute; z-index: 1; left: -'.$img_header_limit_y.'px; right: -'.$img_header_limit_y.'px; top: -'.$img_header_limit_x.'px; bottom: -'.$img_header_limit_x.'px;';
						}

						$image_options = $static_img_section["home_header_image_options"];

						$default_img = false;
						$override_img = false;
						$img_url = '';

						$header_img_html = '';

						if ( isset( $image_options['src'] ) ){
							if ( $use_pattern && !empty( $pattern_image ) && isset( $pattern_image['src'] ) && !empty( $pattern_image['src'] ) ){
								$pattern_img_src = esc_url($pattern_image['src']);
								$header_img_html .= "<div class='bg_layer' style='background-image:url(" . $pattern_img_src . ");".($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '')."'></div>";
							}
							if ( $color_overlay_type == 'color' && !empty( $overlay_color ) ){
								$header_img_html .= "<div class='bg_layer' style='background-color:" . esc_attr($overlay_color) . ";" . ( !empty( $color_overlay_opacity ) ? "opacity:".esc_attr($color_overlay_opacity).";" : '' ) . ";".($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '')."'></div>";
							}
							else if ( $color_overlay_type == 'gradient' && !empty( $gradient_settings ) ){
								$gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $gradient_settings ) );
								$header_img_html .= "<div class='bg_layer' style='$gradient_rules" . ( !empty( $color_overlay_opacity ) ? "opacity:".esc_attr($color_overlay_opacity).";" : '' ) . ";".($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '')."'></div>";
							}
						}

						if ( isset( $image_options['src'] ) ) {
							$bg_header = true;
							$header_img_fs_view = $img_header_height== false ? 'header_video_fs_view' : '';
							$header_img_height_coef = $img_header_height == false ? '' : " data-wrapper-height='".(960 / $img_header_height)."'";
							$img_header_height = $img_header_height == false ? '' : "style='height:" . esc_attr($img_header_height) ."px'";

							echo "<div class='fs_img_header " . sanitize_html_class( $header_img_fs_view ) ."' " . $img_header_height . " ". $header_img_height_coef .">";

							if($img_header_parallaxify){ echo  '<div class="cws_parallax_section" '.$img_header_parallaxify_atts.'>';}
								wp_enqueue_script ('parallax');
								if($img_header_parallaxify){ echo  '<div class="layer" data-depth="1.00">';}
									echo sprintf("%s", $header_img_html);
									echo "<div class='stat_img_cont' style='". ($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '') ."background-image: url(".esc_url($image_options['src']).");background-size: cover;background-position: center center;'></div>";
								if($img_header_parallaxify){
									echo '</div></div>';
								}
							echo '</div>';

						}
					break;
					default:
				}
			}
			else if ( is_page() || $this->cws_is_woo() && is_shop() ){
				if($this->cws_is_woo() && is_shop()){
				$slider_type = $this->cws_get_option( 'shop-slider-type' );
				switch( $slider_type ){
					case 'img-slider':
						$slider_settings = $this->cws_get_meta_option( 'slider_override' );
						$slider_shortcode = wp_specialchars_decode($this->cws_get_option( 'shop-header-slider-options' ));

						if ( is_page() && $slider_settings['is_override'] == '1' ){
							$slider_shortcode = wp_specialchars_decode($slider_settings['shortcode']);
						}
						$slider_options = isset($slider_options) ? $slider_options : '';
						$slider_options = wp_specialchars_decode($slider_options, ENT_QUOTES);

						if (isset($slider_settings['is_wide']) && $slider_settings['is_wide'] !== '1'){
							echo "<div class='slider_bg'><div class='container'>" . do_shortcode( $slider_shortcode ) . "</div></div>";
						} else {
							echo do_shortcode( $slider_shortcode );
						}

						$slider_error = strpos(do_shortcode( $slider_shortcode ), 'Revolution Slider Error') ? true : false;

						$show_page_title = !empty( $slider_options ) ? false : $show_page_title;
						$bg_header = true;
						break;
					case 'video-slider':
						$bg_header = false;
						$video_slider_settings = $this->cws_get_option('shopslidersection-start');

						$slider_shortcode = isset($video_slider_settings[ 'slider_shortcode' ]) ? $video_slider_settings[ 'slider_shortcode' ] : "";
						$slider_switch = $video_slider_settings[ 'slider_switch' ];
						$video_type = $video_slider_settings[ 'video_type' ];
						$set_video_header_height = $video_slider_settings[ 'set_video_header_height' ];
						$video_header_height = $video_slider_settings[ 'video_header_height' ];
						$sh_source = isset($video_slider_settings[ 'sh_source' ]) ? $video_slider_settings[ 'sh_source' ] : "";
						$youtube_source = $video_slider_settings[ 'youtube_source' ];
						$vimeo_source = isset($video_slider_settings[ 'vimeo_source' ]) ? $video_slider_settings[ 'vimeo_source' ] : "";
						$color_overlay_type = $video_slider_settings[ 'color_overlay_type' ];
						$overlay_color = isset($video_slider_settings[ 'overlay_color' ]) ? $video_slider_settings[ 'overlay_color' ] : "";
						$color_overlay_opacity = isset($video_slider_settings[ 'color_overlay_opacity' ]) ? $video_slider_settings[ 'color_overlay_opacity' ] : "";
						$use_pattern = isset($video_slider_settings[ 'use_pattern' ]) ? $video_slider_settings[ 'use_pattern' ] : "";
						$pattern_image = isset($video_slider_settings[ 'pattern_image' ]) ? $video_slider_settings[ 'pattern_image' ] : "";

						$video_header_height = $set_video_header_height == "1" ? $video_header_height : false;
						$gradient_video_set = isset($video_slider_settings["slider_gradient_settings"]) ? $video_slider_settings["slider_gradient_settings"] : "";
						$gradient_settings = $this->cws_render_gradient($gradient_video_set);

						$sh_source = isset( $sh_source['src'] ) && !empty( $sh_source['src'] ) ? $sh_source['src'] : '';
						$color_overlay_opacity = (int)$color_overlay_opacity / 100;
						$has_video_src = false;
						$header_video_atts = '';
						$header_video_class = "fs_video_bg";
						$header_video_styles = '';
						$header_video_html = '';
						$uniqid = uniqid( 'video-' );
						$uniqid_esc = esc_attr( $uniqid );
						switch ( $video_type ){
							case 'self_hosted':
								if ( !empty( $sh_source ) ){
									$has_video_src = true;
									$header_video_class .= " cws_self_hosted_video";
									$header_video_html .= "<video class='self_hosted_video' src='$sh_source' autoplay='autoplay' loop='loop' muted='muted'></video>";
								}
								break;
							case 'youtube':
								if ( !empty( $youtube_source ) ){
									//wp_enqueue_script ('cws_YT_bg');
									$has_video_src = true;
									$header_video_class .= " cws_Yt_video_bg loading";
									$header_video_atts .= " data-video-source='$youtube_source' data-video-id='$uniqid'";
									$header_video_html .= "<div id='$uniqid_esc'></div>";
								}
								break;
							case 'vimeo':
								if ( !empty( $vimeo_source ) ){
									wp_enqueue_script ('vimeo');
									//wp_enqueue_script ('cws_self&vimeo_bg');
									$has_video_src = true;
									$header_video_class .= " cws_Vimeo_video_bg";
									$header_video_atts .= " data-video-source='$vimeo_source' data-video-id='$uniqid'";
									$header_video_html .= "<iframe id='$uniqid_esc' src='" . $vimeo_source . "?api=1&player_id=$uniqid' frameborder='0'></iframe>";
								}
								break;
						}
						if ( $has_video_src ){
							$bg_header = true;
							if ( $use_pattern && !empty( $pattern_image ) && isset( $pattern_image['url'] ) && !empty( $pattern_image['url'] ) ){
								$pattern_img_src = $pattern_image['url'];
								$header_video_html .= "<div class='bg_layer' style='background-image:url(" . $pattern_img_src . ")'></div>";
							}
							if ( $color_overlay_type == 'color' && !empty( $overlay_color ) ){
								$header_video_html .= "<div class='bg_layer' style='background-color:" . $overlay_color . ";" . ( !empty( $color_overlay_opacity ) ? "opacity:$color_overlay_opacity;" : '' ) . "'></div>";
							}
							else if ( $color_overlay_type == 'gradient' ){
								$gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $gradient_settings ) );
								$header_video_html .= "<div class='bg_layer' style='$gradient_rules" . ( !empty( $color_overlay_opacity ) ? "opacity:$color_overlay_opacity;" : '' ) . "'></div>";
							}
						}

						$header_video_atts .= !empty( $header_video_class ) ? " class='" . trim( $header_video_class ) . "'" : '';
						$header_video_atts .= !empty( $header_video_styles ) ? " style='". esc_attr($header_video_styles) ."'" : '';


						if ( !empty( $slider_shortcode ) && $has_video_src && $slider_switch == 1 ){
							$bg_header = true;
							echo "<div class='fs_video_slider'>";
							if ( $is_revslider_active ) {
								echo  do_shortcode( $slider_shortcode );
							} else {
								echo do_shortcode( "[cws_sc_msg_box type='warning' is_closable='1' text='Install and activate Slider Revolution plugin'][/cws_sc_msg_box]" );
							}
								echo '<div ' . $header_video_atts . '>';
								echo sprintf("%s", $header_video_html);
								echo '</div>';
								echo '</div>';
						} elseif ( $has_video_src && $slider_switch == 0 ) {
							$bg_header = true;
							$header_video_fs_view = $video_header_height == false ? 'header_video_fs_view' : '';
							$video_height_coef = $video_header_height == false ? '' : " data-wrapper-height='".(960 / $video_header_height)."'";
							$video_header_height = $video_header_height == false ? '' : "style='height:" . $video_header_height ."px'";
							echo "<div class='fs_video_slider ". sanitize_html_class( $header_video_fs_view ) ."' " . $video_header_height . " ". $video_height_coef .">";
							echo '<div ' . $header_video_atts . '>';
							echo sprintf("%s", $header_video_html);
							echo '</div>';
							echo '</div>';
						}elseif ( ! empty( $slider_shortcode ) && $slider_switch == 1 && ! $has_video_src ) {
							$bg_header = true;
							if ( $is_revslider_active ) {
								echo  do_shortcode( $slider_shortcode );
							} else {
								echo do_shortcode( "[cws_sc_msg_box type='warning' is_closable='1' text='Install and activate Slider Revolution plugin'][/cws_sc_msg_box]" );
							}
						}else{
							$bg_header = true;
							if ( $has_video_src ){
								echo "<div class='fs_video_slider'></div>";
							}
						}

						break;
					case 'stat-img-slider':
						$bg_header = false;
						$static_img_section = $this->cws_get_option('static_img_section');
						$set_img_header_height = $static_img_section['set_static_image_height'];
						$img_header_height = $static_img_section[ 'static_image_height' ];


						$color_overlay_type = '';
						$overlay_color = '';
						$color_overlay_opacity = '';
						$gradient_settings = array();

						if ($static_img_section[ 'static_customize_colors' ] == "1"){
							$color_overlay_type = $static_img_section[ 'img_header_color_overlay_type' ];
							$overlay_color = $static_img_section[ 'img_header_overlay_color' ];
							$color_overlay_opacity = $static_img_section[ 'img_header_color_overlay_opacity' ];
							$color_overlay_opacity = (int)$color_overlay_opacity / 100;
							$gradient_settings = $this->cws_render_gradient( $static_img_section["img_header_gradient_settings"] );
						}

						$use_pattern = $static_img_section[ 'img_header_use_pattern' ];
						$pattern_image = $static_img_section[ 'img_header_pattern_image' ];

						$img_header_height = $set_img_header_height == "1" ? $img_header_height : false;

						$parallax_header_opt = $static_img_section['img_header_parallax_options'];

						$img_header_parallaxify = $static_img_section["img_header_parallaxify"];

						if ($img_header_parallaxify == '1'){
							$img_header_scalar_x = $parallax_header_opt["img_header_scalar_x"];
							$img_header_scalar_y = $parallax_header_opt["img_header_scalar_y"];
							$img_header_limit_x = $parallax_header_opt["img_header_limit_x"];
							$img_header_limit_y = $parallax_header_opt["img_header_limit_y"];

							$img_header_parallaxify_atts = ' data-scalar-x="'.$img_header_scalar_x.'" data-scalar-y="'.$img_header_scalar_y.'" data-limit-y="'.$img_header_limit_y.'" data-limit-x="'.$img_header_limit_x.'"';
							$img_header_parallaxify_layer_atts = 'position: absolute; z-index: 1; left: -'.$img_header_limit_y.'px; right: -'.$img_header_limit_y.'px; top: -'.$img_header_limit_x.'px; bottom: -'.$img_header_limit_x.'px;';
						}

						$image_options = $static_img_section["shop_header_image_options"];

						$default_img = false;
						$override_img = false;
						$img_url = '';

						$header_img_html = '';

						if ( isset( $image_options['src'] ) ){
							if ( $use_pattern && !empty( $pattern_image ) && isset( $pattern_image['src'] ) && !empty( $pattern_image['src'] ) ){
								$pattern_img_src = esc_url($pattern_image['src']);
								$header_img_html .= "<div class='bg_layer' style='background-image:url(" . $pattern_img_src . ");".($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '')."'></div>";
							}
							if ( $color_overlay_type == 'color' && !empty( $overlay_color ) ){
								$header_img_html .= "<div class='bg_layer' style='background-color:" . esc_attr($overlay_color) . ";" . ( !empty( $color_overlay_opacity ) ? "opacity:".esc_attr($color_overlay_opacity).";" : '' ) . ";".($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '')."'></div>";
							}
							else if ( $color_overlay_type == 'gradient' && !empty( $gradient_settings ) ){
								$gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $gradient_settings ) );
								$header_img_html .= "<div class='bg_layer' style='$gradient_rules" . ( !empty( $color_overlay_opacity ) ? "opacity:".esc_attr($color_overlay_opacity).";" : '' ) . ";".($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '')."'></div>";
							}
						}

						if ( isset( $image_options['src'] ) ) {
							$bg_header = true;
							$header_img_fs_view = $img_header_height== false ? 'header_video_fs_view' : '';
							$header_img_height_coef = $img_header_height == false ? '' : " data-wrapper-height='".(960 / $img_header_height)."'";
							$img_header_height = $img_header_height == false ? '' : "style='height:" . esc_attr($img_header_height) ."px'";

							echo "<div class='fs_img_header " . sanitize_html_class( $header_img_fs_view ) ."' " . $img_header_height . " ". $header_img_height_coef .">";
							if($img_header_parallaxify){
								echo '<div class="cws_parallax_section" '.$img_header_parallaxify_atts.'>';
							}
								wp_enqueue_script ('parallax');
								if($img_header_parallaxify){
									echo '<div class="layer" data-depth="1.00">';
								}
									echo sprintf("%s", $header_img_html);
									echo "<div class='stat_img_cont' style='". ($img_header_parallaxify ? $img_header_parallaxify_layer_atts : '') ."background-image: url(".esc_url($image_options['src']).");background-size: cover;background-position: center center;'></div>";
									if($img_header_parallaxify){
										echo '</div></div>';
									}
							echo '</div>';

						}
					break;
					default:
				}
				}else{
					$slider_settings = $this->cws_get_meta_option( 'slider_override' );

					if ( isset($slider_settings['is_override']) && $slider_settings['is_override'] == '1' ){
						$bg_header = true;

						if (isset($slider_settings['is_wide']) && $slider_settings['is_wide'] !== '1'){
							echo "<div class='slider_bg'><div class='container'>" . do_shortcode(wp_specialchars_decode($slider_settings['slider_shortcode'])) . "</div></div>";
						} else {
							echo do_shortcode(wp_specialchars_decode($slider_settings['slider_shortcode']));
						}
						$slider_error = strpos(do_shortcode(wp_specialchars_decode($slider_settings['slider_shortcode'])), 'Revolution Slider Error') ? true : false;						
					}					
				}


			}
			else if ( is_single() ){}
			else if ( is_archive() ){}

		$slider_content = ob_get_clean();

		ob_start();
			$this->cws_header_menu_and_logo(false); // !!!
		$header_content = ob_get_clean();

		if ($this->cws_get_meta_option( 'menu-stick' ) == '1') {
			echo "<div class='sticky_header'>";
				$this->cws_header_menu_and_logo(true);
			echo '</div>';
		}

		wp_add_inline_script('cws_scripts', 'window.header_after_slider=false;');

		//Header (General)
		$header = $this->cws_get_meta_option('header' );


		// EXTR_PREFIX_ALL


			$header_order = $this->cws_get_meta_option('header_zone' );

			$customizer_header = $this->cws_get_meta_option('customizer_header' );
			$header_image = $this->cws_get_meta_option('header_image' );

			$post_meta = cws_core_cwsfw_get_post_meta( get_the_ID(), 'cws_mb_post' );
			$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();
			$header_image_post =  isset($post_meta['post_header_box_image']) && !empty($post_meta['post_header_box_image']) ? $post_meta['post_header_box_image'] : "";

			if(isset($header_image_post) && !empty($header_image_post['src'])){
				$header_image = $header_image_post;
			}


			$header_image_style = $this->cws_get_meta_option('header_image_style' );
			$header_image_size = $this->cws_get_meta_option('header_image_size' );
			$header_image_size = isset($header_image_size) && !empty($header_image_size) ? $header_image_size : "cover";
			$header_image_repeat = $this->cws_get_meta_option('header_image_repeat' );
			$header_image_position = $this->cws_get_meta_option('header_image_position' );
			
			$header_image_position = $this->cws_print9positions($header_image_position);
			$header_spacings = $this->cws_get_meta_option('header_spacings' );
			$woo_customize_title = $this->cws_get_option('woo_customize_title');
			if($this->cws_is_woo() && !empty($woo_customize_title ) ){
				$header_image = $this->cws_get_option('woo_default_header_image');
			}


		ob_start();
			if ( isset( $header_spacings ) ){
				$header_spacings_styles = '';
				foreach ( $header_spacings as $key => $value ){
					if ( !empty( $value ) || $value == '0' ){
						$header_spacings_styles .= "padding-".$key . ": " . $value . "px;";
					}
				}
			}
			$header_image_style = isset($header_image['src']) && !empty($header_image['src']) ? (!empty($header_spacings["top"]) || !empty($header_spacings["bottom"]) ? esc_attr($header_spacings_styles): '')."background-size: ".esc_attr($header_image_size).";".($header_image_style == 'fixed' ? 'background-attachment: fixed;' : '')."background-position: ".esc_attr($header_image_position).";background-repeat: ".esc_attr($header_image_repeat).";background-image: url(".(esc_url($header_image['src'])).");" : '';

			$header_color_overlay_type = $this->cws_get_meta_option('header_color_overlay_type' );
			$header_overlay_color = $this->cws_get_meta_option('header_overlayc' );
			$header_overlay_color = $this->cws_Hex2RGB($header_overlay_color);
			$header_color_overlay_opacity_style = $this->cws_get_meta_option('header_color_overlay_opacity');
			$header_color_overlay_opacity = !empty( $header_color_overlay_opacity_style ) ? ($header_color_overlay_opacity_style)/100 : 1;
			$header_color_style = 'rgba('.$header_overlay_color.','.$header_color_overlay_opacity.')';
			$header_gradient = $this->cws_get_meta_option('header_gradient_settings' );
			$header_gradient_settings = $this->cws_render_gradient($header_gradient); 

			if($this->cws_is_woo() && !empty($woo_customize_title ) ){
				$header_color_overlay_type = $this->cws_get_option('woo_color_overlay_type');
			}

			if($this->cws_is_woo() && !empty($woo_customize_title)){
				$header_overlay_color = $this->cws_get_option('woo_overlay_color' );
				$header_overlay_color = $this->cws_Hex2RGB($header_overlay_color);
				$header_color_overlay_opacity = $this->cws_get_option('woo_color_overlay_opacity' ) ? ($this->cws_get_option('woo_color_overlay_opacity' ))/100 : 1;
				$header_color_style = 'rgba('.$header_overlay_color.','.$header_color_overlay_opacity.')';
			}			


			$header_post_overlay = $this->cws_get_meta_option('apply_bg_color');
			if(!empty($header_post_overlay) && $header_post_overlay != 'list_color'){
				$header_overlay_color = $this->cws_get_meta_option('title_overlay' );
				$header_overlay_color = $this->cws_Hex2RGB($header_overlay_color);
				$header_color_overlay_opacity = $this->cws_get_meta_option('title_bg_opacity' ) ? ($this->cws_get_meta_option('title_bg_opacity' ))/100 : 1;
				$header_color_style = 'rgba('.$header_overlay_color.','.$header_color_overlay_opacity.')';
				$header_color_overlay_type = $this->cws_get_meta_option('post_color_overlay_type' );
				$header_gradient = $this->cws_get_meta_option('post_gradient_settings' );
				$header_gradient_settings = $this->cws_render_gradient($header_gradient); 
			}		
			?>
				<div class="header_zone" <?php echo (isset($header_image['src']) && !empty($header_image['src']) && $customizer_header == '1' && !$show_header_shop_slider ? 'style="'.$header_image_style.'"' : '') ?>><!-- header_zone -->
					<?php
					if(!$show_header_shop_slider){
						if ( $customizer_header == '1' && $header_color_overlay_type == 'color' && !empty( $header_overlay_color ) ){
							echo "<div class='header_overlay' style='background-color:" . $header_color_style . "'></div>";
						}
						else if ( $customizer_header == '1' && $header_color_overlay_type == 'gradient' ){

							$header_gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $header_gradient_settings ) );
							echo "<div class='header_overlay' style='$header_gradient_rules" . ( isset( $header_color_overlay_opacity ) ? "opacity:$header_color_overlay_opacity;" : '' ) ."'></div>";
						}						
					}


		$header_zone = ob_get_clean();
		$this->header['drop_zone_start'] = $header_zone;

		$fixed_header = $this->cws_get_meta_option( 'fixed_header' );
		//Render Header from template_parts
		ob_start();
		echo '<div class="header_wrapper_container'.($fixed_header == '1' ? ' fixed_header' : '').((($show_header_outside_slider == '1') && ($bg_header == true) ) ? ' header_outside_slider' : '').'">';
			echo sprintf("%s", $this->header['before_header']) ;
			if ( isset( $header_order ) ){
				foreach ($header_order as $key => $value){
					//if (($show_header_outside_slider && $value['val'] == 'header_box' )  ) continue;
					if (($show_header_shop_slider && $value['val'] == 'header_box' )  ) continue;
					echo sprintf("%s", $this->header[$value['val']]);
				}
			}
			echo sprintf("%s", $this->header['after_header']);
		echo '</div>';
		$header = ob_get_clean();
		echo sprintf("%s", $header);


		if (isset($slider_error) && $slider_error){
			$slider_content = "<div class='rev_slider_error'><div class='message'>Revolution Slider Error: Slider not found.</div></div>";
		}

		echo (!empty($slider_content) ? $slider_content : '');
	}

	/*
		prints associative array keys with prefix and value
	*/
	protected function cws_print_keys($a, $prefix = '') {
		$out = '';
		if (is_array($a)) {
			foreach ($a as $key => $value) {
				if (!is_array($key) && !empty($value)) {
					$out .= $prefix . $key . '="' . $value . '" ';
				}
			}
		}
		return trim($out);
	}

	public function cws_print_css_keys($a, $prefix = '', $suffix = '') {
		$out = '';
		if (is_array($a)) {
			foreach ($a as $key => $value) {
				if (!is_array($a[$key]) && !empty($value)) {
					if ('position' === $key) {
						$out .= $prefix . $key . ':' . $this->cws_print9positions($value) . $suffix . ';';
					} else {
						$out .= $prefix . $key . ':' . $value . $suffix . ';';
					}
				}
			}
		}
		return trim($out);
	}

	public function cws_print_parallaxify_atts($opts, &$atts, &$layer_atts) {
		if (!empty($opts)) {
			$atts .= $this->cws_print_keys($opts, 'data-');
			$layer_atts .= 'position:absolute;z-index:1;left:-'.$opts['limit_y'].'px;right:-'.$opts['limit_y'].'px;top:-'.$opts['limit_x'].'px;bottom:-'.$opts['limit_x'].'px;';
		}
	}

	public function cws_build_page_title($font_color, $header_box_spacings, $animate_title) {
		$post_type = get_post_type();
		global $post;
		$page_title_section_atts = '';
		$page_title_section_class = "page_title".($animate_title ? ' animate_title' : '');
		$page_title_section_class .= $header_box_spacings ? (!empty($header_box_spacings['top']) || !empty($header_box_spacings['bottom'])) ? ' custom_spacing' : '' : '';
		$page_title_section_atts = !empty( $page_title_section_class ) ? " class='$page_title_section_class'" : '';
		$page_title_section_atts .= !empty( $page_title_section_styles ) ? " style='$page_title_section_styles'" : '';

		$page_title_container_styles = '';

		if(!empty($header_box_spacings)){
			foreach ( $header_box_spacings as $key => $value ) {
				if ( !empty( $value ) ) {
					$value =(int) $value;
					$page_title_container_styles .= "padding-{$key}:{$value}px;";
					$page_title_section_atts .= " data-init-$key='$value'";
				}
			}
		}
		$post_id = get_the_id();
		$post_meta = get_post_meta( $post_id, 'cws_mb_post' );
		$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();

		$apply_color = isset($post_meta['apply_color']) && !empty($post_meta['apply_color']) ? $post_meta['apply_color'] : "";
		
		$post_title_color = isset($post_meta['post_title_color']) && !empty($post_meta['post_title_color']) ? $post_meta['post_title_color'] : "";
		if($apply_color == 'list_color' ){
			$post_title_color = '';
		}
		$page_title_container_styles = $this->print_ne($page_title_container_styles, ' style="' . esc_attr($page_title_container_styles) . '"');

		$show_breadcrumbs = $this->cws_get_option( 'breadcrumbs' ) == '1';

		$page_title = $this->cws_get_page_title($post_type);
		// Animate options
		$animate_title_options = $animate_container_options = $animate_section_options = '';

		$breadcrumbs = '';
		if ( $show_breadcrumbs ){
			$alt_bc = self::$cws_theme_config['alt_breadcrumbs'];
			reset($alt_bc);
			$key = key($alt_bc);
			if (!empty($alt_bc) && function_exists($key)) {
				$breadcrumbs = call_user_func_array($key, $alt_bc[$key]);
			} else {
				ob_start();
				aasana_dimox_breadcrumbs($animate_title_options);
				$breadcrumbs = ob_get_clean();
			}
		}
		$header_box = $this->cws_get_meta_option('header_box');
		$header_box_settings = isset($header_box['hide_divider']) ? $header_box['hide_divider'] : "";
		
		$breadcrumbs_logo = isset($header_box['breadcrumbs_divider']) ? $header_box['breadcrumbs_divider'] : "";
		$woo_customize_title = $this->cws_get_option('woo_customize_title');
		if($this->cws_is_woo() && !empty($woo_customize_title)){
			$header_box_settings = $this->cws_get_option('woo_hide_divider');
			$breadcrumbs_logo_divider = $this->cws_get_option('woo_breadcrumbs_divider');
			$breadcrumbs_logo = !empty($breadcrumbs_logo_divider) ? $breadcrumbs_logo_divider : "";
		}

		$logo_exists = false;
		$diver_logo = array();		
		if ( !empty( $breadcrumbs_logo['src'] ) ) {
			$logo_exists = true;
			$logo_hw = isset($header_box['breadcrumbs_dimensions']) ? $header_box['breadcrumbs_dimensions'] : "";
			if($this->cws_is_woo() && !empty($woo_customize_title)){
				$logo_hw = $this->cws_get_option('woo_breadcrumbs_dimensions');
			}
			$bfi_args = array();
			
			if ( is_array( $logo_hw ) ) {
				foreach ( $logo_hw as $key => $value ) {
					if ( ! empty( $value ) ) {
						$bfi_args[ $key ] = $value;
						$bfi_args['crop'] = true;
					}
				}
			}

			$logo_m = isset($header_box['breadcrumbs-margin']) ? $header_box['breadcrumbs-margin'] : "";
			if($this->cws_is_woo() && !empty($woo_customize_title)){
				$logo_m = $this->cws_get_option('woo_breadcrumbs_dimensions');
			}
			if (!empty($logo_hw)){
				foreach ($logo_hw as $key => $value) {
					if ( !empty($value) ){
						$bfi_args[$key] = (int)$value;
						$bfi_args['crop'] = true;
					}
				}
			}

			if(!empty($breadcrumbs_logo['src'])){
				$file_parts = pathinfo($breadcrumbs_logo['src']);

				if($file_parts['extension'] == 'svg'){
					$diver_logo['svg'] = $this->cws_print_svg_html($breadcrumbs_logo, $bfi_args);
				}else{
					$diver_logo['img'] = $this->cws_print_img_html($breadcrumbs_logo, $bfi_args);
				}			
			}
			$logo_lr_spacing = $logo_tb_spacing = '';
			if ( is_array( $logo_m ) ) {
				$logo_lr_spacing = $this->cws_print_css_keys($logo_m, 'margin-', 'px');
				$logo_tb_spacing = $this->cws_print_css_keys($logo_m, 'padding-', 'px');
			}
		}
		$page_title = esc_html($page_title);
		$out = '';
		if ( !is_front_page() ) {
			$header_center = $this->print_if($this->cws_get_option('header_center') == '1', ' header_center');
			if($this->cws_is_woo() && !empty($woo_customize_title)){
				$header_center = $this->print_if($this->cws_get_option('woo_header_center') == '1', ' header_center');
			}
			$out .= '<section' . $page_title_section_atts . $animate_section_options . '>';
			$out .= '<div class="container' . $header_center . '"';
			$out .= $page_title_container_styles . $animate_container_options . '>';
			$font_color = !empty($post_title_color) ? $post_title_color : $font_color;
			$out .= '<div class="title"><h1' . $this->print_ne( $font_color, ' style="color:'.esc_attr($font_color).';"');	
			$out .= $animate_title_options . '>';	
			if ($logo_exists){

				$spacing = !empty( $logo_lr_spacing ) ? " style='{$logo_lr_spacing}'" : '';
				$out .= sprintf('<span%s class="logo_breadcrumbs svg_lotus">', $spacing);
				if(!empty($diver_logo)){
					foreach ($diver_logo as $key => $value) {
						switch ($key) {
							case 'img' :
							$out .= '<img '. $diver_logo[$key] .' alt = "' . get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) . '" />';
							break ;
							case 'svg' :
							$out .= $diver_logo[$key];
							break ;
						}
					}			
				}					
				$out .= '</span>';
			}		
			$out .= esc_html($page_title) . '</h1></div>';
			if(is_page() && !empty($post->post_excerpt)){
				$out .= "<div class='page_excerpt'>".$post->post_excerpt."</div>";
			}
			$out .= $breadcrumbs . '</div></section>';
		}
		return $out;
	}

	public function cws_get_page_title($post_type) {
		$page_title = '';
		if ( is_404() ) {
			$page_title = self::$cws_theme_config['strings']['404'];
		} else if ( is_search() ) {
			$page_title = self::$cws_theme_config['strings']['search'];
		} else if ( is_front_page() ) {
			$page_title = self::$cws_theme_config['strings']['home'];
		} else if ( is_category() ) {
			$cat = get_category( get_query_var( 'cat' ) );
			$cat_name = isset( $cat->name ) ? $cat->name : '';
			$page_title = sprintf( self::$cws_theme_config['strings']['category'], $cat_name );
		} else if ( is_tag() ) {
			$page_title = sprintf( self::$cws_theme_config['strings']['tag'], single_tag_title( '', false ) );
		} elseif ( is_day() ) {
			$page_title = get_the_time( get_option('date_format') );
		} elseif ( is_month() ) {
			$page_title = get_the_time( 'F, Y' );
		} elseif ( is_year() ) {
			$page_title = get_the_time( 'Y' );
		} elseif ( has_post_format() && !is_singular() ) {
			$page_title = get_post_format_string( get_post_format() );
		} else if ( is_tax( array( 'cws_portfolio_cat', 'cws_staff_member_department', 'cws_staff_member_position' ) ) ) {
			$tax_slug = get_query_var( 'taxonomy' );
			$term_slug = get_query_var( $tax_slug );
			$tax_obj = get_taxonomy( $tax_slug );
			$term_obj = get_term_by( 'slug', $term_slug, $tax_slug );

			$singular_tax_label = isset( $tax_obj->labels ) && isset( $tax_obj->labels->singular_name ) ? $tax_obj->labels->singular_name : '';
			$term_name = isset( $term_obj->name ) ? $term_obj->name : '';
			$page_title = $singular_tax_label . ' ' . $term_name ;
		} else if (  function_exists('tribe_is_event_query') && tribe_is_event_query() ) {
			$page_title = get_post_type_object('tribe_events')->labels->name ;
		} elseif ( function_exists ( 'is_shop' ) && is_shop() ) {
			$page_title = woocommerce_page_title(false);			
		} elseif ( is_archive()) {
			$post_type_obj = get_post_type_object( $post_type );
			$post_type_name = isset( $post_type_obj->label ) ? $post_type_obj->label : '';
			$page_title = $post_type_name ;
		} else if ( $this->cws_is_woo() ) {
			if(is_cart()){
				$page_title = self::$cws_theme_config['strings']['cart'];
			}elseif(is_checkout()){
				$page_title = self::$cws_theme_config['strings']['checkout'];
			}else{
				$page_title = woocommerce_page_title( false );
			}
		} else if (substr($post_type, 0, 4) === 'cws_' ) {
			$slug_option = substr($post_type, 4) . '_slug'; // if post_type is cws_portfolio, this will turn it into portfolio_slug option name
			$portfolio_slug = $this->cws_get_option( $slug_option );
			$post_type_obj = get_post_type_object( $post_type );
			$post_type_name = $post_type_obj->labels->menu_name;
			$page_title = !empty($portfolio_slug) ? $portfolio_slug : $post_type_name ;
		} else {
			$blog_title = $this->cws_get_option('blog_title');
			$page_title = (!is_page() && !empty($blog_title)) ? $blog_title : get_the_title();
		}
		return $page_title;
	}

	/* Social Links */
	public function cws_render_social_links() {
		$out = '';
		$social_groups = $this->cws_get_option( 'social_group' );
		if ($social_groups) {
			$search_place = $this->cws_get_meta_option('search_place');

			$side_panel = $this->cws_get_meta_option( 'side_panel' );
			$side_panel_place = $side_panel['place'];

			$links = null;
			foreach ( $social_groups as $social_group ) {
				$title = esc_attr($social_group['title']);
				$icon = $social_group['icon'];
				$url = !empty($social_group['url']) ? esc_url($social_group['url']) : '#';
				$target = isset($social_group['open']) && !empty($social_group['open']) ? '_blank' : '_self';
				$links .= "<a href='{$url}' class='cws_social_link {$icon}' title='{$title}' target='{$target}'></a>";
			}
			if ($links) {
				$social_groups_position = $this->cws_get_option( 'social' );
				$social_groups_position = $social_groups_position['top_bar'];
				$social_class = ($search_place == 'top' || $this->cws_get_option('woo_cart_place') == 'top' || $side_panel_place == 'topbar_right') ? 'social-divider' : '';
				$social_links_location = $social_groups_position['location'];
				$social_class .= ('top' === substr($social_links_location, 0, 3)) ? ' social-'. $social_groups_position : '';
				$out = "<div class='cws_social_links {$social_class}'>{$links}</div>";
			}
		}
		return $out;
	}
	/* \Social Links */

	//public function

	public function cws_header_menu_and_logo ($is_sticky) {
		$stick_menu = $this->cws_get_meta_option( 'menu-stick' ) == '1';
		$stick_shadow = $this->cws_get_meta_option( 'stick-shadow') == '1' && $stick_menu;

		$woo_mini_cart = $this->cws_getWooMiniCart();

		$woo_mini_icon = $this->cws_getWooMiniIcon();

		/*** Logo Position ***/
		$logo_position = $this->cws_get_meta_option('logo-position');
		$logo_text_in_menu = $this->cws_get_meta_option('site_name_in_menu') == '1';
		$logo_with_site_name = $this->cws_get_meta_option('logo_with_site_name');

		$header_class = 'site_header';
		$header_class .= $stick_menu ? ' sticky_enable' : '';
		$header_class .= $stick_shadow ? ' sticky_shadow' : '';
		if ( !empty( $logo_position ) ) {
			$header_class .= ' logo-' . $logo_position;
		}
		if ( $logo_text_in_menu && $logo_position == 'center') {
			$header_class .= ' text-in-menu';
		}
		$show_header_outside_slider = $this->cws_get_meta_option('show_header_outside_slider') == '1' || $this->cws_get_option( 'shop-slider-type' ) != 'none';

		$header_class .= !empty( $slider_content ) && ($show_header_outside_slider) && $bg_header ? " with_background" : '';

		$sandwich_menu = $this->cws_get_meta_option( 'show_sandwich_menu' ) == '1';

		//$header_class .= !( empty($sandwich_menu) && !empty($header_widget_set) ) ? ' inline-menu' : '';
		$header_class .= $sandwich_menu ? ' active-sandwich-menu' : ' none-sandwich-menu';
		/***** \Logo Position *****/

		/***** Menu Position *****/
		global $current_user;

		$menu_locations = get_nav_menu_locations();
		$menu_position = $this->cws_get_meta_option('menu-position');

		/***** \Menu Position *****/
		$is_logo_center = $logo_position == 'center';
		ob_start();

			echo "<div class='header_cont'>";

				$menu_margin = $this->cws_get_meta_option( 'menu_margin' );
				$header_style = $this->cws_print_css_keys($menu_margin, 'padding-', 'px');
				$show_menu_bg_color = $this->cws_get_option('show_menu_bg_color');
				$show_header_slider_bg_color = $this->cws_get_meta_option('show_header_slider_bg_color');

				if($this->cws_is_woo() && !empty($show_menu_bg_color) ){
					$header_style .= $this->cws_print_rgba('background-color',
						$this->cws_get_option('woo_menu_bg_color'),
						$this->cws_get_option('woo_menu_opacity'));
				}else{
					if(!empty($show_header_slider_bg_color)){
						$header_style .= $this->cws_print_rgba('background-color',
						$this->cws_get_meta_option('header_outside_slider_bg_color'),
						$this->cws_get_meta_option('header_outside_slider_bg_opacity'));				
					}					
				}

				$mobile_menu_place = $this->cws_get_meta_option("mobile_menu_place");
	?>
				<header <?php echo !empty($header_class) ? "class='$header_class'" : ''; ?>><!-- header -->
					<div class="header_container"><!-- header_container -->
		<?php
		$before_header_content = ob_get_clean();
		$this->header['before_header'] = $before_header_content;
		$bfi_args = $bfi_args_sticky = $bfi_args_mobile = array();
		$a_logos = array(); // array of main logo, mobile and sticky
		$enable_logo = $this->cws_get_meta_option('enable_logo') == '1';
		if ($enable_logo) {
			ob_start();
				/***** Logo Settings *****/
				// TODO: need to add some filter to get proper logo in case there are more than one option
				$default_logo = 'logo_' . $this->cws_get_meta_option('default_logo');
				$woo_customize_logotype = $this->cws_get_option('woo_customize_logotype');
				if($this->cws_is_woo() && !empty($woo_customize_logotype)){
					$default_logo = 'logo_woo';
				}

				$logo_class = '';
				$logo_lr_spacing = $logo_tb_spacing = $main_logo_height = '';

				$logo = $this->cws_get_meta_option($default_logo);
				$logo_exists = false;
				if ( !empty( $logo['src'] ) ) {
					$logo_exists = true;
					$logo_hw = $this->cws_get_meta_option('logo-dimensions');
					$logo_sticky_hw = $this->cws_get_meta_option( 'logo-dimensions-sticky' );
					$logo_mobile_hw = $this->cws_get_meta_option( 'logo-dimensions-mobile' );
					if ( is_array( $logo_hw ) ) {
						foreach ( $logo_hw as $key => $value ) {
							if ( ! empty( $value ) ) {
								$bfi_args[ $key ] = $value;
								$bfi_args['crop'] = true;
							}
						}
					}
					if ( is_array( $logo_sticky_hw ) ) {
						foreach ( $logo_sticky_hw as $key => $value ) {
							if ( ! empty( $value ) ) {
								$bfi_args_sticky[ $key ] = $value;
								$bfi_args_sticky['crop'] = true;
							}
						}
					}				
					if ( is_array( $logo_mobile_hw ) ) {
						foreach ( $logo_mobile_hw as $key => $value ) {
							if ( ! empty( $value ) ) {
								$bfi_args_mobile[ $key ] = $value;
								$bfi_args_mobile['crop'] = true;
							}
						}
					}

					$logo_m = $this->cws_get_meta_option('logo-margin');
					if (!empty($logo_hw)){
						foreach ($logo_hw as $key => $value) {
							if ( !empty($value) ){
								$bfi_args[$key] = (int)$value;
								$bfi_args['crop'] = true;
							}
						}
					}
					
					if(!empty($logo['src'])){
						$file_parts = pathinfo($logo['src']);

						if($file_parts['extension'] == 'svg'){
							$a_logos['logo']['svg'] = $this->cws_print_svg_html($logo, $bfi_args, $main_logo_height);
						}else{
							$a_logos['logo']['img'] = $this->cws_print_img_html($logo, $bfi_args, $main_logo_height);
						}			
					}

					
					$logo_lr_spacing = $logo_tb_spacing = '';
					if ( is_array( $logo_m ) ) {
						$logo_lr_spacing = $this->cws_print_css_keys($logo_m, 'margin-', 'px');
						$logo_tb_spacing = $this->cws_print_css_keys($logo_m, 'padding-', 'px');
					}

					if (!empty($main_logo_height)) {
						$logo_lr_spacing .= "height:{$main_logo_height}px;";
						$main_logo_height = " style='height:{$main_logo_height}px;'";
						$a_logos['logo_h'] = $main_logo_height;
					}
				}

				/***** \Logo Settings *****/
				$logo_sticky = $this->cws_get_meta_option( 'logo_sticky' );
				$logo_sticky_src = array();
				if ( !empty($logo_sticky['src']) ) {
					$file_parts_sticky = pathinfo($logo_sticky['src']);

					if($file_parts_sticky['extension'] == 'svg'){
						$logo_sticky_src['svg'] = $this->cws_print_svg_html($logo_sticky, $bfi_args);
					}else{
						$logo_sticky_src['img'] = $this->cws_print_img_html($logo_sticky['id'], (!empty($bfi_args_sticky) ? $bfi_args_sticky : null));
					}	
					$logo_class .= ' custom_sticky_logo';
				}

				$logo_mobile = $this->cws_get_meta_option( 'logo_mobile' );
				$logo_mobile_src = array();
				if ( !empty( $logo_mobile['src']) ) {
					$file_parts_mobile = pathinfo($logo_mobile['src']);
					if($file_parts_mobile['extension'] == 'svg'){
						$logo_mobile_src['svg'] = $this->cws_print_svg_html($logo_mobile, $bfi_args);
					}else{
						$logo_mobile_src['img'] = $this->cws_print_img_html($logo_mobile['id'], (!empty($bfi_args_mobile) ? $bfi_args_mobile : null));
					}			
					$logo_class .= ' custom_mobile_logo';
				}
				$a_logos['mobile'] = $logo_mobile_src;
				$a_logos['sticky'] = $logo_sticky_src;
	?>
				<!-- logo_box -->
		<?php
				//Logo box
				$logo_style = '';
				$esc_blog_name = esc_html(get_bloginfo('name'));

				$lb_overlay = $this->cws_get_meta_option('logo_overlay');
				$lbo_css = $lb_overlay ? $this->cws_print_overlay($lb_overlay) : '';

				$logo_style .= $logo_tb_spacing;
				$logo_box = $this->cws_get_meta_option( 'logo_box' );			
				$logo_style .= $this->cws_print_border_box($logo_box);

				if ($is_logo_center) { ?>
					<div class="logo_box header_logo_part<?php echo (!empty($logo_class) ? $logo_class : '');?><?php if($logo_with_site_name) { echo (' logo_with_text'); }?>" <?php echo !empty( $logo_position ) && ! empty( $logo_style ) ? " style='".$logo_style."'" : ''; ?>>

						<?php
						echo "<div class='bg_layer' style='{$lbo_css}'></div>";
						if ($logo_exists){
							echo sprintf("%s", $this->cws_print_logo_block($logo_lr_spacing, $a_logos, $logo_with_site_name, $esc_blog_name));
						}else{ ?>
							<h1 class='header_site_title'><?php echo sprintf("%s", $esc_blog_name);  ?></h1>
						<?php } ?>
					</div>
				<?php }	?>
				<!-- /logo_box -->
			<?php
			$logo_box = ob_get_clean();
		}
		$this->header['logo_box'] = $logo_box;

		ob_start();

		$customizer_header = $this->cws_get_meta_option('customizer_header' );
		$header_color_overlay_type = $this->cws_get_option('search_open_background' );
		$header_overlay_color = $this->cws_get_option('search_overlayc' );
		$header_overlay_color = $this->cws_Hex2RGB($header_overlay_color);
		$header_color_overlay_opacity = $this->cws_get_meta_option('search_color_overlay_opacity' ) ? ($this->cws_get_meta_option('search_color_overlay_opacity' ))/100 : 1;
		$header_color_style = 'rgba('.$header_overlay_color.','.$header_color_overlay_opacity.')';
		$header_gradient = $this->cws_get_option('search_gradient_settings' );
		$header_gradient_settings = $this->cws_render_gradient($header_gradient); 

		$menu_box = $this->cws_get_meta_option( "menu_box" );

		$is_side_panel_switcher = $this->cws_get_meta_option('show_side_panel');
		$side_panel = $this->cws_get_meta_option( 'side_panel' );
		$side_panel_place = $side_panel['place'];
		$wide_menu = $this->cws_get_meta_option('wide_menu');
		$header_style .= $this->cws_print_border_box($menu_box); ?>
			<!-- menu_box -->
			<div class="menu_box<?php echo (!empty($menu_position) ? ' menu_position_a-'.$menu_position : ""); ?>" <?php echo (!empty( $header_style )) ? ' style="'.esc_attr($header_style).'"' : ''; ?>>
				<div class="container<?php if($wide_menu == '1'){ echo (' wide_container'); }?>">
					<?php
					if (($logo_position == "left" || $logo_position == "right") && $enable_logo) {
					?>
						<div class="header_logo_part<?php echo (!empty($logo_class) ? $logo_class : '');?><?php if($logo_with_site_name){ echo(' logo_with_text'); }?>" <?php echo isset( $logo_position ) && !empty( $logo_position ) && $logo_position == 'center' && ! empty( $logo_tb_spacing ) ? " style='".esc_attr($logo_tb_spacing)."'" : ''; ?>>

				<?php if ($logo_exists) {
								echo sprintf("%s", $this->cws_print_logo_block($logo_lr_spacing, $a_logos, $logo_with_site_name, $esc_blog_name));
							}else{ ?>
								<h1 class='header_site_title'><a class="logo s_title" href="<?php echo esc_url(home_url());?>"> <?php echo esc_html(get_bloginfo( 'name' )) ?></a></h1>
							<?php } ?>
					</div>
				<?php
			}
				$custom_menu = $this->cws_get_meta_option('override_menu') == '1' ? $this->cws_get_meta_option('custom_menu') : '';

				$mobile_menu_place = $this->cws_get_meta_option("mobile_menu_place");
				$search_place = $this->cws_get_meta_option('search_place');

				if ( !empty($menu_locations['header-menu']) || !empty($custom_menu) ) {
				if ($logo_position != "center") {
					echo '<div class="menu_left_icons">';
						if ($side_panel_place == 'menu_left' && $is_side_panel_switcher) {
							echo "<a href='#' class='side_panel_icon flaticon-squares ".esc_attr($side_panel_place)."'><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></a>";
						}
						if ($this->cws_get_option('woo_cart_place') == 'left') {
							echo class_exists( 'woocommerce' ) ? "<div class='mini-cart'>$woo_mini_icon$woo_mini_cart</div>" : '';
						}

						if ($mobile_menu_place == "left"){
							echo '
							<div class="mobile_menu_switcher mobile_menu_hamburger mobile_menu_hamburger--htx">
								<span></span>
							</div>
							';
						}

						if($search_place == 'left'){
							echo "<div class='search_menu'></div>";
						}
					echo '</div>';
				}
				?>
				<div class="header_nav_part <?php echo !empty($menu_position) ? "header_nav_part_a-{$menu_position}" : ''; ?>">
					<nav class="main-nav-container <?php echo !empty($menu_position) ? "a-{$menu_position}" : ''; ?>">
					<?php
					if ($logo_position == "center") {
						echo '<div class="menu_left_icons">';

							$this->echo_if($side_panel_place == 'menu_left' && $is_side_panel_switcher,
								"<a href='#' class='side_panel_icon flaticon-squares {$side_panel_place}'><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></a>"
							);

							$this->echo_if($mobile_menu_place == "left",
								'<div class="mobile_menu_switcher mobile_menu_hamburger mobile_menu_hamburger--htx"><span></span></div>');
							if ($this->cws_get_option('woo_cart_place') == 'left') {
								echo class_exists( 'woocommerce' ) ? "<div class='mini-cart'>$woo_mini_icon$woo_mini_cart</div>" : '';
							}
							$this->echo_if($search_place == 'left', '<div class="search_menu"></div>');
						echo '</div>';

					}
						if ($mobile_menu_place == "center"){
							?>
							<div class='menu_center_icons mobile_menu_bar'>
								<div class="mobile_menu_switcher mobile_menu_hamburger mobile_menu_hamburger--htx">
									<span></span>
								</div>
							</div>
							<?php
						}
						ob_start();
							wp_nav_menu( array(
								'theme_location' => (!empty($custom_menu) ? '' : 'header-menu'),
								'menu' => (!empty($custom_menu) ? $custom_menu : ''),
								'menu_class' => 'main-menu',
								'items_wrap'      => '<div class="'.(($logo_position == 'center' && $enable_logo && $logo_text_in_menu == '1') || $logo_position == 'in-menu' && $enable_logo ? 'menu-left-part' : 'no-split-menu').'"><ul class="%2$s">%3$s</ul></div>',
								'container' => false,
								'walker' => new Aasana_Walker_Nav_Menu($this)
							) );
						$menu = ob_get_clean();
						echo sprintf("%s", $menu);

					if ($is_logo_center) {
						echo '<div class="menu_right_icons">';
							if ($this->cws_get_option('woo_cart_place') == 'right') {
								echo class_exists( 'woocommerce' ) ? "<div class='mini-cart'>$woo_mini_icon$woo_mini_cart</div>" : '';
							}
							if($search_place == 'right'){
								echo "<div class='search_menu'></div>";
							}
							if ($mobile_menu_place == "right"){
								echo '
								<div class="mobile_menu_switcher mobile_menu_hamburger mobile_menu_hamburger--htx">
									<span></span>
								</div>
								';
							}
							if ($side_panel_place == 'menu_right' && $is_side_panel_switcher) {
								echo "<a href='#' class='side_panel_icon flaticon-squares ".esc_attr($side_panel_place)."'><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></a>";
							}
						echo '</div>';
					}

					if ( !empty($sandwich_menu) ) {
						if ( $menu_position == 'center' && $is_logo_center) {
							echo '<a href="#" class="menu-bar"><span class="ham"></span></a>';
						}
					}
					?>

					</nav>
				</div>
				<?php
				if (!$is_logo_center){
					echo '<div class="menu_right_icons">';
						if ($this->cws_get_option('woo_cart_place') == 'right') {
							echo class_exists( 'woocommerce' ) ? "<div class='mini-cart'>$woo_mini_icon$woo_mini_cart</div>" : '';
						}
						if($search_place == 'right'){
							echo "<div class='search_menu'></div>";
						}
						if ($mobile_menu_place == "right"){
							echo '<div class="mobile_menu_switcher mobile_menu_hamburger mobile_menu_hamburger--htx"><span></span></div>';
						}
						if ($side_panel_place == 'menu_right' && $is_side_panel_switcher) {
							echo "<a href='#' class='side_panel_icon flaticon-squares ".esc_attr($side_panel_place)."'><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></a>";
						}
					echo '</div>';
				}

				if ( !empty($sandwich_menu) ) {
					if ( ($menu_position == 'left' || $menu_position == 'right' || $menu_position == 'center') && $logo_position != 'center' ) {
						echo '<a href="#" class="menu-bar"><span class="ham"></span></a>';
					}
				}
			}
			?>
		</div>
		<?php
			if(($search_place == 'right') || ($search_place == 'left')){
				echo "<div class='search_menu_wrap'>";			
					if (  $header_color_overlay_type == 'color' && !empty( $header_overlay_color ) ){
						echo "<div class='search_overlay' style='background-color:" . $header_color_style . "'></div>";
					}
					else if ($header_color_overlay_type == 'gradient' ){

						$header_gradient_rules = $this->cws_render_gradient_rules( array( 'settings' => $header_gradient_settings ) );
						echo "<div class='search_overlay' style='opacity:{$header_color_overlay_opacity}; $header_gradient_rules'></div>";
					}	
					echo "<div class='search_menu_cont'>";
						echo "<div class='container'>";
							get_search_form();
							echo "<div class='search_back_button'></div>";
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		?>
		</div><!-- /menu_box -->
			<div class="mobile_menu_wrapper">
				<div class="mobile_menu_container">
				<?php
				ob_start();
					wp_nav_menu( array(
						'theme_location'  => 'header-menu',
						'menu_id'  => 'mobile_menu'.($is_sticky ? '_sticky' : ''),
						'menu_class' => 'mobile_menu main-menu',
						'container' => false,
						'walker' => new Aasana_Walker_Nav_Mobile_Menu()
					) );
				$mobile_menu = ob_get_clean();
				echo sprintf("%s", $mobile_menu);
				?>
			<i class="mobile_menu_switcher"></i>
			</div>
		</div>
		<?php
		$is_menu_enabled = $this->cws_get_meta_option('enable_menu');
		if($this->cws_get_meta_option('customize_menu') == '1'){
			$is_menu_enabled = $this->cws_get_meta_option('enable_menu_mb');
		}

		$menu_box = '';
		if ($is_menu_enabled){
			$menu_box = ob_get_clean();
		} else {
			ob_clean();
		}
		$this->header['menu_box'] = $menu_box;
		ob_start();
		?>

			</div><!-- header_container -->
		</header><!-- header -->
		<?php
		echo '</div>';
		$after_header_content = ob_get_clean();

		if (!$is_sticky){
			$this->header['after_header'] = $after_header_content;
		}

		if($is_sticky){
			echo sprintf("%s", $before_header_content);
			echo sprintf("%s", $menu_box);
			echo sprintf("%s", $after_header_content);
		}
	}

	public function cws_print_logo_block($logo_lr_spacing, $lg, $logo_with_site_name, $blog_name) {
		extract(shortcode_atts( array(
			'logo' => '',
			'mobile' => '',
			'sticky' => '',
			'logo_h' => '',
		), $lg));

		$spacing = !empty( $logo_lr_spacing ) ? " style='{$logo_lr_spacing}'" : '';
		$out = sprintf('<a%s class="logo" href="%s">', $spacing, home_url());
		
		if(!empty($sticky) && is_array($sticky)){
			foreach ($sticky as $key => $value) {
		    	switch ($key) {
			        case 'img' :
			            $out .= $this->print_if( !empty($sticky[$key]), "<img {$sticky[$key]} class='logo_sticky' />");
			            break ;
			        case 'svg' :
			        	$out .= "<span class='logo_sticky cws_svg_sticky'>";
			            $out .= $sticky[$key];
			            $out .= "</span>";
			            break ;
		    	}
		 	}				
		}
	
		if(!empty($mobile) && is_array($mobile)){
		 	foreach ($mobile as $key => $value) {
		    	switch ($key) {
			        case 'img' :
			            $out .= $this->print_if( !empty($mobile[$key]), "<img {$mobile[$key]} class='logo_mobile' />");
			            break ;
			        case 'svg' :
			        	$out .= "<span class='logo_mobile cws_svg_mobile'>";
			            $out .= $mobile[$key];
			            $out .= "</span>";
			            break ;
		    	}
		 	}			
		}


		if(!empty($logo)){
			foreach ($logo as $key => $value) {
		    	switch ($key) {
			        case 'img' :
			            $out .= '<img '. $logo[$key] . $logo_h .' />';
			            break ;
			        case 'svg' :
			            $out .= $logo[$key];
			            break ;
		    	}
		 	}			
		}

		
		if ($logo_with_site_name) {
			$out .= '<h1 class="header_site_title">' . $blog_name . '</h1>';
		}
		$out .= '</a>';
		return $out;
	}


	public function cws_get_date_parts () {
		$part_val = array();
		$perm_struct = get_option( 'permalink_structure' );
		if (!empty( $perm_struct )) {
			$part_val = array(
				'year' => get_query_var( 'year' ),
				'monthnum' => get_query_var( 'monthnum' ),
				'day' => get_query_var( 'day' ),
			);
		} else {
			$merge_date = get_query_var( 'm' );
			$match = preg_match( '#(\d{4})?(\d{1,2})?(\d{1,2})?#', $merge_date, $matches );
			$part_val = array(
				'year' => isset( $matches[1] ) ? $matches[1] : '',
				'monthnum' => isset( $matches[2] ) ? $matches[2] : '',
				'day' => isset( $matches[3] ) ? $matches[3] : '',
			);
		}
		return $part_val;
	}
	public function cws_get_date_part ( $part = '' ){
		$part_val = '';
		$p_id = get_queried_object_id();
		$perm_struct = get_option( 'permalink_structure' );
		$use_perms = !empty( $perm_struct );
		$merge_date = get_query_var( 'm' );
		$match = preg_match( '#(\d{4})?(\d{1,2})?(\d{1,2})?#', $merge_date, $matches );
		switch ( $part ){
			case 'y':
				$part_val = $use_perms ? get_query_var( 'year' ) : ( isset( $matches[1] ) ? $matches[1] : '' );
				break;
			case 'm':
				$part_val = $use_perms ? get_query_var( 'monthnum' ) : ( isset( $matches[2] ) ? $matches[2] : '' );
				break;
			case 'd':
				$part_val = $use_perms ? get_query_var( 'day' ) : ( isset( $matches[3] ) ? $matches[3] : '' );
				break;
		}
		return $part_val;
	}

	public function cws_render_top_panel($pid) {
		ob_start();

		$is_top_panel = $this->cws_get_meta_option('top_panel_switcher') == '1';
		if ($is_top_panel) {
			$top_bar = $this->cws_get_meta_option('top_panel_text');
			//$top_panel_text = stripslashes($top_bar['text']);
			$top_panel_text = stripslashes($top_bar);
			//$is_menu = $top_bar['show_menu'] == '1';
			$top_bar_wide = $this->cws_get_meta_option('top_bar_wide'); // this option is not available in top_bar
			$social_toggle = $this->cws_get_meta_option('toggle-share-icon') == '1';

			$socials = $this->cws_get_meta_option( 'socials' );
			$socials_location = '';
			if(isset($socials['location'][0])){
				$socials_location = explode(",", $socials['location'][0]);
			}

			$social_links = $woo_mini_cart = $woo_mini_icon = '';
			$show_wpml_header = CWS_WPML_ACTIVE;
			if (CWS_WOO_ACTIVE) {
				$woo_mini_cart = $this->cws_getWooMiniCart();
				$woo_mini_icon = $this->cws_getWooMiniIcon();
			}
			$social_links_top_bar = null;
			if(is_array($socials_location)){
				if ( in_array('top', $socials_location)) {
					$social_links = $this->cws_render_social_links();
					if (!empty($social_links)) {
						$social_links_top_bar = isset( $socials['top_bar']) ? $socials['top_bar'] : "";
					}
				}			
			}
		$show_top_bar_menu = $this->cws_get_meta_option('show_top_bar_menu');

		if ( $is_top_panel ) {
			$menu_locations = get_nav_menu_locations();
			$menu_position = $this->cws_get_meta_option('top_bar_menu_position' );
			$logo_position = $this->cws_get_meta_option('logo-position' );
			$search_place = $this->cws_get_meta_option('search_place');
			$side_panel = $this->cws_get_meta_option( 'side_panel' );
			$side_panel_place = $side_panel['place'];
			$is_side_panel_switcher = $this->cws_get_meta_option('show_side_panel');

			$is_show_language_bar = $this->cws_get_option('show_language_bar') == '1';
			$language_bar_pos = $is_show_language_bar ? $this->cws_get_option('language_bar_position') : null;
			
			echo "<!-- top_bar_box --><div class='top_bar_box' id='site_top_panel'>";
				echo "<div class='container".($top_bar_wide ? ' wide_container ' : '').($show_top_bar_menu ? (!empty($menu_position) ? ' topbar-menu-' . esc_attr($menu_position) : '') : ' hide_topbar_menu')."'>";

				echo "<div class='topbar_left_icons'>";
				if ($side_panel_place == 'topbar_left' && $is_side_panel_switcher){
					echo "<div class='side_panel_icon_wrapper'><a href='#' class='side_panel_icon flaticon-squares ".esc_attr($side_panel_place)."'><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></a></div>";
				}

				if ( $show_wpml_header && $is_show_language_bar && $language_bar_pos == 'left') { ?>
					<div class="lang_bar">
						<?php do_action( 'icl_language_selector' ); ?>
					</div>
				<?php }

				if($social_links_top_bar == 'left'){
					echo "<div id='top_social_links_wrapper' class='".($social_toggle ? 'toggle-on' : 'toggle-off')."'> <span class='social-btn-open'>".($social_toggle ? 'Social' : '')."</span> $social_links</div>";
				}

				echo '</div>';
				if ( !empty($menu_locations['topbar-menu']) && $show_top_bar_menu ) {	?>
					<div class="header_nav_part topbar_nav_part <?php echo !empty($menu_position) ? 'a-' . esc_attr($menu_position) : ''; ?>">
						<nav class="main-nav-container">
						<?php
							ob_start();
								wp_nav_menu( array(
									'theme_location'  => 'topbar-menu',
									'menu_class' => 'main-menu topbar-menu',
									'container' => false,
									'walker' => new Aasana_Walker_Nav_Topbar_Menu()
								) );
							echo ob_get_clean();

							if ( !empty($sandwich_menu) && $menu_position === 'center' && $logo_position === 'center') {
								echo '<a href="#" class="menu-bar"><span class="ham"></span></a>';
							}
						?>
						</nav>
					</div>
					<?php
					if ( !empty($sandwich_menu) ) {
						if ( ($menu_position == 'left' || $menu_position == 'right' || $menu_position == 'center') && $logo_position != 'center' ) {
							echo '<a href="#" class="menu-bar"><span class="ham"></span></a>';
						}
					}
				}

				echo ((CWS_WPML_ACTIVE && $is_show_language_bar && $language_bar_pos == 'right') || $side_panel_place == 'topbar_right' || $social_links_top_bar === 'right' ) ? "<div class='topbar_right_icons'>" : '' ;?>
			

				<?php
					echo !empty( $top_panel_text ) ? "<div id='top_panel_text'>".$top_panel_text.'</div>' : '';
					echo !empty( $social_links ) && $social_links_top_bar == 'right' ? "<div id='top_social_links_wrapper' class='".($social_toggle ? 'toggle-on' : 'toggle-off')."'> <span class='social-btn-open'>".($social_toggle ? 'Social' : '')."</span> $social_links</div>" : '';

					ob_start();
					 	do_action( 'icl_language_selector' );
					$wpml_wr = ob_get_clean();

				if ( $show_wpml_header && $is_show_language_bar && !empty($wpml_wr) && $language_bar_pos == 'right') { ?>
					<div class="lang_bar">
						<?php do_action( 'icl_language_selector' ); ?>
					</div>
				<?php }
					$is_woo_active = $this->cws_get_option('woo_cart_enable');
					if ( ($is_woo_active && $this->cws_get_option('woo_cart_place') == 'top') || ($side_panel_place == 'topbar_right' && $is_side_panel_switcher) /*|| $this->cws_get_option('search_place') == 'top'*/ ){
						echo "<div id='top_panel_links'>";
							if ($is_woo_active) {
								echo "<div class='mini-cart'>$woo_mini_icon$woo_mini_cart</div>";
							} else {
								echo "<div class='side_panel_icon_wrapper'><a href='#' class='side_panel_icon flaticon-squares ".esc_attr($side_panel_place)."'><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></a></div>";
							}
						echo '</div>';
					}
					echo ((CWS_WPML_ACTIVE && $is_show_language_bar && $language_bar_pos == 'right') || $side_panel_place == 'topbar_right' || $social_links_top_bar === 'right' ) ? '</div>' : '';

					if($search_place == 'top'){
						echo "<div class='search_icon'></div>";
						echo "<div class='row_text_search'>";
						get_search_form();
						echo '</div>';
					}

				echo '</div>';
				echo "<div id='top_panel_curtain'></div>";
			echo "</div><!-- /top_bar_box -->";
		}

		}
		return ob_get_clean();
	}
	/* /THEME HEADER */

	public function cws_blog_output ( $query = false ) {
		$blogtype = $this->cws_get_meta_option( 'blogtype' );

		$custom_layout_arr = array(
			'this_shortcode' => isset( $query->query_vars['this_shortcode'] ) ? $query->query_vars['this_shortcode'] : false,
			'column_style' => isset( $query->query_vars['column_style'] ) ? $query->query_vars['column_style'] : false,
			'custom_layout' => isset( $query->query_vars['custom_layout'] ) ? $query->query_vars['custom_layout'] : 0,
			'post_text_length' => ! empty( $query->query_vars['post_text_length'] ) ? $query->query_vars['post_text_length'] : '',
			'content_divider' => ! empty( $query->query_vars['content_divider'] ) ? $query->query_vars['content_divider'] : 0,
			'dropcap' => ! empty( $query->query_vars['dropcap'] ) ? $query->query_vars['dropcap'] : 0,
			'post_divider' => ! empty( $query->query_vars['post_divider'] ) ? $query->query_vars['post_divider'] : 0,
			'button_name' => ! empty( $query->query_vars['button_name'] ) ? $query->query_vars['button_name'] : '',
			'button_align' => ! empty( $query->query_vars['button_align'] ) ? $query->query_vars['button_align'] : 'right',
			'use_carousel' => isset( $query->query_vars['use_carousel'] ) ? $query->query_vars['use_carousel'] : 0,
			'disable_lightbox' => isset( $query->query_vars['disable_lightbox'] ) ? $query->query_vars['disable_lightbox'] : '',
			'hide' => isset( $query->query_vars['hide'] ) ? $query->query_vars['hide'] : array(),
			'text_over_image' => isset( $query->query_vars['text_over_image'] ) ? $query->query_vars['text_over_image'] : '',
			'post_size' => isset( $query->query_vars['post_size'] ) ? $query->query_vars['post_size'] : '',
			'date_style' => isset( $query->query_vars['date_style'] ) ? $query->query_vars['date_style'] : '1',
			'boxed_style' => isset( $query->query_vars['boxed_style'] ) ? $query->query_vars['boxed_style'] : '',
			'column_count' => isset( $query->query_vars['column_count'] ) ? intval( $query->query_vars['column_count'] ) : 1,
			'is_related' => isset( $query->query_vars['is_related'] ) ? $query->query_vars['is_related'] : false,
			'meta_position' => isset( $query->query_vars['meta_position'] ) ? $query->query_vars['meta_position'] : 'top',
			'meta_align' => isset( $query->query_vars['meta_align'] ) ? $query->query_vars['meta_align'] : 'left',
			'content_align' => isset( $query->query_vars['content_align'] ) ? $query->query_vars['content_align'] : 'left',
			'blogtype' => isset( $blogtype ) ? $blogtype : '',
			'spacing' => isset( $query->query_vars['spacing'] ) ? $query->query_vars['spacing'] : '',
			'aspect_ratio' => isset( $query->query_vars['aspect_ratio'] ) ? $query->query_vars['aspect_ratio'] : 0,

			'blogtype' => isset( $query->query_vars['blogtype'] ) ? $query->query_vars['blogtype'] : '',
			'row_style' => isset( $query->query_vars['row_style'] ) ? $query->query_vars['row_style'] : 'def',
			'pagination' => isset( $query->query_vars['pagination'] ) ? $query->query_vars['pagination'] : '',
			'pagination_style' => isset( $query->query_vars['pagination_style'] ) ? $query->query_vars['pagination_style'] : '',
		);

		global $wp_query;
		$query = $query ? $query : $wp_query;

		if (!empty($custom_layout_arr['boxed_style']) && $custom_layout_arr['boxed_style'] != 'none') {
			$blogtype .= ' boxed_style';
			if ($custom_layout_arr['boxed_style'] == 'with_shadow') {
				$blogtype .= ' with_shadow';
			} elseif ($custom_layout_arr['boxed_style'] == 'with_border') {
				$blogtype .= ' with_border';
			}
		}

		if ($query->have_posts()):
			ob_start();
			while($query->have_posts()):
				$query->the_post();

				if ($custom_layout_arr['text_over_image'] == '1'){
					if (has_post_thumbnail() || (get_post_format() == 'gallery') ){
					?>
					<article <?php post_class(array( 'item', 'col-'.$blogtype )); ?> <?php echo (!empty($custom_layout_arr['spacing']) || $custom_layout_arr['spacing'] == '0' ? " style='padding-left: ".$custom_layout_arr['spacing']."px;padding-right: ".$custom_layout_arr['spacing']."px;'" : ''); ?>>
						<?php $this->cws_post_output( $custom_layout_arr ); ?>
					</article>
					<?php
					}
				} else {
					?>
					<article <?php post_class(array( 'item', 'col-'.$blogtype, (is_sticky(get_the_id()) ? ' sticky-post ': ''), 'meta-'.$custom_layout_arr['meta_align'], 'content-'.$custom_layout_arr['content_align'] )); ?> <?php echo (!empty($custom_layout_arr['spacing']) || $custom_layout_arr['spacing'] == '0' ? " style='padding-left: ".$custom_layout_arr['spacing']."px;padding-right: ".$custom_layout_arr['spacing']."px;'" : ''); ?>>
						<?php $this->cws_post_output( $custom_layout_arr ); ?>
					</article>
					<?php
				}

			endwhile;
			wp_reset_postdata();
			ob_end_flush();
		endif;
	}

	public function cws_add_style() {
		$out = $this->cws_theme_header_process_fonts();
		$out .= $this->cws_theme_header_process_colors();
		$out .= $this->cws_theme_header_process_blur();
		$out .= $this->cws_theme_loader();
		$out .= $this->cws_theme_mobile();
		wp_add_inline_style('empty', $out);
	}

	public function cws_Hex2RGBA( $hex, $opacity = '1' ) {
		$hex = str_replace('#', '', $hex);
		$color = '';

		if(strlen($hex) == 3) {
			$color = hexdec(substr($hex, 0, 1 )) . ',';
			$color .= hexdec(substr($hex, 1, 1 )) . ',';
			$color .= hexdec(substr($hex, 2, 1 )) . ',';
		}
		else if(strlen($hex) == 6) {
			$color = hexdec(substr($hex, 0, 2 )) . ',';
			$color .= hexdec(substr($hex, 2, 2 )) . ',';
			$color .= hexdec(substr($hex, 4, 2 )) . ',';
		}
		$color .= $opacity;
		return "rgba($color)";
	}

	public function cws_theme_youtube_api_init (){
		wp_add_inline_script('yt_player_api', '
			var tag = document.createElement("script");
			tag.src = "https://www.youtube.com/player_api";
			var firstScriptTag = document.getElementsByTagName("script")[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		');
	}

	public function cws_dbl_to_sngl_quotes ( $content ) {
		return preg_replace( "|\"|", "'", $content );
	}

	public function cws_loading_body_class ( $classes ) {
			$classes[] = self::$text_domain.'-new-layout';
		return $classes;
	}

	public function cws_custom_search ( $form ) {
		$form = "
		<form method='get' class='search-form' action=' ".site_url()." ' >
			<div class='search_wrapper'>
				<label><span class='screen-reader-text'>Search for:</span></label>
				<input type='text' placeholder='".esc_html__( 'Type and press Enter ...', 'aasana' )."' class='search-field' value='". esc_attr(apply_filters('the_search_query', get_search_query())) ."' name='s'/>
				<input type='submit' class='search-submit' value=' ".esc_html__( 'Search', 'aasana' )." ' />
			</div>
		</form>";

		return $form;
	}

	// Custom filter function to modify default gallery shortcode output
	public function cws_custom_gallery( $output, $attr ) {

		// Initialize
		global $post, $wp_locale;

		// Gallery instance counter
		static $instance = 0;
		$instance++;

		// Validate the author's orderby attribute
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) unset( $attr['orderby'] );
		}

		// Get attributes from shortcode
		extract( shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'div',
			'icontag'    => 'div',
			'captiontag' => 'div',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => ''
		), $attr ) );

		// Initialize
		$id = intval( $id );
		$attachments = array();
		if ( $order == 'RAND' ) $orderby = 'none';

		if ( ! empty( $include ) ) {

			// Include attribute is present
			$include = preg_replace( '/[^0-9,]+/', '', $include );
			$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );

			// Setup attachments array
			foreach ( $_attachments as $key => $val ) {
				$attachments[ $val->ID ] = $_attachments[ $key ];
			}

		} else if ( ! empty( $exclude ) ) {

			// Exclude attribute is present
			$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );

			// Setup attachments array
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		} else {
			// Setup attachments array
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		}

		if ( empty( $attachments ) ) return '';

		// Filter gallery differently for feeds
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) $output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
			return $output;
		}

		// Filter tags and attributes
		$itemtag = tag_escape( $itemtag );
		$captiontag = tag_escape( $captiontag );
		$columns = intval( $columns );
		$itemwidth = $columns > 0 ? round(100 / $columns, 2) : 100;
		$float = is_rtl() ? 'right' : 'left';
		$selector = "gallery-{$instance}";

		// Filter gallery CSS
		$output = apply_filters( 'gallery_style', "
			<!-- see gallery_shortcode() in wp-includes/media.php -->
			<div id='$selector' class='gallery galleryid-{$id}'>"
		);

		// Iterate through the attachments in this gallery instance
		$i = 0;
		foreach ( $attachments as $id => $attachment ) {

			// Attachment link
			$link = isset( $attr['link'] ) && 'file' == $attr['link'] ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false );

			if ( isset($attr['link']) && $attr['link'] == 'none') {
				$link = preg_replace("/<a[^>]*>([^|]*)<\/a>/", "<div>$1</div>", $link);
			}

			// Start itemtag
			$output .= "<{$itemtag} class='gallery-item' style='float: {$float}; width: {$itemwidth}%;'>";

			// icontag
			$output .= "
			<{$icontag} class='gallery-icon'>
				$link
			</{$icontag}>";

			if ( $captiontag && trim( $attachment->post_excerpt ) ) {

				// captiontag
				$output .= "
				<{$captiontag} class='gallery-caption'>
					" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";

			}

			// End itemtag
			$output .= "</{$itemtag}>";

			// Line breaks by columns set
			if($columns > 0 && ++$i % $columns == 0) $output .= '<br style="clear: both">';

		}

		// End gallery output
		$output .= "
			<br style='clear: both;'>
		</div>\n";

		return $output;
	}

	public function cws_custom_categories_postcount_filter ($count) {
		$count = str_replace('</a> (', '<span class="post_count">(', $count);
		$count = str_replace(')', ')</span> </a>', $count);
		return $count;
	}

	public function cws_oembed_wrapper( $html, $url, $args ) {
		return !empty( $html ) ? "<div class='cws_oembed_wrapper'>$html</div>" : '';
	}

	public function cws_custom_excerpt_length( $length ) {
		return 1400;
	}

	public function cws_ajaxurl() {
		wp_localize_script('cws_scripts', 'ajaxurl', array(
			'templateDir' => esc_url( get_template_directory_uri() ),
			'url' => admin_url( 'admin-ajax.php' ),
		));		
	}

	public function cws_ajax_redirect() {
		$ajax = isset( $_POST['ajax'] ) ? (bool)$_POST['ajax'] : false;
		if ( $ajax ) {
			$template = isset( $_POST['template'] ) ? $_POST['template'] : '';
			if ( !empty( $template ) ) {
				if ( strpos( $template, '-' ) ) {
					$template_parts = explode( '-', $template );
					if ( count( $template_parts ) == 2 ) {
						get_template_part( $template_parts[0], $template_parts[1] );
					}
					else {
						return;
					}
				}	else {
					get_template_part( $template );
				}
				exit();
			}
		}
		return;
	}

	public function cws_meta_vars() {}

	public function cws_render_gradient ($arrs) {
		$gradient = array(
			'first_color' => (!empty($arrs[ 'first_color' ]) ? $arrs[ 'first_color' ] : ''),
			'second_color' => (!empty($arrs[ 'second_color' ]) ? $arrs[ 'second_color' ] : ''),
			'first_color_opacity' => (!empty($arrs[ 'first_color_opacity' ]) ? $arrs[ 'first_color_opacity' ] : ''),
			'second_color_opacity' => (!empty($arrs[ 'second_color_opacity' ]) ? $arrs[ 'second_color_opacity' ] : ''),
			'type' => (!empty($arrs[ 'type' ]) ? $arrs[ 'type' ] : ''),
			'linear_settings' => (!empty($arrs[ 'linear_settings' ]) ? $arrs[ 'linear_settings' ] : ''),
			'radial_settings' => array(
				'shape_settings' => (!empty($arrs['radial_settings']['shape_settings']) ? $arrs['radial_settings']['shape_settings'] : ''),
				'shape' => (!empty($arrs['radial_settings']['shape']) ? $arrs['radial_settings']['shape'] : ''),
				'size_keyword' => (!empty($arrs['radial_settings']['size_keyword']) ? $arrs['radial_settings']['size_keyword'] : ''),
				'size' => (!empty($arrs['radial_settings']['size']) ? $arrs['radial_settings']['size'] : ''),
				)
		);
		return $gradient;
	}

	public function cws_page_links() {
		$args = array(
			'before'		   => '',
			'after'			=> '',
			'link_before'	  => '<span>',
			'link_after'	   => '</span>',
			'next_or_number'   => 'number',
			'nextpagelink'	 =>  esc_html__("Next Page",'aasana'),
			'previouspagelink' => esc_html__("Previous Page",'aasana'),
			'pagelink'		 => '%',
			'echo'			 => 0
		);
		$pagination = wp_link_pages( $args );
		echo !empty( $pagination ) ? "<div class='pagination'><div class='page_links'>$pagination</div></div>" : '';
	}

	public function cws_post_output ( $custom_layout_arr = false ) {
		$pid = get_the_id();
		$is_single = is_single( $pid );
		$title = esc_html( get_the_title() );
		$permalink = esc_url( get_the_permalink() );
		$show_author = $this->cws_get_option( "blog_author" ); //$this->cws_get_option( "blog_author" );
		ob_start();

		if (!in_array('categories', $custom_layout_arr['hide']) || !in_array('tags', $custom_layout_arr['hide']) ) {

			ob_start();
				echo "<hr>";
				echo "<div class='post_meta'>";
				
				if (!in_array('categories', $custom_layout_arr['hide'])) {
						if ( has_category() ) {
							echo "<div class='post_category'>";
								ob_start();
									$category_part = the_category (' ');
								$category_part = ob_get_clean();
								echo sprintf("%s", $category_part);
							echo '</div>';
						}
				}

				if (!in_array('tags', $custom_layout_arr['hide'])) {
						if ( has_tag() ) {
							echo "<div class='post_tags'>";
								ob_start();
								$tags_part = the_tags ("", " ", "" );
								$tags_part = ob_get_clean();
								echo sprintf("%s", $tags_part);
							echo '</div>';
						}
				}
				if (function_exists('wsl_activate')) {
					echo "<div class='social_share'>".do_shortcode('[wordpress_social_login]')."</div>";
				};


				echo '</div>';
			$meta_part = ob_get_clean();
			echo sprintf("%s", $meta_part);
		}

		if (!in_array('title', $custom_layout_arr['hide'])) {
			ob_start();
				if ($is_single){
					$title_part = $title;

					$post_format = get_post_format();
					$thumbnail = has_post_thumbnail();
					$media_meta = cws_core_cwsfw_get_post_meta( get_the_ID(), 'cws_mb_post' );
					$media_meta = isset( $media_meta[0] ) ? $media_meta[0] : array();
					$link = isset( $media_meta['link'] ) ? esc_url( $media_meta['link'] ) : '';
				} else {
					$title_part = "<h3><a href='$permalink'>". $title ."</a></h3>";
				}
				echo !empty( $title ) ?	$this::THEME_BEFORE_CE_TITLE . "<div>" . $title_part . '</div>' . $this::THEME_AFTER_CE_TITLE : '';
			$title_full_part = ob_get_clean();
			echo sprintf("%s",$title_full_part );
		}

		$author = '';
		$author .= $show_author ? esc_html(get_the_author()) : '';

		ob_start();
		the_author_posts_link();
		$author_link = ob_get_clean();

		$special_pf = $this->cws_is_special_post_format();
		$comments_n = get_comments_number();

		if (!in_array('meta', $custom_layout_arr['hide'])) {
			ob_start();
				echo '<div class="post_info">';
					if ( !empty($author) || $special_pf ) {
						echo "<div class='info'>";
							ob_start();
								echo !empty($author) ? (esc_html__('by ', 'aasana'))."<span class='post_author'>$author_link</span>" : '';
							$author_part = ob_get_clean();
							echo sprintf("%s", $author_part);
						echo '</div>';
					}
					if(function_exists("cws_vc_shortcode_get_simple_likes_button")){
						echo " <div class='like new_style'>".cws_vc_shortcode_get_simple_likes_button( get_the_ID() )."</div>";
					}
					
					if ( (int) $comments_n > 0 ) {
						$permalink .= "#comments";
						echo "<div class='comments_link'><a href='$permalink'><i class='fa fa-comment-o'></i> $comments_n<span> ".((int) $comments_n == 1 ? esc_html__( "comment",'aasana') : esc_html__( "comments", 'aasana'))."</span></a></div>";
						
						$comments_part = "<a href='$permalink'>$comments_n <span> ".esc_html__("comments", 'aasana')."</span></a>";
					}
				echo '</div>';
			$post_info_part = ob_get_clean();
			echo sprintf("%s", $post_info_part);
		}

		if ($custom_layout_arr['content_divider']  == 1) {
			echo '<hr>';
		}

		if (!in_array('content', $custom_layout_arr['hide'])) {
			ob_start();
				$this->cws_post_content_output( $custom_layout_arr );
			$content_part = ob_get_clean();
			echo sprintf("%s", $content_part);
		}

		$meta_info = ob_get_clean();

		//Normal style
		if ($custom_layout_arr['text_over_image'] != 1) {

			if ($custom_layout_arr['meta_position'] == 'bottom'){
				$this->cws_post_info_part($custom_layout_arr);
				echo sprintf("%s", $meta_info);
			} elseif ($custom_layout_arr['meta_position'] == 'top') {
				$this->cws_post_info_part($custom_layout_arr);
				
				echo (!empty($title_full_part) ? $title_full_part : '');
				echo (!empty($post_info_part) ? $post_info_part : '');
				echo (!empty($content_part) ? do_shortcode($content_part) : '');
				echo (!empty($meta_part) ? $meta_part : '');
				
				
			}
		//Colored style
		} else {
			$meta_info_arr = array(
				'title' => (!empty($title_part) ? $title_part : ''),
				'category' => (!empty($category_part) ? $category_part : ''),
				'tags' => (!empty($tags_part) ? $tags_part : ''),
				'author' => (!empty($author_part) ? $author_part : ''),
				'comments' => (!empty($comments_part) ? $comments_part : ''),
				'content' => (!empty($content_part) ? $content_part : '')
			);
			$this->cws_post_info_part($custom_layout_arr,$meta_info_arr);
		}

		$this->cws_page_links();
	}	
	public function cws_single_post_output ( $custom_layout_arr = false ) {
		$pid = get_the_id();
		$is_single = is_single( $pid );
		$title = esc_html( get_the_title() );
		$permalink = esc_url( get_the_permalink() );
		$show_author = $this->cws_get_option( "blog_author" ); //$this->cws_get_option( "blog_author" );
		ob_start();

		if (!in_array('categories', $custom_layout_arr['hide']) || !in_array('tags', $custom_layout_arr['hide']) ) {

			ob_start();
				echo "<hr>";
				echo "<div class='post_meta'>";
				
				if (!in_array('categories', $custom_layout_arr['hide'])) {
						if ( has_category() ) {
							echo "<div class='post_category'>";
								ob_start();
								$category_part = the_category (' ');
								$category_part = ob_get_clean();
								echo sprintf("%s", $category_part);
							echo '</div>';
						}
				}

				if (!in_array('tags', $custom_layout_arr['hide'])) {
						if ( has_tag() ) {
							echo "<div class='post_tags'>";
								ob_start();
								$tags_part = the_tags ("", " ", "" );
								$tags_part = ob_get_clean();
								echo sprintf("%s", $tags_part);
							echo '</div>';
						}
				}				
				if (!in_array('social', $custom_layout_arr['hide'])) {
					if (function_exists('wsl_activate')) {
						echo "<div class='social_share'>".do_shortcode('[wordpress_social_login]')."</div>";
					};
				}
				echo "<hr>";
				echo '</div>';

			$meta_part = ob_get_clean();
			echo sprintf("%s", $meta_part);
		}

		if (!in_array('title', $custom_layout_arr['hide'])) {
			ob_start();
				if ($is_single){
					$title_part = $title;

					$post_format = get_post_format();
					$thumbnail = has_post_thumbnail();
					$media_meta = cws_core_cwsfw_get_post_meta( get_the_ID(), 'cws_mb_post' );
					$media_meta = isset( $media_meta[0] ) ? $media_meta[0] : array();
					$link = isset( $media_meta['link'] ) ? esc_url( $media_meta['link'] ) : '';

				} else {
					$title_part = "<h3><a href='$permalink'>". $title ."</a></h3>";
				}
				echo !empty( $title ) ?	$this::THEME_BEFORE_CE_TITLE . "<div>" . $title_part . '</div>' . $this::THEME_AFTER_CE_TITLE : '';
			$title_full_part = ob_get_clean();
			echo sprintf("%s", $title_full_part);
		}

		$author = '';
		$author .= $show_author ? esc_html(get_the_author()) : '';

		ob_start();
		the_author_posts_link();
		$author_link = ob_get_clean();

		$special_pf = $this->cws_is_special_post_format();
		$comments_n = get_comments_number();

		if (!in_array('meta', $custom_layout_arr['hide'])) {
			ob_start();
				echo '<div class="post_info">';
					if ( !empty($author) || $special_pf ) {
						echo "<div class='info'>";
							ob_start();
								echo !empty($author) ? (esc_html__('by ', 'aasana'))."<span class='post_author'>$author_link</span>" : '';
							$author_part = ob_get_clean();
							echo sprintf("%s", $author_part);
						echo '</div>';
					}
					if(function_exists("cws_vc_shortcode_get_simple_likes_button")){
						echo " <div class='like new_style'>".cws_vc_shortcode_get_simple_likes_button( get_the_ID() )."</div>";
					}
					
					if ( (int) $comments_n > 0 ) {
						$permalink .= "#comments";
						echo "<div class='comments_link'><a href='$permalink'><i class='fa fa-comment-o'></i>$comments_n<span> ".((int) $comments_n == 1 ? esc_html__( "comment",'aasana') : esc_html__( "comments", 'aasana'))."</span></a></div>";
						
						$comments_part = "<a href='$permalink'>$comments_n <span> ".esc_html__("comments", 'aasana')."</span></a>";
					}
				echo '</div>';
			$post_info_part = ob_get_clean();
			echo sprintf("%s", $post_info_part);
		}

		if (!in_array('content', $custom_layout_arr['hide'])) {
			ob_start();
				$this->cws_post_content_output( $custom_layout_arr );
			$content_part = ob_get_clean();
			echo sprintf("%s", $content_part);
		}

		$meta_info = ob_get_clean();

		//Normal style
		if ($custom_layout_arr['text_over_image'] != 1) {

			if ($custom_layout_arr['meta_position'] == 'bottom'){
				$this->cws_post_info_part($custom_layout_arr);
				echo sprintf("%s", $meta_info) ;
			} elseif ($custom_layout_arr['meta_position'] == 'top') {
				
				
				echo (!empty($title_full_part) ? $title_full_part : '');
				echo (!empty($post_info_part) ? $post_info_part : '');
				$this->cws_post_info_part($custom_layout_arr);
				echo (!empty($content_part) ? $content_part : '');
				
				echo (!empty($meta_part) ? $meta_part : '');
				
				
			}
		//Colored style
		} else {
			$meta_info_arr = array(
				'title' => (!empty($title_part) ? $title_part : ''),
				'category' => (!empty($category_part) ? $category_part : ''),
				'tags' => (!empty($tags_part) ? $tags_part : ''),
				'author' => (!empty($author_part) ? $author_part : ''),
				'comments' => (!empty($comments_part) ? $comments_part : ''),
				'content' => (!empty($content_part) ? $content_part : '')
			);
			$this->cws_post_info_part($custom_layout_arr,$meta_info_arr);
		}

		$this->cws_page_links();
	}
	public function cws_post_content_output ( $custom_layout_arr = false ) {
		global $post;
		global $more;
		$old_version = $more = 0;
		$content = $button_word = '';
		$button_add = false;
		$chars_count = $this->cws_blog_get_chars_count( $custom_layout_arr['column_count'] );
		$char_length = intval( $custom_layout_arr['post_text_length'] ) !== 0 ? intval( $custom_layout_arr['post_text_length'] ) : $chars_count;

		if ( is_single() ) {
			if(strpos( (string) $post->post_content, '<!--more-->' )){
				$content .= apply_filters('the_content', $post->post_content);
			}	else {
				$content .= apply_filters('the_content', get_the_content());
			}
		} else {
			if ( ! empty( $post->post_excerpt ) ) {
				$content .= $post->post_excerpt;
			} else {
				$button_word = esc_html__( 'Read More', 'aasana' );
				$pos = strpos( (string) $post->post_content, '<!--more-->' );
				if ( $pos ) {
					$button_add = true;
				}
				$content .= get_the_content( '[...]' );
			}
		}

		//$content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]>', $content);

		if ( $custom_layout_arr['this_shortcode'] ) {
			$content = ! empty( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;
			if ($custom_layout_arr['dropcap'] == '1'){	
				//If exist shortcode	
				if (strpos( $content, 'cws_sc_dropcap' )){
					$full_content = trim( preg_replace( '/[\s]{2,}/u', ' ', strip_tags( $content )  ) );
					$without_dropcap = trim( preg_replace( '/(\[cws_sc_dropcap)(.*)(cws_sc_dropcap])/', ' ', strip_tags( $full_content ) ) );
					$content = $full_content;	
					$diff_length = strlen($full_content) - strlen($without_dropcap);
					$char_length = $char_length + $diff_length;
				}
			} else {
				preg_match('/\[cws_sc_dropcap.*\](.*)\[\/cws_sc_dropcap\]/', $content, $matches);
				$letter = isset($matches[1]) ? $matches[1] : '';
				$without_shortcodes_content = trim( preg_replace( '/[\s]{2,}/u', ' ', strip_shortcodes( strip_tags( $content ) ) ) );
				$content = $letter.$without_shortcodes_content;
			}
			$content = wptexturize( $content );
			$content_length = strlen( $content );
			if ( $content_length > $char_length ) {
				$button_add = false;
				$content = mb_substr( $content, 0, $char_length );
				if ( strlen( $custom_layout_arr['button_name'] ) !== 0 && $custom_layout_arr['custom_layout'] == 1 ) {
					$button_add = true;
					$button_word = esc_html( $custom_layout_arr['button_name'] );
				} else if ( $custom_layout_arr['custom_layout'] == 0 ) {
					$button_add = true;
					$button_word = esc_html__( 'Read More', 'aasana' );
				}
				$content .= "<a class='p_cut' href='".esc_url( get_the_permalink() )."'> ...</a>";
			}
		}

		if (! empty( $content ) ) {
			echo "<div class='post_content clearfix'>" . ($custom_layout_arr['is_related'] == '1' ? apply_filters('the_content', $content) : $content);
			if ( $button_add && $custom_layout_arr['text_over_image'] != 1) {
				echo "<div class='button_cont clearfix btn-read-more".($custom_layout_arr['custom_layout'] == '1' ? ' button_align_'.$custom_layout_arr['button_align'] : '')."'><a href='".esc_url( get_the_permalink() )."' class='cws_button read-more regular'>" . $button_word . '</a></div>';
			}
			echo '</div>';
		}else{
			if ( $button_add && $custom_layout_arr['text_over_image'] != 1) {
				echo "<div class='button_cont clearfix btn-read-more".($custom_layout_arr['custom_layout'] == '1' ? ' button_align_'.$custom_layout_arr['button_align'] : '')."'><a href='".esc_url( get_the_permalink() )."' class='cws_button read-more regular'>" . $button_word . '</a></div>';
			}
		}
	}

	public function cws_get_grid_shortcodes() {
		return array( 'cws-row', 'col', 'cws-widget' );
	}

	public function cws_get_special_post_formats() {
		return array( 'aside' );
	}

	public function cws_is_special_post_format() {
		global $post;
		$sp_post_formats = $this->cws_get_special_post_formats();
		if ( isset($post) ) {
			return in_array( get_post_format(), $sp_post_formats );
		} else{
			return false;
		}
	}

	public function cws_post_format_mark() {
		global $post;
		$out = '';
		if ( isset( $post ) ) {
			$pf = get_post_format();
			$post_format_icons = array(
				'aside' => 'bullseye',
				'gallery' =>'bullseye',
				'link' => 'chain',
				'image' => 'image',
				'quote' => 'quote-lef',
				'status' => 'flag',
				'video' => 'video-camer',
				'audio' => 'music',
				'chat' => 'wechat',
			);
			$icon = '';
			if (isset($post_format_icons[$pf])) {
				$icon = $post_format_icons[$pf];
			}
			$out = "<i class='fa fa-$icon'></i> $pf";
		}
		return $out;
	}

	public function cws_strip_grid_shortcodes($text) {
		$shortcodes = function_exists('aasana_get_grid_shortcodes') ? aasana_get_grid_shortcodes () : "";
		$find = array();
		if(!empty($shortcodes)){
			foreach ( $shortcodes as $shortcode ) {
				$shortcode = preg_replace( "|-|", "\-", $shortcode );
				$op_tag = "|\[.*" . $shortcode . ".*\]|";
				$cl_tag = "|\[/.*" . $shortcode . ".*\]|";
				array_push( $find, $op_tag, $cl_tag );
			}			
		}

		$text = preg_replace( $find, '', $text );
		return $text;
	}

	// Check if WooCommerce is active
	public function cws_woo_ajax_remove_from_cart() {
		global $woocommerce;

		$woocommerce->cart->set_quantity( $_POST['remove_item'], 0 );

		$ver = explode( '.', WC_VERSION );

		if ( $ver[1] == 1 && $ver[2] >= 2 ) :
			$wc_ajax = new WC_AJAX();
			$wc_ajax->get_refreshed_fragments();
		else :
			woocommerce_get_refreshed_fragments();
		endif;

		die();
	}

	public function cws_woo_header_add_to_cart_fragment( $fragments ) {
		ob_start();
		?>
			<i class='woo_mini-count flaticon-shopcart-icon-aasana'><?php echo ((WC()->cart->cart_contents_count > 0) ?  '<span>' . WC()->cart->cart_contents_count .'</span>' : '') ?></i>
		<?php
		$fragments['.woo_mini-count'] = ob_get_clean();

		ob_start();
		woocommerce_mini_cart();
		$fragments['div.woo_mini_cart'] = ob_get_clean();
		return $fragments;
	}

	public function cws_woo_related_products_args( $args ) {
		$args['posts_per_page'] = $this->cws_get_option( 'woo-resent-num-products' ); // 4 related products
		$args['columns'] = 3; // arranged in 2 columns
		return $args;
	}

	/************** JAVASCRIPT VARIABLES INIT **************/
	public function cws_js_vars_init() {
		$is_user_logged = is_user_logged_in();
		$logged_var = $is_user_logged ? 'true' : 'false';
		$stick_mode = $this->cws_get_meta_option('stick-mode');
		$sticky_menu_mode = !empty($stick_mode) ? $stick_mode : 'false';
		$stick_menu =  $this->cws_get_meta_option('menu-stick') == '1' ? 'true' : 'false';
		$sticky_on_mobile = $this->cws_get_meta_option('stick-on-mobile') == '1' ? 'true' : 'false';
		$sticky_sidebars = $this->cws_get_meta_option('sticky_sidebars') == '1' ? 'true' : 'false';
		$page_loader = $this->cws_get_meta_option('show_loader') == '1' ? 'true' : 'false';
		//Don't forget boolean value without ''
		wp_add_inline_script('cws_scripts', '
			var is_user_logged = '.$logged_var.','.
			'stick_menu = '.$stick_menu.','.
			'sticky_menu_mode = "'.$sticky_menu_mode.'",'.
			'sticky_on_mobile = '.$sticky_on_mobile.','.
			'sticky_sidebars = '.$sticky_sidebars.','.
			'page_loader = '.$page_loader.','.
			'use_blur = false;');
	}
	/************** \JAVASCRIPT VARIABLES INIT **************/

	/******************** TYPOGRAPHY ********************/
	// MENU FONT HOOK
	private function cws_print_font_css($font_array) {
		$out = '';
		foreach ($font_array as $style=>$v) {
			if ($style != 'font-weight' && $style != 'font-sub' && $style != 'font-type') {
				$out .= !empty($v) ? $style .':'.$v.';' : '';
			}
		}
		return $out;
	}

	private function cws_print_menu_font() {
		ob_start();
		do_action( 'menu_font_hook' );
		return ob_get_clean();
	}

	public function cws_menu_font_action() {
		$out = '';
		$font_array = $this->cws_get_meta_option('menu-font');
		$customize_title_area = $this->cws_get_meta_option( 'customize-title-area' );
		$slider_settings = $this->cws_get_meta_option( 'slider_override' );

		if (isset($font_array)) {
			$out .= '
			.mobile_menu .menu-item a,
			.main-nav-container .menu-item a,
			.main-nav-container .main-menu > .menu-item>a, 
			.main-nav-container .main-menu > .menu-item>span,
			.main-nav-container .menu-item .button_open,
			.mobile_menu_header,
			.site_name a
			{'
				. esc_attr($this->cws_print_font_css($font_array)) . ';
			}';

			$out .= '
			.main-nav-container .topbar-menu > .menu-item > a
			{
				line-height: inherit;
			}';

			$out .= '
			.header_container .menu_left_icons a,
			.header_container .menu_right_icons a,
			.main-nav-container .search_menu,
			.header_container .side_panel_icon,
			.main-nav-container .mini-cart,
			.header_wrapper_container .mobile_menu_wrapper .mini-cart,
			.header_wrapper_container .site_header .mobile_menu_wrapper .search_menu
			{
				color : '. esc_attr($font_array["color"]) . ';
			}';

			$out .= '
			.main-menu .search_menu
			{
			font-size : '. esc_attr($font_array["font-size"]) . ';
			}';

			$out .= '
			.menu-bar .ham,
			.menu-bar .ham:after,
			.menu-bar .ham:before,
			.menu_box .mobile_menu_hamburger span,
			.menu_box .mobile_menu_hamburger span::before,
			.menu_box .mobile_menu_hamburger span::after
			{
				background : '. esc_attr($this->cws_Hex2RGBA($font_array["color"],1)) . ';
			}';
		}

		$menu_font_color = $this->cws_get_meta_option('menu_font_color'); // !!! sections should have this value too

		$show_header_outside_slider = $this->cws_get_meta_option('show_header_outside_slider') == '1' || $this->cws_get_option( 'shop-slider-type' ) != 'none';

		$p_type = get_post_type();

		// !!! need to decide when to show colored menu
		if (!$this->cws_is_woo() &&  'cws_' !== substr($p_type, 0, 4) ){
			$out .= '
			.main-nav-container .main-menu > .menu-item>a,
			.header_container .side_panel_icon, .header_site_title,
			.header_wrapper_container .header_nav_part:not(.mobile_nav) .main-nav-container > .main-menu > .menu-item:not(.current-menu-ancestor):not(:hover):not(.current-menu-item) > a,
			.header_wrapper_container .mini-cart,
			.header_wrapper_container .site_header .search_menu,
			.site_name a,
			.site_header .header_logo_part.logo_with_text .header_site_title,
			.header_container .menu_left_icon_bar a,
			.header_container .menu_right_icon_bar a,

			.header_container .menu_left_icons a,
			.header_container .menu_right_icons a,
			.header_container .search_menu
			{
				color : '. esc_attr($menu_font_color) . ';
			}';

			$out .= '
			.menu-bar .ham,
			.menu-bar .ham:after,
			.menu-bar .ham:before,
			.menu_box .mobile_menu_hamburger span,
			.menu_box .mobile_menu_hamburger span::before,
			.menu_box .mobile_menu_hamburger span::after
			{
				background : '. esc_attr($menu_font_color) . ';
			}';
		}

		if ($this->cws_get_meta_option( 'customizer_header' ) == '1' ) {
			$menu_font_color = $this->cws_get_meta_option('override_menu_color');
			$menu_border_color = $this->cws_get_meta_option('override_menu_border');
			$out .= '
			.header_zone .main-nav-container .main-menu > .menu-item>a,
			.header_zone .main-nav-container .main-menu > .menu-item>span,
			.header_container .header_zone .side_panel_icon,
			.header_zone .menu_box .header_site_title,
			.header_wrapper_container .header_zone .header_nav_part:not(.mobile_nav) .main-nav-container > .main-menu > .menu-item:not(.current-menu-ancestor):not(:hover):not(.current-menu-item) > a,
			.header_wrapper_container .header_zone .mini-cart,
			.header_wrapper_container .header_zone .site_header .search_menu,
			.header_zone .site_name a,
			.site_header .header_zone .header_logo_part.logo_with_text .header_site_title,
			.header_container .header_zone .menu_left_icon_bar a,
			.header_container .header_zone .menu_right_icon_bar a,

			.header_container .header_zone .menu_left_icons a,
			.header_container .header_zone .menu_right_icons a,
			.header_container .header_zone .search_menu
			{
				color : '. esc_attr($menu_font_color) . ';
				stroke : '. esc_attr($menu_font_color) . ';
			}';
			$out .= '.header_zone .main-nav-container .main-menu > .menu-item>a:before, 
			.header_zone .main-nav-container .main-menu > .menu-item>span:before{
				border-color : '. esc_attr($menu_border_color) . ';
			}';			
			$out .= '.side_panel_icon.flaticon-squares span{
				border-color : '. esc_attr($menu_font_color) . ';
				background-color : '. esc_attr($menu_font_color) . ';
			}';


			$out .= '
			.header_container .header_zone .menu_box .mobile_menu_hamburger span,
			.header_container .header_zone .menu_box .mobile_menu_hamburger span::before,
			.header_container .menu_left_icons>*:after,
			.main-nav-container .main-menu > .menu-item.wpml-ls-menu-item > a:before,
			.header_container .menu_right_icons>*:after,
			.header_container .header_zone .menu_box .mobile_menu_hamburger span::after
			{
				background : '. esc_attr($menu_font_color) . ';
			}';

		}		
		if ($this->cws_get_option( 'woo_customize_menu' ) == '1' && $this->cws_is_woo() ) {
			$menu_font_color = $this->cws_get_option('woo_menu_font_color');
			$menu_border_color = $this->cws_get_option('woo_menu_border_color');
			$out .= '
			.header_zone .main-nav-container .main-menu > .menu-item>a,
			.header_zone .main-nav-container .main-menu > .menu-item>span,
			.header_container .header_zone .side_panel_icon,
			.header_zone .menu_box .header_site_title,
			.header_wrapper_container .header_zone .header_nav_part:not(.mobile_nav) .main-nav-container > .main-menu > .menu-item:not(.current-menu-ancestor):not(:hover):not(.current-menu-item) > a,
			.header_wrapper_container .header_zone .mini-cart,
			.header_wrapper_container .header_zone .site_header .search_menu,
			.header_zone .site_name a,
			.site_header .header_zone .header_logo_part.logo_with_text .header_site_title,
			.header_container .header_zone .menu_left_icon_bar a,
			.header_container .header_zone .menu_right_icon_bar a,

			.header_container .header_zone .menu_left_icons a,
			.header_container .header_zone .menu_right_icons a,
			.header_container .header_zone .search_menu
			{
				color : '. esc_attr($menu_font_color) . ';
				stroke : '. esc_attr($menu_font_color) . ';
			}';
			$out .= '.woocommerce .main-nav-container .main-menu > .menu-item>a:before, 
			.woocommerce .main-nav-container .main-menu > .menu-item>span:before{
				border-color : '. esc_attr($menu_border_color) . ';
			}';

			$out .= '
			.header_container .header_zone .menu_box .mobile_menu_hamburger span,
			.header_container .menu_left_icons>*:after,
			.header_container .menu_right_icons>*:after,
			.main-nav-container .main-menu > .menu-item.wpml-ls-menu-item > a:before,
			.header_container .header_zone .menu_box .mobile_menu_hamburger span::before,
			.header_container .header_zone .menu_box .mobile_menu_hamburger span::after
			{
				background : '. esc_attr($menu_font_color) . ';
			}';

		}
		echo preg_replace('/\s+/',' ', $out);
	}

	// \MENU FONT HOOK

	// HEADER FONT HOOK

	private function cws_print_header_font () {
		ob_start();
		do_action( 'header_font_hook' );
		return ob_get_clean();
	}

	public function cws_header_font_action () {
		$out = '';
		$font_array = $this->cws_get_option('header-font');

		if (isset($font_array)) {
			$out .=  "
				.news .ce_title a.link_post,
				.cws_portfolio_items .post_info.outside .title_part a,
				.gallery-icon + .gallery-caption,
				.vc_general.vc_tta.vc_tta-tabs .vc_tta-tab .vc_tta-title-text,
				.cta_subtitle,
				.cta_title,
				.grid_row.single_related .carousel_nav span,
				.cta_desc_subtitle,
				.tribe-nav-label,
				.cta_offer + .cta_banner .cws_vc_shortcode_cta_banner .cws_vc_shortcode_banner_title,
				.cta_offer + .cta_banner .cws_vc_shortcode_cta_banner .cws_vc_shortcode_banner_price,
				.cta_offer + .cta_banner .cws_vc_shortcode_cta_banner .cws_vc_shortcode_banner_desc,
				.cws_vc_shortcode_pricing_plan .pricing_plan_price .price,
				form.wpcf7-form > div:not(.wpcf7-response-output)>p,
				.page_title .page_excerpt, 
				.page_content > main .grid_row.cws_tribe_events #tribe-bar-form label,
				form.wpcf7-form > div:not(.wpcf7-response-output)>label,
				#tribe-events-footer .tribe-events-sub-nav .tribe-events-nav-next a, #tribe-events-header .tribe-events-sub-nav .tribe-events-nav-next a,
				#tribe-events-footer .tribe-events-sub-nav li a, #tribe-events-header .tribe-events-sub-nav li a
				{
					font-family: ". esc_attr($font_array['font-family']) .";
				}";
			$out .= '
				.ce_title, figcaption .title_info h3, .aasana-new-layout .cws-widget .widget-title,.woo_product_post_title.posts_grid_post_title, .comments-area .comment-reply-title,
				.woocommerce div[class^="post-"] h1.product_title.entry-title, .page_title.customized .title h1, .bg_page_header .title h1
				{'
				. esc_attr($this->cws_print_font_css($font_array)) .
				'}';
			$out .= '
				.testimonial .author figcaption,
				.testimonial .quote .quote_link:hover,
				.pagination a,
				.widget-title,
				.ce_toggle.alt .accordion_title:hover,
				.pricing_table_column .price_section,
				.comments-area .comments_title,
				.comments-area .comment-meta,
				.comments-area .comment-reply-title,
				.comments-area .comment-respond .comment-form input:not([type=\'submit\']),
				.comments-area .comment-respond .comment-form textarea,
				.page_title .bread-crumbs,
				.benefits_container .cws_textwidget_content .link a:hover,
				.cws_portfolio_fw .title,
				.cws_portfolio_fw .cats a:hover,
				.msg_404,
				.cws_portfolio_items .post_info.outside .title_part a
				{
					color:' . esc_attr($font_array['color']) . ';
				}';
				$out .=  "h1, h2, h3, h4, h5, h6
				{
					font-family: ". esc_attr($font_array['font-family']) .";
					color: ". esc_attr($font_array['color']) .";
				}";
				$out .=  ".item .post_title a,.item .post_title a:hover,.news .ce_title a,.news .ce_title a:hover
				{
					color: ". esc_attr($font_array['color']) .";
				}";
				$out .= '.posts_grid.cws_portfolio_posts_grid > h2
					{
						font-size:' . esc_attr($font_array['font-size']) . ';
					}';

		}
		echo preg_replace('/\s+/',' ', $out);
	}

	// \HEADER FONT HOOK

	// BODY FONT HOOK

	private function cws_print_body_font () {
		ob_start();
		do_action( 'body_font_hook' );
		return ob_get_clean();
	}

	public function cws_body_font_action () {
		$out = '';
		$font_array = $this->cws_get_option('body-font');
		if (isset($font_array)) {
			$out .= 'body
				{
				'. esc_attr($this->cws_print_font_css($font_array)) .
				'}';
			$out .= '.news .ce_title a,
					.tribe-this-week-events-widget .tribe-this-week-widget-horizontal .entry-title,.tribe-this-week-events-widget  .tribe-this-week-widget-horizontal .entry-title a,
					form.wpcf7-form > div:not(.wpcf7-response-output)>p span, 
					.main-nav-container .sub-menu .cws_megamenu_item .widgettitle,
				form.wpcf7-form > div:not(.wpcf7-response-output)>label span,
				.tribe-events-schedule h2,
				.aasana-new-layout .tooltipster-light .tooltipster-content
					{
						font-family:'. esc_attr($font_array['font-family']) . ';
					}';
			$out .= '.cws-widget ul li>a,
					.comments-area .comments_nav.carousel_nav_panel a,
					.cws_img_navigation.carousel_nav_panel a,
					.cws_portfolio_fw .cats a,
					.cws_portfolio .categories a,
					.row_bg .ce_accordion.alt .accordion_title,
					.row_bg .ce_toggle .accordion_title,
					.mini-cart .woo_mini_cart,
					blockquote p cite,
					.lang_bar ul li ul li a,
					.thumb_staff_posts_title a,
					.tribe-this-week-widget-wrapper .tribe-this-week-widget-day .duration, .tribe-this-week-widget-wrapper .tribe-this-week-widget-day .tribe-venue,
					.thumb_staff_posts_title,
					#mc_embed_signup input,
					.mc4wp-form .mc4wp-form-fields input,
					form.wpcf7-form > div:not(.wpcf7-response-output) .select2-selection--single .select2-selection__rendered,.cws-widget #wp-calendar tbody td a:hover,
					.tribe-mini-calendar .tribe-events-has-events div[id*="daynum-"] a:hover,
					.cws-widget #wp-calendar td:hover, .cws-widget #wp-calendar th:hover,.cws-widget #wp-calendar tfoot td#prev:hover a:before,
					form.wpcf7-form > div:not(.wpcf7-response-output) .select2-selection--single .select2-selection__arrow b,
					.main-nav-container .sub-menu .cws_megamenu_item .widgettitle,
					.vc_general.vc_tta.vc_tta-tabs .vc_tta-tabs-list .vc_tta-tab, .tabs.wc-tabs li,
					.cws-widget .post_item .post_title a,
					#tribe-events-content .tribe-events-calendar div[id*=tribe-events-event-] h3.tribe-events-month-event-title,
					.tribe-events-calendar td.tribe-events-past div[id*=tribe-events-daynum-],
					.tribe-events-calendar td.tribe-events-present div[id*=tribe-events-daynum-],
					.tribe-events-calendar td.tribe-events-past div[id*=tribe-events-daynum-]>a,
					#tribe-events-content .tribe-events-calendar div[id*=tribe-events-event-] h3.tribe-events-month-event-title a,
					.tribe-events-calendar td div[id*=tribe-events-daynum-] > a,
					.posts_grid .portfolio_item_post.under_img .cws_portfolio_posts_grid_post_content
					{
						color:' . esc_attr($font_array['color']) . ';
					}';
			$out .= '.mini-cart .woo_mini_cart,
					body input,body  textarea
					{
						font-size:' . esc_attr($font_array['font-size']) . ';
					}';
			$out .= 'body input,body  textarea
					{
						line-height:' . esc_attr($font_array['line-height']) . ';
					}';
			$out .= 'abbr
					{
						border-bottom-color:' . esc_attr($font_array['color']) . ';
					}';
			$fs_match = preg_match( '#(\d+)(.*)#', $font_array['font-size'], $fs_matches );
			$lh_match = preg_match( '#(\d+)(.*)#', $font_array['line-height'], $lh_matches );
			if ( $fs_match && $lh_match ) {
				$fs_number = (int)$fs_matches[1];
				$fs_units = $fs_matches[2];
				$lh_number = (int)$lh_matches[1];
				$lh_units = $lh_matches[2];
				$out .= "
				.dropcap{
					font-size:" . esc_attr($fs_number * 2 . $fs_units).";
					line-height:" . esc_attr($lh_number * 2 . $lh_units).";
					width:" . esc_attr($lh_number * 2 . $lh_units).";
				}";
			}
		}
		echo preg_replace('/\s+/',' ', $out);
	}

	// \BODY FONT HOOK

	private function cws_print_helper_font () {
		ob_start();
		do_action( 'body_helper_hook' );
		return ob_get_clean();
	}

	// HELPER FONT HOOK
	public function cws_body_helper_action() {
		$out = '';
		$font_array = $this->cws_get_option('helper-font');

		if (isset($font_array)) {
			$out .= '
				.category-images .grid .item .category-wrapper .category-label-wrapper .category-label,
				.page_footer.instagram_feed #sb_instagram .sbi_follow_btn a,
				.header_wrapper_container .header_site_title
				{
					font-family:'. esc_attr($font_array['font-family']) . ';
				}';
		}

		echo preg_replace('/\s+/',' ', $out);
	}
	// \HELPER FONT HOOK

	public function cws_process_fonts() {
		$out = $this->cws_print_menu_font();
		$out .= $this->cws_print_header_font();
		$out .= $this->cws_print_body_font();
		$out .= $this->cws_print_helper_font();
		return $out;
	}

	/*
	prints specially prepared structure from theme options
	*/
	public function cws_print_background($props = null) {
		$out = '';
		if ($props && is_array($props)) {
			$out = 'background:';
			foreach ($props as $key => $value) {
				if ('url' === $key) {
					$out .= sprintf('url(%s))', $value['src']);
				} else {
					$out .= $value;
				}
				$out .= ' ';
			}
			$out .= ';';
		}
		return $out;
	}

	public function cws_process_blur() {
		$blur_intensity = $this->cws_get_option('blur_intensity');
		$use_blur = $this->cws_get_option('use_blur');
		$use_blur = isset($use_blur) && !empty($use_blur) && ($use_blur == '1') ? true : false;
		if (!$use_blur) return;
		$out = '.pic.blured img.blured-img,
			.item .pic_alt .img_cont>img.blured-img,
			.pic .img_cont>img.blured-img,
			.cws-widget .post_item .post_thumb:hover img,
			.cws_img_frame:hover img,
			.cws-widget .portfolio_item_thumb .pic .blured-img{
				-webkit-filter: blur('.$blur_intensity.'px);
				-moz-filter: blur('.$blur_intensity.'px);
				-o-filter: blur('.$blur_intensity.'px);
				-ms-filter: blur('.$blur_intensity.'px);
				filter: blur('.$blur_intensity.'px);
		}';
		return $out;
	}	

	public function cws_loader() {
		$cws_loader = $this->cws_get_option('overlay_loader_color');
		if(!empty($cws_loader)){
			$out = '#cws_page_loader .inner:before{
				    background-image: -webkit-linear-gradient(top, '.$cws_loader.', '.$cws_loader.');
	    			background-image: -moz-linear-gradient(top, '.$cws_loader.', '.$cws_loader.');
	   				background-image: linear-gradient(to bottom, '.$cws_loader.', '.$cws_loader.');
			}';		
			$out .= '#cws_page_loader .inner:after{
				    background-image: -webkit-linear-gradient(top, #ffffff, '.$cws_loader.');
	    			background-image: -moz-linear-gradient(top, #ffffff, '.$cws_loader.');
	   				background-image: linear-gradient(to bottom, #ffffff, '.$cws_loader.');
			}';
			return $out;			
		}

	}	

	public function cws_mobile() {
		$mobile_menu_type = $this->cws_get_meta_option('mobile_background' );
		$mobile_menu_color = $this->cws_get_meta_option('mobile_overlayc' );
		$mobile_menu_color = $this->cws_Hex2RGB($mobile_menu_color);
		$mobile_menu_opacity = $this->cws_get_meta_option('mobile_overlay_opacity' ) ? ($this->cws_get_meta_option('mobile_overlay_opacity' ))/100 : 1;
		$mobile_menu_style = 'rgba('.$mobile_menu_color.','.$mobile_menu_opacity.')';
		$mobile_menu_gradient = $this->cws_get_meta_option('mobile_gradient_settings' );

		$is_page_color = $this->cws_get_meta_option('is_theme_color');
		$main_secondary_color = !empty($is_page_color) ? $this->cws_get_meta_option('theme-main-secondary-color') : $this->cws_get_option('theme-main-secondary-color');

		$mobile_menu_settings = $this->cws_render_gradient($mobile_menu_gradient); 

		$out = '';
		if (  $mobile_menu_type == 'color' && !empty( $mobile_menu_color ) ){
			
			$out .= '@media screen and ( max-width: 979px ){';
			$out .= '.header_wrapper_container.header_outside_slider{
				background-color: '.$mobile_menu_style.';
			}';	
			$out .= '}';
			return $out;
		}
		else if ( $mobile_menu_type == 'gradient' && $mobile_menu_type == 'gradient' ){
			$out .= '@media screen and ( max-width: 979px ){';
			$settings = $this->cws_render_gradient_rules( array( 'settings' => $mobile_menu_settings ) );
			$out .= '.header_wrapper_container.header_outside_slider:after{
				'.$settings.'
			}';	
			$out .= '.header_wrapper_container.header_outside_slider:after{
				opacity: '.$mobile_menu_opacity.';
			}';	

			$out .= '}';
			return $out;
		}else{
			$out .= '@media screen and ( max-width: 979px ){';
			$out .= '.header_wrapper_container.header_outside_slider{
				background-color:'.esc_attr($main_secondary_color).';
			}';	
			$out .= '}';
			return $out;
		}


	}

	/* THEME FOOTER */
	public function cws_page_footer (){

		$footer = 	$this->cws_get_meta_option('footer');
		$footer_img_settings = isset($footer['footer_img_settings']) ? $footer['footer_img_settings'] : "";
		$footer_bg_im = isset($footer['footer_img_settings']) ? $footer['footer_img_settings'] : "";
		$footer_settings = isset($footer['footer_img_settings']) ? $footer['footer_img_settings'] : "";
		$footer_bg_im = (!empty($footer_bg_im['footer_bg_im']) ? $footer_bg_im['footer_bg_im'] : "");
		$footer_pattern = isset($footer['footer_pattern']) ? $footer['footer_pattern'] : "";
		$footer_img_set = '';
		if ( !empty($footer_bg_im["src"]) ) {
			$footer_img_set .= "background-image: url(".$footer_bg_im['src'].");";
			$footer_img_set .= " background-size: ".$footer_settings['footer_img_size'].";";
			$footer_img_set .= " background-position: ".$footer_settings['footer_img_pos_x']." ".$footer_settings['footer_img_pos_y'] .";";
			$footer_img_set .= " background-repeat: ".$footer_settings['footer_img_repeat'].";";
			$footer_img_set .= " background-attachment: ".$footer_settings['footer_img_attachment'].";";

		}
		
		$footer_sidebar = isset($footer['footer_sidebar']) ? $footer['footer_sidebar'] : "";
		$footer_layout = '';
		$footer_layout = isset($footer['footer_layout']) ? $footer['footer_layout'] : "";
		$is_instagram = isset($footer['footer_instagram_feed']) ? $footer['footer_instagram_feed'] : "";
		$footer_sidebar_position = isset($footer['footer_sidebar_position']) ? $footer['footer_sidebar_position'] : "";
		$footer_text_alignment = isset($footer['footer_text_alignment']) ? $footer['footer_text_alignment'] : "";

		$instagramm_html = $instagramm_html_full = '';
		$is_instagram_full = false;
		if ($is_instagram == '1') {
			$is_instagram_full = $footer['footer_instagram_feed_full_width'] == '1';
			$instagram_content = do_shortcode($footer['instagram_feed_shortcode']);
			if ($is_instagram_full) {
				$instagramm_html_full = $instagram_content;
			} else {
				$instagramm_html = $instagram_content;
			}
		}
		$footer_spacings = $footer['footer_spacings'];
		if ( isset( $footer_spacings ) ){
			$footer_spacings_styles = '';
			foreach ( $footer_spacings as $key => $value ){
				if ( !empty( $value ) || $value == '0' ){
						$footer_spacings_styles .= "padding-".$key . ": " . $value . "px;";
				}
			}
		}
		$footer_class = isset($footer['footer_fixed_style']) && $footer['footer_fixed_style'] == '1' ? ' footer_fixed fixed' : '';
		$footer_style = !empty($footer_img_set) ? $footer_img_set : "";
		$footer_style .= !empty($footer_spacings["top"]) || !empty($footer_spacings["bottom"]) ? esc_attr($footer_spacings_styles): '';
		echo sprintf("%s", $instagramm_html_full) ;
		if ( !empty( $footer_sidebar ) && is_active_sidebar( $footer_sidebar ) ) {
			echo "<footer class='page_footer".$footer_class."' style='".$footer_style."'>";
				if ( !empty($footer_pattern["src"]) ) {
					echo "<div class='footer_container_pattern'>";
					echo "<div class='footer-pattern' style='background-image: url(".$footer_pattern['src'].");'></div>";
					echo "</div>";
				}	
				echo "<div class='container'>";
					echo sprintf("%s", $instagramm_html);
					echo "<div class='footer_container".(!empty($footer_layout) ? ' col-'.$footer_layout : " col-4").(!empty($footer_sidebar_position) ? ' align-'.$footer_sidebar_position : " align-center").(!empty($footer_text_alignment) ? ' txt_align_'.$footer_text_alignment : " txt_align_center")."'>";
						if ( is_active_sidebar( $footer_sidebar ) ) {
							dynamic_sidebar( $footer_sidebar );
						}
					echo "</div>";
				echo "</div>";
			echo "</footer>";
		}

		$copyrights = $footer["footer_copyrights_text"];
		$menu_copyrights = get_nav_menu_locations();
		
		$copyrights_menu_position = isset($footer['copyrights_menu_position']) ? $footer['copyrights_menu_position'] : "";
		$show_copyrights_menu = isset($footer['show_copyrights_menu']) ? $footer['show_copyrights_menu'] : "";
		ob_start();
		if ( !empty( $show_copyrights_menu ) ) {
			echo "<div class='copyrights_menu a-".(!empty($copyrights_menu_position) ? $copyrights_menu_position : "left")."' >";
				if ( !empty($menu_copyrights['copyrights-menu']) ) {	?>
						<nav class="main-nav-container">
						<?php
							ob_start();
								wp_nav_menu( array(
									'theme_location'  => 'copyrights-menu',
									'menu_class' => 'main-menu copyrights-menu',
									'container' => false,
									'walker' => new Aasana_Walker_Nav_Topbar_Menu()
								) );
							echo ob_get_clean();
						?>
						</nav>
					<?php
				}
			echo "</div>";
		}
		$copyrights_menu = ob_get_clean();
		
		$social_links = '';
		$socials = $this->cws_get_meta_option('socials');
		$social_group = $this->cws_get_meta_option('social_group');

		$copyright_class_container = '';
		if(is_array($socials)){
			if ( in_array('bottom', $socials) ){
				$social_links = $this->cws_render_social_links();
				$copyright_class_container = ' a-center';
			}			
		}
		
		$is_wpml = '';
		if ( function_exists('wpml_init_language_switcher') ) {
			global $wpml_language_switcher;
			$slot = $wpml_language_switcher->get_slot( 'statics', 'footer' );
			$template = $slot->get_model();
			$is_wpml = $slot->is_enabled();
		}

		if ( !empty( $social_links ) || !empty($is_wpml) ) {
			ob_start();
			echo !empty( $social_links ) ? $social_links : "";
			if ( $is_wpml ) {
				do_action( 'wpml_footer_language_selector');		
			}
			$copy_footer = ob_get_clean();
		}

		ob_start();

		if ( !empty( $copyrights ) ) {
			echo "<div class='copyrights' >$copyrights</div>";
		}
		
		if(!empty($copyrights_menu)){
			echo sprintf("%s", $copyrights_menu);
		}
		
		if ( !empty( $social_links ) || ! empty( $show_wpml_footer ) ) {
			echo "<div class='copyrights_panel'>";
				echo "<div class='copyrights_panel_wrapper'>";
					echo isset($copy_footer) && !empty( $copy_footer ) ? $copy_footer : "";
				echo "</div>";
			echo "</div>";
		}
	
		$copyrights_content = ob_get_clean();
		if ( !empty( $copyrights_content ) ) {
			echo "<div class='copyrights_area".$footer_class."'>";
				echo "<div class='container'>";
					echo "<div class='copyrights_container'>";				
						echo sprintf("%s", $copyrights_content);
					echo "</div>";
				echo "</div>";
			echo "</div>";
		}

	}
	/* END THEME FOOTER */

	/******************** \TYPOGRAPHY ********************/

	public function cws_layout_class ($classes=array()) {
		$is_boxed_layout = $this->cws_get_meta_option('boxed_layout' ) == '1';

		if ($is_boxed_layout) {
			array_push( $classes, 'override_boxed_layout' );
		} else {
			array_push( $classes, 'wide' );
		}

		return $classes;
	}
	public function cws_widgets_init() {
		$sidebars = $this->cws_get_option('sidebars');
		if (!empty($sidebars) && function_exists('register_sidebars')) {
			foreach ($sidebars as $sb) {
				if ($sb && !empty($sb['title'])) {
					register_sidebar( array(
						'name' => $sb['title'],
						'id' => strtolower(preg_replace("/[^a-z0-9\-]+/i", "_", $sb['title'])),
						'before_widget' => '<div class="cws-widget"><span class="cws-widget-circle"><span class="cws-widget-innter-circle"></span></span>',
						'after_widget' => '</div>',
						'before_title' => '<div class="widget-title"><div class="inherit-wt">',
						'after_title' => '</div></div>',
					));
				}
			}
		}
	}

	public function cws_theme_reset_styles() {
		wp_enqueue_style('reset', AASANA_URI . '/css/reset.css');
	}

	public function cws_theme_enqueue_styles() {
		wp_register_style( 'empty', false );
		wp_enqueue_style( 'empty' );
		$styles =	array(
			'layout' => 'layout.css',
			'font-awesome' => 'font-awesome.css',
			'fancybox' => 'jquery.fancybox.css',
			'select2_init' => 'select2.css',
			'animate' => 'animate.css',
		);

		foreach($styles as $key=>$sc){
			wp_enqueue_style( $key, AASANA_URI . '/css/' . $sc);
		}

		$cwsfi = get_option('cwsfi');
		if (!empty($cwsfi) && isset($cwsfi['css'])) {
			wp_enqueue_style( 'cwsfi-css', $cwsfi['css']);
		} else {
			wp_enqueue_style( 'flaticon', AASANA_URI . '/fonts/flaticon/flaticon.css' );
		};

		wp_enqueue_style( 'cws-iconpack', AASANA_URI . '/fonts/cws-iconpack/flaticon.css' );

		$is_custom_color = $this->cws_get_option('is-custom-color');
		if ($is_custom_color != '1') {
			$style = $this->cws_get_option('stylesheet');
			if (!empty($style)) {
				wp_enqueue_style( 'style-color', AASANA_URI . '/css/' . $style . '.css' );
			}
		}
		wp_enqueue_style( 'cws_main', AASANA_URI . '/css/main.css' );
	}

	public function cws_enqueue_theme_stylesheet () {
		wp_enqueue_style( 'style', get_stylesheet_uri() );
	}

	public function cws_admin_init( $hook ) {
		$this->cws_read_options();
		wp_enqueue_style('admin-css', AASANA_URI . '/core/css/mb-post-styles.css' );
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('custom-admin', AASANA_URI . '/core/js/custom-admin.js', array( 'jquery' ) );

		$cwsfi = get_option('cwsfi');
		if (!empty($cwsfi) && isset($cwsfi['css'])) {
			wp_enqueue_style( 'cwsfi-css', $cwsfi['css']);
		}else{
			wp_enqueue_style( 'flaticon', AASANA_URI . '/fonts/flaticon/flaticon.css' );
		};

		if (('toplevel_page_Aasana' == $hook) || ('toplevel_page_AasanaChildTheme' == $hook)) {
			wp_enqueue_style( 'cws-redux-style' , AASANA_URI . '/core/css/cws-redux-style.css' );
		}
	}

	public function cws_register_fonts () {
		aasana_render_fonts_url ();
		$gf_url = esc_url( aasana_render_fonts_url () );
		wp_enqueue_style( '', $gf_url );
	}

	public function cws_after_setup_theme() {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support(' widgets ');
		add_theme_support( 'title-tag' );

		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
		add_theme_support( 'post-formats', self::$cws_theme_config['post-formats'] );
		$nav_menus = self::$cws_theme_config['nav-menus'];
		foreach ($nav_menus as $key => $value) {
			register_nav_menu( $key, $value );
		}

		add_theme_support( 'woocommerce' );
		add_theme_support( 'custom-background', array('default-color' => '616262') );


		$user = wp_get_current_user();
		$user_nav_adv_options = get_user_option( 'managenav-menuscolumnshidden', get_current_user_id() );
		if ( is_array($user_nav_adv_options) ) {
			$css_key = array_search('css-classes', $user_nav_adv_options);
			if (false !== $css_key) {
				unset($user_nav_adv_options[$css_key]);
				update_user_option($user->ID, 'managenav-menuscolumnshidden', $user_nav_adv_options,	true);
			}
		}

		add_editor_style();
	}

	// THEME COLOR HOOK

	private function cws_print_theme_color() {
		ob_start();
		do_action( 'theme_color_hook' );
		return ob_get_clean();
	}

	public function cws_theme_color_action() {
		$out = '';
		$is_page_color = $this->cws_get_meta_option('is_theme_color');		
		$main_one_color = !empty($is_page_color) ? $this->cws_get_meta_option('theme-main-one-color') : $this->cws_get_option('theme-main-one-color');
		$main_secondary_color = !empty($is_page_color) ? $this->cws_get_meta_option('theme-main-secondary-color') : $this->cws_get_option('theme-main-secondary-color');
		
		$second_color = !empty($is_page_color) ? $this->cws_get_meta_option('theme-second-color') : $this->cws_get_option('theme-second-color'); 

		if (isset($main_secondary_color)) {
			global $wp_filesystem;
			if( empty( $wp_filesystem ) ) {
				require_once( ABSPATH .'/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			$file = get_template_directory() . '/css/theme-color.css';
			if ( $wp_filesystem->exists($file) ) {
				$file = $wp_filesystem->get_contents( $file );
					$colors = array();
					$colors[0] = '|#theme-color-1#|';
					$colors[1] = '|#theme-color-2#|';
					$colors[2] = '|#theme-color-3#|';
					$replace = array();
					$replace[0] = $main_secondary_color;
					$replace[1] = $second_color;
					$replace[2] = $main_one_color;
					$new_css = preg_replace($colors, $replace, $file);
					$out .= $new_css;
			}
		}
		$result = preg_replace('/\s+/',' ', $out);
		$result .= "
		.item .date.new_style:hover .date-cont
		{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_secondary_color,0.5)).";
		}
		.cws_portfolio_nav li a.active .title_nav_portfolio,
		.cws_staff_nav li a.active .title_nav_staff,
		.tribe_events_nav li a.active .title_nav_events,
		.cws_classes_nav li a.active .title_nav_classes{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_secondary_color,0.3)).";
		}
		.cws_portfolio_nav li a.active .title_nav_portfolio:after,
		.cws_staff_nav li a.active .title_nav_staff:after,
		.tribe_events_nav li a.active .title_nav_events:after,
		.cws_classes_nav li a.active .title_nav_classes:after{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_secondary_color,0.3))." transparent transparent transparent;
		}		
		.cws_staff_post .post_social_links.cws_staff_post_social_links a,
		.post_social_links_classes a{
			border:3px solid ".esc_attr($this->cws_Hex2RGBA($main_secondary_color,0.3)).";
		}
		.cws_staff_post .post_social_links.cws_staff_post_social_links a,
		.post_social_links_classes a{
			color: ".esc_attr($this->cws_Hex2RGBA($main_secondary_color,0.3)).";
		}
		.cws_vc_shortcode_pricing_plan  .pricing_plan_price_wrapper{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.85)).";
		}		
		.header_cont .main-nav-container .sub-menu .menu-item:hover>a,
		.header_cont .menu .menu-item.current-menu-item>a,
		.header_cont .menu-item.current-menu-ancestor .current-menu-ancestor>a,
		.header_cont .menu-item.current-menu-parent .current-menu-item>a{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.10)).";
		}
		.tabs_classes li{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.65)).";
		}
		.wrap_title .price_single_classes
		{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.7)).";
		}		
		.staff_classes_single .staff_post_wrapper
		{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}		
		.single_classes_divider.separator-line,
		.post_post_info.posts_grid_post_info > hr
		{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}		
		.grid_row.single_related .carousel_nav span
		{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.3)).";
			color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.3)).";
		}
		input,
		select,
		textarea{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}
		.has-post-thumbnail .post_format_quote_media_wrapper .cws_vc_shortcode_module .quote_bg_c,.news .btn-read-more a{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.6)).";
		}		
		.news .btn-read-more a{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,1)).";
		}
		.single .news .has_thumbnail .quote-wrap .quote_bg_c,.quote_bg_c,.single_staff_wrapper .post_terms a{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.6)).";
		}
		.single_svg_divider svg
		{
			fill: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}
		.cws_staff_posts_grid .widget_header .carousel_nav span,
		.single_classes .carousel_nav span,
		.news.blog_post.posts_grid.posts_grid_carousel.navigation_owl .carousel_nav span,
		.single_portfolio .carousel_nav span{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.3)).";
			color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.3)).";
		}		
		.link_post_src:after,.link_bg:after,.tribe-events-list .tribe-events-event-cost span{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.7)).";
		}		
		.news .item .post_info .info,
		.sl-icon:before,
		.info span.post_author a,
		.comments_link i,
		.comments_link,
		.sl-count,
		.sl-button,
		.like,
		.post_post_info > .post_meta .social_share a,
		.tribe-events-schedule h2,
		.single-tribe_events .tribe-events-schedule .recurringinfo, .single-tribe_events .tribe-events-schedule .tribe-events-cost, .single-tribe_events .tribe-events-schedule .tribe-events-divider,
		.event-is-recurring,
		.tribe-related-event-info,
		.news.single .item > .post_meta .social_share a,
		.news .item .post_info .info:before{
			color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.6)).";
		}		
		.news div.post_category a, 
		.news div.post_tags a{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.6)).";
		}			
		.comments-area .comment_list .comment-reply-link{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}		
		.comment_list .comment{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.3)).";
		}
		.comment_info_header .button-content.reply:after{
			border-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}
		.comments-area .comment_list .comment-reply-link:hover{
			color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}
		.cws_portfolio_content_wrap, .video .cover_img,.hoverdir .cws_portfolio_content_wrap,
		.news .media_part:hover .hover-effect,
		.media_part.link_post .hover-effect,
		.cws_staff_post.posts_grid_post:hover .add_btn .cws_staff_photo:after,
		.blog_post .post_media .hover-effect,.has-post-thumbnail .post_format_quote_media_wrapper .cws_vc_shortcode_module:hover .quote_bg_c{
			background-color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.8)).";
		}
		.select2-container .select2-selection--single{
			border:1px solid ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}		
		.wrap_desc_info *:before,.single_classes .post_time_meta:before,.single_classes .post_destinations_meta:before{
			color:".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}	
		aside .cws-widget:before,
		aside .cws-widget:after,aside .cws-widget + .cws-widget:after{
			background:".esc_attr($this->cws_Hex2RGBA($main_one_color,0.25)).";
		}		
		table.shop_table.woocommerce-checkout-review-order-table>tfoot{
			border-top-color:".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}		
		.cws-widget-circle,
		.cws-widget-circle:before,
		.cws-widget-circle:after,
		.cws-widget-circle .cws-widget-innter-circle{
			border-color:".esc_attr($this->cws_Hex2RGBA($main_one_color,0.25)).";
		}		
		.woocommerce .cart-collaterals .cart_totals table.shop_table tr th,
		.woocommerce .cart-collaterals .cart_totals h2,
		.woocommerce .cart-collaterals .cart_totals table.shop_table tr td{
			border-bottom-color:".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}
		.st55,.st54,.tribe-events-list svg{
			fill:".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}		
		.flxmap-container,
		#wpgmza_map{
			outline: 2px solid ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}			
		.tribe-events-list .type-tribe_events .cws-tribe-events-list:before,
		.tribe-events-list .type-tribe_events .cws-tribe-events-list:after{
			background: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.2)).";
		}		
		#tribe-events-content .tribe-event-duration:before, #tribe-events-footer .tribe-events-sub-nav li a, #tribe-events-header .tribe-events-sub-nav li a, #tribe-events-footer .tribe-events-sub-nav .tribe-events-nav-next a, #tribe-events-header .tribe-events-sub-nav .tribe-events-nav-next a{
			color: ".esc_attr($this->cws_Hex2RGBA($main_one_color,0.5)).";
		}					
		";
		echo sprintf("%s", $result);
	}

	public function cws_theme_rgba_color () {
		$out = '';
		$font_array = $this->cws_get_option('theme-main-one-color');
		$rgb_color = $this->cws_Hex2RGB( $font_array );
		$rgb_color = esc_attr($rgb_color);

		$lighter_rgba_color = $rgb_color .  ",0.2";

		$out = ".pagination .page_links .prev.page-numbers{color:rgba($lighter_rgba_color);}";
		$out .= ".pagination .page_links .next.page-numbers{color:rgba($lighter_rgba_color);}";
		echo preg_replace('/\s+/',' ', $out);
	}

	public function cws_custom_page_title_styles_action () {
		$header_box = $this->cws_get_meta_option( "header_box" );
		$box_style = '';

		$header_box_font_color = $header_box['font_color'];

		if (is_single() && $this->cws_get_option('show_on_posts') == '1' ){
			$title_area_font_color = $header_box_font_color;
		} else if (is_archive() && $this->cws_get_option('show_on_archives') == '1' ){
			$title_area_font_color = $header_box_font_color;
		} else {
			$title_area_font_color = $header_box_font_color;
		}

		$is_customize_title_area = $this->cws_get_meta_option( "customize_title_area" ) == '1';

		$box_style .= isset($header_box) ? $this->cws_print_border_box($header_box) : "";

		ob_start();
		if ($is_customize_title_area && !empty($title_area_font_color)) {
		?>
			.bg_page_header .title h1,
			.page_title .bread-crumbs > a,
			.page_title .bread-crumbs > .delimiter,
			.page_title .bread-crumbs > .current
			{
				color: <?php echo esc_attr($title_area_font_color) ?>;
			}
		<?php
			echo ".bg_page_header{ $box_style }";
		}
		echo ob_get_clean();
	}

	public function cws_custom_header_styles_action () {
		$header_order = $this->cws_get_meta_option('header_zone');
		if ( isset( $header_order ) ) {
			$count = count($header_order);
			foreach ($header_order as $key => $value){
				if ( !empty($value) && !in_array( $value['val'], array('drop_zone_start', 'drop_zone_end', 'before_header', 'after_header') ) ){
					$z_index = $count-$key;
					echo esc_attr(".header_wrapper_container .{$value['val']}{z-index:$z_index;} ");
				}
			}
		}
		$spacings_page = $this->cws_get_meta_option('spacings_page');
		if(isset($spacings_page)){
			echo esc_attr("#main .page_content{padding-top: ".(int) $spacings_page['top']."px;padding-bottom: ".(int) $spacings_page['bottom']."px}");
		}
		
	}


	public function cws_custom_top_bar_styles_action () {
		$topbar_bg_color = $this->cws_Hex2RGB($this->cws_get_meta_option('top_bar_bg_color' ));
		$top_bar_bg_opacity = ($this->cws_get_meta_option('top_bar_bg_opacity' )) / 100;
		$top_bar_bg_style = 'rgba('.$topbar_bg_color.','.$top_bar_bg_opacity.')';
		$top_bar_font_color = $this->cws_get_meta_option('top_bar_font_color' );
		$top_bar_spacings = $this->cws_get_meta_option('top_bar_spacings' );
		$top_bar_spacings_style = '';

		if ( isset($top_bar_spacings) ){
			foreach ( $top_bar_spacings as $key => $value ){
				if ( !empty( $value ) || $value == '0' ){
					$top_bar_spacings_style .= "padding-".$key . ": " . $value . "px;";
				}
			}
		}

		$top_bar_border = $this->cws_get_meta_option('top_bar_border' );
		$top_bar_border_type = $this->cws_get_meta_option('top_bar_border_type' );
		$top_bar_border_color = $this->cws_get_meta_option('top_bar_border_color' );

		ob_start();
		?>
			#site_top_panel,
			#site_top_panel form.search-form
			{
				background-color: <?php echo esc_attr($top_bar_bg_style) ?>;
			}

			#site_top_panel .search_icon:after,
			#site_top_panel .topbar_left_icons .side_panel_icon_wrapper:after,
			#site_top_panel .topbar_right_icons:after,
			#site_top_panel .topbar-menu-left .topbar_left_icons:after,
			#site_top_panel .topbar-menu-right .topbar_right_icons:after
			{
				background-color: <?php echo esc_attr($top_bar_font_color) ?>;
			}

			#site_top_panel > a,
			.main-nav-container .topbar-menu > .menu-item > .button_open,
			#site_top_panel,
			#site_top_panel #top_social_links_wrapper .cws_social_links a,
			#top_panel_text,
			#top_panel_text a,
			#site_top_panel .side_panel_icon,
			#site_top_panel .main-nav-container > .main-menu > .menu-item > a,
			#site_top_panel .main-nav-container > .main-menu > .menu-item > span,
			#site_top_panel .lang_bar #lang_sel > ul > li > a,
			#site_top_panel form.search-form .search-field
			{
				color: <?php echo esc_attr($top_bar_font_color) ?>;
			}

			#site_top_panel .topbar-menu-left .topbar_left_icons,
			#site_top_panel .topbar-menu-right .topbar_right_icons,
			#site_top_panel .topbar_right_icons + .search_icon{
				border-color: <?php echo esc_attr($top_bar_font_color) ?>;
			}

		<?php

		$customizer_header = $this->cws_get_meta_option('customizer_header' );
		if ($customizer_header == '1'){
			$top_bar_font_color = $this->cws_get_meta_option('override_topbar_color' );

		?>

			.header_zone #site_top_panel > a,
			.header_zone #site_top_panel,
			.header_zone #site_top_panel #top_social_links_wrapper .cws_social_links a,
			.header_zone #top_panel_text,
			.header_zone #top_panel_text a,
			.header_zone #site_top_panel .side_panel_icon,
			.header_zone #site_top_panel .main-nav-container .main-menu > .menu-item > a,
			.header_zone #site_top_panel form.search-form .search-field,
			.header_zone #site_top_panel form.search-form .search-field::-webkit-input-placeholder,
			.header_zone #site_top_panel .lang_bar #lang_sel > ul > li > a
			{
				color: <?php echo esc_attr($top_bar_font_color) ?>;
			}

			.header_zone #site_top_panel .topbar_right_icons:after,
			#site_top_panel .container > #top_panel_text + #top_panel_links:after,
			#site_top_panel .topbar_right_icons > * + *:after,
			.header_zone #site_top_panel .topbar-menu-left .topbar_left_icons:after,
			.header_zone #site_top_panel .topbar-menu-right .topbar_right_icons:after,

			.header_zone #site_top_panel .search_icon:after,
			.header_zone #site_top_panel .topbar_left_icons .side_panel_icon_wrapper:after,
			.header_zone #site_top_panel .topbar_right_icons:after,
			.header_zone #site_top_panel .topbar-menu-left .topbar_left_icons:after,
			.header_zone #site_top_panel .topbar-menu-right .topbar_right_icons:after
			{
				background-color: <?php echo esc_attr($top_bar_font_color) ?>;
			}

		<?php
		}

		if ( $top_bar_border != 'none'){
			if ($top_bar_border == 'top'){
				?>
					#site_top_panel
					{
						border-top: 1px <?php echo esc_attr($top_bar_border_type).' '.esc_attr($top_bar_border_color);?>;
					}

				<?php
			} elseif ($top_bar_border == 'bottom') {
				?>
					#site_top_panel
					{
						border-bottom: 1px <?php echo esc_attr($top_bar_border_type).' '.esc_attr($top_bar_border_color);?>;
					}

				<?php
			} elseif ($top_bar_border == 'both') {
				?>
					#site_top_panel
					{
						border-top: 1px <?php echo esc_attr($top_bar_border_type).' '.esc_attr($top_bar_border_color);?>;
						border-bottom: 1px <?php echo esc_attr($top_bar_border_type).' '.esc_attr($top_bar_border_color);?>;
					}

				<?php
			}
		}

		if ( !empty($top_bar_spacings_style)){
			?>
				#site_top_panel{
					<?php echo esc_attr($top_bar_spacings_style); ?>
				}
			<?php
		}

		echo ob_get_clean();
	}


	public function cws_custom_footer_styles_action() {
		global $aasana_theme_funcs;
		$footer = $this->cws_get_meta_option('footer');
		$footer_font_color = $footer['footer_font_color'];

		$footer_copyrights_font_color = $footer['footer_copyrights_font_color'];

		$footer_bg_color = $footer['footer_bg_color'];
		
		$footer_copyrights_color = $footer['footer_copyrights_bg_color'];

		ob_start();
		?>	
			.copyrights_area{
				background: <?php echo esc_attr($footer_copyrights_color) ?>;
			}
			.page_footer,
			.page_footer .footer_container .cws-widget
			.page_footer .footer_container .cws-widget .cws_social_links a .cws_fa {
				color: <?php echo esc_attr($footer_font_color) ?>;
			}

			.page_footer .footer_container .owl-pagination .owl-page {
					-webkit-box-shadow: 0px 0px 0px 1px <?php echo esc_attr($footer_font_color) ?>;
					-moz-box-shadow: 0px 0px 0px 1px <?php echo esc_attr($footer_font_color) ?>;
					box-shadow: 0px 0px 0px 1px <?php echo esc_attr($footer_font_color) ?>;
			}
			.page_footer{
				background-color: <?php echo esc_attr($footer_bg_color) ?>;
			}

			.page_footer .footer_container .owl-pagination .owl-page.active:before {
				background-color: <?php echo esc_attr($footer_font_color) ?>;
			}

			.copyrights_area {
				color: <?php echo esc_attr($footer_copyrights_font_color) ?>;
			}

			.copyrights_panel_wrapper .wpml_language_switch.lang_bar:after{
				background-color: <?php echo esc_attr($footer_copyrights_font_color) ?>;
			}

		<?php
		echo ob_get_clean();
	}

	public function cws_custom_sticky_menu_styles_action () {
		$stick_menu_style = sprintf('rgba(%s,%s)', $this->cws_Hex2RGB( $this->cws_get_meta_option( "stick_bg_color" ) ), $this->cws_get_meta_option( "stick_bg_opacity" )/100);

		$stick_menu_color = $this->cws_get_meta_option( "stick_font_color" );

		$stick_border = $this->cws_get_meta_option( "stick_border" );
		$stick_border_type = $this->cws_get_meta_option( "stick_border_type" );
		$stick_border_color = $this->cws_get_meta_option( "stick_border_color" );

		$stick_menu_border = '';

		$attrs = ': 1px ' . $stick_border_type . ' ' . $stick_border_color . ' !important;';
		switch ($stick_border) {
			case 'top':
			case 'bottom':
				$stick_menu_border = 'border-' . $stick_border . $attrs;
				break;
			case 'both':
				$stick_menu_border = 'border-top' . $attrs;
				$stick_menu_border = 'border-bottom' . $attrs;
				break;
		}
		ob_start();

		if ( $this->cws_get_meta_option( "menu-stick" ) == '1' ){
		?>
.sticky_header .header_cont .header_container .menu_box {
	background-color: <?php echo esc_attr($stick_menu_style) ?> !important;
	<?php echo esc_attr($stick_menu_border) ?>
}

.sticky_header .header_cont .header_container .menu_left_icon_bar,
.sticky_header .header_cont .header_container .menu_right_icon_bar {
	background-color: transparent;
}

.sticky_header .header_cont .header_container .menu_left_icon_bar .woo_mini_cart>ul,
.sticky_header .header_cont .header_container .menu_right_icon_bar .woo_mini_cart>ul
{
	overflow: auto;
	height: 400px;
}

.sticky_header .main-nav-container .main-menu > .menu-item>a,
.sticky_header .header_container .menu_left_icon_bar a,
.sticky_header .header_container .menu_right_icon_bar a,
.sticky_header .header_container .side_panel_icon,
.sticky_header .header_container .mini-cart a,
.sticky_header .header_container .search_menu {
	color: <?php echo esc_attr($stick_menu_color) ?>;
}
.sticky_header .header_container .menu_left_icons>*:after,
.sticky_header .main-nav-container .main-menu > .menu-item.wpml-ls-menu-item > a:before,
.sticky_header .header_container .menu_right_icons>*:after{
	background: <?php echo esc_attr($stick_menu_color) ?>;
}
<?php
		}
		echo ob_get_clean();
	}

	public function cws_custom_boxed_layout_styles_action() {
		$enable_boxed = $this->cws_get_meta_option('enable_boxed');
		if ($enable_boxed == '1') {
			$bb = $this->cws_get_meta_option('boxed_background');
			if ( !empty($bb['image']['src']) ){
				echo ('body.custom-background.override_boxed_layout {');
				echo sprintf("%s", $this->cws_print_css_keys($bb, 'background-'));
				echo 'background-image:url('.$bb['image']['src'].');';
				echo 'background-color:#ffffff;';
				echo '}';
			}
		}
	}

	public function cws_print_overlay($o){
		$bg_styles = '';
		if(!empty($o)){
			extract($o);
		}
		
		switch ($type) {
			case 'gradient':
				$bg_styles = $this->cws_render_gradient_rules($gradient);
				$bg_styles .= "opacity:{$opacity};";
				break;
			case 'color':
				$bg_styles = $this->cws_print_rgba('background-color', $overlay_color, $opacity);
				break;
		}
		return $bg_styles;
	}

	public function cws_print_rgba($prefix, $color, $op = 100){
		return sprintf('%s:rgba(%s,%s);', $prefix, $this->cws_Hex2RGB($color), (int)$op/100);
	}

	private function cws_print_theme_gradient () {
		ob_start();
		do_action( 'theme_gradient_hook' );
		return ob_get_clean();
	}

	public function cws_theme_gradient_action () {
		$out = '';
		$use_gradients = $this->cws_get_option('use_gradients');
		if ( $use_gradients ) {
			$gradient_settings = $this->cws_get_option( 'gradient_settings' );
			require_once( get_template_directory() . "/css/gradient_selectors.php" );
			if ( function_exists( "get_gradient_selectors" ) ) {
				$gradient_selectors = get_gradient_selectors();
				$out .= $this->cws_render_gradient_rules( array(
					'settings' => $gradient_settings,
					'selectors' => $gradient_selectors,
					'use_extra_rules' => true
				));
			}
		}
		echo preg_replace('/\s+/',' ', $out);
	}

	public function cws_gradients_body_class ( $classes ) {
		$use_gradients = $this->cws_get_option('use_gradients');
		if ( $use_gradients ) {
			$classes[] = "cws_gradients";
		}
		return $classes;
	}

	public function cws_process_colors() {
		$out = $this->cws_print_theme_color();
		return preg_replace('/\s+/',' ', $out);
	}

	public function cws_Hex2RGB($hex) {
		$hex = str_replace('#', '', $hex);
		$color = '';

		if(strlen($hex) == 3) {
			$color = hexdec(mb_substr($hex, 0, 1)) . ',';
			$color .= hexdec(mb_substr($hex, 1, 1)) . ',';
			$color .= hexdec(mb_substr($hex, 2, 1));
		}
		else if(strlen($hex) == 6) {
			$color = hexdec(mb_substr($hex, 0, 2)) . ',';
			$color .= hexdec(mb_substr($hex, 2, 2)) . ',';
			$color .= hexdec(mb_substr($hex, 4, 2));
		}
		return $color;
	}

	// \  COLOR HOOK

	public function cws_theme_header_process_fonts (){
		return $this->cws_process_fonts();
	}

	public function cws_theme_header_process_colors (){
		return $this->cws_process_colors();
	}

	public function cws_theme_header_process_blur (){
		return $this->cws_process_blur();
	}	

	public function cws_theme_loader (){
		return $this->cws_loader();
	}	
	public function cws_theme_mobile (){
		return $this->cws_mobile();
	}
	/* END THE HEADER META */
	public function cws_pagination ( $paged=1, $max_paged=1, $style = 'paged', $pagination_text = 'Load More') {
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
		<div class='pagination <?php if($style == 'load_more'){ echo ("pagination_load_more");}?> separated'>
			<div class='page_links'>
			<?php
			$pagination_args = array( 'base' => $pagenum_link,
				'format' => $format,
				'current' => $paged,
				'total' => $max_paged,
				"prev_text" => "<i class='fa fa-angle-left'></i>",
				"next_text" => ($style == 'paged' ? "<i class='fa fa-angle-right'></i>" : $pagination_text),
				"link_before" => '',
				"link_after" => '',
				"before" => '',
				"after" => '',
				"mid_size" => 2,
			);
			echo paginate_links($pagination_args);
			?>
			</div>
		</div>
		<?php
	}

	/* Comments */
	public function cws_comment_nav() {
		// Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
		?>
		<div class="comments_nav carousel_nav_panel clearfix">
			<?php
				if ( $prev_link = get_previous_comments_link( "<span class='prev'></span><span>" . esc_html__( 'Older Comments', 'aasana' ) . "</span>" ) ) {
					printf( '<div class="prev_section">%s</div>', esc_html($prev_link) );
				}

				if ( $next_link = get_next_comments_link( "<span>" . esc_html__( 'Newer Comments', 'aasana' ) . "</span><span class='next'></span>" ) ) {
					printf( '<div class="next_section">%s</div>', esc_html($next_link) );
				}
			?>
		</div><!-- .comment-navigation -->
		<?php
		}
	}

	public function cws_comment_post( $incoming_comment ) {
		$comment = strip_tags($incoming_comment['comment_content']);
		$comment = esc_html($comment);
		$incoming_comment['comment_content'] = $comment;
		return $incoming_comment;
	}
	/* /Comments */

	public 	function cws_blog_slider_output ( $query = false ) {
		$blogtype = $this->cws_get_meta_option( 'blogtype' );

		$custom_layout_arr = array(
			'this_shortcode' => isset( $query->query_vars['this_shortcode'] ) ? $query->query_vars['this_shortcode'] : false,

			'custom_layout' => isset( $query->query_vars['custom_layout'] ) ? $query->query_vars['custom_layout'] : 0,
			'button_name' => ! empty( $query->query_vars['button_name'] ) ? $query->query_vars['button_name'] : '',
			'theme' => ! empty( $query->query_vars['theme'] ) ? $query->query_vars['theme'] : '',
			'alt_style' => isset( $query->query_vars['alt_style'] ) ? $query->query_vars['alt_style'] : '1',

			'post_text_length' => ! empty( $query->query_vars['post_text_length'] ) ? $query->query_vars['post_text_length'] : '',
			'hide' => isset( $query->query_vars['hide'] ) ? $query->query_vars['hide'] : array(),
			'column_count' => isset( $query->query_vars['column_count'] ) ? intval( $query->query_vars['column_count'] ) : 3,
			'blogtype' => isset( $blogtype ) ? $blogtype : '',
			'full_width' => isset( $query->query_vars['full_width'] ) ? $query->query_vars['full_width'] : 0,
			'square_crop' => isset( $query->query_vars['square_crop'] ) ? $query->query_vars['square_crop'] : 0,
		);

		global $wp_query;
		$query = $query ? $query : $wp_query;

		if ($query->have_posts()):
			ob_start();
			while($query->have_posts()):
				$query->the_post();
			?>
			<article <?php post_class(array( 'item', 'col-'.$blogtype )); ?>>
			<?php
			$pid = get_the_id();
			$is_single = is_single( $pid );
			$title = esc_html( get_the_title() );
			$permalink = esc_url( get_the_permalink() );
			$show_author = $this->cws_get_option( "blog_author" );
			$date = esc_html( get_the_time( get_option( 'date_format' ) ) );
			$first_word_boundary = strpos( $date, ' ' );

			ob_start();

				//Post meta
				if (!in_array('categories', $custom_layout_arr['hide']) || !in_array('tags', $custom_layout_arr['hide']) ) {
					echo "<hr>";
					echo "<div class='post_meta'>";
					if (!in_array('categories', $custom_layout_arr['hide'])) {
						if ( has_category() ) {
							echo "<div class='post_category'>";
								$category_part = the_category (', ');
								echo sprintf("%s", $category_part);
							echo '</div>';
						}
						if (!in_array('tags', $custom_layout_arr['hide']) && has_tag()) {
							echo " | ";
						}
					}

					if (!in_array('tags', $custom_layout_arr['hide'])) {
						if ( has_tag() ) {
							echo "<div class='post_tags'>";
								$tags_part = the_tags ("", " ", "" );
								echo sprintf("%s", $tags_part);
							echo '</div>';
						}
					}
					echo '</div>';
				}

				//Post title
				if (!in_array('title', $custom_layout_arr['hide'])) {
					$title_part = ( !$is_single ? "<h3><a href='$permalink'>" : '' ) . $title . ( !$is_single ? "</a></h3>" : '' );
					echo !empty( $title ) ?	$this::THEME_BEFORE_CE_TITLE . "<div>" . $title_part . '</div>' . $this::THEME_AFTER_CE_TITLE : '';
				}

				//Post info
				if (!in_array('meta', $custom_layout_arr['hide'])) {
					$author = '';
					$author .= $show_author ? esc_html(get_the_author()) : '';
					$special_pf = $this->cws_is_special_post_format();
					$comments_n = get_comments_number();

					ob_start();
					the_author_posts_link();
					$author_link = ob_get_clean();

					echo '<div class="post_info">';
					$date = esc_html( get_the_time( get_option( 'date_format' ) ) );
					echo sprintf("%s", $date);
						if ( !empty($author) || $special_pf ) {
						echo "<span class='blog-meta-divider'> / </span>";
							echo "<div class='info'>";
									echo !empty($author) ? (esc_html_e('by ', 'aasana'))."<span class='post_author'>$author_link</span>" : '';
							echo '</div>';
						}

						if ( (int) $comments_n > 0 ) {
							echo "<span class='blog-meta-divider'> / </span>";
							$permalink .= "#comments";
							echo "<div class='comments_link'><a href='$permalink'>$comments_n comments</a></div>";
							$comments_part = "<a href='$permalink'>$comments_n <span> comments</span></a>";
						}
					echo '</div>';
				}

				//Post content
				if (!in_array('content', $custom_layout_arr['hide'])) {
					ob_start();
						$custom_layout_arr['alt_style'] = '1';
						$this->cws_post_content_output($custom_layout_arr);
						$custom_layout_arr['alt_style'] = '0';
					$content_part = ob_get_clean();
				}

			$post_output = ob_get_clean();
			ob_start();

			$custom_layout = intval( $custom_layout_arr['custom_layout'] );
			$use_blur = $this->cws_get_option( 'use_blur' ) == 1 ? true : false;
			$post_url = esc_url(get_the_permalink());
			$single = is_single();
			$post_format = get_post_format( );
			$eq_thumb_height = in_array( $post_format, array( 'gallery' ) );
			$media_meta = cws_core_cwsfw_get_post_meta( get_the_ID(), 'cws_mb_post' );
			$media_meta = isset( $media_meta[0] ) ? $media_meta[0] : array();

			$thumbnail = has_post_thumbnail( ) ? wp_get_attachment_image_src( get_post_thumbnail_id( ),'full' ) : '';
			$thumbnail = ! empty( $thumbnail ) ? $thumbnail[0] : '';
			$thumbnail_dims = $this->cws_get_post_thumbnail_dims( $eq_thumb_height );

			$real_thumbnail_dims = array();
			if ( isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
			if ( isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];
			$thumbnail_dims = $this->cws_get_post_thumbnail_dims( $eq_thumb_height, $real_thumbnail_dims );

			$crop_thumb = isset( $thumbnail_dims['width'] ) && $thumbnail_dims['width'] > 0;
			$thumb_media = false;

			$image_data = wp_get_attachment_metadata( get_post_thumbnail_id( get_the_ID() ) );
			if ( ! empty( $thumbnail ) ) {

				if ($custom_layout_arr['square_crop'] == '1'){
					//Make square dimensions
					if ($image_data['width'] < $image_data['height']){
						$img_data['width'] = $image_data['width'];
						$img_data['height'] = $image_data['width'];
					} elseif ($image_data['height'] < $image_data['width']) {
						$img_data['width'] = $image_data['height'];
						$img_data['height'] = $image_data['height'];
					}
				} else {
					$img_data['width'] = $image_data['width'];
					$img_data['height'] = null;
				}

				$img_data['crop'] = array(
					$this->cws_get_option( "crop_x" ),
					$this->cws_get_option( "crop_y" )
				);
			}

			$only_link = ($post_format == 'link' && empty( $thumbnail ) ) ? ' only_link' : '';
			$quote = ('quote' === $post_format && isset( $media_meta['quote'] )) ? $media_meta['quote'] : '';
			$quote_post = ( ! empty( $quote )) ? ' quoute_post' : '';
			$link_post = ( 'link' === $post_format ) ? ' link_post' : '';
			$video_post = ('video' === $post_format)  ? ' video_post' : '';
			$audio_post = ( 'audio' === $post_format && isset( $media_meta['audio'] ) ) ? ' audio_post' : '';
			$audio_post .= isset( $media_meta['audio'] ) ? ( is_int( strpos( $media_meta['audio'], 'https://soundcloud' ) ) ? ' soundcloud' : '') : '';
			
			$gallery_post = ('gallery' === $post_format && isset( $media_meta['gallery'] ) && !empty($media_meta['gallery'])) ? ' gallery_post'.($media_meta['gallery_type'] == 'grid' ? ' gallery_grid' : '') : '';
			$some_media = false;
			ob_start();
			?>
				<div class="media_part<?php	echo esc_attr($only_link); echo esc_attr($link_post); echo esc_attr($quote_post); echo esc_attr($video_post); echo esc_attr($audio_post); echo esc_attr($gallery_post); ?>">
					<?php
						switch ($post_format) {
							case 'gallery':
								$gallery = isset( $media_meta['gallery'] ) ? $media_meta['gallery'] : '';
								if ( !empty( $gallery ) ) {
									$match = preg_match_all("/\d+/",$gallery,$images);
									if ($match){

										if ($media_meta['gallery_type'] == 'grid' && $single) {
											$columns = $media_meta['grid_cols'];
											$grid_class = "grid grid-$columns isotope";
											wp_enqueue_script ('isotope');
										}
											$images = $images[0];
											$image_arr = array();

											foreach ( $images as $image ) {
												$image_src = wp_get_attachment_image_src($image,'full');
												if (isset($image_src[0]) && !empty($image_src[0])){
													$image_dims = array(
														'url' => $image_src[0],
														'width' => $image_src[1],
														'height' => $image_src[2]
													);
													array_push( $image_arr, $image_dims );
												}
											}

											$thumb_media = $some_media = count( $image_arr ) > 0 ? true : false;

											$carousel = count($image_arr) > 1 ? true : false;
											$gallery_id = uniqid( 'cws-gallery-' );
											if ($carousel && $media_meta['gallery_type'] == 'slider' || !$single) {
												wp_enqueue_script ('owl_carousel');
												wp_enqueue_script ('isotope');
											}
											if ($media_meta['gallery_type'] == 'slider' || !$single) {
												if($carousel){
													echo  "<a class='carousel_nav prev'><span><i class='fa fa-long-arrow-left'></i></span></a>
																		<a class='carousel_nav next'><span><i class='fa fa-long-arrow-right'></i></span></a>
																		<div class='gallery_post_carousel'>";
												}
											} elseif ($media_meta['gallery_type'] == 'grid' && $single) {
												echo "<div class='".$grid_class."'>";
											}

											foreach ( $image_arr as $image_arr_item ) {
												if ($media_meta['gallery_type'] == 'grid' && $single) {
												?>
													<article <?php post_class(array( 'item' )); ?>>
												<?php
												}
												//Make square dimensions
												if ($image_arr_item['width'] < $image_arr_item['height']){
													$gallery_data['width'] = $image_data['width'];
													$gallery_data['height'] = $image_data['width'];
												} elseif ($image_arr_item['height'] < $image_arr_item['width']) {
													$gallery_data['width'] = $image_data['height'];
													$gallery_data['height'] = $image_data['height'];
												}

												$gallery_data['crop'] = true;

												$img_obj = cws_thumb( $image_arr_item['url'], $gallery_data , false );
												$img_url = esc_url($img_obj[0]);

												$retina_img_exists = $img_obj[3]['retina_thumb_exists'];
												$retina_img_url = esc_attr($img_obj[3]['retina_thumb_url']);
												?>
												<div class='pic<?php if($use_blur && (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1'))) echo(' blured');  ?>'>
													<?php
													if ( $retina_img_exists && $custom_layout_arr['full_width'] != '1') {
														echo "<img src='".esc_url($img_url)."' data-at2x='$retina_img_url' alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
														
														if($use_blur  && ((($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && ( isset($media_meta['enable_lightbox']) && ($media_meta['enable_lightbox'] == '1'))))){

														}
														echo  "<img src='$img_url' data-at2x='$retina_img_url' class='blured-img' alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
													}
													else{
														echo "<img src='".esc_url($img_url)."' data-no-retina alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
														if($use_blur  && ((($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && ( isset($media_meta['enable_lightbox']) && ($media_meta['enable_lightbox'] == '1'))))){
															echo  "<img src='".esc_url($img_url)."' data-no-retina class='blured-img' alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
														}
													}
													if ( (($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0)) || (isset($media_meta['enable_lightbox']) && $media_meta['enable_lightbox'] == '1')	)  {

														echo "<div class='gradient-wrapper'>";
															echo "<div class='bottom-hover-effect'>";
																echo sprintf("%s", $post_output);
															echo '</div>';
														echo '</div>';
													}
													?>
												</div>
												<?php
												if ($media_meta['gallery_type'] == 'grid'&& $single) {
													echo "</article>";
												}
											}
											if ($media_meta['gallery_type'] == 'grid'&& $single) {
												echo '</div>';
											}
											echo  (($carousel  && $media_meta['gallery_type'] == 'slider') || !$single) ? '</div>' : '';
									}
									$some_media = true;
								}
								break;
						}
			if ( !$some_media && !empty( $thumbnail ) ) {

				$thumb_obj = cws_thumb( $thumbnail, $img_data, false );
				$thumb_url = esc_url($thumb_obj[0]);

				if ($custom_layout_arr['full_width'] == '1'){
					$thumb_url = $thumbnail;
				}

				$retina_thumb_url = esc_attr($thumb_obj[3]);
				echo "<div class='pic ".($custom_layout_arr['alt_style'] == 1 ? 'colored_box_style' : '').( $use_blur && (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1')) ? ' blured' : '' )."'>";

				if ( isset($thumb_obj[3]) ) {
					echo "<img src='".esc_url($thumb_url)."' data-at2x='$retina_thumb_url' alt />";
					if($use_blur  && ((($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && ( isset($media_meta['enable_lightbox']) && ($media_meta['enable_lightbox'] == '1'))))){
						echo "<img src='".esc_url($thumb_url)."' data-at2x='$retina_thumb_url' class='blured-img' alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
					}
					
				}
				else{
					echo "<img src='".esc_url($thumb_url)."' data-no-retina alt />";
					if($use_blur  && ((($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && ( isset($media_meta['enable_lightbox']) && ($media_meta['enable_lightbox'] == '1'))))){
						echo "<img src='".esc_url($thumb_url)."' data-no-retina class='blured-img' alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
					}
					
				}

				if (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1'))  {
					echo "<a href='".$permalink."'>";
						echo "<div class='gradient-wrapper'>";
							echo "<div class='bottom-hover-effect'>";
								echo sprintf("%s", $post_output);
							echo '</div>';
						echo '</div>';
					echo "</a>";
				}

				echo '</div>';
				$thumb_media = true;
				$some_media = true;

			}
					?>
				</div>
			<?php
			$some_media ? ob_end_flush() : ob_end_clean();

			$media_content = ob_get_clean();

			$section_class = "post_info_part";
			$section_class .= (!empty($custom_layout_arr['blogtype']) ? (in_array($custom_layout_arr['blogtype'], array("1","large","2", "3", "4")) ? ' clearfix' : '') : '');
			$section_class .= empty( $media_content ) ? " full_width" : '';
			$header_class = "post_info_header";
			$post_format = get_post_format();

			$header_class .= ( in_array( $post_format, array( 'quote', 'audio' ) ) || ( $post_format == 'link' && !has_post_thumbnail() ) ) ? " rounded" : '';

			$post_meta = cws_core_cwsfw_get_post_meta( get_the_ID(), 'cws_mb_post' );
			$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();

			if (!empty($media_content)) {
				?><div class="<?php echo esc_attr($section_class); ?>"><?php echo sprintf("%s", $media_content);?></div><?php
			}

		echo "</article>";
			endwhile;
			echo ob_get_clean();
		endif;
	}

	/* SIDE PANEL */
	public function cws_side_panel() {
		$is_switcher = $this->cws_get_meta_option( 'show_side_panel' );
		if ($is_switcher) {
			$side_panel = $this->cws_get_meta_option( 'side_panel' );
			$theme = $side_panel['theme'];
			switch ($theme) {
				case 'light':
					$bg_color = '255,255,255';
					break;
				case 'dark':
					$bg_color = '31,31,31';
					break;
			}

			$bg_image = $side_panel['bg_' . $theme];
			$logo = $side_panel['logo_' . $theme];
			$bg_src = isset($bg_image) ? $bg_image['src'] : '';
			$bg_size = isset($side_panel['bg_size']) ? $side_panel['bg_size'] : "";
			$bg_opacity = (int)$side_panel['bg_opacity'] / 100;
			$bg_pos = isset($side_panel['bg_position']) ? $this->cws_print9positions($side_panel['bg_position']) : "";

			$logo_src = '';
			if ( !empty( $logo['src'] ) ) {
				$logo_hw = $side_panel['logo_dimensions'];

				$bfi_args = array();
				if (is_array($logo_hw)) {
					foreach ($logo_hw as $key => $value) {
						if ( !empty($value) ){
							$bfi_args[$key] = $value;
							$bfi_args['crop'] = false;
						}
					}
				}

				$main_logo_height = '';
				$logo_src = $this->cws_print_img_html($logo, $bfi_args, $main_logo_height);
			}

			$main_logo_height = !empty($main_logo_height) ? ' style="height:' . $main_logo_height . 'px;"' : '';

			$side_panel_position = isset($side_panel['sidepanel-position']) ? $side_panel['sidepanel-position'] : "";

			$side_panel_sidebar = isset($side_panel['sidebar']) ? $side_panel['sidebar'] : "";

			$side_panel_appear = isset($side_panel['appear']) ? $side_panel['appear'] : "";

			echo '
			<div class="side_panel_overlay '.esc_attr($side_panel_appear).'" style="background:'.esc_attr($this->cws_Hex2RGBA($side_panel['overlay_color'],$bg_opacity)).'">
			</div>

			<div class="side_panel_container '.esc_attr($side_panel_position).' '.esc_attr($side_panel_appear).'">
				<div class="side_panel_bg '.esc_attr($side_panel_position).' '.esc_attr($side_panel_appear).'" style="'.
				esc_attr("background-size:$bg_size;background-image: url($bg_src); background-position: $bg_pos;").'"></div>
				<aside style="'.
				esc_attr("background-color: rgba($bg_color,$bg_opacity);").'" class="side_panel '.esc_attr($side_panel['theme'] ).'-theme '.esc_attr($side_panel_position).' '.esc_attr($side_panel_appear).'">
						<div class="side_panel_wrapper close-'.esc_attr($side_panel['sidepanel_close_position']).'" style="text-align:'.esc_attr($side_panel['logo_position'] ).'">
						 	'.(!empty($logo_src) ? '<img '.$logo_src.' '.$main_logo_height.' alt />' : "").' 
							<div class="mobile_menu_bar">
								<div class="close_side_panel mobile_menu_hamburger mobile_menu_hamburger--htx deactive">
									<span></span>
								</div>
							</div>
						</div>';
				if (!empty($side_panel_sidebar)) {
					if ( is_active_sidebar( $side_panel_sidebar ) ) {
						dynamic_sidebar($side_panel_sidebar);
					}
				}
				echo '</aside>
			</div>';
		}
	}
	/* SIDE PANEL */

	/* BODY OVERLAY */
	public function cws_body_overlay() {
		$is_boxed = $this->cws_get_meta_option('is_boxed') == '1';
		if ($is_boxed) {

			$overlay = $this->cws_get_meta_option('boxed_overlay');
			$type = $this->cws_get_meta_option('boxed_overlay_type');
			$opacity = $overlay['opacity']/100;

			switch ($type) {
				case 'color':
					$color = $overlay['color'];
					$style = sprintf('rgba(%s;%s)',$color, $opacity);
					echo "<div class='body_overlay' style='background-color:" . esc_attr($overlay_style) . "'></div>";
					break;
				case 'gradient':
					//$boxed_gradient = $this->cws_render_gradient($this->cws_get_meta_option('boxed_gradient'));
					$boxed_gradient_rules = $this->cws_render_gradient_rules( $this->cws_get_meta_option('boxed_gradient') );
					echo "<div class='body_overlay' style='$boxed_gradient_rules" . "opacity:".esc_attr($opacity).";'></div>";
					break;
			}
		}
	}
	/* BODY OVERLAY */

	/******************** CUSTOM COLOR ********************/
	public function cws_render_gradient_rules( $settings, $selectors = '',  $use_extra_rules = false) {
		extract( shortcode_atts( array(
			'c1' => AASANA_COLOR,
			'c2' => '#0eecbd',
			'op1' => '100',
			'op2' => '100',
			'type' => 'linear',
			'linear_settings' => array(),
			'radial_settings' => array(),			
			'first_color' => "",
			'second_color' => '',
			'first_color_opacity' => '',
			'second_color_opacity' => '',
		), $settings['settings']));
		$c1 = !empty($settings['settings']['first_color']) ? $settings['settings']['first_color'] : $c1;
		$c2 = !empty($settings['settings']['second_color']) ? $settings['settings']['second_color'] : $c2;
		$op1 = !empty($settings['settings']['first_color_opacity']) ? $settings['settings']['first_color_opacity'] : $op1;
		$op2 = !empty($settings['settings']['second_color_opacity']) ? $settings['settings']['second_color_opacity'] : $op2;

		$c1 = 'rgba('.$this->cws_Hex2RGB($c1).','.(int)$op1/100 .')';
		$c2 = 'rgba('.$this->cws_Hex2RGB($c2).','.(int)$op2/100 .')';

		$out = '';
		$rules = '';
		switch ($type) {
			case 'linear':
				$angle = $linear_settings['angle'];
				$rules .= "background:-webkit-linear-gradient({$angle}deg, $c1, $c2);";
				$rules .= "background:-o-linear-gradient({$angle}deg, $c1, $c2);";
				$rules .= "background:-moz-linear-gradient({$angle}deg, $c1, $c2);";
				$rules .= "background:linear-gradient({$angle}deg, $c1, $c2);";
				break;
			case 'radial':
				extract( shortcode_atts( array(
					'shape_settings' => 'simple',
					'shape' => 'ellipse',
					'size_keyword' => 'farthest-corner',
					'size' => ''
				), $radial_settings));
				switch ($shape_settings) {
					case 'simple':
						$rules .= "background:-webkit-radial-gradient($shape $c1, $c2);";
						$rules .= "background:-o-radial-gradient($shape $c1, $c2);";
						$rules .= "background:-moz-radial-gradient($shape $c1, $c2);";
						$rules .= "background:radial-gradient($shape $c1, $c2);";
						break;
					case 'exteneded':
						$rules .= "background:-webkit-radial-gradient( $size $size_keyword $c1, $c2);";
						$rules .= "background:-o-radial-gradient( $size $size_keyword $c1, $c2);";
						$rules .= "background:-moz-radial-gradient( $size $size_keyword $c1, $c2);";
						$rules .= "background:radial-gradient($size_keyword at $size $c1, $c2);";
						break;
				}
				break;
		}

		if ( !empty($rules) ) {
			$printf_rules = !empty($selectors) ? '%s{%s}' : '%s%s';
			$out .= sprintf($printf_rules, $selectors, $rules);
			if ( $use_extra_rules ) {
				$border_extra_rules = 'border-color:transparent;-moz-background-clip:border;-webkit-background-clip: border;background-clip:border-box;-moz-background-origin:border;-webkit-background-origin:border;background-origin:border-box;background-repeat:no-repeat;';
				$transition_extra_rules = '-webkit-transition-property:background,color,border-color,opacity;-webkit-transition-duration:0s,0s,0s,0.6s;-o-transition-property:background,color,border-color,opacity;-o-transition-duration:0s,0s,0s,0.6s;-moz-transition-property:background,color,border-color,opacity;-moz-transition-duration:0s,0s,0s,0.6s;transition-property:background,color,border-color,opacity;transition-duration:0s,0s,0s,0.6s;';
				$out .= sprintf($printf_rules, $selectors, $border_extra_rules);
				$out .= sprintf($printf_rules, $selectors, 'color: #fff !important;');
				$selectors_wth_pseudo = str_replace( ':hover', '', $selectors );
				$out .= sprintf($printf_rules, $selectors_wth_pseudo, $transition_extra_rules);
			}
		}
		return $out;
	}
	/******************** \CUSTOM COLOR ********************/

	public function cws_widget_title_icon_rendering( $args = array() ) {
		extract( shortcode_atts(
			array(
				'icon_type' => '',
				'icon_fa' => '',
				'icon_img' => array(),
				'icon_color' => '#fff',
				'icon_bg_type' => 'color',
				'icon_bgcolor' => AASANA_COLOR,
				'gradient_first_color' => AASANA_COLOR,
				'gradient_second_color' => '#0eecbd',
				'gradient_type' => '',
				'gradient_linear_angle' => '',
				'gradient_radial_shape' => '',
				'gradient_radial_type' => '',
				'gradient_radial_size_key' => '',
				'gradient_radial_size' => '',
				), $args));

		$r = $icon_styles = '';
		if ( $icon_type == 'fa' && !empty( $icon_fa ) ) {
			switch ($icon_bg_type) {
				case 'none':
					$icon_styles .= "border-width: 1px; border-style: solid;";
					break;
				case 'color':
					$icon_styles .= "background-color:$icon_bgcolor";
					break;
				case 'gradient':
					$gradient_settings = $this->cws_extract_array_prefix($args, 'gradient');
					$gradient_settings_arr = array(
						'first_color' => $gradient_settings["first_color"],
						'second_color' => $gradient_settings["second_color"],
						'type' => $gradient_settings["type"],
						'linear_settings' => array(
							'angle' => $gradient_settings["linear_angle"],
						),
						'radial_settings' => array(
							'shape_settings' => $gradient_settings["radial_shape"],
							'shape' => $gradient_settings["radial_type"],
							'size_keyword' => $gradient_settings["radial_size_key"],
							'size' => $gradient_settings["radial_size"],
						),
					);

					$gradient_settings = isset( $gradient_settings_arr ) ? $gradient_settings_arr : new stdClass();
					$settings = new stdClass();

					foreach ($gradient_settings_arr as $key => $value) {
						$settings->$key = $value;
					}

					$icon_styles .= esc_attr( $this->cws_render_gradient_rules( array( 'settings' => $settings ) ) );
					break;
			}

			$icon_styles .= "color:$icon_color;";
			$r .= "<i class='$icon_fa' style='$icon_styles'></i>";
		}	else if ( $icon_type == 'img' && !empty( $icon_img['src'] ) ) {

			$font = $this->cws_get_meta_option( 'body-font' );
			$font_size = isset( $font['font_size'] ) ? preg_replace( 'px', '', $font['font_size'] ) : '15';
			$thumb_size = (int)round( (float)$font_size * 2 );

			$g_img = $this->cws_print_img_html(array('src' => $icon_img['src']), array( 'width' => $thumb_size, 'height' => $thumb_size ));
			$this->echo_ne($g_img, "<img{$g_img} alt/>");
			//$thumb_obj = cws_thumb( $img_url, array( 'width' => $thumb_size, 'height' => $thumb_size ), false );

		}
		return $r;
	}

	private function cws_extract_array_prefix($arr, $prefix) {
		$ret = array();
		$pref_len = strlen($prefix);
		foreach ($arr as $key => $value) {
			if (0 === strpos($key, $prefix . '_') ) {
				$ret[mb_substr($key, $pref_len+1)] = $value;
			}
		}
		return $ret;
	}

	public function cws_post_info_part($custom_layout_arr = null, $meta_info_arr = array()) {
		$show_author = $this->cws_get_meta_option( 'blog_author' );
		$permalink = get_permalink();
		$title = get_the_title();
		$date = get_the_time( get_option( 'date_format' ) );
		$first_word_boundary = strpos( $date, ' ' );

		ob_start();
		$this->cws_output_media_part($custom_layout_arr,$permalink,$date,$first_word_boundary,$meta_info_arr);
		$media_content = ob_get_clean();

		$section_class = "post_info_part";
		$section_class .= (!empty($custom_layout_arr['blogtype']) ? (in_array($custom_layout_arr['blogtype'], array("1","large","2", "3", "4")) && $custom_layout_arr['post_size'] != 'mini' ? ' clearfix' : '') : '');
		$section_class .= empty( $media_content ) ? " full_width" : '';
		$header_class = "post_info_header";
		$post_format = get_post_format();

		$header_class .= ( in_array( $post_format, array( 'quote', 'audio' ) ) || ( $post_format == 'link' && !has_post_thumbnail() ) ) ? " rounded" : '';

		$post_meta = cws_core_cwsfw_get_post_meta( get_the_ID(), 'cws_mb_post' );
		$post_meta = isset( $post_meta[0] ) ? $post_meta[0] : array();

		if (!empty($media_content)) {
			if (is_single() && $custom_layout_arr['is_related'] == '0') {
				if (!empty($post_meta['show_featured']) && $post_meta['show_featured'] == '1' && empty($post_meta['full_width'])) {
				?><div class="<?php echo esc_attr($section_class); ?>"><?php echo sprintf("%s", $media_content);?></div><?php
				}
			} else {
				?><div class="<?php echo esc_attr($section_class); ?>"><?php echo sprintf("%s", $media_content);?></div><?php
			}
		}
	}

	public function cws_get_post_thumbnail_dims ( $custom_layout_arr = false, $eq_thumb_height = false, $real_dims = array() ) {
		$p_id = get_queried_object_id();

		if (is_single()){	
			if ($custom_layout_arr['is_related'] == '1'){
				$p_meta = cws_core_cwsfw_get_post_meta( $p_id, 'cws_mb_post' );
				$p_meta = isset( $p_meta[0] ) ? $p_meta[0] : array();
				$p_layout = isset( $p_meta['rpo_cols'] ) ? (int) $p_meta['rpo_cols'] : '2';
				$blogtype = $p_layout;
			} else {
				$blogtype = 'large';
			}
		} else if(is_page()){
			$blogtype_from_meta = $this->cws_get_page_meta_var( array( "blog", "blogtype" ) );
			$blogtype = $blogtype_from_meta ? $blogtype_from_meta : $this->cws_get_option( "def_blogtype" );
		} else {
			$blogtype = $this->cws_get_option( "def_blogtype" );
		}

			if ($custom_layout_arr['pagination'] == '1' && ($custom_layout_arr['pagination_style'] == 'ajax' || $custom_layout_arr['pagination_style'] == 'load_more')){
				$blogtype = $custom_layout_arr['blogtype'];
			}		

			if ($blogtype == 'default') {
				$blogtype = $this->cws_get_option( 'def_blogtype' );
			}	

		$sb = $this->cws_get_sidebars( $p_id );
		$sb_block = !empty( $sb['sb_layout'] ) && $sb['sb_exist'] ? $sb['sb_layout'] : 'none';

		$single = is_single();
		$width_correction =  0;
		$height_correction = 0;
		$dims = array( 'width'=>0, 'height'=>0 );

		list($width, $height)	= self::$blog_thumb_dims[$blogtype][$sb_block];
		$dims = array( 'width'=> $width, 'height'=> $width );

		$dims['width'] = $dims['width'] != 0 ? $dims['width'] - $width_correction : $dims['width'];
		$dims['height'] = $dims['height'] != 0 ? $dims['height'] - $height_correction : $dims['height'];
		return $dims;
	}

	public function cws_get_fw_post_thumbnail_dims ( $custom_layout_arr = array() ){
		$p_id = get_queried_object_id();
		$sb = $this->cws_get_sidebars( $p_id );
		$sb_block = !empty( $sb['sb_layout'] ) && $sb['sb_exist'] ? $sb['sb_layout'] : 'none';

		$width_correction = 0;
		$correction_arr = array(
			'checkerboard' => array(
				'def' => array(
					'none' => 30,
					'left' => 35,
					'right' => 35,
					'both' => 30,
				),
				'benefits' => array(
					'none' => 30,
					'left' => 35,
					'right' => 35,
					'both' => 30,
				),
				'fullwidth_background' => array(
					'none' => 30,
					'left' => 35,
					'right' => 35,
					'both' => 30,
				),
				'fullwidth_item_no_padding' => array(
					'none' => 24,
					'left' => 35,
					'right' => 35,
					'both' => 30,
				),	
				'fullwidth_item' => array(
					'none' => 24,
					'left' => 35,
					'right' => 35,
					'both' => 30,
				),	
			),
		);		

		$blogtype = !empty($custom_layout_arr['blogtype']) ? $custom_layout_arr['blogtype'] : '1';
		$row_style = isset($custom_layout_arr['row_style']) ? $custom_layout_arr['row_style'] : 'def';
		$col = isset($custom_layout_arr['column_count']) ? (int) $custom_layout_arr['column_count'] : 1;

		if ($col == 0){
			switch ($blogtype) {
				case 'checkerboard':
					$col = 2;	
					$width_correction = $correction_arr[$blogtype][$row_style][$sb_block];
					break;				
				case 'medium':
					$col = 3;
					break;				
				case 'small':
					$col = 4;
					break;
			}
		}

		switch ($row_style) {
			case 'def':
			case 'benefits':
			case 'fullwidth_background':
				$resolution = 1920;
				break;				
			case 'fullwidth_item_no_padding':
			case 'fullwidth_item':
				$resolution = 1920;
				break;				
		}
		
		if ($sb_block != 'none'){
			if ($sb_block == 'left' || $sb_block == 'right'){
				$resolution = 870;
			} elseif ($sb_block == 'both') {
				$resolution = 570;
			}
		} else {
			$resolution = isset($custom_layout_arr['row_style']) ? $resolution : 1920;
		}

		$width = ($resolution / (int) $col) - $width_correction;
		return array('width' => $width, 'height' => null);
	}

	public function cws_output_media_part ( $custom_layout_arr, $permalink, $date, $first_word_boundary, $meta_info_arr = array()) {
		$column_style = $custom_layout_arr['column_style'];
		$column_count = $custom_layout_arr['column_count'];
		$custom_layout = intval( $custom_layout_arr['custom_layout'] );
		$post_url = get_the_permalink();
		$single = is_single();
		$post_format = get_post_format();
		$eq_thumb_height = in_array( $post_format, array( 'gallery' ) );

		//Get meta from post
		$media_meta = $this->cws_get_post_meta( get_the_ID(), 'cws_mb_post' );
		$media_meta = isset( $media_meta[0] ) ? $media_meta[0] : array();

		$use_blur = $this->cws_get_meta_option('use_blur');

		$thumbnail = has_post_thumbnail( ) ? wp_get_attachment_image_src( get_post_thumbnail_id( ),'full' ) : '';
		$thumbnail = ! empty( $thumbnail ) ? $thumbnail[0] : '';
		$thumbnail_dims = $this->cws_get_post_thumbnail_dims( $custom_layout_arr, $eq_thumb_height );

		$real_thumbnail_dims = array();
		if ( isset( $thumbnail_props[1] ) ) $real_thumbnail_dims['width'] = $thumbnail_props[1];
		if ( isset( $thumbnail_props[2] ) ) $real_thumbnail_dims['height'] = $thumbnail_props[2];

		$thumbnail_dims = $this->cws_get_fw_post_thumbnail_dims($custom_layout_arr);

		$crop_thumb = isset( $thumbnail_dims['width'] ) && $thumbnail_dims['width'] > 0;
		$thumb_media = false;

		$image_data = wp_get_attachment_metadata( get_post_thumbnail_id( get_the_ID() ) );
		if ( ! empty( $thumbnail ) ) {
			if ( $single ) {
				if ( ($image_data['width'] < $thumbnail_dims['width']) && $custom_layout_arr['is_related'] != '1') {
					$img_data['width'] = 0;
					$img_data['height'] = 0;
				} else {
					$img_data = $thumbnail_dims;
				}

				if ($this->cws_get_option( "crop_related_items" ) == '1' && $custom_layout_arr['is_related'] == '1'){
					$img_data['crop'] = array(
						$this->cws_get_option( "crop_x" ),
						$this->cws_get_option( "crop_y" )
					);
				}
			} else {
				if ($image_data['width'] < $thumbnail_dims['width']){
					$img_data['width'] = 0;
					$img_data['height'] = 0;
				} else {
					$img_data = $thumbnail_dims;
					$img_data['crop'] = array(
						$this->cws_get_option( "crop_x" ),
						$this->cws_get_option( "crop_y" )
					);
				}
			}
		}

		if ($custom_layout_arr['aspect_ratio'] == '1'){
			$img_data['height'] = null;
			$img_data['crop'] = false;
		}

		$quote = ('quote' === $post_format && isset( $media_meta['quote_text'] )) ? $media_meta['quote_text'] : '';

		$quote_post = ( !empty($quote) ) ? ' quoute_post' : '';
		$video_post = ('video' === $post_format)  ? ' video_post' : '';
		$audio_post = ( 'audio' === $post_format && isset( $media_meta['audio'] ) ) ? ' audio_post' : '';
		$audio_post .= isset( $media_meta['audio'] ) ? ( is_int( strpos( $media_meta['audio'], 'https://soundcloud' ) ) ? ' soundcloud' : '') : '';
		$link_title = isset( $media_meta['link_title'] ) ? esc_html( $media_meta['link_title'] ) : "";
		$link_post = isset( $media_meta['link'] ) ? esc_url( $media_meta['link'] ) : "";
		$gallery_post = ('gallery' === $post_format && isset( $media_meta['gallery'] ) && !empty($media_meta['gallery'])) ? ' gallery_post'.($media_meta['gallery_type'] == 'grid' ? ' gallery_grid' : '') : '';
		$some_media = false;
		ob_start();
			$post_class = '';
			$out = '';
			switch ($post_format) {
				case 'link':
					ob_start();
					if (is_home() && is_front_page()){
						$link = $media_meta['link'];
					} else {
						$link = $this->cws_get_meta_option('link');
					}		
						?>
						<div class="pic <?php
							echo !empty( $link ) ? 'link_post' : '';
							echo !empty( $thumbnail ) ? ' has_thumbnail' : ' not_thumbnail';
							if($use_blur && (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1'))){
								echo(' blured');
							}
							echo('">');
							echo !empty($link) ? "<a href='".esc_url($link)."'>" : '';
							$thumb_obj = cws_thumb( $thumbnail,$img_data,false );
							$thumb_path_hdpi = $thumb_obj[3] ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_url( $thumb_obj[3] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
							?>			
							<?php
							if(!empty($thumbnail)){
								echo '<span class="link_bg" style="background-image: url('.esc_attr($thumbnail).');background-position: center center;"></span>';
							}
							if(!empty($link_title)){
								echo "<span class='post_media_link_title'>$link_title</span>";
							}
							echo "<div class='hover-effect'></div>";
							
							$some_media = true; 
						
							echo !empty($link) ? "</a>" : '';
							?>
						</div>
						<?php
						$thumb_media = true;
						$some_media = true;
						$post_class = ' link_post';
					
					$out = ob_get_clean();
					break;
				case 'video':
					ob_start();
					$video = isset( $media_meta['video'] ) ? $media_meta['video'] : '';

					if ( ! empty( $video ) ) {
						global $wp_filesystem;

						$unknown_video_service = false;
						$video_service = '';
						if (strpos($video, 'youtu')){
							$video_service = 'youtube';
						} elseif (strpos($video, 'vimeo')) {
							$video_service = 'vimeo';
						} else{
							$unknown_video_service = true;
						}

						$video_dims = $this->cws_get_fw_post_thumbnail_dims($custom_layout_arr);

						if ( !empty( $video_service ) ) {
							preg_match('@[^/]*$@', $video, $video_link);
							$clear_url = array("?", "&amp", "watchv=");
							$video_id = str_replace($clear_url, '', preg_replace('/[^?][a-z]*=\w+/', '', $video_link[0]));
							$video_url = ($video_service == 'youtube' ? str_replace('watch?v=', '', $video_link[0]) :  $video_link[0]);

							if ($video_service == 'youtube'){
								$thumbnail_img = "http://img.youtube.com/vi/".esc_attr($video_id)."/maxresdefault.jpg";
								$link = "http://www.youtube.com/embed/";
								(strpos($video, 't=') ? preg_match('@(t=)[0-9a-z]+@', $video, $time) : '');
								$video_time = isset($time[0]) ? $time[0] : '';
							} elseif ($video_service == 'vimeo') {
								$json = json_decode($wp_filesystem->get_contents("https://vimeo.com/api/oembed.json?url=".$video));
								$vimeo_id = $json->video_id;
								$thumbnail_img = $json->thumbnail_url;
								$link = "https://player.vimeo.com/video/";
								$video_url = ($video_id != $vimeo_id ? str_replace($video_id, $vimeo_id, $video_url) : $video_url);
							}
							$embed_link = $link.esc_attr($video_url).(!$single ? (($video_id != $video_url ? '&amp;' : '?')."autoplay=1") : '');
						}

						?>
							<div class='video'>
								<?php if ( !$single && $custom_layout_arr['use_carousel'] == '1' && $media_meta['enable_lightbox'] == '1' && !$unknown_video_service) { ?>
									<div class='pic'>

									<?php if (!empty($thumbnail) ){
										$thumb_obj = cws_thumb( $thumbnail,$img_data,false );
										$thumb_path_hdpi = $thumb_obj[3]['retina_thumb_exists'] ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_attr( $thumb_obj[3]['retina_thumb_url'] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
										echo "<img $thumb_path_hdpi alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
										$some_media = true;
									} else {
									?>
										<img src='<?php echo esc_url($thumbnail_img);?>' >
									<?php } ?>

										<div class='hover-effect'></div>
										<div class='links'>
											<a class="fancy fancybox.iframe" href="<?php echo esc_url($embed_link);?>"><i class='play_video fa fa-play-circle'></i></a>
										</div>
									</div>
								<?php } else {
									echo apply_filters( 'the_content',"[embed width='" . $video_dims['width'] . "']" .($video_service == 'youtube' ? 'https://youtu.be/'.$video_id : $video ).(!empty($video_time) ? '?'.$video_time : '').'[/embed]' );
								} ?>
							</div>

						<?php
					
						$some_media = true;
						$post_class = ' video_post';
					}		
					$out = ob_get_clean();
							
					break;
				case 'audio':
					ob_start();
					$audio = isset( $media_meta['audio'] ) ? $media_meta['audio'] : '';
					$is_sounfcloud = is_int( strpos( (string) $audio, 'https://soundcloud' ) );

					if ( $is_sounfcloud == false ) {
						if ( ! empty( $thumbnail ) ) {
							$thumb_obj = cws_thumb( $thumbnail,$img_data,false );
							$thumb_path_hdpi = $thumb_obj[3]['retina_thumb_exists'] ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_attr( $thumb_obj[3]['retina_thumb_url'] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
							echo "<div class='pic".($use_blur ? ' blured' : '')."'>
										<img ". $thumb_path_hdpi ." alt />
										".($use_blur ? "<img ". $thumb_path_hdpi ." class='blured-img' alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />" : '' )."
									</div>";
							$thumb_media = true;
							$some_media = true;
						}
						if ( ! empty( $audio ) ) {
							echo "<div class='audio'>" . apply_filters( 'the_content','[audio src="' . esc_url( $audio ) . '"]' ) . '</div>';
							$some_media = true;
						}
					} else {
						switch ($custom_layout_arr['row_style']) {
							case 'def':
							case 'benefits':
							case 'fullwidth_background':
								echo apply_filters( 'the_content',"[embed width='".$thumbnail_dims['width']."' height='".$thumbnail_dims['height']."'] $audio [/embed]" );
								break;				
							case 'fullwidth_item_no_padding':
							case 'fullwidth_item':
								echo "<div class='soundcloud'>";
									echo apply_filters( 'the_content',"$audio" );
								echo '</div>';
								break;				
						}
						$some_media = true;
					}
					$out = ob_get_clean();
					break;
				case 'quote':
					ob_start();
					$quote = isset( $media_meta['quote_text'] ) ? $media_meta['quote_text'] : '';
					$author = isset( $media_meta['quote_author'] ) ? $media_meta['quote_author'] : '';

					if ( !empty($thumbnail) ) {
						?>
						<div class="pic has_thumbnail <?php echo !empty( $quote ) ? 'quote_post' : ''; ?>">
							<?php
							$thumb_obj = cws_thumb( $thumbnail,$img_data,false );
							$thumb_path_hdpi = $thumb_obj[3] ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_url( $thumb_obj[3] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
							?>
							<?php 
							$some_media = true; 
							

							if ( !empty( $quote )) {
								echo "<div class='hover-effect'></div>";
								echo "<div class='quote-wrap'>"; 
									echo "<div class='quote'>";
										echo "<p class='text'>".$quote."</p>";
										if(!empty($author)){
											echo "<p class='author'> - ".$author."</p>";
										}
										echo "<div class='quote_bg_c'></div>";
										
									echo '</div>';
									echo '<div class="quote_bg" style="background-image: url('.esc_attr($thumbnail).');background-position: center center;"></div>';
								echo '</div>';
							}
							?>
						</div>
						<?php
						$thumb_media = true;
						$some_media = true;
					} else {
						echo "<div class='quote-wrap'>"; 
						echo "<blockquote><p>$quote<cite> - $author</cite></p></blockquote>";
						$some_media = true;
						echo "</div'>"; 
					}
					$out = ob_get_clean();
					break;
				case 'gallery':
					ob_start();
					$gallery = isset( $media_meta['gallery'] ) ? $media_meta['gallery'] : '';
					if ( !empty( $gallery ) ) {
						$match = preg_match_all("/\d+/",$gallery,$images);
						if ($match){
							

							$images = $images[0];
							$image_srcs = array();
							
							foreach ( $images as $image ) {
								$image_src = wp_get_attachment_image_src($image,'full');
								$image_url = $image_src[0];
								if (!empty($image_url)) array_push( $image_srcs, $image_src );
							}

							$thumb_media = $some_media = count( $image_srcs ) > 0 ? true : false;

							$carousel = count($image_srcs) > 1 ? true : false;
							$gallery_id = uniqid( 'cws-gallery-' );

							$is_grid = $media_meta['gallery_type'] == 'grid' && $single && $custom_layout_arr['is_related'] != '1';
							$is_slider = $carousel && $media_meta['gallery_type'] == 'slider' || !$single || $custom_layout_arr['is_related'] == '1';

							if ($is_slider) {
								wp_enqueue_script ('owl_carousel');
								wp_enqueue_script ('isotope');
							}

							if ($is_grid) {
								$columns = $media_meta['grid_cols'];
								$grid_class = "grid grid-$columns isotope";
								wp_enqueue_script ('isotope');
							}

							if ($is_slider && $carousel) {
								echo "<a class='carousel_nav prev'><span><i class='fa fa-long-arrow-left'></i></span></a>
													<a class='carousel_nav next'><span><i class='fa fa-long-arrow-right'></i></span></a>
													<div class='gallery_post_carousel'>";
							} elseif ($is_grid) {
								echo "<div class='".$grid_class."'>";
							}

							foreach ( $image_srcs as $image_src ) {
								$src = $image_src[0];
								$width = $image_src[1];
								$height = $image_src[2];

								if ($is_grid) {
									echo "<article class='item'>";
								}

								if ($custom_layout_arr['aspect_ratio'] == '1'){
									$thumbnail_dims['height'] = null;
								}

								$thumbnail_dims['crop'] = array(
									$this->cws_get_option( "crop_x" ),
									$this->cws_get_option( "crop_y" )
								);

								if ($single && $is_grid){
									//Make square dimensions
									if ($width <= $height){
										$thumbnail_dims['width'] = $width;
										$thumbnail_dims['height'] = $width;
									} elseif ($height <= $width) {
										$thumbnail_dims['width'] = $height;
										$thumbnail_dims['height'] = $height;
									}
								}

								$img_src = $this->cws_print_img_html( array('src' => $src), $thumbnail_dims );
								?>
								<div class='pic<?php if($custom_layout_arr['text_over_image'] == 1) {echo (' colored_box_style');} if($use_blur && (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1'))){ echo(' blured');}  ?>'>

									<?php
									if ($custom_layout_arr['text_over_image'] == 1){
										$category = explode(", ", $meta_info_arr['category']);
									?>
										<figure class="effect-marley">
											<div class="colored_category">
											<?php
											$colors = self::$cws_theme_config['category_colors'];
											$bg_color = $bg_color0 = '';
											for ($i = 0; $i < count($category); $i++)	{
												$bg_color0 = $colors[rand(0, count($colors)-1)];
												if ('' !== $bg_color) {
													while ($bg_color === $bg_color0) {
														$bg_color0 = $colors[rand(0, count($colors)-1)];
													}
												}
												$bg_color = $bg_color0;
											?>
												<div class="category_blocks" style="background-color: #<?php echo esc_attr($bg_color); ?>;">
													<?php echo sprintf("%s", $category[$i]);?>
												</div>
											<?php }
											?>
											</div>

											<a href='<?php echo esc_url(get_the_permalink()); ?>'>
												<div class="effect-wrapper"></div>
											</a>
												<?php
													echo "<img ".$img_src." alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
												?>
												<figcaption>
													<div class="title_info">
													<?php echo sprintf("%s", $meta_info_arr['title']);?>
													<?php echo sprintf("%s", $meta_info_arr['content']);?>
													</div>

													<div class="meta_info">
													<?php echo sprintf("%s", $date);?> / <?php echo sprintf("%s", $meta_info_arr['author']);?>
													</div>
												</figcaption>

										</figure>

									<?php
										$thumb_media = true;
										$some_media = true;
									} else {

										echo (($custom_layout == 1 ) || (($custom_layout != 1) && ( !empty($media_meta['enable_lightbox']))   )) ? "<img ".$img_src." alt = '" . get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />" : '';
					
										if (	(($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0)) || (isset($media_meta['enable_lightbox']) && $media_meta['enable_lightbox'] == '1')	) 
										{
										?>
											
											<div class="links">
												<a href="<?php echo esc_url($image_src[0]); ?>" <?php echo !empty($carousel) ? " data-fancybox-group='".esc_attr($gallery_id)."'" : ''; ?> class="fancy <?php echo !empty($carousel) ? 'cwsicon-search-icon fancy_gallery' : 'cwsicon-magnifying-glass84'; ?>"></a>
											</div>
										<?php }
									}
									?>

								</div>
								<?php
								if ($is_grid) {
									echo "</article>";
								}
							}
							if ($is_grid) {
								echo '</div>';
							}
							if($is_slider){
								echo "</div>";
							}
						}
						$post_class = ' gallery_post';
						$some_media = true;
					}
					$out = ob_get_clean();
					break;
			}
	?><?php
		if ($custom_layout_arr['date_style'] != '0'){
			if ( !(!empty($custom_layout_arr['boxed_style']) && $custom_layout_arr['boxed_style'] != 'none') || empty($media_content) ) {
				?>
				<div class="date new_style d">
					<div class="meta_date">
						<?php
						echo "<a class='date-content' href='".$permalink."'>";
						if ( $first_word_boundary ) {
							$arr_date = explode(" ", $date);
							foreach ($arr_date as $key => $value) {
								echo "<span class='date-c'>".$value."</span>";
							}
						}
						echo "</a>";
						?>
					</div>
				</div>
				<?php
			}
		}
		if ( !$some_media && !empty( $thumbnail ) ) {
			ob_start();

			$retina_thumb_url = $this->cws_print_img_html(array('src' => $thumbnail), $img_data);
			echo "<div class='pic ".($custom_layout_arr['text_over_image'] == 1 ? 'colored_box_style' : '').
				( (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1')) ? ' blured' : '' )."'>";

			if ($custom_layout_arr['text_over_image'] == 1){
				//Alternative Style (Colored Boxes)
				$category = explode(", ", $meta_info_arr['category']);
		?>

		<figure class="effect-marley">
			<div class="colored_category">
			<?php
			$colors = self::$cws_theme_config['category_colors'];
			$bg_color = $bg_color0 = '';
			for ($i = 0; $i < count($category); $i++)	{
				$bg_color0 = $colors[rand(0, count($colors)-1)];
				if ('' !== $bg_color) {
					while ($bg_color === $bg_color0) {
						$bg_color0 = $colors[rand(0, count($colors)-1)];
					}
				}
				$bg_color = $bg_color0;
			?>
				<div class="category_blocks" style="background-color: #<?php echo esc_attr($bg_color); ?>;">
					<?php echo sprintf("%s", $category[$i]);?>
				</div>
			<?php }
			?>
			</div>

			<a href='<?php echo esc_url(get_the_permalink()); ?>'>
				<div class="effect-wrapper"></div>
			</a>
				<?php
					echo "<img".$retina_thumb_url." alt = '" . get_post_meta(  get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";
				?>
				<figcaption>
					<div class="title_info">
					<?php echo sprintf("%s", $meta_info_arr['title']);?>
					<?php echo sprintf("%s", $meta_info_arr['content']);?>
					</div>

					<div class="meta_info">
					<?php echo sprintf("%s",$date) ;?> / <?php echo sprintf("%s", $meta_info_arr['author']);?>
					</div>
				</figcaption>

		</figure>

		<?php
			echo '</div>';
			$thumb_media = true;
			$some_media = true;
		} else {

			if ( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 1) && !$single){
				echo "<a href='".$permalink."'>";
			}
				echo "<img".$retina_thumb_url." alt = '" . get_post_meta(  get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) . "' />";

			if ( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 1) && !$single){
				echo "</a>";
			}

			if (( ($custom_layout == 1) && (intval( $custom_layout_arr['disable_lightbox'] ) == 0) ) || (($custom_layout != 1) && (isset($media_meta['enable_lightbox'])) && $media_meta['enable_lightbox'] == '1'))  {

				echo "<div class='hover-effect'></div>";
				echo "<a class='fancy post_media_link post_post_media_link posts_grid_post_media_link' href='".esc_url($thumbnail)."'></a>";
			}

			echo '</div>';
			$thumb_media = true;
			$some_media = true;
			}
			$out = ob_get_clean();
		}
		echo '<div class="media_part ' .  esc_attr($post_class) . '">' . $out . '</div>';

		$some_media ? ob_end_flush() : ob_end_clean();
	}

	public function cws_categories_output ( $query = false ) {
		$custom_layout_arr = array(
			'posts_per_page' => isset( $query['posts_per_page'] ) ? $query['posts_per_page'] : 10,
			'this_shortcode' => isset( $query['this_shortcode'] ) ? $query['this_shortcode'] : false,
			'use_carousel' => isset( $query['use_carousel'] ) ? $query['use_carousel'] : 0,
			'categories' => isset( $query['categories'] ) ? $query['categories'] : '',
			'column_count' => isset( $query['column_count'] ) ? intval( $query['column_count'] ) : 3,
		);

		$page_id = get_queried_object_id();
		$blogtype = $custom_layout_arr['column_count'];

		$sb = $this->cws_get_sidebars( $page_id );
		if ($sb['sb_layout'] == 'default') $sb['sb_layout'] = $this->cws_get_option( "def-page-layout" );
		if (($sb['sb_layout'] == 'left' || $sb['sb_layout'] == 'right') && (empty($sb['sidebar1'])) ) $sb['sb_layout'] = 'none';
		if ($sb['sb_layout'] == 'both' && (empty($sb['sidebar1']) || empty($sb['sidebar2'])) ) $sb['sb_layout'] = 'none';
		$sb_block = isset( $sb['sb_layout'] ) ? $sb['sb_layout'] : 'none';

		list($width, $height)	= self::$blog_thumb_dims[$blogtype][$sb_block];
		$dims = array( 'width'=> $width, 'height'=> $width );

		for ($i=0; (($i <= $custom_layout_arr['posts_per_page']-1) && $i<= count($custom_layout_arr['categories'])-1); $i++) {
			$category = $custom_layout_arr['categories'][$i];
			$id = get_cat_ID($category);
			$link = get_category_link($id);
			$term_image = get_term_meta( $id, 'cws_mb_term' );
			$dummy_image = get_template_directory_uri() . "/img/img_placeholder.png";
			$is_dummy = true;

			ob_start();
				if (!empty($term_image[0]['image']['src'])){
					$is_dummy = false;
					$dims['crop'] = true;
					$img_obj = cws_thumb( $term_image[0]['image']['src'], $dims , false );
				}
				?>

					<article <?php post_class(array( 'item')); ?>>
						<div class='category-block'>
							<a href="<?php echo esc_url($link);?>">
								<img src='<?php echo esc_url( !$is_dummy ? $img_obj[0] : $dummy_image ); ?>' <?php if($is_dummy){ echo (esc_attr("style=width:{$dims['width']}px;height:{$dims['height']}px;")); } ?> alt=''>
								<div class='category-wrapper'>
									<div class='category-label-wrapper'>
										<span class='category-label'><?php echo sprintf("%s", $category); ?></span>
									</div>
								</div>
							</a>
						</div>
					</article>

				<?php
			echo ob_get_clean();
		}
	}

	public function cws_move_comment_field_to_bottom( $fields ) {
		$comment_field = $fields['comment'];
		unset( $fields['comment'] );
		$fields['comment'] = $comment_field;
		return $fields;
	}

}
/* end of Theme's Class */
//Ajax blog pagination
add_action("wp_ajax_cws_get_post", "cws_get_post");			
add_action("wp_ajax_nopriv_cws_get_post", "cws_get_post"); 
function cws_get_post(){
	$data = $_POST['data'];
	extract( shortcode_atts( array(
		'p_id' => '',
		'paged' => 1,
		'items_per_page' => get_option( 'posts_per_page' ),
		'pagination' => '0',
		'pagination_style' => 'paged',
		'url' => '',
		'columns' => '1',

		'meta_position' => 'bottom',
		'meta_align' => 'center',
		'content_align' => 'center',
		'full_width' => '0',
		'full_width_spacing' => '',
		'full_width_border' => '',
		'aspect_ratio' => '0',
		'sel_posts_by' => 'none',

		'order_posts_by' => 'date',
		'order_posts_direction' => 'DESC',

		'titles' => '',
		'categories' => '',
		'tags' => '',
		'exclude' => '',

		'this_shortcode' => true,

		'use_carousel' => '0',
		'custom_layout' => '0',
		'post_text_length' => '',
		'content_divider' => '0',
		'post_divider' => '0',
		'button_name' => '',
		'disable_lightbox' => '0',
		'hide' => '',
		'text_over_image' => '0',
		'no_margin' => '0',
		'post_size' => '',
		'date_style' => '1',
		'boxed_style' => '',
		'is_related' => '0',
		'extra_query' => array(),

		'blogtype' => 'large',
		'row_style' => 'def',
	), $data));

	global $aasana_theme_funcs;

	if ( empty( $url ) ) return;
	$match = preg_match( "#paged?(=|/)(\d+)#", $url, $matches );
	$paged = $match ? $matches[2] : 1;

	$post_parts = array('title', 'tags', 'categories', 'meta', 'content');
	$hide_all = count(array_intersect($hide, $post_parts)) == count($post_parts);

	if ( $sel_posts_by == 'titles' && !empty( $titles ) ) {
		$items_per_page = count( $titles );
	}

	$p_id = get_queried_object_id();

	$items_per_page = (isset($items_per_page) && !empty($items_per_page) ? (int) $items_per_page : 3);

	$column_style = ($columns >= '2');
	$query_args = array(
		'paged' => $paged,
		'post_type' => 'post',
		'ignore_sticky_posts' => true,
		'post_status' => 'publish',
		'posts_per_page' => $items_per_page,
		'this_shortcode' => true,
		'column_style' => $column_style,
		'custom_layout' => (int) $custom_layout,
		'post_text_length' => (int) $post_text_length,
		'content_divider' => (int) $content_divider,
		'post_divider' => (int) $post_divider,
		'button_name' => $button_name,
		'use_carousel' => $use_carousel,
		'disable_lightbox' => $disable_lightbox ,
		'hide' => $hide,
		'text_over_image' => $text_over_image,
		'post_size' => $post_size,
		'date_style' => $date_style,
		'boxed_style' => $boxed_style,
		'column_count' => (int) $columns,
		'is_related' => $is_related,
		'meta_position' => $meta_position,
		'meta_align' => $meta_align,
		'content_align' => $content_align,
		'full_width' => $full_width,
		'full_width_spacing' => $full_width_spacing,
		'full_width_border' => $full_width_border,
		'aspect_ratio' => $aspect_ratio,

		'orderby'    => $order_posts_by,
		'order'      => $order_posts_direction,

		'blogtype' => $columns,
		'pagination' => $pagination,
		'pagination_style' => $pagination_style,		

		'blogtype' => $columns,	
		'row_style' => $row_style,	
	);

	$tax_query = array();
	if ( ($sel_posts_by == 'cats') && !empty( $categories )){ 
		$tax_query[] = array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => $categories
		);
	} else if ( ($sel_posts_by == 'tags') && !empty( $tags )){ 
		$tax_query[] = array(
			'taxonomy' => 'post_tag',
			'field' => 'slug',
			'terms' => $tags
		);
	} else if ( $sel_posts_by == 'titles' && !empty( $titles ) ) {
		$query_args['post__in'] = $titles;
	}

	if (!empty($extra_query)) $query_args  = array_merge($query_args, $extra_query);
	if (!empty($tax_query)) $query_args['tax_query'] = $tax_query;

	$q = new WP_Query( $query_args );

	echo "<div class='cws_ajax_response'>";
	if ( $q->have_posts() ) {

		$aasana_theme_funcs->cws_blog_output( $q );

		$max_paged = ceil( $q->found_posts / $items_per_page );

		if(isset($data['pagination_style']) && $data['pagination_style'] == 'ajax'){
			$aasana_theme_funcs->cws_pagination( $paged, $max_paged );
		}
		else{
			$aasana_theme_funcs->cws_pagination( $paged, $max_paged, 'load_more');
		}	
	}			
	echo "</div>";

	die();
 }

/* FA ICONS */
function aasana_get_all_fa_icons() {
	$meta = get_option('cws_fa');
	if (!empty($meta) || (time() - $meta['t']) > 3600*7 ) {
		global $wp_filesystem;
		if( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
		$file = get_template_directory() . '/css/font-awesome.css';
		$fa_content = '';
		if ( $wp_filesystem && $wp_filesystem->exists($file) ) {
			$fa_content = $wp_filesystem->get_contents($file);
			if ( preg_match_all( "/fa-((\w+|-?)+):before/", $fa_content, $matches, PREG_PATTERN_ORDER ) ) {
				return $matches[1];
			}
		}
	} else {
		return $meta['fa'];
	}
}
/* \FA ICONS */

/* FL ICONS */
function aasana_get_all_flaticon_icons() {
	$cwsfi = get_option('cwsfi');
	if (!empty($cwsfi) && isset($cwsfi['entries'])) {
		return $cwsfi['entries'];
	} else {
		global $wp_filesystem;
		if( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
		$file = get_template_directory() . '/fonts/flaticon/flaticon.css';
		$fi_content = '';
		$out = '';
		if ( $wp_filesystem && $wp_filesystem->exists($file) ) {
			$fi_content = $wp_filesystem->get_contents($file);
			if ( preg_match_all( "/flaticon-((\w+|-?)+):before/", $fi_content, $matches, PREG_PATTERN_ORDER ) ){
				return $matches[1];
			}
		}
	}
}
/* \FL ICONS */

/********************************** !!! **********************************/

function cws_getTweets( $count = 20 ) {
	$res = null;
	global $aasana_theme_funcs;

	if ( '0' != $aasana_theme_funcs->cws_get_option( 'turn-twitter' ) ) {
		$twitt_name = trim($aasana_theme_funcs->cws_get_option( 'tw-username' )) ? trim($aasana_theme_funcs->cws_get_option( 'tw-username' )) : 'Creative_WS';
		if (function_exists('getTweets')) {
			$res = getTweets($twitt_name, $count);
		}
	}

	return $res;
}

if ( ! isset( $content_width ) ) $content_width = 1170;

	/* Full width blog */

	function cws_load_more( $paged = 0, $template = '', $max_paged = PHP_INT_MAX ) {
		?>
			<div class="div_load_more"><a class="cws_button large cws_load_more alt" href="#" data-paged="<?php echo esc_attr($paged) ?>" data-max-paged="<?php echo esc_attr($max_paged) ?>" data-template="<?php echo esc_attr($template); ?>"><?php echo esc_html__( "Load More", 'aasana' ); ?></a></div>
		<?php
	}

	function cws_portfolio_loader(){
		ob_start();
		?>
			<div class='portfolio_loader_wraper'>
				<div class='portfolio_loader_container'>
					<svg width='104px' height='104px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(0 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(30 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.083s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(60 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.1667s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(90 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.25s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(120 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.33s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(150 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.4166s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(180 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(210 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.5833s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(240 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.67s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(270 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.75s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(300 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.83s' repeatCount='indefinite'/></rect><rect x='46.5' y='40' width='7' height='20' rx='5' ry='5' fill='#000000' transform='rotate(330 50 50) translate(0 -30)'><animate attributeName='opacity' from='1' to='0' dur='1s' begin='0.9167s' repeatCount='indefinite'/></rect></svg>
				</div>
			</div>
		<?php
		echo ob_get_clean();
	}

/****************** WALKER *********************/
class Aasana_Walker_Nav_Menu extends Walker {
	private $elements;
	private $elements_counter = 0;
	private $logo_with_site_name;
	private $logo_position;
	private $site_name_title;
	private $aasana_theme_funcs;

	function __construct($a) {
		$this->aasana_theme_funcs = $a;
		$this->logo_with_site_name = $this->aasana_theme_funcs->cws_get_meta_option( 'logo_with_site_name' );

		$this->logo_position = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-position' );

		$this->site_name_title = $this->aasana_theme_funcs->cws_get_meta_option( 'site_name_in_menu' );
	}

	function walk ($items, $depth, ...$args) {
		$this->elements = $this->get_number_of_root_elements($items);
		return parent::walk($items, $depth);
	}

	/**
	 * @see Walker::$tree_type
	 * @since 3.0.0
	 * @var string
	 */
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	/**
	 * @see Walker::$db_fields
	 * @since 3.0.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<span class='button_open'></span><ul class=\"sub-menu\">";
		$output .= "<li class=\"menu-item back\"><a href=\"#\">&lt;" . esc_html__('BACK', 'aasana') . "</a></li>";
		$output .= "\n";
	}
	/**
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */

	function logo_ini( $indent, $item ) {
		$logo_position = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-position' );
		$enable_logo = $this->aasana_theme_funcs->cws_get_meta_option("enable_logo") == '1';
		$logo_cont = '';
		
		$default_logo = 'logo_' . $this->aasana_theme_funcs->cws_get_meta_option('default_logo');
		$logo_lr_spacing = $logo_tb_spacing = $main_logo_height = '';
		$logo = $this->aasana_theme_funcs->cws_get_meta_option($default_logo);
		if ( $logo_position == 'in-menu' && $enable_logo ) {
			$logo_border = '';
			$is_logo_with_site_name = $this->logo_with_site_name == '1';
			if ( !empty($logo['src']) ) {			
				$logo_hw = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-dimensions' );
				$logo_sticky_hw = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-dimensions-sticky' );
				$logo_mobile_hw = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-dimensions-mobile' );
				$logo_m = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-margin' );
				$bfi_args = $bfi_args_sticky = $bfi_args_mobile = array();
				if ( is_array( $logo_hw ) ) {
					foreach ( $logo_hw as $key => $value ) {
						if ( ! empty( $value ) ) {
							$bfi_args[ $key ] = $value;
							$bfi_args['crop'] = true;
						}
					}
				}
				if ( is_array( $logo_sticky_hw ) ) {
					foreach ( $logo_sticky_hw as $key => $value ) {
						if ( ! empty( $value ) ) {
							$bfi_args_sticky[ $key ] = $value;
							$bfi_args_sticky['crop'] = true;
						}
					}
				}				
				if ( is_array( $logo_mobile_hw ) ) {
					foreach ( $logo_mobile_hw as $key => $value ) {
						if ( ! empty( $value ) ) {
							$bfi_args_mobile[ $key ] = $value;
							$bfi_args_mobile['crop'] = true;
						}
					}
				}


				$logo_lr_spacing = $logo_tb_spacing = '';
				if ( is_array( $logo_m ) ) {
					$logo_lr_spacing = $this->aasana_theme_funcs->cws_print_css_keys($logo_m, 'margin-', 'px');
					$logo_tb_spacing = $this->aasana_theme_funcs->cws_print_css_keys($logo_m, 'padding-', 'px');
				}
				$logo_src = $this->aasana_theme_funcs->cws_print_img_html($logo, $bfi_args, $main_logo_height);



				$img_result = '';
				if(!empty($logo_src)){
					$img_result .= '<img '. $logo_src .' '.$img_mrg.' />';		
				}

				if(!empty($logo['src'])){
					$file_parts = pathinfo($logo['src']);
					if($file_parts['extension'] == 'svg'){
						$img_result = $this->aasana_theme_funcs->cws_print_svg_html($logo, $bfi_args, $main_logo_height);
					}			
				}

				$logo_sticky = $this->aasana_theme_funcs->cws_get_meta_option( 'logo_sticky' );

				if ( !empty( $logo_sticky['src'] ) ) {
					$logo_sticky_src = $this->aasana_theme_funcs->cws_print_img_html($logo_sticky['id'], (!empty($bfi_args_sticky) ? $bfi_args_sticky : null));
				}

				$logo_mobile = $this->aasana_theme_funcs->cws_get_meta_option( 'logo_mobile' );
				if ( ! empty( $logo_mobile['src'] ) ) {
					$logo_mobile_src = $this->aasana_theme_funcs->cws_print_img_html($logo_mobile['id'], (!empty($bfi_args_mobile) ? $bfi_args_mobile : null));
				}

				$rety = home_url();
				$img_mrg = ! empty( $logo_lr_spacing ) ? "style='".esc_attr( $logo_lr_spacing )."'" : '';

				$logo_cont = '
				</ul></div>
					<div class="header_logo_part '.($is_logo_with_site_name ? 'logo_with_text ' : '').'menu-center-part'. $logo_border .'">
						<a class="logo" href="'.$rety.'">';
						if(!empty($logo_sticky_src)){
							$file_parts_sticky = pathinfo($logo['src']);
							if($file_parts_sticky['extension'] != 'svg'){
								$logo_cont .= ($logo_sticky_src ?  '<img '.$logo_sticky_src." class='logo_sticky' />" : '');
							}else{
								$logo_cont .= "<span class='logo_mobile'>";
								$logo_cont .= $this->aasana_theme_funcs->cws_print_svg_html($logo_sticky, $bfi_args);
								$logo_cont .= "</span>";
							}
						}						

						if(!empty($logo_mobile_src)){
							$file_parts_mobile = pathinfo($logo['src']);
							if($file_parts_mobile['extension'] != 'svg'){
								$logo_cont .= ($logo_mobile_src ?  '<img '.$logo_mobile_src." class='logo_mobile' />" : '');
							}else{
								$logo_cont .= "<span class='logo_mobile'>";
								$logo_cont .= $this->aasana_theme_funcs->cws_print_svg_html($logo_mobile, $bfi_args);
								$logo_cont .= "</span>";
							}
						}

						$logo_cont .= ($logo_mobile_src ?  '<img '.$logo_mobile_src." class='logo_mobile' />" : '');
						$logo_cont .= $img_result.($is_logo_with_site_name ? '<h1 class="header_site_title">'.esc_html(get_bloginfo( "name" )).'</h1>' : '').'
						</a>
					</div>
				<div class="menu-right-part"><ul class="main-menu">';
			} else {
				$logo_cont = '
				</ul></div>
					<div class="header_logo_part '.($is_logo_with_site_name ? 'logo_with_text ' : '').'menu-center-part'. $logo_border .'">
						<h1 class="header_site_title">'.esc_html(get_bloginfo( 'name' )).'</h1>
					</div>
				<div class="menu-right-part"><ul class="main-menu">';
			}
		}
		return $logo_cont;
	}

	function site_name_ini( $indent, $item ) {
		$logo_position = $this->aasana_theme_funcs->cws_get_meta_option( 'logo-position' );
		if ( $indent == 0 && $logo_position == 'center' ) {
			ob_start();
			?>
			</ul></div>
				<div class="header_logo_part site_name menu-center-part" <?php echo isset( $logo_position ) && !empty( $logo_position ) && $logo_position == 'center' && ! empty( $logo_tb_spacing ) ? " style='".esc_attr($logo_tb_spacing)."'" : ''; ?>>
					<a <?php echo ( ! empty( $logo_lr_spacing ) ? " style='".esc_attr($logo_lr_spacing)."'" : '') ?> class="logo" href="<?php echo esc_url( home_url() ); ?>" >
						<h1 class='header_site_title'><?php echo get_bloginfo( 'name' ); ?></h1>
					</a>
							</div>
					<div class="menu-right-part"><ul class="main-menu">
						<?php
						$site_name_cont = ob_get_clean();
		} else {
			$site_name_cont = '';
		}
		return $site_name_cont;
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . sanitize_html_class( $item->ID );

		if ($item->menu_item_parent=="0") {
			$this->elements_counter += 1;
			if ($this->elements_counter>$this->elements/2){
				array_push($classes,'right');
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . $class_names  . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. sanitize_html_class( $item->ID ), $item, $args );
		$id = $id ? ' id="' . $id . '"' : '';

		// logo in cont init;
		if ( $item->menu_item_parent == '0' && $this->elements_counter == floor(($this->elements / 2)+1) ) {
			$logo_container = $this->logo_ini( $indent, $item );
		} else {
			$logo_container = '';
		}

		// Site name in cont init;
		if ($this->site_name_title){
			if ( $item->menu_item_parent == '0' && $this->elements_counter == floor(($this->elements / 2)+1) ) {
				$site_name_container = $this->site_name_ini( $indent, $item );
			} else {
				$site_name_container = '';
			}
		}

		$output .= $indent . (!empty($search_and_woo_icon_start) ? $search_and_woo_icon_start : '' ) . $logo_container .(!empty($site_name_container) ? $site_name_container : ''). '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )	 ? $item->target	 : '';
		$atts['rel']	= ! empty( $item->xfn )		? $item->xfn		: '';
		$atts['href']   = ! empty( $item->url )		? $item->url		: '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output = !empty($args->before) ? $args->before : '';
		$item_output .= '<a'. $attributes .'>';

		$item_output .= ( !empty($args->link_before) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( !empty($args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';
		$item_output .= ( !empty($args->after) ? $args->after : '' );

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * @see Walker::end_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */


	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n".(!empty($search_and_woo_icon_end) ? $search_and_woo_icon_end : '');
	}
}

/****************** /WALKER *********************/

/****************** MOBILE WALKER *********************/
class Aasana_Walker_Nav_Mobile_Menu extends Walker {
	private $elements;
	private $elements_counter = 0;
	private $test;

	function __construct() {
		$this->test = 'mobile';
	}

	function walk ($items, $depth, ...$args) {
		$this->elements = $this->get_number_of_root_elements($items);
		return parent::walk($items, $depth);
	}

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">";
		$output .= "\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . sanitize_html_class( $item->ID );

		if ($item->menu_item_parent=="0") {
			$this->elements_counter += 1;
			if ($this->elements_counter>$this->elements/2){
				array_push($classes,'right');
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . $class_names  . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. sanitize_html_class( $item->ID ), $item, $args );
		$id = $id ? ' id="' . $id . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )	 ? $item->target	 : '';
		$atts['rel']	= ! empty( $item->xfn )		? $item->xfn		: '';
		$atts['href']   = ! empty( $item->url )		? $item->url		: '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = !empty($args->before) ? $args->before : '';
		$item_output .= '<span class="menu_row"><a'. $attributes .'>';

		$item_output .= ( !empty($args->link_before) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( !empty($args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';

		if (is_array($item->classes)){
			if ( in_array( 'menu-item-has-children', $item->classes ) ){
				$item_output .= "<span class='button_open'></span>";
			}
		}

		$item_output .= '</span>';
		$item_output .= ( !empty($args->after) ? $args->after : '' );

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
/****************** /MOBILE WALKER *********************/

/****************** TOPBAR WALKER *********************/
class Aasana_Walker_Nav_Topbar_Menu extends Walker {
	private $elements;
	private $elements_counter = 0;
	private $test;

	function __construct($e = null) {
		$this->test = !empty($e) ? $e : "topbar";
	}

	function walk ($items, $depth, ...$args) {
		$this->elements = $this->get_number_of_root_elements($items);
		return parent::walk($items, $depth);
	}

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">";
		$output .= "\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . sanitize_html_class( $item->ID );

		if ($item->menu_item_parent=="0") {
			$this->elements_counter += 1;
			if ($this->elements_counter>$this->elements/2){
				array_push($classes,'right');
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . $class_names  . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'top-bar-menu-item-'. sanitize_html_class( $item->ID ), $item, $args );
		$id = $id ? ' id="' . $id . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )	 ? $item->target	 : '';
		$atts['rel']	= ! empty( $item->xfn )		? $item->xfn		: '';
		$atts['href']   = ! empty( $item->url )		? $item->url		: '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = !empty($args->before) ? $args->before : '';
		$item_output .= '<a'. $attributes .'>';

		$item_output .= ( !empty($args->link_before) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( !empty($args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';

		if ( in_array( 'menu-item-has-children', $item->classes ) ){
			$item_output .= "<span class='button_open'></span>";
		}
		$item_output .= ( !empty($args->after) ? $args->after : '' );

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

/****************** /TOPBAR WALKER *********************/

/* Comments */
class AASANA_Walker_Comment extends Walker_Comment {
	// init classwide variables
	var $tree_type = 'comment';
	var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );
	function __construct() { ?>
		<div class="comment_list">
	<?php }

	/** START_LVL
	 * Starts the list before the CHILD elements are added. Unlike most of the walkers,
	 * the start_lvl function means the start of a nested comment. It applies to the first
	 * new level under the comments that are not replies. Also, it appear that, by default,
	 * WordPress just echos the walk instead of passing it to &$output properly. Go figure.  */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1; ?>
		<div class="comments_children">
	<?php }

	/** END_LVL
	 * Ends the children list of after the elements are added. */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$GLOBALS['comment_depth'] = $depth + 1; ?>
		</div><!-- /.children -->

	<?php }

	/** START_EL */
	function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
		$depth++;
		global $aasana_theme_funcs;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment'] = $comment;
		$parent_class = ( empty( $args['has_children'] ) ? '' : 'parent' );
		$old_version = 0;
		?>

		<div <?php comment_class( $parent_class ); ?> id="comment-<?php comment_ID() ?>">
			<div id="comment-body-<?php comment_ID() ?>" class="comment-body clearfix">

				<div class="avatar_section">
					<?php 
						if($args['avatar_size'] != 0){
							echo ( get_avatar( $comment, $args['avatar_size'] ));
						}  ?>

				</div>
				<div class="comment_info_section">
					<div class="comment_info_header">
							<?php $reply_args = array(
								'reply_text' => " &nbsp;" . esc_html__( 'Reply', 'aasana' ),
								'depth' => $depth,
								'max_depth' => $args['max_depth']
							);

						echo "<div class='button-content reply'>";
						comment_reply_link( array_merge( $args, $reply_args ) );
						echo '</div>';
						?>
						<div class="comment-meta comment-meta-data">
							<cite class="fn n author-name"><?php echo get_comment_author_link(); ?></cite>
							<p class="comment_info">
								<span class="comment_date"><?php
									echo "<span class='date'>";
										comment_date();
									echo "</span>";
									echo esc_html__( ' at', 'aasana' );
									echo " <span class='time'>";
										comment_time();
									echo "</span>";
									?>
								</span>
								<?php edit_comment_link( '(Edit)' ); ?>
							</p>
						</div><!-- /.comment-meta -->


					</div>

					<div id="comment-content-<?php comment_ID(); ?>" class="comment-content">
						<?php if( !$comment->comment_approved ) : ?>
						<em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'aasana'); ?></em>
						<?php else: comment_text(); ?>
						<?php endif; ?>
					</div><!-- /.comment-content -->
				</div>
			</div><!-- /.comment-body -->

	<?php }

	function end_el(&$output, $comment, $depth = 0, $args = array() ) { ?>

		</div><!-- /#comment-' . get_comment_ID() . ' -->

	<?php }

	/** DESTRUCTOR
	 * I just using this since we needed to use the constructor to reach the top
	 * of the comments list, just seems to balance out :) */
	function __destruct() { ?>

	</div><!-- /#comment-list -->

	<?php }
}
/* \Comments */

// Remove X-Powered-By header
function remove_x_powered_by_header() {
    header_remove('X-Powered-By');
}
add_action('send_headers', 'remove_x_powered_by_header');

require_once get_template_directory() . '/inc/lh-home-refresh.php';
require_once get_template_directory() . '/inc/lh-footer.php';
require_once get_template_directory() . '/inc/lh-header-menu.php';

?>