<?php
/**
 * Plugin Name: LH Live Form Converter (one-time)
 * Description: Replaces frozen copies of WooCommerce / Custom Product Addons forms inside WPBakery Raw HTML blocks with [lh_product_form id="..."], so pages always show the form saved in WCPA admin. Tools → LH Live Forms. Deactivate and delete after use.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LH_LFC_BACKUP_META    = '_lh_live_form_backup';
const LH_LFC_CONVERTED_META = '_lh_live_form_converted_md5';
const LH_LFC_BATCH          = 20;

/**
 * Scans pages; when $apply is true converts up to $limit of them.
 *
 * @return array{todo: array, done: array, skipped: array, remaining: int}
 */
function lh_lfc_run( $apply, $limit = LH_LFC_BATCH ) {
	global $wpdb;

	$form_rx = '/<form\b[^>]*class=["\'][^"\']*\bcart\b[^>]*>.*?<\/form>/s';
	$ids     = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->posts}
		 WHERE post_type NOT IN ('revision','nav_menu_item') AND post_status IN ('publish','draft','private','pending','future')
		 AND post_content LIKE '%vc_raw_html%' ORDER BY ID"
	);

	$result = array( 'todo' => array(), 'done' => array(), 'skipped' => array(), 'remaining' => 0 );

	if ( $apply ) {
		kses_remove_filters();
	}

	foreach ( $ids as $id ) {
		$page    = get_post( (int) $id );
		$content = $page->post_content;
		$changes = array();
		$problem = '';

		$new_content = preg_replace_callback(
			'/(\[vc_raw_html[^\]]*\])(.*?)(\[\/vc_raw_html\])/s',
			static function ( $m ) use ( $form_rx, &$changes, &$problem ) {
				$html = rawurldecode( base64_decode( html_entity_decode( $m[2] ) ) );
				if ( false !== strpos( $html, 'lh_product_form' ) ) {
					return $m[0];
				}
				$count = preg_match_all( $form_rx, $html, $fm );
				if ( 0 === $count ) {
					return $m[0];
				}
				if ( 1 !== $count ) {
					$problem = "block has {$count} cart forms";
					return $m[0];
				}
				if ( ! preg_match( '/name=["\']add-to-cart["\'][^>]*value=["\'](\d+)["\']|value=["\'](\d+)["\'][^>]*name=["\']add-to-cart["\']/', $fm[0][0], $pm ) ) {
					$problem = 'no add-to-cart product id in form';
					return $m[0];
				}
				$pid     = (int) ( $pm[1] ? $pm[1] : $pm[2] );
				$product = wc_get_product( $pid );
				if ( ! $product ) {
					$problem = "product {$pid} not found";
					return $m[0];
				}
				if ( 'publish' !== $product->get_status() || ! $product->is_purchasable() ) {
					$problem = "product {$pid} not purchasable (status " . $product->get_status() . ')';
					return $m[0];
				}
				$changes[] = $pid;
				return $m[1] . base64_encode( rawurlencode( preg_replace( $form_rx, '[lh_product_form id="' . $pid . '"]', $html, 1 ) ) ) . $m[3];
			},
			$content
		);

		$row = array(
			'id'       => $page->ID,
			'title'    => $page->post_title,
			'status'   => $page->post_status,
			'link'     => get_permalink( $page->ID ),
			'products' => implode( ', ', $changes ),
		);

		if ( $problem ) {
			$row['note']         = $problem;
			$result['skipped'][] = $row;
			continue;
		}
		if ( ! $changes ) {
			continue;
		}
		if ( ! $apply || count( $result['done'] ) >= $limit ) {
			$result['todo'][] = $row;
			continue;
		}

		if ( '' === get_post_meta( $page->ID, LH_LFC_BACKUP_META, true ) ) {
			update_post_meta( $page->ID, LH_LFC_BACKUP_META, wp_slash( $content ) );
		}
		_wp_put_post_revision( $page );
		$res = wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => $new_content ) ), true );
		$saved = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $page->ID ) );

		if ( is_wp_error( $res ) || $saved !== $new_content ) {
			$row['note']         = is_wp_error( $res ) ? 'update failed: ' . $res->get_error_message() : 'saved content differs, please check this page';
			$result['skipped'][] = $row;
			continue;
		}
		update_post_meta( $page->ID, LH_LFC_CONVERTED_META, md5( $new_content ) );
		$result['done'][] = $row;
	}

	$result['remaining'] = count( $result['todo'] );
	return $result;
}

/**
 * Restores pages converted by this tool, unless they were edited afterwards.
 */
function lh_lfc_undo() {
	global $wpdb;
	kses_remove_filters();
	$out = array( 'restored' => array(), 'skipped' => array() );
	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", LH_LFC_BACKUP_META ) );
	foreach ( $ids as $id ) {
		$page     = get_post( (int) $id );
		$original = get_post_meta( $page->ID, LH_LFC_BACKUP_META, true );
		$label    = $page->ID . ' ' . $page->post_title;
		if ( md5( $page->post_content ) !== get_post_meta( $page->ID, LH_LFC_CONVERTED_META, true ) ) {
			$out['skipped'][] = $label . ' (edited after conversion, restore it from Revisions instead)';
			continue;
		}
		$res = wp_update_post( wp_slash( array( 'ID' => $page->ID, 'post_content' => $original ) ), true );
		if ( is_wp_error( $res ) ) {
			$out['skipped'][] = $label . ' (' . $res->get_error_message() . ')';
			continue;
		}
		delete_post_meta( $page->ID, LH_LFC_BACKUP_META );
		delete_post_meta( $page->ID, LH_LFC_CONVERTED_META );
		$out['restored'][] = $label;
	}
	return $out;
}

add_action(
	'admin_menu',
	static function () {
		add_management_page( 'LH Live Forms', 'LH Live Forms', 'manage_options', 'lh-live-forms', 'lh_lfc_page' );
	}
);

function lh_lfc_table( $rows, $with_note = false ) {
	if ( ! $rows ) {
		echo '<p><em>None.</em></p>';
		return;
	}
	echo '<table class="widefat striped"><thead><tr><th>ID</th><th>Page</th><th>Status</th><th>Product</th>' . ( $with_note ? '<th>Reason</th>' : '' ) . '</tr></thead><tbody>';
	foreach ( $rows as $r ) {
		printf(
			'<tr><td>%d</td><td><a href="%s" target="_blank">%s</a></td><td>%s</td><td>%s</td>%s</tr>',
			(int) $r['id'],
			esc_url( $r['link'] ),
			esc_html( $r['title'] ),
			esc_html( $r['status'] ),
			esc_html( $r['products'] ),
			$with_note ? '<td>' . esc_html( $r['note'] ) . '</td>' : ''
		);
	}
	echo '</tbody></table>';
}

function lh_lfc_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$ready  = shortcode_exists( 'lh_product_form' );
	$action = isset( $_POST['lh_lfc_action'] ) ? sanitize_key( $_POST['lh_lfc_action'] ) : '';

	echo '<div class="wrap"><h1>LH Live Forms</h1>';
	echo '<p>Replaces the frozen form copies on service pages with <code>[lh_product_form id="…"]</code> (same product ID), so changes saved in Custom Product Addons show on the page. Nothing outside the form is changed. Each page\'s original content is kept for Undo, and a revision is saved.</p>';

	if ( ! $ready ) {
		echo '<div class="notice notice-error"><p><strong>Upload the theme files first.</strong> The <code>[lh_product_form]</code> shortcode is not loaded (theme <code>inc/lh-product-form.php</code> + the require line in <code>functions.php</code>). Converting now would show raw shortcode text on pages, so Apply is disabled.</p></div>';
	}

	if ( $action && check_admin_referer( 'lh_lfc' ) ) {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 300 );
		}
		if ( 'apply' === $action && $ready ) {
			$res = lh_lfc_run( true );
			echo '<div class="notice notice-success"><p>Converted ' . count( $res['done'] ) . ' page(s) in this batch. Remaining: ' . (int) $res['remaining'] . ( $res['remaining'] ? ' — click Apply again.' : ' — all done. Clear your cache plugin / CDN cache.' ) . '</p></div>';
			echo '<h2>Converted in this batch</h2>';
			lh_lfc_table( $res['done'] );
		} elseif ( 'undo' === $action ) {
			$res = lh_lfc_undo();
			echo '<div class="notice notice-warning"><p>Restored ' . count( $res['restored'] ) . ' page(s).</p>';
			if ( $res['skipped'] ) {
				echo '<p>Not restored:<br>' . implode( '<br>', array_map( 'esc_html', $res['skipped'] ) ) . '</p>';
			}
			echo '</div>';
		}
	}

	$scan = lh_lfc_run( false );
	echo '<h2>Will be converted (' . count( $scan['todo'] ) . ')</h2>';
	lh_lfc_table( $scan['todo'] );
	echo '<h2>Will be skipped (' . count( $scan['skipped'] ) . ')</h2>';
	echo '<p>These keep their current form. Usually the product is a draft or deleted — fix the product ID in the page manually.</p>';
	lh_lfc_table( $scan['skipped'], true );

	echo '<form method="post" style="margin-top:20px;display:flex;gap:10px">';
	wp_nonce_field( 'lh_lfc' );
	printf(
		'<button class="button button-primary" name="lh_lfc_action" value="apply" %s onclick="return confirm(\'Convert the next %d pages? Take a database backup first.\')">Apply (next %d pages)</button>',
		( $ready && $scan['todo'] ) ? '' : 'disabled',
		LH_LFC_BATCH,
		LH_LFC_BATCH
	);
	$has_backup = (bool) $GLOBALS['wpdb']->get_var( $GLOBALS['wpdb']->prepare( "SELECT 1 FROM {$GLOBALS['wpdb']->postmeta} WHERE meta_key = %s LIMIT 1", LH_LFC_BACKUP_META ) );
	printf(
		'<button class="button" name="lh_lfc_action" value="undo" %s onclick="return confirm(\'Restore all pages converted by this tool to their old frozen forms?\')">Undo all</button>',
		$has_backup ? '' : 'disabled'
	);
	echo '</form></div>';
}
