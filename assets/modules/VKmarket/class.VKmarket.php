<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKapi
{
    public function __construct($modx)
    {
        $this->modx = $modx;
        $this->moduleid = (int) $_GET['id'];
        $this->moduleurl = 'index.php?a=112&id=' . $this->moduleid;
        $this->theme = $this->modx->config['manager_theme'];
        $this->iconfolder = 'media/style/' . $this->theme . '/images/icons/';
    }
}
