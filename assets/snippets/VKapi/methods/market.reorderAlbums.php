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
& album_id          |  идентификатор подборки
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& before            |  ID подборки, перед которой поместить
& after             |  ID подборки, после которой поместить
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($album_id)) {
    $error['error']['error_msg'] = 'Not found required param: album_id';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'album_id' => $album_id
);

// Добавляем к запросу доп. параметры
if (isset($before)) {
    $request_params['before'] = $before;
}
if (isset($after)) {
    $request_params['after'] = $after;
}

// Изменяем положение подборки
$request = $vk->request('market.reorderAlbums', $request_params);

// Если подборка не перемещена
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Albums reordered',
        'response' => 1,
        'request_params' => array(
            'album_id' => (int) $album_id
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($before)) {
    $result['success']['request_params']['before'] = $before;
}
if (isset($after)) {
    $result['success']['request_params']['after'] = $after;
}

// Выводим отчёт об успехе
return $vk->report($response, $result);
