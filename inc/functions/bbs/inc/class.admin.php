<?php
/**
 * BBS Admin class stub
 * Replaces encrypted admin management class
 */

class zib_bbs_admin
{
    public $bbs = null;

    public function __construct($bbs_instance = null)
    {
        $this->bbs = $bbs_instance;

        if ($this->bbs && is_admin()) {
            $this->setup();
        }
    }

    public function setup()
    {
        // Admin columns and filters for BBS post types
        add_filter('manage_forum_post_posts_columns', array($this, 'posts_columns'));
        add_action('manage_forum_post_posts_custom_column', array($this, 'posts_custom_column'), 10, 2);
        add_filter('manage_edit-forum_post_sortable_columns', array($this, 'sortable_columns'));
        add_action('pre_get_posts', array($this, 'admin_orderby_query'), 1000);
        add_filter('request', array($this, 'admin_plate_filter_request'), 20);
        add_filter('posts_where', array($this, 'admin_exclude_trash_posts_where'), 20, 2);

        add_filter('manage_plate_posts_columns', array($this, 'plate_columns'));
        add_action('manage_plate_posts_custom_column', array($this, 'plate_custom_column'), 10, 2);

        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);

        add_action('bulk_edit_custom_box', array($this->bbs, 'bulk_edit_custom_box'), 10, 2);
        add_action('save_post', array($this->bbs, 'bulk_save_post'), 10, 3);
    }

    // Forum post columns
    public function posts_columns($columns)
    {
        $current_order = isset($_REQUEST['order']) ? sanitize_key(wp_unslash($_REQUEST['order'])) : '';
        $order         = 'asc' === $current_order ? 'desc' : 'asc';
        $order_by = isset($_REQUEST['orderby']) ? sanitize_key(wp_unslash($_REQUEST['orderby'])) : '';
        $o_icon   = '<i class="opacity5 ml3 fa fa-long-arrow-' . ('asc' === $current_order ? 'up' : 'down') . '"></i>';

        $new_columns = array();

        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }

        $new_columns['title']    = isset($columns['title']) ? $columns['title'] : __('标题', 'zib_language');
        $new_columns['plate_id'] = '<a href="' . esc_url(add_query_arg(array('orderby' => 'plate_id', 'order' => $order))) . '">' . __('板块', 'zib_language') . ('plate_id' === $order_by ? $o_icon : '') . '</a>';

        $new_columns['all_count'] = '<a href="' . esc_url(add_query_arg(array('orderby' => 'views', 'order' => $order))) . '">' . __('阅读', 'zib_language') . ('views' === $order_by ? $o_icon : '') . '</a>'
            . '·<a href="' . esc_url(add_query_arg(array('orderby' => 'score', 'order' => $order))) . '">' . __('评分', 'zib_language') . ('score' === $order_by ? $o_icon : '') . '</a>'
            . '·<a href="' . esc_url(add_query_arg(array('orderby' => 'favorite_count', 'order' => $order))) . '">' . __('收藏', 'zib_language') . ('favorite_count' === $order_by ? $o_icon : '') . '</a>';

        if (isset($columns['author'])) {
            $new_columns['author'] = $columns['author'];
        }

        $new_columns['taxonomy-forum_topic'] = __('论坛话题', 'zib_language');
        $new_columns['taxonomy-forum_tag']   = __('论坛标签', 'zib_language');

        if (isset($columns['comments'])) {
            $new_columns['comments'] = $columns['comments'];
        }

        if (isset($columns['date'])) {
            $new_columns['date'] = $columns['date'];
        }

        return $new_columns;
    }

    public function posts_custom_column($column_name, $post_id)
    {
        switch ($column_name) {
            case 'plate_id':
                $plate_id = function_exists('zib_bbs_get_plate_id') ? zib_bbs_get_plate_id($post_id) : get_post_meta($post_id, 'plate_id', true);
                if ($plate_id) {
                    $plate = get_post($plate_id);
                    if ($plate) {
                        $title = esc_html($plate->post_title);
                        echo '<a href="' . esc_url(add_query_arg('cat', 'plate_' . $plate_id)) . '">' . $title . '</a>';
                    } else {
                        echo esc_html($plate_id);
                    }
                } else {
                    echo '<span style="color:#ee4545;">' . esc_html__('未选择板块', 'zib_language') . '</span>';
                }
                break;
            case 'all_count':
                $views          = get_post_meta($post_id, 'views', true);
                $score          = get_post_meta($post_id, 'score', true);
                $favorite_count = get_post_meta($post_id, 'favorite_count', true);

                if (function_exists('_cut_count')) {
                    $views          = _cut_count((string) $views);
                    $score          = _cut_count((string) $score);
                    $favorite_count = _cut_count((string) $favorite_count);
                } else {
                    $views          = (int) $views;
                    $score          = (int) $score;
                    $favorite_count = (int) $favorite_count;
                }

                echo '<div style="font-size: 12px;">' . esc_html__('阅读', 'zib_language') . esc_html($views) . ' · ' . esc_html__('评分', 'zib_language') . esc_html($score) . ' · ' . esc_html__('收藏', 'zib_language') . esc_html($favorite_count) . '</div>';
                break;
        }
    }

    public function sortable_columns($columns)
    {
        $columns['comments'] = array('comment_count', false);
        $columns['date']     = array('date', false);
        return $columns;
    }

    public function admin_orderby_query($query)
    {
        if (!$query->is_main_query() || !is_admin()) {
            return;
        }

        $post_type = $query->get('post_type');
        if (!$post_type && isset($_GET['post_type'])) {
            $post_type = sanitize_key(wp_unslash($_GET['post_type']));
        }

        if ('forum_post' !== $post_type) {
            return;
        }

        $orderby = $query->get('orderby');
        if (!$orderby && isset($_GET['orderby'])) {
            $orderby = sanitize_key(wp_unslash($_GET['orderby']));
        }

        $meta_orderbys = array(
            'plate_id',
            'views',
            'score',
            'favorite_count',
        );

        if (in_array($orderby, $meta_orderbys, true)) {
            $query->set('meta_key', $orderby);
            $query->set('orderby', 'meta_value_num');
        }
    }

    public function admin_plate_filter_request($query_vars)
    {
        if (!$this->is_forum_post_admin_list_request($query_vars)) {
            return $query_vars;
        }

        $post_status = isset($_GET['post_status']) ? sanitize_key(wp_unslash($_GET['post_status'])) : '';
        if (!$post_status || 'all' === $post_status) {
            $query_vars['post_status'] = array('publish', 'pending', 'draft', 'future', 'private', 'closed');
        }

        $plate_id = $this->get_admin_plate_id();
        if ($plate_id) {
            $meta_query   = isset($query_vars['meta_query']) && is_array($query_vars['meta_query']) ? $query_vars['meta_query'] : array();
            $meta_query[] = array(
                'key'     => 'plate_id',
                'value'   => $plate_id,
                'compare' => '=',
                'type'    => 'NUMERIC',
            );

            $query_vars['meta_query']                         = $meta_query;
            $query_vars['zib_bbs_admin_plate_filter_applied'] = 1;
        }

        unset($query_vars['cat'], $query_vars['bbs_plate_id'], $query_vars['plate_id']);

        return $query_vars;
    }

    public function admin_exclude_trash_posts_where($where, $query)
    {
        if (!$this->is_forum_post_admin_list_query($query)) {
            return $where;
        }

        $post_status = isset($_GET['post_status']) ? sanitize_key(wp_unslash($_GET['post_status'])) : '';
        if ('trash' === $post_status) {
            return $where;
        }

        global $wpdb;
        return $where . $wpdb->prepare(" AND {$wpdb->posts}.post_status <> %s", 'trash');
    }

    protected function is_forum_post_admin_list_request($query_vars)
    {
        if (!is_admin()) {
            return false;
        }

        global $pagenow;
        if ('edit.php' !== $pagenow) {
            return false;
        }

        $post_type = isset($query_vars['post_type']) ? $query_vars['post_type'] : '';
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }

        if (!$post_type && isset($_GET['post_type'])) {
            $post_type = sanitize_key(wp_unslash($_GET['post_type']));
        }

        return 'forum_post' === $post_type;
    }

    protected function is_forum_post_admin_list_query($query)
    {
        if (!is_admin() || !is_object($query) || !method_exists($query, 'is_main_query') || !$query->is_main_query()) {
            return false;
        }

        global $pagenow;
        if ('edit.php' !== $pagenow) {
            return false;
        }

        $post_type = $query->get('post_type');
        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }

        if (!$post_type && isset($_GET['post_type'])) {
            $post_type = sanitize_key(wp_unslash($_GET['post_type']));
        }

        return 'forum_post' === $post_type;
    }

    protected function get_admin_plate_id()
    {
        $plate_id = 0;

        if (isset($_GET['cat']) && is_string($_GET['cat'])) {
            $cat = sanitize_text_field(wp_unslash($_GET['cat']));
            if (0 === strpos($cat, 'plate_')) {
                $plate_id = absint(substr($cat, 6));
            }
        }

        if (!$plate_id && isset($_GET['bbs_plate_id']) && '' !== $_GET['bbs_plate_id']) {
            $plate_id = absint(wp_unslash($_GET['bbs_plate_id']));
        }

        if (!$plate_id && isset($_GET['plate_id']) && '' !== $_GET['plate_id']) {
            $plate_id = absint(wp_unslash($_GET['plate_id']));
        }

        return $plate_id;
    }

    // Plate columns
    public function plate_columns($columns)
    {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
        }
        $new_columns['posts_count'] = '帖子数';
        $new_columns['views']       = '浏览';
        $new_columns['follow_count'] = '关注';
        return $new_columns;
    }

    public function plate_custom_column($column_name, $post_id)
    {
        switch ($column_name) {
            case 'posts_count':
                echo (int) get_post_meta($post_id, 'posts_count', true);
                break;
            case 'views':
                echo (int) get_post_meta($post_id, 'views', true);
                break;
            case 'follow_count':
                echo (int) get_post_meta($post_id, 'follow_count', true);
                break;
        }
    }

    // Meta boxes
    public function add_meta_boxes()
    {
        add_meta_box('bbs_post_settings', '帖子设置', array($this, 'render_post_meta_box'), 'forum_post', 'side', 'high');
        add_meta_box('plate_settings', '版块设置', array($this, 'render_plate_meta_box'), 'plate', 'side', 'high');
    }

    public function render_post_meta_box($post)
    {
        wp_nonce_field('bbs_meta_box_nonce', 'bbs_meta_nonce');
        $plate_id = get_post_meta($post->ID, 'plate_id', true);
        $topping  = get_post_meta($post->ID, 'topping', true);
        $bbs_type = get_post_meta($post->ID, 'bbs_type', true);
        echo '<p><label>版块ID: </label><input type="number" name="plate_id" value="' . esc_attr($plate_id) . '" /></p>';
        echo '<p><label>置顶: </label><input type="number" name="topping" value="' . esc_attr($topping) . '" min="0" /></p>';
        echo '<p><label>类型: </label><input type="text" name="bbs_type" value="' . esc_attr($bbs_type) . '" /></p>';
    }

    public function render_plate_meta_box($post)
    {
        wp_nonce_field('bbs_meta_box_nonce', 'bbs_meta_nonce');
        $plate_type = get_post_meta($post->ID, 'plate_type', true);
        echo '<p><label>版块类型: </label><input type="text" name="plate_type" value="' . esc_attr($plate_type) . '" /></p>';
    }

    public function save_meta_boxes($post_id, $post)
    {
        if (!isset($_POST['bbs_meta_nonce']) || !wp_verify_nonce($_POST['bbs_meta_nonce'], 'bbs_meta_box_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type === 'forum_post') {
            $fields = array('plate_id', 'topping', 'bbs_type');
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
                }
            }
        }

        if ($post->post_type === 'plate') {
            if (isset($_POST['plate_type'])) {
                update_post_meta($post_id, 'plate_type', sanitize_text_field($_POST['plate_type']));
            }
        }
    }

    // Fallback for any missing method calls
    public function __call($name, $arguments)
    {
        return true;
    }

    public static function __callStatic($name, $arguments)
    {
        return true;
    }
}
