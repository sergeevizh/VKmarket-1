<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

# TODO: OnBeforeDocDuplicate        ДО      создания дубликата
# TODO: OnDocDuplicate              ПОСЛЕ   создания дубликата
# TODO: OnBeforeDocFormDelete       ДО      удаления документа
# TODO: OnDocFormDelete             ПОСЛЕ   удаления документа
# TODO: OnDocFormUnDelete           ПОСЛЕ   восстановления документа
# TODO: OnBeforeEmptyTrash          ДО      очистки корзины
# TODO: OnEmptyTrash                ПОСЛЕ   очистки корзины
# TODO: OnBeforeMoveDocument        ДО      перемещения документа
# TODO: OnAfterMoveDocument         ПОСЛЕ   перемещения документа

require_once MODX_BASE_PATH . 'assets/plugins/VKsync/functions.VKsync.php';

// Генерируем массив с конфигурацией плагина
$config = array(
    'access_token' => $access_token,
    'group_id' => $group_id,
    'v' => $v,
    'tvList' => $tv_list,
    'item' => array(
        'template' => $template_item,
        'tv' => $tv_item_id,
        'name' => $item_name_tpl,
        'description' => $item_description_tpl,
        'price' => $item_price_tpl,
        'image' => $item_image_tpl,
        'category_id' => $album_category_id
    ),
    'album' => array(
        'template' => $template_album,
        'tv' => $tv_album_id,
        'title' => $album_title_tpl,
        'image' => $album_image_tpl
    )
);


switch ($modx->event->name) {

    case 'OnDocFormPrerender':

        switch ($template) {

            case $config['item']['template']:
                // ТОВАРЫ ========================================
                // -----------------------------------------------
                $params = params('item', $config, $id);
                return alert('success', 'Параметры товара', $params);

                break;

            case $config['album']['template']:
                // ПОДБОРКИ ======================================
                // -----------------------------------------------
                $params = params('album', $config, $id);
                return alert('success', 'Параметры подборки', $params);

                break;
        }
        break;
}
