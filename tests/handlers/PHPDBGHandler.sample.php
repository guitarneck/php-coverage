<?php
// phpdbg.exe -qrr tests/PHPDBGHandler.test.php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use coverage\Configuration;
use coverage\collector\DataCoverage;
use coverage\filters\Filter;
use coverage\handlers\PHPDBGHandler;

$coverage = new PHPDBGHandler(new Filter(Configuration::instance()));
if (!$coverage->isAvailable()) die("PHPDBG coverage cannot run :\n{$coverage->reason}");
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
         foreach ($function->lines() as $line) print "    - {$line->number} : {$line->hit}\n";
      }
   }
});