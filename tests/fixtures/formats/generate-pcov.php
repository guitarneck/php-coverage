<?php

namespace coverage;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

use coverage\collector\DataCoverage;
use coverage\filters\Filter;
use coverage\format\Formater;

$config = json_decode(file_get_contents(dirname(__DIR__, 3).DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'Coverage.json'));

include_once dirname(__DIR__, 2) . '/mocks/PCOVMock.php';
include_once dirname(__DIR__) . '/data/Data.class.php';

$formats = explode(',', 'export,serialize,json,dot,dump,coverage,coveralls,lcov,clover,raw');

foreach ( $formats as $fmt )
{
   $h = new MockHandler(new Filter(new Configuration(true,array(\Data::CONFIGURATION)), false));
   $d = new DataCoverage();
   $h->coverage($d);
   $formater = Formater::factory($fmt, $config->formats);
   file_put_contents(__DIR__ . "/pcov-{$fmt}.fmt", $formater->render($d));
}