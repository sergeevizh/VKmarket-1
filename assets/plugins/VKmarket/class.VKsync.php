<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKsync
{
    public function __construct($modx, $config)
    {
        $site_tmplvars = $modx->getFullTableName('site_tmplvars');
        $site_tmplvar_contentvalues = $modx->getFullTableName('site_tmplvar_contentvalues');
        $vk_item_tvid = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_item_id"'));
        $vk_album_tvid = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_album_id"'));
        $vk_category_tvid = $modx->db->getValue($modx->db->select('id', $site_tmplvars, 'name="vk_category_id"'));

        $this->modx                         = $modx;
        $this->access_token                 = $config['access_token'];
        $this->group_id                     = $config['group_id'];
        $this->v                            = $config['v'];

        $this->site_tmplvars                = $site_tmplvars;
        $this->site_tmplvar_contentvalues   = $site_tmplvar_contentvalues;

        $this->vk_item_id                   = $config['vk_item_id'];
        $this->vk_album_id                  = $config['vk_album_id'];
        $this->vk_category_id               = $config['vk_category_id'];
        $this->vk_item_tvid                 = $vk_item_tvid;
        $this->vk_album_tvid                = $vk_album_tvid;
        $this->vk_category_tvid             = $vk_category_tvid;

        $this->template_item                = $config['template_item'];
        $this->template_album               = $config['template_album'];

        $this->tv_list                      = $config['tv_list'];
        $this->item_name_tpl                = $config['item_name_tpl'];
        $this->item_description_tpl         = $config['item_description_tpl'];
        $this->item_price_tpl               = $config['item_price_tpl'];
        $this->item_image_tpl               = $config['item_image_tpl'];
        $this->album_title_tpl              = $config['album_title_tpl'];
        $this->album_image_tpl              = $config['album_image_tpl'];
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
                $this->modx->logEvent(1, 3, json_encode($params, JSON_UNESCAPED_UNICODE), '[ VKsync ] - ' . $title);
                break;

            case 'success':
                $this->modx->logEvent(1, 1, json_encode($params, JSON_UNESCAPED_UNICODE), '[ VKsync ] - ' . $title);
                break;
        }
    }

    public function check($template, $id)
    {
        /* Проверяет, есть ли связь элемента с ВК ===================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & template          |  шаблон ресурса
        & id                |  id ресурса
        ============================================================= */

        switch ($template) {
            case $this->template_item:

                // ТОВАР ============================================

                $sync_item_id = $this->modx->getTemplateVar($this->vk_item_id, '*', $id);
                $vk_item_id = $sync_item_id['value'];
                $result = 0;

                if ($vk_item_id) {
                    $request = $this->modx->runSnippet('VKapi', array(
                        'api_method' => 'market.getById',
                        'access_token' => $this->access_token,
                        'group_id' => $this->group_id,
                        'v' => $this->v,
                        'item_ids' => $vk_item_id,
                        'extended' => 1
                    ));
                    if ($request['success']) {
                        $result = $request['success']['response']['items'][0];
                    } else {
                        $result = 0;
                        $this->modx->db->delete(
                            $this->site_tmplvar_contentvalues,
                            'tmplvarid="' . $this->vk_item_tvid . '" AND contentid="' . $id . '"'
                        );
                    }
                }

                return $result;
                break;

            case $this->template_album:

                // ПОДБОРКА =========================================

                $sync_album_id = $this->modx->getTemplateVar($this->vk_album_id, '*', $id);
                $vk_album_id = $sync_album_id['value'];
                $result = 0;

                if ($vk_album_id) {
                    $request = $this->modx->runSnippet('VKapi', array(
                        'api_method' => 'market.getAlbumById',
                        'access_token' => $this->access_token,
                        'group_id' => $this->group_id,
                        'v' => $this->v,
                        'album_ids' => $vk_album_id
                    ));

                    if ($request['success']) {
                        $result = $request['success']['response']['items'][0];
                    } else {
                        $result = 0;
                        $this->modx->db->delete(
                            $this->site_tmplvar_contentvalues,
                            'tmplvarid="' . $this->vk_album_tvid . '" AND contentid="' . $id . '"'
                        );
                    }
                }

                return $result;
                break;
        }
    }

    public function params($template, $id, $groups)
    {
        /* Генерирует массив параметров =============================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & template          |  шаблон ресурса
        & id                |  id ресурса
        & groups            |  группы через запятую [api,evo,params,vk]
        ============================================================= */

        $result = array();
        $groups = explode(',', $groups);

        if (in_array('params', $groups)) {

            switch ($template) {
                case $this->template_item:

                    // ТОВАР ============================================

                    $parent = $this->modx->getDocument($id)['parent'];
                    $gparent = $this->modx->getDocument($parent)['parent'];
                    $ggparent = $this->modx->getDocument($gparent)['parent'];

                    $name = $this->modx->runSnippet('DocLister', array(
                        'tvList' => $this->tv_list,
                        'documents' => $id,
                        'tpl' => $this->item_name_tpl,
                        'ownerTPL' => '@CODE:[+dl.wrap+]'
                    ));

                    $description = $this->modx->runSnippet('DocLister', array(
                        'tvList' => $this->tv_list,
                        'documents' => $id,
                        'tpl' => $this->item_description_tpl,
                        'ownerTPL' => '@CODE:[+dl.wrap+]'
                    ));

                    $category_id = $this->modx->runSnippet('DocInfo', array(
                        'docid' => $parent,
                        'field' => $this->vk_category_id
                    ));

                    $price = $this->modx->runSnippet('DocLister', array(
                        'tvList' => $this->tv_list,
                        'documents' => $id,
                        'tpl' => $this->item_price_tpl,
                        'ownerTPL' => '@CODE:[+dl.wrap+]'
                    ));

                    $image = $this->modx->runSnippet('DocLister', array(
                        'tvList' => $this->tv_list,
                        'documents' => $id,
                        'tpl' => $this->item_image_tpl,
                        'ownerTPL' => '@CODE:[+dl.wrap+]'
                    ));

                    $url = $this->modx->makeUrl($id, '', '', 'full');

                    $result['params']['name'] = $name;
                    $result['params']['description'] = $description;
                    $result['params']['category_id'] = $category_id;
                    $result['params']['price'] = $price;
                    $result['params']['image'] = $image;
                    $result['params']['url'] = $url;

                    $albums = array();

                    $parent_album_id = $this->modx->getTemplateVar($this->vk_album_id, '*', $parent);

                    if ($parent_album_id) {
                        array_push($albums, $parent_album_id['value']);
                        $gparent_album_id = $this->modx->getTemplateVar($this->vk_album_id, '*', $gparent);

                        if ($gparent_album_id) {
                            array_push($albums, $gparent_album_id['value']);
                            $ggparent_album_id = $this->modx->getTemplateVar($this->vk_album_id, '*', $ggparent);

                            if ($ggparent_album_id) {
                                array_push($albums, $ggparent_album_id['value']);
                            }
                        }
                    }

                    $albums = implode(",", $albums);
                    $result['params']['albums'] = $albums;

                    break;

                case $this->template_album:

                    // ПОДБОРКА =========================================

                    $title = $this->modx->runSnippet('DocLister', array(
                        'tvList' => $this->tv_list,
                        'documents' => $id,
                        'tpl' => $this->album_title_tpl,
                        'ownerTPL' => '@CODE:[+dl.wrap+]'
                    ));

                    $image = $this->modx->runSnippet('DocLister', array(
                        'tvList' => $this->tv_list,
                        'documents' => $id,
                        'tpl' => $this->album_image_tpl,
                        'ownerTPL' => '@CODE:[+dl.wrap+]'
                    ));

                    $result['params']['title'] = $title;
                    $result['params']['image'] = $image;

                    break;
            }
        }

        if (in_array('api', $groups)) {

            $result['api']['access_token'] = $this->access_token;
            $result['api']['group_id'] = $this->group_id;
            $result['api']['v'] = $this->v;
        }

        if (in_array('evo', $groups)) {

            $result['evo']['id'] = $id;
            $result['evo']['template'] = $template;
        }

        if (in_array('vk', $groups)) {

            $check = $this->check($template, $id);
            if ($check)  $result['vk'] = $check;
        }

        return $result;
    }

    public function differ($before, $after)
    {
        /* Проверяет, есть ли отличия ДО и ПОСЛЕ ====================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & before            |  параметры ДО
        & after             |  параметры ПОСЛЕ
        ============================================================= */

        $differs = array_diff_assoc($after, $before);
        $result = count($differs) == 0 ? 0 : $differs;

        return $result;
    }

    public function add($params)
    {
        /* Добавляет элемент в ВК ===================================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & params            |  параметры из функции params()
        ============================================================= */

        if ($params['vk']['id']) {
            return true;
        }

        switch ($params['evo']['template']) {
            case $this->template_item:

                // ТОВАР ============================================

                $request_params = $params['api'] + $params['params'];
                $request_params['api_method'] = 'market.add';

                $add = $this->modx->runSnippet('VKapi', $request_params);

                if ($add['success']) {
                    $market_item_id = (int) $add['success']['response'];

                    // сохраняем id в TV (vk_item_id)
                    $db_params = array(
                        'tmplvarid' => $this->vk_item_tvid,
                        'contentid' => $params['evo']['id'],
                        'value' => $market_item_id
                    );
                    $this->modx->db->insert(
                        $db_params,
                        $this->site_tmplvar_contentvalues
                    );

                    // добавляем товар в подборки
                    if ($params['params']['albums']) {

                        $request_params = $params['api'];
                        $request_params['api_method'] = 'market.addToAlbum';
                        $request_params['item_id'] = $market_item_id;
                        $request_params['album_ids'] = $params['params']['albums'];

                        $this->modx->runSnippet('VKapi', $request_params);
                    }
                    $this->alert('success', $params['params']['name'] . " (add)", $add);
                    return true;
                } else {
                    $this->alert('error', $params['params']['name'] . " (add)", $add);
                    return false;
                }

                break;

            case $this->template_album:

                // ПОДБОРКА =========================================

                $request_params = $params['api'] + $params['params'];
                $request_params['api_method'] = 'market.addAlbum';

                $add = $this->modx->runSnippet('VKapi', $request_params);

                if ($add['success']) {
                    $market_album_id = (int) $add['success']['response'];

                    // сохраняем id в TV (vk_item_id)
                    $db_params = array(
                        'tmplvarid' => $this->vk_album_tvid,
                        'contentid' => $params['evo']['id'],
                        'value' => $market_album_id
                    );
                    $this->modx->db->insert(
                        $db_params,
                        $this->site_tmplvar_contentvalues
                    );

                    $this->alert('success', $params['params']['title'] . " (add)", $add);
                    return true;
                } else {
                    $this->alert('error', $params['params']['title'] . " (add)", $add);
                    return false;
                }
                break;
        }
    }

    public function edit($params)
    {
        /* Редактирует элемент в ВК =================================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & params            |  параметры из функции params()
        ============================================================= */


        switch ($params['evo']['template']) {
            case $this->template_item:

                // ТОВАР ============================================

                $request_params = $params['api'];
                $request_params['item_id'] = $params['vk']['id'];

                if ($params['params']) {

                    $request_params['api_method'] = 'market.edit';
                    $request_params = $request_params + $params['params'];
                    $edit = $this->modx->runSnippet('VKapi', $request_params);
                }

                if ($params['params']['albums']) {
                    $albums_now = $params['vk']['albums_ids'];
                    $albums_now = implode($albums_now);

                    $remove_params = $params['api'];
                    $remove_params['item_id'] = $params['vk']['id'];
                    $remove_params['album_ids'] = $albums_now;
                    $remove_params['api_method'] = 'market.removeFromAlbum';
                    $this->modx->runSnippet('VKapi', $remove_params);

                    $add_params = $params['api'];
                    $add_params['item_id'] = $params['vk']['id'];
                    $add_params['album_ids'] = $params['params']['albums'];
                    $add_params['api_method'] = 'market.addToAlbum';
                    $this->modx->runSnippet('VKapi', $add_params);
                }

                $result = $edit;

                return $result;
                break;

            case $this->template_album:

                // ПОДБОРКА =========================================

                $request_params = $params['api'];
                $request_params['api_method'] = 'market.editAlbum';
                $request_params['album_id'] = $params['vk']['id'];
                $request_params = $request_params + $params['params'];
                $edit = $this->modx->runSnippet('VKapi', $request_params);

                $result = $edit;

                return $result;
                break;
        }
    }

    public function delete($params)
    {
        /* Редактирует элемент в ВК =================================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & params            |  параметры из функции params()
        ============================================================= */


        switch ($params['evo']['template']) {
            case $this->template_item:

                // ТОВАР ============================================

                $request_params = $params['api'];
                $request_params['api_method'] = 'market.delete';
                $request_params['item_id'] = $params['vk']['id'];

                $result = $this->modx->runSnippet('VKapi', $request_params);
                $this->modx->db->delete(
                    $this->site_tmplvar_contentvalues,
                    'tmplvarid="' . $this->vk_item_tvid . '" AND contentid="' . $params['evo']['id'] . '"'
                );

                return $result;
                break;

            case $this->template_album:

                // ПОДБОРКА =========================================

                $request_params = $params['api'];
                $request_params['api_method'] = 'market.deleteAlbum';
                $request_params['album_id'] = $params['vk']['id'];

                $result = $this->modx->runSnippet('VKapi', $request_params);
                $this->modx->db->delete(
                    $this->site_tmplvar_contentvalues,
                    'tmplvarid="' . $this->vk_album_tvid . '" AND contentid="' . $params['evo']['id'] . '"'
                );

                return $result;
                break;
        }
    }
}
