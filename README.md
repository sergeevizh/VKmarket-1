# apiVK

-- _Сниппет для Evolution CMS_ --

Работа с API ВКонтакте<br>
Ссылка на документацию API: <https://vk.com/dev/manuals>

## Ключ доступа к API

Перед началом работы с API ВКонтакте необходимо получить ключ доступа access_token

- **[Implicit flow](https://vk.com/dev/implicit_flow_user)**<br>

  - [x] Для запросов с устройства пользователя (например через Javascript на веб-сайте)
  - [ ] Для запросов с серверной стороны

--------------------------------------------------------------------------------

```bash
# Для запроса перейти по ссылке, подставив свой client_id
  https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token
```

[Ссылка из примера](https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token)

- **[Authorization Code Flow](https://vk.com/dev/authcode_flow_user)**<br>

  - [x] Для запросов с устройства пользователя (например через Javascript на веб-сайте)
  - [x] Для запросов с серверной стороны

--------------------------------------------------------------------------------

```bash
# 1 Для запроса кода перейти по ссылке, подставив свой client_id
  https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=code

  # 2 По полученному коду получить ключ доступа
  https://oauth.vk.com/access_token?code=______&client_id=______&client_secret=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html
```

[Ссылка #1 из примера](https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=code)<br>
[Ссылка #2 из примера](https://oauth.vk.com/access_token?code=______&client_id=______&client_secret=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html)

## Поддерживаемые сниппетом методы

- [market.add](#marketadd) - добавляет новый товар
- [market.addAlbum](#marketaddalbum) - добавляет новую подборку с товарами
- [market.delete](#marketdelete) - удаляет товар
- [market.deleteAlbum](#marketdeletealbum) - удаляет подборку с товарами
- [market.edit](#marketedit) - редактирует товар
- [market.editAlbum](#marketeditalbum) - редактирует подборку с товарами
- [market.get](#marketget) - возвращает список товаров в сообществе
- [market.getAlbums](#marketgetalbums) - возвращает список подборок в сообществе
- [market.getCategories](#marketgetcategories) - возвращает список категорий для товаров
- [market.removeFromAlbum](#marketremovefromalbum) - удаляет товар из подборок
- [market.search](#marketsearch) - ищет товары в каталоге сообщества

## Передача файлов

Передача файлов производится через специальные параметры:<br>

- **image** - путь к файлу изображения (абсолютный или относительно доменного имени)

### Процесс загрузки

Сниппет в фоновом режиме реализует полный [процесс загрузки файлов](https://vk.com/dev/upload_files):

1. Получает адрес для загрузки файла;
2. Передаёт файл на полученный адрес;
3. Сохраняет информацию о загруженном файле.

## Общие параметры

### Обязательные

- **api_method** - вызываемый метод API<br>
  _например: `market.add`_

- **access_token** - ключ доступа к API

- **group_id** - идентификатор сообщества

### Дополнительные

- **v** - версия API<br>
  _по умолчанию: `5.101`_

## Параметры методов

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

Редактирует товар

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

- **album_id** - идентификатор подборки, товары из которой нужно вернуть<br>
  _по умолчанию: `0`_

- **offset** - смещение относительно первого найденного товара<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых товаров<br>
  _максимум: `200`_<br>
  _по умолчанию: `100`_

- **extended** - возвращать ли дополнительные поля `likes`, `can_comment`, `can_repost`, `photos`, `views_count`<br>
  _`1` - возвращать_<br>
  _`0` - не возвращать_<br>
  _по умолчанию: `0`_

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

### [market.removeFromAlbum](https://vk.com/dev/market.removeFromAlbum)

Удаляет товар из одной или нескольких выбранных подборок

**Обязательные параметры:**

- **item_id** - идентификатор товара

- **album_ids** - идентификаторы подборок, из которых нужно удалить товар<br>
  _через запятую_

### [market.search](https://vk.com/dev/market.search)

Ищет товары в каталоге сообщества

**Дополнительные параметры:**

- **album_id** - идентификатор подборки, товары из которой нужно вернуть<br>
  _по умолчанию: `0`_

- **q** - строка поискового запроса

- **price_from** - минимальное значение цены товаров<br>
  _указывается цена, умноженная на 100_

- **price_to** - максимальное значение цены товаров<br>
  _указывается цена, умноженная на 100_

- **sort** - вид сортировки<br>
  _`0` - пользовательская расстановка_<br>
  _`1` - по дате добавления товара_<br>
  _`2`_ - по цене<br>
  _`3` - по популярности_<br>
  _по умолчанию: `0`_

- **rev** - направление сортировки<br>
  _`0` - по возрастанию_<br>
  _`1` - по убыванию_<br>
  _по умолчнию: `1`_

- **offset** - смещение относительно первого найденного товара<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых товаров<br>
  _максимум: `200`_<br>
  _по умолчанию: `20`_

- **extended** - возвращать ли дополнительные поля `likes`, `can_comment`, `can_repost`, `photos`, `views_count`<br>
  _`1` - возвращать_<br>
  _`0` - не возвращать_<br>
  _по умолчанию: `0`_
