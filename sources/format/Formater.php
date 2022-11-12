<?php
namespace coverage\format;

use coverage\php\Process;
use coverage\format\AbstractFormat;

const COVERAGE_FORMATER_TYPE_NOT_FOUND  = "\n\e[31m[ERROR]\e[0m \e[1;30mformater not found:\e[0m \e[31m%s\e[0m\n";
const COVERAGE_FORMATER_CLASS_NOT_FOUND = "\n\e[31m[ERROR]\e[0m \e[1;30mformater class not found.\e[0m\n";

const COVERAGE_FORMAT_WITH_NO_PARAMETER = "\n   \e[1m%s\e[0m format required no parameter.\n";
const COVERAGE_FORMAT_PARMAMETER_HEAD   = "\n   \e[4mFormat parameters\e[0m\n\n   \e[1m%s\e[0m\e[37m%s\e[0m";
const COVERAGE_FORMAT_PARMAMETER_PARM   = "\n      - %s";
const COVERAGE_FORMAT_PARMAMETER_FOOT   = "\n";

class Formater
{
   protected   $format,
               $params;

   function __construct ( string $format )
   {
      list($this->format, $this->params) = self::parse_str($format);
   }

   /**
    * Retrieve a formater object.
    *
    * @param string $format      The format to use.
    * @param \stdClass $formats  The formats configuration.
    * @return AbstractFormat|null     The formater object.
    */
   static
   function factory ( string $format, \stdClass $formats ): ?AbstractFormat
   {
      $formater = new Formater($format);
      list($script, $class) = $formater->findOrDie($format, $formats);

      @include_once $script;
      $class = __NAMESPACE__ . '\\' . $class;
      if ( class_exists($class) )
         return new $class($formater->params);
      else
         return null;
   }

   /**
    * Print the help page of the given format.
    *
    * @param string $format      The format to use.
    * @param \stdClass $formats  The formats configuration.
    */
   static
   function help ( string $format, \stdClass $formats ): void
   {
      $formater = new Formater($format);
      list($script,$class) = $formater->findOrDie($format, $formats);

      if ( $script === null ) return;

      @include_once $script;
      $class = __NAMESPACE__ . '\\' . $class;
      if ( class_exists($class) )
         print $class::help();
      else
         print '';
   }

   /**
    * Parse the format string.
    *
    * @param string $format      The format to use.
    * @return array<string,array>  An array of format name and the array key/value parameters.
    */
   static
   function parse_str ( string $format ) : array
   {
      $params = null;

      if ( ($pos = mb_strpos($format, '?')) !== false )
      {
         parse_str(mb_substr($format, $pos+1), $params);
         $format = mb_substr($format, 0, $pos);
      }
      return array($format, $params);
   }

   /**
    * Retrieve the script name and the class according to a format.
    *
    * @param string $format      The format to use.
    * @param \stdClass $formats  The formats configuration.
    * @return array<string|null,string|null> An array with the script name and the class name, or null.
    */
   function findOrDie ( string $format, \stdClass $formats ) : array
   {
      if ( ! $this->inFormats($formats) )
      {
         Process::terminate(sprintf(COVERAGE_FORMATER_TYPE_NOT_FOUND, $format));
         return array(null, null);
      }

      $class = $this->formaterClass($formats);
      $script  = $this->formaterScript($class);
      if ( ! file_exists($script) )
      {
         Process::terminate(COVERAGE_FORMATER_CLASS_NOT_FOUND);
         return array(null, null);
      }

      return array($script, $class);
   }

   function isValid ( \stdClass $formats ) : bool
   {
      if ( !$this->inFormats($formats) ) return false;

      $class   = $this->formaterClass($formats);
      $script  = $this->formaterScript($class);
      return file_exists($script);
   }

   function inFormats ( \stdClass $formats ) : bool
   {
      return isset($formats->{$this->format});
   }

   function formaterClass ( \stdClass $formats ) : string
   {
      return sprintf('%sFormat', $formats->{$this->format});
   }

   function formaterScript ( string $className ) : string
   {
      return sprintf('%s%s%s.php', __DIR__, DIRECTORY_SEPARATOR, $className);
   }

   static
   function class2format ( string $classname ) : string
   {
      return strtolower(substr(str_replace('Format', '', $classname), strlen(__NAMESPACE__) + 1));
   }
}