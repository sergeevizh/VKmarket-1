<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/* Удаляет товар из сообщества ==============================
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

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'item_id' => $item_id
);

// Удаляем товар из сообщества
$request = $vk->request('market.delete', $request_params);

// Если товар не удалён
if ($request !== 1) {
    // выводим отчёт об ошибке
    return $vk->report($response, $request);
}

// Генерируем отчёт об успехе
$result = array(
    'success' => array(
        'message' => 'Item deleted',
        'response' => 1,
        'request_params' => array(
            'item_id' => (int) $item_id
        )
    )
);

// Выводим отчёт об успехе
return $vk->report($response, $result);
