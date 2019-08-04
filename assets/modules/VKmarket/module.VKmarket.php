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

$market = new VKmarket($modx);

// Если есть ошибки
if ($errors) {
    // Генерируем фронт с отчётом
    $tpl = $market->getFileContents('errors.html');
} else {
    // Генерируем товары
    $items_nopub = $modx->runSnippet('DocLister', array(
        'id' => 'items_nopub',
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_item_id:=:0)',
        'tvList' => 'vk_item_id',
        'tpl' => '@FILE:VKmarket/items_nopub',
        'ownerTPL' => '@FILE:VKmarket/items_nopub_wrap',
        'display' => 12,
        'orderBy' => 'pagetitle ASC',
        'paginate' => 'pages',
        'PrevNextAlwaysShow' => 0,
        'pageAdjacents' => 4,
        'TplWrapPaginate' => '@CODE:<ul class="pages">[+wrap+]</ul>',
        'TplPrevP' => '',
        'TplNextP' => '',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;items_nopub_page=[+num+]`]]">[+num+]</a></li>',
        'TplCurrentPage' => '@CODE:<li class="pages__item"><span class="pages__link active">[+num+]</span></li>',
        'TplDotsPage' => '@CODE:<li class="pages__item"><span class="pages__separ">...</span></li>'
    ));

    $items_pub = $modx->runSnippet('DocLister', array(
        'id' => 'items_pub',
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_item_id:!=:0)',
        'tvList' => 'vk_item_id',
        'tpl' => '@FILE:VKmarket/items_pub',
        'ownerTPL' => '@FILE:VKmarket/items_pub_wrap',
        'display' => 12,
        'orderBy' => 'pagetitle ASC',
        'paginate' => 'pages',
        'PrevNextAlwaysShow' => 0,
        'pageAdjacents' => 4,
        'TplWrapPaginate' => '@CODE:<ul class="pages">[+wrap+]</ul>',
        'TplPrevP' => '',
        'TplNextP' => '',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;items_pub_page=[+num+]`]]">[+num+]</a></li>',
        'TplCurrentPage' => '@CODE:<li class="pages__item"><span class="pages__link active">[+num+]</span></li>',
        'TplDotsPage' => '@CODE:<li class="pages__item"><span class="pages__separ">...</span></li>'
    ));

    // Генерируем подборки
    $albums_nopub = $modx->runSnippet('DocLister', array(
        'id' => 'albums_nopub',
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'addWhereList' => 'c.template=' . $template_album,
        'filters' => 'AND(tvd:vk_album_id:=:0)',
        'tvList' => 'vk_album_id',
        'tpl' => '@FILE:VKmarket/albums_nopub',
        'ownerTPL' => '@FILE:VKmarket/albums_nopub_wrap',
        'display' => 12,
        'orderBy' => 'pagetitle ASC',
        'paginate' => 'pages',
        'PrevNextAlwaysShow' => 0,
        'pageAdjacents' => 4,
        'TplWrapPaginate' => '@CODE:<ul class="pages">[+wrap+]</ul>',
        'TplPrevP' => '',
        'TplNextP' => '',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;albums_nopub_page=[+num+]`]]">[+num+]</a></li>',
        'TplCurrentPage' => '@CODE:<li class="pages__item"><span class="pages__link active">[+num+]</span></li>',
        'TplDotsPage' => '@CODE:<li class="pages__item"><span class="pages__separ">...</span></li>'
    ));
    $albums_pub = $modx->runSnippet('DocLister', array(
        'id' => 'albums_pub',
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'addWhereList' => 'c.template=' . $template_album,
        'filters' => 'AND(tvd:vk_album_id:!=:0)',
        'tvList' => 'vk_album_id',
        'tpl' => '@FILE:VKmarket/albums_pub',
        'ownerTPL' => '@FILE:VKmarket/albums_pub_wrap',
        'display' => 12,
        'orderBy' => 'pagetitle ASC',
        'paginate' => 'pages',
        'PrevNextAlwaysShow' => 0,
        'pageAdjacents' => 4,
        'TplWrapPaginate' => '@CODE:<ul class="pages">[+wrap+]</ul>',
        'TplPrevP' => '',
        'TplNextP' => '',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;albums_pub_page=[+num+]`]]">[+num+]</a></li>',
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
    'items_nopub' => $items_nopub,
    'items_pub' => $items_pub,
    'albums_nopub' => $albums_nopub,
    'albums_pub' => $albums_pub
);

$output = $modx->parseText($tpl, $placeholders);
echo $output;
