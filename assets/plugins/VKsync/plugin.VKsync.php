<?php

# TODO: настроить работу с подборками
# TODO: настроить перераспределение по подборкам
# TODO: переделать всё на Class

# TODO: OnBeforeDocDuplicate        ДО      создания дубликата
# TODO: OnDocDuplicate              ПОСЛЕ   создания дубликата
# TODO: OnBeforeDocFormDelete       ДО      удаления документа
# TODO: OnDocFormDelete             ПОСЛЕ   удаления документа
# TODO: OnDocFormUnDelete           ПОСЛЕ   восстановления документа
# TODO: OnBeforeEmptyTrash          ДО      очистки корзины
# TODO: OnEmptyTrash                ПОСЛЕ   очистки корзины
# TODO: OnBeforeMoveDocument        ДО      перемещения документа
# TODO: OnAfterMoveDocument         ПОСЛЕ   перемещения документа

require_once MODX_BASE_PATH . "assets/plugins/VKsync/class.VKsync.php";

// Проверяем заполненность обязательных параметров конфигурации
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($access_token)) {
    $error['error']['error_msg'] = 'Not found required param: access_token';
    // выводим отчёт об ошибке
    return alert("error", "Not found access_token", $error);
}

if (!isset($group_id)) {
    $error['error']['error_msg'] = 'Not found required param: group_id';
    // выводим отчёт об ошибке
    return alert("error", "Not found group_id", $error);
}

$params = $modx->runSnippet("VKmarket", array(
    "api_method" => "market.search",
    "access_token" => $access_token,
    "group_id" => $group_id
));

alert("success", "First test", $params);
