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

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'album_id' => $album_id
);

// Удаляем товар из сообщества
$request = $vk->request('market.deleteAlbum', $request_params);

// Если подборка не удалёна
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Album deleted',
        'response' => 1,
        'request_params' => array(
            'album_id' => (int) $album_id
        )
    )
);

// Выводим отчёт об успехе
return $vk->report($response, $result);
