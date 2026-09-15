<?php

class Aasana_WooExt{

	public $def_args;
	public $args;
	public $def_img_sizes;
	public $widget_args;

	public function __construct ( $args = array() ){
		$this->args = wp_parse_args( $args, $this->def_args );
		add_theme_support( 'woocommerce' );	// Declare Woo Support
		add_action( 'activate_woocommerce/woocommerce.php', array( $this, 'on_woo_activation' ), 10 );
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$this->def_args = array(
				'shop_catalog_image_size' 		=> array(),
				'shop_single_image_size'		=> array(),
				'shop_thumbnail_image_size'		=> array(),
				'shop_thumbnail_image_spacings'	=> array(),
				'shop_single_image_spacings'	=> array()
			);
			add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
			add_action( 'woocommerce_init', array( $this, 'woo_init' ) );
			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
			add_filter( 'woocommerce_show_page_title', '__return_false' );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
			add_action( 'wp_enqueue_scripts',  array( $this, 'wp_enqueue_woocommerce_style' )  );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 11 );
			add_action( 'body_font_hook',  array( $this, 'body_font_styles' ) );
			add_action( 'header_font_hook',  array( $this, 'header_font_styles' ) );
			if ( in_array( 'woocommerce-grid-list-toggle/woocommerce-grid-list-toggle.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$this->gridlist_init();
			}
		}	
	}
	public function on_woo_activation (){
		/* set product images dimensions */
		update_option( 'shop_catalog_image_size', $this->args['shop_catalog_image_size'] ); 
		update_option( 'shop_single_image_size', $this->args['shop_single_image_size'] ); 
		update_option( 'shop_thumbnail_image_size', $this->args['shop_thumbnail_image_size'] ); 
		/* set product images dimensions */
	}
	public function after_switch_theme (){
		/* set product images dimensions */
		update_option( 'shop_catalog_image_size', $this->args['shop_catalog_image_size'] ); 
		update_option( 'shop_single_image_size', $this->args['shop_single_image_size'] ); 
		update_option( 'shop_thumbnail_image_size', $this->args['shop_thumbnail_image_size'] ); 
		/* set product images dimensions */
	}
	public function woo_init (){
		/* loop */
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10, 0 ); 
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5, 0 ); 
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10, 0 );

		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'shop_loop_item_title_price_rating_button_wrapper_open' ), 8 );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'shop_loop_item_title_price_rating_wrapper_open' ), 9 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'after_shop_loop_item_title_wrapper_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'woocommerce_template_loop_rating_reviews_open'), 4 );
		add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'woocommerce_template_loop_rating_reviews'), 6 );
		add_action( 'woocommerce_after_shop_loop_item_title', array($this, 'woocommerce_template_loop_rating_reviews_close'), 7 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'after_shop_loop_item_title_wrapper_close' ), 20 );
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'shop_loop_item_title_price_rating_wrapper_close' ), 21 );
		add_action( 'woocommerce_review_before', array( $this, 'woocommerce_review_display_gravatar_before' ), 9 );
		add_action( 'woocommerce_review_before', array( $this, 'woocommerce_review_display_gravatar_after' ), 11 );
		// remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item_wrapper_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item_description_wrapper_open' ), 2 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item_description_wrapper_close' ), 9 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item_wrapper_close' ), 20 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'shop_loop_item_title_price_rating_button_wrapper_close' ), 21 );
		add_action( 'woocommerce_single_product_summary', array( $this,'woocommerce_template_single_excerpt_divider_open'), 19 );
		add_action( 'woocommerce_single_product_summary', array( $this,'woocommerce_template_single_excerpt_divider_close'), 21 );
		add_action( 'woocommerce_review_order_before_payment', array( $this,'woocommerce_review_order_before_payment_open'), 1 );
		add_action( 'woocommerce_review_order_after_payment', array( $this,'woocommerce_review_order_before_payment_close'), 1 );
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'shop_loop_item_content_wrapper_open' ), 1 );
		add_action( 'woocommerce_before_subcategory', array( $this, 'shop_loop_item_content_wrapper_open' ), 1 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'shop_loop_item_content_wrapper_close' ), 28 );
		add_action( 'woocommerce_after_subcategory', array( $this, 'shop_loop_item_content_wrapper_close' ), 28 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'shop_loop_item_divider_wrapper_open' ), 29 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'shop_loop_item_divider_wrapper_close' ), 31 );
		// add_action( 'woocommerce_after_shop_loop_item', array( $this, 'divider' ), 30, 0 );
		add_action( 'woocommerce_after_subcategory', array( $this, 'shop_loop_item_divider_wrapper_open' ), 29 );
		add_action( 'woocommerce_after_subcategory', array( $this, 'shop_loop_item_divider_wrapper_close' ), 31 );
		// add_action( 'woocommerce_after_subcategory', array( $this, 'divider' ), 30, 0 );
		add_filter( 'woocommerce_layered_nav_link', array( $this, 'custom_woocommerce_layered_nav_get_color_filter' ), 30, 1 );
		add_filter( 'woocommerce_layered_nav_count', array( $this, 'custom_woocommerce_layered_nav_set_color_filter' ), 30, 1 );
		add_filter( 'loop_shop_per_page', array( $this, 'loop_products_per_page' ), 20 );	
		/* \loop */

		/* single */
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10, 0 );

		// Carousel on product thumbnails (if many thumbnails) [!]
		// add_filter( 'aasana_woo_single_product_thumbnail_section_classes', array( $this, 'product_thumbnails_carousel_init' ) );	
		// add_action( 'aasana_woo_single_before_product_thumbnails', array( $this, 'single_product_thumbnails_carousel_nav' ) );

		// add_action( 'woocommerce_single_product_summary', array( $this, 'divider' ), 101 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_product_divider_before_upsells' ), 14 );	
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_product_divider_before_related' ), 19 );
		add_action( 'woocommerce_cart_collaterals', array( $this, 'divider' ), 1 );	
		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );		
		/* single */
		/* widgets */
		add_action( 'woocommerce_before_mini_cart', array( $this, 'minicart_wrapper_open' ) );
		add_action( 'woocommerce_after_mini_cart', array( $this, 'minicart_wrapper_close' ) );
		add_action( 'wp_ajax_woocommerce_remove_from_cart', array( $this, 'ajax_remove_from_cart' ), 1000 );
		add_action( 'wp_ajax_nopriv_woocommerce_remove_from_cart', array( $this, 'ajax_remove_from_cart' ), 1000 );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'header_add_to_cart_fragment' ) );
		/* \widgets */
		$this->set_img_dims();
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'change_breadcrumb_delimiter' ) );
	}

	public function gridlist_init (){
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 40 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'remove_excess_gridlist_actions' ), 40 );	
		add_action( 'wp', array( $this, 'remove_excess_gridlist_actions' ), 30 );
	}

	public function set_img_dims (){
		global $pagenow;
	 	if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
			return;
		}
		if ( isset( $this->args['shop_catalog_image_size'] ) && !empty( $this->args['shop_catalog_image_size'] ) ){
			update_option( 'shop_catalog_image_size', $this->args['shop_catalog_image_size'] );		
		}
		if ( isset( $this->args['shop_single_image_size'] ) && !empty( $this->args['shop_single_image_size'] ) ){
			update_option( 'shop_single_image_size', $this->args['shop_single_image_size'] );		
		}
		if ( isset( $this->args['shop_thumbnail_image_size'] ) && !empty( $this->args['shop_thumbnail_image_size'] ) ){
			update_option( 'shop_thumbnail_image_size', $this->args['shop_thumbnail_image_size'] );		
		}
	}

	public function divider (){
		echo "<hr />";
	}

	public function custom_woocommerce_layered_nav_get_color_filter ($link) {
		preg_match('/[^=]\w+$/', $link, $match_color);
		$this->widget_args['color_link'] = $match_color[0];
		return $link;
	}		

	public function custom_woocommerce_layered_nav_set_color_filter ($span) {
		$span = str_replace('<span class="count">', '<span class="color-box" style="background:'.esc_attr($this->widget_args['color_link']).';"></span><span class="count">', $span);
		return $span;
	}	

	public function check_stock() {
		global $product;
		ob_start();
		if ( $product->is_in_stock() ) {
		    echo '<span class="stock-label stock-mark" >' . $product->get_stock_quantity() . __( ' in stock', 'aasana' ) . '</span>';
		} else {
		    echo '<span class="stock-label out-of-stock-mark" >' . __( 'not in stock', 'aasana' ) . '</span>';
		}
		return ob_get_clean();
	}	

	static function get_wc_placeholder_img_src (){
		$image_link = wc_placeholder_img_src();
		$has_ext = preg_match( "#\.[^(\.)]*$#", $image_link, $matches );
		if ( $has_ext ){
			$ext = $has_ext ? $matches[0] : "";
			$wc_placeholder_img_name = "wc_placeholder_img";
			$wp_upload_dir = wp_upload_dir();
			$wp_upload_base_dir = isset( $wp_upload_dir['basedir'] ) ? $wp_upload_dir['basedir'] : "";
			$woo_upload_dir = trailingslashit( $wp_upload_base_dir ) . "woocommerce_uploads";
			$wc_placeholder_img_src = trailingslashit( $woo_upload_dir ) . "{$wc_placeholder_img_name}{$ext}";
			if ( !file_exists( $wc_placeholder_img_src ) ){
				$image_editor = wp_get_image_editor( $image_link );
				if ( ! is_wp_error( $image_editor ) ) {
					$image_editor->save( $wc_placeholder_img_src );
					return $wc_placeholder_img_src;
				}
			}
			else{
				return $wc_placeholder_img_src;
			}
		}
		return false;
	}

	public function change_breadcrumb_delimiter( $defaults ) {
		$defaults['delimiter'] = ' >> ';
		return $defaults;
	}

	/**/
	/* STYLES */
	/**/

	// declare woocomerece custom theme stylesheets
	public function wp_enqueue_woocommerce_style() {
		wp_register_style( 'woocommerce', get_template_directory_uri() . '/woocommerce/css/woocommerce.css' );
		if ( class_exists( 'woocommerce' ) ) {
			wp_enqueue_style( 'woocommerce' );
		}
	}

	public function enqueue_style() {
			$is_rtl = is_rtl();
			if ( in_array( 'woocommerce-grid-list-toggle/woocommerce-grid-list-toggle.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {		
				wp_register_style( 'woocommerce_gridlist', AASANA_URI . '/woocommerce/css/woocommerce_gridlist.css', array( 'grid-list-layout', 'grid-list-button' ) );	
				wp_enqueue_style( 'woocommerce_gridlist' );
			}
			if ( $is_rtl ){
				wp_register_style( 'woocommerce-rtl', AASANA_URI . '/woocommerce/css/woocommerce-rtl.css', array( 'main', 'aasana' ) );
				wp_enqueue_style( 'woocommerce-rtl' );
				if ( in_array( 'woocommerce-grid-list-toggle/woocommerce-grid-list-toggle.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {		
					wp_register_style( 'woocommerce_gridlist-rtl', AASANA_URI . '/woocommerce/css/woocommerce_gridlist-rtl.css', array( 'grid-list-layout', 'grid-list-button', 'woocommerce_gridlist' ) );	
					wp_enqueue_style( 'woocommerce_gridlist-rtl' );
				}				
			}
			$this->custom_styles();
	}
	public function custom_styles(){
		$product_thumb_dims = get_option( 'shop_single_image_size' );
		$product_thumb_width = isset( $product_thumb_dims['width'] ) ? $product_thumb_dims['width'] : $this->args['shop_single_image_size']['width'];

		ob_start();
		echo "
		.woo_product_post_media.post_single_post_media > .post_media_wrapper{
			width: {$product_thumb_width}px;
		}";
		if ( isset( $this->args['shop_thumbnail_image_spacings'] ) && !empty( $this->args['shop_thumbnail_image_spacings'] ) ){
			echo ".woo_product_post_thumbnail.post_single_post_thumbnail{";
			foreach ( $this->args['shop_thumbnail_image_spacings'] as $key => $value) {
				echo "padding-{$key}: {$value}px;";		
			}
			echo "}";
			echo ".woo_product_post_media.post_single_post_media .thumbnails{";
			foreach ( $this->args['shop_thumbnail_image_spacings'] as $key => $value) {
				echo "margin-{$key}: -{$value}px;";		
			}
			echo "}";
		}
		if ( isset( $this->args['shop_single_image_spacings'] ) && !empty( $this->args['shop_single_image_spacings'] ) ){
			echo ".woo_product_post_media_wrapper.post_single_post_media_wrapper > .pic:not(:only-child){";
			foreach ( $this->args['shop_single_image_spacings'] as $key => $value) {
				echo "margin-{$key}: {$value}px;";		
			}
			echo "}";
		}
		$custom_styles = ob_get_clean();
		if ( !empty( $custom_styles ) ){
			wp_add_inline_style( 'woocommerce', $custom_styles );
		}	
	}
	public function body_font_styles (){
		global $aasana_theme_funcs;
		if($aasana_theme_funcs){
			$font_options = $aasana_theme_funcs->cws_get_option('body-font');
		}else{
			$font_options = array(
			    'font-family' => 'PT Sans',
			    'font-weight' => array('regular','italic','700','700italic'),
			    'font-sub' => array('latin'),
			    'font-type' => '',
			    'color' => '#707273',
			    'font-size' => '18px',
			    'line-height' => '35px',
			);
		}
		

		$font_family = $font_options['font-family'];
		$font_size = $font_options['font-size'];
		$line_height = $font_options['line-height'];
		$font_color = $font_options['color'];
		
		if ( class_exists( 'woocommerce' ) ) {
			ob_start();
			if ( !empty( $font_size ) ){
				echo ".widget .woocommerce-product-search .screen-reader-text:before,
				.woocommerce .cart_totals h2,
				.woocommerce-checkout h3
				{
					font-size: $font_size;
				}";
			}
			if ( !empty( $font_color ) ){
				echo "#top_panel_woo_minicart,
					.woocommerce .button,
					.shop_table.cart .coupon .button:hover,
					.shop_table.cart input[name=\"update_cart\"]:hover,
					.woocommerce .checkout-button:hover,
					.checkout_coupon input[name=\"apply_coupon\"]:hover,
					#top_panel_woo_minicart .buttons .button:hover,
					#top_panel_woo_minicart .buttons .button.checkout:hover,
					h3.woo_product_post_title a,
					.woocommerce .cart_totals h2,
					.woocommerce-checkout h3
					{
					color: $font_color;
					}
				";
			}
			if ( !empty( $font_family ) ){
				echo "
					.tipr_content,
					.woocommerce .cart_totals h2,
					.woocommerce-checkout h3
					{
						font-family: $font_family;
					}
				";
			}

			if ( !empty( $line_height ) ){
				echo "
					.woocommerce .cart_totals h2,
					.woocommerce-checkout h3
					{
						line-height: $line_height;
					}
				";
			}			

			$styles = ob_get_clean();
			echo sprintf("%s", $styles);
		}
	}
	public function header_font_styles (){
		global $aasana_theme_funcs;
		if($aasana_theme_funcs){
			$font_options = $aasana_theme_funcs->cws_get_option( 'header-font' );
		}else{
			$font_options = array(
    			'font-family' => 'Poppins',
			    'font-weight' => array('300','regular','500','600','700'),
			    'font-sub' => array('latin'),
			    'font-type' => '',
			    'color' => '#7b6cd5',
			    'font-size' => '48px',
			    'line-height' => '36px',
			);	
		}
		
		$font_family = $font_options['font-family'];
		$font_size = $font_options['font-size'];
		$line_height = $font_options['line-height'];
		$font_color = $font_options['color'];

		if ( class_exists( 'woocommerce' ) ) {
			ob_start();
			if ( !empty( $font_size ) ){
				echo "";
			}
			if ( !empty( $font_family ) ){
				echo "ul.products.list li.product .woo_product_post_title.posts_grid_post_title,
				{
					font-family: $font_family;
				}";
			}
			if ( !empty( $font_color ) ){
				echo "#top_panel_woo_minicart.widget ul,
						ul.products.list li.product .woo_product_post_title.posts_grid_post_title{
					color: $font_color;
				}";
			}
			$styles = ob_get_clean();
			echo sprintf("%s", $styles);
		}
	}
	/**/
	/* \STYLES */
	/**/

	/**/
	/* SCRIPTS */
	/**/
	public function enqueue_script() {
		wp_register_script( 'aasana_woo', AASANA_URI . '/woocommerce/js/woocommerce.js' );
		if ( class_exists( 'woocommerce' ) ) {
			wp_enqueue_script( 'aasana_woo' );
		}
	}
	/**/
	/* SCRIPTS */
	/**/

	/**/
	/* LOOP */
	/**/
	public function loop_products_per_page() {
		global $aasana_theme_funcs;
		if($aasana_theme_funcs){
			return (int) $aasana_theme_funcs->cws_get_option( 'woo_num_products' );
		}else{
			return 10;
		}
		
	}
	public function shop_loop_item_title_price_rating_button_wrapper_open (){
		echo "<div class='title_price_rating_button_wrapper'>";
	}	
	public function shop_loop_item_title_price_rating_wrapper_open (){
		echo "<div class='title_price_rating_wrapper'>";
	}
	public function after_shop_loop_item_title_wrapper_open (){
		echo "<div class='aasana_after_shop_loop_item_title_wrapper clearfix'>";
	}
	public function woocommerce_template_loop_rating_reviews_open (){
		echo "<div class='rating_reviews'>";
	}
	public function woocommerce_template_loop_rating_reviews (){
		global $product;
		$review_count = $product->get_review_count();
		$permalink = esc_url( get_the_permalink() );	
		if ($review_count > 0){
			ob_start();
			echo "<span class='woocommerce-reviews-count'><span class='rating_reviews_divider'>/</span> ";
				?>
				<a href="<?php echo esc_url($permalink);?>#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s Review', '%s Reviews', $review_count, 'aasana' ), '<span class="count">' . $review_count . '</span>' ); ?></a>
				<span class='rating_reviews_divider'>/</span>
				<?php
				echo sprintf("%s", $this->check_stock());
				echo "</span>";
			echo ob_get_clean();
		} else {
			echo "<span class='woocommerce-reviews-count'><span class='rating_reviews_divider'>/</span> ";
			echo sprintf("%s", $this->check_stock()) ;
			echo "</span>";
		}
	}	
	public function woocommerce_template_loop_rating_reviews_close (){
		echo "</div>";
	}

	public function after_shop_loop_item_title_wrapper_close (){
		echo "</div>";
	}
	public function shop_loop_item_title_price_rating_wrapper_close (){
		echo "</div>";
	}

	public function woocommerce_review_display_gravatar_before (){
		echo "<div class='woocommerce_avatar_section'>";
	}
	public function woocommerce_review_display_gravatar_after (){
		echo "</div>";
	}	
	public function after_shop_loop_item_wrapper_open (){
		echo "<div class='button_wrapper aasana_after_shop_loop_item_wrapper clearfix'>";
	}
	public function after_shop_loop_item_description_wrapper_open (){
		echo "<div class='item-description'>";
	}
	public function after_shop_loop_item_description_wrapper_close (){
		echo "</div>";
	}
	public function after_shop_loop_item_wrapper_close (){
		echo "</div>";
	}
	public function shop_loop_item_title_price_rating_button_wrapper_close (){
		echo "</div>";
	}
	public function woocommerce_review_order_before_payment_open (){
		echo "<div class='payment_methods_wrapper'><h3>Payment methods</h3>";
	}
	public function woocommerce_review_order_before_payment_close (){
		echo "</div>";
	}
	// Description block single item
	public function woocommerce_template_single_excerpt_divider_open (){
		echo "<div class='item-description'>";
	}
	// Description block single item
	public function woocommerce_template_single_excerpt_divider_close (){
		echo "</div>";
	}	

	public function shop_loop_item_content_wrapper_open (){
		echo "<div class='aasana_shop_loop_item_content_wrapper'>";
	}
	public function shop_loop_item_content_wrapper_close (){
		echo "</div>";
	}
	public function shop_loop_item_divider_wrapper_open (){
		echo "<div class='aasana_shop_loop_item_divider_wrapper'>";
	}
	public function shop_loop_item_divider_wrapper_close (){
		echo "</div>";
	}
	public function remove_excess_gridlist_actions (){
		$actions = array(
			'woocommerce_after_shop_loop_item'	=> array( 'gridlist_buttonwrap_open', 'gridlist_buttonwrap_close', 'gridlist_hr' )
		);
		global $wp_filter;
		foreach ( $actions as $hook => $functions ) {
			if ( array_key_exists( $hook, $wp_filter ) ){
				$reg_functions = &$wp_filter[$hook];
				foreach ( $reg_functions as $reg_id => $reg_atts ){
					foreach ( $reg_atts as $reg_method_id => $reg_method_atts) {
						$reg_method = $reg_method_atts['function'];
						$reg_method_name = "";
						if ( is_array( $reg_method ) && isset( $reg_method[1] ) ){
							$reg_method_name = $reg_method[1];
						}else{
							$reg_method_name = $reg_method;
						}
						if ( in_array( $reg_method_name, $functions ) ){
							// unset( $wp_filter[$hook][$reg_id][$reg_method_id] );
							if ( empty( $wp_filter[$hook][$reg_id] ) ) unset( $wp_filter[$hook][$reg_id] );
							break 1;
						}
					}
				}
			}
		}
	}
	/**/
	/* \LOOP */
	/**/

	/**/
	/* SINGLE */
	/**/
	public function product_thumbnails_carousel_init ( $classes ){
		global $product;
		if ( !isset( $product ) ) return $classes;
		$attachment_ids = $product->get_gallery_image_ids();
		$product_thumb_dims = get_option( 'shop_single_image_size' );
		$product_thumb_width = isset( $product_thumb_dims['width'] ) ? $product_thumb_dims['width'] : $this->args['shop_single_image_size']['width'];
		$thumb_dims = get_option( 'shop_thumbnail_image_size' );
		$thumb_width = isset( $thumb_dims['width'] ) ? $thumb_dims['width'] : $this->args['shop_thumbnail_image_size']['width'];
		$visible_items = (int)floor( $product_thumb_width / $thumb_width );
		if ( count( $attachment_ids ) > $visible_items ){
			$classes[] = "carousel";
			$classes[] = "woo_product_thumbnail_carousel";
			$classes[] = "carousel_cols_$visible_items";
		}	
		return $classes;
	}
	public function single_product_thumbnails_carousel_nav (){
		global $product;
		if ( !isset( $product ) ) return $classes;
		$attachment_ids = $product->get_gallery_image_ids();
		$product_thumb_dims = get_option( 'shop_single_image_size' );
		$product_thumb_width = isset( $product_thumb_dims['width'] ) ? $product_thumb_dims['width'] : $this->args['shop_single_image_size']['width'];
		$thumb_dims = get_option( 'shop_thumbnail_image_size' );
		$thumb_width = isset( $thumb_dims['width'] ) ? $thumb_dims['width'] : $this->args['shop_thumbnail_image_size']['width'];
		$visible_items = (int)floor( $product_thumb_width / $thumb_width );
		if ( count( $attachment_ids ) > $visible_items ){
			?>
				<div class='carousel_nav prev'></div>
				<div class='carousel_nav next'></div>
			<?php
		}		
	}
	public function single_product_divider_before_upsells (){
		global $product;
		$posts_per_page = get_option( 'posts_per_page' );
		$upsells = $product->get_upsell_ids( $posts_per_page );
		echo sizeof( $upsells ) ? "<hr />" : "";
	}
	public function single_product_divider_before_related (){
		global $product;
		if ( !isset( $product ) ) return false;
		$posts_per_page = get_option( 'posts_per_page' );
		if(function_exists('wc_get_related_products')){
			$related = wc_get_related_products( $posts_per_page );
		}else{
			$related = $product->get_related( $posts_per_page );
		}
		
		echo sizeof( $related ) ? "<hr />" : "";
	}
	public function related_products_args( $args ) {
		global $product;
		global $aasana_theme_funcs;
		if($aasana_theme_funcs){
			$ppp = $aasana_theme_funcs->cws_get_option( 'woo_related_num_products' );
			$ppp_cols = $aasana_theme_funcs->cws_get_option( 'woo_columns' );
		}else{
			$ppp = 3;
			$ppp_cols = 3;
		}

		$args['posts_per_page'] = $ppp;
		$args['columns'] = $ppp_cols;
		return $args;
	}
	/**/
	/* \SINGLE */
	/**/

	/**/
	/* WIDGETS */
	/**/
	public function ajax_remove_from_cart() {
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
	public function header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		ob_start();
			?>
				<span class='woo_mini_count'><?php echo ( (WC()->cart->cart_contents_count > 0) ? esc_html( WC()->cart->cart_contents_count ) : '' ) ?></span>
			<?php
			$fragments['.woo_mini_count'] = ob_get_clean();

			ob_start();
			woocommerce_mini_cart();
			$fragments['.cws_woo_minicart_wrapper'] = ob_get_clean();

			return $fragments;
	}
	public function minicart_wrapper_open (){
		echo "<div class='woo_mini_cart'>";
	}
	public function minicart_wrapper_close (){
		echo "</div>";
	}
	/**/
	/* \WIDGETS */
	/**/
}

/**/
/* Config and enable extension */
/**/
$aasana_woo_args = array(
	'shop_catalog_image_size'		=> array(
		'width'	=> 270,
		'height'=> 270,
		'crop'	=> 1
	),
	'shop_single_image_size'		=> array(
		'width'	=> 370,
		'height'=> 370,
		'crop'	=> 1
	),
	'shop_thumbnail_image_size'		=> array(
		'width'	=> 116,
		'height'=> 116,
		'crop'	=> 1
	),
	'shop_thumbnail_image_spacings' => array(
		'left'	=> 6,
		'right'	=> 5,
		'top'	=> 11
	),
	'shop_single_image_spacings'	 => array(
		'bottom'=> 10
	),
);
global $aasana_woo_ext;
$aasana_woo_ext = new Aasana_WooExt ( $aasana_woo_args );

/**/
/* \Config and enable extension */
/**/

/**/
/* Overriden functions */
/**/
function woocommerce_template_loop_product_title(){
	$title = get_the_title();
	$permalink = get_the_permalink();
	echo !empty( $title ) ? "<h3 class='post_title woo_product_post_title posts_grid_post_title'><a href='$permalink'>$title</a></h3>" : "";
}
function woocommerce_template_loop_product_thumbnail (){
	$pid = get_the_id();
	$post_thumb_exists = has_post_thumbnail( $pid );
	$permalink = esc_url( get_the_permalink() );
	$img_url = "";
	if ( $post_thumb_exists ){
		$img_obj = wp_get_attachment_image_src( get_post_thumbnail_id( $pid ), 'full' );
		$img_url = isset( $img_obj[0] ) ? esc_url( $img_obj[0] ) : '';
	}
	else{
		$wc_placeholder_img_src = Aasana_WooExt::get_wc_placeholder_img_src();
		$img_url = $wc_placeholder_img_src ? $wc_placeholder_img_src : $img_url;
	}
	if ( empty( $img_url ) ) return false;
	$lightbox_en = get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false;	
	ob_start();
	if ( $lightbox_en ) {	
	echo "<div class='links'>";
		woocommerce_template_loop_add_to_cart();
		if($post_thumb_exists ){ echo ("<a href='$img_url' data-mode='top' data-tip='Quick view' class='tip fancy fa flaticon-search-icon-aasana'></a>"); } 
		echo "<a href='$permalink' data-mode='top' data-tip='Product page' class='tip fancy fa flaticon-right-arrow-icon-aasana'></a>";
	echo "</div>";
	} else {
		echo "<a href='$permalink' class='cws_overlay'></a>";
	}
	$lightbox = ob_get_clean();	
	$thumb_dims = get_option( 'shop_catalog_image_size' );
    $retina_thumb_url = '';
	$thumb_obj = cws_thumb( $img_url, $thumb_dims, false );
	$thumb_url = isset( $thumb_obj[0] ) ? esc_url( $thumb_obj[0] ) : "";
	ob_start();
	woocommerce_show_product_loop_sale_flash();
	$sale = ob_get_clean();
	$sale_banner = !empty( $sale ) ? "<div class='woo_banner_wrapper'><div class='woo_banner sale_bunner'><div class='woo_banner_text'>$sale</div></div></div>" : "";
	echo "<div class='post_media woo_product_post_media posts_grid_post_media'>";
		echo !empty( $sale_banner ) ? $sale_banner : "";
		echo "<div class='pic'>";
			echo "<a href='$permalink'>";
			$thumb_path_hdpi = !empty($thumb_obj[3]) ? " src='". esc_url( $thumb_obj[0] ) ."' data-at2x='" . esc_attr( $thumb_obj[3] ) ."'" : " src='". esc_url( $thumb_obj[0] ) . "' data-no-retina";
			echo "<img $thumb_path_hdpi alt = '" . get_post_meta( get_post_thumbnail_id( $pid ), '_wp_attachment_image_alt', true ) . "' />";
			
			echo "<div class='hover-effect'></div>";
			echo "</a>";
			echo sprintf("%s", $lightbox);
		echo "</div>";
	echo '</div>';
}
/**/
/* \Overriden functions */
/**/

// Reposition WooCommerce breadcrumb
function aasana_remove_woo_breadcrumb() {
	remove_action(
	'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_action(
	'woocommerce_before_main_content', 'aasana_remove_woo_breadcrumb'
);

?>
