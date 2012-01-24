<?php 

/* 
Dagon Design Sitemap Generator v3.x
http://www.dagondesign.com/articles/sitemap-generator-plugin-for-wordpress/

Finnish Language File by
http://pokeriapuri.com
*/


// Text shown on sitemap page 

define('DDSG_PAGE_HEADER', '<h2>Sivut</h2>');
define('DDSG_POST_HEADER', '<h2>Artikkelit</h2>');
define('DDSG_CAT_HEADER', '<strong>Aihe:</strong>');
define('DDSG_NO_TITLE', '(Ei otsikkoa)');
define('DDSG_VIEW_XML', 'Avaa XML Sivukartta');
define('DDSG_CREDITS', 'Lis&auml;osan on tehnyt');

// Navigation method 1
// Example: Page 2 of 5 : Previous : Next

define('DDSG_NAV1_PAGE', 'Sivu');
define('DDSG_NAV1_OF', '&#47;');
define('DDSG_NAV1_PREV', 'Edellinen');
define('DDSG_NAV1_NEXT', 'Seuraava');

define('DDSG_CONTINUED', '(continued)');

// Navigation method 2
// Example: Pages: 1 2 3 4 5

define('DDSG_NAV2_PAGE', 'Sivut:');


// Text shown in options page 
 
define('DDSG_DEFAULTS_LOADED', 'Vakioasetukset ladattu!');
define('DDSG_CONFIG_UPDATED', 'Asetukset p&auml;ivitetty!');

define('DDSG_FOR_INFO', 'Lis&auml;&auml; tietoa ja p&auml;ivitykset t&auml;&auml;lt&auml;:');
define('DDSG_DEFAULT_NOTICE', '<strong>P&auml;ivititk&ouml; uuteen versioon?</strong> Jos p&auml;ivit&auml;t vanhemmasta versiosta, klikkaa <strong>Lataa vakioasetukset</strong> nappia alempaa. Asetukset ovat saattaneet muuttua uudemmassa versiossa.');

define('DDSG_LANGUAGE', 'Kieli');
define('DDSG_LANGUAGE_DESC', 'Kieliasetukset ja -tiedostot on tallennettu polkuun <strong>/wp-content/plugins/sitemap-generator/lang/</strong>');
define('DDSG_GENERAL_OPTIONS', 'Yleiset asetukset');
define('DDSG_ITEMS_PER_PAGE', 'Kohtia per sivu:');
define('DDSG_ITEMS_PER_PAGE_INFO', 'Aseta 0 jos haluat kaikki yhdelle sivulle');
define('DDSG_SITEMAP_SLUG', 'Sivukartta sivun :');
define('DDSG_SITEMAP_SLUG_INFO', 'Jos k&auml;yt&auml;t kestolinkkej&auml; (permalinks), kirjoita sivukartan kestolinkin nimi t&auml;h&auml;n. P&auml;ivit&auml; osoiterakenne jos muutat t&auml;t&auml;! Jos esimerkiksi kirjoitat &quot;sivukartta&quot;, kestolinkiksi tulee http&#58;&#47;&#47;DOMAIN-NIMESI&#47;sivukartta&#47;');
define('DDSG_SITEMAP_GENERATION', 'Sivukartan luominen');
define('DDSG_SHOW', 'Mit&auml; n&auml;ytet&auml;&auml;n:');
define('DDSG_SHOW_BOTH', 'Sivut ja artikkelit:');
define('DDSG_SHOW_POSTS', 'Vain artikkelit');
define('DDSG_SHOW_PAGES', 'Vain sivut');
define('DDSG_WHICH_FIRST', 'Jos n&auml;ytet&auml;&auml;n molemmat, kumpi ensin:');
define('DDSG_WHICH_FIRST_POSTS', 'Artikkelit ensin');
define('DDSG_WHICH_FIRST_PAGES', 'Sivut ensin');
define('DDSG_POST_SORT', 'Artikkeleiden j&auml;rjestys:');
define('DDSG_POST_SORT_T', 'Otsikon mukaan');
define('DDSG_POST_SORT_DA', 'P&auml;iv&auml;m&auml;&auml;r&auml;n mukaan (vanhin ensin)');
define('DDSG_POST_SORT_DD', 'P&auml;iv&auml;m&auml;&auml;r&auml;n mukaan (uusin ensin)');
define('DDSG_PAGE_SORT', 'Sivujen j&auml;rjestys:');
define('DDSG_PAGE_SORT_T', 'Otsikon mukaan');
define('DDSG_PAGE_SORT_DA', 'P&auml;iv&auml;m&auml;&auml;r&auml;n mukaan (vanhin ensin)');
define('DDSG_PAGE_SORT_DD', 'P&auml;iv&auml;m&auml;&auml;r&auml;n mukaan (uusin ensin)');
define('DDSG_PAGE_SORT_MA', 'Valikkoj&auml;rjestyksen mukaan (nouseva)');
define('DDSG_PAGE_SORT_MD', 'Valikkoj&auml;rjestyksen mukaan (laskeva)');
define('DDSG_POST_COMMENTS', 'N&auml;yt&auml; kommenttien m&auml;&auml;r&auml; artikkeleiden j&auml;lkeen:');
define('DDSG_PAGE_COMMENTS', 'N&auml;yt&auml; kommenttien m&auml;&auml;r&auml; sivujen j&auml;lkeen:');
define('DDSG_ZERO_COMMENTS', 'Jos kommenttien m&auml;&auml;r&auml; n&auml;ytet&auml;&auml;n, n&auml;ytet&auml;&auml;nk&ouml;, jos nolla:');
define('DDSG_MULTI_POSTS', 'N&auml;yt&auml; artikkelit vain yhdess&auml; aiheessa, jos luokitettu useampaan aiheeseen:');
define('DDSG_POST_DATES', 'N&auml;yt&auml; p&auml;iv&auml;m&auml;&auml;r&auml;t artikkeleiden j&auml;lkeen:');
define('DDSG_PAGE_DATES', 'N&auml;yt&auml; p&auml;iv&auml;m&auml;&auml;r&auml;t sivujen j&auml;lkeen:');
define('DDSG_DATE_FORMAT', 'P&auml;iv&auml;m&auml;&auml;r&auml;n formaatti (jos p&auml;iv&auml;m&auml;&auml;r&auml;t n&auml;ytet&auml;&auml;n):');
define('DDSG_DATE_FORMAT_DESC', 'K&auml;yt&auml; <a href="http://us3.php.net/date" target="_blank">PHP date() muotoilua</a>');
define('DDSG_EXCLUSIONS', 'Pois sulkeminen');
define('DDSG_EXCLUDED_CATS', 'Pois j&auml;tetyt aiheet:');
define('DDSG_EXCLUDED_CATS_DESC', '- Aihe (category) IDt, erotettuna pilkulla<br />- ala-aiheet j&auml;tet&auml;&auml;n my&ouml;s pois');
define('DDSG_EXCLUDED_PAGES', 'Pois j&auml;tetyt sivut:');
define('DDSG_EXCLUDED_PAGES_DESC', '- Sivu (page) IDt, erotettuna pilkulla<br />- ala-aiheet j&auml;tet&auml;&auml;n my&ouml;s pois');
define('DDSG_HIDE_FUTURE', 'Piilota tulevalla p&auml;iv&auml;m&auml;&auml;r&auml;ll&auml; merkatut artikkelit');
define('DDSG_HIDE_PASS', 'Piilota salasanalla suojatut:');
define('DDSG_NAVIGATION', 'Navigointi');
define('DDSG_NAV_METHOD', 'Sivukartan navigointi menetelm&auml;:');
define('DDSG_NAV_WHERE', 'N&auml;yt&auml; sivukartan navigointi:');
define('DDSG_NAV_WHERE_TOP', 'Ylh&auml;&auml;ll&auml;');
define('DDSG_NAV_WHERE_BOT', 'Alhaalla');
define('DDSG_NAV_WHERE_BOTH', 'Sek&auml; ylh&auml;&auml;ll&auml; ett&auml; alhaalla');
define('DDSG_MISC', 'Sekalaista');
define('DDSG_XML_PATH', 'XML Sivukartan koko polku:');
define('DDSG_XML_PATH_DESC', 'Jos k&auml;yt&auml;t my&ouml;s XML sitemap -lis&auml;osaa, voit kirjoittaa xml-sivukartan polun t&auml;h&auml;n ja linkki lis&auml;t&auml;&auml;n luotavallle sivukartta-sivulle');
define('DDSG_XML_WHERE', 'Miss&auml; XML sivukartta linkki n&auml;ytet&auml;&auml;n:');
define('DDSG_XML_WHERE_LAST', 'Viimeisen sivun lopussa');
define('DDSG_XML_WHERE_EVERY', 'Jokaisen sivun lopussa');
define('DDSG_NEW_WINDOW', 'Sivukartan linkit aukeavat uuteen ikkunaan:');

define('DDSG_DEFAULT_BUTTON', 'Lataa vakioasetukset');
define('DDSG_UPDATE_BUTTON', 'P&auml;ivit&auml; asetukset');

?>
