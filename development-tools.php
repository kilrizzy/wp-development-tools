<?php
/**
 * Plugin Name: Development Tools
 * Plugin URI: http://kilroyweb.com
 * Description: Classes and Scripts for Wordpress Development
 * Version: 0.1.0
 * Author: Jeff Kilroy
 * Author URI: http://kilroyweb.com
 * License: GPL2
 */

require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
    'https://github.com/kilrizzy/wp-development-tools/',
    __FILE__,
    'master'
);

if (!class_exists('DevelopmentTools\DevelopmentTool')) {
    require_once __DIR__ . '/classes/DevelopmentTool.php';
}
$developmentTool = new DevelopmentTools\DevelopmentTool([
    'pluginDir' => __DIR__,
    'pluginPath' => plugin_dir_url(__FILE__),
]);