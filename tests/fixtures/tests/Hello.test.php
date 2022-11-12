<?php
/*
   This script is not realy a unit test, it's only purpose is
   to generate format rendering.
*/
require_once dirname(__DIR__, 3) . '/vendor/guitarneck/taphp/taphp.lib.php';

require_once (__DIR__) . '/src/Hello.class.php';
require_once (__DIR__) . '/src/Nop.class.php';
require_once (__DIR__) . '/src/dumy.lib.php';

test('Hello - byebye', function (TAPHP $t)
{
   $t->ok(Hello::byebye(), 'Bye-bye who you are !');
   $t->ok(Hello::byebye('Tony'), 'Bye-bye Tony !');
   $t->end();
});

test('Wazza - grettings', function (TAPHP $t)
{
   require_once (__DIR__) . '/src/Wazza.class.php';
   $t->equal(wazza_func(), 'Wazza who you are !');
   $t->equal(Wazza::grettings(), 'Wazza who you are !');
   $t->end();
});

test('dumy lib', function (TAPHP $t)
{
   $t->equal('yo !', yo());
   $t->end();
});