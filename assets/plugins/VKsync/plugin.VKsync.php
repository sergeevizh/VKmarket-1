<?php

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

// Проверяем заполненность параметров конфигурации
$error = array(
    'error' => array(
        'error_code' => 'configuration'
    )
);

if (!isset($access_token)) {
    $error['error']['error_msg'] = 'Не заполнено поле: VK access_token';
    // выводим отчёт об ошибке
    return alert('error', 'Ошибка конфигурации', $error);
}

if (!isset($group_id)) {
    $error['error']['error_msg'] = 'Не заполнено поле: VK group_id';
    // выводим отчёт об ошибке
    return alert('error', 'Ошибка конфигурации', $error);
}

if (!isset($template_item)) {
    $error['error']['error_msg'] = 'Не заполнено поле: ID шаблона товаров';
    // выводим отчёт об ошибке
    return alert('error', 'Ошибка конфигурации', $error);
}

if (!isset($template_album)) {
    $error['error']['error_msg'] = 'Не заполнено поле: ID шаблона категорий (подборок)';
    // выводим отчёт об ошибке
    return alert('error', 'Ошибка конфигурации', $error);
}

if (!isset($tv_item_id)) {
    $error['error']['error_msg'] = 'Не заполнено поле: ID TV для market_item_id (id товара)';
    // выводим отчёт об ошибке
    return alert('error', 'Ошибка конфигурации', $error);
}

if (!isset($tv_album_id)) {
    $error['error']['error_msg'] = 'Не заполнено поле: ID TV для market_album_id (id подборки)';
    // выводим отчёт об ошибке
    return alert('error', 'Ошибка конфигурации', $error);
}


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
