<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/* Возвращает информацию о товарах ==========================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& item_ids          |  идентификаторы через запятую
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& extended          |  возвращать ли дополнительные поля
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($item_ids)) {
    $error['error']['error_msg'] = 'Not found required param: item_ids';
    // выводим отчёт об ошибке
    return $api->report($response, $error);
}

// Генерируем список запрашиваемых id в нужном формате
$before_ids = str_replace(' ', '', $item_ids);
$array_ids = explode(',', $before_ids);
foreach ($array_ids as &$item_id) {
    $item_id = '-' . $group_id . '_' . $item_id;
}
$after_ids = implode(",", $array_ids);

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'item_ids' => $after_ids
);

// Добавляем к запросу доп. параметры
if (isset($extended)) {
    $request_params['extended'] = $extended;
}

// Запрашиваем информацию о товарах
$request = $api->request('market.getById', $request_params);

// Если информация не получена
if (!count($request['items'])) {
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
if (isset($extended)) {
    $result['success']['request_params']['extended'] = (int) $extended;
}

// Выводим отчёт об успехе
return $api->report($response, $result);
