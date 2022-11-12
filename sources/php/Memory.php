<?php

namespace coverage\php;

use coverage\php\Process;

class Memory
{
   const UNLIMITED = -1;

   protected   $bytes;

   function __construct ( $bytes )
   {
      $this->bytes = $bytes;
   }

   function __toString ()
   {
      static $units = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
      $expo = floor(log($this->bytes, 1024));
      $frac = $this->bytes / pow(1024, $expo);
      return sprintf('%.2f %s', $frac, $units[$expo]);
   }

   static
   function usage ( $real_usage = false )
   {
      return new Memory(memory_get_usage($real_usage));
   }

   static
   function peakUsage ( $real_usage = false )
   {
      return new Memory(memory_get_peak_usage($real_usage));
   }

   static
   function limit ()
   {
      return new Memory(static::toBytes(ini_get('memory_limit')));
   }

   /*
      Before PHP 5.2.1, this only works if PHP is compiled with --enable-memory-limit.
      From PHP 5.2.1 and later this function is always available.
   */
   static protected
   function isMemoryLimitAvailable () : bool
   {
      if ( ! version_compare(Process::version(), '5.2.1', '>') )
      {
         ob_start();
         phpinfo(INFO_GENERAL);
         $out = ob_get_clean();
         preg_match('/^Configure Command =>(.*)$/m', $out, $matches);
         return strpos($matches[0], '--enable-memory-limit') !== false;
      }

      return true;
   }

   static protected
   function toBytes ( $string )
   {
      static $units = array('K' => 1, 'M' => 2, 'G' => 3, 'T' => 4, 'P' => 5, 'E' => 6, 'Z' => 7, 'Y' => 8);
      $unit = strtoupper(substr(rtrim($string, 'bB'), -1));
      return ((int) $string) * pow(1024, isset($units[$unit]) ? $units[$unit] : 0);
   }
}