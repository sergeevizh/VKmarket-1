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
& response          |  тип успешного результата
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($title)) {
    $error['error']['error_msg'] = 'Not found required param: title';
    return json_encode($error, true);
}

// Если при вызове было указано изображение
if (isset($image)) {

    $image_path = "image.png";
    copy(MODX_BASE_PATH . $image, "image.png");

    // Получаем сервер VK для загрузки изображения подборки
    $server = $vk->getMarketAlbumUploadServer($group_id);

    // Если сервер VK не получен
    if (!isset($server['upload_url'])) {
        return $server; // выводим отчёт об ошибке
    }

    // Загружаем изображение на сервер VK
    $upload = $vk->uploadFile($server['upload_url'], $image_path);

    // Если изображение не загружено
    if (!isset($upload['photo'])) {
        return $upload; // выводим отчёт об ошибке
    }

    // Сохраняем изображение на сервере VK
    $save = $vk->saveMarketAlbumPhoto(
        [
            'group_id' => $group_id,
            'photo' => $upload['photo'],
            'server' => $upload['server'],
            'hash' => $upload['hash']
        ]
    );

    // Если изображение не сохранено на сервере VK
    if (!isset($save[0]['id'])) {
        return $save; // выводим отчёт об ошибке
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
$request = $vk->addAlbum($request_params);

// Если подборка не создана
if (!isset($request['market_album_id'])) {
    return $request; // выводим отчёт об ошибке
}

// Получаем ID созданной подборки
$market_album_id = $request['market_album_id'];

// Генерируем отчёт об успешном создании подборки
$result = array(
    'success' => array(
        'message' => 'Album created',
        'response' => $market_album_id,
        'request_params' => array(
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
        $result['success']['request_params'],
        array(
            'key' => 'photo_id',
            'value' => $photo_id
        )
    );
}

// Выводим отчёт об успешном создании подборки
$success = json_encode($result, JSON_UNESCAPED_UNICODE);
switch ($response) {
    case 'id':
        return $market_album_id;
        break;

    case 'json':
    default:
        return $success;
        break;
}
