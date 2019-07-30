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

switch ($modx->event->name) {

    case 'OnDocFormPrerender':

        switch ($template) {
            case $template_album:
                // ПОДБОРКИ ======================================
                // -----------------------------------------------

                break;

            case $template_item:

                $parent = (int) $modx->runSnippet("DocInfo", array(
                    "docid" => $id,
                    "field" => "parent"
                ));

                // ТОВАРЫ ========================================
                // -----------------------------------------------
                $name = $modx->runSnippet("DocLister", array(
                    "tvList" => $tv_list,
                    "documents" => $id,
                    "tpl" => $item_name_tpl,
                    "ownerTPL" => "@CODE:[+dl.wrap+]"
                ));
                $description = $modx->runSnippet("DocLister", array(
                    "tvList" => $tv_list,
                    "documents" => $id,
                    "tpl" => $item_description_tpl,
                    "ownerTPL" => "@CODE:[+dl.wrap+]"
                ));
                $price = $modx->runSnippet("DocLister", array(
                    "tvList" => $tv_list,
                    "documents" => $id,
                    "tpl" => $item_price_tpl,
                    "ownerTPL" => "@CODE:[+dl.wrap+]"
                ));
                $image = $modx->runSnippet("DocLister", array(
                    "tvList" => $tv_list,
                    "documents" => $id,
                    "tpl" => $item_image_tpl,
                    "ownerTPL" => "@CODE:[+dl.wrap+]"
                ));
                $category_id = $modx->runSnippet("DocInfo", array(
                    "docid" => $parent,
                    "field" => $album_category_id
                ));

                $params = array(
                    "name" => $item_name,
                    "description" => $item_description,
                    "item_price" => $item_price,
                    "image" => $image,
                    "category_id" => $category_id
                );
                return alert("success", "Тест параметров", $params);

                break;
        }
        break;
}
