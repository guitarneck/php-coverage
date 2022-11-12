<?php

namespace coverage;

use coverage\filters\Filter;
use coverage\handlers\PHPDBGHandler;

include_once dirname(__DIR__) . '/fixtures/data/phpdbg.data.php';
use function data_import;

require_once 'Configuration!Fake!.php';

// Mock PHPDBGHandler
class MockHandler extends PHPDBGHandler
{
   function __construct (Filter $filter)
   {
      $this->filter = $filter;
      list($this->executables,$this->executed) = data_import();
   }

   protected
   function userClasses(): array
   {
      return array(
         'coverage\\php\\process' => 'coverage\\php\\Process',
         'coverage\\configuration' => 'coverage\\Configuration'
      );
   }
}