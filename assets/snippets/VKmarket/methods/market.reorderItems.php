<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/* Изменяет положение подборки в списке =====================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& item_id           |  идентификатор товара
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& album_id          |  идентификатор подборки для сортировки
& before            |  ID товара, перед которым поместить
& after             |  ID товара, после которого поместить
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    // выводим отчёт об ошибке
    return $api->report($response, $error);
}

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'item_id' => $item_id
);

// Добавляем к запросу доп. параметры
if (isset($album_id)) {
    $request_params['album_id'] = $album_id;
}
if (isset($before)) {
    $request_params['before'] = $before;
}
if (isset($after)) {
    $request_params['after'] = $after;
}

// Изменяем положение товара
$request = $api->request('market.reorderItems', $request_params);

// Если товар не перемещен
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $api->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Items reordered',
        'response' => 1,
        'request_params' => array(
            'item_id' => (int) $item_id
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($album_id)) {
    $result['success']['request_params']['album_id'] = $album_id;
}
if (isset($before)) {
    $result['success']['request_params']['before'] = $before;
}
if (isset($after)) {
    $result['success']['request_params']['after'] = $after;
}

// Выводим отчёт об успехе
return $api->report($response, $result);
