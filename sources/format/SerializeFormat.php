<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

class SerializeFormat extends AbstractFormat
{
   function __construct ( array $params=null )
   {
   }

   function filenameFormat () : string
   {
      return '%s.xser';
   }

   function render ( DataCoverage $coverage ) : string
   {
      return serialize($coverage->getRawCoverage());
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_WITH_NO_PARAMETER, Formater::class2format(__CLASS__));
   }
}