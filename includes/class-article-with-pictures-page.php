<?php

/**
 * 基础设置
 */
class Article_With_Pictures_Page
{
    // 初始化页面
    public static function init_page()
    {
        // 注册一个新页面
        register_setting('article_with_pictures_page', 'article_with_pictures_options');

        add_settings_section(
            'article_with_pictures_page_section',
            null,
            null,
            'article_with_pictures_page'
        );

        add_settings_field(
            'list_image_background_color',
            // 输入框说明文字
            '缩略图背景颜色',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_background_color',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '缩略图背景颜色，例如：#ffffff'
            )
        );

        add_settings_field(
            'list_image_text_color',
            // 输入框说明文字
            '缩略图文字颜色',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_text_color',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '缩略图文字颜色，例如：#000000'
            )
        );

        add_settings_field(
            'list_image_text_size',
            // 输入框说明文字
            '缩略图文字大小',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_text_size',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '缩略图文字大小，例如：16'
            )
        );

        // 查询字体文件
        $form_data = array();
        $form_data[] = array(
            'title' => '选择字体文件',
            'value' => '0'
        );
        $font_dir = ARTICLE_WITH_PICTURES_PLUGIN_DIR . 'fonts';
        $files = scandir($font_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (preg_match('/\.ttf$/i', $file)) {
                $form_data[] = array(
                    'title' => $file,
                    'value' => $file
                );
            }
        }
        add_settings_field(
            'list_image_text_font',
            // 输入框说明文字
            '缩略图文字字体',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_text_font',
                'form_type' => 'select',
                'form_data' => $form_data,
                'form_desc' => '请使用免费可商用的中文字体'
            )
        );

        add_settings_field(
            'list_image_text_multiline',
            // 输入框说明文字
            '缩略图文字单行显示',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_text_multiline',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '文字是否在缩略图上显示一行，如果设置为否，则会在缩略图上显示多行文字'
            )
        );

        add_settings_field(
            'list_image_text_overflow',
            // 输入框说明文字
            '文字超出图片宽度后的替代文字',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_text_overflow',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '如果文字超出了缩略图的宽度，则剩余的文字使用这里的替代文字显示'
            )
        );

        add_settings_field(
            'list_image_width',
            // 输入框说明文字
            '缩略图宽度',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_width',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '文章列表页面中的缩略图宽度'
            )
        );

        add_settings_field(
            'list_image_height',
            // 输入框说明文字
            '缩略图高度',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'list_image_height',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '文章列表页面中的缩略图高度'
            )
        );

        add_settings_field(
            'generate_image_type',
            // 输入框说明文字
            '主动生成特色图片',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'generate_image_type',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '是',
                        'value' => '1'
                    ),
                    array(
                        'title' => '否',
                        'value' => '2'
                    )
                ),
                'form_desc' => '如果文章没有特色图片，设置为是将会主动生成特色图片。'
            )
        );

        add_settings_field(
            'content_image_type',
            // 输入框说明文字
            '文章内容显示缩略图',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'content_image_type',
                'form_type' => 'select',
                'form_data' => array(
                    array(
                        'title' => '不显示',
                        'value' => '0'
                    ),
                    array(
                        'title' => '开头',
                        'value' => '1'
                    ),
                    array(
                        'title' => '中间',
                        'value' => '2'
                    ),
                    array(
                        'title' => '结尾',
                        'value' => '3'
                    ),
                    array(
                        'title' => '随机',
                        'value' => '4'
                    )
                )
            )
        );
    }
}