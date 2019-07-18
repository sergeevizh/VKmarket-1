<?php

/* Редактирует подборку с товарами ==========================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& album_id          |  идентификатор подборки
& title             |  новое название подборки
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
& image             |  путь к новому изображению
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($album_id)) {
    $error['error']['error_msg'] = 'Not found required param: album_id';
    return json_encode($error);
}

if (!isset($title)) {
    $error['error']['error_msg'] = 'Not found required param: title';
    return json_encode($error);
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
    'album_id' => $album_id,
    'title' => $title
);

// Добавляем к запросу доп. параметры
if (isset($image)) {
    $request_params['photo_id'] = $photo_id;
}

// Редактируем подборку в сообществе
$editAlbum = $vk->market__editAlbum($request_params);

// Если подборка не отредактирована
if ($editAlbum !== 1) {
    return $editAlbum; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном редактировании подборки
$json_editAlbum = array(
    'success' => array(
        'message' => 'Album edited',
        'response' => $editAlbum,
        'request_params' => array(
            array(
                'key' => 'album_id',
                'value' => $album_id
            ),
            array(
                'key' => 'title',
                'value' => $title
            )
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($photo_id)) {
    array_push(
        $json_editAlbum['success']['request_params'],
        array(
            'key' => 'photo_id',
            'value' => $photo_id
        )
    );
}

$success = json_encode($json_editAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном редактировании подборки
