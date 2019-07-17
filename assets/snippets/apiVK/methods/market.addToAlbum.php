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
if (!isset($group_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
}
if (!isset($item_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: item_id"}}';
}
if (!isset($album_ids)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: album_ids"}}';
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
        'request_params' => array(
            array(
                'key' => 'item_id',
                'value' => $item_id
            ),
            array(
                'key' => 'album_ids',
                'value' => $album_ids
            )
        ),
        'response' => $addToAlbum
    )
);

$success = json_encode($json_addToAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном добавлении товара в подборки
