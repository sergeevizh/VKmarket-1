<?php

/* Добавляет товар в подборки ===============================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& item_id           |  идентификатор товара
& album_ids         |  подборки, куда добавить товар (через запятую)
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

// Добавляем товар в указанные подборки
$addToAlbum = $vk->market__addToAlbum([
    'owner_id' => "-$group_id",
    'item_id' => $item_id,
    'album_ids' => $album_ids
]);

// Если товар не добавлен в какую-либо подборку
if ($addToAlbum !== 1) {
    return $addToAlbum; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном добавлении
$json_addToAlbum = array(
    'success' => array(
        'message' => 'Item added to albums',
        'response' => $addToAlbum,
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

$success = json_encode($json_addToAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном добавлении товара в подборки
