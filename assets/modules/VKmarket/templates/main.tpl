<!DOCTYPE html>
<html>

<head>
    <title>VKmarket</title>
    <link rel="stylesheet" type="text/css" href="media/style/[+theme+]/style.css" />
    <script type="text/javascript" src="media/script/tabpane.js"></script>
    <script type="text/javascript" src="[(mgr_jquery_path)]"></script>
    <script type="text/javascript" src="media/script/mootools/mootools.js"></script>
</head>

<body>
    <script>
        if ([(manager_theme_mode)] == '4') {
            document.body.className = 'darkness';
        }
    </script>

    <h1>
        <i class="fa fa-vk"></i> VKmarket
    </h1>

    <div id="actions">
        <div class="btn-group">
            <a id="Button1" class="btn btn-secondary" href="javascript:;" onclick="window.location.href='index.php?a=112&amp;id=[+moduleid+]';">
                <i class="fa fa-refresh"></i>
                <span>Обновить</span>
            </a>
            <a id="Button2" class="btn btn-success" href="javascript:;" onclick="window.location.href='index.php?a=106';">
                <i class="fa fa-times-circle"></i>
                <span>Закрыть VKmarket</span>
            </a>
        </div>
    </div>

    <div class="tab-pane" id="vkMarketPane">

        <div class="tab-page" id="tabItems">
            <h2 class="tab"><i class="fa fa-newspaper-o"></i> Товары</h2>
            <div class="tab-body">
                Товары
            </div>
        </div>

        <div class="tab-page" id="tabAlbums">
            <h2 class="tab"><i class="fa fa-list-alt"></i> Подборки</h2>
            <div class="tab-body">
                Подборки
            </div>
        </div>
    </div>

</body>

</html>