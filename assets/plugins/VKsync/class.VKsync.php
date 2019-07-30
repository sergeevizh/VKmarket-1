<?php

/* Выводит сообщение в лог EVO ==============================
-------------------------------------------------------------
Параметры
-------------------------------------------------------------
& case              |  тип сообщения : error / success
& title             |  код сообщения (заголовок)
& params            |  json с подробностями
============================================================= */

function alert($case, $title, $params)
{
    global $modx;

    switch ($case) {
        case 'error':
        default:
            $modx->logEvent(1, 3, json_encode($params, JSON_UNESCAPED_UNICODE), $title);
            break;

        case 'success':
            $modx->logEvent(1, 1, json_encode($params, JSON_UNESCAPED_UNICODE), $title);
            break;
    }
}
