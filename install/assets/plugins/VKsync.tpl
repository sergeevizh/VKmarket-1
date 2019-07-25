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
 * @internal    @properties &access_token=access_token;string;; &group_id=group_id;string;;
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