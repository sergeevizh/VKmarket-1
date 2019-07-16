# apiVK

-- _Сниппет для Evolution CMS_ --

Работа с API ВКонтакте<br>
Ссылка на документацию API: <https://vk.com/dev/manuals>

Перед работой с API ВКонтакте необходимо получить access_token

- **[Implicit flow](https://vk.com/dev/implicit_flow_user)**<br>
  Такой ключ может быть использован только для запросов непосредственно с устройства пользователя (например, для выполнения вызовов из Javascript на веб-сайте или из мобильного приложения).

  ```bash
  # Для запроса перейти по ссылке, подставив свой client_id
  https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token
  ```

  [Ссылка из примера](https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token)

- **[Authorization Code Flow](https://vk.com/dev/authcode_flow_user)**<br>
  Для работы с API от имени пользователя с серверной стороны Вашего сайта.

  ```bash
  # 1 Для запроса кода перейти по ссылке, подставив свой client_id
  https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=code

  # 2 По полученному коду получить ключ доступа
  https://oauth.vk.com/access_token?code=______&client_id=______&client_secret=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html
  ```

  [Ссылка #1 из примера](https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=code)<br>
  [Ссылка #2 из примера](https://oauth.vk.com/access_token?code=______&client_id=______&client_secret=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html)

## Поддерживаемые сниппетом методы

### [Раздел "Market"](https://vk.com/dev/market)

#### [market.add](https://vk.com/dev/market.add)

Добавляет новый товар

**Обязательные параметры:**

- **api_method** - вызываемый метод API<br>
  _указать: `market.add`_

- **access_token** - ключ доступа к API

- **group_id** - ID сообщества

- **name** - название товара

- **description** - описание товара<br>
  _перенос строки передать не получилось. Проверены символы:_

  ```javascript
  // В '& #__;' нужно убрать пробел
  ['%0A','\n','<br>','& #13;','& #10;','& #013;','& #010;']
  ```

- **category_id** : идентификатор категории товара<br>
  _cписок получается методом [market.getCategories](https://vk.com/dev/market.getCategories)_

- **price** : цена товара

- **image** : путь к файлу изображения<br>
  _мин. размер: 400х400px_

**Дополнительные параметры:**

- **v** - версия API<br>
  _по умолчанию: `5.101`_

- **album_ids** - ID подборок, к которым относится товар<br>
  _через запятую_

- **deleted** - статус товара<br>
  _`1` - удалён_<br>
  _`0` - не удалён_<br>
  _по умолчанию: `0`_<br>

- **url**<br>
  ссылка на сайт товара

#### [market.getAlbums](https://vk.com/dev/market.getAlbums)

Возвращает список подборок в сообществе

#### [market.get](https://vk.com/dev/market.get)

Возвращает список товаров в сообществе

- **extended**<br>
  возвращать ли дополнительные поля<br>
  _1 -- будут возвращены поля `likes, can_comment, can_repost, photos, views_count`_

### Добавление подборок / товаров

#### [market.addAlbum](https://vk.com/dev/market.addAlbum)

Добавляет новую подборку с товарами

- **title**<br>
  название подборки

- **photo_id**<br>
  идентификатор фотографии-обложки подборки

#### [market.addToAlbum](https://vk.com/dev/market.addToAlbum)

Добавляет товар в одну или несколько подборок

- **item_id**<br>
  идентификатор товара

- **album_ids**<br>
  идентификаторы подборок, в которые нужно добавить товар<br>
  _указываются через запятую_

## Методы photos

### Получение серверов для загрузки фото

#### [photos.getMarketAlbumUploadServer](https://vk.com/dev/photos.getMarketAlbumUploadServer)

Возвращает адрес сервера для загрузки фотографии подборки

- **group_id**<br>
  идентификатор сообщества, для которого необходимо загрузить фотографию подборки товаров

#### [photos.getMarketUploadServer](https://vk.com/dev/photos.getMarketUploadServer)

Возвращает адрес сервера для загрузки фотографии товара

- **group_id**<br>
  идентификатор сообщества, для которого необходимо загрузить фотографию подборки товаров

- **main_photo**<br>
  является ли фотография обложкой товара<br>
  _1 -- фотография для обложки_

### Сохранение загруженного фото

#### [Фото для подборки](https://vk.com/dev/photos.saveMarketAlbumPhoto)

Сохраняет фотографию подборки после успешной загрузки

- **group_id**<br>
  идентификатор сообщества, для которого необходимо загрузить фотографию подборки товаров

- **photo**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

- **server**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

- **hash**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

#### [Фото для товара](https://vk.com/dev/photos.saveMarketPhoto)

Сохраняет фотографию товара после успешной загрузки

- **group_id**<br>
  идентификатор сообщества, для которого необходимо загрузить фотографию подборки товаров

- **photo**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

- **server**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

- **hash**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

- **crop_data**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер

- **crop_hash**<br>
  параметр, возвращаемый в результате загрузки фотографии на сервер
