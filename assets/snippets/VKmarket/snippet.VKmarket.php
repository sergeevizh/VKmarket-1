<?php

require_once MODX_BASE_PATH . "assets/snippets/VKmarket/class.VKmarket.php";

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
$vk = new VKmarket($access_token, $v);

switch ($api_method) {

    case 'market.add':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.add.php";
        break;

    case 'market.addAlbum':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.addAlbum.php";
        break;

    case 'market.addToAlbum':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.addToAlbum.php";
        break;

    case 'market.delete':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.delete.php";
        break;

    case 'market.deleteAlbum':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.deleteAlbum.php";
        break;

    case 'market.edit':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.edit.php";
        break;

    case 'market.editAlbum':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.editAlbum.php";
        break;

    case 'market.get':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.get.php";
        break;

    case 'market.getAlbums':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.getAlbums.php";
        break;

    case 'market.getCategories':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.getCategories.php";
        break;

    case 'market.removeFromAlbum':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.removeFromAlbum.php";
        break;

    case 'market.search':
        return require MODX_BASE_PATH . "assets/snippets/VKmarket/methods/market.search.php";
        break;
}
