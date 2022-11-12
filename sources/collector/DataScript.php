<?php

namespace coverage\collector;

use coverage\collector\DataFunction;
use coverage\collector\DataLines;
use coverage\collector\DataLine;

class DataScript
{
   public      $name;
   protected   $functions,
               $lines;

   function __construct ( $name )
   {
      $this->name = $name;
      $this->functions = array();
      $this->lines = new DataLines();
   }

   function addFunction ( DataFunction $function )
   {
      $this->functions[$function->name] = $function;
   }

   function addLine ( DataLine $line )
   {
      $this->lines->add($line);
   }

   function hasFunctions ()
   {
      return !empty($this->functions);
   }

   /**
    * Yield the functions.
    *
    * @return \Generator<coverage\collector\DataFunction>
    */
   function functions ()
   {
      foreach ( $this->functions as $function ) yield $function;
   }

   function hasLines ()
   {
      return !$this->lines->isEmpty();
   }

   /**
    * Yield the lines.
    *
    * @return \Generator<coverage\collector\DataLine>
    */
   function lines ()
   {
      return $this->lines->lines();
   }

   function linesHits ()
   {
      return $this->lines->hits();
   }

   function linesTop ()
   {
      return $this->lines->top();
   }

   function linesBottom ()
   {
      return $this->lines->bottom();
   }
}