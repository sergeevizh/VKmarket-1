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
            <a class="btn btn-secondary" href="javascript:;" onclick="window.location.href='index.php?a=112&amp;id=[+moduleid+]';">
                <i class="fa fa-refresh"></i>
                <span>Обновить</span>
            </a>
            <a class="btn btn-danger" href="javascript:;" onclick="window.location.href='index.php?a=106';">
                <i class="fa fa-times-circle"></i>
                <span>Закрыть</span>
            </a>
        </div>
    </div>

    <div class="tab-pane" id="vkMarketPane">

        <div class="tab-page" id="tabItems">
            <h2 class="tab"><i class="fa fa-th"></i> Товары</h2>
            <div class="tab-body">
                Товары
            </div>
        </div>

        <div class="tab-page" id="tabAlbums">
            <h2 class="tab"><i class="fa fa-th-large"></i> Подборки</h2>
            <div class="tab-body">
                Подборки
            </div>
        </div>
    </div>

</body>

</html>