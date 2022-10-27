<?php
/*
Plugin Name: TBC ip
Description: Плагин для вывода текста, в зависимости от установленных правил ip.
Version: 1.0
Author: Maxim Grishan
*/

defined('ABSPATH') or die('Fail.');

/**
 * Активация плагина
 */
function activate_tbc_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'inc/class-activate.php';
    $activate = new Activate();
}
register_activation_hook( __FILE__,'activate_tbc_plugin' );

/**
 * Деактивация плагина
 */
function deactivate_tbc_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'inc/class-deactivate.php';
    $deactivate = new Deactivate();
}
register_deactivation_hook( __FILE__,'deactivate_tbc_plugin' );

/**
 * Инициализирование плагина
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/class-init.php';
$Init = new Init();