<?php

/**
 * 基础类
 */
class Article_With_Pictures_Plugin
{
    // 启用插件
    public static function plugin_activation()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'article_with_pictures';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = <<<SQL
CREATE TABLE $table_name (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`post_id` INT(10) UNSIGNED NOT NULL COMMENT '文章ID',
	`title` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '文章标题' COLLATE 'utf8mb4_general_ci',
	`width` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '缩略图宽度',
	`height` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '缩略图高度',
	`thumbnail_file` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '文章图片存储位置' COLLATE 'utf8mb4_general_ci',
	`thumbnail` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '文章缩略图' COLLATE 'utf8mb4_general_ci',
	`create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
	`update_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
	PRIMARY KEY (`id`) USING BTREE
) $charset_collate;
SQL;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//	dbDelta( $sql );
        // 如果表不存在才会执行创建
        maybe_create_table($table_name, $sql);
        // 创建默认配置
        add_option('article_with_pictures_options', array(
            'cdkey' => '',
            'timeout' => 30,
            'domain' => parse_url(get_home_url(), PHP_URL_HOST),
        ));
    }

    // 删除插件执行的代码
    public static function plugin_uninstall()
    {
        // 删除表
        global $wpdb;
        $table_name = $wpdb->prefix . 'article_with_pictures';
        $wpdb->query('DROP TABLE IF EXISTS `' . $table_name . '`');
        // 删除配置
        delete_option('article_with_pictures_options');
    }

    /**
     * 表单输入框回调
     *
     * @param array $args 这数据就是add_settings_field方法中第6个参数（$args）的数据
     */
    public static function field_callback($args)
    {
        // 表单的id或name字段
        $id = $args['label_for'];
        // 表单的名称
        $input_name = 'article_with_pictures_options[' . $id . ']';
        // 获取表单选项中的值
        global $article_with_pictures_option;
        // 表单的值
        $input_value = isset($article_with_pictures_option[$id]) ? $article_with_pictures_option[$id] : '';
        // 表单的类型
        $form_type = isset($args['form_type']) ? $args['form_type'] : 'input';
        // 输入表单说明
        $form_desc = isset($args['form_desc']) ? $args['form_desc'] : '';
        // 输入表单type
        $type = isset($args['type']) ? $args['type'] : 'text';
        // 输入表单placeholder
        $form_placeholder = isset($args['form_placeholder']) ? $args['form_placeholder'] : '';
        // 下拉框等选项值
        $form_data = isset($args['form_data']) ? $args['form_data'] : array();
        // 扩展form表单属性
        $form_extend = isset($args['form_extend']) ? $args['form_extend'] : array();
        switch ($form_type) {
            case 'input':
                self::generate_input(
                    array_merge(
                        array(
                            'id' => $id,
                            'type' => $type,
                            'placeholder' => $form_placeholder,
                            'name' => $input_name,
                            'value' => $input_value,
                            'class' => 'regular-text',
                        ),
                        $form_extend
                    ));
                break;
            case 'select':
                self::generate_select(
                    array_merge(
                        array(
                            'id' => $id,
                            'placeholder' => $form_placeholder,
                            'name' => $input_name
                        ),
                        $form_extend
                    ),
                    $form_data,
                    $input_value
                );
                break;
            case 'checkbox':
                self::generate_checkbox(
                    array_merge(
                        array(
                            'name' => $input_name . '[]'
                        ),
                        $form_extend
                    ),
                    $form_data,
                    $input_value
                );
                break;
            case 'textarea':
                self::generate_textarea(
                    array_merge(
                        array(
                            'id' => $id,
                            'placeholder' => $form_placeholder,
                            'name' => $input_name,
                            'class' => 'large-text code',
                            'rows' => 5,
                        ),
                        $form_extend
                    ),
                    $input_value
                );
                break;
        }
        if (!empty($form_desc)) {
            ?>
            <p class="description"><?php echo esc_html($form_desc); ?></p>
            <?php
        }
    }

    /**
     * 生成textarea表单
     * @param array $form_data 标签上的属性数组
     * @param string $value 默认值
     * @return void
     */
    public static function generate_textarea($form_data, $value = '')
    {
        ?><textarea <?php
        foreach ($form_data as $k => $v) {
            echo esc_attr($k); ?>="<?php echo esc_attr($v); ?>" <?php
        } ?>><?php echo esc_textarea($value); ?></textarea>
        <?php
    }

    /**
     * 生成checkbox表单
     * @param array $form_data 标签上的属性数组
     * @param array $checkboxs 下拉列表数据
     * @param string|array $value 选中值，单个选中字符串，多个选中数组
     * @return void
     */
    public static function generate_checkbox($form_data, $checkboxs, $value = '')
    {
        ?>
        <fieldset><p>
                <?php
                $len = count($checkboxs);
                foreach ($checkboxs as $k => $checkbox) {
                    $checked = '';
                    if (!empty($value)) {
                        if (is_array($value)) {
                            if (in_array($checkbox['value'], $value)) {
                                $checked = 'checked';
                            }
                        } else {
                            if ($checkbox['value'] == $value) {
                                $checked = 'checked';
                            }
                        }
                    }
                    ?>
                    <label>
                        <input type="checkbox" <?php checked($checked, 'checked'); ?><?php
                        foreach ($form_data as $k2 => $v2) {
                            echo esc_attr($k2); ?>="<?php echo esc_attr($v2); ?>" <?php
                        } ?> value="<?php echo esc_attr($checkbox['value']); ?>"
                        ><?php echo esc_html($checkbox['title']); ?>
                    </label>
                    <?php
                    if ($k < ($len - 1)) {
                        ?>
                        <br>
                        <?php
                    }
                }
                ?>
            </p></fieldset>
        <?php
    }

    /**
     * 生成input表单
     * @param array $form_data 标签上的属性数组
     * @return void
     */
    public static function generate_input($form_data)
    {
        ?><input <?php
        foreach ($form_data as $k => $v) {
            echo esc_attr($k); ?>="<?php echo esc_attr($v); ?>" <?php
        } ?>><?php
    }

    /**
     * 生成select表单
     * @param array $form_data 标签上的属性数组
     * @param array $selects 下拉列表数据
     * @param string|array $value 选中值，单个选中字符串，多个选中数组
     * @return void
     */
    public static function generate_select($form_data, $selects, $value = '')
    {
        ?><select <?php
        foreach ($form_data as $k => $v) {
            echo esc_attr($k); ?>="<?php echo esc_attr($v); ?>" <?php
        } ?>><?php
        foreach ($selects as $select) {
            $selected = '';
            if (!empty($value)) {
                if (is_array($value)) {
                    if (in_array($select['value'], $value)) {
                        $selected = 'selected';
                    }
                } else {
                    if ($select['value'] == $value) {
                        $selected = 'selected';
                    }
                }
            }
            ?>
            <option <?php selected($selected, 'selected'); ?>
                    value="<?php echo esc_attr($select['value']); ?>"><?php echo esc_html($select['title']); ?></option>
            <?php
        }
        ?>
        </select>
        <?php
    }

    // 初始化
    public static function admin_init()
    {
        // 注册设置页面
        Article_With_Pictures_Page::init_page();
    }

    // 添加菜单
    public static function admin_menu()
    {
        // 设置页面
        add_options_page(
            '文章配图',
            '文章配图',
            'manage_options',
            'article-with-pictures-setting',
            array('Article_With_Pictures_Plugin', 'show_page')
        );
    }

    // 显示设置页面
    public static function show_page()
    {
        // 检查用户权限
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // 输出表单
                settings_fields('article_with_pictures_page');
                do_settings_sections('article_with_pictures_page');
                // 输出保存设置按钮
                submit_button('保存更改');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * 添加设置链接
     * @param array $links
     * @return array
     */
    public static function link_setting($links)
    {
        $business_link = '<a href="https://www.ggdoc.cn/plugin/3.html" target="_blank">商业版</a>';
        array_unshift($links, $business_link);

        $settings_link = '<a href="options-general.php?page=article-with-pictures-setting">设置</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    /**
     * 列表页面缩略图
     * @param $html
     * @param $post_id
     * @return string
     */
    public static function post_thumbnail_html($html, $post_id)
    {
        if (empty($html) && 'post' === get_post_type($post_id)) {
            $alt_text = get_the_title($post_id);
            $image_src = self::get_article_image($post_id, $alt_text);
            if (empty($image_src)) {
                return '';
            }
            return '<img src="' . esc_url($image_src) . '" alt="' . esc_attr($alt_text) . '"/>';
        }
        return $html;
    }

    /**
     * 文章内容添加缩略图
     * @param $content
     * @return string
     */
    public static function the_content($content)
    {
        if (is_single()) {
            if (!preg_match('/<img/i', $content)) {
                global $article_with_pictures_option;
                $post = get_post();
                if (!empty($post)) {
                    $post_thumbnail = self::get_article_image($post->ID, $post->post_title);
                    if (!empty($post_thumbnail) && !empty($article_with_pictures_option['content_image_type'])) {
                        $image_html = '<figure class="wp-block-image size-full"><img loading="lazy" src="' . esc_url($post_thumbnail) . '" alt="' . esc_attr($post->post_title) . '"/></figure>';
                        switch ($article_with_pictures_option['content_image_type']) {
                            case 1:
                                $content = $image_html . $content;
                                break;
                            case 3:
                                $content = $content . $image_html;
                                break;
                            default:
                                $image_start_num = 0;
                                if (preg_match_all('/<p[^>]*>/i', $content, $mat)) {
                                    $p_nums = count($mat[0]);
                                    if ($article_with_pictures_option['content_image_type'] == 2) {
                                        $image_start_num = intval($p_nums / 2);
                                    } else {
                                        $image_start_num = mt_rand(0, $p_nums - 1);
                                    }
                                }
                                $content = preg_replace_callback('/<p[^>]*>(.*)<\/p>/i', function ($matches) use ($image_start_num, $image_html) {
                                    static $i = -1;
                                    $i++;
                                    if ($i == $image_start_num) {
                                        return $matches[0] . $image_html;
                                    }
                                    return $matches[0];
                                }, $content);
                        }
                    }
                }
            }
        }
        return $content;
    }

    /**
     * 获取文章缩略图
     * @param string $title 文章标题
     * @param int $post_id 文章ID
     * @return mixed|string
     */
    public static function get_image($title = '', $post_id = 0)
    {
        global $article_with_pictures_option;
        if (!empty($article_with_pictures_option['domain'])) {
            $domain = $article_with_pictures_option['domain'];
        } else {
            $domain = parse_url(get_home_url(), PHP_URL_HOST);
        }
        if (!empty($article_with_pictures_option['cdkey'])) {
            $cdkey = $article_with_pictures_option['cdkey'];
        } else {
            $cdkey = '';
        }
        $default_image = !empty($article_with_pictures_option['default_image']) ? $article_with_pictures_option['default_image'] : '';
        if (!empty($article_with_pictures_option['api_url'])) {
            // 使用接口获取图片
            $timeout = 30;
            if (!empty($article_with_pictures_option['timeout'])) {
                $timeout = (int)$article_with_pictures_option['timeout'];
            }
            $response = wp_remote_post($article_with_pictures_option['api_url'], array(
                'timeout' => $timeout,
                'headers' => array(
                    'GGDEV-CDKEY' => $cdkey,
                    'GGDEV-ACTIVATE-DOMAIN' => $domain
                ),
                'body' => array(
                    'title' => $title,
                    'post_id' => $post_id,
                    'image_width' => $article_with_pictures_option['list_image_width'],
                    'image_height' => $article_with_pictures_option['list_image_height'],
                )
            ));
            if (is_wp_error($response)) {
                return $default_image;
            }
            $code = (int)wp_remote_retrieve_response_code($response);
            if ($code !== 200) {
                return $default_image;
            }
            $result = wp_remote_retrieve_body($response);
            if (empty($result)) {
                return $default_image;
            }
            $result_arr = json_decode($result, true);
            if (empty($result_arr)) {
                return $default_image;
            }
            if ($result_arr['status'] != 1) {
                return $default_image;
            }
            if (empty($result_arr['data'])) {
                return $default_image;
            }
            if (preg_match('/^http/i', $result_arr['data'])) {
                self::save_article_image($post_id, $title, $result_arr['data']);
                return $result_arr['data'];
            } else if (preg_match('/^data:image/i', $result_arr['data'])) {
                // 先将图片存储在本地，然后返回本地图片链接地址
                $upload_dir = wp_upload_dir();
                $reg = '/data:image\/([^;]+);base64,/i';
                $image_ext = 'jpeg';
                if (preg_match($reg, $result_arr['data'], $mat)) {
                    $image_ext = strtolower($mat[1]);
                }
                if (!in_array($image_ext, array('bmp', 'gif', 'ico', 'jpeg', 'jpg', 'png', 'svg', 'tif', 'tiff', 'webp'))) {
                    return $default_image;
                }
                $img = preg_replace($reg, '', $result_arr['data']);
                $img = base64_decode($img);
                if (empty($img)) {
                    return $default_image;
                }
                // 检查图片是否为真实的图片
                $im = @imagecreatefromstring($img);
                if (!$im) {
                    return $default_image;
                }
                imagedestroy($im);
                $img_filename = md5($post_id) . '.' . $image_ext;
                if (!file_put_contents($upload_dir['path'] . '/' . $img_filename, $img)) {
                    return $default_image;
                }
                self::save_article_image($post_id, $title, $upload_dir['url'] . '/' . $img_filename, $upload_dir['subdir'] . '/' . $img_filename);
                return $upload_dir['url'] . '/' . $img_filename;
            } else {
                return $default_image;
            }
        }
        return $default_image;
    }

    /**
     * 保存文章配图
     * @param int $post_id 文章ID
     * @param string $title 文章标题
     * @param string $thumbnail 文章缩略图
     * @param string $thumbnail_file 缩略图存储位置
     * @return bool|int|mysqli_result|resource|null
     */
    public static function save_article_image($post_id, $title, $thumbnail, $thumbnail_file = '')
    {
        global $article_with_pictures_option;
        global $wpdb;
        $table_name = $wpdb->prefix . 'article_with_pictures';
        $sql = 'INSERT INTO `' . $table_name . '` (`post_id`,`title`,`width`,`height`,`thumbnail_file`,`thumbnail`) values(%d,%s,%d,%d,%s,%s)';
        $query = $wpdb->prepare(
            $sql,
            $post_id,
            $title,
            $article_with_pictures_option['list_image_width'],
            $article_with_pictures_option['list_image_height'],
            $thumbnail_file,
            $thumbnail
        );
        return $wpdb->query($query);
    }

    /**
     * 删除文章配图
     * @param int $post_id 文章ID
     * @return bool|int|mysqli_result|resource|null
     */
    public static function delete_article_image($post_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'article_with_pictures';
        $sql = 'DELETE FROM `' . $table_name . '` WHERE `post_id` = %d';
        $query = $wpdb->prepare(
            $sql,
            $post_id
        );
        return $wpdb->query($query);
    }

    /**
     * 获取当前文章缩略图
     * @param int $post_id 文章ID
     * @param string $title 文章标题
     * @return mixed
     */
    public static function get_article_image($post_id, $title)
    {
        global $wpdb;
        global $article_with_pictures_option;
        $table_name = $wpdb->prefix . 'article_with_pictures';
        $sql = 'SELECT `title`,`width`,`height`,`thumbnail` FROM `' . $table_name . '` WHERE `post_id` = %d LIMIT 1';
        $query = $wpdb->prepare(
            $sql,
            $post_id
        );
        $result = $wpdb->get_results($query, 'ARRAY_A');
        if (empty($result[0])) {
            return self::get_image($title, $post_id);
        }
        // 文章标题变更，或者缩略图大小变了，重新生成
        if ($title != $result[0]['title'] || $article_with_pictures_option['list_image_width'] != $result[0]['width'] || $article_with_pictures_option['list_image_height'] != $result[0]['height']) {
            self::delete_article_image($post_id);
            return self::get_image($title, $post_id);
        }
        return $result[0]['thumbnail'];
    }

    /**
     * 在插件页面添加同名插件处理问题
     *
     * @param $links
     *
     * @return mixed
     */
    public static function duplicate_name($links)
    {
        $settings_link = '<a href="https://www.ggdoc.cn/plugin/3.html" target="_blank">请删除其它版本《文章配图》插件</a>';
        array_unshift($links, $settings_link);

        return $links;
    }
}