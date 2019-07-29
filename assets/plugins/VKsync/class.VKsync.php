<?php

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
