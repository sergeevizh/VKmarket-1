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
        'orderBy' => 'pagetitle ASC',
        'ownerTPL' => '@CODE:[+dl.wrap+]'
    );

    $original = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:6)',
        'tpl' => '@FILE:VKmarket/original--tpl'
    ));

    $license = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:1)',
        'tpl' => '@FILE:VKmarket/license--tpl'
    ));

    $spray = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:2)',
        'tpl' => '@FILE:VKmarket/spray--tpl'
    ));

    $probnik = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:3)',
        'tpl' => '@FILE:VKmarket/probnik--tpl'
    ));

    $phero10 = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:4)',
        'tpl' => '@FILE:VKmarket/phero10--tpl'
    ));

    $mini = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_item,
        'filters' => 'AND(tv:slot-have:%:5)',
        'tpl' => '@FILE:VKmarket/mini--tpl'
    ));

    // Генерируем подборки
    $album = $modx->runSnippet('DocLister', $params_doclister + array(
        'addWhereList' => 'c.template=' . $template_album,
        'tpl' => '@FILE:VKmarket/album--tpl'
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
    'license'           => $license,
    'spray'             => $spray,
    'probnik'           => $probnik,
    'phero10'           => $phero10,
    'mini'              => $mini,
    'album'             => $album
);

$output = $modx->parseText($tpl, $placeholders);
echo $output;
