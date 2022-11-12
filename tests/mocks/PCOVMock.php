<?php

namespace coverage;

use coverage\filters\Filter;
use coverage\handlers\PCOVHandler;

include_once dirname(__DIR__) . '/fixtures/data/pcov.data.php';
use function data_import;

require_once 'Configuration!Fake!.php';

// Mock PCOVHandler
class MockHandler extends PCOVHandler
{
   function __construct(Filter $filter)
   {
      $this->filter = $filter;
      $this->datas = data_import();
   }
}
