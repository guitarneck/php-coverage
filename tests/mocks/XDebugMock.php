<?php

namespace coverage;

use coverage\filters\Filter;
use coverage\handlers\XDebugHandler;

include_once dirname(__DIR__) . '/fixtures/data/xdebug.data.php';
use function data_import;

require_once 'Configuration!Fake!.php';

// Mock XDebugHandler
class MockHandler extends XDebugHandler
{
   function __construct(Filter $filter)
   {
      $this->filter = $filter;
      $this->datas = data_import();
   }
}
