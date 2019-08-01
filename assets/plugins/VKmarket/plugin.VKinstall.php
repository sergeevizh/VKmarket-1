<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

/*
@author of original dzhuryn https://github.com/dzhuryn
*/

if ($modx->event->name == 'OnManagerPageInit') {
    $M = $modx->getFullTableName('site_modules');
    $MD = $modx->getFullTableName('site_module_depobj');
    $S = $modx->getFullTableName('site_snippets');
    $P = $modx->getFullTableName('site_plugins');
    $TV = $modx->getFullTableName('site_tmplvars');
    $CATS = $modx->getFullTableName('categories');

    //поиск и обновление модуля
    $value  = $modx->db->getValue($modx->db->select('id', $M, 'name="VKmarket"'));
    $moduleGuid  = $modx->db->getValue($modx->db->select('guid', $M, 'name="VKmarket"'));
    $moduleId =  $value;
    $fields = array('enable_sharedparams' => 1);
    $modx->db->update($fields, $M, 'id = "' . $moduleId . '"');

    // добавление связей
    $snippets = array('VKapi');
    $plugins = array('VKsync');
    foreach ($snippets as $snippet) {
        $snippetId  = $modx->db->getValue($modx->db->select('id', $S, 'name="' . $snippet . '"'));
        if (empty($snippetId)) {
            continue;
        }
        $value = $modx->db->getValue($modx->db->select('id', $MD, 'resource="' . $snippetId . '" AND module="' . $moduleId . '" AND type=40'));
        if (!empty($value)) {
            continue;
        }

        //запись в site_module_depobj
        $fields = array(
            'module' => $moduleId,
            'resource' => $snippetId,
            'type' => 40
        );
        $modx->db->insert($fields, $MD);
        //добавляем модуль в сниппет
        $fields = array('moduleguid' => $moduleGuid);
        $modx->db->update($fields, $S, 'id = "' . $snippetId . '"');
    }
    foreach ($plugins as $plugin) {
        $pluginId  = $modx->db->getValue($modx->db->select('id', $P, 'name="' . $plugin . '"'));
        if (empty($pluginId)) {
            continue;
        }
        //запись в site_module_depobj
        $value = $modx->db->getValue($modx->db->select('id', $MD, 'resource="' . $pluginId . '" AND module="' . $moduleId . '"  AND type=30'));
        if (!empty($value)) {
            continue;
        }
        $fields = array(
            'module' => $moduleId,
            'resource' => $pluginId,
            'type' => 30
        );
        $modx->db->insert($fields, $MD);
        //добавляем модуль в плагин
        $fields = array('moduleguid' => $moduleGuid);
        $modx->db->update($fields, $P, 'id = "' . $pluginId . '"');
    }

    // ID категории VKmarket
    $module_category = $modx->db->getValue($modx->db->select('id', $CATS, 'category="VKmarket"'));

    // ТВ-параметр для ID категории товаров
    $vk_category_id = $modx->db->getValue($modx->db->select('id', $TV, 'name="vk_category_id"'));
    $vk_category_id_params = array(
        'type' => 'text',
        'name' => 'vk_category_id',
        'caption' => 'ID категории товаров ВКонтакте',
        'description' => 'В API ВКонтакте: <em>category_id</em>',
        'category' => $module_category
    );

    if ($vk_category_id) {
        // обновляем
        $modx->db->update($vk_category_id_params, $TV, 'id="' . $vk_category_id . '"');
    } else {
        // создаём
        $modx->db->insert($vk_category_id_params, $TV);
    }

    // ТВ-параметр для ID подборок
    $vk_album_id = $modx->db->getValue($modx->db->select('id', $TV, 'name="vk_album_id"'));
    $vk_album_id_params = array(
        'type' => 'text',
        'name' => 'vk_album_id',
        'caption' => 'ID подборки ВКонтакте',
        'description' => 'В API ВКонтакте: <em>market_album_id</em>',
        'category' => $module_category
    );

    if ($vk_album_id) {
        // обновляем
        $modx->db->update($vk_album_id_params, $TV, 'id="' . $vk_album_id . '"');
    } else {
        // создаём
        $modx->db->insert($vk_album_id_params, $TV);
    }

    // ТВ-параметр для ID товаров
    $vk_item_id = $modx->db->getValue($modx->db->select('id', $TV, 'name="vk_item_id"'));
    $vk_item_id_params = array(
        'type' => 'text',
        'name' => 'vk_item_id',
        'caption' => 'ID товара ВКонтакте',
        'description' => 'В API ВКонтакте: <em>market_item_id</em>',
        'category' => $module_category
    );

    if ($vk_item_id) {
        // обновляем
        $modx->db->update($vk_item_id_params, $TV, 'id="' . $vk_item_id . '"');
    } else {
        // создаём
        $modx->db->insert($vk_item_id_params, $TV);
    }

    //удаляем плагин
    $pluginId  = $modx->db->getValue($modx->db->select('id', $P, 'name="VKinstall"'));
    if (!empty($pluginId)) {
        $modx->db->delete($P, "id = $pluginId");
        $modx->db->delete($modx->getFullTableName("site_plugin_events"), "pluginid=$pluginId");
    };
}
