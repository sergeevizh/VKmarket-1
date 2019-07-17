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
if (!isset($item_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: item_id"}}';
}
if (!isset($album_ids)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: album_ids"}}';
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
        'response' => $removeFromAlbum
    )
);

$success = json_encode($json_removeFromAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном добавлении товара в подборки
