<?php

require_once MODX_BASE_PATH . "assets/snippets/apiVK/class.apiVK.php";

// Проверяем наличие обязательных параметров
if (!isset($api_method)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: api_method"}}';
}
if (!isset($access_token)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: access_token"}}';
}
if (!isset($group_id)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
}

$v = isset($v) ? $v : '5.101';
$vk = new apiVK($access_token, $v);

switch ($api_method) {

    case 'market.add':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.add.php";
        break;

    case 'market.addAlbum':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.addAlbum.php";
        break;

    case 'market.addToAlbum':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.addToAlbum.php";
        break;

    case 'market.delete':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.delete.php";
        break;

    case 'market.deleteAlbum':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.deleteAlbum.php";
        break;

    case 'market.edit':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.edit.php";
        break;

    case 'market.editAlbum':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.editAlbum.php";
        break;

    case 'market.get':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.get.php";
        break;

    case 'market.getAlbums':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.getAlbums.php";
        break;

    case 'market.getCategories':
        return require_once MODX_BASE_PATH . "assets/snippets/apiVK/methods/market.getCategories.php";
        break;
}
