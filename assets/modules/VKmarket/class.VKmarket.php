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
        switch ($_POST['function']) {
            case 'add':
                $this->add($_POST['id'], $_POST['template']);
                header("Location: " . $_SERVER['REQUEST_URI']);
                break;

            case 'edit':
                $this->edit($_POST['id']);
                header("Location: " . $_SERVER['REQUEST_URI']);
                break;

            case 'delete':
                $this->delete($_POST['id']);
                header("Location: " . $_SERVER['REQUEST_URI']);
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
                } else {
                    $this->alert('error', '[ add item ] - ' . $params['name'], $request);
                    return false;
                }
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
                } else {
                    $this->alert('error', '[ add album ] - ' . $params['title'], $request);
                    return false;
                }

                break;
        }
    }
}
