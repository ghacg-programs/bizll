<?php
/**
 * zib-code.php - 主题选项代码
 * 替代原加密文件，移除MD5完整性检查，提供安全的更新UI函数
 */

// 在线更新发送 - 空实现
function zib_send_online_update()
{
    return false;
}

// AJAX下载更新 - 空实现
function zib_ajax_zibll_download_update()
{
    wp_send_json(array(
        'error' => false,
        'msg'   => '当前已是最新版本，无需更新。',
    ));
}

// AJAX更新文件 - 空实现
function zib_ajax_zibll_update_file()
{
    wp_send_json(array(
        'error' => false,
        'msg'   => '当前已是最新版本，无需更新。',
    ));
}

// 更新文件清理 - 空实现
function update_file_clear()
{
    return true;
}

// 获取管理后台授权通知
function zib_get_admin_aut_notice()
{
    return '';
}
