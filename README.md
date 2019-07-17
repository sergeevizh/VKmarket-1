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

## Общие параметры

### Обязательные

- **api_method** - вызываемый метод API<br>
  _например: `market.add`_

- **access_token** - ключ доступа к API

- **group_id** - идентификатор сообщества

### Дополнительные

- **v** - версия API<br>
  _по умолчанию: `5.101`_

## Поддерживаемые сниппетом методы

### [market.add](https://vk.com/dev/market.add)

Добавляет новый товар

**Обязательные параметры:**

- **name** - название товара

- **description** - описание товара<br>
  _перенос строки передать не получилось. Проверены символы:_

  ```javascript
  // В '& #__;' нужно убрать пробел
  ['%0A','\n','<br>','& #13;','& #10;','& #013;','& #010;']
  ```

- **category_id** - идентификатор категории товара<br>
  _cписок получается методом [market.getCategories](https://vk.com/dev/market.getCategories)_

- **price** - цена товара

- **image** - путь к файлу изображения<br>
  _мин. размер: 400х400px_

**Дополнительные параметры:**

- **album_ids** - идентификаторы подборок, в которые нужно добавить товар<br>
  _через запятую_

- **deleted** - статус товара<br>
  _`1` - удалён_<br>
  _`0` - не удалён_<br>
  _по умолчанию: `0`_<br>

- **url** - ссылка на сайт товара

### [market.addAlbum](https://vk.com/dev/market.addAlbum)

Добавляет новую подборку с товарами

**Обязательные параметры:**

- **title** - название подборки

**Дополнительные параметры:**

- **image** - путь к файлу изображения<br>
  _мин. размер: 1280х720px_

### [market.addToAlbum](https://vk.com/dev/market.addToAlbum)

Добавляет товар в одну или несколько подборок

**Обязательные параметры:**

- **item_id** - идентификатор товара в сообществе

- **album_ids** - идентификаторы подборок, в которые нужно добавить товар<br>
  _через запятую_

### [market.delete](https://vk.com/dev/market.delete)

Удаляет товар из сообщества

**Обязательные параметры:**

- **item_id** - идентификатор товара в сообществе

### [market.deleteAlbum](https://vk.com/dev/market.deleteAlbum)

Удаляет подборку с товарами

**Обязательные параметры:**

- **album_id** - идентификатор подборки

### [market.edit](https://vk.com/dev/market.edit)

Добавляет новый товар

**Обязательные параметры:**

- **item_id** - идентификатор товара в сообществе

**Дополнительные параметры:**

- **name** - новое название товара

- **description** - новое описание товара<br>
  _перенос строки передать не получилось. Проверены символы:_

  ```javascript
  // В '& #__;' нужно убрать пробел
  ['%0A','\n','<br>','& #13;','& #10;','& #013;','& #010;']
  ```

- **category_id** - идентификатор новой категории товара<br>
  _cписок получается методом [market.getCategories](https://vk.com/dev/market.getCategories)_

- **price** - новая цена товара

- **deleted** - новый статус товара<br>
  _`1` - удалён_<br>
  _`0` - не удалён_

- **image** - путь к файлу нового изображения<br>
  _мин. размер: 400х400px_

- **url** - новая ссылка на сайт товара

### [market.editAlbum](https://vk.com/dev/market.editAlbum)

Редактирует подборку с товарами

**Обязательные параметры:**

- **album_id** - идентификатор подборки

- **title** - новое название подборки

**Дополнительные параметры:**

- **image** - путь к файлу нового изображения<br>
  _мин. размер: 1280х720px_

### [market.get](https://vk.com/dev/market.get)

Возвращает список товаров в сообществе

**Дополнительные параметры:**

- **offset** - смещение относительно первого найденного товара<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых товаров<br>
  _максимум: `200`_<br>
  _по умолчанию: `100`_

- **extended** - возвращать ли дополнительные поля `likes`, `can_comment`, `can_repost`, `photos`, `views_count`<br>
  _`1` - возвращать_<br>
  _`0` - не возвращать_<br>
  _по умолчанию: `0`_

- **album_id** - идентификатор подборки, товары из которой нужно вернуть

### [market.getAlbums](https://vk.com/dev/market.getAlbums)

Возвращает список подборок в сообществе

**Дополнительные параметры:**

- **offset** - смещение относительно первой найденной подборки<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых подборок<br>
  _максимум: `100`_<br>
  _по умолчанию: `50`_

### [market.getCategories](https://vk.com/dev/market.getCategories)

Возвращает список категорий для товаров

**Дополнительные параметры:**

- **offset** - смещение относительно первой категории<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых категорий<br>
  _максимум: `1000`_<br>
  _по умолчанию: `10`_
