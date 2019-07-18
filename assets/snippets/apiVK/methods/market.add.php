<?php

/* Добавляет новый товар ====================================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& name              |  название товара
& description       |  описание товара
& category_id       |  идентификатор категории товара
& price             |  цена товара
& image             |  путь к изображению
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
& deleted           |  статус товара (удалён или не удалён)
& url               |  ссылка на сайт товара
& album_ids         |  подборки, куда добавить товар (через запятую)
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($name)) {
    $error['error']['error_msg'] = 'Not found required param: name';
    return json_encode($error);
}

if (!isset($description)) {
    $error['error']['error_msg'] = 'Not found required param: description';
    return json_encode($error);
}

if (!isset($category_id)) {
    $error['error']['error_msg'] = 'Not found required param: category_id';
    return json_encode($error);
}

if (!isset($price)) {
    $error['error']['error_msg'] = 'Not found required param: price';
    return json_encode($error);
}

if (!isset($image)) {
    $error['error']['error_msg'] = 'Not found required param: image';
    return json_encode($error);
}


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

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'name' => $name,
    'description' => $description,
    'category_id' => $category_id,
    'price' => $price,
    'main_photo_id' => $main_photo_id,
    'deleted' => isset($deleted) ? $deleted : 0,
);

// Добавляем к запросу доп. параметры
if (isset($url)) {
    $request_params['url'] = $url;
}

// Создаём товар в сообществе
$add = $vk->market__add($request_params);

// Если товар не создан
if (!isset($add['market_item_id'])) {
    return $add; // выводим отчёт об ошибке
}

// Получаем ID созданного товара
$market_item_id = $add['market_item_id'];

// Генерируем отчёт об успешном создании товара
$json_add = array(
    'success' => array(
        'message' => 'Item created',
        'request_params' => array(
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
                'key' => 'main_photo_id',
                'value' => $main_photo_id
            ),
            array(
                'key' => 'deleted',
                'value' => isset($deleted) ? $deleted : 0
            )
        ),
        'response' => array(
            'key' => 'market_item_id',
            'value' => $market_item_id
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($url)) {
    array_push(
        $json_add['success']['request_params'],
        array(
            'key' => 'url',
            'value' => $url
        )
    );
}

// Если при вызове был указан список подборок
if (isset($album_ids)) {

    // Добавляем созданный товар в указанные подборки
    $addToAlbum = $vk->market__addToAlbum([
        'owner_id' => "-$group_id",
        'item_id' => $market_item_id,
        'album_ids' => $album_ids
    ]);

    // Если товар не добавлен в какую-либо подборку
    if ($addToAlbum !== 1) {

        $error = json_decode($addToAlbum, true); // копируем отчет об ошибке добавления в подборки
        $error['success'] = $json_add['success']; // добавляем в него отчёт об успешном создании товара
        return json_encode($error, JSON_UNESCAPED_UNICODE); // выводим отчёт об ошибке

    }

    // Генерируем отчёт об успешном добавлении товара в подборки
    $json_addToAlbum = array(
        'success' => array(
            'message' => 'Item added to albums',
            'response' => $addToAlbum,
            'request_params' => array(
                array(
                    'key' => 'item_id',
                    'value' => $market_item_id
                ),
                array(
                    'key' => 'album_ids',
                    'value' => $album_ids
                )
            )
        )
    );

    $success = array();
    $success[0] = $json_add;
    $success[1] = $json_addToAlbum;
    $success = json_encode($success, JSON_UNESCAPED_UNICODE);

    return $success; // Выводим отчёт об успешном создании товара и добавлении его в подборки
}

$success = json_encode($json_add, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном создании товара
