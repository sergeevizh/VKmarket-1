<?php

require_once("assets/snippets/apiVK/class.Vk.php");

// Проверяем наличие обязательных параметров
if (!isset($api_method)) {
    return '{"error":{"error_type":"required","error_msg":"Not found: api_method."}}';
}
if (!isset($access_token)) {
    return '{"error":{"error_type":"required","error_msg":"Not found: access_token."}}';
}

$v = isset($v) ? $v : '5.101';
$vk = new Vk($access_token, $v);

switch ($api_method) {

    case 'market.add':

        /* Добавляет новый товар ============================
        
        Метод API           |  market.add
        -----------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  ID сообщества
        & name              |  название товара
        & description       |  описание товара
        & category_id       |  ID категории товара
        & price             |  цена товара
        & image             |  путь к изображению
        -----------------------------------------------------
        & v                 |  версия API
        & album_ids         |  ID подборок, к которым относится товар (через запятую)
        & deleted           |  статус товара (удалён или не удалён)
        & url               |  ссылка на сайт товара
        ----------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: group_id."}}';
        }
        if (!isset($name)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: name."}}';
        }
        if (!isset($description)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: description."}}';
        }
        if (!isset($category_id)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: category_id."}}';
        }
        if (!isset($price)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: price."}}';
        }
        if (!isset($image)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: image."}}';
        }

        $image_path = 'image.jpg';
        copy($image, 'image.jpg');

        // Получаем сервер VK для загрузки изображения товара
        $upload_server = $vk->photos__getMarketUploadServer($group_id, 1);

        // Если сервер VK не получен
        if (!isset($upload_server['upload_url'])) {
            return $upload_server; // выводим отчёт об ошибке
        }

        // Загружаем изображение на сервер VK
        $upload = $vk->uploadFile($upload_server['upload_url'], $image_path);

        // Если изображение не загружено на сервер VK
        if (!isset($upload['photo'])) {
            return json_encode($upload, true); // выводим отчёт об ошибке
        }

        // Сохраняем изображение на сервере VK
        $save = $vk->photos__saveMarketPhoto(
            [
                'group_id' => $group_id,
                'photo' => $upload['photo'],
                'server' => $upload['server'],
                'hash' => $upload['hash'],
                'crop_data' => $upload['crop_data'],
                'crop_hash' => $upload['crop_hash']
            ]
        );

        // Если изображение не сохранено на сервере VK
        if (!isset($save[0]['id'])) {
            return $save; // выводим отчёт об ошибке
        }

        // Получаем ID загруженного изображения
        $main_photo_id = $save[0]['id'];

        // Создаём товар в сообществе
        $add = $vk->market__add([
            'owner_id' => "-$group_id",
            'name' => $name,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $price,
            'main_photo_id' => $main_photo_id,
            'deleted' => $deleted,
            'url' => $url
        ]);

        // Если товар не создан
        if (!isset($add['market_item_id'])) {
            return $add; // выводим отчёт об ошибке
        }

        // Получаем ID созданного товара
        $item_id = $add['market_item_id'];
        $result = '{"success":{"report":"Item successfully created in VK.","name":"' . addslashes($name) . '","item_id":' . $item_id . '}}';
        $result_add = json_decode($result, true);

        // Если при вызове был указан список подборок
        if (isset($album_ids)) {

            // Добавляем созданный товар в указанные подборки
            $add_album = $vk->market__addToAlbum([
                'owner_id' => "-$group_id",
                'item_id' => $item_id,
                'album_ids' => $album_ids
            ]);

            // Если товар не добавлен в какую-либо подборку
            if ($add_album !== 1) {
                $error = json_decode($add_album, true); // копируем отчет о создании товара
                $error['success'] = $result_add['success']; // добавляем его к отчёту об ошибке
                return json_encode($error, JSON_UNESCAPED_UNICODE); // выводим отчёт об ошибке
            } else {

                $result = '{"success":[{"report":"Item successfully created in VK.","name":"' . addslashes($name) . '","item_id":' . $item_id . '},{"report":"Item successfully added to albums.","album_ids":"' . $album_ids . '"}]}';
                return $result; // Выводим отчёт об успешном создании товара и добавлении его в подборки

            }
        }

        return json_encode($result_add, JSON_UNESCAPED_UNICODE); // Выводим отчёт об успешном создании товара
        break;

    case 'market.addAlbum':

        /* Добавляет новую подборку =========================
        
        Метод API           |  market.addAlbum
        -----------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & group_id          |  ID сообщества
        & title             |  название подборки
        -----------------------------------------------------
        & v                 |  версия API
        & image             |  путь к изображению
        ----------------------------------------------------- */

        // Проверяем наличие обязательных параметров
        if (!isset($group_id)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: group_id."}}';
        }
        if (!isset($title)) {
            return '{"error":{"error_type":"required","error_msg":"Not found: title."}}';
        }

        // Если при вызове было указано изображение
        if (isset($image)) {

            $image_path = "image.png";
            copy($image, "image.png");

            // Получаем сервер VK для загрузки изображения подборки
            $upload_server = $vk->photos__getMarketAlbumUploadServer($group_id);

            // Если сервер VK не получен
            if (!isset($upload_server['upload_url'])) {
                return $upload_server; // выводим отчёт об ошибке
            }

            // Загружаем изображение на сервер VK
            $upload = $vk->uploadFile($upload_server['upload_url'], $image_path);

            // Если изображение не загружено на сервер VK
            if (!isset($upload['photo'])) {
                return json_encode($upload, true); // выводим отчёт об ошибке
            }

            // Сохраняем изображение на сервере VK
            $save = $vk->photos__saveMarketAlbumPhoto(
                [
                    'group_id' => $group_id,
                    'photo' => $upload['photo'],
                    'server' => $upload['server'],
                    'hash' => $upload['hash']
                ]
            );

            // Если изображение не сохранено на сервере VK
            if (!isset($save[0]['id'])) {
                return $save; // выводим отчёт об ошибке
            }

            // Получаем ID загруженного изображения
            $photo_id = $save[0]['id'];
        }

        // Создаём подборку в сообществе
        $add = $vk->market__addAlbum([
            'owner_id' => "-$group_id",
            'title' => $title,
            'photo_id' => $photo_id
        ]);

        // Если подборка не создана
        if (!isset($add['market_album_id'])) {
            return $add; // выводим отчёт об ошибке
        }

        // Получаем ID созданной подборки
        $market_album_id = $add['market_album_id'];
        $result = '{"success":{"report":"Album successfully created in VK.","title":"' . addslashes($title) . '","market_album_id":' . $market_album_id . '}}';

        return $result; // Выводим отчёт об успешном создании подборки
        break;

    case 'market.getAlbums':

        /* Возвращает список подборок =======================
        
        Метод API           |  market.getAlbums
        -----------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & v                 |  версия API [по-умолчанию: 5.101]
        & group_id          |  ID сообщества
        ----------------------------------------------------- */
        $result = $vk->market__getAlbums([
            'owner_id' => "-$group_id"
        ]);

        return json_encode($result, true);
        break;
}
