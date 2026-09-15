<?php

namespace Acowebs\WCPA;


use WP_Query;

/**
 * Handling Tables related functions
 *
 */
class LookUpTable
{

    static $CPT = "wcpa_pt_tables";
    static $META_KEY_1 = "_wcpa_table_data";
    static $META_KEY_2 = "_wcpa_table_unique_key";

    /**
     * Class Constructor
     *
     */

    public function __construct()
    {
        $this->register_cpt();
    }

    /**
     * Register Custom Post Type
     *
     */
    public function register_cpt()
    {
        $labels = array(
            'name' => _x('LookUp Tables', 'Table Custom Post Type Name', "woo-custom-product-addons-pro"),
            'singular_name' => _x('LookUp Table', 'Table Custom Post Type Name', "woo-custom-product-addons-pro"),
            'name_admin_bar' => _x('LookUp Tables', 'Table Custom Post Type Name', "woo-custom-product-addons-pro"),
            'add_new' => __('Add New LookUp Table', 'woo-custom-product-addons-pro'),
            'add_new_item' => __('Add New LookUp Table', "woo-custom-product-addons-pro"),
            'edit_item' => __('Edit LookUp Table', "woo-custom-product-addons-pro"),
            'new_item' => __('New LookUp Table', "woo-custom-product-addons-pro"),
            'all_items' => __('LookUp Tables', "woo-custom-product-addons-pro"),
            'view_item' => __('View LookUp Table', "woo-custom-product-addons-pro"),
            'search_items' => __('Search LookUp Table', "woo-custom-product-addons-pro"),
            'not_found' => __('No LookUp Table Found', "woo-custom-product-addons-pro"),
            'not_found_in_trash' => __('No LookUp Table Found In Trash', "woo-custom-product-addons-pro"),
            'parent_item_colon' => __('Parent LookUp Table', "woo-custom-product-addons-pro"),
            'menu_name' => 'LookUp Tables'
        );

        $args = array(
            'labels' => apply_filters(self::$CPT . '_labels', $labels),
            'description' => '',
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'query_var' => false,
            'can_export' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'rest_base' => self::$CPT,
            'hierarchical' => false,
            'show_in_rest' => false,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports' => array('title'),
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post',
        );

        register_post_type(self::$CPT, apply_filters(self::$CPT . '_register_args', $args, self::$CPT));
    }

    public function init()
    {
        $this->register_cpt();
    }

    /**
     *  To ensure the post_type in QP_Query has not modified.
     * Some customers writing custom codes to filter out 'posts' from front end search by setting post type 'product'
     * This can cause issue it rest api requests for forms, options fetching
     * @param $query
     * @return mixed
     */
    public function suppress_filters($query)
    {
        $query->set('post_type', array(self::$CPT));
        return $query;
    }

    /**
     * Get the Tables
     */
    public function get_tables($tab, $page = 1, $per_page = 20, $search = '')
    {
        //TODO wpml compatibility
        // add_filter('wpml_should_use_display_as_translated_snippet', '__return_false');
        $args = [
            'post_type' => self::$CPT,
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_status' => array('publish', 'draft'),
            's' => $search,

            //    'lang'=>'en',
            'suppress_filters' => false // set false avoid listing all translation for wpml
        ];
        // if ($this->ml->is_active()) {
        //     $args = $this->ml->listArgs($args);
        // }
        if ($tab == 'trash') {
            $args['post_status'] = 'trash';
        }
        add_filter('pre_get_posts', array($this, 'suppress_filters'), 999, 1);

        $posts = new WP_Query($args);
        remove_filter('pre_get_posts', array($this, 'suppress_filters'), 999);
        $tables = [];
        if ($posts->have_posts()): while ($posts->have_posts()) {
            $posts->the_post();
            $p = [
                'id' => get_the_ID(),
                'title' => html_entity_decode(get_the_title()),
                'active' => get_post_status() === 'publish',
                'post_parent' => wp_get_post_parent_id(get_the_ID()),
                'uniqueId' => get_post_meta(get_the_ID(), self::$META_KEY_2, true)
            ];
            // if ($this->ml->is_active()) {
            //     $p['translations'] = $this->ml->get_post_translations_links(get_the_ID());
            //     $p['lang'] = $this->ml->get_post_language(get_the_ID());
            // }
            $tables[] = $p;
        } endif;
        wp_reset_postdata();

        return ['tables' => $tables, 'totalTables' => $posts->found_posts, 'totalPages' => $posts->max_num_pages];
    }

    public function save_table($post_id, $post_data)
    {
        $response = ['status' => true, 'id' => $post_id, 'redirect' => false];

        $uniqueId = $post_data['id'];
        $post = $post_data['post'];
        $title = $post['title'];
        $data = $post['data'];
        $columns = $post['columns'];
        $tableInfo = Array(
            'data' => $data,
            'columns' => $columns,
        );

        $tableInfo_json = wp_slash(json_encode($tableInfo));

        $lang = false;

        if ($post_id === 0) {
            $new_post_id = $this->insert($title, $uniqueId, $tableInfo_json, $lang);
            $response['id'] = $new_post_id;
            $post_id = $new_post_id;
        } else {
            $this->update($post_id, $title, $tableInfo_json, $lang);
        }

        refreshCaches($post_id);

        Cron::schedule_cron();

        return $response;
    }

    public function insert($title, $uniqueId, $tableInfo_json, $lang = false, $base_lang_id = false)
    {
        $my_post = array(
            'post_title' => $title,
            'post_type' => self::$CPT,
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post($my_post);

        // if ($lang) {
        //     $this->ml->set_post_lang($post_id, $lang, $base_lang_id, self::$CPT);
        // }

        update_post_meta($post_id, self::$META_KEY_1, $tableInfo_json);
        update_post_meta($post_id, self::$META_KEY_2, $uniqueId);

        return $post_id;
    }

    public function update($post_id, $title, $tableInfo_json, $lang = false)
    {
        update_post_meta($post_id, self::$META_KEY_1, $tableInfo_json);
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $title,
            'post_status' => 'publish',
        ));

        // if ($lang) {
        //     $this->ml->set_post_lang($post_id, $lang);
        // }
    }

    public function get_table($table_id)
    {
        $response = ['status' => false];

        $post = get_post($table_id);
        if ($post) {
            $response['post'] = array(
                'title' => $post->post_title,
                'id' => $post->ID
            );
        }

        $settings = get_post_meta($table_id, self::$META_KEY_1, true);
        $uniqueId = get_post_meta($table_id, self::$META_KEY_2, true);


        $tableInfo = json_decode($settings, false);
        $response['data'] = isset($tableInfo->data) ? $tableInfo->data : [];
        $response['columns'] = isset($tableInfo->columns) ? $tableInfo->columns : [];
        $response['id'] = $uniqueId;
        $response['status'] = true;

        return $response;
    }

    public function delete_table($posts)
    {
        $response = array();
        if (is_array($posts)) {
            foreach ($posts as $post_id) {
                $status = wp_delete_post($post_id);
                if ($status) {
                    $response[$post_id] = ['status' => true];
                } else {
                    $response[$post_id] = ['status' => false];
                }
            }
        }

        return $response;
    }

    public function trash_table($posts)
    {
        $response = array();
        if (is_array($posts)) {
            foreach ($posts as $post_id) {
                $status = wp_trash_post($post_id);
                if ($status) {
                    $response[$post_id] = ['status' => true];
                } else {
                    $response[$post_id] = ['status' => false];
                }
            }
        }

        return $response;
    }

    public function restore_table($posts)
    {
        $response = array();
        if (is_array($posts) && !empty($posts)) {
            foreach ($posts as $post_id) {
                $status = wp_untrash_post($post_id);
                wp_publish_post($post_id);
                if ($status) {
                    $response[$post_id] = ['status' => true];
                } else {
                    $response[$post_id] = ['status' => false];
                }
            }
        }

        return $response;
    }

    public function duplicate_table($table_id)
    {
        $response = array();
        if ($table_id) {
            global $wpdb;

            $_duplicate = get_post($table_id);

            if (!isset($_duplicate->post_type) || $_duplicate->post_type !== self::$CPT) {
                return ['status' => false];
            }


            $title = $_duplicate->post_title . ' ' . __('Copy', 'woo-custom-product-addons-pro');

            $settings_json = get_post_meta($table_id, self::$META_KEY_1, true);
            $settings_json = wp_slash($settings_json);
            $uniqueId = "wcpa-table-" . time();

            $lang = false;

            $new_post_id = $this->insert($title, $uniqueId, $settings_json, $lang);

            $item = [
                'id' => $new_post_id,
                'title' => get_the_title($new_post_id),
                'active' => get_post_status($new_post_id) === 'publish' ? true : false,
                'post_parent' => wp_get_post_parent_id($new_post_id),
                'uniqueId' => $uniqueId
            ];
            $response = ['status' => true, 'item' => $item];
        }

        return $response;
    }

    /**
     * Export only tables to a JSON file
     *
     * @param int $table_id
     *
     * @return array $result
     */
    public function export_table($table_id)
    {
        // JSON
        // $tableData_json = get_post_meta($table_id, self::$META_KEY_1, true);
        // $tableData = json_decode($tableData_json);

        // $result = [];
        // $result['title'] = get_the_title($table_id);
        // $result['tableData'] = $tableData;

        // $response['data'] = $result;
        // return $response;

        // CSV
        $response = ['status' => true, 'data' => []];
        $tableInfo_json = get_post_meta($table_id, self::$META_KEY_1, true);
        $tableInfo = json_decode($tableInfo_json);
        $data = isset($tableInfo->data) ? $tableInfo->data : [];
        $columns = isset($tableInfo->columns) ? $tableInfo->columns : [];
        $csv = '';

        foreach ($data as $row) {
            $rowData = [];
            foreach ($columns as $col) {
                $key = $col->id;
                $value = isset($row->$key) ? $row->$key : '';
                $rowData[] = $value;
            }
            $csv .= implode(',', $rowData) . "\n";
        }

        $response['data'] = $csv;
        return $response;
    }

    public function generateColumnName($index) {
        $dividend = $index + 1;
        $columnName = '';

        while ($dividend > 0) { 
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = intval(($dividend - $modulo) / 26);
        }

        return strtolower($columnName);
    }

    public function import_table($post_id, $post_data)
    {
        $response = ['status' => true, 'id' => $post_id, 'settings' => []];
        $post = $post_data['post'];

        $data = [];
        $columns = [];

        if (isset($_FILES['file']) && !empty($_FILES['file'])) {
            $csv = $_FILES['file'];
            if (isset($csv['type']) && $csv['type'] == 'text/csv') {
                if (is_uploaded_file($csv["tmp_name"])) {
                    $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

                    $headings = $line = fgetcsv($csvFile);
                    // Generate column names based on headings count
                    $columnNames = [];
                    if (!empty($headings)) {
                        foreach ($headings as $key => $val) {
                            $columnNames[$key] = $this->generateColumnName($key);
                        }
                    }

                    if (!empty($columnNames)) {
                        foreach ($columnNames as $key => $colName) {
                        $columns[] = (object)[
                            'id' => strtolower($colName),
                            'title' => strtoupper($colName),
                            'columnData' => (object)[
                            'key' => strtolower($colName),
                            'original' => (object)[
                                'component' => (object)[
                                'compare' => null
                                ],
                                'columnData' => (object)[
                                'alignRight' => false,
                                'continuousUpdates' => true,
                                ],
                            ],
                            ],
                        ];
                        }
                    }
                    rewind($csvFile);

                    while (($line = fgetcsv($csvFile)) !== false) {
                        // Build row with column names as keys
                        $formattedRow = [];
                        foreach ($columnNames as $key => $colName) {
                            $formattedRow[$colName] = isset($line[$key]) ? trim($line[$key]) : '';
                        }

                        $data[] = $formattedRow;
                    }
                }
            }
        }
        
        $settings_json = (object)[];
        $uniqueId = "wcpa-table-" . time();

        $settings = (object)[
            'data' => $data,
            'columns' => $columns,
        ];

        $settings_json = wp_slash(json_encode($settings));

        $lang = false;
        // if ($this->ml->is_active()) {
        //     $lang = $post->lang;
        // }

        $response['settings'] = $settings;

        if ($post_id === 0) {
            $new_post_id = $this->insert('', $uniqueId, $settings_json, $lang);
            $response['id'] = $new_post_id;
        } else {
            $this->update($post_id, get_the_title($post_id), $settings_json, $lang);
        }

        return $response;
    }

    private function download_send_headers($filename)
    {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    /**
     * Retrive options items for front end
     *
     * @param $key
     */
    public function get_tables_by_key($key)
    {
        $args = array(
            'post_type' => self::$CPT,
            'meta_key' => self::$META_KEY_2,
            'meta_value' => $key
        );
        add_filter('pre_get_posts', array($this, 'suppress_filters'), 999, 1);
        $query = new WP_Query($args);
        remove_filter('pre_get_posts', array($this, 'suppress_filters'), 999);
        $post_id = wp_list_pluck($query->posts, 'ID');
        if (!$post_id || !isset($post_id[0])) {
            return [];
        }
        $json_string = get_post_meta($post_id[0], self::$META_KEY_1, true);
        $json_decode = json_decode($json_string);
        $tableInfo = [];
        if (isset($json_decode)) {
            $tableInfo[$key] = $json_decode;
            return $tableInfo;
        }

        return [];
    }

    /**
     * Upload file to media by url
     *
     */
    private function uploadImportFileByUrl($url)
    {
        require_once(ABSPATH . "/wp-load.php");
        require_once(ABSPATH . "/wp-admin/includes/image.php");
        require_once(ABSPATH . "/wp-admin/includes/file.php");
        require_once(ABSPATH . "/wp-admin/includes/media.php");

        // Download url to a temp file
        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            return false;
        }

        // Get the filename and extension ("photo.png" => "photo", "png")
        $filename = pathinfo($url, PATHINFO_FILENAME);
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        // Upload by "sideloading": "the same way as an uploaded file is handled by media_handle_upload"
        $args = array(
            'name' => "$filename.$extension",
            'tmp_name' => $tmp,
        );

        // Do the upload
        $attachment_id = media_handle_sideload($args);

        // Cleanup temp file
        @unlink($tmp);

        // Error uploading
        if (is_wp_error($attachment_id)) {
            return false;
        }

        // Success, return attachment ID (int)
        return (int)$attachment_id;
    }

    /**
     * Export Tables to json
     *
     * @return array $result
     */
    function export_bulk_tables($posts = 'all')
    {
        $response = ['status' => true, 'data' => []];
        $args = array(
            'post_type' => self::$CPT,
            'posts_per_page' => -1
        );

        if (!($posts == 'all')) {
            if (is_array($posts) && !empty($posts)) {
                $args['post__in'] = $posts;
            } else {
                $args['post__in'] = [];
            }
        }
        add_filter('pre_get_posts', array($this, 'suppress_filters'), 999, 1);
        $opt_query = new WP_Query($args);
        remove_filter('pre_get_posts', array($this, 'suppress_filters'), 999);
        $result = [];
        if ($opt_query->have_posts()) {
            while ($opt_query->have_posts()) {
                $opt_query->the_post();
                $element = [];
                $element['uniqueId'] = get_post_meta(get_the_ID(), self::$META_KEY_2, true);
                $element['post'] = [
                    'title' => get_the_title(),
                    'lang' => false
                ];
                $tableInfo = json_decode(get_post_meta(get_the_ID(), self::$META_KEY_1, true));
                $element['settings'] = [
                    'data' => isset($tableInfo->data) ? $tableInfo->data : [],
                    'columns' => isset($tableInfo->columns) ? $tableInfo->columns: []
                ];

                $result[] = $element;
            }
        }
        wp_reset_postdata();

        $response['data'] = $result;
        return $response;
    }

    /**
     * Import Tables from json
     * @return array $result
     */

    public function import_bulk_tables($post_data)
    {
        $response = ['status' => true];

        $data = [];

        if (isset($_FILES['file']) && !empty($_FILES['file'])) {
            $json_file = $_FILES['file'];
            if (isset($json_file['type']) && $json_file['type'] == 'application/json') {
                if (is_uploaded_file($json_file["tmp_name"])) {
                    $jsonData = file_get_contents($json_file["tmp_name"]);
                    $posts = json_decode($jsonData);
                    if (!empty($posts)) {
                        foreach ($posts as $p) {
                            $uniqueId = $p->uniqueId;
                            $post = $p->post;
                            $settings = $p->settings;
                            $data = $settings->data;
                            $columns = $settings->columns;
                            $tableInfo = Array(
                                'data' => $data,
                                'columns' => $columns,
                            );
                            $tableInfo_json = wp_slash(json_encode($tableInfo));
                            $lang = false;
                            // $uniqueId = "wcpa-table-" . time();
                            $new_post_id = $this->insert($post->title, $uniqueId, $tableInfo_json, $lang);
                        }
                    }
                }
            }
        }

        return $response;
    }

}