<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/modules/VKmarket/class.VKmarket.php';

$market = new VKmarket($modx);

$items = $modx->runSnippet('DocLister', array(
    'parents' => 0,
    'display' => 20,
    'depth' => 10,
    'showParent' => 1,
    'addWhereList' => 'c.template=' . $template_item,
    'tvList' => $tv_list,
    'tpl' => '@FILE:VKmarket/module_item',
    'ownerTPL' => '@FILE:VKmarket/module_items_wrap',
    'paginate' => 'pages'
));

$albums = $modx->runSnippet('DocLister', array(
    'parents' => 0,
    'display' => 20,
    'depth' => 10,
    'showParent' => 1,
    'addWhereList' => 'c.template=' . $template_album,
    'tvList' => $tv_list,
    'tpl' => '@FILE:VKmarket/module_album',
    'ownerTPL' => '@FILE:VKmarket/module_albums_wrap',
    'paginate' => 'pages'
));

$placeholders = array(
    'theme' => $market->theme,
    'module_id' => $market->module_id,
    'module_url' => $market->module_url,
    'jquery_path' => $market->jquery_path,
    'items' => $items,
    'albums' => $albums
);

$tpl = $market->getFileContents('main.html');
$output = $modx->parseText($tpl, $placeholders);
echo $output;
