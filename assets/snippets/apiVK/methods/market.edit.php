<?php

/* Редактирует товар ========================================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& item_id           |  идентификатор товара
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
& name              |  новое название товара
& description       |  новое описание товара
& category_id       |  идентификатор новой категории товара
& price             |  новая цена товара
& deleted           |  новый статус товара (удалён или не удалён)
& image             |  путь к новому изображению
& url               |  новая ссылка на сайт товара
============================================================= */

// Проверяем наличие обязательных параметров
if (!isset($group_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
}
if (!isset($item_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: item_id"}}';
}

// Если нужно заменить изображение
if (isset($image)) {

    $image_path = 'image.jpg';
    copy($image, 'image.jpg');

    // Получаем сервер VK для загрузки изображения товара
    $upload_server = $vk->photos__getMarketUploadServer($group_id, 1);

    // Если сервер VK не получен
    if (!isset($upload_server['upload_url'])) {
        return json_encode($upload_server, true); // выводим отчёт об ошибке
    }

    // Загружаем изображение на сервер VK
    $file_uploaded = $vk->uploadFile($upload_server['upload_url'], $image_path);

    // Если изображение не загружено
    if (!isset($file_uploaded['photo'])) {
        return json_encode($file_uploaded, true); // выводим отчёт об ошибке
    }

    // Сохраняем изображение на сервере VK
    $file_saved = $vk->photos__saveMarketPhoto(
        [
            'group_id' => $group_id,
            'photo' => $file_uploaded['photo'],
            'server' => $file_uploaded['server'],
            'hash' => $file_uploaded['hash'],
            'crop_data' => $file_uploaded['crop_data'],
            'crop_hash' => $file_uploaded['crop_hash']
        ]
    );

    // Если изображение не сохранено на сервере VK
    if (!isset($file_saved[0]['id'])) {
        return json_encode($file_saved, true); // выводим отчёт об ошибке
    }

    // Получаем ID загруженного изображения
    $main_photo_id = $file_saved[0]['id'];
}

// Редактируем товар в сообществе
$edit = $vk->market__edit([
    'owner_id' => "-$group_id",
    'item_id' => $item_id,
    'name' => $name,
    'description' => $description,
    'category_id' => $category_id,
    'price' => $price,
    'deleted' => $deleted ? $deleted : 0,
    'main_photo_id' => $main_photo_id,
    'url' => $url
]);

// Если товар не отредактирован
if ($edit !== 1) {
    return $edit; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном редактировании товара
$json_edit = array(
    'success' => array(
        'message' => 'Item edited',
        'request_params' => array(
            array(
                'key' => 'item_id',
                'value' => $item_id
            ),
            array(
                'key' => 'name',
                'value' => $name
            ),
            array(
                'key' => 'description',
                'value' => $description
            ),
            array(
                'key' => 'category_id',
                'value' => $category_id
            ),
            array(
                'key' => 'price',
                'value' => $price
            ),
            array(
                'key' => 'deleted',
                'value' => $deleted ? $deleted : 0
            ),
            array(
                'key' => 'main_photo_id',
                'value' => $main_photo_id
            ),
            array(
                'key' => 'url',
                'value' => $url
            )
        ),
        'response' => 1
    )
);

$success = json_encode($json_edit, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном редактировании товара
