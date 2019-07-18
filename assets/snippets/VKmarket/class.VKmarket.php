<?php

class VKmarket
{
    private $access_token;
    private $v;

    public function __construct($access_token, $v)
    {
        $this->token = $access_token;
        $this->v = $v;
    }

    // Добавляет новый товар ===============================================
    public function add($data)
    {
        return $this->request('market.add', $data);
    }

    // Добавляет новую подборку ============================================
    public function addAlbum($data)
    {
        return $this->request('market.addAlbum', $data);
    }

    // Добавляет товар в одну или несколько подборок =======================
    public function addToAlbum($data)
    {
        return $this->request('market.addToAlbum', $data);
    }

    // Удаляет товар =======================================================
    public function delete($data)
    {
        return $this->request('market.delete', $data);
    }

    // Удаляет подборку ====================================================
    public function deleteAlbum($data)
    {
        return $this->request('market.deleteAlbum', $data);
    }

    // Редактирует товар ===================================================
    public function edit($data)
    {
        return $this->request('market.edit', $data);
    }

    // Редактирует подборку с товарами =====================================
    public function editAlbum($data)
    {
        return $this->request('market.editAlbum', $data);
    }

    // Возвращает список товаров ===========================================
    public function get($data)
    {
        return $this->request('market.get', $data);
    }

    // Возвращает список подборок ==========================================
    public function getAlbums($data)
    {
        return $this->request('market.getAlbums', $data);
    }

    // Возвращает список категорий для товаров =============================
    public function getCategories($data)
    {
        return $this->request('market.getCategories', $data);
    }

    // Удаляет товар из подборок ===========================================
    public function removeFromAlbum($data)
    {
        return $this->request('market.removeFromAlbum', $data);
    }

    // Ищет товары в сообществе ============================================
    public function search($data)
    {
        return $this->request('market.search', $data);
    }

    // Осуществляет загрузку фотографии на адрес сервера ===================
    public function uploadFile($link, $path)
    {
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        if (class_exists('\CURLFile')) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new \CURLFile($path, 'image/jpg', 'image.jpg')]);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => "@$path"]);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data, true);
    }

    // Возвращает сервер для загрузки фотографии товара ====================
    public function getMarketUploadServer($group_id, $main)
    {
        $params = [
            'group_id' => $group_id,
            'main_photo' => $main
        ];
        return $this->request('photos.getMarketUploadServer', $params);
    }

    // Сохраняет фотографию товара после успешной загрузки ==================
    public function saveMarketPhoto($params)
    {
        return $this->request('photos.saveMarketPhoto', $params);
    }

    // Возвращает сервер для загрузки фотографии подборки ===================
    public function getMarketAlbumUploadServer($group_id)
    {
        $params = [
            'group_id' => $group_id
        ];
        return $this->request('photos.getMarketAlbumUploadServer', $params);
    }

    // Сохраняет фотографию подборки после успешной загрузки ================
    public function saveMarketAlbumPhoto($params)
    {
        return $this->request('photos.saveMarketAlbumPhoto', $params);
    }

    // Вызов методов API ВКонтакте ==========================================
    private function request($method, array $params)
    {
        $params['v'] = $this->v;

        $ch = curl_init('https://api.vk.com/method/' . $method . '?access_token=' . $this->token);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($data, true);
        if (!isset($json['response'])) {
            $json = json_encode($json, JSON_UNESCAPED_UNICODE);
            return mb_convert_encoding($json, 'utf-8', mb_detect_encoding($json));
        }
        usleep(mt_rand(1000000, 2000000));
        return $json['response'];
    }
}
