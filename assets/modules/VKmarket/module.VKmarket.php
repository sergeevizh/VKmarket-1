<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/modules/VKmarket/class.VKmarket.php';

// Проверяем заполненность параметров конфигурации
$error_list = "";

if (!isset($access_token)) {
    $error_list = $error_list . '<li><b>access_token</b> - VK access_token</li>';
}

if (!isset($group_id)) {
    $error_list = $error_list . '<li><b>group_id</b> - VK group_id</li>';
}

if (!isset($template_item)) {
    $error_list = $error_list . '<li><b>template_item</b> - ID шаблона товаров</li>';
}

if (!isset($template_album)) {
    $error_list = $error_list . '<li><b>template_album</b> - ID шаблона категорий (подборок)</li>';
}

// Генерируем фронт модуля
$market = new VKmarket($modx);
$tpl = $market->getFileContents('main.html');

// Если есть ошибки
if ($error_list !== "") {
    $errors = '<div class="tab-page">
        <div class="tab-body">
            <div class="alert alert-danger"><b>Ошибка конфигурации модуля VKmarket</b></div>
            <div class="tab-section">
                <div class="tab-body">
                    <p>Не заполнены следующие поля:</p>
                    <ul>' . $error_list . '</ul>
                </div>
            </div>
        </div>
    </div>';

    $placeholders = array(
        'errors' => $errors,
        'theme' => $market->theme,
        'module_id' => $market->module_id,
        'module_url' => $market->module_url,
        'jquery_path' => $market->jquery_path,
        'items' => '',
        'albums' => ''
    );

    $output = $modx->parseText($tpl, $placeholders);
    echo $output;
    return;
}

$items = $modx->runSnippet('DocLister', array(
    'id' => 'items',
    'parents' => 0,
    'showParent' => 1,
    'showNoPublish' => 1,
    'depth' => 10,
    'addWhereList' => 'c.template=' . $template_item,
    'tvList' => $tv_list,
    'tpl' => '@FILE:VKmarket/module_item',
    'ownerTPL' => '@FILE:VKmarket/module_items_wrap',
    'display' => 12,
    'orderBy' => 'pagetitle ASC',
    'paginate' => 'pages',
    'PrevNextAlwaysShow' => 0,
    'pageAdjacents' => 4,
    'TplWrapPaginate' => '@CODE:<ul class="pages">[+wrap+]</ul>',
    'TplPrevP' => '',
    'TplNextP' => '',
    'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;items_page=[+num+]`]]">[+num+]</a></li>',
    'TplCurrentPage' => '@CODE:<li class="pages__item"><span class="pages__link active">[+num+]</span></li>',
    'TplDotsPage' => '@CODE:<li class="pages__item"><span class="pages__separ">...</span></li>'
));

$albums = $modx->runSnippet('DocLister', array(
    'id' => 'albums',
    'parents' => 0,
    'showParent' => 1,
    'showNoPublish' => 1,
    'depth' => 10,
    'addWhereList' => 'c.template=' . $template_album,
    'tvList' => $tv_list,
    'tpl' => '@FILE:VKmarket/module_album',
    'ownerTPL' => '@FILE:VKmarket/module_albums_wrap',
    'display' => 12,
    'orderBy' => 'pagetitle ASC',
    'paginate' => 'pages',
    'PrevNextAlwaysShow' => 0,
    'pageAdjacents' => 4,
    'TplWrapPaginate' => '@CODE:<ul class="pages">[+wrap+]</ul>',
    'TplPrevP' => '',
    'TplNextP' => '',
    'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;albums_page=[+num+]`]]">[+num+]</a></li>',
    'TplCurrentPage' => '@CODE:<li class="pages__item"><span class="pages__link active">[+num+]</span></li>',
    'TplDotsPage' => '@CODE:<li class="pages__item"><span class="pages__separ">...</span></li>'
));

$placeholders = array(
    'errors' => '',
    'theme' => $market->theme,
    'module_id' => $market->module_id,
    'module_url' => $market->module_url,
    'jquery_path' => $market->jquery_path,
    'items' => $items,
    'albums' => $albums
);

$output = $modx->parseText($tpl, $placeholders);
echo $output;
