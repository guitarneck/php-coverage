<?php

namespace coverage\filters;

use coverage\Configuration;

class Filter
{
   protected   $config,
               $purge;

   function __construct ( Configuration $configuration, bool $purge = true )
   {
      $this->config = $configuration;
      $this->purge  = $purge;
   }

   /**
    * Purge this framework path from the datas collected.
    *
    * @param array $datas  The datas to purge.
    * @return array  The purged datas
    */
   function purge ( array $datas ) : array
   {
      return $this->purge ? $this->exclude($datas, array(dirname(__DIR__))) : $datas;
   }

   /**
    * Retrive includes and excludes arrays.
    *
    * @return array<array,array> The includes and exludes arrays.
    */
   // return [includes, excludes]
   function intraFilter () : array
   {
      $config = $this->config;
      return array($config->includes, $config->excludes);
   }

   /**
    * Filter datas with includes and excludes paths.
    *
    * @param array $datas  The datas to filter.
    * @return array  The filtered datas.
    */
   function extraFilter ( array $datas ) : array
   {
      $config = $this->config;

      if ($config->noExtraFilter) return $datas;

      if ($config->includes)
         $datas = $this->include($datas, $config->includes);

      if ($config->excludes)
         $datas = $this->exclude($datas, $config->excludes);

      return $datas;
   }

   protected
   function include ( array $datas, array $includes ) : array
   {
      return array_filter($datas, function ($k) use ($includes)
      {
         return $this->contains($k, $includes);
      }, ARRAY_FILTER_USE_KEY);
   }

   protected
   function exclude ( array $datas, array $excludes ) : array
   {
      return array_filter($datas, function ($k) use ($excludes)
      {
         return !$this->contains($k, $excludes);
      }, ARRAY_FILTER_USE_KEY);
   }

   protected
   function contains ( string $needle, array $haystack ) : bool
   {
      $found = false;
      foreach ( $haystack as $hay )
      {
         $found = ($hay === substr($needle, 0, strlen($hay)) );
         if ( $found ) break;
      }
      return $found;
   }
}