<?php

namespace coverage;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use coverage\collector\DataCoverage;
use coverage\filters\Filter;
use coverage\format\Formater;

$config = json_decode(file_get_contents(dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'Coverage.json'));

include_once dirname(__DIR__, 2) . '/mocks/XDebugMock.php';
include_once dirname(__DIR__) . '/data/Data.class.php';

$formats = explode(',', 'export,serialize,json,dot,dump,coverage,coveralls,lcov,clover,raw');

foreach ( $formats as $fmt )
{
   $h = new MockHandler(new Filter(new Configuration(true,array(\Data::CONFIGURATION)), false));
   $d = new DataCoverage();
   $h->coverage($d);
   $formater = Formater::factory($fmt, $config->formats);
   $output = $formater->render($d);
   $output = str_replace(
      array(\Data::onlyRoot(\Data::CONFIGURATION),str_replace('\\','\\\\',\Data::onlyRoot(\Data::CONFIGURATION))),
      array('{% TUXROOT %}','{% WINROOT %}'),
      $output);
   file_put_contents(__DIR__ . "/xdebug-{$fmt}.fmt", $output);
}