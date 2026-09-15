<?php
/**
 * One-time CLI: reorganize Main Navigation Menu parents only.
 * Does not delete menu items, pages, or change URLs/object IDs.
 *
 * Usage:
 *   php lh-reorganize-nav.php
 *   php lh-reorganize-nav.php --revert
 */
if ( php_sapi_name() !== 'cli' ) {
	fwrite( STDERR, "CLI only.\n" );
	exit( 1 );
}

require_once dirname( __FILE__, 5 ) . '/wp-load.php';

$menu_id = 2; // Main Navigation Menu
$option_backup = 'lh_nav_reorg_backup_v1';
$option_folders = 'lh_nav_reorg_folders_v1';
$revert = in_array( '--revert', $argv, true );

function lh_nav_clean_title( $title ) {
	$title = preg_replace( '/<img[^>]*>/i', '', $title );
	$title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/', ' ', $title ) );
}

function lh_nav_get_items( $menu_id ) {
	$items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'publish' ) );
	return is_array( $items ) ? $items : array();
}

function lh_nav_item_map( $menu_id ) {
	$map = array();
	foreach ( lh_nav_get_items( $menu_id ) as $item ) {
		$map[ (int) $item->ID ] = $item;
	}
	return $map;
}

function lh_nav_set_parent( $item_id, $parent_id, $order ) {
	global $wpdb;
	update_post_meta( $item_id, '_menu_item_menu_item_parent', (string) (int) $parent_id );
	$wpdb->update(
		$wpdb->posts,
		array( 'menu_order' => (int) $order ),
		array( 'ID' => (int) $item_id ),
		array( '%d' ),
		array( '%d' )
	);
	clean_post_cache( $item_id );
}

function lh_nav_find_or_create_folder( $menu_id, $title, $parent_id, $existing_id = 0 ) {
	if ( $existing_id ) {
		wp_update_post(
			array(
				'ID'         => $existing_id,
				'post_title' => $title,
			)
		);
		update_post_meta( $existing_id, '_menu_item_menu_item_parent', (string) (int) $parent_id );
		update_post_meta( $existing_id, '_menu_item_url', '#' );
		return (int) $existing_id;
	}

	foreach ( lh_nav_get_items( $menu_id ) as $item ) {
		$url = isset( $item->url ) ? $item->url : '';
		$is_folder = ( $item->type === 'custom' ) && ( $url === '#' || $url === '' );
		if (
			$is_folder
			&& (int) $item->menu_item_parent === (int) $parent_id
			&& lh_nav_clean_title( $item->title ) === lh_nav_clean_title( $title )
		) {
			return (int) $item->ID;
		}
	}

	$new_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-url'       => '#',
			'menu-item-status'    => 'publish',
			'menu-item-type'      => 'custom',
			'menu-item-parent-id' => (int) $parent_id,
		)
	);

	if ( is_wp_error( $new_id ) ) {
		fwrite( STDERR, 'Failed to create folder: ' . $title . ' — ' . $new_id->get_error_message() . "\n" );
		exit( 1 );
	}

	return (int) $new_id;
}

$items_before = lh_nav_item_map( $menu_id );
if ( ! $items_before ) {
	fwrite( STDERR, "Menu 2 is empty.\n" );
	exit( 1 );
}

if ( $revert ) {
	$backup = get_option( $option_backup );
	if ( ! is_array( $backup ) ) {
		fwrite( STDERR, "No backup found.\n" );
		exit( 1 );
	}
	foreach ( $backup as $id => $row ) {
		if ( ! get_post( $id ) ) {
			continue;
		}
		lh_nav_set_parent( $id, $row['parent'], $row['order'] );
		if ( isset( $row['title'] ) ) {
			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => $row['title'],
				)
			);
		}
	}
	$folders = get_option( $option_folders, array() );
	foreach ( (array) $folders as $fid ) {
		wp_delete_post( (int) $fid, true );
	}
	delete_option( $option_folders );
	echo "Reverted menu hierarchy. Created category folders removed.\n";
	exit( 0 );
}

if ( ! get_option( $option_backup ) ) {
	$backup = array();
	foreach ( $items_before as $id => $item ) {
		$backup[ $id ] = array(
			'parent' => (int) $item->menu_item_parent,
			'order'  => (int) $item->menu_order,
			'title'  => $item->post_title,
		);
	}
	update_option( $option_backup, $backup, false );
	echo 'Backup saved (' . count( $backup ) . " items).\n";
}

// Existing top-level / reused folders.
$TOP = array(
	'home'          => 13,
	'horoscope'     => 2009,
	'consultation'  => 81937,
	'y2026'         => 72595,
	'transits'      => 72921,
	'career'        => 54063,
	'marriage'      => 54065,
	'combo'         => 24021,
	'ask'           => 54066,
	'enquiry'       => 86159,
	'blog'          => 84141,
	'shop'          => 28310,
);

$folders = get_option( $option_folders, array() );

$F = array();
$F['life_horoscope']        = lh_nav_find_or_create_folder( $menu_id, 'Life Horoscope', $TOP['horoscope'], 54057 );
$F['kids']                  = lh_nav_find_or_create_folder( $menu_id, 'Kids & Child Horoscope', $TOP['horoscope'], $folders['kids'] ?? 0 );
$F['star']                  = lh_nav_find_or_create_folder( $menu_id, 'Star & Rasi Predictions', $TOP['horoscope'], $folders['star'] ?? 0 );
$F['monthly']               = lh_nav_find_or_create_folder( $menu_id, 'Monthly Horoscope', $TOP['horoscope'], $folders['monthly'] ?? 0 );
$F['numerology']            = lh_nav_find_or_create_folder( $menu_id, 'Numerology', $TOP['horoscope'], $folders['numerology'] ?? 0 );
$F['gemstone']              = lh_nav_find_or_create_folder( $menu_id, 'Gemstone', $TOP['horoscope'], $folders['gemstone'] ?? 0 );
$F['education']             = lh_nav_find_or_create_folder( $menu_id, 'Education Horoscope', $TOP['horoscope'], $folders['education'] ?? 0 );
$F['wealth']                = lh_nav_find_or_create_folder( $menu_id, 'Wealth & Finance', $TOP['horoscope'], $folders['wealth'] ?? 0 );
$F['remedies']              = lh_nav_find_or_create_folder( $menu_id, 'Remedies & Special Services', $TOP['horoscope'], $folders['remedies'] ?? 0 );
$F['specialized']           = lh_nav_find_or_create_folder( $menu_id, 'Specialized Predictions', $TOP['horoscope'], $folders['specialized'] ?? 0 );

$F['astro_consult']         = lh_nav_find_or_create_folder( $menu_id, 'Astrology Consultation', $TOP['consultation'], $folders['astro_consult'] ?? 0 );
$F['medical']               = lh_nav_find_or_create_folder( $menu_id, 'Medical Astrology', $TOP['consultation'], 81936 );
$F['vaastu']                = lh_nav_find_or_create_folder( $menu_id, 'Vaastu Consultation', $TOP['consultation'], 86146 );
$F['palmistry']             = lh_nav_find_or_create_folder( $menu_id, 'Palmistry', $TOP['consultation'], 50442 );

$F['personalized_2026']     = lh_nav_find_or_create_folder( $menu_id, 'Personalized 2026 Predictions', $TOP['y2026'], $folders['personalized_2026'] ?? 0 );
$F['yearly_2026']           = lh_nav_find_or_create_folder( $menu_id, 'Yearly Horoscope 2026', $TOP['y2026'], $folders['yearly_2026'] ?? 0 );
$F['combos_2026']           = lh_nav_find_or_create_folder( $menu_id, '2026 Related Combos', $TOP['y2026'], $folders['combos_2026'] ?? 0 );

$F['jupiter']               = lh_nav_find_or_create_folder( $menu_id, 'Jupiter Transit', $TOP['transits'], $folders['jupiter'] ?? 0 );
$F['rahu_ketu']             = lh_nav_find_or_create_folder( $menu_id, 'Rahu Ketu Transit', $TOP['transits'], $folders['rahu_ketu'] ?? 0 );
$F['saturn']                = lh_nav_find_or_create_folder( $menu_id, 'Saturn Transit', $TOP['transits'], $folders['saturn'] ?? 0 );
$F['triple']                = lh_nav_find_or_create_folder( $menu_id, 'Triple Transit', $TOP['transits'], $folders['triple'] ?? 0 );
$F['double']                = lh_nav_find_or_create_folder( $menu_id, 'Double Transit', $TOP['transits'], $folders['double'] ?? 0 );
$F['transit_combos']        = lh_nav_find_or_create_folder( $menu_id, 'Transit Combos', $TOP['transits'], $folders['transit_combos'] ?? 0 );

$F['career_astrology']      = lh_nav_find_or_create_folder( $menu_id, 'Career Astrology', $TOP['career'], $folders['career_astrology'] ?? 0 );
$F['career_prediction']     = lh_nav_find_or_create_folder( $menu_id, 'Career Prediction', $TOP['career'], $folders['career_prediction'] ?? 0 );
$F['career_compatibility']  = lh_nav_find_or_create_folder( $menu_id, 'Career Compatibility', $TOP['career'], $folders['career_compatibility'] ?? 0 );
$F['career_combos']         = lh_nav_find_or_create_folder( $menu_id, 'Career Combos', $TOP['career'], $folders['career_combos'] ?? 0 );

$F['marriage_astrology']    = lh_nav_find_or_create_folder( $menu_id, 'Marriage Astrology', $TOP['marriage'], $folders['marriage_astrology'] ?? 0 );
$F['marriage_matching']     = lh_nav_find_or_create_folder( $menu_id, 'Marriage Matching', $TOP['marriage'], $folders['marriage_matching'] ?? 0 );
$F['marriage_compat']       = lh_nav_find_or_create_folder( $menu_id, 'Marriage Compatibility', $TOP['marriage'], $folders['marriage_compat'] ?? 0 );
$F['marriage_predictions']  = lh_nav_find_or_create_folder( $menu_id, 'Marriage Predictions', $TOP['marriage'], $folders['marriage_predictions'] ?? 0 );
$F['marriage_combos']       = lh_nav_find_or_create_folder( $menu_id, 'Marriage Combos', $TOP['marriage'], $folders['marriage_combos'] ?? 0 );

$F['life_combos']           = lh_nav_find_or_create_folder( $menu_id, 'Life Horoscope Combos', $TOP['combo'], 54060 );
$F['wealth_combos']         = lh_nav_find_or_create_folder( $menu_id, 'Wealth Combos', $TOP['combo'], $folders['wealth_combos'] ?? 0 );
$F['remedy_combos']         = lh_nav_find_or_create_folder( $menu_id, 'Horoscope + Remedies Combos', $TOP['combo'], $folders['remedy_combos'] ?? 0 );

$created_only = array();
foreach ( $F as $key => $id ) {
	if ( ! isset( $items_before[ $id ] ) ) {
		$created_only[ $key ] = $id;
	}
}
update_option( $option_folders, $created_only, false );

/*
 * Existing item ID => new parent ID.
 * Every original service/page menu item is listed.
 */
$parents = array(
	// Top level stays top level.
	13    => 0,
	2009  => 0,
	81937 => 0,
	72595 => 0,
	72921 => 0,
	54063 => 0,
	54065 => 0,
	24021 => 0,
	54066 => 0,
	86159 => 0,
	84141 => 0,
	28310 => 0,

	// Horoscope category folders.
	$F['life_horoscope'] => $TOP['horoscope'],
	$F['kids']           => $TOP['horoscope'],
	$F['star']           => $TOP['horoscope'],
	$F['monthly']        => $TOP['horoscope'],
	$F['numerology']     => $TOP['horoscope'],
	$F['gemstone']       => $TOP['horoscope'],
	$F['education']      => $TOP['horoscope'],
	$F['wealth']         => $TOP['horoscope'],
	$F['remedies']       => $TOP['horoscope'],
	$F['specialized']    => $TOP['horoscope'],

	// Life Horoscope services.
	85    => $F['life_horoscope'],
	37087 => $F['life_horoscope'],
	54526 => 37087, // stay nested under Varshaphala
	53728 => $F['life_horoscope'],
	41712 => $F['life_horoscope'],
	50935 => $F['life_horoscope'],
	40893 => $F['life_horoscope'],
	41719 => $F['life_horoscope'],
	86676 => $F['life_horoscope'],
	86145 => $F['life_horoscope'],
	73007 => $F['life_horoscope'],
	73209 => $F['life_horoscope'],
	84750 => $F['life_horoscope'],
	73216 => $F['life_horoscope'],
	73361 => $F['life_horoscope'],
	50479 => $F['life_horoscope'],
	72374 => $F['life_horoscope'],
	72472 => $F['life_horoscope'],

	// Kids & Child.
	67991 => $F['kids'],
	71714 => $F['kids'],
	74787 => $F['kids'],
	68945 => $F['kids'],

	// Star & Rasi.
	73689 => $F['star'],

	// Monthly.
	17380 => $F['monthly'],
	85925 => $F['monthly'],
	85929 => $F['monthly'],
	59673 => $F['monthly'],

	// Numerology.
	17395 => $F['numerology'],
	74806 => $F['numerology'],
	75240 => $F['numerology'],

	// Gemstone.
	17250 => $F['gemstone'],
	72549 => $F['gemstone'],

	// Education.
	46108 => $F['education'],
	49609 => $F['education'],

	// Wealth & Finance.
	46110 => $F['wealth'],
	49621 => $F['wealth'],

	// Remedies.
	72616 => $F['remedies'],
	49637 => $F['remedies'],

	// Specialized.
	67487 => $F['specialized'],
	49627 => $F['specialized'],

	// Consultation folders + services.
	$F['astro_consult'] => $TOP['consultation'],
	$F['medical']       => $TOP['consultation'],
	$F['vaastu']        => $TOP['consultation'],
	$F['palmistry']     => $TOP['consultation'],
	81940 => $F['astro_consult'],
	81938 => $F['medical'],
	49615 => $F['medical'],
	86147 => $F['vaastu'],
	86148 => $F['vaastu'],
	86149 => $F['vaastu'],
	51393 => $F['palmistry'],
	51391 => $F['palmistry'],

	// 2026.
	$F['personalized_2026'] => $TOP['y2026'],
	$F['yearly_2026']       => $TOP['y2026'],
	$F['combos_2026']       => $TOP['y2026'],
	70395 => $F['personalized_2026'],
	72557 => $F['personalized_2026'],
	71261 => $F['personalized_2026'],
	72376 => $F['personalized_2026'],
	85671 => $F['yearly_2026'],
	85674 => $F['yearly_2026'],
	59381 => $F['combos_2026'],
	71850 => $F['combos_2026'],

	// Transits.
	$F['jupiter']        => $TOP['transits'],
	$F['rahu_ketu']      => $TOP['transits'],
	$F['saturn']         => $TOP['transits'],
	$F['triple']         => $TOP['transits'],
	$F['double']         => $TOP['transits'],
	$F['transit_combos'] => $TOP['transits'],
	50025 => $F['jupiter'],
	72927 => $F['rahu_ketu'],
	74582 => $F['saturn'],
	72567 => $F['saturn'],
	70426 => $F['saturn'],
	75484 => $F['triple'],
	51731 => $F['double'],
	72922 => $F['transit_combos'],
	71456 => $F['transit_combos'],

	// Career.
	$F['career_astrology']     => $TOP['career'],
	$F['career_prediction']    => $TOP['career'],
	$F['career_compatibility'] => $TOP['career'],
	$F['career_combos']        => $TOP['career'],
	86577 => $F['career_astrology'],
	46109 => $F['career_prediction'],
	49423 => $F['career_prediction'],
	49631 => $F['career_prediction'],
	49659 => $F['career_prediction'],
	68543 => $F['career_compatibility'],
	67132 => $F['career_compatibility'],
	47959 => $F['career_combos'],
	47954 => $F['career_combos'],

	// Marriage.
	$F['marriage_astrology']   => $TOP['marriage'],
	$F['marriage_matching']    => $TOP['marriage'],
	$F['marriage_compat']      => $TOP['marriage'],
	$F['marriage_predictions'] => $TOP['marriage'],
	$F['marriage_combos']      => $TOP['marriage'],
	86576 => $F['marriage_astrology'],
	1275  => $F['marriage_matching'],
	72069 => $F['marriage_matching'],
	72030 => $F['marriage_matching'],
	88159 => $F['marriage_matching'],
	67119 => $F['marriage_compat'],
	49947 => $F['marriage_compat'],
	53297 => $F['marriage_compat'],
	52818 => $F['marriage_predictions'],
	49654 => $F['marriage_predictions'],
	49644 => $F['marriage_predictions'],
	49665 => $F['marriage_predictions'],
	47860 => $F['marriage_combos'],

	// Combo Services.
	$F['life_combos']   => $TOP['combo'],
	$F['wealth_combos'] => $TOP['combo'],
	$F['remedy_combos'] => $TOP['combo'],
	39260 => $F['life_combos'],
	48636 => $F['life_combos'],
	46172 => $F['life_combos'],
	28220 => $F['life_combos'],
	35734 => $F['life_combos'],
	27818 => $F['life_combos'],
	40669 => $F['life_combos'],
	48492 => $F['life_combos'],
	47693 => $F['wealth_combos'],
	51922 => $F['remedy_combos'],
	49461 => $F['remedy_combos'],
	49416 => $F['remedy_combos'],
	27647 => $F['remedy_combos'],

	// Ask 1 Question.
	58160 => $TOP['ask'],

	// Shop (unchanged).
	58001 => $TOP['shop'],
	28384 => $TOP['shop'],
	33181 => $TOP['shop'],
	28311 => $TOP['shop'],
	28312 => $TOP['shop'],
	28313 => $TOP['shop'],
);

$missing = array();
foreach ( $items_before as $id => $item ) {
	if ( ! isset( $parents[ $id ] ) ) {
		$missing[] = $id . ' | ' . lh_nav_clean_title( $item->title );
	}
}
if ( $missing ) {
	fwrite( STDERR, "ABORT: unmapped existing items:\n" . implode( "\n", $missing ) . "\n" );
	exit( 1 );
}

$tree_order = array(
	$TOP['home'],
	$TOP['horoscope'],
		$F['life_horoscope'], 85, 37087, 54526, 53728, 41712, 50935, 40893, 41719, 86676, 86145, 73007, 73209, 84750, 73216, 73361, 50479, 72374, 72472,
		$F['kids'], 67991, 71714, 74787, 68945,
		$F['star'], 73689,
		$F['monthly'], 17380, 85925, 85929, 59673,
		$F['numerology'], 17395, 74806, 75240,
		$F['gemstone'], 17250, 72549,
		$F['education'], 46108, 49609,
		$F['wealth'], 46110, 49621,
		$F['remedies'], 72616, 49637,
		$F['specialized'], 67487, 49627,
	$TOP['consultation'],
		$F['astro_consult'], 81940,
		$F['medical'], 81938, 49615,
		$F['vaastu'], 86147, 86148, 86149,
		$F['palmistry'], 51393, 51391,
	$TOP['y2026'],
		$F['personalized_2026'], 70395, 72557, 71261, 72376,
		$F['yearly_2026'], 85671, 85674,
		$F['combos_2026'], 59381, 71850,
	$TOP['transits'],
		$F['jupiter'], 50025,
		$F['rahu_ketu'], 72927,
		$F['saturn'], 74582, 72567, 70426,
		$F['triple'], 75484,
		$F['double'], 51731,
		$F['transit_combos'], 72922, 71456,
	$TOP['career'],
		$F['career_astrology'], 86577,
		$F['career_prediction'], 46109, 49423, 49631, 49659,
		$F['career_compatibility'], 68543, 67132,
		$F['career_combos'], 47959, 47954,
	$TOP['marriage'],
		$F['marriage_astrology'], 86576,
		$F['marriage_matching'], 1275, 72069, 72030, 88159,
		$F['marriage_compat'], 67119, 49947, 53297,
		$F['marriage_predictions'], 52818, 49654, 49644, 49665,
		$F['marriage_combos'], 47860,
	$TOP['combo'],
		$F['life_combos'], 39260, 48636, 46172, 28220, 35734, 27818, 40669, 48492,
		$F['wealth_combos'], 47693,
		$F['remedy_combos'], 51922, 49461, 49416, 27647,
	$TOP['ask'],
		58160,
	$TOP['enquiry'],
	$TOP['blog'],
	$TOP['shop'],
		58001, 28384, 33181, 28311, 28312, 28313,
);

$order = 1;
$seen  = array();
foreach ( $tree_order as $id ) {
	if ( isset( $seen[ $id ] ) ) {
		fwrite( STDERR, "ABORT: duplicate in tree_order: $id\n" );
		exit( 1 );
	}
	$seen[ $id ] = true;
	if ( ! isset( $parents[ $id ] ) ) {
		fwrite( STDERR, "ABORT: tree item missing parent map: $id\n" );
		exit( 1 );
	}
	lh_nav_set_parent( $id, $parents[ $id ], $order );
	$order++;
}

foreach ( $items_before as $id => $item ) {
	if ( ! isset( $seen[ $id ] ) ) {
		fwrite( STDERR, "ABORT: original item not in tree_order: $id " . lh_nav_clean_title( $item->title ) . "\n" );
		exit( 1 );
	}
}

wp_cache_delete( $menu_id, 'nav_menu' );
clean_taxonomy_cache( 'nav_menu' );

echo 'Reorganized ' . count( $seen ) . " menu items. Original items preserved.\n";
echo 'New category folders created: ' . count( $created_only ) . "\n";
foreach ( $created_only as $key => $id ) {
	echo "  $key => $id\n";
}
echo "Done.\n";
