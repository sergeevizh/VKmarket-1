/**
 * VKmarket
 *
 * Синхронизация товаров EVO CMS с товарами ВКонтакте
 *
 * @category        module
 * @version         1.0.0
 * @author          cgehuzi <mail.cgehuzi@yandex.ru>
 * @reportissues    https://github.com/cgehuzi/VKmarket
 * @license         http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 *
 * @internal        @guid VKmarket
 * @internal        @modx_category VKmarket
 * @internal        @properties &access_token=VK access_token;text &group_id=VK group_id;text &v=VK v (версия api);text;5.101 &template_item=ID шаблона товаров;text &template_album=ID шаблона категорий (подборок);text &tv_list=Список используемых TV-параметров (tvList);text;vk_item_id,vk_album_id,vk_category_id,price,image &item_name_tpl=Чанк DocLister для названия товара (name);text;@CODE:[+pagetitle+] &item_description_tpl=Чанк DocLister для описания товара (description);text;@CODE:[+description+] &item_price_tpl=Чанк DocLister для цены товара (price);text;@CODE:[+tv.price+] &item_image_tpl=Чанк DocLister для изображения товара (image);text;@CODE:[+image+] &album_title_tpl=Чанк DocLister для названия подборки (title);text;@CODE:[+pagetitle+] &album_image_tpl=Чанк DocLister для изображения подборки (image);text;@CODE:[+image+]
 * @internal        @installset base, sample
 */
 
/**
 * VKmarket
 *
 * Синхронизация товаров EVO CMS с товарами ВКонтакте
 *
 * @category        module
 * @version         1.0.0
 * @author          cgehuzi <mail.cgehuzi@yandex.ru>
 * @reportissues    https://github.com/cgehuzi/VKmarket
 * @license         http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 *
 * @documentation   Необходим для работы с API ВКонтакте
 * @documentation   Документация: https://github.com/cgehuzi/VKmarket
 */

require_once MODX_BASE_PATH . "assets/modules/VKmarket/module.VKmarket.php";