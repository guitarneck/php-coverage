<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;
use coverage\php\Process;

const COVERAGE_DOT_FORMATER_REQUIRE_XDEBUG = "\n\e[31m[ERROR]\e[0m \e[1;30mXDebugHandler is required for the dot format to works.\e[0m\n";

class DotFormat extends AbstractFormat
{
   protected $pathInsteadOfBranch;

   function __construct ( array $params = null )
   {
      if (!isset($params['pathInsteadOfBranch'])) $params['pathInsteadOfBranch'] = true;

      $this->pathInsteadOfBranch = $params['pathInsteadOfBranch'];
   }

   function filenameFormat () : string
   {
      return '%s.dot';
   }

   function render ( DataCoverage $coverage ) : string
   {
      if ( !$coverage->handlerClassname === 'XDebugHandler')
         Process::terminate(COVERAGE_DOT_FORMATER_REQUIRE_XDEBUG);

      include 'contribs/branch_coverage_to_dot.php';
      return rtrim(branch_coverage_to_dot($coverage->getRawCoverage(), $this->pathInsteadOfBranch));
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_PARMAMETER_HEAD, Formater::class2format(__CLASS__), "[?][pathInsteadOfBranch]")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "pathInsteadOfBranch : (boolean) Use paths, not branches.")
           . COVERAGE_FORMAT_PARMAMETER_FOOT;
   }
}