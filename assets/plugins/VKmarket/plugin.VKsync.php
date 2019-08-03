<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/plugins/VKmarket/class.VKsync.php';

/* Конфигурация модуля VKmarket =============================
-------------------------------------------------------------

Параметры API
-------------------------------------------------------------
$access_token           |  access_token
$group_id               |  group_id
$v                      |  v (версия API)
-------------------------------------------------------------

TV-параметры
-------------------------------------------------------------
vk_item_id              |  для id товара (market_item_id)
vk_album_id             |  для id подборки (market_album_id)
vk_category_id          |  для id категории (id_category)
-------------------------------------------------------------

Шаблоны
-------------------------------------------------------------
$template_item          |  id шаблона товаров
$template_album         |  id шаблона категорий (подборок)
-------------------------------------------------------------

Для DocLister
-------------------------------------------------------------
$tv_list                |  список TV-параметров (tvList)
$item_name_tpl          |  чанк названия товара (name)
$item_description_tpl   |  чанк описания товара (description)
$item_price_tpl         |  чанк цены товара (price)
$item_image_tpl         |  чанк изображения товара (image)
$album_title_tpl        |  чанк названия подборки (title)
$album_image_tpl        |  чанк изображения подборки (image)
-------------------------------------------------------------
============================================================= */

// Конфигурация для класса VKsync
$module_config = array(
    'access_token' => $access_token,
    'group_id' => $group_id,
    'v' => $v,
    'tv_item_id' => 'vk_item_id',
    'tv_album_id' => 'vk_album_id',
    'tv_category_id' => 'vk_category_id',
    'template_item' => $template_item,
    'template_album' => $template_album,
    'tv_list' => $tv_list,
    'item_name_tpl' => $item_name_tpl,
    'item_description_tpl' => $item_description_tpl,
    'item_price_tpl' => $item_price_tpl,
    'item_image_tpl' => $item_image_tpl,
    'album_title_tpl' => $album_title_tpl,
    'album_image_tpl' => $album_image_tpl
);

$sync = new VKsync($modx, $module_config);

switch ($modx->event->name) {

    case 'OnDocFormPrerender':

        switch ($template) {

            case $template_item:
            case $template_album:

                if ($template == $template_item) $alert_title = 'Параметры товара';
                if ($template == $template_album) $alert_title = 'Параметры товара';
                $params = $sync->params($template, $id);
                return $sync->alert('success', $alert_title, $params);
                break;
        }
        break;
}
