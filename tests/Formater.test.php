<?php

namespace Namespace_declaration_statement_has_to_be_the_very_first_statement_suks;

   require_once 'includes/opcache-disable.inc.php';
   require_once 'includes/uopz-requirement.inc.php';
   require_once 'mocks/MockProcess.php';

namespace coverage;

   require_once dirname(__DIR__) . '/vendor/guitarneck/taphp/taphp.lib.php';
   require_once dirname(__DIR__) . '/vendor/autoload.php';

   use coverage\format\Formater;

   $config = json_decode(file_get_contents(dirname(__DIR__).DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'Coverage.json'));

   test('Formater - Factory', function (\TAPHP $t) use($config) {
      ob_start(); // trap output error
      $fmt = Formater::factory('unexisting', $config->formats);
      ob_end_clean();
      $t->ok( $fmt === null );
      $t->ok( is_a(Formater::factory('raw', $config->formats),'coverage\format\RawFormat') );
      $t->end();
   });

   test('Formater - parse_str', function (\TAPHP $t) {
      list($name,$parms) = Formater::parse_str('fmt?a=1&b=2');
      $t->equal($name,'fmt');
      $t->ok(is_array($parms));
      $t->ok(array_key_exists('a',$parms));
      $t->ok(array_key_exists('b',$parms));
      $t->deep_equal($parms,array('a'=>'1','b'=>'2'));
      $t->end();
   });