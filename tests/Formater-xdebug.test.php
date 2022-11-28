<?php

namespace coverage;

require_once dirname(__DIR__) . '/vendor/guitarneck/taphp/taphp.lib.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use coverage\format\Formater;

$config = json_decode(file_get_contents(dirname(__DIR__).DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'Coverage.json'));

use coverage\handlers\XDebugHandler;
use coverage\collector\DataCoverage;
use coverage\filters\Filter;

include_once 'fixtures/data/Data.class.php';
include_once 'mocks/XDebugMock.php';

$formats = explode(',', 'export,serialize,json,dot,dump,coverage,lcov,raw,coveralls,clover');

foreach ( $formats as $fmt )
{
   test("Formater xdebug - render {$fmt}", function (\TAPHP $t) use($config, $fmt) {

      $h = new MockHandler(new Filter(new Configuration(true,array(\Data::CONFIGURATION)), false));
      $d = new DataCoverage();
      $h->coverage($d);
      $t->equal($d->handlerClassname, XDebugHandler::class);
      $formater = Formater::factory($fmt, $config->formats);

      $rendered = $formater->render($d);
      $saved = file_get_contents(__DIR__ . "/fixtures/formats/xdebug-{$fmt}.fmt");
      $length = strlen(\Data::CONFIGURATION);

      $saved = str_replace(
         array('{% TUXROOT %}', '{% WINROOT %}', '{% UNDERLINE %}', '{% STRLENGTH %}'),
         array(
            \Data::onlyRoot(\Data::CONFIGURATION),
            str_replace('\\','\\\\',\Data::onlyRoot(\Data::CONFIGURATION)),
            str_repeat('-',$length),
            $length
         ),
         $saved);

      $rendered = str_replace(
         array('{% TUXROOT %}', '{% WINROOT %}', '{% UNDERLINE %}', '{% STRLENGTH %}'),
         array(
            \Data::onlyRoot(\Data::CONFIGURATION),
            str_replace('\\','\\\\',\Data::onlyRoot(\Data::CONFIGURATION)),
            str_repeat('-',$length),
            $length
         ),
         $rendered);

      if ( $fmt === 'coveralls' )
      {
         $t->comment('Removing "run_at", because of diffs');
         $rendered = preg_replace('/"run_at":".+",/', '', $rendered);
         $saved = preg_replace('/"run_at":".+",/', '', $saved);
      }

      if ( $fmt === 'clover' )
      {
         $t->comment('Removing "generated" and "timestamp", because of diffs');
         $rendered = preg_replace(array('/ generated="\d+" /', '/ timestamp="\d+" /'), array(' ', ' '), $rendered);
         $saved = preg_replace(array('/ generated="\d+" /', '/ timestamp="\d+" /'), array(' ', ' '), $saved);
      }

      if ( $fmt === 'dot' )
      {
         $t->comment('Removing hashed tags');
         $rendered = preg_replace(array('/ cluster_file_[0-9a-f]+ /','/ cluster_[0-9a-f]+ /'),array(' cluster_file_ ',' cluster_ '), $rendered);
         $saved = preg_replace(array('/ cluster_file_[0-9a-f]+ /','/ cluster_[0-9a-f]+ /'),array(' cluster_file_ ',' cluster_ '), $saved);
      }

      $t->equal($rendered, $saved);
      $t->end();
   });
}