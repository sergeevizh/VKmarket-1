<?php

class Vk
{
    private $access_token;
    private $v;

    public function __construct($access_token, $v)
    {
        $this->token = $access_token;
        $this->v = $v;
    }

    // Добавляет новый товар =======================
    public function market__add($data)
    {
        return $this->request('market.add', $data);
    }

    // Добавляет новую подборку =======================
    public function market__addAlbum($data)
    {
        return $this->request('market.addAlbum', $data);
    }

    // Возвращает список подборок =======================
    public function market__getAlbums($data)
    {
        return $this->request('market.getAlbums', $data);
    }

    // Добавляет товар в одну или несколько подборок =======================
    public function market__addToAlbum($data)
    {
        return $this->request('market.addToAlbum', $data);
    }

    // Осуществляет загрузку фотографии на адрес сервера =======================
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

    // Возвращает адрес сервера для загрузки фотографии товара =======================
    public function photos__getMarketUploadServer($group_id, $main)
    {
        $params = [
            'group_id' => $group_id,
            'main_photo' => $main
        ];
        return $this->request('photos.getMarketUploadServer', $params);
    }

    // Сохраняет фотографию товара после успешной загрузки =======================
    public function photos__saveMarketPhoto($params)
    {
        return $this->request('photos.saveMarketPhoto', $params);
    }

    // Возвращает адрес сервера для загрузки фотографии подборки =======================
    public function photos__getMarketAlbumUploadServer($group_id)
    {
        $params = [
            'group_id' => $group_id
        ];
        return $this->request('photos.getMarketAlbumUploadServer', $params);
    }

    // Сохраняет фотографию подборки после успешной загрузки =======================
    public function photos__saveMarketAlbumPhoto($params)
    {
        return $this->request('photos.saveMarketAlbumPhoto', $params);
    }

    // Вызов методов API ВКонтакте =======================
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
            return json_encode($json);
        }
        usleep(mt_rand(1000000, 2000000));
        return $json['response'];
    }
}
