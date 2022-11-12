<?php

namespace coverage\php;

class Process
{
   static $debug = false;

   /**
    * Terminate the process.
    *
    * @param integer|string $status    Optional exit status.
    * @return void
    */
   static
   function terminate ( $status = 0 )
   {
      if ( static::$debug )
         print $status;
      else
         exit($status);
   }

   static
   function version ()
   {
      return phpversion();
   }

   static
   function binary ()
   {
      return PHP_BINARY;
   }

   static
   function isCli ()
   {
      return (substr(php_sapi_name(), 0, 3) === 'cli' || php_sapi_name() === 'phpdbg') && empty($_SERVER['DOCUMENT_ROOT']);
   }
}