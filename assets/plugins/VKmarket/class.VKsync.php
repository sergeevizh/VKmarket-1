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
        $this->site_tmplvars                = $site_tmplvars;
        $this->site_tmplvar_contentvalues   = $site_tmplvar_contentvalues;
        $this->access_token                 = $config['access_token'];
        $this->group_id                     = $config['group_id'];
        $this->v                            = $config['v'];
        $this->vk_item_id                   = $config['vk_item_id'];
        $this->vk_album_id                  = $config['vk_album_id'];
        $this->vk_category_id               = $config['vk_category_idy'];
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
        /* Генерирует массив параметров для ВК ======================
        -------------------------------------------------------------
        Параметры
        -------------------------------------------------------------
        & template          |  шаблон ресурса
        & id                |  id ресурса
        ============================================================= */

        switch ($template) {
            case $this->template_item:

                // ТОВАР ============================================

                $parent = $this->modx->runSnippet('DocInfo', array(
                    'docid' => $id,
                    'field' => 'parent'
                ));

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
                    'field' => 'vk_category_id'
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

                $result = array(
                    'name' => $name,
                    'description' => $description,
                    'category_id' => $category_id,
                    'price' => $price,
                    'image' => $image
                );

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

                $result = array(
                    'title' => $title,
                    'image' => $image
                );

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

                if ($vk_item_id == '') {
                    $result = 0;
                } else {
                    $request = $this->modx->runSnippet('VKapi', array(
                        'api_method' => 'market.getById',
                        'access_token' => $this->access_token,
                        'group_id' => $this->group_id,
                        'item_ids' => $vk_item_id
                    ));
                    if ($request['success']) {
                        $result = (int) $vk_item_id;
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

                if ($vk_album_id == '') {
                    $result = 0;
                } else {
                    $request = $this->modx->runSnippet('VKapi', array(
                        'api_method' => 'market.getAlbumById',
                        'access_token' => $this->access_token,
                        'group_id' => $this->group_id,
                        'album_ids' => $vk_album_id
                    ));

                    if ($request['success']) {
                        $result = (int) $vk_album_id;
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
        /* проверяет, есть ли отличия ДО и ПОСЛЕ ====================
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
}
