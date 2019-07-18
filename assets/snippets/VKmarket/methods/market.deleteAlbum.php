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
& response          |  тип успешного результата
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($album_id)) {
    $error['error']['error_msg'] = 'Not found required param: album_id';
    return json_encode($error, true);
}

// Удаляем подборку
$request = $vk->deleteAlbum([
    'owner_id' => "-$group_id",
    'album_id' => $album_id
]);

// Если подборка не удалёна
if ($request !== 1) {
    return $request; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном добавлении товара в подборки
$result = array(
    'success' => array(
        'message' => 'Album deleted',
        'response' => $request,
        'request_params' => array(
            array(
                'key' => 'album_id',
                'value' => (int)$album_id
            )
        )
    )
);

// Выводим отчёт об успешном удалении товара
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
