# apiVK

Сниппет для Evolution CMS

Работа с API ВКонтакте<br>
Ссылка на документацию API: <https://vk.com/dev/manuals>

Перед работой с API VK необходимо получить access_token

- **[Implicit flow](https://vk.com/dev/implicit_flow_user)**<br>
  Такой ключ может быть использован только для запросов непосредственно с устройства пользователя (например, для выполнения вызовов из Javascript на веб-сайте или из мобильного приложения).

  ```bash
  # Для запроса перейти по ссылке, подставив свой client_id
  https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token
  ```

- **[Authorization Code Flow](https://vk.com/dev/authcode_flow_user)**<br>
  Для работы с API от имени пользователя с серверной стороны Вашего сайта.

  ```bash
  # 1 Для запроса кода перейти по ссылке, подставив свой client_id
  https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=code

  # 2 По полученному коду получить ключ доступа
  https://oauth.vk.com/access_token?code=______&client_id=______&client_secret=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html
  ```

## Методы market

### Получение информации

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

#### [market.add](https://vk.com/dev/market.add)

Добавляет новый товар

- **name**<br>
  название товара

- **description**<br>
  описание товара<br>
  _для переноса строки НЕ ПОДХОДЯТ: `%0A , \n , <br> , ,`_

- **category_id**<br>
  идентификатор категории товара<br>
  _Получить список можно [тут](https://vk.com/dev/market.getCategories)_

- **price**<br>
  цена товара

- **main_photo_id**<br>
  идентификатор фотографии обложки товара

- **url**<br>
  ссылка на сайт товара

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

### Загрузка фото

#### [Фото для подборки](https://vk.com/dev/upload_files_2?f=7.%20%D0%97%D0%B0%D0%B3%D1%80%D1%83%D0%B7%D0%BA%D0%B0%20%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D0%B8%20%D0%B4%D0%BB%D1%8F%20%D0%BF%D0%BE%D0%B4%D0%B1%D0%BE%D1%80%D0%BA%D0%B8%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%BE%D0%B2)

_Минимальный размер фотографии -- 1280x720 пикселей_<br>
Передача файла производится через сниппет VKphotos

```php
[[VKphotos? &method=`getMarketAlbumUploadServer` &img=`___`]]
```

Сниппет возвращает JSON

```json
{
    "server": ______,
    "photo": ______,
    "gid": ______,
    "hash": ______
}
```

#### [Фото для товара](https://vk.com/dev/upload_files_2?f=6.%20%D0%97%D0%B0%D0%B3%D1%80%D1%83%D0%B7%D0%BA%D0%B0%20%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D0%B8%20%D0%B4%D0%BB%D1%8F%20%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%B0)

_Минимальный размер фотографии -- 400х400 пикселей_<br>
Передача файла производится через сниппет VKphotos

```php
[[VKphotos? &method=`getMarketUploadServer` &main=`1 | 0` &img=`___`]]
```

Сниппет возвращает JSON

```json
{
    "server": ______,
    "photo": "[{\"photo\":\"8387ec5d69:x\",\"sizes\":,\"latitude\":0,\"longitude\":0,\"kid\":\"8fe601bf5bdb63ef1f03f00362380402\"}]",
    "hash": ______,
    "crop_data": ______,
    "crop_hash": ______
}
```

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
