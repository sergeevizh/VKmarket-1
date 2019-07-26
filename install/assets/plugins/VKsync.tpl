<?php
/**
 * VKsync
 *
 * Синхронизация товаров EVO CMS с товарами ВКонтакте
 *
 * @category    plugin
 * @version     1.0.0
 * @author      cgehuzi <mail.cgehuzi@yandex.ru>
 * @reportissues https://github.com/cgehuzi/VKmarket

 * @internal    @events OnDocFormPrerender,OnDocFormSave,OnBeforeDocDuplicate,OnDocDuplicate,OnBeforeDocFormDelete,OnDocFormDelete,OnDocFormUnDelete,OnBeforeEmptyTrash,OnEmptyTrash,OnBeforeMoveDocument,OnAfterMoveDocument
 * @internal    @modx_category API ВКонтакте
 * @internal    @properties &access_token=Ключ доступа к API (access_token);text &group_id=Идентификатор сообщества (group_id);int &v=версия API (v);int &template_product=Шаблон товара &template_album=Шаблон категории товаров
 * @internal    @installset base
 
 */
 
/**
 * VKsync
 *
 * Синхронизация товаров EVO CMS с товарами ВКонтакте
 *
 * @category    plugin
 * @version     1.0.0
 * @author      cgehuzi <mail.cgehuzi@yandex.ru>
 * @reportissues https://github.com/cgehuzi/VKmarket

 * @documentation Необходим для работы с API ВКонтакте
 * @documentation 
 * @documentation Документация: https://github.com/cgehuzi/VKmarket

 */

return require MODX_BASE_PATH . 'assets/plugins/VKsync/plugin.VKsync.php';