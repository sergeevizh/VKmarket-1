<?php

require_once("assets/snippets/apiVK/class.Vk.php");

$access_token = isset($access_token) ? $access_token : 0;
$v = isset($v) ? $v : '5.101';
$group_id = isset($group_id) ? $group_id : 0;
$vk = new Vk($access_token, $v);

switch ($api_method) {

    case 'market.add':

        /* Добавляет новый товар ============================
        
        Метод API           |  market.add
        -----------------------------------------------------
        & api_method        |  метод API
        & access_token      |  ключ доступа к API
        & v                 |  версия API [по-умолчанию: 5.101]
        & group_id          |  ID сообщества
        & image             |  путь к изображению
        & name              |  название товара
        & description       |  описание товара
        & category_id       |  ID категории товара
        & price             |  цена товара
        & url               |  ссылка на сайт товара
        & album_ids         |  ID подборок, к которым относится товар (через запятую)
        ----------------------------------------------------- */

        $image_path = 'image.jpg';
        copy($image, 'image.jpg');

        $upload_server = $vk->getMarketUploadServer($group_id, 1);

        $upload = $vk->uploadFile($upload_server['upload_url'], $image_path);

        $save = $vk->saveMarketPhoto(
            [
                'group_id' => $group_id,
                'photo' => $upload['photo'],
                'server' => $upload['server'],
                'hash' => $upload['hash'],
                'crop_data' => $upload['crop_data'],
                'crop_hash' => $upload['crop_hash']
            ]
        );

        $main_photo_id = $save[0]['id'];

        $add = $vk->add([
            'owner_id' => "-$group_id",
            'name' => $name,
            'description' => $description,
            'category_id' => $category_id,
            'price' => $price,
            'main_photo_id' => $main_photo_id,
            'url' => $url
        ]);


        if (isset($album_ids)) {

            $item_id = $add['market_item_id'];

            $result = $vk->addToAlbum([
                'owner_id' => "-$group_id",
                'item_id' => $item_id,
                'album_ids' => $album_ids
            ]);

            return json_encode($result);
        } else {
            return json_encode($add);
        }
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

        $upload_server = $vk->getMarketAlbumUploadServer($group_id);

        $upload = $vk->uploadFile($upload_server['upload_url'], $image_path);

        $save = $vk->saveMarketAlbumPhoto(
            [
                'group_id' => $group_id,
                'photo' => $upload['photo'],
                'server' => $upload['server'],
                'hash' => $upload['hash']
            ]
        );

        $photo_id = $save[0]['id'];

        $add = $vk->addAlbum([
            'owner_id' => "-$group_id",
            'title' => $title,
            'photo_id' => $photo_id
        ]);

        return json_encode($add);
        break;
}
