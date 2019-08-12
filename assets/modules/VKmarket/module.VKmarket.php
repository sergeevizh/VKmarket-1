<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/modules/VKmarket/class.VKmarket.php';
require_once MODX_BASE_PATH . 'assets/plugins/VKmarket/class.VKsync.php';

// Проверяем заполненность параметров конфигурации
$errors = "";
if (!isset($access_token))      $errors = $errors . '<li><strong>access_token</strong> : ключ доступа к API Вконтакте/li>';
if (!isset($group_id))          $errors = $errors . '<li><strong>group_id</strong> : идентификатор сообщества ВКонтакте</li>';
if (!isset($v))                 $errors = $errors . '<li><strong>v (версия API)</strong> : версия используемого API ВКонтакте</li>';
if (!isset($template_item))     $errors = $errors . '<li><strong>ID шаблона товаров</strong> : шаблон ресурсов, которые будут товарами ВКонтакте</li>';
if (!isset($template_album))    $errors = $errors . '<li><strong>ID шаблона категорий (подборок)</strong> : шаблон ресурсов, которые будут подборками ВКонтакте</li>';

// Конфигурация для класса VKmarket

$site_tmplvars = $modx->getFullTableName('site_tmplvars');
$site_tmplvar_contentvalues = $modx->getFullTableName('site_tmplvar_contentvalues');

$vk_original_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_original_id"'));
$vk_license_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_license_id"'));
$vk_spray_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_spray_id"'));
$vk_probnik_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_probnik_id"'));
$vk_phero10_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_phero10_id"'));
$vk_mini_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_mini_id"'));
$vk_album_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_album_id"'));
$vk_category_id = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_category_id"'));

$module_config = array(
    'tmplvarid' => array(
        'vk_original_id'        => $vk_original_id,
        'vk_license_id'        => $vk_license_id,
        'vk_spray_id'        => $vk_spray_id,
        'vk_probnik_id'        => $vk_probnik_id,
        'vk_phero10_id'        => $vk_phero10_id,
        'vk_mini_id'        => $vk_mini_id,
        'vk_album_id'       => $vk_album_id,
        'vk_category_id'    => $vk_category_id
    ),

    'api' => array(
        'access_token'      => $access_token,
        'group_id'          => $group_id,
        'v'                 => $v
    ),

    'template_item'         => $template_item,
    'template_album'        => $template_album,
    'tv_list'               => $tv_list,

    'original_name_tpl'         => $original_name_tpl,
    'original_description_tpl'  => $original_description_tpl,
    'original_price_tpl'        => $original_price_tpl,
    'original_image_tpl'        => $original_image_tpl,

    'license_name_tpl'         => $license_name_tpl,
    'license_description_tpl'  => $license_description_tpl,
    'license_price_tpl'        => $license_price_tpl,
    'license_image_tpl'        => $license_image_tpl,

    'spray_name_tpl'         => $spray_name_tpl,
    'spray_description_tpl'  => $spray_description_tpl,
    'spray_price_tpl'        => $spray_price_tpl,
    'spray_image_tpl'        => $spray_image_tpl,

    'probnik_name_tpl'         => $probnik_name_tpl,
    'probnik_description_tpl'  => $probnik_description_tpl,
    'probnik_price_tpl'        => $probnik_price_tpl,
    'probnik_image_tpl'        => $probnik_image_tpl,

    'phero10_name_tpl'         => $phero10_name_tpl,
    'phero10_description_tpl'  => $phero10_description_tpl,
    'phero10_price_tpl'        => $phero10_price_tpl,
    'phero10_image_tpl'        => $phero10_image_tpl,

    'mini_name_tpl'         => $mini_name_tpl,
    'mini_description_tpl'  => $mini_description_tpl,
    'mini_price_tpl'        => $mini_price_tpl,
    'mini_image_tpl'        => $mini_image_tpl,

    'album_title_tpl'       => $album_title_tpl,
    'album_image_tpl'       => $album_image_tpl,

    'db' => array(
        'tvs'               => $site_tmplvars,
        'tv_value'          => $site_tmplvar_contentvalues
    )
);

$market = new VKmarket($modx, $module_config);

$market->makeActions();

// Если есть ошибки
if ($errors) {
    // Генерируем фронт с отчётом
    $tpl = $market->getFileContents('errors.html');
} else {

    // Генерируем товары
    $params_doclister = array(
        'parents' => 0,
        'showParent' => 1,
        'showNoPublish' => 1,
        'depth' => 10,
        'tvList' => 'vk_original_id,vk_license_id,vk_spray_id,vk_probnik_id,vk_phero10_id,vk_mini_id,vk_album_id,slot-have',
        'display' => 'all',
        'orderBy' => 'pagetitle ASC'
    );

    $original = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:6)',
        'tpl' => '@FILE:VKmarket/original--tpl',
        'ownerTPL' => '@FILE:VKmarket/original--ownerTPL'
    ));

    $license_nopub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'license_nopub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_license_id:=:0;tv:slot-have:%:1)',
        'tpl' => '@FILE:VKmarket/license_nopub--tpl',
        'ownerTPL' => '@FILE:VKmarket/license_nopub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;license_nopub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $license_pub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'license_pub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_license_id:!=:0;tv:slot-have:%:1)',
        'tpl' => '@FILE:VKmarket/license_pub--tpl',
        'ownerTPL' => '@FILE:VKmarket/license_pub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;license_pub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $spray_nopub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'spray_nopub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_spray_id:=:0;tv:slot-have:%:2)',
        'tpl' => '@FILE:VKmarket/spray_nopub--tpl',
        'ownerTPL' => '@FILE:VKmarket/spray_nopub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;spray_nopub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $spray_pub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'spray_pub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_spray_id:!=:0;tv:slot-have:%:2)',
        'tpl' => '@FILE:VKmarket/spray_pub--tpl',
        'ownerTPL' => '@FILE:VKmarket/spray_pub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;spray_pub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $probnik_nopub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'probnik_nopub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_probnik_id:=:0;tv:slot-have:%:3)',
        'tpl' => '@FILE:VKmarket/probnik_nopub--tpl',
        'ownerTPL' => '@FILE:VKmarket/probnik_nopub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;probnik_nopub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $probnik_pub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'probnik_pub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_probnik_id:!=:0;tv:slot-have:%:3)',
        'tpl' => '@FILE:VKmarket/probnik_pub--tpl',
        'ownerTPL' => '@FILE:VKmarket/probnik_pub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;probnik_pub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $phero10_nopub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'phero10_nopub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_phero10_id:=:0;tv:slot-have:%:4)',
        'tpl' => '@FILE:VKmarket/phero10_nopub--tpl',
        'ownerTPL' => '@FILE:VKmarket/phero10_nopub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;phero10_nopub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $phero10_pub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'phero10_pub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_phero10_id:!=:0;tv:slot-have:%:4)',
        'tpl' => '@FILE:VKmarket/phero10_pub--tpl',
        'ownerTPL' => '@FILE:VKmarket/phero10_pub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;phero10_pub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $mini_nopub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'mini_nopub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_mini_id:=:0;tv:slot-have:%:4)',
        'tpl' => '@FILE:VKmarket/mini_nopub--tpl',
        'ownerTPL' => '@FILE:VKmarket/mini_nopub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;mini_nopub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    $mini_pub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'mini_pub',
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tvd:vk_mini_id:!=:0;tv:slot-have:%:4)',
        'tpl' => '@FILE:VKmarket/mini_pub--tpl',
        'ownerTPL' => '@FILE:VKmarket/mini_pub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;mini_pub_page=[+num+]`]]">[+num+]</a></li>',
    ));

    // Генерируем подборки
    $album_nopub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'album_nopub',
        'addWhereList' => 'c.template=' . $template_album,
        'filters' => 'AND(tvd:vk_album_id:=:0)',
        'tpl' => '@FILE:VKmarket/album_nopub--tpl',
        'ownerTPL' => '@FILE:VKmarket/album_nopub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;album_nopub_page=[+num+]`]]">[+num+]</a></li>'
    ));

    $album_pub = $modx->runSnippet('DocLister', $params_doclister + array(
        'id' => 'album_pub',
        'addWhereList' => 'c.template=' . $template_album,
        'filters' => 'AND(tvd:vk_album_id:!=:0)',
        'tpl' => '@FILE:VKmarket/album_pub--tpl',
        'ownerTPL' => '@FILE:VKmarket/album_pub--ownerTPL',
        'TplPage' => '@CODE:<li class="pages__item"><a class="pages__link" href="index.php?a=112&amp;id=' . $market->module_id . '[[if? &is=`[+num+]:!=:1` &then=`&amp;album_pub_page=[+num+]`]]">[+num+]</a></li>'
    ));

    // Генерируем фронт с элементами
    $tpl = $market->getFileContents('main.html');
}

$placeholders = array(
    'errors'            => $errors,
    'theme'             => $market->theme,
    'module_id'         => $market->module_id,
    'module_url'        => $market->module_url,
    'jquery_path'       => $market->jquery_path,
    'template_item'     => $template_item,
    'template_album'    => $template_album,
    'original'          => $original,
    'license_nopub'     => $license_nopub,
    'license_pub'       => $license_pub,
    'spray_nopub'       => $spray_nopub,
    'spray_pub'         => $spray_pub,
    'probnik_nopub'     => $probnik_nopub,
    'probnik_pub'       => $probnik_pub,
    'phero10_nopub'     => $phero10_nopub,
    'phero10_pub'       => $phero10_pub,
    'mini_nopub'        => $mini_nopub,
    'mini_pub'          => $mini_pub,
    'album_nopub'       => $album_nopub,
    'album_pub'         => $album_pub
);

$output = $modx->parseText($tpl, $placeholders);
echo $output;
