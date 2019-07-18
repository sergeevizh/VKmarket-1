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
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    return json_encode($error);
}

if (!isset($album_ids)) {
    $error['error']['error_msg'] = 'Not found required param: album_ids';
    return json_encode($error);
}

// Удаляем товар из указанных подборок
$removeFromAlbum = $vk->market__removeFromAlbum([
    'owner_id' => "-$group_id",
    'item_id' => $item_id,
    'album_ids' => $album_ids
]);

// Если товар не удалён из какой-либо подборки
if ($removeFromAlbum !== 1) {
    return $removeFromAlbum; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном удалении
$json_removeFromAlbum = array(
    'success' => array(
        'message' => 'Item removed from albums',
        'response' => $removeFromAlbum,
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

$success = json_encode($json_removeFromAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном добавлении товара в подборки
