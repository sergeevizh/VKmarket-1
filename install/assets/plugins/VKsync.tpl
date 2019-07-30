//<?php
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
 * @internal        @properties &access_token=access_token;text &group_id=group_id;int &v=Версия API (v);list;5.100,5.101;5.101 &template_product=Шаблон товара;int &template_album=Шаблон категории товаров;int
 * @internal        @events OnDocFormPrerender,OnDocFormSave,OnBeforeDocDuplicate,OnDocDuplicate,OnBeforeDocFormDelete,OnDocFormDelete,OnDocFormUnDelete,OnBeforeEmptyTrash,OnEmptyTrash,OnBeforeMoveDocument,OnAfterMoveDocument
 * @internal        @modx_category API ВКонтакте
 * @internal        @installset base
 * @internal        @disabled 1
 *
 * @documentation Необходим для работы с API ВКонтакте
 * @documentation 
 * @documentation Документация: https://github.com/cgehuzi/VKmarket 
 */
 
return require MODX_BASE_PATH . 'assets/plugins/VKsync/plugin.VKsync.php';