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
    'vk_item_id' => 'vk_item_id',
    'vk_album_id' => 'vk_album_id',
    'vk_category_id' => 'vk_category_id',
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

                // генерируем параметры "ДО"
                $before = $sync->params($template, $id, 1);

                // запоминаем параметры "ДО"
                $_SESSION['before'] = $before;

                break;
        }
        break;

    case 'OnDocFormSave':

        $template = $modx->getDocument($id)['template'];

        switch ($template) {

            case $template_item:
            case $template_album:

                // вспоминаем параметры "ДО"
                $before =  $_SESSION['before'];

                // генерируем параметры "ПОСЛЕ"
                $after = $sync->params($template, $id, 0);

                // если элемент еесть в ВК
                if ($before['vk']['id']) {

                    // ищем отличия
                    $albums_now = $before['vk']['albums_ids'];
                    $albums_now = implode($albums_now);
                    $differ_albums = $albums_now !== $after['albums'];
                    $differ_params = $sync->differ($before['params'], $after['params']);

                    // если отличия есть
                    if ($differ_albums || $differ_params) {
                        $differs['api'] = $before['api'];
                        $differs['vk'] = $before['vk'];
                        $differs['evo'] = $before['evo'];

                        if ($differ_params) $differs['params'] = $differ_params;
                        if ($differ_albums) $differs['albums'] = $after['albums'];

                        // изменяем элемент в ВК
                        $result = $sync->edit($differs);
                        return $sync->alert('success', $template, $result);
                    }
                } else {
                    // если элемента нет в ВК
                    // добавляем его
                    $result = $sync->add($after);
                    return $sync->alert('success', $template, $result);
                }

                break;
        }
        break;
}
