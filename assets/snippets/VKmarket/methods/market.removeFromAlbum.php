<?php

/* Добавляет товар в подборки ===============================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& item_id           |  идентификатор товара
& album_ids         |  подборки, откуда удалить товар (через запятую)
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

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($album_ids)) {
    $error['error']['error_msg'] = 'Not found required param: album_ids';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'item_id' => $item_id,
    'album_ids' => $album_ids
);

// Удаляем товар из указанных подборок
$request = $vk->request('market.removeFromAlbum', $request_params);

// Если товар не удалён из какой-либо подборки
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Item removed from albums',
        'response' => 1,
        'request_params' => array(
            'item_id' => (int) $item_id,
            'album_ids' => $album_ids
        )
    )
);

// Выводим отчёт об успехе
return $vk->report($response, $result);
