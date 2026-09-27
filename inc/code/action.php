<?php
/**
 * AJAX authorization and update stubs.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_admin_curl_aut', 'zib_admin_curl_aut');
add_action('wp_ajax_admin_delete_aut', 'zib_admin_delete_aut');
add_action('wp_ajax_admin_detect_update', 'zib_admin_detect_update');
add_action('wp_ajax_admin_skip_update', 'zib_admin_skip_update');
add_action('wp_ajax_zibll_online_update', 'zib_admin_online_update');

function zib_admin_aut_add_action()
{
    return true;
}

function zib_admin_ajax_can_manage()
{
    return current_user_can('manage_options') || (function_exists('is_super_admin') && is_super_admin());
}

function zib_admin_ajax_send($data)
{
    if (function_exists('wp_send_json')) {
        wp_send_json($data);
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function zib_admin_ajax_permission_check()
{
    if (!zib_admin_ajax_can_manage()) {
        zib_admin_ajax_send(array(
            'error' => true,
            'msg'   => __('权限不足', 'zib_language'),
        ));
    }
}

function zib_admin_curl_aut()
{
    zib_admin_ajax_permission_check();

    zib_admin_ajax_send(array(
        'error'  => false,
        'msg'    => __('授权成功', 'zib_language'),
        'button' => __('操作成功', 'zib_language'),
        'reload' => true,
        'data'   => class_exists('ZibAut') ? ZibAut::get_aut_data() : array('status' => 'active'),
    ));
}

function zib_admin_delete_aut()
{
    zib_admin_ajax_permission_check();

    zib_admin_ajax_send(array(
        'error'  => false,
        'msg'    => __('操作成功', 'zib_language'),
        'button' => __('操作成功', 'zib_language'),
        'reload' => true,
    ));
}

function zib_admin_detect_update()
{
    zib_admin_ajax_permission_check();

    zib_admin_ajax_send(array(
        'error'  => false,
        'msg'    => __('当前主题已经是最新版本', 'zib_language'),
        'button' => __('检测更新', 'zib_language'),
    ));
}

function zib_admin_skip_update()
{
    zib_admin_ajax_permission_check();

    zib_admin_ajax_send(array(
        'error'  => false,
        'msg'    => __('已忽略此次更新', 'zib_language'),
        'button' => __('忽略此次更新', 'zib_language'),
    ));
}

function zib_admin_online_update()
{
    zib_admin_ajax_permission_check();

    zib_admin_ajax_send(array(
        'error'  => true,
        'msg'    => __('当前本地版本未启用在线更新，请手动更新主题', 'zib_language'),
        'button' => __('在线更新', 'zib_language'),
    ));
}

function zib_is_local()
{
    return false;
}
