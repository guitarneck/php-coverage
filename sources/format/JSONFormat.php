<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

class JSONFormat extends AbstractFormat
{
   protected $flags;
   protected $depth;

   function __construct ( array $params=null )
   {
      if ( ! isset($params['flags']) ) $params['flags'] = JSON_PRETTY_PRINT;
      if ( ! isset($params['depth']) ) $params['depth'] = 512;

      $this->flags = $params['flags'];
      $this->depth = $params['depth'];
   }

   function filenameFormat () : string
   {
      return '%s.json';
   }

   function render ( DataCoverage $coverage ) : string
   {
      return json_encode($coverage->getRawCoverage(), $this->flags, $this->depth);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_PARMAMETER_HEAD, Formater::class2format(__CLASS__), "[?][flags=][&][depth=]")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "flags : (int)")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "depth : (int)")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "see https://www.php.net/manual/fr/function.json-decode.php")
           . COVERAGE_FORMAT_PARMAMETER_FOOT;
   }
}