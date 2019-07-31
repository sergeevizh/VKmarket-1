<?php

require_once MODX_BASE_PATH . 'assets/snippets/VKmarket/class.VKmarket.php';

$v = isset($v) ? $v : '5.101';
$response = isset($response) ? $response : 'decode';

// Проверяем наличие обязательных параметров
$error = array(
    'error' => array(
        'error_code' => 'required'
    )
);

if (!isset($api_method)) {
    $error['error']['error_msg'] = 'Not found required param: api_method';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($access_token)) {
    $error['error']['error_msg'] = 'Not found required param: access_token';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

if (!isset($group_id)) {
    $error['error']['error_msg'] = 'Not found required param: group_id';
    // выводим отчёт об ошибке
    return $vk->report($response, $error);
}

$vk = new VKmarket($access_token, $v);

switch ($api_method) {

    case 'market.add':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.add.php';
        break;

    case 'market.addAlbum':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.addAlbum.php';
        break;

    case 'market.addToAlbum':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.addToAlbum.php';
        break;

    case 'market.delete':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.delete.php';
        break;

    case 'market.deleteAlbum':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.deleteAlbum.php';
        break;

    case 'market.edit':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.edit.php';
        break;

    case 'market.editAlbum':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.editAlbum.php';
        break;

    case 'market.get':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.get.php';
        break;

    case 'market.getAlbums':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.getAlbums.php';
        break;

    case 'market.getCategories':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.getCategories.php';
        break;

    case 'market.removeFromAlbum':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.removeFromAlbum.php';
        break;

    case 'market.reorderAlbums':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.reorderAlbums.php';
        break;

    case 'market.reorderItems':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.reorderItems.php';
        break;

    case 'market.search':
        return require MODX_BASE_PATH . 'assets/snippets/VKmarket/methods/market.search.php';
        break;
}
