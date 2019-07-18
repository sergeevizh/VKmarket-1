<?php

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
$error = array('error' => array('error_code' => 'required'));

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    return json_encode($error, true);
}

// Удаляем товар из сообщества
$request = $vk->delete([
    'owner_id' => "-$group_id",
    'item_id' => $item_id
]);

// Если товар не удалён
if ($request !== 1) {
    return $request; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном добавлении товара в подборки
$result = array(
    'success' => array(
        'message' => 'Item deleted',
        'response' => $request,
        'request_params' => array(
            array(
                'key' => 'item_id',
                'value' => (int)$item_id
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
