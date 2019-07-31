<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/modules/VKmarket/class.VKmarket.php';

$market = new VKmarket($modx);

$placeholders = array(
    "theme" = $market->theme,
    "moduleid" = $market->moduleid,
    "moduleurl" = $market->moduleurl
);

$output = $market->parseTemplate('main.tpl', $placeholders);
echo $output;
