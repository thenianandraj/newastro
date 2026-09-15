<?php
function cws_core_cwsfw_get_grouped_types() {
	return array('tab');
}

function cws_core_cwsfw_fillMbAttributes($meta, &$attr, $prefix = '') {
	foreach ($meta as $k => $v) {
		$entry = !empty($prefix) ? $prefix . "[$k]" : $k;
		//$attr_k = &$attr[$k];

		$attr_k = &cws_core_cwsfw_find_array_keys($attr, $entry);
		if ($attr_k) {
			switch ($attr_k['type']) {
				case 'text':
				case 'number':
				case 'textarea':
				case 'datetime':
				case 'gallery':
					$attr_k['value'] = htmlentities(stripslashes($v));
					break;
				case 'media':
					if (isset($attr_k['layout'])) {
						cws_core_cwsfw_fillMbAttributes($v, $attr_k['layout']);
					}
					$attr_k['value'] = $v;
					break;
				case 'radio':
					if (is_array($attr_k['value'])) {
						foreach ($attr_k['value'] as $key => $val) {
							if ($key == $v) {
								$attr_k['value'][$key][1] = true;
							} else {
								$attr_k['value'][$key][1] = false;
							}
						}
					}
					break;
				case 'checkbox':
					$atts = '';
					if (isset($attr_k['atts'])) {
						$atts = $attr_k['atts'];
					}
					if ('on' === $v || '1' == $v) {
						$atts .= ' checked';
						$attr_k['atts'] = $atts;
					} else {
						$attr_k['atts'] = str_replace('checked', '', $atts);
					}
					break;
				case 'group':
					if (!empty($v)) {
						$attr_k['value'] = $v;
					}
					break;
				case 'dimensions':
				case 'margins':
					foreach ($v as $key => $value) {
						if (isset($attr_k['value'][$key])) {
							$attr_k['value'][$key]['value'] = $value;
						}
					}
					break;
				case 'font':
					foreach ($v as $key => $value) {
						$attr_k['value'][$key] = $value;
					}
					break;
				case 'taxonomy':
				case 'post_type':
				case 'select':
					if (is_array($attr_k['source']) && !empty($v)) {
						foreach ($attr_k['source'] as $key => $value) {
							$attr_k['source'][$key][1] = false; // reset all
						}
						if (is_array($v)) {
							foreach ($v as $key => $value) {
								if ( !empty($value) && '!!!dummy!!!' !== $value ) {
									$attr_k['source'][$value][1] = true;
								}
							}
						} else {
							$attr_k['source'][$v][1] = true;
						}
					} else {
						if (is_string($v)) {
							$attr_k['source'] .= ' ' . $v;
						} else {
							$attr_k['source'] = null;
						}
					}
					break;
				case 'fields':
					cws_core_cwsfw_fillMbAttributes($v, $attr_k['layout'], $prefix);
					break;
				default:
					break;
			}
		}
	}
}

function &cws_core_cwsfw_find_array_keys(&$attr, $key) {
	$ret = null;
	$non_grouped = cws_core_cwsfw_get_grouped_types();
	if (isset($attr[$key]) && !in_array($attr[$key]['type'], $non_grouped)) {
		$ret = &$attr[$key];
	} else {
		foreach ($attr as $k=>&$value) {
			if (isset($value['layout'][$key])) {
				$ret = &$value['layout'][$key];
				break;
			}
		}
	}
	return $ret;
}

/* straighten up our array, filling the references */
function cws_core_cwsfw_build_array_keys(&$attr) {
	$ret = array();
	foreach ($attr as $section => &$value) {
		$first_element = reset($value['layout']);
		if ('tab' === $first_element['type']) {
			foreach ($value['layout'] as $tabs => &$val) {
				foreach ($val['layout'] as $k => &$v) {
					$ret[$k] = &$v;
				}
			}
		} else {
			foreach ($value['layout'] as $k => &$v) {
				$ret[$k] = &$v;
			}
		}
	}
	return $ret;
}

function cws_core_cwsfw_print_layout ($layout, $prefix, &$values = null) {
	$out = '';
	$isTabs = false;
	$isCustomizer = is_customize_preview();
	$tabs = array();
	$bIsWidget = '[' === substr($prefix, -1);
	$tabs_idx = 0;
	$current_wn_to = false;
	global $current_screen;
	if(isset($current_screen->id) && $current_screen->id == 'toplevel_page_cwsfw'){
		$current_wn_to = true;
	}
	foreach ($layout as $key => $v) {
		if (isset($v['customizer']) && !$v['customizer']['show'] && $isCustomizer) continue;
		if ($bIsWidget && empty($v)) continue;
		$row_classes = isset($v['rowclasses']) ? $v['rowclasses'] : 'row row_options ' . $key;
		$row_classes = isset($v['addrowclasses']) ? $row_classes . ' ' . $v['addrowclasses'] : $row_classes;
		if ('label' === $v['type']) {
		}

		$row_atts = isset($v['row_atts']) ? ' ' . $v['row_atts'] : '';

		$row_atts = $v['type'] === 'media' ? $row_atts . ' data-role="media"' : $row_atts;

		if (isset($values) && isset($v['value']) ) {
			$values[$key] = $v['value'];
		}

		if ($bIsWidget) {
			$a = strpos($key, '[');
			if (false !== $a) {
				$name = substr($key, 0, $a) . ']' . substr($key, $a, -1) . ']';
			} else {
				$name = $key . ']';
			}
		} else {
			$name = $key;
		}
		$salt = '';
		if ('module' !== $v['type'] && 'tab' !== $v['type']) {
			$out .= '<div class="' . $row_classes . '"' . $row_atts . '>';
			if (isset($v['label']) || isset($v['title'])) {
				if ('checkbox' === $v['type']) {
					$salt = '_' . time()*rand(1,1024);
				}
				$cws_print_title = isset($v['title']) ? $v['title'] : (isset($v['label']) ? $v['label'] : '' );
				$out .= '<label for="' . $prefix . $name . $salt . '">' . $cws_print_title . '</label>';
				if (isset($v['tooltip']) && is_array($v['tooltip']) ) {
					$out .= '<div class="cwsfw-qtip dashicons-before" title="' . $v['tooltip']['title'] . '" qt-content="'.$v['tooltip']['content'].'">';
					$out .= '</div>';
				}
			}
			$out .= "<div>";
		}
		if(!$current_wn_to){
			$value = isset($v['value']) && !is_array($v['value']) ? ' value="' . htmlspecialchars_decode($v['value']) . '"' : '';
		}else{
			$value = isset($v['value']) && !is_array($v['value']) ? ' value="' . $v['value'] . '"' : '';
		}
		
		$atts = isset($v['atts']) ? ' ' . $v['atts'] : '';
		switch ($v['type']) {
			case 'text':
			case 'number':
				$ph = isset($value['placeholder']) ? ' placeholder="' . $value['placeholder'] . '"' : '';
				if (isset($v['verification'])) {
					$atts .= ' data-verification="' . esc_attr( str_replace('"', '\'', json_encode($v['verification'])) ) . '"';
				}
				$out .= '<input type="'. $v['type'] .'" name="'. $prefix . $name .'"' . $value . $atts . $ph . '>';
				break;
			case 'info':
				$subtype = isset($v['subtype']) ? $v['subtype'] : 'info';
				$out .= '<div class="'. $subtype .'">';
				if (isset($v['icon']) && is_array($v['icon'])) {
					$out .= '<div class="info_icon">';
					switch ($v['icon'][0]) {
						case 'fa':
							$out .= "<i class='fa fa-2x fa-{$v['icon'][1]}'></i>";
							break;
					}
					$out .= '</div>';
				}
				$out .= '<div class="info_desc">';
				$out .= $v['value'];
				$out .= '</div>';
				$out .= '<div class="clear"></div>';
				$out .= '</div>';
				break;
			case 'checkbox':
				$value = ' value="1"';
				if (!empty($atts) && false !== strpos($atts, 'checked')) {
					$values[$key] = '1';
				} else {
					$values[$key] = '0';
				}
				$out .= '<input type="hidden" name="'. $prefix . $name .'" value="0">';
				$out .= '<input type="'. $v['type'] .'" name="'. $prefix . $name .'" id="' . $prefix . $name . $salt.'"' . $value . $atts . '>';
				$out .= '<label for="' . $prefix . $name . $salt.'">'. (isset($v['label']) ? $v['label'] : '') .'</label>';
				break;
			case 'radio':
				$radio_cols = isset($v['cols']) ? (int)$v['cols'] : 0;
				if (isset($v['subtype']) && 'images' === $v['subtype']) {
					$out .= '<ul class="cws_image_select">';
					foreach ($v['value'] as $k => $value) {
						$selected = '';
						if (isset($value[1]) && true === $value[1]) {
							$selected = ' checked';
							$values[$key] = $k;
						}
						$out .= '<li class="image_select' . $selected . '">';
						$out .= '<div class="cws_img_select_wrap">';
						$src = $value[3];
						if (substr($value[3], 0, 4) === '/img') {
							$src = CWSTO_PLUGIN_URL . $src;
						}
						$out .= '<img src="' . $src . '" alt="image"/>';
						$data_options = !empty($value[2]) ? ' data-options="' . $value[2] . '"' : '';
						$out .= '<input type="'. $v['type'] .'" name="'. $prefix. $name . '" value="' . $k . '" title="' . $k . '"' .  $data_options . $selected . '>' . $value[0] . '<br/>';
						$out .= '</div>';
						$out .= '</li>';
					}
					$out .= '<div class="clear"></div>';
					$out .= '</ul>';
				} else {
					$i = 0;
					$br = $radio_cols ? '' : '<br/>';
					foreach ($v['value'] as $k => $value) {
						$selected = '';
						if (isset($value[1]) && true === $value[1]) {
							$selected = ' checked';
							$values[$key] = $k;
						}
						$data_options = !empty($value[2]) ? ' data-options="' . $value[2] . '"' : '';
						$out .= '<input type="'. $v['type'] .'" name="'. $prefix. $name . '" value="' . $k . '" title="' . $k . '"' .  $data_options . $selected . '>' . $value[0] . $br;
						$i++;
						if ($radio_cols && $i === $radio_cols) {
							$out .= '<br/>';
							$i = 0;
						}
					}
				}
				break;
			case 'insertmedia':
				$out .= '<div class="cws_tmce_buttons">';
				$out .= 	'<a href="#" id="insert-media-button" class="button insert-media add_media" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a>';
				$out .= 	'<div class="cws_tmce_controls">';
				$out .= 	'<a href="#" id="cws-switch-text" class="button" data-editor="content" data-mode="tmce" title="Switch to Text">Switch to Text</a>';
				$out .= '</div></div>';
				break;
			case 'fields':
				$out .= '<div class="cwsfw_fields">';
				$values[$key] = array();
				$out .= cws_core_cwsfw_print_layout( $v['layout'], $prefix . $name . '[', $values[$key], true ); // here would be a template stored
				$out .= '</div>';
				break;
			case 'group':
				if (isset($v['value'])) {
					$out .= '<script type="text/javascript">';
					$out .= 'if(undefined===window[\'cws_groups\']){window[\'cws_groups\']={};}';
					$out .= 'window[\'cws_groups\'][\'' . $prefix .$key .(!empty($prefix) ? ']' : '').'\']=\'' . json_encode($v['value']) . '\';';
					// $out .= 'window[\'cws_groups\'][\'' . $key .'\']=\'' . json_encode($v['value']) . '\';';
					$out .= '</script>';
				}
				$out .= '<script class="cwsfe_group" style="display:none" data-key="'.$key.'" data-templ="group_template" type="text/html">';
				$out .= cws_core_cwsfw_print_layout( $v['layout'], $prefix . $name . '[%d][', $values ); //would be a template stored
				$out .= '</script>';
				$out .= '<ul class="groups"></ul>';
				if (isset($v['button_title'])) {
					$out .= '<button type="button" class="'.(isset($v['button_class']) ? $v['button_class'] : '').'" name="'.$key.'">'.(isset($v['button_icon']) ? '<i class="'. $v['button_icon'] .'"></i> ' : ''). $v['button_title'] .'</button>';
				}
				break;
			case 'font':
				global $gf;
				if (empty($gf)) {
					$gf = json_decode(file_get_contents(CWSTO_PLUGIN_DIR . '/gf.json'));
				}
				$out .= '<fieldset class="cwsfw_'. $v['type'] .'" id="'. $prefix . $name . '">';
				$out .= '<div class="cwsfw_gf_filters">';
				$out .= '<div class="cwsfw_filter_item">';
				$out .= '<label for="font-catalog">'. esc_html__('Font Catalog', 'cws-to') .'</label>';
				$out .= '<select class="font-catalog">';
				$out .= cws_core_cwsfw_print_gf_cat();
				$out .= '</select>';
				$out .= '</div>';
				$out .= '<div class="cwsfw_filter_item">';
				$out .= '<label for="font-subs">'. esc_html__('Font Subsets', 'cws-to') .'</label>';
				$out .= '<select class="font-subs">';
				$out .= cws_core_cwsfw_print_gf_subs();
				$out .= '</select>';
				$out .= '</div>';
				$out .= '<div class="clear"></div>';
				$out .= '</div>';

				$out .= '<div class="cwsfw_gf_props">';

				$out .= '<div class="props_item">';
				$out .= '<label for="font-family">'. esc_html__('Font Family', 'cws-to') .'</label>';
				$out .= '<select name="'. $prefix . $name .'[font-family]" class="font-family">';
				$out .= cws_core_cwsfw_print_gf($v['value']['font-family']);
				$out .= '</select>';
				$out .= '</div>';

				$out .= '<div class="props_item">';
				$out .= '<label for="font-weight">'. esc_html__('Font Weight', 'cws-to') .'</label>';
				$out .= '<select multiple name="'. $prefix . $name .'[font-weight][]" class="font-weight">';
				if (isset($v['value']['font-weight'])) {
					$font = $v['value']['font-family'];
					$var = $gf->{$font}->var;
					$var_a = explode(';', $var);
					$out .= cws_core_cwsfw_print_gf_weight($v['value']['font-weight'], $var_a);
				}
				$out .= '</select>';
				$out .= '</div>';

				$out .= '<div class="props_item">';
				$out .= '<label for="font-sub">'. esc_html__('Font Scripts', 'cws-to') .'</label>';
				$out .= '<select multiple name="'. $prefix . $name .'[font-sub][]" class="font-sub">';
				if (isset($v['value']['font-sub'])) {
					$font = $v['value']['font-family'];
					$var = $gf->{$font}->sub;
					$var_a = explode(';', $var);
					$out .= cws_core_cwsfw_print_gf_sub($v['value']['font-sub'], $var_a);
				}

				$out .= '</select>';
				$font_type = isset($v['value']['font-type']) ? $v['value']['font-type'] : '';
				$out .= '<input type=hidden name="'. $prefix . $name .'[font-type]" value="'.$font_type.'">';
				$out .= '</div>';
				$out .= '<div class="clear"></div>';

				if ($v['font-color']) {
					$out .= '<div class="props_item">';
					$out .= '<label for="color">'. esc_html__('Font Color', 'cws-to') .'</label>';
					$out .= '<input type=text name="'. $prefix . $name .'[color]" class="color" data-default-color="'.$v['value']['color'].'">';
					$out .= '</div>';
				}
				if ($v['font-size']) {
					$out .= '<div class="props_item">';
					$out .= '<label for="font-size">'. esc_html__('Font Size', 'cws-to') .'</label>';
					$out .= '<input type=text name="'. $prefix . $name .'[font-size]" class="font-size" value="'.$v['value']['font-size'].'">';
					$out .= '</div>';
				}
				if ($v['line-height']) {
					$out .= '<div class="props_item">';
					$out .= '<label for="line-height">'. esc_html__('Line height', 'cws-to') .'</label>';
					$out .= '<input type=text name="'. $prefix . $name .'[line-height]" class="line-height" value="'.$v['value']['line-height'].'">';
					$out .= '</div>';
				}
				$out .= '<div class="clear"></div>';
				$out .= '</div>'; // cwsfw_gf_props
				if (!$isCustomizer) {
					/* now preview */
					$out .= '<div class="preview">';
					$out .= '<div class="preview_text">';
					$preview_text = esc_html__('Quick brown fox jumps over the lazy dog', 'cws-to');
					$out .= '<p>' . $preview_text . '</p>';
					$out .= '</div>';
					$out .= '</div>';
				}
				$out .= '</fieldset>';
				break;
			case 'dimensions':
			case 'margins':
				$out .= '<fieldset class="cwsfw_'. $v['type'] .'">';
				foreach ($v['value'] as $k => $value) {
					$out .= '<input type="text" name="'. $prefix . $name .'['.$k.']" value="' . $value['value'] .'" placeholder="' . $value['placeholder'] . '"' . $atts . '>';
					$values[$key][$k] = $value['value'];
				}
				$out .= '</fieldset>';
				break;
			case 'tab':
				$isTabs = true;
				$tabs[$tabs_idx] = array(
					'tab' => $key,
					'title' => $v['title'],
					'active' => (isset($v['init']) && $v['init'] === 'open'),
					'icon' => isset($v['icon']) ? $v['icon'] : '');
				$tabs_idx++;
				$out .= '<div class="cws_form_tab' . (isset($v['init']) ?  ' ' . $v['init'] : ' closed' ). '" data-tabkey="'.$key.'">';
				$out .= cws_core_cwsfw_print_layout( $v['layout'], $prefix, $values );
				$out .= '</div>';
				break;
			case 'textarea':
				$out .= '<textarea name="'. $prefix . $name .'"' . $atts . '>' . (isset($v['value']) ? $v['value'] : '') . '</textarea>';
				break;
			case 'button':
				$out .= '<button type="button" name="'. $prefix . $name .'"' . $atts . '>' . (isset($v['btitle']) ? $v['btitle'] : '') . '</button>';
				break;
			case 'datetime_add':
				$out .= '<ul class="recurring_events" data-pattern="'. $prefix . $name .'" data-lang="'. esc_html__('From', 'cws-to') . '|' . esc_html__('till', 'cws-to') .'">';
				if (!empty($v['source'])) {
					$i = 0;
					foreach ($v['source'] as $dstart => $dend) {
						$out .= '<li class="recdate">'. esc_html__('From', 'cws-to') .' <span>'.$dstart.'</span> '.esc_html__('till', 'cws-to').' <span>'. $dend .'</span><div class="close"></div>';
						$out .= '<input type="hidden" name="'.$prefix.$key.'['.$i.'][s]" value="'.$dstart.'" />';
						$out .= '<input type="hidden" name="'.$prefix.$key.'['.$i.'][e]" value="'.$dend.'" />';
						$out .= '</li>';
						$i++;
					}
				}
				$datatype = 'datepicker;periodpicker;'.$key.'-end';
				$out .= '<input type="text" data-cws-type="'. $datatype .'" name="'. $key .'"' . $value . $atts . '>';
				$out .= '<div class="row '. $key .'-end">';
				$out .= '<input type="text" name="'. $key .'-end">';
				$out .= '<button type="button" name="'.$key.'">Add '. $v['title'] .'</button>';
				$out .= '</ul>';
				break;
			case 'datetime':
				if (isset($v['dtype'])) {
					list($dtype, $end) = $v['dtype'];
					$datatype = 'datepicker;' . $dtype .';'. $end;
					$out .= '<input type="text" data-cws-type="'. $datatype .'" name="'. $prefix . $name .'"' . $value . $atts . '>';
				}
				break;
			case 'map':
				$out .= '<div class="cws_maps" id="' . $key . '"></div>';
				break;
			case 'taxonomy':
				$taxonomy = isset($v['taxonomy']) ? $v['taxonomy'] : '';
				$ismul = (false !== strpos($atts, 'multiple'));
				$out .= '<select name="'. $prefix . $name . ($ismul ? '[]':''). '"' . $atts . '>';
				$out .= cws_core_cwsfw_print_taxonomy($taxonomy, $v['source']);
				$out .= '</select>';
				break;			
			case 'post_type':

				$taxonomy = isset($v['post_type']) ? $v['post_type'] : '';
				$ismul = (false !== strpos($atts, 'multiple'));
				$out .= '<select name="'. $prefix . $name . ($ismul ? '[]':''). '"' . $atts . '>';
				$out .= cws_core_cwsfw_print_post_type($taxonomy, $v['source']);
				$out .= '</select>';
				break;
			case 'input_group':
				$out .= '<fieldset class="' . substr($key, 2) . '">';
				$source = $v['source'];
				foreach ($source as $key => $value) {
					$out .= sprintf('<input type="%s" id="%s" name="%s" placeholder="%s">', $value[0], $key, $prefix.$key, $value[1]);
				}
				$out .= '</fieldset>';
				break;
			case 'select':
				if (false !== strpos($atts, 'multiple') ) {
					$name .= '[]';
					$out .= '<input type="hidden" name="'. $prefix . $name .'" value="!!!dummy!!!">'; // dummy
				}
				$out .= '<select name="'. $prefix . $name .'"' . $atts;
				$closed_tag = false;
				if (!empty($v['source'])) {
					$source = $v['source'];
					if (is_array($source)) {
						reset($source);
						$fk = key($source);
						if (!empty($source[$fk][2])) {
							$out .= ' data-options="select:options">';
							$closed_tag = true;
						}
					}
					if (!$closed_tag) {
						$out .= '>';
						$closed_tag = true;
					}
					if ( is_string($source) ) {
						if (strpos($source, ' ') !== false) {
							list($func, $arg0) = explode(' ', $source, 2);
						} else {
							$arg0 = '';
							$func = $source;
						}
						$out .= call_user_func_array('cws_core_cwsfw_print_' . $func, array($arg0) );
					}	else {
						foreach ($source as $k => $value) {
							$selected = '';
							if (isset($value[1]) && true === $value[1]) {
								$selected = ' selected';
								$values[$key] = $k;
							}
							$data_options = !empty($value[2]) ? ' data-options="' . $value[2] . '"' : '';
							$out .= '<option value="' . $k . '"' . $data_options . $selected .'>' . $value[0] . '</option>';
						}
					}
				} else {
					$out .= '>';
				}
				$out .= '</select>';
				break;
			case 'media':
				$isValueSet = !empty($v['value']['src']);
				$display_none = ' style="display:none"';
				$out .= '<div class="img-wrapper">';
				$out .= !empty($v['subtitle']) ? '<p><span class="sub_title">'.$v['subtitle'].'</span></p>' : '';
				$out .= '<img src'. ($isValueSet ? '="'.$v['value']['src'] . '"' : '') .'/>';
				$url_atts = !empty($v['url-atts']) ? ' ' . $v['url-atts'] : ' readonly type="hidden"';
				$out .= '<input class="widefat" data-key="img"' . $url_atts . ' id="' . $prefix . $name . '" name="' . $prefix . $name . '[src]" value="' . ($isValueSet ? $v['value']['src']:'') . '" />';
				$out .= '<a class="pb-media-cws-pb"'. ($isValueSet ? $display_none : '') .'><i class="fa fa-picture-o"></i> '. esc_html__('Select', 'cws-to') . '</a>';
				$out .= '<a class="pb-remov-cws-pb"'. ($isValueSet ? '' : $display_none) .'><i class="fa fa-trash-o"></i> ' . esc_html__('Remove', 'cws-to') . '</a>';
				$out .= '<input class="widefat" data-key="img-id" readonly id="' . $prefix . $name . '[id]" name="' . $prefix . $name . '[id]" type="hidden" value="'.($isValueSet ? $v['value']['id']:'').'" />';
				if (isset($v['layout'])) {
					$out .= '<div class="media_supplements">';
					$out .=	cws_core_cwsfw_print_layout( $v['layout'], $prefix . $name . '[' );
					$out .= '</div>';
				}
				$out .= '</div>';
				break;
			case 'gallery':
				$isValueSet = !empty($v['value']);
				$out .= '<div class="img-wrapper">';
				$out .= '<a class="pb-gmedia-cws-pb">'. esc_html__('Select', 'cws-to') . '</a>';
				$out .= '<input class="widefat" data-key="gallery" readonly id="' . $prefix . $name . '" name="' . $prefix . $name . '" type="hidden" value="' . ($isValueSet ? esc_attr($v['value']):'') . '" />';
				if ($isValueSet) {
					$g_value = htmlspecialchars_decode($v['value'], ENT_NOQUOTES); // shortcodes should be un-escaped
					$ids = shortcode_parse_atts($g_value);
					if (isset($ids['ids'])) {
						preg_match_all('/\d+/', $ids['ids'], $match);
						if (!empty($match)) {
							$out .= '<div class="cws_gallery">';
							foreach ($match[0] as $k => $val) {
								$out .= '<img src="' . wp_get_attachment_url($val) . '">';
							}
							$out .= '<div class="clear"></div></div>';
						}
					}
				}
				$out .= '</div>';
				break;
		}
		if (isset($v['description'])) {
			$out .= '<div class="description">' . $v['description'] . '</div>';
		}
		if ('module' !== $v['type'] && 'tab' !== $v['type'] ) {
			$out .= "</div>";
			$out .= '</div>';
		}
	}
	if ($isTabs) {
		$out .= '<div class="clear"></div>';
		$tabs_out = '<div class="cws_pb_ftabs">';
		foreach ($tabs as $key => $v) {
			if (is_array($v['icon'])) {
				$icon = sprintf('<i class="%s %s-%s"></i>', $v['icon'][0], $v['icon'][0], $v['icon'][1]);
			} else {
				// direct link
				$icon = '<span></span>';
			}
			$tabs_out .= '<a href=# data-tab="'. $v['tab'] .'" class="' . ($v['active'] ? 'active' : '') .'">' . $icon . $v['title'] . '</a>';
		}
		$tabs_out .= '<div class="clear"></div></div>';
		$out = $tabs_out . $out;
	}
	return $out;
}

function cws_core_cwsfw_print_gf($sel) {
	global $gf;
	$output = '';
	foreach ( $gf as $k => $v) {
		$type = '';
		if (!empty($v->type)) {
			$type = ' data-type="' . $v->type . '"';
		}
		$selected = (!empty($sel) && $k === $sel) ? ' selected' : '';
		$output .= '<option value="' . esc_attr($k) . '"' . $selected . $type . ' data-cat="'.$v->cat.'" data-weight="'.$v->var.'" data-sub="'.$v->sub.'">' . $k . '</option>';
	}
	return $output;
}

function cws_core_cwsfw_print_gf_weight($sel, $font_arr) {
	$output = '';
	$weights = array(
		'100' => esc_html__('Ultra-Light 100', 'cws-to'),
		'100italic' => esc_html__('Ultra-Light Italic 100', 'cws-to'),
		'200' => esc_html__('Light 200', 'cws-to'),
		'200italic' => esc_html__('Light Italic 200', 'cws-to'),
		'300' => esc_html__('Book 300', 'cws-to'),
		'300italic' => esc_html__('Book Italic 300', 'cws-to'),
		'regular' => esc_html__('Regular 400', 'cws-to'),
		'italic' => esc_html__('Italic 400', 'cws-to'),
		'500' => esc_html__('Medium 500', 'cws-to'),
		'500italic' => esc_html__('Medium Italic 500', 'cws-to'),
		'600' => esc_html__('Semi-Bold 600', 'cws-to'),
		'600italic' => esc_html__('Semi-Bols Italic 600', 'cws-to'),
		'700' => esc_html__('Bold 700', 'cws-to'),
		'700italic' => esc_html__('Bold Italic 700', 'cws-to'),
		'800' => esc_html__('Extra-Bold 800', 'cws-to'),
		'800italic' => esc_html__('Extra-Bold Italic 800', 'cws-to'),
		'900' => esc_html__('Ultra-Bold 900', 'cws-to'),
		'900italic' => esc_html__('Ultra-Bold Italic 900', 'cws-to'),
		);
	foreach ( $weights as $k => $v) {
		$selected = (!empty($sel) && in_array((String)$k, $sel) ) ? ' selected' : '';
		$disabled = !in_array($k, $font_arr) ? $selected . ' disabled' : $selected;
		$output .= '<option value="' . $k . '"' . $disabled . '>' . $v . '</option>';
	}
	return $output;
}

function cws_core_cwsfw_getGFSubs() {
	return array(
		'latin' => esc_html__('Latin', 'cws-to'),
		'latin-ext' => esc_html__('Latin Extended', 'cws-to'),
		'greek' => esc_html__('Greek', 'cws-to'),
		'greek-ext' => esc_html__('Greek Extended', 'cws-to'),
		'vietnamese' => esc_html__('Vietnamese', 'cws-to'),
		'hebrew' => esc_html__('Hebrew', 'cws-to'),
		'arabic' => esc_html__('Arabic', 'cws-to'),
		'devanagari' => esc_html__('Devanagari', 'cws-to'),
		'cyrillic' => esc_html__('Cyrillic', 'cws-to'),
		'cyrillic-ext' => esc_html__('Cyrillic Extended', 'cws-to'),
		'khmer' => esc_html__('Khmer', 'cws-to'),
		'tamil' => esc_html__('Tamil', 'cws-to'),
		'thai' => esc_html__('Thai', 'cws-to'),
		'telugu' => esc_html__('Telugu', 'cws-to'),
		'bengali' => esc_html__('Bengali', 'cws-to'),
		'gujarati' => esc_html__('Gujarati', 'cws-to'),
	);
}

function cws_core_cwsfw_print_gf_subs() {
	$output = '<option value="all" selected>' . esc_html('All', 'cws-to') . '</option>';
	$subs = cws_core_cwsfw_getGFSubs();
	foreach ( $subs as $k => $v) {
		$output .= '<option value="' . $k . '">' . $v . '</option>';
	}
	return $output;
}

function cws_core_cwsfw_print_gf_sub($sel, $font_arr) {
	$output = '';
	$subs = cws_core_cwsfw_getGFSubs();
	foreach ( $subs as $k => $v) {
		$selected = (!empty($sel) && in_array($k, $sel) ) ? ' selected' : '';
		$disabled = !in_array($k, $font_arr) ? $selected . ' disabled' : $selected;
		$output .= '<option value="' . $k . '"' . $disabled . '>' . $v . '</option>';
	}
	return $output;
}

function cws_core_cwsfw_print_gf_cat() {
	global $gf;
	$output = '<option value="all" selected>' . esc_html('All', 'cws-to') . '</option>';
	$cats = array();
	foreach ( $gf as $k => $v) {
		if (!in_array($v->cat, $cats)) {
			$output .= '<option value="' . $v->cat . '">' . $v->cat . '</option>';
			$cats[] = $v->cat;
		}
	}
	return $output;
}

function cws_core_cwsfw_print_sidebars($sel) {
	global $wp_registered_sidebars;
	$output = '<option value=""></option>';
	foreach ( (array) $wp_registered_sidebars as $k=>$v) {
		$selected = (!empty($sel) && (String)$k === $sel) ? ' selected' : '';
		$output .= '<option value="' . $k . '"' . $selected . '>' . $v['name'] . '</option>';
	}
	return $output;
}


function cws_core_cwsfw_print_post_type($name, $src) {
	$source = cws_core_cwsfw_get_post_type_array($name);
	$output = '<option value=""></option>';
	foreach($source as $k=>$v) {
		$selected = (!empty($src[$k]) && true === $src[$k][1]) ? ' selected' : '';
		$output .= '<option value="' . $k . '"'.$selected.'>' . $v . '</option>';
	}
	return $output;
}
function cws_core_cwsfw_get_post_type_array($tax, $args = '') {
	global $wpdb;
	$post_type_array = array();
	if(!empty($tax)){
		$tax   = esc_sql( $tax );
		$res = $wpdb->get_results("SELECT ID,post_title,post_name FROM $wpdb->posts WHERE $wpdb->posts.post_type LIKE '$tax' AND $wpdb->posts.post_status = 'publish'");			
		foreach ($res as $key => $value) {
			$post_type_array[$value->ID] = $value->post_title;
		}
	}

	return $post_type_array;
}

function cws_core_cwsfw_print_taxonomy($name, $src) {
	$source = cws_core_cwsfw_get_taxonomy_array($name);
	$output = '<option value=""></option>';
	foreach($source as $k=>$v) {
		$selected = (!empty($src[$k]) && true === $src[$k][1]) ? ' selected' : '';
		$output .= '<option value="' . $k . '"'.$selected.'>' . $v . '</option>';
	}
	return $output;
}
function cws_core_cwsfw_get_taxonomy_array($tax, $args = '') {
	$terms = get_terms($tax, $args);
	$ret = array();
	if (!is_wp_error($terms)) {
		foreach ($terms as $k=>$v) {
			$slug = str_replace('%', '|', $v->slug);
			$ret[$slug] = $v->name;
		}
	}
	return $ret;
}

function cws_core_cwsfw_print_fa ($sel) {
	$cwsfi = get_option('cwsfi');
	$isFlatIcons = false;
	$fIcons = null;
	if (function_exists( CWSTO_THEMESLUG . '_get_all_flaticon_icons')) {
		$fIcons = call_user_func( CWSTO_THEMESLUG. '_get_all_flaticon_icons');
		$isFlatIcons = !empty($fIcons) && is_array($fIcons);
	}
	$output = '<option value=""></option>';
	if (function_exists( CWSTO_THEMESLUG. '_get_all_fa_icons')) {
		if ($isFlatIcons) {
			$output .= '<optgroup label="Font Awesome">';
		}
		$icons = call_user_func( CWSTO_THEMESLUG. '_get_all_fa_icons');
		foreach ($icons as $icon) {
			$selected = ($sel === 'fa fa-' . $icon) ? ' selected' : '';
			$output .= '<option value="fa fa-' . $icon . '" '.$selected.'>' . $icon . '</option>';
		}
		if ($isFlatIcons) {
			$output .= '</optgroup>';
		}
	}
	if ($isFlatIcons) {
		$output .= '<optgroup label="Flaticon">';
		foreach ($fIcons as $icon) {
			$selected = ($sel === 'flaticon-' . $icon) ? ' selected' : '';
			$output .= '<option value="flaticon-' . $icon . '" '.$selected.'>' . $icon . '</option>';
		}
		$output .= '</optgroup>';
	}
	return $output;
}

function cws_core_cwsfw_print_titles ( $ptype ) {
	global $post;
	$output = '';
	$post_bc = $post;
	$r = new WP_Query( array( 'posts_per_page' => '-1', 'post_type' => $ptype, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) );
	while ( $r->have_posts() ) {
		$r->the_post();
		$output .= '<option value="' . $r->post->ID . '">' . esc_attr( get_the_title() ) . "</option>\n";
	}
	wp_reset_postdata();
	$post = $post_bc;
	return $output;
}
function cws_core_build_layout($meta, $layout, $prefix = '', $custom_components = null) {
	if (!empty($layout)) {
		$g_components = cws_core_get_base_components();
		if ($custom_components && is_array($custom_components)) {
			$g_components = cws_core_merge_components($g_components, $custom_components);
		}
		cws_core_build_settings($layout, $g_components);
		if (!empty($meta)) {
			cws_core_cwsfw_fillMbAttributes($meta, $layout);
		}
		//die;
		echo '<script>';
		echo 'window.cws_team_list_ajax="'. wp_create_nonce('cws_team_list_ajax') .'";';
		echo '</script>';	
		return cws_core_cwsfw_print_layout($layout, $prefix);
	} else {
		return '';
	}
}
?>
