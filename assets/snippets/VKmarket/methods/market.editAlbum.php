<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

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
& image             |  путь к новому изображению
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($album_id)) {
    $error['error']['error_msg'] = 'Not found required param: album_id';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($title)) {
    $error['error']['error_msg'] = 'Not found required param: title';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

// Если при вызове было указано изображение
if (isset($image)) {

    // Получаем сервер VK для загрузки изображения подборки
    $server_params = array(
        'group_id' => $group_id
    );
    $server = $vk->request('photos.getMarketAlbumUploadServer', $server_params);

    // Если сервер VK не получен
    if (!isset($server['upload_url'])) {
        // выводим отчёт об ошибке
        return $vk->report($response, $server);
    }


    // Загружаем изображение на сервер VK
    $upload = $vk->upload($server['upload_url'], $image);

    // Если изображение не загружено
    if (!isset($upload['photo'])) {
        // выводим отчёт об ошибке
        return $vk->report($response, $upload);
    }

    // Сохраняем изображение на сервере VK
    $save_params = array(
        'group_id' => $group_id,
        'photo' => $upload['photo'],
        'server' => $upload['server'],
        'hash' => $upload['hash']
    );
    $save = $vk->request('photos.saveMarketAlbumPhoto', $save_params);

    // Если изображение не сохранено на сервере VK
    if (!isset($save[0]['id'])) {
        // выводим отчёт об ошибке
        return $vk->report($response, $save);
    }

    // Получаем ID загруженного изображения
    $photo_id = $save[0]['id'];
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
$request = $vk->request('market.editAlbum', $request_params);

// Если подборка не отредактирована
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Album edited',
        'response' => 1,
        'request_params' => array(
            'album_id' => (int) $album_id,
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
return $vk->report($response, $result);
