<?php
	global $aasana_theme_funcs;
	$theme_color = esc_attr( $aasana_theme_funcs->cws_get_option( 'theme-main-one-color' ) );
	// Map Shortcode in Visual Composer
	vc_map( array(
		"name"				=> esc_html__( 'CWS Tips', 'aasana' ),
		"base"				=> "cws_sc_tips",
		'category'			=> "By CWS",
		// "icon"				=> "boc_spacing",
		"weight"			=> 80,
        'description' => __( 'Image Tips with tooltip', 'aasana' ),
        "params" => array(
        	array(
        		"type" => "attach_image",
        		"heading" => __("Image", "aasana"),
        		"param_name" => "image",
        		"value" => "",
        		"description" => __("Select image from media library.", "aasana")
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("Resize image to this width", "aasana"),
        		"param_name" => "width",
        		"value" => __("", 'aasana'),
        		"description" => __("You can resize image to this width, or keep it to blank to use the original image.", "aasana")
        		),
        	array(
        		"type" => "textarea_html",
        		"holder" => "div",
        		"heading" => __("Tooltip content, divide each one with [cwstips][/cwstips], please edit in text mode:", "aasana"),
        		"param_name" => "content",
        		"value" => __("[cwstips]
        			You have to wrap each tooltip block in <strong>cwstips</strong>.
        			[/cwstips]
        			[cwstips]
        			Hello tooltip 2, you can customize the icon color, link, arrow position, tooltip content etc in the backend.
        			[/cwstips]
        			[cwstips]
        			Hello tooltip 3
        			[/cwstips]
        			", "aasana"), "description" => __("Enter content for each block here. Divide each with [cwstips].", "aasana") ),
        	array(
        		"type" => "dropdown",
        		"heading" => __("Display which tooltip by default?", "aasana"),
        		"param_name" => "isdisplayall",
        		'value' => array(__("Display all of them when loaded", "aasana") => "on", __("Display a specified one (customize it below:)", "aasana") => "specify", __("Hide them all when loaded", "aasana") => "off"),
        		'std' => 'off',
        		"description" => __('Default all the tooltips are hidden. Though you can choose to open all of them or a single one when page is loaded.', 'aasana')
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("Display this tooltip when page loaded:", "aasana"),
        		"param_name" => "displayednum",
        		"value" => "1",
        		"dependency" => Array('element' => "isdisplayall", 'value' => array('specify')),
        		"description" => __("You can specify to display which tooltip in current image. Default is <strong>1</strong>, which stand for the number 1 tooltip will be opened when page is loaded.", "aasana")
        		),
        	array(
        		"type" => "dropdown",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Display the tips with?", "aasana"),
        		"param_name" => "icontype",
        		"value" => array(__("single dot", "aasana") => "dot", __("number", "aasana") => "number", __("Font Awesome icon", "aasana") => "icon"),
        		"description" => __("", "aasana")
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("Numbers start from", "aasana"),
        		"param_name" => "startnumber",
        		"value" => "1",
        		"dependency" => Array('element' => "icontype", 'value' => array('number')),
        		"description" => __("Default is start from 1, you can specify other value here, like 4.", "aasana")
        		),
        	array(
        		"type" => "exploded_textarea",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Font Awesome icon for each tips:", 'aasana'),
        		"param_name" => "fonticon",
        		"value" => __("fa-hand-o-right,fa-image,fa-coffee,fa-comment", 'aasana'),
        		"dependency" => Array('element' => "icontype", 'value' => array('icon')),
        		"description" => __("Put the <a href='http://fortawesome.github.io/Font-Awesome/icons/' target='_blank'>Font Awesome icon</a> here, divide with linebreak (Enter).", 'aasana')
        		),
        	array(
        		"type" => "exploded_textarea",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Each tips icon's position", 'aasana'),
        		"param_name" => "position",
        		"value" => __("25%|30%,35%|20%,45%|60%,75%|20%", 'aasana'),
        		"description" => __("Position of each icon in <strong>top|left</strong> format. Please update via dragging the tips icon in the Visual Composer Frontend editor. See a <a href='http://youtu.be/9j1XhIQw9JE' target='_blank'>Youtube video demo</a>.", 'aasana')
        		),
        	array(
        		"type" => "colorpicker",
        		"holder" => "div",
        		"class" => "",
        		"heading" => __("Global tips icon color", 'aasana'),
        		"param_name" => "iconbackground",
        		"value" => 'rgba(0,0,0,0.8)',
        		"description" => __("Global color for the tips icon. Or you can specify different color for each icon below.", 'aasana')
        		),
        	array(
        		"type" => "colorpicker",
        		"holder" => "div",
        		"class" => "",
        		"heading" => __("Hotspot circle dot (or Font Awesome icon) color", 'aasana'),
        		"param_name" => "circlecolor",
        		"value" => '#FFFFFF',
        		"description" => __("Color for the tips circle dot. Default is white.", 'aasana')
        		),
        	array(
        		"type" => "exploded_textarea",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Each tips icon's color", 'aasana'),
        		"param_name" => "color",
        		"value" => __("", 'aasana'),
        		"description" => __("Color for each icon, you can use the value like #663399 or the name of the color like blue here. Divide each with linebreaks (Enter).", 'aasana')
        		),
        	array(
        		"type" => "dropdown",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Display pulse animation for the tips icon?", "aasana"),
        		"param_name" => "ispulse",
        		"value" => array(__("yes", "aasana") => "yes", __("no", "aasana") => "no"),
        		"description" => __("", "aasana")
        		),
        	array(
        		"type" => "dropdown",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Select pulse border color", "aasana"),
        		"param_name" => "pulsecolor",
        		"value" => array(__("Default", "aasana") => "pulse-white", __("gray", "aasana") => "pulse-gray", __("red", "aasana") => "pulse-red", __("green", "aasana") => "pulse-green", __("yellow", "aasana") => "pulse-yellow", __("blue", "aasana") => "pulse-blue", __("purple", "aasana") => "pulse-purple"),
        		"dependency" => Array('element' => "ispulse", 'value' => array('yes')),
        		"std" => "pulse-white",
        		"description" => __("You can select the pulse border color here, default is white.", "aasana")
        		),
        	array(
        		"type" => "exploded_textarea",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Tooltip arrow position for each tips", 'aasana'),
        		"param_name" => "arrowposition",
        		"value" => __("", 'aasana'),
        		"description" => __("The arrow position for each tooltip, default is top. The available options are: <strong>top, right, bottom, left, top-right, top-left, bottom-right, bottom-left</strong>. Divide each with linebreaks (Enter)", 'aasana')
        		),

        	array(
        		"type" => "textfield",
        		"heading" => __("Hotspot icon opacity", "aasana"),
        		"param_name" => "opacity",
        		"value" => "1",
        		"description" => __("The opacity of each icon, default is 1", "aasana")
        		),
        	array(
        		"type" => "dropdown",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Tooltip style", "aasana"),
        		"param_name" => "tooltipstyle",
        		"value" => array(__("shadow", "aasana") => "shadow", __("light", "aasana") => "light", __("noir", "aasana") => "noir", __("punk", "aasana") => "punk"),
        		"description" => __("", "aasana")
        		),
        	array(
        		"type" => "dropdown",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Tooltip trigger when user", "aasana"),
        		"param_name" => "trigger",
        		"value" => array(__("hover", "aasana") => "hover", __("click", "aasana") => "click"),
        		"description" => __("Select how to trigger the tooltip.", "aasana")
        		),
        	array(
        		"type" => "dropdown",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Tooltip animation", "aasana"),
        		"param_name" => "tooltipanimation",
        		"value" => array(__("grow", "aasana") => "grow", __("fade", "aasana") => "fade", __("swing", "aasana") => "swing", __("slide", "aasana") => "slide", __("fall", "aasana") => "fall"),
        		"description" => __("Choose the animation for the tooltip.", "aasana")
        		),
        	array(
        		"type" => "exploded_textarea",
        		"holder" => "",
        		"class" => "aasana",
        		"heading" => __("Link for each tips icon", 'aasana'),
        		"param_name" => "links",
        		"value" => __("", 'aasana'),
        		"description" => __("Specify link for each icon, divide each with linebreaks (Enter).", 'aasana')
        		),
        	array(
        		"type" => "dropdown",
        		"heading" => __("How to open the link for the icon?", "aasana"),
        		"param_name" => "custom_links_target",
        		"description" => __('Select how to open the links', 'aasana'),
        		'value' => array(__("Same window", "aasana") => "_self", __("New window", "aasana") => "_blank")
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("maxWidth of the tooltip", "aasana"),
        		"param_name" => "maxwidth",
        		"value" => "240",
        		"description" => __("maxWidth for the tooltip, 0 is auto width, you can specify a value here, default is 240.", "aasana")
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("Container width", "aasana"),
        		"param_name" => "containerwidth",
        		"value" => "",
        		"description" => __("You can specify the container width here, default is 100%. You can try other value like 80%, it will be align center automatically.", "aasana")
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("Margin offset", "aasana"),
        		"param_name" => "marginoffset",
        		"value" => "",
        		"description" => __("The margin offset for the tips icon in small screen. For example <strong>-6px 0 0 -6px</strong> will move the icons upper left for 6px offset in small screen. Leave here to be blank if you do not want it.", "aasana")
        		),
        	array(
        		"type" => "textfield",
        		"heading" => __("Extra class name for the container", "aasana"),
        		"param_name" => "extra_class",
        		"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "aasana")
        		)

        	)
	));

	if ( class_exists( 'WPBakeryShortCode' ) ) {
	    class WPBakeryShortCode_CWS_Sc_Tips extends WPBakeryShortCode {
	    }
	}
?>