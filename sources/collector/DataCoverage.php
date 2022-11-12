<?php

namespace coverage\collector;

use coverage\collector\DataScript;

class DataCoverage
{
   protected   $scripts,
               $rawCoverage;

   public      $handlerClassname;

   function __construct ()
   {
      $this->scripts = array();
   }

   function add ( DataScript $script )
   {
      $this->scripts[$script->name] = $script;
   }

   function hasScripts ()
   {
      return !empty($this->scripts);
   }

   /**
    * Yield the scripts.
    *
    * @return \Generator<coverage\collector\DataScript> | DataScript[]
    */
   function scripts () : \Generator
   {
      ksort($this->scripts);
      foreach ( $this->scripts as $script ) yield $script;
   }

   /**
    * Set a function that returns the raw coverage, but purged, datas coverage collected.
    * @param callable $rawCoverage  The function that retrieve the datas collected.
    */
   function setRawConverage ( callable $rawCoverage ) : void
   {
      $this->rawCoverage = $rawCoverage;
   }

   /**
    * Call the raw datas coolected function, and retrieve its result.
    * @return array  The raw datas collected by the handler.
    */
   function getRawCoverage () : array
   {
      return call_user_func($this->rawCoverage);
   }
}