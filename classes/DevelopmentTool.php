<?php

namespace DevelopmentTools;

class DevelopmentTool
{

    public $pluginDir = __DIR__;
    public $pluginPath;

    public function __construct($options = [])
    {
        if (isset($options['pluginDir'])) {
            $this->pluginDir = $options['pluginDir'];
        }
        if (isset($options['pluginPath'])) {
            $this->pluginPath = $options['pluginPath'];
        }
        add_action("activated_plugin", array($this, 'loadThisPluginFirst'));
        add_action('init', array($this, 'init'));
    }

    public function loadThisPluginFirst()
    {
        $wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR . "/$2", $this->pluginDir);
        $this_plugin = plugin_basename(trim($wp_path_to_this_file));
        $active_plugins = get_option('active_plugins');
        $this_plugin_key = array_search($this_plugin, $active_plugins);
        if ($this_plugin_key) {
            array_splice($active_plugins, $this_plugin_key, 1);
            array_unshift($active_plugins, $this_plugin);
            update_option('active_plugins', $active_plugins);
        }
    }

    public function init()
    {
        
    }

}