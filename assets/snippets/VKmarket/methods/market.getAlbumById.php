<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/* Возвращает данные подборки с товарами ====================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& album_ids         |  идентификаторы подборок через запятую
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($album_ids)) {
    $error['error']['error_msg'] = 'Not found required param: album_ids';
    // выводим отчёт об ошибке
    return $api->report($response, $error);
}

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'album_ids' => $album_ids
);

// Запрашиваем информацию о подборках
$request = $api->request('market.getAlbumById', $request_params);

// Если информация не получена
if ($request['items'][0]['id'] == 0) {
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

// Выводим отчёт об успехе
return $api->report($response, $result);
