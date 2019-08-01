<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKmarket
{
    public function __construct($modx)
    {
        $this->modx = $modx;
        $this->module_id = (int) $_GET['id'];
        $this->module_url = 'index.php?a=112&id=' . $this->moduleid;
        $this->theme = $this->modx->config['manager_theme'] ? $this->modx->config['manager_theme'] : 'default';
        $this->jquery_path = $this->modx->config['mgr_jquery_path'] ? $this->modx->config['mgr_jquery_path'] : 'media/script/jquery/jquery.min.js';
    }

    public function getFileContents($file)
    {
        if (empty($file)) {
            return false;
        } else {
            $file = MODX_BASE_PATH . 'assets/modules/VKmarket/templates/' . $file;
            $contents = file_get_contents($file);
            return $contents;
        }
    }
}
