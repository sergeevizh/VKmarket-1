<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

class VKapi
{
    private $access_token;
    private $v;

    public function __construct($access_token, $v)
    {
        $this->token = $access_token;
        $this->v = $v;
    }

    // Загрузка фотографии на сервер ВКонтакте ===================
    public function upload($server, $image)
    {

        $path = 'image.jpg';
        copy(MODX_BASE_PATH . $image, 'image.jpg');

        $ch = curl_init($server);
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
        $data = json_decode($data, true);

        return $data;
    }

    // Вызов методов API ВКонтакте ==========================================
    public function request($method, array $params)
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
            return $json;
        }
        usleep(mt_rand(1000000, 2000000));
        return $json['response'];
    }

    // Вывод результата ================================================
    public function report($response, $result)
    {
        switch ($response) {
            case 'decode':
            default:
                return $result;
                break;

            case 'encode':
                return json_encode($result, JSON_UNESCAPED_UNICODE);
                break;
        }
    }
}
