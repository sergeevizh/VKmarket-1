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

    public function getParams($id, $template)
    {
        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $parents = $this->modx->getParentIds($id);
                $parent = reset($parents);

                $name = $this->modx->runSnippet('DocLister', array(
                    'showNoPublish' => 1,
                    'tvList' => $this->config['tv_list'],
                    'documents' => $id,
                    'tpl' => $this->config['item_name_tpl'],
                    'ownerTPL' => '@CODE:[+dl.wrap+]'
                ));

                $description = $this->modx->runSnippet('DocLister', array(
                    'showNoPublish' => 1,
                    'tvList' => $this->config['tv_list'],
                    'documents' => $id,
                    'tpl' => $this->config['item_description_tpl'],
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
                    'tpl' => $this->config['item_price_tpl'],
                    'ownerTPL' => '@CODE:[+dl.wrap+]'
                ));

                $image = $this->modx->runSnippet('DocLister', array(
                    'showNoPublish' => 1,
                    'tvList' => $this->config['tv_list'],
                    'documents' => $id,
                    'tpl' => $this->config['item_image_tpl'],
                    'ownerTPL' => '@CODE:[+dl.wrap+]'
                ));

                $url = $this->modx->makeUrl($id, '', '', 'full');

                $result['name'] = $name;
                $result['description'] = $description;
                $result['category_id'] = $category_id;
                $result['price'] = $price;
                $result['image'] = $image;
                $result['url'] = $url;

                $albums = array();

                $parent_album_id = $this->modx->runSnippet('DocInfo', array(
                    'docid' => $parent,
                    'field' => 'vk_album_id'
                ));
                if ($parent_album_id)  array_push($albums, $parent_album_id);

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
        $actions = $_POST['actions'];
        if ($actions) {

            foreach ($actions as $action) {

                $action = json_decode($action, true);

                switch ($action['function']) {
                    case 'add':
                        $this->add($action['id'], $action['template']);
                        break;

                    case 'edit':
                        $this->edit($action['id'], $action['template'], $action['vk_id']);
                        break;

                    case 'delete':
                        $this->delete($action['id'], $action['template'], $action['vk_id']);
                        break;
                }
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            return true;
        }
        return false;
    }

    public function check($id, $template, $vk_id)
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
                    $this->modx->db->delete(
                        $this->config['db']['tv_value'],
                        'tmplvarid="' . $this->config['tmplvarid']['vk_item_id'] . '" AND contentid="' . $id . '"'
                    );
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

    public function add($id, $template)
    {
        $params = $this->getParams($id, $template) + $this->config['api'];

        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $params['api_method'] = 'market.add';
                $request = $this->modx->runSnippet('VKapi', $params);

                if ($request['success']) {
                    $this->alert('success', '[ add item ] - ' . $params['name'], $request);
                    $market_item_id = (int) $request['success']['response'];

                    // сохраняем id в TV (vk_item_id)
                    $db_params = array(
                        'tmplvarid' => $this->config['tmplvarid']['vk_item_id'],
                        'contentid' => $id,
                        'value' => $market_item_id
                    );
                    $this->modx->db->insert(
                        $db_params,
                        $this->config['db']['tv_value']
                    );


                    // добавляем товар в подборки
                    if ($params['albums']) {

                        $params_to = $this->config['api'];
                        $params_to['api_method'] = 'market.addToAlbum';
                        $params_to['item_id'] = $market_item_id;
                        $params_to['album_ids'] = $params['albums'];

                        $request = $this->modx->runSnippet('VKapi', $params_to);
                        if ($request['success']) {
                            $this->alert('success', '[ add to album ] - ' . $params['name'], $request);
                        } else {
                            $this->alert('error', '[ add to album ] - ' . $params['name'], $request);
                        }
                    }
                    return true;
                }

                $this->alert('error', '[ add item ] - ' . $params['name'], $request);
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

                    $this->alert('success', '[ add album ] - ' . $params['title'], $request);
                    return true;
                }

                $this->alert('error', '[ add album ] - ' . $params['title'], $request);
                return false;

                break;
        }
    }

    public function edit($id, $template, $vk_id)
    {
        $check = $this->check($id, $template, $vk_id);

        if ($check) {
            $params = $this->getParams($id, $template) + $this->config['api'];

            switch ($template) {
                case $this->config['template_item']:

                    // ТОВАР ============================================

                    $params['api_method'] = 'market.edit';
                    $params['item_id'] = $vk_id;
                    $request = $this->modx->runSnippet('VKapi', $params);

                    if ($request['success']) {
                        $this->alert('success', '[ edit item ] - ' . $params['name'], $request);
                        return true;
                    } else {
                        $this->alert('error', '[ edit item ] - ' . $params['name'], $request);
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

    public function delete($id, $template, $vk_id)
    {
        $params = $this->config['api'];

        switch ($template) {
            case $this->config['template_item']:

                // ТОВАР ============================================

                $params['api_method'] = 'market.delete';
                $params['item_id'] = $vk_id;
                $request = $this->modx->runSnippet('VKapi', $params);

                if ($request['success']) {
                    $this->alert('success', '[ delete item ] - ' . $params['name'], $request);
                    $this->modx->db->delete(
                        $this->config['db']['tv_value'],
                        'tmplvarid="' . $this->config['tmplvarid']['vk_item_id'] . '" AND contentid="' . $id . '"'
                    );
                    return true;
                } else {
                    $this->alert('error', '[ delete item ] - ' . $params['name'], $request);
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
