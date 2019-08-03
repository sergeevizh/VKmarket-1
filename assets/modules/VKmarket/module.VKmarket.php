<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/modules/VKmarket/class.VKmarket.php';

// Проверяем заполненность параметров конфигурации
$errors = "";
if (!isset($access_token))      $errors = $errors . '<li><strong>access_token</strong> : ключ доступа к API Вконтакте/li>';
if (!isset($group_id))          $errors = $errors . '<li><strong>group_id</strong> : идентификатор сообщества ВКонтакте</li>';
if (!isset($v))                 $errors = $errors . '<li><strong>v (версия API)</strong> : версия используемого API ВКонтакте</li>';
if (!isset($template_item))     $errors = $errors . '<li><strong>ID шаблона товаров</strong> : шаблон ресурсов, которые будут товарами ВКонтакте</li>';
if (!isset($template_album))    $errors = $errors . '<li><strong>ID шаблона категорий (подборок)</strong> : шаблон ресурсов, которые будут подборками ВКонтакте</li>';

// Генерируем фронт модуля
$market = new VKmarket($modx);

// Если есть ошибки
if ($errors !== "") {
    // Генерируем фронт с отчётом
    $tpl = $market->getFileContents('errors.html');
} else {
    // Генерируем товары
    $items = $modx->runSnippet('DocLister', array(
        'id' => 'items',
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'addWhereList' => 'c.template=' . $template_item,
        'tvList' => 'vk_item_id,vk_album_id',
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

    // Генерируем подборки
    $albums = $modx->runSnippet('DocLister', array(
        'id' => 'albums',
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'addWhereList' => 'c.template=' . $template_album,
        'tvList' => 'vk_item_id,vk_album_id',
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

    // Генерируем фронт с элементами
    $tpl = $market->getFileContents('main.html');
}

$placeholders = array(
    'errors' => $errors,
    'theme' => $market->theme,
    'module_id' => $market->module_id,
    'module_url' => $market->module_url,
    'jquery_path' => $market->jquery_path,
    'items' => $items,
    'albums' => $albums
);

$output = $modx->parseText($tpl, $placeholders);
echo $output;
