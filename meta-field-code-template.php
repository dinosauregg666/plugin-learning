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
        add_action('admin_enqueue_scripts', array($this, 'loadMedia'));


        // 添加侧边栏菜单
        add_action('admin_menu', array($this, 'registerSidebarMenu'));
    }

    // 注册侧边栏菜单
    function registerSidebarMenu() {
        add_menu_page(
            '主页面标题',
            '主菜单',
            'manage_options',
            'my-plugin-main-menu',
            array($this, 'renderMainPage'),
            'dashicons-admin-generic',
            25
        );

        add_submenu_page(
            'my-plugin-main-menu', // 父菜单 slug
            '子页面标题',
            '子菜单名称',
            'manage_options',
            'my-plugin-sub-page',
            array($this, 'renderSubPage')
        );
    }
    function renderMainPage() {
        ?>
        <div class="wrap">
            <h1>我的插件主页面</h1>
            <p>这里是你侧边栏菜单点击后的内容。</p>
        </div>
        <?php
    }
    function renderSubPage() {
        echo '<div class="wrap"><h1>这是子页面</h1></div>';
    }



    // 添加媒体库
    function loadMedia() {
        wp_enqueue_media();
    }

    function settings() {
        // 3. 把设置好的东西放到前端页面
        add_settings_section('first_section', null, null, 'now-create-page-url-path');

        // 2. 设置前端信息
        add_settings_field('database_field_name', 'html form item label name', array($this, 'outputHtml'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name'));

        // 1. 注册数据库字段
        register_setting(
            'filed_group_name',
            'my_plugin_options',
            array(
                'default' => array(),
                'sanitize_callback' => array($this, 'sanitizeAllFields')
            )
        );


        // 2. 设置前端信息
        add_settings_field('database_field_name_1', 'html form item label name 1', array($this, 'inputHTML'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name_1'));


        // 2. 设置前端信息
        add_settings_field('database_field_name_2', 'html form item label name 2', array($this, 'checkboxHTML'), 'now-create-page-url-path',  'first_section', array('theName' => 'database_field_name_2'));

        // 可重复文本框
        add_settings_field(
            'repeater_field',
            '可重复输入框',
            array($this, 'repeaterHTML'),
            'now-create-page-url-path',
            'first_section'
        );

        // 图片媒体
        add_settings_field(
            'image_field',
            '选择图片',
            array($this, 'imageHTML'),
            'now-create-page-url-path',
            'first_section'
        );

        // 多选图片
        add_settings_field(
            'image_gallery',
            '多选图片',
            array($this, 'imageGalleryHTML'),
            'now-create-page-url-path',
            'first_section'
        );

        add_settings_field(
            'video_field',
            '选择视频',
            array($this, 'videoHTML'),
            'now-create-page-url-path',
            'first_section'
        );


    }
    
    // 自定义校验下拉选项数据
    function sanitizeAllFields($input) {
        $output = [];

        // select
        if (isset($input['database_field_name'])) {
            $output['database_field_name'] = in_array($input['database_field_name'], ['0','1'])
                ? $input['database_field_name']
                : '0';
        }

        // text
        if (isset($input['database_field_name_1'])) {
            $output['database_field_name_1'] = sanitize_text_field($input['database_field_name_1']);
        }

        // checkbox
        $output['database_field_name_2'] = isset($input['database_field_name_2']) ? '1' : '0';

        // Repeater（无限输入框）
        if (isset($input['repeater_field']) && is_array($input['repeater_field'])) {
            $output['repeater_field'] = array_map('wp_kses_post', $input['repeater_field']);
        }

        // 媒体
        if (isset($input['image_field'])) {
            $output['image_field'] = esc_url_raw($input['image_field']);
        }
        // 保存媒体 attachment ID
        if (isset($input['image_field_id'])) {
            $output['image_field_id'] = intval($input['image_field_id']);
        }

        // 多选图片
        if (isset($input['image_gallery']) && is_array($input['image_gallery'])) {
            $output['image_gallery'] = [];

            foreach ($input['image_gallery'] as $item) {
                if (!empty($item['id']) && !empty($item['url'])) {
                    $output['image_gallery'][] = [
                        'id'  => intval($item['id']),
                        'url' => esc_url_raw($item['url']),
                    ];
                }
            }
        }

        // 视频
        if (isset($input['video_field'])) {
            $output['video_field'] = esc_url_raw($input['video_field']);
        }

        if (isset($input['video_field_id'])) {
            $output['video_field_id'] = intval($input['video_field_id']);
        }

        return $output;
    }


    function myPlugin() {
        add_options_page('browser cart name', 'sidebar menu name', 'manage_options', 'now-create-page-url-path', array($this, 'output_now_create_page_html'));
    }


    function outputHtml($args) {
        $options = get_option('my_plugin_options');
        ?>
        <select name="my_plugin_options[<?php echo $args['theName']; ?>]">
            <option value="0" <?php selected($options[$args['theName']] ?? '', '0'); ?>>select 1</option>
            <option value="1" <?php selected($options[$args['theName']] ?? '', '1'); ?>>select 2</option>
        </select>
        <?php
    }


    // esc_attr 转义特殊字符，如 &, ", ', <, 和 >，确保它们在 HTML 中被正确显示而不是被执行。
    function inputHTML($args) {
        $options = get_option('my_plugin_options');
        ?>
        <input type="text"
            name="my_plugin_options[<?php echo $args['theName']; ?>]"
            value="<?php echo esc_attr($options[$args['theName']] ?? ''); ?>">
        <?php
    }

    function checkboxHTML($args) {
        $options = get_option('my_plugin_options');
        ?>
        <input type="checkbox"
            name="my_plugin_options[<?php echo $args['theName']; ?>]"
            value="1"
            <?php checked($options[$args['theName']] ?? '', '1'); ?>>
        <?php
    }

    // 重复文本框
    function repeaterHTML() {
        $options = get_option('my_plugin_options');
        $items = $options['repeater_field'] ?? [];
        $max_index = 0; 
        if (!empty($items)) { $max_index = max(array_keys($items)); }
        ?>
        <div id="repeater-wrapper">
            <input type="hidden" id="repeater-index" value="<?php echo $max_index; ?>">
            <?php foreach ($items as $index => $value): ?>
                <div class="repeater-item">
                    <input type="text" 
                        name="my_plugin_options[repeater_field][<?php echo $index; ?>]" 
                        value="<?php echo esc_attr($value); ?>" 
                        style="width:300px;">
                    <button type="button" class="button remove-item">删除</button>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="button button-primary" id="add-item">添加一项</button>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let wrapper = document.getElementById('repeater-wrapper');
                let addBtn = document.getElementById('add-item');
                let indexInput = document.getElementById('repeater-index');

                addBtn.addEventListener('click', function () {
                    let index = parseInt(indexInput.value) + 1;
                    indexInput.value = index; // 更新最大 index
                    let html = `
                        <div class="repeater-item">
                            <input type="text" 
                                name="my_plugin_options[repeater_field][${index}]" 
                                style="width:300px;">
                            <button type="button" class="button remove-item">删除</button>
                        </div>
                    `;
                    wrapper.insertAdjacentHTML('beforeend', html);
                });

                wrapper.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-item')) {
                        e.target.parentNode.remove();
                    }
                });
            });
        </script>

        <style>
            .repeater-item { margin-bottom: 10px; }
        </style>
        <?php
    }




// 媒体库选图片 
    function imageHTML() {
        $options = get_option('my_plugin_options');
        $value = $options['image_field'] ?? '';
        ?>
        <div class="image-field-wrapper">
            <input type="hidden"
                name="my_plugin_options[image_field_id]"
                id="image_field_id"
                value="<?php echo esc_attr($options['image_field_id'] ?? ''); ?>">

            <input type="text"
                name="my_plugin_options[image_field]"
                id="image_field_input"
                value="<?php echo esc_attr($value); ?>"
                style="width:300px;">

            <button type="button" class="button" id="image_field_button">选择图片</button>
            <button type="button" class="button" id="image_field_remove">移除</button>

            <div style="margin-top:10px;">
                <img id="image_field_preview"
                    src="<?php echo esc_url($value); ?>"
                    style="max-width:150px; <?php echo $value ? '' : 'display:none;'; ?>">
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let button = document.getElementById('image_field_button');
                let removeBtn = document.getElementById('image_field_remove');
                let input  = document.getElementById('image_field_input');
                let inputId = document.getElementById('image_field_id');
                let preview = document.getElementById('image_field_preview');

                let frame;

                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    frame = wp.media({
                        title: '选择图片',
                        multiple: false,
                        library: { type: 'image' }
                    });

                    // ⭐ 自动选中之前的图片（关键）
                    frame.on('open', function () {
                        let id = parseInt(inputId.value);
                        if (!id) return;

                        let selection = frame.state().get('selection');
                        let attachment = wp.media.attachment(id);

                        attachment.fetch(); // 必须 fetch，否则不会选中
                        selection.add(attachment);
                    });

                    // 选择图片
                    frame.on('select', function () {
                        let attachment = frame.state().get('selection').first().toJSON();

                        input.value = attachment.url;
                        inputId.value = attachment.id;

                        preview.src = attachment.url;
                        preview.style.display = 'block';
                    });

                    frame.open();
                });

                // 移除图片
                removeBtn.addEventListener('click', function () {
                    input.value = '';
                    inputId.value = '';
                    preview.src = '';
                    preview.style.display = 'none';
                });
            });
        </script>
        <?php
    }

    // 多选图片
    function imageGalleryHTML() {
        $options = get_option('my_plugin_options');
        $gallery = $options['image_gallery'] ?? [];
        ?>
        <div id="image-gallery-wrapper">
            <button type="button" class="button" id="image_gallery_button">添加图片</button>
            <div id="image-gallery-preview" style="margin-top:10px;">
                <?php foreach ($gallery as $index => $img): ?>
                    <div class="gallery-item" data-index="<?php echo $index; ?>">
                        <img src="<?php echo esc_url($img['url']); ?>">
                        <input type="hidden" name="my_plugin_options[image_gallery][<?php echo $index; ?>][id]" value="<?php echo esc_attr($img['id']); ?>">
                        <input type="hidden" name="my_plugin_options[image_gallery][<?php echo $index; ?>][url]" value="<?php echo esc_attr($img['url']); ?>">
                        <p>
                            <button type="button" class="button edit-gallery-item">修改</button>
                            <button type="button" class="button remove-gallery-item">删除</button>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <style>
            #image-gallery-preview {
                display: flex;
                gap: 30px;
                flex-wrap: wrap;
            }
            .gallery-item img { 
                margin-bottom:5px; 
                width: auto;
                height: 130px;
            }
            .gallery-item {
                display: flex;
                flex-direction: column;
                justify-content: end;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let addBtn = document.getElementById('image_gallery_button');
                let preview = document.getElementById('image-gallery-preview');
                let frame;
                // 添加图片（多选）
                addBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    frame = wp.media({
                        title: '选择图片',
                        multiple: true,
                        library: { type: 'image' }
                    });

                    frame.on('select', function () {
                        let attachments = frame.state().get('selection').toJSON();

                        attachments.forEach((attachment) => {
                            let index = Date.now() + Math.floor(Math.random() * 1000); // 唯一 index

                            let html = `
                                <div class="gallery-item" data-index="${index}" style="display:inline-block;margin-right:10px;">
                                    <img src="${attachment.url}" style="max-width:120px;">
                                    <input type="hidden" name="my_plugin_options[image_gallery][${index}][id]" value="${attachment.id}">
                                    <input type="hidden" name="my_plugin_options[image_gallery][${index}][url]" value="${attachment.url}">
                                    <button type="button" class="button edit-gallery-item">修改</button>
                                    <button type="button" class="button remove-gallery-item">删除</button>
                                </div>
                            `;
                            preview.insertAdjacentHTML('beforeend', html);
                        });
                    });

                    frame.open();
                });

                // 删除图片
                preview.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-gallery-item')) {
                        e.target.parentNode.remove();
                    }
                });

                // ⭐ 修改单张图片
                preview.addEventListener('click', function (e) {
                    if (!e.target.classList.contains('edit-gallery-item')) return;

                    let item = e.target.parentNode;
                    let idInput = item.querySelector('input[name*="[id]"]');
                    let urlInput = item.querySelector('input[name*="[url]"]');
                    let imgTag = item.querySelector('img');

                    let currentId = idInput.value;

                    frame = wp.media({
                        title: '修改图片',
                        multiple: false,
                        library: { type: 'image' }
                    });

                    // 自动选中当前图片
                    frame.on('open', function () {
                        if (!currentId) return;

                        let selection = frame.state().get('selection');
                        let attachment = wp.media.attachment(currentId);
                        attachment.fetch();
                        selection.add(attachment);
                    });

                    // 替换当前图片
                    frame.on('select', function () {
                        let attachment = frame.state().get('selection').first().toJSON();

                        idInput.value = attachment.id;
                        urlInput.value = attachment.url;
                        imgTag.src = attachment.url;
                    });

                    frame.open();
                });
            });
        </script>
    <?php
    }


    function videoHTML() {
        $options = get_option('my_plugin_options');
        $url = $options['video_field'] ?? '';
        $id  = $options['video_field_id'] ?? '';
        ?>

        <div class="video-field-wrapper">
            <input type="hidden"
                name="my_plugin_options[video_field_id]"
                id="video_field_id"
                value="<?php echo esc_attr($id); ?>">

            <input type="text"
                name="my_plugin_options[video_field]"
                id="video_field_input"
                value="<?php echo esc_attr($url); ?>"
                style="width:300px;">

            <button type="button" class="button" id="video_field_button">选择视频</button>
            <button type="button" class="button" id="video_field_remove">移除</button>

            <div style="margin-top:10px;">
                <?php if ($url): ?>
                    <video id="video_field_preview" src="<?php echo esc_url($url); ?>" controls style="max-width:300px;"></video>
                <?php else: ?>
                    <video id="video_field_preview" controls style="max-width:300px; display:none;"></video>
                <?php endif; ?>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let button = document.getElementById('video_field_button');
                let removeBtn = document.getElementById('video_field_remove');
                let input  = document.getElementById('video_field_input');
                let inputId = document.getElementById('video_field_id');
                let preview = document.getElementById('video_field_preview');

                let frame;

                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    frame = wp.media({
                        title: '选择视频',
                        multiple: false,
                        library: { type: 'video' } // 选择视频
                    });

                    // 自动选中之前的视频
                    frame.on('open', function () {
                        let id = parseInt(inputId.value);
                        if (!id) return;

                        let selection = frame.state().get('selection');
                        let attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment);
                    });

                    // 选择视频
                    frame.on('select', function () {
                        let attachment = frame.state().get('selection').first().toJSON();

                        input.value = attachment.url;
                        inputId.value = attachment.id;

                        preview.src = attachment.url;
                        preview.style.display = 'block';
                    });

                    frame.open();
                });

                // 移除视频
                removeBtn.addEventListener('click', function () {
                    input.value = '';
                    inputId.value = '';
                    preview.src = '';
                    preview.style.display = 'none';
                });
            });
        </script>
        <?php
    }

    // 输出整个表单
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
