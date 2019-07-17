<?php

require_once("assets/snippets/apiVK/class.Vk.php");

// Проверяем наличие обязательных параметров
if (!isset($api_method)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: api_method."}}';
}
if (!isset($access_token)) {
    return '{"error":{"error_code":"required","error_msg":"Not found: access_token."}}';
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

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id."}}';
        }
        if (!isset($name)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: name."}}';
        }
        if (!isset($description)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: description."}}';
        }
        if (!isset($category_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: category_id."}}';
        }
        if (!isset($price)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: price."}}';
        }
        if (!isset($image)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: image."}}';
        }

        $image_path = 'image.jpg';
        copy($image, 'image.jpg');

        // Получаем сервер VK для загрузки изображения товара
        $upload_server = $vk->photos__getMarketUploadServer($group_id, 1);

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
        $file_saved = $vk->photos__saveMarketPhoto(
            [
                'group_id' => $group_id,
                'photo' => $file_uploaded['photo'],
                'server' => $file_uploaded['server'],
                'hash' => $file_uploaded['hash'],
                'crop_data' => $file_uploaded['crop_data'],
                'crop_hash' => $file_uploaded['crop_hash']
            ]
        );

        // Если изображение не сохранено на сервере VK
        if (!isset($file_saved[0]['id'])) {
            return json_encode($file_saved, true); // выводим отчёт об ошибке
        }

        // Получаем ID загруженного изображения
        $main_photo_id = $file_saved[0]['id'];

        // Создаём товар в сообществе
        $add = $vk->market__add([
            'owner_id' => "-$group_id",
            'name' => $name,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $price,
            'main_photo_id' => $main_photo_id,
            'deleted' => $deleted ? $deleted : 0,
            'url' => $url
        ]);

        // Если товар не создан
        if (!isset($add['market_item_id'])) {
            return $add; // выводим отчёт об ошибке
        }

        // Получаем ID созданного товара
        $market_item_id = $add['market_item_id'];

        // Генерируем отчёт об успешном создании товара
        $json_add = array(
            'success' => array(
                'message' => 'Item successfully created',
                'request_params' => array(
                    array(
                        'key' => 'name',
                        'value' => $name
                    ),
                    array(
                        'key' => 'description',
                        'value' => $description
                    ),
                    array(
                        'key' => 'category_id',
                        'value' => $category_id
                    ),
                    array(
                        'key' => 'price',
                        'value' => $price
                    ),
                    array(
                        'key' => 'main_photo_id',
                        'value' => $main_photo_id
                    ),
                    array(
                        'key' => 'deleted',
                        'value' => $deleted ? $deleted : 0
                    ),
                    array(
                        'key' => 'url',
                        'value' => $url
                    )
                ),
                'response' => array(
                    array(
                        'key' => 'market_item_id',
                        'value' => $market_item_id
                    )
                )
            )
        );

        // Если при вызове был указан список подборок
        if (isset($album_ids)) {

            // Добавляем созданный товар в указанные подборки
            $addToAlbum = $vk->market__addToAlbum([
                'owner_id' => "-$group_id",
                'item_id' => $market_item_id,
                'album_ids' => $album_ids
            ]);

            // Если товар не добавлен в какую-либо подборку
            if ($addToAlbum !== 1) {

                $error = json_decode($addToAlbum, true); // копируем отчет об ошибке добавления в подборки
                $error['success'] = $json_add['success']; // добавляем в него отчёт об успешном создании товара
                return json_encode($error, JSON_UNESCAPED_UNICODE); // выводим отчёт об ошибке

            } else {

                // Генерируем отчёт об успешном добавлении товара в подборки
                $json_addToAlbum = array(
                    'success' => array(
                        'message' => 'Item successfully added to albums',
                        'request_params' => array(
                            array(
                                'key' => 'item_id',
                                'value' => $market_item_id
                            ),
                            array(
                                'key' => 'album_ids',
                                'value' => $album_ids
                            )
                        ),
                        'response' => $addToAlbum
                    )
                );

                $success = array();
                $success[0] = $json_add;
                $success[1] = $json_addToAlbum;
                $success = json_encode($success, JSON_UNESCAPED_UNICODE);

                return $success; // Выводим отчёт об успешном создании товара и добавлении его в подборки
            }
        }

        $success = json_encode($json_add, JSON_UNESCAPED_UNICODE);
        return $success; // Выводим отчёт об успешном создании товара

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
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id."}}';
        }
        if (!isset($title)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: title."}}';
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
                'message' => 'Album successfully created',
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
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id."}}';
        }
        if (!isset($item_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: item_id."}}';
        }
        if (!isset($album_ids)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: album_ids."}}';
        }

        // Добавляем товар в указанные подборки
        $res = $vk->market__addToAlbum([
            'owner_id' => "-$group_id",
            'item_id' => $item_id,
            'album_ids' => $album_ids
        ]);

        // Если товар не добавлен в какую-либо подборку
        if ($res !== 1) {
            return $res; // выводим отчёт об ошибке
        }

        $res = '{"success":{"report":"Item successfully added to albums.","item_id":' . $item_id . ',"album_ids":"' . $album_ids . '"}}';
        return $res; // Выводим отчёт об успешном добавлении товара в подборки
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
            return '{"error":{"error_code":"required","error_msg":"Not found: group_id."}}';
        }
        if (!isset($item_id)) {
            return '{"error":{"error_code":"required","error_msg":"Not found: item_id."}}';
        }

        // Удаляем товар из сообщества
        $res = $vk->market__delete([
            'owner_id' => "-$group_id",
            'item_id' => $item_id
        ]);

        // Если товар не удалён
        if ($res !== 1) {
            return $res; // выводим отчёт об ошибке
        }

        $res = '{"success":{"report":"Item successfully added to albums.","item_id":' . $item_id . ',"album_ids":"' . $album_ids . '"}}';
        return $res; // Выводим отчёт об успешном добавлении товара в подборки
        break;

    case 'market.getAlbums':

        /* Возвращает список подборок ===============================
        
        Метод API           |  market.getAlbums
        -------------------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & v                 |  версия API [по-умолчанию: 5.101]
        & group_id          |  ID сообщества
        ------------------------------------------------------------- */
        $res = $vk->market__getAlbums([
            'owner_id' => "-$group_id"
        ]);

        return json_encode($res, true);
        break;
}
