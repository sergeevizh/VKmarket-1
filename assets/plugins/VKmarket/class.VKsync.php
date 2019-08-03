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

    public function params($template, $id)
    {
        /* Генерирует массив параметров =============================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & template          |  шаблон ресурса
        & id                |  id ресурса
        ============================================================= */

        switch ($template) {
            case $this->template_item:

                // ТОВАР ============================================

                $parent = $this->modx->getDocument($id)['parent'];

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

                $result = array();

                $result['api']['access_token'] = $this->access_token;
                $result['api']['group_id'] = $this->group_id;
                $result['api']['v'] = $this->v;

                $result['params']['name'] = $name;
                $result['params']['description'] = $description;
                $result['params']['category_id'] = $category_id;
                $result['params']['price'] = $price;
                $result['params']['image'] = $image;
                $result['params']['url'] = $url;


                $parent_album_id = $this->modx->getTemplateVar($this->vk_album_id, '*', $parent);
                if ($parent_album_id) {
                    $album = $parent_album_id['value'];
                    $result['albums'] = $album;
                }

                return $result;
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

                $result = array();

                $result['api']['access_token'] = $this->access_token;
                $result['api']['group_id'] = $this->group_id;
                $result['api']['v'] = $this->v;

                $result['params']['title'] = $title;
                $result['params']['image'] = $image;

                return $result;
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

                if ($vk_item_id !== '') {
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

                if ($vk_album_id !== '') {
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

    public function add($template, $id, $params)
    {
        /* Добавляет элемент в ВК ===================================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & template          |  шаблон ресурса
        & id                |  id ресурса
        & params            |  параметры для API ВКонтакте
        ============================================================= */

        switch ($template) {
            case $this->template_item:

                // ТОВАР ============================================

                $add_params = $params['api'];
                $add_params['api_method'] = 'market.add';
                $add_params = $add_params + $params['params'];

                $add = $this->modx->runSnippet('VKapi', $add_params);

                if ($add['success']) {
                    $market_item_id = (int) $add['success']['response'];

                    // сохраняем id в TV (vk_item_id)
                    $db_params = array(
                        'tmplvarid' => $this->vk_item_tvid,
                        'contentid' => $id,
                        'value' => $market_item_id
                    );
                    $this->modx->db->insert(
                        $db_params,
                        $this->site_tmplvar_contentvalues
                    );

                    // добавляем товар в подборку
                    if ($params['albums']) {

                        $to_params = $params['api'];
                        $to_params['api_method'] = 'market.addToAlbum';
                        $to_params['item_id'] = $market_item_id;
                        $to_params['album_ids'] = $params['albums'];

                        $this->modx->runSnippet('VKapi', $to_params);
                    }
                }

                $result = $add;

                return $result;
                break;

            case $this->template_album:

                // ПОДБОРКА =========================================

                $add_params = $params['api'];
                $add_params['api_method'] = 'market.addAlbum';
                $add_params = $add_params + $params['params'];

                $add = $this->modx->runSnippet('VKapi', $add_params);

                if ($add['success']) {
                    $market_album_id = (int) $add['success']['response'];

                    // сохраняем id в TV (vk_item_id)
                    $db_params = array(
                        'tmplvarid' => $this->vk_album_tvid,
                        'contentid' => $id,
                        'value' => $market_album_id
                    );
                    $this->modx->db->insert(
                        $db_params,
                        $this->site_tmplvar_contentvalues
                    );
                }

                $result = $add;

                return $result;
                break;
        }
    }

    public function edit($template, $params, $differs)
    {
        /* Редактирует элемент в ВК =================================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & template          |  шаблон ресурса
        & id                |  id ресурса
        & params            |  параметры для API ВКонтакте
        ============================================================= */


        switch ($template) {
            case $this->template_item:

                // ТОВАР ============================================

                $edit_params = $params['api'];
                $edit_params['api_method'] = 'market.edit';
                $edit_params['item_id'] = $params['vk_id'];
                $edit_params = $edit_params + $differs;

                $edit = $this->modx->runSnippet('VKapi', $edit_params);

                $result = $edit;

                return $result;
                break;

            case $this->template_album:

                // ПОДБОРКА =========================================

                $edit_params = $params['api'];
                $edit_params['api_method'] = 'market.editAlbum';
                $edit_params['album_id'] = $params['vk_id'];
                $edit_params = $edit_params + $differs;

                $edit = $this->modx->runSnippet('VKapi', $edit_params);

                $result = $edit;

                return $result;
                break;
        }
    }
}
