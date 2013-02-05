<?php

//$debut = microtime(true);

define('WEBROOT',dirname(__FILE__));
define('ROOT',dirname(WEBROOT));
define('DS',DIRECTORY_SEPARATOR);
define('CORE',ROOT.DS.'core');
define('BASE_URL',dirname(dirname($_SERVER['SCRIPT_NAME'])));



//init autoloader
//page github https://github.com/jonathankowalski/autoload
include '../core/autoloader.php';
$loader = JK\Autoloader::getInstance()
->addDirectory('../config')
->addDirectory('../controller')
->addDirectory('../core')
->addDirectory('../model');

//Mail librairy
//require '../lib/SwiftMailer/swift_required.php';


//define routes for the router
new Routes();

//launch the dispacher
new Dispatcher();

?>



<?php
//echo 'Page généré en '.round(microtime(true) - $debut,5).' secondes';
?>

