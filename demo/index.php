<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2010-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          LGPL Lesser General Public License <http://www.gnu.org/copyleft/lesser.html>
 * @link             http://hizup.com
 */

if (version_compare(PHP_VERSION, '5.4.0', '<'))
{
    echo '<p style="color:#FF0000;text-align:center">Oops! Your PHP version is <i>' . PHP_VERSION . '</i>. <b>H2OTpl Demo</b> is only compatible with PHP <i>5.4</i> or higher.</p>';
    exit;
}

if (ini_get('short_open_tag') == 0)
{
    echo '<p style="color:#FF0000;text-align:center"><b>Warning!</b> short_open_tag of your PHP configuration is Off (must be On!)<br /> This example cannot work without <b>short_open_tag</b>.</p>';
    exit;
}

define('SITE_URL', 'http://'.dirname($_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']) .DIRECTORY_SEPARATOR);
include_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'libs/H2OTpl.class.php');
$oTpl = new H2OTpl;
$oTpl->setTplDir('templates'); // Set the templates directory
$oTpl->setCacheDir('cache'); // Set the cache directory
$oTpl->setTplExt('.tpl.php'); // Set a template extension
$oTpl->setCacheExt('.cache.php'); // Set a cache extension
$oTpl->setCaching(true); // Enable the cache
$oTpl->setCacheExpire(3600); // Set the caching time - 3600 = 1 hour
$oTpl->setHtmlCompress(true); // Set the HTML compress
// $oTpl->clearCache(); // Clear Cache

/* Disable automatic protection on variables, because we use HTML code in the variables. However, to protect other variable (such <title></title> tag in the example) it is still possible to use the escape() method. */
$oTpl->setAutoEscape(false);


// Create some variables...
$sName = 'Welcome To My WebSite';
$sH1Description = 'Hello World!';
$sDescription = 'Welcome to my site using <strong>H2OTpl</strong>: The <em>fastest</em> and <em>easiest</em> template engine using PHP!';

// Generate an array of authors and titles.
$aBookList = [
    [
        'author' => 'Hernando de Soto',
        'title' => 'The Mystery of Capitalism'
    ],
    [
        'author' => 'Neal Stephenson',
        'title' => 'Cryptonomicon'
    ],
    [
        'author' => 'Milton Friedman',
        'title' => 'Free to Choose'
    ]
];

// Assign values to the H2OTpl instance.
$oTpl->sBaseUrl = SITE_URL;
$oTpl->sTitle = $sName;
$oTpl->aBooks = $aBookList;
$oTpl->sTitle = $sName;
$oTpl->sH1 = $sH1Description;
$oTpl->sDesc = $sDescription;

// Display the template using the assigned values.
$oTpl->display('home');
