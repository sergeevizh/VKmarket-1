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

    // создание ТВ-параметров
    $tv_item_params = array(
        'type' => 'text',
        'name' => 'vk_item_id',
        'caption' => 'ID товара ВКонтакте',
        'description' => 'В API ВКонтакте: <em>market_item_id</em>'
    );
    $insert_result = $modx->db->insert($tv_item_params, $TV);
    $modx->logEvent(1, 3, json_encode($insert_result, JSON_UNESCAPED_UNICODE), '[ VKmarket ] - tv_result');

    //удаляем плагин
    $pluginId  = $modx->db->getValue($modx->db->select('id', $P, 'name="VKinstall"'));
    if (!empty($pluginId)) {
        $modx->db->delete($P, "id = $pluginId");
        $modx->db->delete($modx->getFullTableName("site_plugin_events"), "pluginid=$pluginId");
    };
}
