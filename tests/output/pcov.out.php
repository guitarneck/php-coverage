<?php
/*
   This script generate data output from pcov extension.

   https://pecl.php.net/package/pcov
   https://github.com/krakjoe/pcov

   run :
   -----
   php -d pcov.enabled=On -d xdebug.mode=Off [-d  pcov.directory=<project directory>] tests/output/pcov.out.php

   php -d pcov.enabled=On -d xdebug.mode=Off -d uopz.disable=Off tests/output/pcov.out.php > tests/output/pcov.out

   !!! pcov.directory is most important to be set as root of sources files to be covered. !!!
 */

require_once dirname(__DIR__) . '/fixtures/data/Data.class.php';

try
{
   if (!isPCOVLoaded()) throw new \ErrorException("pcov is not loaded\n");
   // if (isXDebugLoadedConflict()) throw new \ErrorException("beware ! PCOV cannot run when XDebug is loaded\n");
   if (!isPCOV()) throw new \ErrorException("pcov is not started\n");
   if (!function_exists('\pcov\start')) throw new \ErrorException("pcov is unreachable\n");

   // ini_set('pcov.directory','../..'); // NOT WORKING !!! Must be set with -d option

   \pcov\start();
   print ini_get('pcov.directory') . "\n";
}
catch (\Exception $e)
{
   echo $e->getMessage();
   exit;
}

ob_start();

$path = realpath(dirname(__DIR__) . '/Configuration.test.php');
include_once $path;

register_shutdown_function(function ()
{
   while ( ob_get_level() > 0 ) { ob_end_clean(); }

   // echo "\n===\n";

   \pcov\stop();
   $waiting = \pcov\waiting();
   $collected = \pcov\collect(\pcov\inclusive, array(
      Data::CONFIGURATION,
      Data::CLIARGUMENTS
   ));
   \pcov\clear();

   echo "# waiting\n";
   var_export($waiting);
   echo "\n";

   echo "# collected\n";
   var_export($collected);
   echo "\n";
});

function isPCOVLoaded()
{
   return extension_loaded('pcov');
}

function isXDebugLoadedConflict()
{
   return extension_loaded('xdebug'); // Beware of conflict ! (FALSE : Actually, it still works fine)
}

function isPCOV()
{
   return ini_get('pcov.enabled') === '1';
}