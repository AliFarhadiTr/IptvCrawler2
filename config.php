<?php

require_once 'vendor/autoload.php';

/*
 * set countries
 *allowed countries=>
 * Sport,Africa,Asia,Europe,North America,Oceania,South America,Arab Countries,Ex Yugoslavia,Latin America,
 * Scandinavia,Afghanistan,Albania,Algeria,American Samoa,Andorra,Angola,Argentina,Armenia,Aruba,Australia,
 * Austria,Azerbaijan,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Bolivia,Bosnia and Herzegovina,Brazil,Brunei,
 * Bulgaria,Burkina Faso,Cambodia,Cameroon,Canada,Chile,China,Colombia,Costa Rica,Croatia,Curaçao,Cyprus,
 * Czech Republic,Democratic Republic of the Congo,Denmark,Dominican Republic,Ecuador,Egypt,El Salvador,
 * Equatorial Guinea,Eritrea,Estonia,Ethiopia,Faroe Islands,Fiji,Finland,France,Gambia,Georgia,Germany,
 * Ghana,Greece,Guadeloupe,Haiti,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,
 * Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kosovo,Kuwait,Kyrgyzstan,Laos,Latvia,Lebanon,Libya,
 * Liechtenstein,Lithuania,Luxembourg,Macao,Malaysia,Maldives,Mexico,Moldova,Mongolia,Montenegro,Morocco,
 * Mozambique,Myanmar,Nepal,Netherlands,New Zealand,Nicaragua,Nigeria,North Korea,North Macedonia,Norway,
 * Oman,Pakistan,Palestine,Panama,Paraguay,Peru,Philippines,Poland,Portugal,Puerto Rico,Qatar,Romania,
 * Russian Federation,Rwanda,San Marino,Saudi Arabia,Senegal,Serbia,Sierra Leone,Singapore,Slovakia,Slovenia,
 * Somalia,South Korea,Spain,Sri Lanka,Sudan,Sweden,Switzerland,Syria,Taiwan,Tanzania,Thailand,
 * Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Ukraine,Undefined,United Arab Emirates,United Kingdom,
 * United States of America,Uruguay,Uzbekistan,Venezuela,Vietnam,Western Sahara,Yemen,
 *   لیست کشورها
 */
define('COUNTRIES',['Sport','Africa','Asia','Europe']);
/*#####################################################################################################*/
/*
 *set quality range (0-100)
 * محدودیت کیفیت
 */
define('MIN_QUALITY',80);
define('MAX_QUALITY',100);
/*#####################################################################################################*/
/*
 *set status (enable,disable,all)
 * انتخاب وضعیت
 */
define('STATUS','enable');
/*#####################################################################################################*/
/*
 * maximum channels(0-100000)
 * حداکثر کانال
 */
define('MAX_CHANEL',5);
/*#####################################################################################################*/
/*
 * replace find_text to replace_text (['txt1','txt2'])
 * حذفیات
 */
define('FIND_TEXT',['#PLAYLIST:iptvcat.com']);
define('REPLACE_TEXT',['#EXTM3U']);
/*#####################################################################################################*/
/*
 *متن مربوط به کل شبکه ها
 */
define('APPEND_FIRST',
    '#EXTINF:-1 tvg-logo="https://lh3.googleusercontent.com/ogw/ADGmqu-mfXA-oq04H82TcIWTi4HIuoDHyAjC1LvyGSJJ" group-title="Premium IPTV & ZalTV",Long term IPTV & ZalTV special offer
    http://www.mene77.ir/zt/ads-11626999.mp4');
/*#####################################################################################################*/
/*
 *متن مربوط به هر دسته
 */
define('APPEND_ALL',
    '#EXTINF:-1 tvg-logo="https://lh3.googleusercontent.com/ogw/ADGmqu-mfXA-oq04H82TcIWTi4HIuoDHyAjC1LvyGSJJ" group-title="Sport(5)",Long term Sport IPTV & ZalTV
    http://www.mene77.ir/zt/ads-11626.mp4');

// USE #__COUNTRY__# for country name
/*#####################################################################################################*/
/*
 * github token(use '' for disable)
 */
define('GIT_TOKEN','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');