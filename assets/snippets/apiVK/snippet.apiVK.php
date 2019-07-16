<?php

require_once("assets/snippets/apiVK/class.Vk.php");

// Проверяем обязательные параметры
if (!isset($api_method)) {
    return '{"error":{"error_type":"required","error_msg":"Not found: api_method."}}';
}
if (!isset($access_token)) {
    return '{"error":{"error_type":"required","error_msg":"Not found: access_token."}}';
}
if (!isset($group_id)) {
    return '{"error":{"error_type":"required","error_msg":"Not found: group_id."}}';
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

        // Проверяем обязательные параметры
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

        // Загружаем товар в сообщество
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

        // Если товар не загружен в сообщество
        if (!isset($add['market_item_id'])) {
            return $add; // выводим отчёт об ошибке
        }

        // Получаем ID загруженного товара
        $item_id = $add['market_item_id'];
        $result = '{"success":{"report":"Item successfully added to public.","name":"' . addslashes($name) . '","item_id":' . $item_id . '}}';
        $result_add = json_decode($result, true);

        // Если при вызове метода был указан список подборок
        if (isset($album_ids)) {

            // Добавляем полученный товар в указанные подборки
            $add_album = $vk->market__addToAlbum([
                'owner_id' => "-$group_id",
                'item_id' => $item_id,
                'album_ids' => $album_ids
            ]);

            // Если товар не добавлен в подборку
            if ($add_album !== 1) {
                $error = json_decode($add_album, true); // копируем отчет о загрузке
                $error['success'] = $result_add['success']; // добавляем его к отчёту об ошибке
                return json_encode($error, JSON_UNESCAPED_UNICODE); // выводим отчёт об ошибке
            } else {
                $result = '{"success":[{"report":"Item successfully added to public.","name":"' . addslashes($name) . '","item_id":' . $item_id . '},{"report":"Item successfully added to albums.","album_ids":"' . $album_ids . '"}]}';
                return $result;
            }
        }

        return json_encode($result_add, JSON_UNESCAPED_UNICODE);
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

    case 'market.addAlbum':

        /* Добавляет новую подборку =========================
    
        Метод API           |  market.addAlbum
        -----------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & v                 |  версия API [по-умолчанию: 5.101]
        & group_id          |  ID сообщества
        & image             |  путь к изображению
        & title             |  название подборки
        ----------------------------------------------------- */
        $image_path = "image.png";
        copy($image, "image.png");

        $upload_server = $vk->photos__getMarketAlbumUploadServer($group_id);

        $upload = $vk->uploadFile($upload_server['upload_url'], $image_path);

        $save = $vk->photos__saveMarketAlbumPhoto(
            [
                'group_id' => $group_id,
                'photo' => $upload['photo'],
                'server' => $upload['server'],
                'hash' => $upload['hash']
            ]
        );

        $photo_id = $save[0]['id'];

        $add = $vk->market__addAlbum([
            'owner_id' => "-$group_id",
            'title' => $title,
            'photo_id' => $photo_id
        ]);

        return json_encode($add, true);
        break;
}
