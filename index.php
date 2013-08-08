<?php

$yourTransifexUsername = 'transifex_user_or_email';
$yourTransifexPassword = 'transifex_password'; 

@mkdir('downloads',0777);
if(!file_exists('downloads')) {
    echo 'No permission to create downloads dir for saving CSV files!';
    exit;
}
touch('cookies.txt');
if(!file_exists('cookies.txt')) {
    echo 'No permission to create cookies.txt for saving cookies!';
    exit;
}
@unlink('cookies.txt');

$packages = 'Mage_Authorizenet
Mage_Bundle
Mage_Captcha
Mage_Catalog
Mage_CatalogRule
Mage_CatalogSearch
Mage_Centinel
Mage_Checkout
Mage_Cms
Mage_Compiler
Mage_Contacts
Mage_Cron
Mage_CurrencySymbol
Mage_Eav
Mage_GoogleAnalytics
Mage_GoogleBase
Mage_GoogleOptimizer
Mage_ImportExport
Mage_Index
Mage_Log
Mage_Media
Mage_PageCache
Mage_PaypalUk
Mage_Poll
Mage_ProductAlert
Mage_Rating
Mage_Reports
Mage_Review
Mage_Rss
Mage_Rule
Mage_SalesRule
Mage_Sendfriend
Mage_Sitemap
Mage_Weee
Mage_Tax
Mage_CatalogInventory
Mage_Connect
Mage_Install
Mage_Adminhtml
Mage_Core
Mage_Customer
Mage_Page
Mage_Backup
Mage_Persistent
Mage_AdminNotification
Mage_Directory
Mage_Tag
Mage_Widget
Mage_Dataflow
Mage_Sales
Mage_Downloadable
Mage_GoogleCheckout
Mage_Shipping
Mage_Api
Mage_Payment
Mage_Wishlist
Mage_Paypal
Mage_XmlConnect
Mage_Newsletter
Mage_GiftMessage
Mage_Usa
Mage_Api2
Mage_Paygate
Mage_Oauth
Mage_Paybox';
$packages = explode("\n",$packages);

$loginUrl = 'https://www.transifex.com/signin/';
$url = 'https://www.transifex.com/projects/p/magento-ce-17/resource/PACKAGE/l/nl/download/for_use/';

$loginPostInfo = array(
    'identification'=>$yourTransifexUsername,
    'password'=>$yourTransifexPassword,
    'remember_me'=>true,
    'next'=>'/projects/p/magento-ce-17/language/nl/'
);

/* Fetch the page to get the cookies */
$login = curl_init($loginUrl);
curl_setopt($login, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_exec($login);
curl_close($login);

/* Extract CSRF token from cookie */
$cookie = file_get_contents('cookies.txt');
$cookieLines = explode("\n",$cookie);
$cookieLineData = explode("\t",$cookieLines[count($cookieLines)-2]);
$CSRFToken = $cookieLineData[6];

/* Set CSRF token in login post info */
$loginPostInfo['csrfmiddlewaretoken'] = $CSRFToken;

/* Do the login post */
$ch = curL_init($loginUrl);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_REFERER, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($loginPostInfo));
curl_exec($ch);

/* Loop through all packages to retrieve all files */
foreach($packages as $package) {
    $csvUrl = str_replace('PACKAGE',strtolower($package),$url);
    curl_setopt($ch, CURLOPT_URL, $csvUrl);
    $csv = curl_exec($ch);
        
    file_put_contents('downloads/' . $package . '.csv',$csv);
    echo $package . ' downloaded.'."\n";
}

if(count($packages) == count(scandir('downloads'))-2) {
    echo 'All files are downloaded.'."\n";
}
