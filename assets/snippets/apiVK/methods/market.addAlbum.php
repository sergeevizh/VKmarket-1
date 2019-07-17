<?php

/* Добавляет новую подборку =================================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& title             |  название подборки
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
& image             |  путь к изображению
============================================================= */

// Проверяем наличие обязательных параметров
if (!isset($group_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
}
if (!isset($title)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: title"}}';
}

// Если при вызове было указано изображение
if (isset($image)) {

    $image_path = "image.png";
    copy($image, "image.png");

    // Получаем сервер VK для загрузки изображения подборки
    $upload_server = $vk->photos__getMarketAlbumUploadServer($group_id);

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
    $file_saved = $vk->photos__saveMarketAlbumPhoto(
        [
            'group_id' => $group_id,
            'photo' => $file_uploaded['photo'],
            'server' => $file_uploaded['server'],
            'hash' => $file_uploaded['hash']
        ]
    );

    // Если изображение не сохранено на сервере VK
    if (!isset($file_saved[0]['id'])) {
        return json_encode($file_saved, true); // выводим отчёт об ошибке
    }

    // Получаем ID загруженного изображения
    $photo_id = $file_saved[0]['id'];
}

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'title' => $title
);

// Добавляем к запросу доп. параметры
if (isset($image)) {
    $request_params['photo_id'] = $photo_id;
}

// Создаём подборку в сообществе
$addAlbum = $vk->market__addAlbum($request_params);

// Если подборка не создана
if (!isset($addAlbum['market_album_id'])) {
    return $addAlbum; // выводим отчёт об ошибке
}

// Получаем ID созданной подборки
$market_album_id = $addAlbum['market_album_id'];

// Генерируем отчёт об успешном создании подборки
$json_addAlbum = array(
    'success' => array(
        'message' => 'Album created',
        'request_params' => array(
            array(
                'key' => 'title',
                'value' => $title
            ),
            array(
                'key' => 'photo_id',
                'value' => $photo_id
            )
        ),
        'response' => array(
            array(
                'key' => 'market_album_id',
                'value' => $market_album_id
            )
        )
    )
);

$success = json_encode($json_addAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном создании подборки
