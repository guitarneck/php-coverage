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
require_once 'remap_rootdir.func.php';

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
   echo remap_rootdir(Data::onlyRoot(Data::CONFIGURATION), var_export($executedLines, true));
   echo "\n";

   $executableLines = phpdbg_get_executable(["functions" => true, "opcodes" => false, "files" => $files]); // Les lignes executables, et les functions (qui ne respectent pas la casse !!!)
   trimExecutables($executableLines);
   echo "#  executable\n";
   echo remap_rootdir(Data::onlyRoot(Data::CONFIGURATION), var_export($executableLines, true));
   echo "\n";
});

function trimExecutables ( & $executables )
{
   foreach ( $executables as $s => $i )
      $executables[$s] = array_combine(array_map('trim', array_keys($i)), $i);
}

function isPHPDBG()
{
   return php_sapi_name() === 'phpdbg';
}

function isPCOVConflict()
{
   return extension_loaded('pcov') && true == ini_get('pcov.enabled'); // Beware of conflict !
}