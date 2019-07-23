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
& response          |  тип успешного результата
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($name)) {
    $error['error']['error_msg'] = 'Not found required param: name';
    return json_encode($error, true);
}

if (!isset($description)) {
    $error['error']['error_msg'] = 'Not found required param: description';
    return json_encode($error, true);
}

if (!isset($category_id)) {
    $error['error']['error_msg'] = 'Not found required param: category_id';
    return json_encode($error, true);
}

if (!isset($price)) {
    $error['error']['error_msg'] = 'Not found required param: price';
    return json_encode($error, true);
}

if (!isset($image)) {
    $error['error']['error_msg'] = 'Not found required param: image';
    return json_encode($error, true);
}


$image_path = 'image.jpg';
copy(MODX_BASE_PATH . $image, 'image.jpg');

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
$request = $vk->add($request_params);

// Если товар не создан
if (!isset($request['market_item_id'])) {
    return $request; // выводим отчёт об ошибке
}

// Получаем ID созданного товара
$market_item_id = $request['market_item_id'];

// Генерируем отчёт об успешном создании товара
$result = array(
    'success' => array(
        'message' => 'Item created',
        'response' => $market_item_id,
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
                'value' => (int) $category_id
            ),
            array(
                'key' => 'price',
                'value' => (int) $price
            ),
            array(
                'key' => 'main_photo_id',
                'value' => $main_photo_id
            ),
            array(
                'key' => 'deleted',
                'value' => isset($deleted) ? (int) $deleted : 0
            )
        )
    )
);

// Добавляем к отчёту доп. параметры
if (isset($url)) {
    array_push(
        $result['success']['request_params'],
        array(
            'key' => 'url',
            'value' => $url
        )
    );
}

// Выводим отчёт об успешном создании товара
switch ($response) {
    case 'id':
        return $market_item_id;
        break;

    case 'json':
    default:
        return $result;
        break;
}
