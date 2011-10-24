<?php 

/* 
Dagon Design Sitemap Generator v3.x
http://www.dagondesign.com/articles/sitemap-generator-plugin-for-wordpress/

Estonian Language File
by Lembit Kivisik, http://designux.com/
*/

// Text shown on sitemap page 

define('DDSG_PAGE_HEADER', '<h2>Lehed</h2>');
define('DDSG_POST_HEADER', '<h2>Postitused</h2>');
define('DDSG_CAT_HEADER', '<strong>Rubriik</strong>');
define('DDSG_NO_TITLE', '(Pealkiri puudub)');
define('DDSG_VIEW_XML', 'XML Sitemap (otsingumootoritele)');
define('DDSG_CREDITS', 'Plugina autor');

// Navigation method 1
// Example: Page 2 of 5 : Previous : Next

define('DDSG_NAV1_PAGE', 'Leht');
define('DDSG_NAV1_OF', '/');
define('DDSG_NAV1_PREV', 'Eelmine');
define('DDSG_NAV1_NEXT', 'J&auml;rgmine');

define('DDSG_CONTINUED', '(j&auml;tkub)');

// Navigation method 2
// Example: Pages: 1 2 3 4 5

define('DDSG_NAV2_PAGE', 'Lehed:');


// Text shown in options page 
 
define('DDSG_DEFAULTS_LOADED', 'Algseaded taastatud!');
define('DDSG_CONFIG_UPDATED', 'Muudatused salvestatud!');

define('DDSG_FOR_INFO', 'Info ja uuendused:');
define('DDSG_DEFAULT_NOTICE', '<strong>Kui uuendad pluginat</strong>, taasta vaikimisi seaded, klikkides nupule <strong>Laadi algseaded</strong> lehe l&otilde;pus, sest plugina omadused v&otilde;ivad olla muutunud.');

define('DDSG_LANGUAGE', 'Keel:');
define('DDSG_LANGUAGE_DESC', '<em>T&otilde;lkefailid asuvad /wp-content/plugins/sitemap-generator/lang/</em>');
define('DDSG_GENERAL_OPTIONS', '<h3>&Uuml;ldseaded</h3>');
define('DDSG_ITEMS_PER_PAGE', 'Kuva:');
define('DDSG_ITEMS_PER_PAGE_INFO', '<em><strong>0</strong> = kuva k&otilde;ik &uuml;hel lehel</em>');
define('DDSG_SITEMAP_SLUG', 'Sisukaardi lehe l&uuml;hikirjeldus:');
define('DDSG_SITEMAP_SLUG_INFO', '<em>Kui kasutad kohandatud p&uuml;siviiteid, sisesta sisukaardi lehe l&uuml;hikirjeldus <em>(slug)</em>.</em>');
define('DDSG_SITEMAP_GENERATION', '<h3>Sisukaardi seaded</h3>');
define('DDSG_SHOW', 'N&auml;ita:');
define('DDSG_SHOW_BOTH', 'Lehti ja postitusi');
define('DDSG_SHOW_POSTS', 'Ainult postitusi');
define('DDSG_SHOW_PAGES', 'Ainult lehti');
define('DDSG_WHICH_FIRST', 'Kui m&otilde;lemad, n&auml;ita:');
define('DDSG_WHICH_FIRST_POSTS', 'Postitusi esimesena');
define('DDSG_WHICH_FIRST_PAGES', 'Lehti esimesena');
define('DDSG_POST_SORT', 'Postituste sortimisalus:');
define('DDSG_POST_SORT_T', 'Pealkiri');
define('DDSG_POST_SORT_DA', 'Avaldamiskuup&auml;ev (vanemad enne)');
define('DDSG_POST_SORT_DD', 'Avaldamiskuup&auml;ev (uuemad enne)');
define('DDSG_PAGE_SORT', 'Lehtede sortimisalus:');
define('DDSG_PAGE_SORT_T', 'Pealkiri');
define('DDSG_PAGE_SORT_DA', 'Avaldamiskuup&auml;ev (vanemad enne)');
define('DDSG_PAGE_SORT_DD', 'Avaldamiskuup&auml;ev (uuemad enne)');
define('DDSG_PAGE_SORT_MA', 'J&auml;rjestus (t&otilde;usev, 1&ndash;9)');
define('DDSG_PAGE_SORT_MD', 'J&auml;rjestus (laskuv, 9&ndash;1)');
define('DDSG_POST_COMMENTS', 'Kuva kommentaaride arvu postituste juures:');
define('DDSG_PAGE_COMMENTS', 'Kuva kommentaaride arvu lehtede juures:');
define('DDSG_ZERO_COMMENTS', 'Kui kommentaaride arv kuvatud, kuva ka nulli:');
define('DDSG_MULTI_POSTS', 'Kuva mitmesse rubriiki kuuluvaid postitusi ainult &uuml;he rubriigi all:');
define('DDSG_POST_DATES', 'Kuva avaldamiskuup&auml;eva postituste juures:');
define('DDSG_PAGE_DATES', 'Kuva avaldamiskuup&auml;eva lehtede juures:');
define('DDSG_DATE_FORMAT', 'Kuup&auml;eva formaat:');
define('DDSG_DATE_FORMAT_DESC', '<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Kuup&auml;evavormingu dokumentatsioon</a>');
define('DDSG_EXCLUSIONS', '<h3>V&auml;ljaj&auml;tmine</h3>');
define('DDSG_EXCLUDED_CATS', 'J&auml;ta v&auml;lja rubriigid:');
define('DDSG_EXCLUDED_CATS_DESC', '<em>&ndash; rubriikide ID-d, eraldatud komaga<br />&ndash; ka alamrubriigid j&auml;etakse v&auml;lja</em>');
define('DDSG_EXCLUDED_PAGES', 'J&auml;ta v&auml;lja lehed:');
define('DDSG_EXCLUDED_PAGES_DESC', '<em>&ndash; lehtede ID-d, eraldatud komaga<br />&ndash; ka alamlehed j&auml;etakse v&auml;lja</em>');
define('DDSG_HIDE_FUTURE', 'J&auml;ta v&auml;lja postitused avaldamiskuup&auml;evaga tulevikus:');
define('DDSG_HIDE_PASS', 'J&auml;ta v&auml;lja salas&otilde;naga kaitstud lehed ja postitused:');
define('DDSG_NAVIGATION', '<h3>Sirvimine</h3>');
define('DDSG_NAV_METHOD', 'Sisukaardi sirvimine:');
define('DDSG_NAV_WHERE', 'Kuva sirvimise viited:');
define('DDSG_NAV_WHERE_TOP', 'Sisukaardi &uuml;lal');
define('DDSG_NAV_WHERE_BOT', 'Sisukaardi all');
define('DDSG_NAV_WHERE_BOTH', 'Nii &uuml;lal kui all');
define('DDSG_MISC', '<h3>Muu</h3>');
define('DDSG_XML_PATH', 'XML Sitemap\'i rada:');
define('DDSG_XML_PATH_DESC', '<em>Kui kasutad lehel ka otsingumootoritele m&otilde;eldud XML-sisukaarti (Sitemap), n&auml;ita siin selle asukoht ning vastav viide lisatakse sisukaardi lehele.</em>');
define('DDSG_XML_WHERE', 'Kuva XML Sitemap\'i viide:');
define('DDSG_XML_WHERE_LAST', 'Viimase lehe l&otilde;pus');
define('DDSG_XML_WHERE_EVERY', 'Iga lehe l&otilde;pus');
define('DDSG_NEW_WINDOW', 'Ava sisukaardi viited uues aknas:');

define('DDSG_DEFAULT_BUTTON', 'Laadi algseaded');
define('DDSG_UPDATE_BUTTON', 'Salvesta muudatused');

?>
