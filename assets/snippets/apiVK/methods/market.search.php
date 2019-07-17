<?php

/* Возвращает список товаров ================================
-------------------------------------------------------------
Обязательные параметры
-------------------------------------------------------------
& api_method        |  метод API
& access_token      |  ключ доступа к API
& group_id          |  идентификатор сообщества
-------------------------------------------------------------
Дополнительные параметры
-------------------------------------------------------------
& v                 |  версия API
& album_id          |  идентификатор подборки, в которой искать
& q                 |  строка поискового запроса
& price_from        |  минимальное значение цены
& price_to          |  максимальное значение цены
& sort              |  вид сортировки
& rev               |  направление сортировки
& offset            |  смещение относительно первого найденной подборки
& count             |  количество возвращаемых подборок
& extended          |  возвращать ли дополнительные поля
============================================================= */

// Генерируем запрос обязательных параметров
$request_params = array(
    'owner_id' => "-$group_id",
    'album_id' => isset($album_id) ? $album_id : 0,
    'sort' => isset($sort) ? $sort : 0,
    'rev' => isset($rev) ? $rev : 1,
    'offset' => isset($offset) ? $offset : 0,
    'count' => isset($count) ? $count : 20,
    'extended' => isset($extended) ? $extended : 0,
);

// Добавляем к запросу доп. параметры
if (isset($q)) {
    $request_params['q'] = $q;
}
if (isset($price_from)) {
    $request_params['price_from'] = $price_from;
}
if (isset($price_to)) {
    $request_params['price_to'] = $price_to;
}

// Осуществляем поиск товаров
$search = $vk->market__search($request_params);

// Если поиск не осуществлён
if (!isset($search['count'])) {
    return $search; // выводим отчёт об ошибке
}

$success = json_encode($search, JSON_UNESCAPED_UNICODE);
return $success; // Выводим результат поиска
