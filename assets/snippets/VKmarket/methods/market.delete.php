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
============================================================= */


// Проверяем наличие обязательных параметров
$error = array('error' => array('error_code' => 'required'));

if (!isset($item_id)) {
    $error['error']['error_msg'] = 'Not found required param: item_id';
    return json_encode($error);
}

// Удаляем товар из сообщества
$delete = $vk->market__delete([
    'owner_id' => "-$group_id",
    'item_id' => $item_id
]);

// Если товар не удалён
if ($delete !== 1) {
    return $delete; // выводим отчёт об ошибке
}

// Генерируем отчёт об успешном добавлении товара в подборки
$json_delete = array(
    'success' => array(
        'message' => 'Item deleted',
        'response' => $delete,
        'request_params' => array(
            array(
                'key' => 'item_id',
                'value' => $item_id
            )
        )
    )
);

$success = json_encode($json_delete, JSON_UNESCAPED_UNICODE);
return $success; // Выводим отчёт об успешном удалении товара
