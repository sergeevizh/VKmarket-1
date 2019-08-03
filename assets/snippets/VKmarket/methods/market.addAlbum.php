<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

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
& image             |  путь к изображению
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($title)) {
    $error['error']['error_msg'] = 'Not found required param: title';
    // выводим отчёт об ошибке
    return $api->report($response, $error);
}

// Если при вызове было указано изображение
if (isset($image)) {

    // Получаем сервер VK для загрузки изображения подборки
    $server_params = array(
        'group_id' => $group_id
    );
    $server = $api->request('photos.getMarketAlbumUploadServer', $server_params);

    // Если сервер VK не получен
    if (!isset($server['upload_url'])) {
        // выводим отчёт об ошибке
        return $api->report($response, $server);
    }


    // Загружаем изображение на сервер VK
    $upload = $api->upload($server['upload_url'], $image);

    // Если изображение не загружено
    if (!isset($upload['photo'])) {
        // выводим отчёт об ошибке
        return $api->report($response, $upload);
    }

    // Сохраняем изображение на сервере VK
    $save_params = array(
        'group_id' => $group_id,
        'photo' => $upload['photo'],
        'server' => $upload['server'],
        'hash' => $upload['hash']
    );
    $save = $api->request('photos.saveMarketAlbumPhoto', $save_params);

    // Если изображение не сохранено на сервере VK
    if (!isset($save[0]['id'])) {
        // выводим отчёт об ошибке
        return $api->report($response, $save);
    }

    // Получаем ID загруженного изображения
    $photo_id = $save[0]['id'];
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
$request = $api->request('market.addAlbum', $request_params);

// Если подборка не создана
if (!isset($request['market_album_id'])) {
    // выводим отчёт об ошибке
    return $api->report($response, $request);
}

// Получаем ID созданной подборки
$market_album_id = $request['market_album_id'];

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Album created',
        'response' => (int) $market_album_id,
        'request_params' => array(
            'title' => $title
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($image)) {
    $result['success']['request_params']['image'] = $image;
    $result['success']['request_params']['photo_id'] = (int) $photo_id;
}

// Выводим отчёт об успехе
return $api->report($response, $result);
