<?php
/**
 * VKsync
 *
 * Синхронизация товаров EVO CMS с товарами ВКонтакте
 *
 * @category        plugin
 * @version         1.0.0
 * @author          cgehuzi <mail.cgehuzi@yandex.ru>
 * @reportissues    https://github.com/cgehuzi/VKmarket
 * @license         http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 *
 * @internal        @events OnDocFormPrerender,OnDocFormSave,OnBeforeDocDuplicate,OnDocDuplicate,OnBeforeDocFormDelete,OnDocFormDelete,OnDocFormUnDelete,OnBeforeEmptyTrash,OnEmptyTrash,OnBeforeMoveDocument,OnAfterMoveDocument
 * @internal        @modx_category API ВКонтакте
 * @internal        @properties &access_token=VK access_token;text &group_id=VK group_id;int &v=VK v (версия api);list;5.100,5.101;5.101 &template_item=ID шаблона товаров;int &template_album=ID шаблона категорий (подборок);int &tv_item_id=ID TV для market_item_id (id товара);int &tv_album_id=ID TV для market_album_id (id подборки);int &tv_list=Список используемых TV-параметров (tvList);text &item_name_tpl=Чанк DocLister для названия товара (name);text &item_description_tpl=Чанк DocLister для описания товара (description);text &item_price_tpl=Чанк DocLister для цены товара (price);text &item_image_tpl=Чанк DocLister для изображения товара (image);text &album_title_tpl=Чанк DocLister для названия подборки (title);text &album_image_tpl=Чанк DocLister для изображения подборки (image);text &album_category_id=ID TV-параметра категории товаров в подборке (category_id);int
 * @internal        @installset base
 * @internal        @disabled 1 
 */
 
/**
 * VKsync
 *
 * Синхронизация товаров EVO CMS с товарами ВКонтакте
 *
 * @category        plugin
 * @version         1.0.0
 * @author          cgehuzi <mail.cgehuzi@yandex.ru>
 * @reportissues    https://github.com/cgehuzi/VKmarket
 * @license         http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 *
 * @documentation   Необходим для работы с API ВКонтакте
 * @documentation   Документация: https://github.com/cgehuzi/VKmarket
 */

return require MODX_BASE_PATH . 'assets/plugins/VKsync/plugin.VKsync.php';