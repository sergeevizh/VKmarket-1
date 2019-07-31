<?php

/* Выводит сообщение в лог EVO ==============================
-------------------------------------------------------------
Параметры
-------------------------------------------------------------
& case              |  тип сообщения : error / success
& title             |  код сообщения (заголовок)
& params            |  json с подробностями
============================================================= */

function alert($case, $title, $params)
{
    global $modx;

    switch ($case) {
        case 'error':
        default:
            $modx->logEvent(1, 3, json_encode($params, JSON_UNESCAPED_UNICODE), '[ VKsync ] - ' . $title);
            break;

        case 'success':
            $modx->logEvent(1, 1, json_encode($params, JSON_UNESCAPED_UNICODE), '[ VKsync ] - ' . $title);
            break;
    }
}


/* Генерирует массив параметров для ВК ======================
-------------------------------------------------------------
Параметры
-------------------------------------------------------------
& case              |  тип элемента : item / album
& config            |  массив с конфигурацией плагина
============================================================= */

function params($case, $config, $id)
{
    global $modx;

    switch ($case) {
        case 'item':
            // ТОВАРЫ ========================================
            // -----------------------------------------------
            $parent = $modx->runSnippet('DocInfo', array(
                'docid' => $id,
                'field' => 'parent'
            ));

            $name = $modx->runSnippet('DocLister', array(
                'tvList' => $config['tvList'],
                'documents' => $id,
                'tpl' => $config['item']['name'],
                'ownerTPL' => '@CODE:[+dl.wrap+]'
            ));
            $description = $modx->runSnippet('DocLister', array(
                'tvList' => $config['tvList'],
                'documents' => $id,
                'tpl' => $config['item']['description'],
                'ownerTPL' => '@CODE:[+dl.wrap+]'
            ));
            $category_id = $modx->runSnippet('DocInfo', array(
                'docid' => $parent,
                'field' => $config['item']['category_id']
            ));
            $price = $modx->runSnippet('DocLister', array(
                'tvList' => $config['tvList'],
                'documents' => $id,
                'tpl' => $config['item']['price'],
                'ownerTPL' => '@CODE:[+dl.wrap+]'
            ));
            $image = $modx->runSnippet('DocLister', array(
                'tvList' => $config['tvList'],
                'documents' => $id,
                'tpl' => $config['item']['image'],
                'ownerTPL' => '@CODE:[+dl.wrap+]'
            ));

            $result = array(
                "name" => $name,
                "description" => $description,
                "category_id" => $category_id,
                "price" => $price,
                "image" => $image
            );

            return $result;
            break;

        case 'album':
            // ПОДБОРКИ ======================================
            // -----------------------------------------------
            $title = $modx->runSnippet('DocLister', array(
                'tvList' => $config['tvList'],
                'documents' => $id,
                'tpl' => $config['album']['title'],
                'ownerTPL' => '@CODE:[+dl.wrap+]'
            ));
            $image = $modx->runSnippet('DocLister', array(
                'tvList' => $config['tvList'],
                'documents' => $id,
                'tpl' => $config['album']['image'],
                'ownerTPL' => '@CODE:[+dl.wrap+]'
            ));

            $result = array(
                "title" => $title,
                "image" => $image
            );

            return $result;
            break;
    }
}
