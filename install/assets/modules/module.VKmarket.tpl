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
 * @internal        @properties &access_token=access_token;text &group_id=group_id;text &v=v (версия API);text;5.101 &template_item=ID шаблона товаров;text &template_album=ID шаблона категорий (подборок);text &tv_list=Список используемых TV-параметров (tvList);text;price,image &original_name_tpl=Оригинал : чанк DocLister для названия;text;@CODE:[+pagetitle+] &original_description_tpl=Оригинал : чанк DocLister для описания;text;@CODE:[+description+] &original_price_tpl=Оригинал : чанк DocLister для цены;text;@CODE:[+tv.price+] &original_image_tpl=Оригинал : чанк DocLister для изображения;text;@CODE:[+image+] &license_name_tpl=Лицензия : чанк DocLister для названия;text;@CODE:[+pagetitle+] &license_description_tpl=Лицензия : чанк DocLister для описания;text;@CODE:[+description+] &license_price_tpl=Лицензия : чанк DocLister для цены;text;@CODE:[+tv.price+] &license_image_tpl=Лицензия : чанк DocLister для изображения;text;@CODE:[+image+] &spray_name_tpl=Спрей : чанк DocLister для названия;text;@CODE:[+pagetitle+] &spray_description_tpl=Спрей : чанк DocLister для описания;text;@CODE:[+description+] &spray_price_tpl=Спрей : чанк DocLister для цены;text;@CODE:[+tv.price+] &spray_image_tpl=Спрей : чанк DocLister для изображения;text;@CODE:[+image+] &probnik_name_tpl=Пробник : чанк DocLister для названия;text;@CODE:[+pagetitle+] &probnik_description_tpl=Пробник : чанк DocLister для описания;text;@CODE:[+description+] &probnik_price_tpl=Пробник : чанк DocLister для цены;text;@CODE:[+tv.price+] &probnik_image_tpl=Пробник : чанк DocLister для изображения;text;@CODE:[+image+] &phero10_name_tpl=С феромонами : чанк DocLister для названия;text;@CODE:[+pagetitle+] &phero10_description_tpl=С феромонами : чанк DocLister для описания;text;@CODE:[+description+] &phero10_price_tpl=С феромонами : чанк DocLister для цены;text;@CODE:[+tv.price+] &phero10_image_tpl=С феромонами : чанк DocLister для изображения;text;@CODE:[+image+] &mini_name_tpl=Миниатюра : чанк DocLister для названия;text;@CODE:[+pagetitle+] &mini_description_tpl=Миниатюра : чанк DocLister для описания;text;@CODE:[+description+] &mini_price_tpl=Миниатюра : чанк DocLister для цены;text;@CODE:[+tv.price+] &mini_image_tpl=Миниатюра : чанк DocLister для изображения;text;@CODE:[+image+] &album_title_tpl=Чанк DocLister для названия подборки (title);text;@CODE:[+pagetitle+] &album_image_tpl=Чанк DocLister для изображения подборки (image);text;@CODE:[+image+]
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