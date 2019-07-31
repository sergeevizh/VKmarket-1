<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKmarket
{
    public function __construct($modx)
    {
        $this->modx = $modx;
        $this->moduleid = (int) $_GET['id'];
        $this->moduleurl = 'index.php?a=112&id=' . $this->moduleid;
        $this->theme = $this->modx->config['manager_theme'] ? $this->modx->config['manager_theme'] : 'default';
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

    public function parseTemplate($tpl, $values = array())
    {
        $tpl = $this->getFileContents($tpl);

        if ($tpl) {
            if (!isset($this->modx->config['mgr_jquery_path']))  $this->modx->config['mgr_jquery_path'] = 'media/script/jquery/jquery.min.js';
            $tpl = $this->modx->mergeSettingsContent($tpl);
            foreach ($values as $key => $value) {
                $tpl = str_replace('[+' . $key . '+]', $value, $tpl);
            }
            $tpl = preg_replace('/(\[\+.*?\+\])/', '', $tpl);
            return $tpl;
        } else {
            return '';
        }
    }
}
