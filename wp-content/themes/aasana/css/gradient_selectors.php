<?php
	function get_gradient_selectors (){
		$selectors = "
			.main-nav-container .menu-item:hover,
			.main-nav-container .menu-item.current-menu-ancestor,
			.main-nav-container .menu-item.current-menu-item,
			.main-nav-container .sub-menu .menu-item:hover,
			.main-nav-container .sub-menu .menu-item.current-menu-ancestor,
			.main-nav-container .sub-menu .menu-item.current-menu-item,
			.site_header.with_background .main-nav-container .menu-item:hover,
			.site_header.with_background .main-nav-container .menu-item.current-menu-ancestor,
			.site_header.with_background .main-nav-container .menu-item.current-menu-item,
			.site_header.with_background .main-nav-container .sub-menu .menu-item:hover,
			.site_header.with_background .main-nav-container .sub-menu .menu-item.current-menu-ancestor,
			.site_header.with_background .main-nav-container .sub-menu .menu-item.current-menu-item,
			.news .post_info_box .date,
			.pic .hover-effect,
			.news .more-link:hover,
			.pagination .page_links .page-numbers.current,
			.pagination .page_links > span:not([class]),
			input[type='submit'],
			.cws-widget #wp-calendar tbody td#today:before,
			.cws-widget .tagcloud a:hover,
			.ce_accordion:not(.third_style) .accordion_content,
			.ce_accordion.second_style .accordion_section.active,
			.ce_toggle .accordion_title .accordion_icon,
			.ce_tabs .tab.active,
			.pricing_table_column .title_section,
			.comments-area .comment_list .comment-reply-link,
			.cws_milestone.alt,
			.cws_progress_bar .progress,
			.dropcap,
			.tp-caption.aasana-main-slider-layer a:before,
			#site_top_panel .cws_social_links .cws_social_link:hover,
			#site_top_panel #top_social_links_wrapper .cws_social_links.expanded .cws_social_link:hover,
			.copyrights_area .cws_social_links .cws_social_link:hover,
			.ourteam_item_wrapper .social_links a:hover,
			.cws_ourteam.single .social_links a:hover,
			.banner_404:before,
			.cws_img_frame:before,
			.gallery-icon a:before,
			.cws_tweet:before,
			.tweets_carousel_header .follow_us
		";
		$selectors = trim( $selectors );
		return $selectors;
	}
?>