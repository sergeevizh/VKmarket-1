<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/* Возвращает список товаров ================================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& album_id          |  идентификатор подборки, из которой вернуть
& offset            |  смещение относительно первого найденного товара
& count             |  количество возвращаемых товаров
& extended          |  возвращать ли дополнительные поля
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id"
);

// Добавляем к запросу доп. параметры
if (isset($album_id)) {
    $request_params['album_id'] = $album_id;
}
if (isset($offset)) {
    $request_params['offset'] = $offset;
}
if (isset($count)) {
    $request_params['count'] = $count;
}
if (isset($extended)) {
    $request_params['extended'] = $extended;
}

// Запрашиваем список товаров
$request = $api->request('market.get', $request_params);

// Если список не получен
if (!isset($request['count'])) {
    // выводим отчёт об ошибке
    return $api->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Items received',
        'response' => $request
    )
);

// Добавляем к отчёту доп. параметры
if (isset($album_id)) {
    $result['success']['request_params']['album_id'] = (int) $album_id;
}
if (isset($offset)) {
    $result['success']['request_params']['offset'] = (int) $offset;
}
if (isset($count)) {
    $result['success']['request_params']['count'] = (int) $count;
}
if (isset($extended)) {
    $result['success']['request_params']['extended'] = (int) $extended;
}

// Выводим отчёт об успехе
return $api->report($response, $result);
