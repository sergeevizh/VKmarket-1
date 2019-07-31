<?php

if (!defined('MODX_BASE_PATH')) {
    die('What are you doing? Get out of here!');
}

require_once MODX_BASE_PATH . 'assets/modules/VKmarket/class.VKmarket.php';

$market = new VKmarket($modx);

/* ================================================================================
===================================================================================
include_once('eLists.class.php');
$eL = new eListsModule($modx);
$eL->Run();
===================================================================================
================================================================================ */


/********************* шаблон вывода в модуль ************************/
$output = <<<OUT
<!doctype html>
<html lang="ru">
<head>
	<title>VKmarket</title>
	<link rel="stylesheet" type="text/css" href="media/style/{$market->theme}/style.css" />
    <script type="text/javascript" src="media/script/tabpane.js"></script>
    <script type="text/javascript" src="[(mgr_jquery_path)]"></script>
    <script type="text/javascript" src="media/script/mootools/mootools.js"></script>
</head>
<body>
	<h1>Управление списками</h1>
    <div id="actions">
        <ul class="actionButtons">
            <li id="Button1">
                <a href="index.php?a=112&amp;id={$market->moduleid}">
                    <img src="media/style/{$market->theme}/images/icons/refresh.png" alt="Обновить"/>
                    Обновить
                </a>
            </li>
            <li id="Button2">
                <a href="index.php?a=106">
                    <img src="media/style/{$market->theme}/images/icons/stop.png" alt="Закрыть"/>
                    Закрыть
                </a>
            </li>
        </ul>
    </div>
</body>
</html>
OUT;
/****************** конец формирования шаблона в модуль ************/

//выводим все в область контента модуля
echo $output;
