<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

class ExportFormat extends AbstractFormat
{
   function __construct ( array $params = null )
   {
   }

   function filenameFormat () : string
   {
      return '%s.xexp';
   }

   function render ( DataCoverage $coverage ) : string
   {
      return var_export($coverage->getRawCoverage(), true);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_WITH_NO_PARAMETER, Formater::class2format(__CLASS__));
   }
}