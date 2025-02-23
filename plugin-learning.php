<?php
/**
 * plugin name: plugin learning
 * Text Domain: translatedomain
 * Domain Path: /languages
 */

class MyClassPlugin {
    function __construct() {
        add_action('admin_menu', array($this, 'myPlugin'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'ifSomethingFn'));
        add_action('init', array($this, 'languages'));
    }

    // 加载国际化翻译文件
    function languages() {
        load_plugin_textdomain('translatedomain', false,  dirname(plugin_basename(__FILE__)) . '/languages');
        // 注：用Loco Translate插件进行翻译
    }

    function ifSomethingFn($content) {
        if (
                (is_main_query() AND is_single())
                    AND
                (get_option('database_field_name_2', '1') OR get_option('database_field_name_3', '1') OR get_option('database_field_name_4', '1'))
        ) {
            return $this->createHTML($content);
        }
        return $content;
    }

    function createHTML($content) {
        if(get_option('database_field_name_2', '1')) {
            $wordCount = str_word_count(strip_tags($content)); // 统计字数，每个单词为一个字 // strip_tags 去掉html标签
            $strlen = strlen(strip_tags($content)); // 统计字符数，每个单词为一个字 // strip_tags 去掉html标签
        }

        // 使用正则表达式进行替换
        $content = preg_replace_callback(
            '/geekom mini pc/i',
            function($matches) {
                return '<a href="your-link-here" style="color: #009ac5;">' . esc_html($matches[0]) . '</a>'; // esc_html 转义标签为字符串
            },
            $content
        );
        return $content . ' || ' . esc_html__('Word Count', 'translatedomain') . ': ' . $wordCount . ' || ' . __('Strlen', 'translatedomain') . ': ' . $strlen;
    }


    function settings() {
        // 3. 把设置好的东西放到前端页面
        add_settings_section('first_section', null, null, 'now-create-page-url-path');

        // 2. 设置前端信息
        add_settings_field('database_field_name', 'html form item label name', array($this, 'outputHtml'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name'));

        // 1. 注册数据库字段
        register_setting(
            'filed_group_name', // 选项组名称
            'database_field_name',  // 选项名称
            array(
                'default' => '0', // 设置选项默认值 // 刚开始的default是不会保存到数据库中的，database_field_name
                'sanitize_callback' => array($this, 'customSanitizeFn'), // 设置选项数据过滤标准，得到的数据就是过滤后的数据，这里是过滤html标签，得到纯文本
            )
        );

        // 2. 设置前端信息
        add_settings_field('database_field_name_1', 'html form item label name 1', array($this, 'inputHTML'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name_1'));

        // 1. 注册数据库字段
        register_setting(
            'filed_group_name', // 选项组名称
            'database_field_name_1',  // 选项名称
            array(
                'default' => 'this is the default text', // 设置选项默认值
                'sanitize_callback' => 'sanitize_text_field', // 设置选项数据过滤标准，得到的数据就是过滤后的数据，这里是过滤html标签，得到纯文本
            )
        );

        // 2. 设置前端信息
        add_settings_field('database_field_name_2', 'html form item label name 2', array($this, 'checkboxHTML'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name_2'));

        // 1. 注册数据库字段
        register_setting(
            'filed_group_name', // 选项组名称
            'database_field_name_2',  // 选项名称
            array(
                'default' => '1', // 设置选项默认值
                'sanitize_callback' => 'sanitize_text_field', // 设置选项数据过滤标准，得到的数据就是过滤后的数据，这里是过滤html标签，得到纯文本
            )
        );

        // 2. 设置前端信息
        add_settings_field('database_field_name_3', 'html form item label name 3', array($this, 'checkboxHTML'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name_3'));

        // 1. 注册数据库字段
        register_setting(
            'filed_group_name', // 选项组名称
            'database_field_name_3',  // 选项名称
            array(
                'default' => '1', // 设置选项默认值
                'sanitize_callback' => 'sanitize_text_field', // 设置选项数据过滤标准，得到的数据就是过滤后的数据，这里是过滤html标签，得到纯文本
            )
        );

        // 2. 设置前端信息
        add_settings_field('database_field_name_4', 'html form item label name 4', array($this, 'checkboxHTML'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name_4'));

        // 1. 注册数据库字段
        register_setting(
            'filed_group_name', // 选项组名称
            'database_field_name_4',  // 选项名称
            array(
                'default' => '1', // 设置选项默认值
                'sanitize_callback' => 'sanitize_text_field', // 设置选项数据过滤标准，得到的数据就是过滤后的数据，这里是过滤html标签，得到纯文本
            )
        );
    }

    function customSanitizeFn($input) {
        // $input 前端发送过来的数据
        if($input != '0' AND $input != '1') {
            add_settings_error('database_field_name_1', 'database_field_name_1_error', 'database field name 1 error!');
            return get_option('database_field_name_1'); // 返回修改之前的数据
        }
        return $input;
    }

    function myPlugin() {
        add_options_page('browser cart name', __('sidebar menu name', 'translatedomain'), 'manage_options', 'now-create-page-url-path', array($this, 'output_now_create_page_html'));
    }

    function outputHtml() { ?>
        <select name="database_field_name">
            <option value="0" <?php selected(get_option('database_field_name'), '0') ?> >select 1</option>
            <option value="1" <?php selected(get_option('database_field_name'), '1') ?> >select 2</option>
        </select>
    <?php }

    // esc_attr 转义特殊字符，如 &, ", ', <, 和 >，确保它们在 HTML 中被正确显示而不是被执行。
    function outputHtml1() { ?>
        <input type="text" name="database_field_name_1" value="<?php echo esc_attr(get_option('database_field_name_1')); ?>">
    <?php }

    function outputHtml2() { ?>
        <input type="checkbox" name="database_field_name_2" value="1" <?php checked(get_option('database_field_name_2'), '1') ?> >
    <?php }


    function checkboxHTML($args) { ?>
        <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?> >
    <?php }

    function inputHTML($args) { ?>
        <input type="text" name="<?php echo $args['theName'] ?>" value="<?php echo esc_attr(get_option($args['theName'])); ?>">
    <?php }

    function output_now_create_page_html() { ?>
        <div class="wrap">
            <h1>My Plugin</h1>
            <form action="options.php" method="POST">
                <?php
                    settings_fields('filed_group_name'); // 作用：为这个字段组做nonce提交验证
                    do_settings_sections('now-create-page-url-path');
                    submit_button();
                ?>
            </form>
        </div>
    <?php }
}

$myClassPlugin = new MyClassPlugin();

