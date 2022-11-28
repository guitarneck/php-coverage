<?php
/*
   This script generate output from xdebug extension, in coverage usage.

   https://xdebug.org/

   run :
   -----
   php tests/output/xdebug.out.php

   php -d uopz.disable=false tests/output/xdebug.out.php > tests/output/xdebug.out
*/

require_once   dirname(__DIR__) . '/fixtures/data/Data.class.php';
require_once 'remap_rootdir.func.php';

try
{
   if (!isLoaded()) throw new \ErrorException("xdebug is not loaded\n");
   if (!canCoverage()) throw new \ErrorException("xdebug coverage is not enabled\n");
   if (!isReachable()) throw new \ErrorException("xdebug is unreachabled\n");
   if ( isPHPDBGConflict() ) throw new \ErrorException("phpbdg is running : conflict\n");

   xdebug_set_filter(XDEBUG_FILTER_CODE_COVERAGE,XDEBUG_PATH_INCLUDE,array(
      Data::CONFIGURATION,
      Data::CLIARGUMENTS
   ));

   xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE | XDEBUG_CC_BRANCH_CHECK);
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

   $collected = xdebug_get_code_coverage();
   xdebug_stop_code_coverage();

   $files = array(
      Data::CONFIGURATION,
      Data::CLIARGUMENTS
   );
   $collected = array_filter($collected,function ($f) use ($files)
   {
      return in_array($f,$files);
   },ARRAY_FILTER_USE_KEY);

   echo "# collected\n";
   echo remap_rootdir(Data::onlyRoot(Data::CONFIGURATION), var_export($collected, true));
   echo "\n";
});

function isLoaded(): bool
{
   return false !== extension_loaded('xdebug');
}

function canCoverage(): bool
{
   return (false !== strpos(ini_get('xdebug.mode'), 'coverage') || 1 == ini_get('xdebug.coverage_enable'));
}

function isReachable (): bool
{
   return function_exists('\xdebug_start_code_coverage');
}

function isPHPDBGConflict ():bool
{
   return php_sapi_name() === 'phpdbg';
}