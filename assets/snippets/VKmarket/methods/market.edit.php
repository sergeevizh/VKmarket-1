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
& response          |  тип успешного результата
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    return json_encode($error, true);
}

// Если нужно заменить изображение
if (isset($image)) {

    $image_path = 'image.jpg';
    copy($image, 'image.jpg');

    // Получаем сервер VK для загрузки изображения товара
    $server = $vk->getMarketUploadServer($group_id, 1);

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
    $save = $vk->saveMarketPhoto(
        [
            'group_id' => $group_id,
            'photo' => $upload['photo'],
            'server' => $upload['server'],
            'hash' => $upload['hash'],
            'crop_data' => $upload['crop_data'],
            'crop_hash' => $upload['crop_hash']
        ]
    );

    // Если изображение не сохранено на сервере VK
    if (!isset($save[0]['id'])) {
        return $save; // выводим отчёт об ошибке
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
$request = $vk->edit($request_params);

// Если товар не отредактирован
if ($request !== 1) {
    return $request; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном редактировании товара
$result = array(
    'success' => array(
        'message' => 'Item edited',
        'response' => $request,
        'request_params' => array(
            array(
                'key' => 'item_id',
                'value' => (int)$item_id
            )
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($name)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'name',
            'value' => $name
        )
    );
}
if (isset($description)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'description',
            'value' => $description
        )
    );
}
if (isset($category_id)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'category_id',
            'value' => (int)$category_id
        )
    );
}
if (isset($price)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'price',
            'value' => (int)$price
        )
    );
}
if (isset($deleted)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'deleted',
            'value' => (int)$deleted
        )
    );
}
if (isset($main_photo_id)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'main_photo_id',
            'value' => $main_photo_id
        )
    );
}
if (isset($url)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'url',
            'value' => $url
        )
    );
}

// Выводим отчёт об успешном редактировании товара
$success = json_encode($result, JSON_UNESCAPED_UNICODE);
switch ($response) {
    case 1:
        return $request;
        break;

    case 'json':
    default:
        return $success;
        break;
}
