<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

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
& deleted           |  статус товара (удалён или не удалён)
& url               |  ссылка на сайт товара
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($name)) {
    $error['error']['error_msg'] = 'Not found required param: name';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($description)) {
    $error['error']['error_msg'] = 'Not found required param: description';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($category_id)) {
    $error['error']['error_msg'] = 'Not found required param: category_id';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($price)) {
    $error['error']['error_msg'] = 'Not found required param: price';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($image)) {
    $error['error']['error_msg'] = 'Not found required param: image';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

// Получаем сервер VK для загрузки изображения товара
$server_params = array(
    'group_id' => $group_id,
    'main_photo' => 1
);
$server = $vk->request('photos.getMarketUploadServer', $server_params);

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
    'hash' => $upload['hash'],
    'crop_data' => $upload['crop_data'],
    'crop_hash' => $upload['crop_hash']
);
$save = $vk->request('photos.saveMarketPhoto', $save_params);

// Если изображение не сохранено на сервере VK
if (!isset($save[0]['id'])) {
    // выводим отчёт об ошибке
    return $vk->report($response, $save);
}

// Получаем ID загруженного изображения
$main_photo_id = $save[0]['id'];

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'name' => $name,
    'description' => $description,
    'category_id' => $category_id,
    'price' => $price,
    'main_photo_id' => $main_photo_id
);

// Добавляем к запросу доп. параметры
if (isset($url)) {
    $request_params['url'] = $url;
}
if (isset($deleted)) {
    $request_params['deleted'] = $deleted;
}

// Создаём товар в сообществе
$request = $vk->request('market.add', $request_params);

// Если товар не создан
if (!isset($request['market_item_id'])) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Получаем ID созданного товара
$market_item_id = $request['market_item_id'];

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Item created',
        'response' => (int) $market_item_id,
        'request_params' => array(
            'name' => $name,
            'description' => $description,
            'category_id' => (int) $category_id,
            'price' => (int) $price,
            'image' => $image,
            'main_photo_id' => (int) $main_photo_id,
            'deleted' => isset($deleted) ? (int) $deleted : 0
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($url)) {
    $result['success']['request_params']['url'] = $url;
}

// Выводим отчёт об успехе
return $vk->report($response, $result);
