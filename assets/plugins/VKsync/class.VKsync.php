<?php

class VKsync
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
    { }
}
