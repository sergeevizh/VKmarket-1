<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKmarket
{
    public function __construct($modx, $config)
    {
        $this->modx = $modx;
        $this->config = $config;

        $this->module_id = (int) $_GET['id'];
        $this->module_url = 'index.php?a=112&id=' . $this->moduleid;
        $this->theme = $this->modx->config['manager_theme'] ? $this->modx->config['manager_theme'] : 'default';
        $this->jquery_path = $this->modx->config['mgr_jquery_path'] ? $this->modx->config['mgr_jquery_path'] : 'media/script/jquery/jquery.min.js';
    }

    public function getFileContents($file)
    {
        if (empty($file)) {
            return false;
        } else {
            $file = MODX_BASE_PATH . 'assets/modules/VKmarket/templates/' . $file;
            $contents = file_get_contents($file);
            return $contents;
        }
    }

    public function alert($case, $title, $params)
    {
        /* Выводит сообщение в лог EVO ==============================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & case              |  тип сообщения : error / success
        & title             |  код сообщения (заголовок)
        & params            |  json с подробностями
        ============================================================= */

        switch ($case) {
            case 'error':
            default:
                $this->modx->logEvent(1, 3, json_encode($params, JSON_UNESCAPED_UNICODE), '<b>VKmarket</b>: ' . $title);
                break;

            case 'success':
                $this->modx->logEvent(1, 1, json_encode($params, JSON_UNESCAPED_UNICODE), '<b>VKmarket</b>: ' . $title);
                break;
        }
    }

    public function getParams($id, $template, $type)
    {
        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $parents = $this->modx->getParentIds($id);
                $parent = reset($parents);
                $albums = array();

                $parent_album_id = $this->modx->runSnippet('DocInfo', array(
                    'docid' => $parent,
                    'field' => 'vk_album_id'
                ));
                if ($parent_album_id)  array_push($albums, $parent_album_id);

                // TODO: Новинки / Хиты / Для кого
                $albumsVK = array(
                    'hit' => 3,
                    'female' => 140,
                    'male' => 141,
                    'unisex' => 142,
                    'original' => 144,
                    'license' => 145,
                    'spray' => 146,
                    'probnik' => 147,
                    'phero10' => 148,
                    'mini' => 149
                );
                $isHit = $this->modx->runSnippet('DocInfo', array(
                    'docid' => $id,
                    'field' => 'slot-hit'
                ));
                if ($isHit)  array_push($albums, $albumsVK['hit']);

                $slotSex = $this->modx->runSnippet('DocInfo', array(
                    'docid' => $id,
                    'field' => 'slot-sex'
                ));
                switch ($slotSex) {
                    case 1:
                        array_push($albums, $albumsVK['female']);
                        break;
                    case 2:
                        array_push($albums, $albumsVK['male']);
                        break;
                    case 3:
                        array_push($albums, $albumsVK['unisex']);
                        break;
                }

                switch ($type) {
                    case 'original':
                        array_push($albums, $albumsVK['original']);

                        $name = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['original_name_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $description = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['original_description_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $category_id = $this->modx->runSnippet('DocInfo', array(
                            'docid' => $parent,
                            'field' => 'vk_category_id'
                        ));

                        $price = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['original_price_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $image = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['original_image_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $url = $this->modx->makeUrl($id, '', '', 'full');
                        break;

                    case 'license':
                        array_push($albums, $albumsVK['license']);

                        $name = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['license_name_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $description = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['license_description_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $category_id = $this->modx->runSnippet('DocInfo', array(
                            'docid' => $parent,
                            'field' => 'vk_category_id'
                        ));

                        $price = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['license_price_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $image = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['license_image_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $url = $this->modx->makeUrl($id, '', '', 'full');
                        break;

                    case 'spray':
                        array_push($albums, $albumsVK['spray']);

                        $name = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['spray_name_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $description = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['spray_description_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $category_id = $this->modx->runSnippet('DocInfo', array(
                            'docid' => $parent,
                            'field' => 'vk_category_id'
                        ));

                        $price = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['spray_price_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $image = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['spray_image_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $url = $this->modx->makeUrl($id, '', '', 'full');
                        break;

                    case 'probnik':
                        array_push($albums, $albumsVK['probnik']);

                        $name = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['probnik_name_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $description = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['probnik_description_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $category_id = $this->modx->runSnippet('DocInfo', array(
                            'docid' => $parent,
                            'field' => 'vk_category_id'
                        ));

                        $price = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['probnik_price_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $image = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['probnik_image_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $url = $this->modx->makeUrl($id, '', '', 'full');
                        break;

                    case 'phero10':
                        array_push($albums, $albumsVK['phero10']);

                        $name = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['phero10_name_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $description = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['phero10_description_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $category_id = $this->modx->runSnippet('DocInfo', array(
                            'docid' => $parent,
                            'field' => 'vk_category_id'
                        ));

                        $price = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['phero10_price_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $image = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['phero10_image_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $url = $this->modx->makeUrl($id, '', '', 'full');
                        break;

                    case 'mini':
                        array_push($albums, $albumsVK['mini']);

                        $name = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['mini_name_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $description = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['mini_description_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $category_id = $this->modx->runSnippet('DocInfo', array(
                            'docid' => $parent,
                            'field' => 'vk_category_id'
                        ));

                        $price = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['mini_price_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $image = $this->modx->runSnippet('DocLister', array(
                            'showNoPublish' => 1,
                            'tvList' => $this->config['tv_list'],
                            'documents' => $id,
                            'tpl' => $this->config['mini_image_tpl'],
                            'ownerTPL' => '@CODE:[+dl.wrap+]'
                        ));

                        $url = $this->modx->makeUrl($id, '', '', 'full');
                        break;
                }

                $result['name'] = $name;
                $result['description'] = $description;
                $result['category_id'] = $category_id;
                $result['price'] = $price;
                $result['image'] = $image;
                $result['url'] = $url;

                #TODO добавление в альбомы (спреи, миниатюры и т.п.)
                $albums = implode(",", $albums);
                $result['albums'] = $albums;

                return $result;

                break;

            case $this->config['template_album']:

                // ПОДБОРКА =========================================

                $title = $this->modx->runSnippet('DocLister', array(
                    'showNoPublish' => 1,
                    'tvList' => $this->config['tv_list'],
                    'documents' => $id,
                    'tpl' => $this->config['album_title_tpl'],
                    'ownerTPL' => '@CODE:[+dl.wrap+]'
                ));

                $image = $this->modx->runSnippet('DocLister', array(
                    'showNoPublish' => 1,
                    'tvList' => $this->config['tv_list'],
                    'documents' => $id,
                    'tpl' => $this->config['album_image_tpl'],
                    'ownerTPL' => '@CODE:[+dl.wrap+]'
                ));

                $result['title'] = $title;
                $result['image'] = $image;

                return $result;

                break;
        }
    }

    public function makeActions()
    {
        $function = $_POST['function'];
        $id = $_POST['id'];
        $template = $_POST['template'];
        $type = $_POST['type'];
        $vk_id = $_POST['vk_id'];
        $items = $_POST['items'];

        if ($id) {
            switch ($function) {
                case 'add':
                    $result = $this->add($id, $template, $type);
                    break;

                case 'edit':
                    $result = $this->edit($id, $template, $vk_id, $type);
                    break;

                case 'delete':
                    $result = $this->delete($id, $template, $vk_id, $type);
                    break;
            }

            header("Location: " . $_SERVER['REQUEST_URI']);
            return $result;
        }

        if ($items) {

            foreach ($items as $item) {

                $item = json_decode($item, true);

                switch ($function) {
                    case 'add':
                        $result = $this->add($item['id'], $template, $type);
                        break;

                    case 'edit':
                        $result = $this->edit($item['id'], $template, $item['vk_id'], $type);
                        break;

                    case 'delete':
                        $result = $this->delete($item['id'], $template, $item['vk_id'], $type);
                        break;
                }
            }

            header("Location: " . $_SERVER['REQUEST_URI']);
            return $result;
        }

        return false;
    }

    public function check($id, $template, $vk_id, $type)
    {
        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $params_get = $this->config['api'];
                $params_get['api_method'] = 'market.getById';
                $params_get['item_ids'] = $vk_id;
                $params_get['extended'] = 1;
                $request_get = $this->modx->runSnippet('VKapi', $params_get);

                if ($request_get['success']) {
                    $result = $request_get['success']['response']['items'][0];
                    return $result;
                } else {
                    switch ($type) {

                        case 'original':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_original_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'license':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_license_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'spray':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_spray_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'probnik':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_probnik_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'phero10':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_phero10_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'mini':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_mini_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;
                    }
                    return 0;
                }

                break;

            case $this->config['template_album']:

                // ПОДБОРКА =========================================

                $params_get = $this->config['api'];
                $params_get['api_method'] = 'market.getAlbumById';
                $params_get['album_ids'] = $vk_id;
                $request_get = $this->modx->runSnippet('VKapi', $params_get);

                if ($request_get['success']) {
                    $result = $request_get['success']['response']['items'][0];
                    return $result;
                } else {
                    $this->modx->db->delete(
                        $this->config['db']['tv_value'],
                        'tmplvarid="' . $this->config['tmplvarid']['vk_album_id'] . '" AND contentid="' . $id . '"'
                    );
                    return 0;
                }

                break;
        }
    }

    public function add($id, $template, $type)
    {
        $params = $this->getParams($id, $template, $type) + $this->config['api'];

        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $params['api_method'] = 'market.add';
                $request = $this->modx->runSnippet('VKapi', $params);

                if ($request['success']) {
                    $market_item_id = (int) $request['success']['response'];

                    switch ($type) {

                        case 'original':
                            $db_params = array(
                                'tmplvarid' => $this->config['tmplvarid']['vk_original_id'],
                                'contentid' => $id,
                                'value' => $market_item_id
                            );
                            $this->modx->db->insert(
                                $db_params,
                                $this->config['db']['tv_value']
                            );
                            break;

                        case 'license':
                            $db_params = array(
                                'tmplvarid' => $this->config['tmplvarid']['vk_license_id'],
                                'contentid' => $id,
                                'value' => $market_item_id
                            );
                            $this->modx->db->insert(
                                $db_params,
                                $this->config['db']['tv_value']
                            );
                            break;

                        case 'spray':
                            $db_params = array(
                                'tmplvarid' => $this->config['tmplvarid']['vk_spray_id'],
                                'contentid' => $id,
                                'value' => $market_item_id
                            );
                            $this->modx->db->insert(
                                $db_params,
                                $this->config['db']['tv_value']
                            );
                            break;

                        case 'probnik':
                            $db_params = array(
                                'tmplvarid' => $this->config['tmplvarid']['vk_probnik_id'],
                                'contentid' => $id,
                                'value' => $market_item_id
                            );
                            $this->modx->db->insert(
                                $db_params,
                                $this->config['db']['tv_value']
                            );
                            break;

                        case 'phero10':
                            $db_params = array(
                                'tmplvarid' => $this->config['tmplvarid']['vk_phero10_id'],
                                'contentid' => $id,
                                'value' => $market_item_id
                            );
                            $this->modx->db->insert(
                                $db_params,
                                $this->config['db']['tv_value']
                            );
                            break;

                        case 'mini':
                            $db_params = array(
                                'tmplvarid' => $this->config['tmplvarid']['vk_mini_id'],
                                'contentid' => $id,
                                'value' => $market_item_id
                            );
                            $this->modx->db->insert(
                                $db_params,
                                $this->config['db']['tv_value']
                            );
                            break;
                    }


                    // добавляем товар в подборки
                    if ($params['albums']) {

                        $params_to = $this->config['api'];
                        $params_to['api_method'] = 'market.addToAlbum';
                        $params_to['item_id'] = $market_item_id;
                        $params_to['album_ids'] = $params['albums'];

                        $request = $this->modx->runSnippet('VKapi', $params_to);
                        if (!$request['success']) {
                            $this->alert('error', '[ add to album ] - ' . $params['name'], $request);
                        }
                    }
                    return true;
                }

                $this->alert('error', '[ add ' . $type . ' ] - ' . $params['name'], $request);
                return false;
                break;

            case $this->config['template_album']:

                // ПОДБОРКА =========================================

                $params['api_method'] = 'market.addAlbum';
                $request = $this->modx->runSnippet('VKapi', $params);

                if ($request['success']) {
                    $market_album_id = (int) $request['success']['response'];

                    // сохраняем id в TV (vk_album_id)
                    $db_params = array(
                        'tmplvarid' => $this->config['tmplvarid']['vk_album_id'],
                        'contentid' => $id,
                        'value' => $market_album_id
                    );
                    $this->modx->db->insert(
                        $db_params,
                        $this->config['db']['tv_value']
                    );
                    return true;
                }

                $this->alert('error', '[ add album ] - ' . $params['title'], $request);
                return false;

                break;
        }
    }

    public function edit($id, $template, $vk_id, $type)
    {
        $check = $this->check($id, $template, $vk_id, $type);

        if ($check) {
            $params = $this->getParams($id, $template, $type) + $this->config['api'];

            switch ($template) {
                case $this->config['template_item']:

                    // ТОВАР ============================================

                    $params['api_method'] = 'market.edit';
                    $params['item_id'] = $vk_id;
                    $request = $this->modx->runSnippet('VKapi', $params);

                    if ($request['success']) {
                        $this->alert('success', '[ edit ' . $type . ' ] - ' . $params['name'], $request);
                        return true;
                    } else {
                        $this->alert('error', '[ edit ' . $type . ' ] - ' . $params['name'], $request);
                        return false;
                    }
                    break;

                case $this->config['template_album']:

                    // ПОДБОРКА =========================================

                    $params['api_method'] = 'market.editAlbum';
                    $params['album_id'] = $vk_id;
                    $request = $this->modx->runSnippet('VKapi', $params);

                    if ($request['success']) {
                        $this->alert('success', '[ edit album ] - ' . $params['title'], $request);
                        return true;
                    } else {
                        $this->alert('error', '[ edit album ] - ' . $params['title'], $request);
                        return false;
                    }
                    break;
            }
        }

        return false;
    }

    public function delete($id, $template, $vk_id, $type)
    {
        $params = $this->config['api'];

        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $params['api_method'] = 'market.delete';
                $params['item_id'] = $vk_id;
                $request = $this->modx->runSnippet('VKapi', $params);

                if ($request['success']) {
                    $this->alert('success', '[ delete ' . $type . ' ] - ' . $params['name'], $request);

                    switch ($type) {
                        case 'original':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_original_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'license':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_license_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'spray':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_spray_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'probnik':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_probnik_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'phero10':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_phero10_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;

                        case 'mini':
                            $this->modx->db->delete(
                                $this->config['db']['tv_value'],
                                'tmplvarid="' . $this->config['tmplvarid']['vk_mini_id'] . '" AND contentid="' . $id . '"'
                            );
                            break;
                    }
                    return true;
                } else {
                    $this->alert('error', '[ delete ' . $type . ' ] - ' . $params['name'], $request);
                    return false;
                }
                break;

            case $this->config['template_album']:

                // ПОДБОРКА =========================================

                $params['api_method'] = 'market.deleteAlbum';
                $params['album_id'] = $vk_id;
                $request = $this->modx->runSnippet('VKapi', $params);

                if ($request['success']) {
                    $this->alert('success', '[ delete album ] - ' . $params['title'], $request);
                    $this->modx->db->delete(
                        $this->config['db']['tv_value'],
                        'tmplvarid="' . $this->config['tmplvarid']['vk_album_id'] . '" AND contentid="' . $id . '"'
                    );
                    return true;
                } else {
                    $this->alert('error', '[ delete album ] - ' . $params['title'], $request);
                    return false;
                }
                break;
        }

        return false;
    }
}
