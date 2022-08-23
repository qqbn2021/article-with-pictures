<?php

/**
 * 基础类
 */
class Article_With_Pictures_Plugin
{
    // 启用插件
    public static function plugin_activation()
    {
        // 创建默认配置
        add_option('article_with_pictures_options', array(
            'list_image_text_color' => '#000000',
            'list_image_default_background_color' => '#dda0dd',
            'list_image_text_size' => 16,
            'list_image_text_multiline' => 1,
            'list_image_auto_update' => 1,
            'list_image_background_text' => 2,
            'list_image_text_overflow' => '...',
            'generate_image_type' => 2,
            'list_image_auto_save' => 2,
            'type' => 0,
            'list_image_width' => 480,
            'list_image_height' => 300,
            'content_image_type' => 0,
            'background_colors' => '#5b8982|#ffffff' . PHP_EOL . '#45545f|#cec6b6' . PHP_EOL . '#d47655|#e1f8e1' . PHP_EOL . '#7379b0|#c6edec'
        ));
    }

    // 删除插件执行的代码
    public static function plugin_uninstall()
    {
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
        global $article_with_pictures_options;
        // 表单的值
        $input_value = isset($article_with_pictures_options[$id]) ? $article_with_pictures_options[$id] : '';
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
            <form action="options.php" method="post" enctype="multipart/form-data">
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
     * @param string $html
     * @param int $post_id
     * @param int $post_thumbnail_id
     * @param int $size
     * @param array|string $attr
     * @return string
     */
    public static function post_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        // 不自动更新缩略图
        global $article_with_pictures_options;
        if (!empty($article_with_pictures_options['list_image_auto_update']) && 2 == $article_with_pictures_options['list_image_auto_update'] && !empty($html)) {
            return $html;
        }
        if ('post' === get_post_type($post_id)) {
            $alt_text = get_the_title($post_id);
            if (!empty($html)) {
                if (!empty($post_thumbnail_id)) {
                    $attached_file = get_attached_file($post_thumbnail_id);
                    if (!empty($attached_file)) {
                        $basename = basename($attached_file);
                        // 如果不是插件生成的或者是插件生成的，直接返回
                        if (!preg_match('/^article_with_pictures_plugin_[a-z0-9]{32}/i', $basename) || preg_match('/^article_with_pictures_plugin_' . preg_quote(self::get_image_key($alt_text)) . '/i', $basename)) {
                            return $html;
                        }
                    }
                    wp_delete_attachment($post_thumbnail_id, true);
                    delete_post_thumbnail($post_id);
                } else {
                    return $html;
                }
            }
            $attachment = self::generate_thumbnail($post_id, $alt_text);
            if (empty($attachment['guid'])) {
                return '';
            }
            if (!empty($html)) {
                return preg_replace('/src=["][^"]+["]/i', 'src="' . esc_url($attachment['guid']) . '"', $html);
            }
            return '<img class="attachment-post-thumbnail size-post-thumbnail wp-post-image" src="' . esc_url($attachment['guid']) . '" alt="' . esc_attr($alt_text) . '"/>';
        }
        return $html;
    }

    /**
     * 如果文章没有缩略图，则生成缩略图
     * @return void
     */
    public static function the_post()
    {
        global $post;
        if (!has_post_thumbnail($post->ID) && $post->post_status === 'publish') {
            // 没有缩略图，生成缩略图
            self::generate_thumbnail($post->ID, $post->post_title);
        }
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
                global $article_with_pictures_options;
                $post = get_post();
                if (!empty($post)) {
                    $image_html = get_the_post_thumbnail($post);
                    if (!empty($image_html) && !empty($article_with_pictures_options['content_image_type'])) {
                        $image_html = '<figure class="wp-block-image size-full aligncenter">' . $image_html . '</figure>';
                        switch ($article_with_pictures_options['content_image_type']) {
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
                                    if ($article_with_pictures_options['content_image_type'] == 2) {
                                        $image_start_num = intval(($p_nums - 1) / 2);
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
                        // 将缩略图内容永久保存到文章
                        if (!empty($article_with_pictures_options['list_image_auto_save']) && 1 == $article_with_pictures_options['list_image_auto_save']) {
                            wp_update_post(array(
                                'ID' => $post->ID,
                                'post_content' => $content,
                            ));
                        }
                    }
                }
            }
        }
        return $content;
    }

    /**
     * 生成文章缩略图
     * @param string $post_title 文章标题
     * @param int $post_id 文章ID
     * @return bool|array
     */
    public static function generate_thumbnail($post_id, $post_title)
    {
        global $article_with_pictures_options;
        if (empty($article_with_pictures_options['list_image_width'])) {
            return false;
        }
        if (empty($article_with_pictures_options['list_image_height'])) {
            return false;
        }
        $post_title = trim($post_title);
        $api = new Article_With_Pictures_Api($article_with_pictures_options['list_image_width'], $article_with_pictures_options['list_image_height']);
        // 设置图片唯一值
        if (!empty($article_with_pictures_options['list_image_auto_save']) && 1 == $article_with_pictures_options['list_image_auto_save']) {
            $api->setImageKey($post_id . '_permanent_' . self::get_image_key($post_title));
        } else {
            $api->setImageKey(self::get_image_key($post_title));
        }
        // 设置图片默认背景颜色
        if (!empty($article_with_pictures_options['list_image_default_background_color'])) {
            $default_background_rgb = $api->getRGB($article_with_pictures_options['list_image_default_background_color']);
            if (!empty($default_background_rgb)) {
                $api->setBackgroundRGB($default_background_rgb);
            }
        }
        // 添加缩略图文字
        if (!empty($article_with_pictures_options['list_image_background_text']) && 1 == $article_with_pictures_options['list_image_background_text']) {
            if (!empty($article_with_pictures_options['list_image_text_font'])) {
                $font_file = ARTICLE_WITH_PICTURES_PLUGIN_DIR . 'fonts/' . $article_with_pictures_options['list_image_text_font'];
                if (file_exists($font_file)) {
                    $api->setFontFile($font_file);
                    if (!empty($article_with_pictures_options['list_image_text_num'])) {
                        $api->setText(mb_substr($post_title, 0, $article_with_pictures_options['list_image_text_num'], 'utf-8'));
                    } else {
                        $api->setText($post_title);
                    }
                }
            }
            if (!empty($article_with_pictures_options['list_image_text_color'])) {
                $text_rgb = $api->getRGB($article_with_pictures_options['list_image_text_color']);
                if (empty($text_rgb)) {
                    return false;
                }
                $api->setTextRGB($text_rgb);
            }
            if (!empty($article_with_pictures_options['list_image_text_size'])) {
                $api->setFontSize($article_with_pictures_options['list_image_text_size']);
            }
            if (!empty($article_with_pictures_options['list_image_text_multiline'])) {
                $api->setIsMultiLine($article_with_pictures_options['list_image_text_multiline'] == '2');
            }
            if (!empty($article_with_pictures_options['list_image_text_overflow'])) {
                $api->setSingleLineText($article_with_pictures_options['list_image_text_overflow']);
            }
        }
        // 背景颜色
        if ($article_with_pictures_options['type'] == 1) {
            if (empty($article_with_pictures_options['background_colors'])) {
                return false;
            }
            // 换行转为数组
            $background_colors = explode(PHP_EOL, $article_with_pictures_options['background_colors']);
            if (empty($background_colors)) {
                return false;
            }
            // 获取有效的配置
            foreach ($background_colors as $k => $background_color) {
                if (empty($background_color) || !preg_match('/#[a-f0-9]{6}/i', $background_color)) {
                    unset($background_colors[$k]);
                }
            }
            if (empty($background_colors)) {
                return false;
            }
            // 打乱数组
            shuffle($background_colors);
            // 取第一个值
            $tmp = explode('|', $background_colors[0]);
            $background_rgb = $api->getRGB(trim($tmp[0]));
            if (empty($background_rgb)) {
                return false;
            }
            $api->setBackgroundRGB($background_rgb);
            if (2 == count($tmp)) {
                $text_rgb = $api->getRGB(trim($tmp[1]));
                if (empty($text_rgb)) {
                    return false;
                }
                $api->setTextRGB($text_rgb);
            }
        }
        $result = $api->getImage();
        if (empty($result)) {
            return false;
        }
        $post_mime_type = 'image/png';
        if (function_exists('mime_content_type')) {
            $post_mime_type = mime_content_type($result['path']);
        } else if (function_exists('imagewebp')) {
            $post_mime_type = 'image/webp';
        }
        $attachment = array(
            'post_mime_type' => $post_mime_type,
            'post_title' => $post_title,
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $result['url']
        );
        $attach_id = wp_insert_attachment($attachment, $result['path'], $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $result['url']);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);
        return $attachment;
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

    /**
     * 获取文章对应的唯一文件名
     * @param string $post_title
     * @return string
     */
    public static function get_image_key($post_title)
    {
        global $article_with_pictures_options;
        return md5('文章配图-' . $post_title . '-' . json_encode($article_with_pictures_options));
    }

    /**
     * 添加图片标签
     * @param $attr
     * @param $attachment
     * @return mixed
     */
    public static function wp_get_attachment_image_attributes($attr, $attachment = null)
    {
        if (empty($attr['alt'])) {
            $img_title = esc_attr(trim(strip_tags($attachment->post_title)));
            $attr['alt'] = $img_title;
        }
        return $attr;
    }
}