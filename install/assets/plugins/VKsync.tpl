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
 * @internal        @properties &access_token=VK access_token;text &group_id=VK group_id;int &v=VK v (версия api);list;5.100,5.101;5.101 &template_item=ID шаблона товаров;int &tv_item_id=ID TV для сохранения market_item_id (id товара);int &tv_item_name=ID TV с ВК-параметром name (название товара);int &tv_item_description=ID TV с ВК-параметром description (описание товара);int &tv_item_category_id=ID TV с ВК-параметром category_id (категория товара);int &tv_item_price=ID TV с ВК-параметром price (цена товара);int &tv_item_image=ID TV с ВК-параметром image (изображение товара);int &template_album=ID шаблона категорий (подборок);int &tv_album_id=ID TV для сохранения market_album_id (id подборки);int &tv_album_title=ID TV с ВК-параметром title (название подборки);int &tv_album_image=ID TV с ВК-параметром image (изображение подборки);int
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