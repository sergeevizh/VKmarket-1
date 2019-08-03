<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKsync
{
    public function __construct($modx, $config)
    {
        $this->modx                     = $modx;
        $this->group_id                 = $config['group_id'];
        $this->v                        = $config['v'];
        $this->tv_item_id               = $config['tv_item_id'];
        $this->tv_album_id              = $config['tv_album_id'];
        $this->tv_category_id           = $config['tv_category_id'];
        $this->template_item            = $config['template_item'];
        $this->template_album           = $config['template_album'];
        $this->tv_list                  = $config['tv_list'];
        $this->item_name_tpl            = $config['item_name_tpl'];
        $this->item_description_tpl     = $config['item_description_tpl'];
        $this->item_price_tpl           = $config['item_price_tpl'];
        $this->item_image_tpl           = $config['item_image_tpl'];
        $this->album_title_tpl          = $config['album_title_tpl'];
        $this->album_image_tpl          = $config['album_image_tpl'];
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
                // ТОВАРЫ ========================================
                // -----------------------------------------------
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
                // ПОДБОРКИ ======================================
                // -----------------------------------------------
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
}
