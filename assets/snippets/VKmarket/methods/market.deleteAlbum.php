<?php

/* Удаляет подборку с товарами ==============================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
& album_id          |  идентификатор подборки
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($album_id)) {
    $error['error']['error_msg'] = 'Not found required param: album_id';
    return json_encode($error);
}

// Удаляем подборку
$deleteAlbum = $vk->market__deleteAlbum([
    'owner_id' => "-$group_id",
    'album_id' => $album_id
]);

// Если подборка не удалёна
if ($deleteAlbum !== 1) {
    return $deleteAlbum; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном добавлении товара в подборки
$json_deleteAlbum = array(
    'success' => array(
        'message' => 'Album deleted',
        'response' => $deleteAlbum,
        'request_params' => array(
            array(
                'key' => 'album_id',
                'value' => $album_id
            )
        )
    )
);

$success = json_encode($json_deleteAlbum, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном удалении товара
