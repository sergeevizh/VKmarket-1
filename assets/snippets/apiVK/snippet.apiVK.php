<?php

require_once("assets/snippets/apiVK/class.Vk.php");

// Проверяем наличие обязательных параметров
if (!isset($api_method)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: api_method"}}';
}
if (!isset($access_token)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: access_token"}}';
}

$v = isset($v) ? $v : '5.101';
$vk = new Vk($access_token, $v);

switch ($api_method) {

    case 'market.add':

        /* Добавляет новый товар ====================================
    
        Метод API           |  market.add
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  идентификатор сообщества
        & name              |  название товара
        & description       |  описание товара
        & category_id       |  идентификатор категории товара
        & price             |  цена товара
        & image             |  путь к изображению
        -------------------------------------------------------------
        & v                 |  версия API
        & album_ids         |  подборки, куда добавить товар (через запятую)
        & deleted           |  статус товара (удалён или не удалён)
        & url               |  ссылка на сайт товара
        ------------------------------------------------------------- */
        require_once("assets/snippets/apiVK/methods/market.add.php");

        break;

    case 'market.addAlbum':

        /* Добавляет новую подборку =================================
        
        Метод API           |  market.addAlbum
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  идентификатор сообщества
        & title             |  название подборки
        -------------------------------------------------------------
        & v                 |  версия API
        & image             |  путь к изображению
        ------------------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
        }
        if (!isset($title)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: title"}}';
        }

        // Если при вызове было указано изображение
        if (isset($image)) {

            $image_path = "image.png";
            copy($image, "image.png");

            // Получаем сервер VK для загрузки изображения подборки
            $upload_server = $vk->photos__getMarketAlbumUploadServer($group_id);

            // Если сервер VK не получен
            if (!isset($upload_server['upload_url'])) {
                return json_encode($upload_server, true); // выводим отчёт об ошибке
            }

            // Загружаем изображение на сервер VK
            $file_uploaded = $vk->uploadFile($upload_server['upload_url'], $image_path);

            // Если изображение не загружено
            if (!isset($file_uploaded['photo'])) {
                return json_encode($file_uploaded, true); // выводим отчёт об ошибке
            }

            // Сохраняем изображение на сервере VK
            $file_saved = $vk->photos__saveMarketAlbumPhoto(
                [
                    'group_id' => $group_id,
                    'photo' => $file_uploaded['photo'],
                    'server' => $file_uploaded['server'],
                    'hash' => $file_uploaded['hash']
                ]
            );

            // Если изображение не сохранено на сервере VK
            if (!isset($file_saved[0]['id'])) {
                return json_encode($file_saved, true); // выводим отчёт об ошибке
            }

            // Получаем ID загруженного изображения
            $photo_id = $file_saved[0]['id'];
        }

        // Создаём подборку в сообществе
        $addAlbum = $vk->market__addAlbum([
            'owner_id' => "-$group_id",
            'title' => $title,
            'photo_id' => $photo_id
        ]);

        // Если подборка не создана
        if (!isset($addAlbum['market_album_id'])) {
            return $addAlbum; // выводим отчёт об ошибке
        }

        // Получаем ID созданной подборки
        $market_album_id = $addAlbum['market_album_id'];

        // Генерируем отчёт об успешном создании товара
        $json_addAlbum = array(
            'success' => array(
                'message' => 'Album created',
                'request_params' => array(
                    array(
                        'key' => 'title',
                        'value' => $title
                    ),
                    array(
                        'key' => 'photo_id',
                        'value' => $photo_id
                    )
                ),
                'response' => array(
                    array(
                        'key' => 'market_album_id',
                        'value' => $market_album_id
                    )
                )
            )
        );

        $success = json_encode($json_addAlbum, JSON_UNESCAPED_UNICODE);
        return $success; // Выводим отчёт об успешном создании подборки
        break;

    case 'market.addToAlbum':

        /* Добавляет товар в подборки ===============================
        
        Метод API           |  market.addToAlbum
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  идентификатор сообщества
        & item_id           |  идентификатор товара
        & album_ids         |  подборки, куда добавить товар (через запятую)
        -------------------------------------------------------------
        & v                 |  версия API
        ------------------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
        }
        if (!isset($item_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: item_id"}}';
        }
        if (!isset($album_ids)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: album_ids"}}';
        }

        // Добавляем товар в указанные подборки
        $addToAlbum = $vk->market__addToAlbum([
            'owner_id' => "-$group_id",
            'item_id' => $item_id,
            'album_ids' => $album_ids
        ]);

        // Если товар не добавлен в какую-либо подборку
        if ($addToAlbum !== 1) {
            return $addToAlbum; // выводим отчёт об ошибке
        }

        // Генерируем отчёт об успешном добавлении
        $json_addToAlbum = array(
            'success' => array(
                'message' => 'Item added to albums',
                'request_params' => array(
                    array(
                        'key' => 'item_id',
                        'value' => $item_id
                    ),
                    array(
                        'key' => 'album_ids',
                        'value' => $album_ids
                    )
                ),
                'response' => $addToAlbum
            )
        );

        $success = json_encode($json_addToAlbum, JSON_UNESCAPED_UNICODE);
        return $success; // Выводим отчёт об успешном добавлении товара в подборки
        break;

    case 'market.delete':

        /* Удаляет товар из сообщества ==============================
        
        Метод API           |  market.delete
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  идентификатор сообщества
        & item_id           |  идентификатор товара
        -------------------------------------------------------------
        & v                 |  версия API
        ------------------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
        }
        if (!isset($item_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: item_id"}}';
        }

        // Удаляем товар из сообщества
        $delete = $vk->market__delete([
            'owner_id' => "-$group_id",
            'item_id' => $item_id
        ]);

        // Если товар не удалён
        if ($delete !== 1) {
            return $delete; // выводим отчёт об ошибке
        }

        // Генерируем отчёт об успешном добавлении товара в подборки
        $json_delete = array(
            'success' => array(
                'message' => 'Item deleted',
                'request_params' => array(
                    array(
                        'key' => 'item_id',
                        'value' => $item_id
                    )
                ),
                'response' => $delete
            )
        );

        $success = json_encode($json_delete, JSON_UNESCAPED_UNICODE);
        return $success; // Выводим отчёт об успешном удалении товара
        break;

    case 'market.deleteAlbum':

        /* Удаляет подборку с товарами ==============================
        
        Метод API           |  market.deleteAlbum
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  идентификатор сообщества
        & album_id          |  идентификатор подборки
        -------------------------------------------------------------
        & v                 |  версия API
        ------------------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
        }
        if (!isset($album_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: album_id"}}';
        }

        // Удаляем подборку
        $deleteAlbum = $vk->market__deleteAlbum([
            'owner_id' => "-$group_id",
            'album_id' => $album_id
        ]);

        // Если подборка не удалёна
        if ($deleteAlbum !== 1) {
            return $deleteAlbum; // выводим отчёт об ошибке
        }

        // Генерируем отчёт об успешном добавлении товара в подборки
        $json_deleteAlbum = array(
            'success' => array(
                'message' => 'Album deleted',
                'request_params' => array(
                    array(
                        'key' => 'album_id',
                        'value' => $album_id
                    )
                ),
                'response' => $deleteAlbum
            )
        );

        $success = json_encode($json_deleteAlbum, JSON_UNESCAPED_UNICODE);
        return $success; // Выводим отчёт об успешном удалении товара
        break;

    case 'market.getAlbums':

        /* Возвращает список подборок ===============================
        
        Метод API           |  market.getAlbums
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  идентификатор сообщества
        -------------------------------------------------------------
        & v                 |  версия API
        & offset            |  смещение относительно первой найденной подборки
        & count             |  количество возвращаемых подборок
        ------------------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id"}}';
        }

        // Запрашиваем список подборок
        $getAlbums = $vk->market__getAlbums([
            'owner_id' => "-$group_id",
            'offset' => $offset ? $offset : 0,
            'count' => $count ? $count : 100
        ]);

        // Если список не получен
        if (!isset($getAlbums['count'])) {
            return $getAlbums; // выводим отчёт об ошибке
        }

        $success = json_encode($getAlbums, JSON_UNESCAPED_UNICODE);
        return $success; // Выводим отчёт об успешном удалении товара
        break;
}
