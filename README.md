# VKmarket

-- _Сниппет для Evolution CMS_ --

Работа с API для товаров ВКонтакте<br>
Документация API для товаров: <https://vk.com/dev/goods_docs>

## Ключ доступа к API

Перед началом работы необходимо получить ключ доступа access_token

### [Implicit flow](https://vk.com/dev/implicit_flow_user)

- [x] для запросов с устройства пользователя (например через Javascript на веб-сайте)
- [ ] для запросов с серверной стороны

--------------------------------------------------------------------------------

```bash
# Для запроса перейти по ссылке, подставив свой client_id
https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token
```

[Ссылка из примера](https://oauth.vk.com/authorize?client_id=______&v=5.101&redirect_uri=https://oauth.vk.com/blank.html&scope=market,photos&response_type=token)

### [Authorization Code Flow](https://vk.com/dev/authcode_flow_user)

- [x] для запросов с устройства пользователя (например через Javascript на веб-сайте)
- [x] для запросов с серверной стороны

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

## Загрузка изображений

Загрузка изображений производится через специальный параметр:<br>

- **image** - путь к файлу изображения<br>
  _абсолютный или относительный (от доменного имени)_

### Процесс загрузки

Сниппет в фоновом режиме реализует полный [процесс загрузки](https://vk.com/dev/upload_files):

1. Получает адрес для загрузки файла;
2. Передаёт файл на полученный адрес;
3. Сохраняет информацию о загруженном файле.

## Общие параметры

Данные параметры указываются для каждого из нижеприведённых методов

**Обязательные параметры:**

- **api_method** - вызываемый метод API<br>
  _например: `market.add`_

- **access_token** - ключ доступа к API<br>
  _тип ключа: [Authorization Code Flow](#authorization-code-flow)_

- **group_id** - идентификатор сообщества

**Дополнительные параметры:**

- **v** - версия API<br>
  _по умолчанию: `5.101`_

## Параметры методов

### [market.add](https://vk.com/dev/market.add)

Добавляет новый товар

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **name** - название товара

- **description** - описание товара<br>
  _перенос строки передать не получилось. Проверены символы:_

  ```javascript
  // В '& #__;' нужно убрать пробел
  ['%0A','\n','<br>','& #13;','& #10;','& #013;','& #010;']
  ```

- **category_id** - идентификатор категории товара<br>
  _cписок получается методом [market.getCategories](#marketgetcategories)_

- **price** - цена товара

- **image** - путь к файлу изображения<br>
  _мин. размер: 400х400px_

**Дополнительные параметры:**

- **deleted** - статус товара<br>
  _`1` - удалён_<br>
  _`0` - не удалён_<br>
  _по умолчанию: `0`_<br>

- **url** - ссылка на сайт товара

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Item created",
          "response" => (int) "идентификатор созданного товара",
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Item created",
          "response" : "идентификатор созданного товара",
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.addAlbum](https://vk.com/dev/market.addAlbum)

Добавляет новую подборку с товарами

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **title** - название подборки

**Дополнительные параметры:**

- **image** - путь к файлу изображения<br>
  _мин. размер: 1280х720px_

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Album created",
          "response" => (int) "идентификатор созданной подборки",
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Album created",
          "response" : "идентификатор созданной подборки",
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.addToAlbum](https://vk.com/dev/market.addToAlbum)

Добавляет товар в одну или несколько подборок

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **item_id** - идентификатор товара в сообществе

- **album_ids** - идентификаторы подборок, в которые нужно добавить товар<br>
  _через запятую_

**Дополнительные параметры:**

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Item added to albums",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Item added to albums",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.delete](https://vk.com/dev/market.delete)

Удаляет товар из сообщества

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **item_id** - идентификатор товара в сообществе

**Дополнительные параметры:**

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Item deleted",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Item deleted",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.deleteAlbum](https://vk.com/dev/market.deleteAlbum)

Удаляет подборку с товарами

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **album_id** - идентификатор подборки

**Дополнительные параметры:**

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Album deleted",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Album deleted",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.edit](https://vk.com/dev/market.edit)

Редактирует товар

**[Общие параметры](#общие-параметры)**

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
  _cписок получается методом [market.getCategories](#marketgetcategories)_

- **price** - новая цена товара

- **deleted** - новый статус товара<br>
  _`1` - удалён_<br>
  _`0` - не удалён_

- **image** - путь к файлу нового изображения<br>
  _мин. размер: 400х400px_

- **url** - новая ссылка на сайт товара

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Item edited",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Item edited",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.editAlbum](https://vk.com/dev/market.editAlbum)

Редактирует подборку с товарами

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **album_id** - идентификатор подборки

- **title** - новое название подборки

**Дополнительные параметры:**

- **image** - путь к файлу нового изображения<br>
  _мин. размер: 1280х720px_

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Album edited",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Album edited",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.get](https://vk.com/dev/market.get)

Возвращает список товаров в сообществе

**[Общие параметры](#общие-параметры)**

**Дополнительные параметры:**

- **album_id** - идентификатор подборки, товары из которой нужно вернуть<br>
  _по умолчанию: `0`_

- **offset** - смещение относительно первого найденного товара<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых товаров<br>
  _максимум: `200`_<br>
  _по умолчанию: `100`_

- **extended** - возвращать ли дополнительные поля `albums_ids`, `photos`, `likes`, `views_count`<br>
  _`1` - возвращать_<br>
  _`0` - не возвращать_<br>
  _по умолчанию: `0`_

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Items received",
          "response" => [
              "count" => (int) "количество товаров в сообществе",
              "items" => [
                  [ ТОВАР ],
                  [ ТОВАР ]
               ]
          ],
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Items received",
          "response" : {
              "count" : "количество товаров в сообществе",
              "items" : [
                  { "ТОВАР" },
                  { "ТОВАР" }
              ]
          },
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

Подробнее о полях объекта "ТОВАР" смотрите [ниже](#товар)

### [market.getAlbums](https://vk.com/dev/market.getAlbums)

Возвращает список подборок в сообществе

**[Общие параметры](#общие-параметры)**

**Дополнительные параметры:**

- **offset** - смещение относительно первой найденной подборки<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых подборок<br>
  _максимум: `100`_<br>
  _по умолчанию: `50`_

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Albums received",
          "response" => [
              "count" => (int) "количество подборок в сообществе",
              "items" => [
                  [ ПОДБОРКА ],
                  [ ПОДБОРКА ]
               ]
          ],
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Albums received",
          "response" : {
              "count" : "количество подборок в сообществе",
              "items" : [
                  { "ПОДБОРКА" },
                  { "ПОДБОРКА" }
              ]
          },
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

Подробнее о полях объекта "ПОДБОРКА" смотрите [ниже](#подборка-товаров)

### [market.getCategories](https://vk.com/dev/market.getCategories)

Возвращает список категорий для товаров

**[Общие параметры](#общие-параметры)**

**Дополнительные параметры:**

- **offset** - смещение относительно первой категории<br>
  _по умолчанию: `0`_

- **count** - количество возвращаемых категорий<br>
  _максимум: `1000`_<br>
  _по умолчанию: `10`_

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Categories received",
          "response" => [
              "count" => (int) "количество подборок в сообществе",
              "items" => [
                  [
                      "id" => 1,
                      "name" => "Женская одежда",
                      "section" => [
                          "id" => 0,
                          "name" => "Гардероб"
                      ]
                  ],[
                      "id" => 2,
                      "name" => "Мужская одежда",
                      "section" => [
                          "id" => 0,
                          "name" => "Гардероб"
                      ]
                  ]
               ]
          ],
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Categories received",
          "response" : {
              "count" : "количество доступных категорий",
              "items" : [
                  {
                      "id": 1,
                      "name": "Женская одежда",
                      "section": {
                          "id": 0,
                          "name": "Гардероб"
                      }
                  },{
                      "id": 2,
                      "name": "Мужская одежда",
                      "section": {
                          "id": 0,
                          "name": "Гардероб"
                      }
                  }
              ]
          },
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.removeFromAlbum](https://vk.com/dev/market.removeFromAlbum)

Удаляет товар из одной или нескольких выбранных подборок

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **item_id** - идентификатор товара

- **album_ids** - идентификаторы подборок, из которых нужно удалить товар<br>
  _через запятую_

**Дополнительные параметры:**

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Item removed from albums",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Item removed from albums",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.reorderAlbums](https://vk.com/dev/market.reorderAlbums)

Изменяет положение подборки с товарами в списке

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **album_id** - идентификатор подборки

**Дополнительные параметры:**

- **before** - идентификатор подборки, перед которой следует поместить текущую

- **after** - идентификатор подборки, после которой следует поместить текущую

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Albums reordered",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Albums reordered",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.reorderItems](https://vk.com/dev/market.reorderItems)

Изменяет положение товара в подборке

**[Общие параметры](#общие-параметры)**

**Обязательные параметры:**

- **item_id** - идентификатор товара

**Дополнительные параметры:**

- **album_id** - идентификатор подборки, в которой находится товар<br>
  `0` - сортировка общего списка товаров

- **before** - идентификатор товара, перед которым следует поместить текущий

- **after** - идентификатор товара, после которого следует поместить текущий

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Items reordered",
          "response" => 1,
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Items reordered",
          "response" : 1,
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

### [market.search](https://vk.com/dev/market.search)

Ищет товары в каталоге сообщества

**[Общие параметры](#общие-параметры)**

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

- **extended** - возвращать ли дополнительные поля `albums_ids`, `photos`, `likes`, `views_count`<br>
  _`1` - возвращать_<br>
  _`0` - не возвращать_<br>
  _по умолчанию: `0`_

- **response** - тип успешного результата<br>
  `decode` - php-массив с подробностями

  ```php
  [
      "success" => [
          "message" => "Search done",
          "response" => [
              "count" => (int) "количество товаров в сообществе",
              "items" => [
                  [ ТОВАР ],
                  [ ТОВАР ]
               ]
          ],
          "request_params" => [
              "параметр" => "значение",
              "параметр" => "значение"
          ]
      ]
  ]
  ```

  `encode` - json с подробностями

  ```javascript
  {
      "success" : {
          "message" : "Search done",
          "response" : {
              "count" : "количество товаров в сообществе",
              "items" : [
                  { "ТОВАР" },
                  { "ТОВАР" }
              ]
          },
          "request_params" : {
              "параметр" : "значение",
              "параметр" : "значение"
          }
      }
  }
  ```

  _по умолчанию: `decode`_

Подробнее о полях объекта "ТОВАР" смотрите [ниже](#товар)

## Типы объектов ВКонтакте

### [Товар](https://vk.com/dev/objects/market_item)

В данном примере приведены не все возможные поля товаров, а лишь наиболее актуальные.

```javascript
{
    // ОСНОВЫНЕ ПОЛЯ:

    "availability": 0, // статус товара
    // 0 : доступен
    // 1 : удалён
    // 2 : недоступен
    "date": 1563820542, // дата добавления
    "description": "Описание товара",
    "id": 2767124,
    "price": {
        "amount": "4590", // цена (в сотых долях)
        "currency": { // валюта
            "id": 933,
            "name": "BYN"
        },
        "text": "45.90 бел. руб."
    },
    "thumb_photo": "изображение", // размера 400х400
    "title": "Название товара",

    // ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ:
    // при extended = 1

    "albums_ids": [1, 2, 3], // массив подборок
    "likes": {  
        "count": 1, // полное количество лайков
        "user_likes": 1
        // 1 : есть лайк от текущего пользователя
        // 0 : нет лайка от текущего пользователя
    },  
    "views_count": 8 // количество просмотров
}
```

### [Подборка товаров](https://vk.com/dev/objects/market_album)

В данном примере приведены не все возможные поля подборок, а лишь наиболее актуальные.

```javascript
{
    "id": 3,
    "title": "Название подборки",
    "count": 58, // количество товаров
    "updated_time": 1563556609 // дата обновления подборки
    // обновление === добавление новых товаров
}
```
