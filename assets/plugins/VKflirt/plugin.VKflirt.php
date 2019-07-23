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


// текущий шаблон
$template = $modx->getTemplateVar('template', "*", $id)['value'];

// TV-параметры для установки связи с ВК

function getChecked($case)
{
    global $modx;
    global $id;

    $result = array();

    switch ($case) {
        case 'product':

            $checked = $modx->getTemplateVar('slot-have', "*", $id)['value'];

            if (strpos($checked, "6") !== false) {
                $result['original'] = 1;
            }
            if (strpos($checked, "1") !== false) {
                $result['license'] = 1;
            }
            if (strpos($checked, "2") !== false) {
                $result['spray'] = 1;
            }
            if (strpos($checked, "3") !== false) {
                $result['probnik'] = 1;
            }
            if (strpos($checked, "4") !== false) {
                $result['phero10'] = 1;
            }
            if (strpos($checked, "5") !== false) {
                $result['mini'] = 1;
            }
    }

    return $result;
}

// функция генерирования параметров для VKmarket
function getParamsList($case)
{
    global $modx;
    global $id;

    $result = array();

    switch ($case) {

        case 'product':
            $albums_ID = array(
                1 => 140,
                2 => 141,
                3 => 142,
                "new" => 4,
                "hit" => 3
            );

            $water_TYPES = array(
                1 => 'Туалетная вода',
                2 => 'Парфюмированная вода',
                3 => 'Одеколон'
            );

            $for_WHOM = array(
                1 => 'Женская',
                2 => 'Мужская',
                3 => 'Унисекс'
            );

            $parent = $modx->getTemplateVar('parent', "*", $id)['value'];
            $brand = $modx->getTemplateVar('pagetitle', "*", $parent)['value'];
            $pagetitle = $modx->getTemplateVar('pagetitle', "*", $id)['value'];
            $slot_desc = $modx->getTemplateVar('slot-desc', "*", $id)['value'];
            $slot_sex = $modx->getTemplateVar('slot-sex', "*", $id)['value'];
            $stickers = $modx->getTemplateVar('stickers', "*", $id)['value'];

            $result['photo']['original'] = $modx->getTemplateVar('slot-image', "*", $id)['value'];
            $result['photo']['license'] = $modx->getTemplateVar('slot-image', "*", $id)['value'];
            $result['photo']['spray'] = $modx->getTemplateVar('photo-spray', "*", $id)['value'];
            $result['photo']['probnik'] = $modx->getTemplateVar('photo-probnik', "*", $id)['value'];
            $result['photo']['phero10'] = $modx->getTemplateVar('photo-phero10', "*", $id)['value'];
            $result['photo']['mini'] = $modx->getTemplateVar('photo-mini', "*", $id)['value'];

            $result['price']['original'] = $modx->getTemplateVar('price-original', "*", 144)['value'];
            $result['price']['license'] = $modx->getTemplateVar('price-license', "*", 144)['value'];
            $result['price']['spray'] = $modx->getTemplateVar('price-spray', "*", 144)['value'];
            $result['price']['probnik'] = $modx->getTemplateVar('price-probnik', "*", 144)['value'];
            $result['price']['phero10'] = $modx->getTemplateVar('price-phero10', "*", 144)['value'];
            $result['price']['mini'] = $modx->getTemplateVar('price-mini', "*", 144)['value'];

            $result['name'] = $brand . ' "' . $pagetitle . '"';

            $type = $water_TYPES[$slot_desc];
            $sex = $for_WHOM[$slot_sex];
            $volume = $modx->getTemplateVar('slot-volume', "*", $id)['value'];;
            $top_notes = $modx->getTemplateVar('top-notes', "*", $id)['value'];
            $mid_notes = $modx->getTemplateVar('mid-notes', "*", $id)['value'];
            $bottom_notes = $modx->getTemplateVar('bottom-notes', "*", $id)['value'];

            $result['description'] = array();
            $result['description']['original'] = $type . " / " . $sex . " / Оригинал / Объём: " . $volume . " ml / Верхние ноты: " . $top_notes . " / Средние ноты: " . $mid_notes . " / Базовые ноты: " . $bottom_notes;
            $result['description']['license'] = $type . " / " . $sex . " / Лицензия / Объём: " . $volume . " ml / Верхние ноты: " . $top_notes . " / Средние ноты: " . $mid_notes . " / Базовые ноты: " . $bottom_notes;
            $result['description']['spray'] = $type . " / " . $sex . " / Спрей / Объём: 80 ml / Верхние ноты: " . $top_notes . " / Средние ноты: " . $mid_notes . " / Базовые ноты: " . $bottom_notes;
            $result['description']['probnik'] = $type . " / " . $sex . " / Пробник / Объём: 50 ml / Верхние ноты: " . $top_notes . " / Средние ноты: " . $mid_notes . " / Базовые ноты: " . $bottom_notes;
            $result['description']['phero10'] = $type . " / " . $sex . " / С феромонами / Объём: 10 ml / Верхние ноты: " . $top_notes . " / Средние ноты: " . $mid_notes . " / Базовые ноты: " . $bottom_notes;
            $result['description']['mini'] = $type . " / " . $sex . " / Миниатюра / Объём: 35 ml / Верхние ноты: " . $top_notes . " / Средние ноты: " . $mid_notes . " / Базовые ноты: " . $bottom_notes;

            $result['album_ids'] = array();
            array_push($result['album_ids'], $modx->getTemplateVar('vk_album_id', "*", $parent)['value']);
            array_push($result['album_ids'], $albums_ID[$slot_sex]);

            if (strpos($stickers, "new") !== false) {
                array_push($result['album_ids'], $albums_ID["new"]);
            }

            if (strpos($stickers, "hit") !== false) {
                array_push($result['album_ids'], $albums_ID["hit"]);
            }

            $result['vk']['original'] = $modx->getTemplateVar('vk-original_id', "*", $id)['value'];
            $result['vk']['license'] = $modx->getTemplateVar('vk-license_id', "*", $id)['value'];
            $result['vk']['spray'] = $modx->getTemplateVar('vk-spray_id', "*", $id)['value'];
            $result['vk']['probnik'] = $modx->getTemplateVar('vk-probnik_id', "*", $id)['value'];
            $result['vk']['phero10'] = $modx->getTemplateVar('vk-phero10_id', "*", $id)['value'];
            $result['vk']['mini'] = $modx->getTemplateVar('vk-mini_id', "*", $id)['value'];
            break;

        case 'album':

            $pagetitle = $modx->getTemplateVar('pagetitle', "*", $id)['value'];

            $result['album_id'] = $modx->getTemplateVar('vk_album_id', "*", $id)['value'];
            $result['title'] = $pagetitle;
            $result['image'] = $modx->getTemplateVar('logo-brand', "*", $id)['value'];

            break;
    }

    return $result;
}

// функция получения параметров для вызова VKMarket
function getDefault__VKMarket($method)
{
    $access_token = '5194f84133c637eafdd2581a320c56ca3e89f8fecca219e9d19d84a8c440c999074fdfe0e03f59462e2fd';
    $group_id = 46258011;

    $params = array();
    $params['api_method'] = $method;
    $params['access_token'] = $access_token;
    $params['group_id'] = $group_id;

    return $params;
}

// Функция добавления записи с ID товара ВКонтакте
function saveAdd($tv_VK, $value)
{
    global $modx;
    global $id;

    $table_name = $modx->getFullTableName('site_tmplvar_contentvalues');
    $fields = array('contentid'  => $id, 'tmplvarid' => $tv_VK, 'value'  => $value);
    $result = $modx->db->insert($fields, $table_name);

    if (!$result) {
        $modx->logEvent(1, 3, $result, 'id ресурса: ' . $id . ' / В базу: market.add');
    }
}

// Функция удаления записи с ID товара ВКонтакте
function saveDelete($tv_VK)
{
    global $modx;
    global $id;

    $table_name = $modx->getFullTableName('site_tmplvar_contentvalues');
    $result = $modx->db->delete($table_name, "contentid = $id AND tmplvarid = $tv_VK");

    if (!$result) {
        $modx->logEvent(1, 1, $result, "id ресурса: $id / Из базы: market.delete");
    }
}

// Функция добавления товара в VK
function addVKMarket($key, $paramsBefore, $paramsAfter)
{
    global $modx;

    $tvs_VK = array();
    $tvs_VK['license'] = 109;
    $tvs_VK['mini'] = 113;
    $tvs_VK['original'] = 108;
    $tvs_VK['phero10'] = 112;
    $tvs_VK['probnik'] = 111;
    $tvs_VK['spray'] = 110;

    $photo = $modx->runSnippet('phpthumb', array(
        'input' => $paramsAfter['photo'][$key],
        'options' => 'w=602,h=602,far=C,bg=ffffff,f=jpg,fltr[]=bord|80|1|1|ffffff'
    ));

    $paramsForVKMarket = getDefault__VKMarket('market.add');
    $paramsForVKMarket['category_id'] = 701;
    $paramsForVKMarket['price'] = $paramsAfter['price'][$key];
    $paramsForVKMarket['name'] = $paramsAfter['name'];
    $paramsForVKMarket['description'] = $paramsAfter['description'][$key];
    $paramsForVKMarket['image'] = $photo;

    $result = $modx->runSnippet('VKMarket', $paramsForVKMarket);

    $message = json_encode($result, JSON_UNESCAPED_UNICODE);
    if ($result['success']['response']) {
        // Если товар добавлен
        $modx->logEvent(1, 1, $message, "market.add : " . $paramsAfter['name'] . " -- $key");
        saveAdd($tvs_VK[$key], $result['success']['response']);
    } else {
        // Если товар не добавлен
        $modx->logEvent(1, 3, $message, "market.add : " . $paramsAfter['name'] . " -- $key");
    }
}

// Функция добавления подборки в VK
function addAlbumVKMarket($paramsBefore, $paramsAfter)
{
    global $modx;
    $tv_VK = 107;

    $paramsForVKMarket = getDefault__VKMarket('market.addAlbum');
    $paramsForVKMarket['title'] = $paramsAfter['title'];

    if ($paramsAfter['image']) {
        $photo = $modx->runSnippet('phpthumb', array(
            'input' => $paramsAfter['image'],
            'options' => 'w=1280,h=720,far=C,bg=f6f6f6,f=jpg,fltr[]=bord|150|1|1|f6f6f6'
        ));
        $paramsForVKMarket['image'] = $photo;
    }

    $result = $modx->runSnippet('VKMarket', $paramsForVKMarket);

    $message = json_encode($result, JSON_UNESCAPED_UNICODE);
    if ($result['success']['response']) {
        // Если подборка добавлена
        $modx->logEvent(1, 1, $message, "market.addAlbum : " . $paramsAfter['title']);
        saveAdd($tv_VK, $result['success']['response']);
    } else {
        // Если подборка не добавлена
        $modx->logEvent(1, 3, $message, "market.addAlbum : " . $paramsAfter['title']);
    }
}

// Функция редактирования товара в VK
function editVKMarket($key, $paramsBefore, $paramsAfter)
{
    global $modx;

    $tvs_VK = array();
    $tvs_VK['license'] = 109;
    $tvs_VK['mini'] = 113;
    $tvs_VK['original'] = 108;
    $tvs_VK['phero10'] = 112;
    $tvs_VK['probnik'] = 111;
    $tvs_VK['spray'] = 110;

    $paramsForVKMarket = getDefault__VKMarket('market.edit');
    $paramsForVKMarket['item_id'] = $paramsBefore['vk'][$key];
    $paramsForVKMarket['category_id'] = 701;
    if ($paramsBefore['name'] !== $paramsAfter['name']) {
        $paramsForVKMarket['name'] = $paramsAfter['name'];
    }
    if ($paramsBefore['description'][$key] !== $paramsAfter['description'][$key]) {
        $paramsForVKMarket['description'] = $paramsAfter['description'][$key];
    }
    if ($paramsBefore['photo'][$key] !== $paramsAfter['photo'][$key]) {
        $photo = $modx->runSnippet('phpthumb', array(
            'input' => $paramsAfter['photo'][$key],
            'options' => 'w=602,h=602,far=C,bg=ffffff,f=jpg,fltr[]=bord|80|1|1|ffffff'
        ));
        $paramsForVKMarket['image'] = $photo;
    }

    $result = $modx->runSnippet('VKMarket', $paramsForVKMarket);

    $message = json_encode($result, JSON_UNESCAPED_UNICODE);
    if ($result['success']['response']) {
        // Если товар отредактирован
        $modx->logEvent(1, 1, $message, "market.edit : " . $paramsAfter['name'] . " -- $key");
        saveAdd($tvs_VK[$key], $paramsBefore['vk'][$key]);
    } else {
        // Если товар не отредактирован
        $modx->logEvent(1, 3, $message, "market.edit : " . $paramsAfter['name'] . " -- $key");
    }
}

// Функция редактирования подборки в VK
function editAlbumVKMarket($paramsBefore, $paramsAfter)
{
    global $modx;
    $tv_VK = 107;

    $paramsForVKMarket = getDefault__VKMarket('market.editAlbum');
    $paramsForVKMarket['album_id'] = $paramsBefore['album_id'];
    $paramsForVKMarket['title'] = $paramsAfter['title'];

    if ($paramsAfter['image'] !== $paramsBefore['image']) {
        $photo = $modx->runSnippet('phpthumb', array(
            'input' => $paramsAfter['image'],
            'options' => 'w=1280,h=720,far=C,bg=f6f6f6,f=jpg,fltr[]=bord|150|1|1|f6f6f6'
        ));
        $paramsForVKMarket['image'] = $photo;
    }

    $result = $modx->runSnippet('VKMarket', $paramsForVKMarket);

    $message = json_encode($result, JSON_UNESCAPED_UNICODE);
    if ($result['success']['response']) {
        // Если подборка отредактирована
        $modx->logEvent(1, 1, $message, "market.editAlbum : " . $paramsAfter['title']);
        saveAdd($tv_VK, $paramsBefore['album_id']);
    } else {
        // Если подборка не отредактирована
        $modx->logEvent(1, 3, $message, "market.editAlbum : " . $paramsAfter['title']);
    }
}

// Функция редактирования товара в VK
function deleteVKMarket($key, $paramsBefore, $paramsAfter)
{
    global $modx;

    $tvs_VK = array();
    $tvs_VK['license'] = 109;
    $tvs_VK['mini'] = 113;
    $tvs_VK['original'] = 108;
    $tvs_VK['phero10'] = 112;
    $tvs_VK['probnik'] = 111;
    $tvs_VK['spray'] = 110;

    $paramsForVKMarket = getDefault__VKMarket('market.delete');
    $paramsForVKMarket['item_id'] = $paramsBefore['vk'][$key];

    $result = $modx->runSnippet('VKMarket', $paramsForVKMarket);

    $message = json_encode($result, JSON_UNESCAPED_UNICODE);
    if ($result['success']['response']) {
        // Если товар отредактирован
        $modx->logEvent(1, 1, $message, "market.delete : " . $paramsAfter['name'] . " -- $key");
        saveDelete($tvs_VK[$key]);
    } else {
        // Если товар не отредактирован
        $modx->logEvent(1, 3, $message, "market.delete : " . $paramsAfter['name'] . " -- $key");
    }
}

switch ($modx->event->name) {

    case 'OnDocFormPrerender':

        switch ($template) {
            case 7:
                // ПОДБОРКИ ======================================
                // -----------------------------------------------

                // генерируем параметры "ДО"
                $before = getParamsList('album');

                // запоминаем параметры "ДО"
                $_SESSION['before'] = $before;

                break;

            case 9:
                // ТОВАРЫ ========================================
                // -----------------------------------------------

                // генерируем параметры "ДО"
                $before = getParamsList('product');
                $checkedBefore = getChecked('product');

                // запоминаем параметры "ДО"
                $_SESSION['before'] = $before;
                $_SESSION['checkedBefore'] = $checkedBefore;

                break;
        }

        break;

    case 'OnDocFormSave':

        switch ($template) {
            case 7:
                // ПОДБОРКИ ======================================
                // -----------------------------------------------

                // вспоминаем параметры "ДО"
                $before =  $_SESSION['before'];

                // генерируем параметры "ПОСЛЕ"
                $after = getParamsList('album');

                // если подборки не было в ВК
                if (empty($before['album_id'])) {

                    // market.addAlbum ========================================
                    addAlbumVKMarket($before, $after);
                    // END / market.addAlbum ========================================

                } else if ($before !== $after) {

                    // market.editAlbum ========================================
                    editAlbumVKMarket($before, $after);
                    // END / market.editAlbum ----------------------------------

                }

                break;
            case 9:
                // ТОВАРЫ ========================================
                // -----------------------------------------------

                // вспоминаем параметры "ДО"
                $before =  $_SESSION['before'];
                $checkedBefore =  $_SESSION['checkedBefore'];

                // генерируем параметры "ПОСЛЕ"
                $after = getParamsList('product');
                $checkedAfter = getChecked('product');


                // если отмечен "ОРИГИНАЛ"
                if ($checkedAfter['original']) {

                    if (!$checkedBefore['original']) {

                        // market.add : ОРИГИНАЛ ========================================
                        addVKMarket('original', $before, $after);
                        // END / market.add ========================================

                    } else if ($before !== $after) {

                        // market.edit : ОРИГИНАЛ ========================================
                        editVKMarket('original', $before, $after);
                        // END / market.edit ----------------------------------

                    }
                } else if ($checkedBefore['original']) {

                    // market.delete : ОРИГИНАЛ ========================================
                    deleteVKMarket('original', $before, $after);
                    // END / market.delete ----------------------------------
                }

                // если отмечен "ЛИЦЕНЗИЯ"
                if ($checkedAfter['license']) {

                    if (!$checkedBefore['license']) {

                        // market.add : ЛИЦЕНЗИЯ ========================================
                        addVKMarket('license', $before, $after);
                        // END / market.add ----------------------------------

                    } else if ($before !== $after) {

                        // market.edit : ЛИЦЕНЗИЯ ========================================
                        editVKMarket('license', $before, $after);
                        // END / market.edit ----------------------------------

                    }
                } else if ($checkedBefore['license']) {

                    // market.delete : ЛИЦЕНЗИЯ ========================================
                    deleteVKMarket('license', $before, $after);
                    // END / market.delete ----------------------------------
                }

                // если отмечен "СПРЕЙ"
                if ($checkedAfter['spray']) {

                    if (!$checkedBefore['spray']) {

                        // market.add : СПРЕЙ ========================================
                        addVKMarket('spray', $before, $after);
                        // END / market.add ========================================

                    } else if ($before !== $after) {

                        // market.edit : СПРЕЙ ========================================
                        editVKMarket('spray', $before, $after);
                        // END / market.edit ----------------------------------

                    }
                } else if ($checkedBefore['spray']) {

                    // market.delete : СПРЕЙ ========================================
                    deleteVKMarket('spray', $before, $after);
                    // END / market.delete ----------------------------------
                }

                // если отмечен "ПРОБНИК"
                if ($checkedAfter['probnik']) {

                    if (!$checkedBefore['probnik']) {

                        // market.add : ПРОБНИК ========================================
                        addVKMarket('probnik', $before, $after);
                        // END / market.add ========================================

                    } else if ($before !== $after) {

                        // market.edit : ПРОБНИК ========================================
                        editVKMarket('probnik', $before, $after);
                        // END / market.edit ----------------------------------

                    }
                } else if ($checkedBefore['probnik']) {

                    // market.delete : ПРОБНИК ========================================
                    deleteVKMarket('probnik', $before, $after);
                    // END / market.delete ----------------------------------
                }

                // если отмечен "C ФЕРОМОНАМИ"
                if ($checkedAfter['phero10']) {

                    if (!$checkedBefore['phero10']) {

                        // market.add : C ФЕРОМОНАМИ ========================================
                        addVKMarket('phero10', $before, $after);
                        // END / market.add ========================================

                    } else if ($before !== $after) {

                        // market.edit : C ФЕРОМОНАМИ ========================================
                        editVKMarket('phero10', $before, $after);
                        // END / market.edit ----------------------------------

                    }
                } else if ($checkedBefore['phero10']) {

                    // market.delete : C ФЕРОМОНАМИ ========================================
                    deleteVKMarket('phero10', $before, $after);
                    // END / market.delete ----------------------------------
                }

                // если отмечен "МИНИАТЮРА"
                if ($checkedAfter['mini']) {

                    if (!$checkedBefore['mini']) {

                        // market.add : МИНИАТЮРА ========================================
                        addVKMarket('mini', $before, $after);
                        // END / market.add ========================================

                    } else if ($before !== $after) {

                        // market.edit : МИНИАТЮРА ========================================
                        editVKMarket('mini', $before, $after);
                        // END / market.edit ----------------------------------

                    }
                } else if ($checkedBefore['mini']) {

                    // market.delete : МИНИАТЮРА ========================================
                    deleteVKMarket('mini', $before, $after);
                    // END / market.delete ----------------------------------
                }

                break;
        }

        break;
}
