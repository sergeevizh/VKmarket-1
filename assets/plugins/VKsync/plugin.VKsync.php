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

$params = $modx->runSnippet("VKmarket", array(
    "api_method" => "market.search",
    "access_token" => "$access_token",
    "group_id" => $group_id
));

alert("error", "First test", $params);
