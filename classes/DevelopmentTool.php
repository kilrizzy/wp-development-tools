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
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        $this->createContentPopupsPostType();
        add_action('wp_enqueue_scripts',  array($this, 'enqueueAssets'), 999);
        add_action('wp_footer',  array($this, 'addPopupsToFooter'), 999);
    }

}