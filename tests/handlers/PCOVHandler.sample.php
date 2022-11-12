<?php
// php -d pcov.enabled=on tests/handlers/PCOVHandler.sample.php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use coverage\Configuration;
use coverage\collector\DataCoverage;
use coverage\filters\Filter;
use coverage\handlers\PCOVHandler;

$coverage = new PCOVHandler(new Filter(Configuration::instance()));
if (!$coverage->isAvailable()) die("PCOV coverage cannot run :\n{$coverage->reason}");
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
      foreach ($script->lines() as $line) print "  - $line->number : $line->hit\n";;
   }
});