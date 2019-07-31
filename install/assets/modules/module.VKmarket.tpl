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
 * @internal        @modx_category API ВКонтакте
 * @internal        @properties &access_token=VK access_token;text &group_id=VK group_id;text &v=VK v (версия api);list;5.100,5.101;5.101 &template_item=ID шаблона товаров;text &template_album=ID шаблона категорий (подборок);text &tv_item_id=ID TV для market_item_id (id товара);text &tv_album_id=ID TV для market_album_id (id подборки);text &tv_list=Список используемых TV-параметров (tvList);text &item_name_tpl=Чанк DocLister для названия товара (name);text &item_description_tpl=Чанк DocLister для описания товара (description);text &item_price_tpl=Чанк DocLister для цены товара (price);text &item_image_tpl=Чанк DocLister для изображения товара (image);text &album_title_tpl=Чанк DocLister для названия подборки (title);text &album_image_tpl=Чанк DocLister для изображения подборки (image);text &album_category_id=TV-параметр категории товаров в подборке (category_id);text
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