<?php
// php -d xdebug.mode=coverage tests/handlers/XDebugHandler.sample.php
// set XDEBUG_MODE=coverage && ...

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use coverage\Configuration;
use coverage\collector\DataCoverage;
use coverage\filters\Filter;
use coverage\handlers\XDebugHandler;

$coverage = new XDebugHandler(new Filter(Configuration::instance()));
if (!$coverage->isAvailable()) die("XDebug coverage cannot run :\n{$coverage->reason}");
$coverage->start();

$path = realpath(dirname(__DIR__) . '/Configuration.test.php');
include_once $path;

register_shutdown_function(function () use ($coverage)
{
   $collector = new DataCoverage();
   $coverage->stop();
   $coverage->coverage($collector);

   foreach ($collector->scripts() as $script)
   {
      print "# $script->name\n";

      foreach ($script->lines() as $line) print "  - {$line->number} : {$line->hit}\n";

      foreach ($script->functions() as $function)
      {
         print "  + {$function->name}\n";
         print "    lines:\n";
         foreach ($function->lines() as $line) print "    - {$line->number} : {$line->hit}\n";

         print "    branches:\n";
         foreach ($function->branches() as $branch)
         {
            print "    - {$branch->number} : {$branch->hit}\n";
            print "    lines:\n";
            foreach ($branch->lines() as $bline) print "      - {$bline->number} : {$bline->hit}\n";
         }
      }
   }
});