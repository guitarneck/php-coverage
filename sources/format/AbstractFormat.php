<?php
namespace coverage\format;

use coverage\collector\DataCoverage;

abstract
class AbstractFormat
{
   /**
    * The formater constructor.
    *
    * @param array $params    Optional parameters for the formater.
    */
   abstract
   function __construct ( array $params = null );

   /**
    * Retrieve the filename, according to the sprintf format.
    *
    * @return string The filename format, such '%s.ext'.
    * @see sprintf()
    */
   abstract
   function filenameFormat () : string;

   /**
    * Retrieve formatted test name file and datas.
    *
    * @param DataCoverage $coverage     A DataCoverage object.
    * @return string The formatted datas.
    */
   abstract
   function render ( DataCoverage $coverage ) : string;

   /**
    * Retrieve user help options, according to the format requirements.
    * @return string    The user help options.
    */
   abstract static
   function help () : string;
}