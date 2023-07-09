<?php

if ( !defined('ABSPATH') ) {
    exit;
} // Exit if accessed directly

/*if ( !function_exists('is_plugin_active') ) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}*/

class Module_service {

    public static function get_widget_settings($callable) {

        $settings_fields = [
            'element_pack_active_modules' => [
                [
                    'name'         => 'advanced-button',
                    'label'        => esc_html__('Advanced Button', 'bdthemes-element-pack'),
                    'type'         => 'checkbox',
                    'default'      => 'off',
                    'widget_type'  => 'pro',
                    'demo_url'     => 'https://www.elementpack.pro/demo/element/advanced-button/',
                    'video_url'    => 'https://youtu.be/Lq_st2IWZiE',
                ],
            ]
        ];

        $settings                    = [];
        $settings['settings_fields'] = $settings_fields;

        return $callable($settings);
    }

    private static function _is_plugin_installed($plugin, $plugin_path) {
        $installed_plugins = get_plugins();
        return isset($installed_plugins[$plugin_path]);
    }





    public static function has_module_style($module_id) {
        if ( file_exists(BDTEP_MODULES_PATH . $module_id . '/module.info.php') ) {
            $module_data = require BDTEP_MODULES_PATH . $module_id . '/module.info.php';

            if ( isset($module_data['has_style']) ) {
                return $module_data['has_style'];
            }
        }
    }

    public static function has_module_script($module_id) {
        if ( file_exists(BDTEP_MODULES_PATH . $module_id . '/module.info.php') ) {
            $module_data = require BDTEP_MODULES_PATH . $module_id . '/module.info.php';

            if ( isset($module_data['has_script']) ) {
                return $module_data['has_script'];
            }
        }
    }
}

