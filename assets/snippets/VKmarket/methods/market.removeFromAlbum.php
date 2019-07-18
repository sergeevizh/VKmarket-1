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
$error = array('error' => array('error_code' => 'required'));

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    return json_encode($error, true);
}

if (!isset($album_ids)) {
    $error['error']['error_msg'] = 'Not found required param: album_ids';
    return json_encode($error, true);
}

// Удаляем товар из указанных подборок
$request = $vk->removeFromAlbum([
    'owner_id' => "-$group_id",
    'item_id' => $item_id,
    'album_ids' => $album_ids
]);

// Если товар не удалён из какой-либо подборки
if ($request !== 1) {
    return $request; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном удалении
$result = array(
    'success' => array(
        'message' => 'Item removed from albums',
        'response' => $request,
        'request_params' => array(
            array(
                'key' => 'item_id',
                'value' => $item_id
            ),
            array(
                'key' => 'album_ids',
                'value' => $album_ids
            )
        )
    )
);

// Выводим отчёт об успешном добавлении товара в подборки
$success = json_encode($result, JSON_UNESCAPED_UNICODE);
switch ($response) {
    case 1:
        return $request;
        break;

    case 'json':
    default:
        return $success;
        break;
}
