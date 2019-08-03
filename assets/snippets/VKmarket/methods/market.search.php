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
& album_id          |  идентификатор подборки, в которой искать
& q                 |  строка поискового запроса
& price_from        |  минимальное значение цены
& price_to          |  максимальное значение цены
& sort              |  вид сортировки
& rev               |  направление сортировки
& offset            |  смещение относительно первого найденной подборки
& count             |  количество возвращаемых подборок
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
if (isset($q)) {
    $request_params['q'] = $q;
}
if (isset($price_from)) {
    $request_params['price_from'] = $price_from;
}
if (isset($price_to)) {
    $request_params['price_to'] = $price_to;
}
if (isset($sort)) {
    $request_params['sort'] = $sort;
}
if (isset($rev)) {
    $request_params['rev'] = $rev;
}
if (isset($offset)) {
    $request_params['offset'] = $offset;
}
if (isset($count)) {
    $request_params['count'] = $count;
}

// Осуществляем поиск товаров
$request = $api->request('market.search', $request_params);

// Если поиск не осуществлён
if (!isset($request['count'])) {
    // выводим отчёт об ошибке
    return $api->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Search done',
        'response' => $request
    )
);

// Добавляем к отчёту доп. параметры
if (isset($album_id)) {
    $result['success']['request_params']['album_id'] = (int) $album_id;
}
if (isset($q)) {
    $result['success']['request_params']['q'] = $q;
}
if (isset($price_from)) {
    $result['success']['request_params']['price_from'] = (int) $price_from;
}
if (isset($price_to)) {
    $result['success']['request_params']['price_to'] = (int) $price_to;
}
if (isset($sort)) {
    $result['success']['request_params']['sort'] = (int) $sort;
}
if (isset($rev)) {
    $result['success']['request_params']['rev'] = (int) $rev;
}
if (isset($offset)) {
    $result['success']['request_params']['offset'] = (int) $offset;
}
if (isset($count)) {
    $result['success']['request_params']['count'] = (int) $count;
}

// Выводим отчёт об успехе
return $api->report($response, $result);
