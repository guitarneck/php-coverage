<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;
use coverage\php\Process;

const COVERAGE_DUMP_FORMATER_REQUIRE_XDEBUG = "\n\e[31m[ERROR]\e[0m \e[1;30mXDebugHandler is required for the dump format to works.\e[0m\n";

class DumpFormat extends AbstractFormat
{
   protected $pathInsteadOfBranch;

   function __construct ( array $params = null )
   {
   }

   function filenameFormat () : string
   {
      return '%s.dmp';
   }

   function render ( DataCoverage $coverage ) : string
   {
      if ( !$coverage->handlerClassname === 'XDebugHandler')
         Process::terminate(COVERAGE_DUMP_FORMATER_REQUIRE_XDEBUG);

      include_once 'contribs/dump_branch_coverage.php';
      ob_start();
      dump_branch_coverage($coverage->getRawCoverage());
      return rtrim(ob_get_clean());
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_WITH_NO_PARAMETER, Formater::class2format(__CLASS__));
   }
}