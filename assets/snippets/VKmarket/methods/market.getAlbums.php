<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/* Возвращает список подборок ===============================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& offset            |  смещение относительно первой найденной подборки
& count             |  количество возвращаемых подборок
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id"
);

// Добавляем к запросу доп. параметры
if (isset($offset)) {
    $request_params['offset'] = $offset;
}
if (isset($count)) {
    $request_params['count'] = $count;
}

// Запрашиваем список подборок
$request = $api->request('market.getAlbums', $request_params);

// Если список не получен
if (!isset($request['count'])) {
    // выводим отчёт об ошибке
    return $api->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Albums received',
        'response' => $request
    )
);

// Добавляем к отчёту доп. параметры
if (isset($offset)) {
    $result['success']['request_params']['offset'] = (int) $offset;
}
if (isset($count)) {
    $result['success']['request_params']['count'] = (int) $count;
}

// Выводим отчёт об успехе
return $api->report($response, $result);
