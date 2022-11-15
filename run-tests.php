<?php

use coverage\php\System;

require_once __DIR__ . '/vendor/guitarneck/taphp/taphp.lib.php';
require_once __DIR__ . '/sources/php/System.php';

function TrapOutput ( $filename, $args )
{
   $php     = PHP_BINARY;
   $tstdir  = __DIR__ . '/tests';
   $nul     = System::devnul();
   ob_start();
   system("$php $args -f $tstdir/$filename.test.php 2>$nul");
   return ob_get_clean();
}

echo 'This should takes a couple of seconds...',TAP_EOL;

$all = [
   'Configuration'   => ['pass' => 31, 'args' => '-d uopz.disable=Off'],
   'Formater'        => ['pass' =>  7, 'args' => '-d uopz.disable=Off'],
   'Formater-xdebug' => ['pass' => 20, 'args' => ''],
   'Formater-phpdbg' => ['pass' => 20, 'args' => ''],
   'Formater-pcov'   => ['pass' => 20, 'args' => ''],
   'PCOVHandler'     => ['pass' =>  4, 'args' => ''],
   'PHPDBGHandler'   => ['pass' =>  4, 'args' => ''],
   'XDebugHandler'   => ['pass' =>  4, 'args' => ''],
];

foreach ( $all as $name => $opts )
{
   test("testing : $name", function (TAPHP $t) use ($name, $opts) {
         $output = TrapOutput($name, $opts['args']);
         $t->ok(strpos($output,'TAP version 13') !== false, 'TAPHP has runned');
         $t->ok(strpos($output,"\n# pass  {$opts['pass']}\n") !== false);
         $t->end();
   });
}