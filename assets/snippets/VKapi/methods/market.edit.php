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
& name              |  новое название товара
& description       |  новое описание товара
& category_id       |  идентификатор новой категории товара
& price             |  новая цена товара
& deleted           |  новый статус товара (удалён или не удалён)
& image             |  путь к новому изображению
& url               |  новая ссылка на сайт товара
& v                 |  версия API
& response          |  тип успешного результата
============================================================= */

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

// Если при вызове было указано изображение
if (isset($image)) {

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
}

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'item_id' => $item_id
);

// Добавляем к запросу доп. параметры
if (isset($name)) {
    $request_params['name'] = $name;
}
if (isset($description)) {
    $request_params['description'] = $description;
}
if (isset($category_id)) {
    $request_params['category_id'] = $category_id;
}
if (isset($price)) {
    $request_params['price'] = $price;
}
if (isset($deleted)) {
    $request_params['deleted'] = $deleted;
}
if (isset($image)) {
    $request_params['main_photo_id'] = $main_photo_id;
}
if (isset($url)) {
    $request_params['url'] = $url;
}

// Редактируем товар в сообществе
$request = $vk->request('market.edit', $request_params);

// Если товар не отредактирован
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Item edited',
        'response' => 1,
        'request_params' => array(
            'item_id' => (int) $item_id
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($name)) {
    $result['success']['request_params']['name'] = $name;
}
if (isset($description)) {
    $result['success']['request_params']['description'] = $description;
}
if (isset($category_id)) {
    $result['success']['request_params']['category_id'] = (int) $category_id;
}
if (isset($price)) {
    $result['success']['request_params']['price'] = (int) $price;
}
if (isset($deleted)) {
    $result['success']['request_params']['deleted'] = (int) $deleted;
}
if (isset($image)) {
    $result['success']['request_params']['image'] = $image;
    $result['success']['request_params']['main_photo_id'] = (int) $main_photo_id;
}
if (isset($url)) {
    $result['success']['request_params']['url'] = $url;
}

// Выводим отчёт об успехе
return $vk->report($response, $result);
