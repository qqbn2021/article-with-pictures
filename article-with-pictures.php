<?php
/**
 * Plugin Name:文章配图
 * Plugin URI:https://www.ggdoc.cn/plugin/3.html
 * Description:如果文章没有缩略图，可以通过本插件自动给文章生成缩略图，同时支持在文章内容页显示缩略图。
 * Version:0.0.1
 * Requires at least: 5.0
 * Requires PHP:5.3
 * Author:果果开发
 * Author URI:https://www.ggdoc.cn
 * License:GPL v2 or later
 */

// 直接访问报404错误
if (!function_exists('add_action')) {
    http_response_code(404);
    exit;
}
if (defined('ARTICLE_WITH_PICTURES_PLUGIN_DIR')) {
    // 在我的插件那添加重名插件说明
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Article_With_Pictures_Plugin', 'duplicate_name'));
    return;
}
// 插件目录后面有 /
const ARTICLE_WITH_PICTURES_PLUGIN_FILE = __FILE__;
define('ARTICLE_WITH_PICTURES_PLUGIN_DIR', plugin_dir_path(ARTICLE_WITH_PICTURES_PLUGIN_FILE));
// 定义配置
$article_with_pictures_option = get_option('article_with_pictures_options', array());

/**
 * 自动加载
 * @param string $class
 * @return void
 */
function article_with_pictures_autoload($class)
{
    $class_file = ARTICLE_WITH_PICTURES_PLUGIN_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
    if (file_exists($class_file)) {
        require_once $class_file;
    }
}

spl_autoload_register('article_with_pictures_autoload');

// 启用插件
register_activation_hook(ARTICLE_WITH_PICTURES_PLUGIN_FILE, array('Article_With_Pictures_Plugin', 'plugin_activation'));
// 删除插件
register_uninstall_hook(ARTICLE_WITH_PICTURES_PLUGIN_FILE, array('Article_With_Pictures_Plugin', 'plugin_uninstall'));
// 添加页面
add_action('admin_init', array('Article_With_Pictures_Plugin', 'admin_init'));
// 添加菜单
add_action('admin_menu', array('Article_With_Pictures_Plugin', 'admin_menu'));
// 在我的插件那添加设置的链接
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Article_With_Pictures_Plugin', 'link_setting'));
// 修改文章缩略图
if (!empty($article_with_pictures_option['api_url']) && !empty($article_with_pictures_option['list_image_width']) && !empty($article_with_pictures_option['list_image_height'])) {
    add_filter('post_thumbnail_html', array('Article_With_Pictures_Plugin', 'post_thumbnail_html'), 10, 2);
}
if (!empty($article_with_pictures_option['content_image_type'])) {
    add_filter('the_content', array('Article_With_Pictures_Plugin', 'the_content'), 1);
}