<?php

namespace coverage;

require_once dirname(__DIR__) . '/vendor/guitarneck/taphp/taphp.lib.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use coverage\collector\DataCoverage;
use coverage\filters\Filter;

use \Data;
use \TAPHP;

require_once 'mocks/PHPDBGMock.php';

// T E S T S ---

test('PHPDBGHandler - coverage : no filter',function(TAPHP $t)
{
   $h = new MockHandler(new Filter(new Configuration(), false));
   $d = new DataCoverage();
   $h->coverage($d);

   $t->equal( count(iterator_to_array($d->scripts())), 2, 'It should have collected 2 scripts');

   $t->end();
});

test('PHPDBGHandler - coverage : with filter include',function(TAPHP $t)
{
   $h = new MockHandler(new Filter(new Configuration(true, array(Data::CONFIGURATION)), false));
   $d = new DataCoverage();
   $h->coverage($d);

   $t->equal( count(iterator_to_array($d->scripts())), 1, 'It should have included 1 scripts');

   $t->end();
});

test('PHPDBGHandler - coverage : with filter exclude',function(TAPHP $t)
{
   $h = new MockHandler(new Filter(new Configuration(true, array(), array(Data::CONFIGURATION)), false));
   $d = new DataCoverage();
   $h->coverage($d);

   $t->equal( count(iterator_to_array($d->scripts())), 1, 'It should have excluded 1 scripts');

   $t->end();
});

test('PHPDBGHandler - coverage : purged',function(TAPHP $t){
   $h = new MockHandler(new Filter(new Configuration(), true));
   $d = new DataCoverage();
   $h->coverage($d);

   $t->equal( count(iterator_to_array($d->scripts())), 0, 'It should have pruged all scripts');

   $t->end();
});