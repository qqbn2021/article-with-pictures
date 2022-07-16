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
            'timeout',
            // 输入框说明文字
            '请求超时时间',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'timeout',
                'form_type' => 'input',
                'type' => 'number',
                'form_desc' => '请求超时时间，默认为30秒'
            )
        );

        add_settings_field(
            'api_url',
            // 输入框说明文字
            '文章配图接口',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'api_url',
                'form_type' => 'input',
                'type' => 'url',
                'form_desc' => '文章配图接口请求地址'
            )
        );

        add_settings_field(
            'cdkey',
            // 输入框说明文字
            '授权码',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'cdkey',
                'form_type' => 'input',
                'type' => 'text',
                'form_desc' => '如果文章配图接口需要授权码，则需要填写。否则，无需填写'
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
            'default_image',
            // 输入框说明文字
            '默认缩略图',
            array('Article_With_Pictures_Plugin', 'field_callback'),
            'article_with_pictures_page',
            'article_with_pictures_page_section',
            array(
                'label_for' => 'default_image',
                'form_type' => 'input',
                'type' => 'url',
                'form_desc' => '如果缩略图无法生成，则使用此图片作为缩略图'
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