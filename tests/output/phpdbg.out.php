<?php
/*
   This script generate output from phpdbg tool software.

   https://github.com/php/php-src/tree/master/sapi/phpdbg

   run :
   -----
   phpdbg.exe -qrr tests/output/phpdbg.out.php

   phpdbg -qrr -d uopz.disable=false tests/output/phpdbg.out.php > tests/output/phpdbg.out
*/

require_once   dirname(__DIR__) . '/fixtures/data/Data.class.php';

try
{
   if (!isPHPDBG()) throw new \ErrorException("phpdbg is not running\n");
   if (isPCOVConflict()) throw new \ErrorException("pcov.enabled is 'On' = conflict\n");
   if (!function_exists('phpdbg_start_oplog')) throw new \ErrorException("phpdbg is unreachable\n");
   @phpdbg_start_oplog();
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

   $files = array(
      Data::CONFIGURATION,
      Data::CLIARGUMENTS
   );

   $executedLines = phpdbg_end_oplog(["functions" => false, "opcodes" => false]); // QUE les lignes exécutées !
   $executedLines = array_filter($executedLines,function ($f) use ($files)
   {
      return in_array($f,$files);
   },ARRAY_FILTER_USE_KEY);
   echo "# oplog\n";
   var_export($executedLines);
   echo "\n";

   $executableLines = phpdbg_get_executable(["functions" => true, "opcodes" => false, "files" => $files]); // Les lignes executables, et les functions (qui ne respectent pas la casse !!!)
   echo "#  executable\n";
   var_export($executableLines);
   echo "\n";
});

function isPHPDBG()
{
   return php_sapi_name() === 'phpdbg';
}

function isPCOVConflict()
{
   return extension_loaded('pcov') && true == ini_get('pcov.enabled'); // Beware of conflict !
}