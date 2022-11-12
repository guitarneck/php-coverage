<?php

namespace coverage\collector;

use coverage\collector\DataLine;

class DataLines
{
   protected   $lines,
               $hits;

   protected   $min,
               $max;

   function __construct ()
   {
      $this->lines = array();
      $this->hits = 0;

      $this->min = PHP_INT_MAX;
      $this->max = PHP_INT_MIN;
   }

   function add ( DataLine $line ) : void
   {
      if ( $this->min > $line->number ) $this->min = $line->number;
      if ( $this->max < $line->number ) $this->max = $line->number;

      $this->lines[$line->number] = $line;
      $this->hits += $line->hit;
   }

   function isEmpty () : bool
   {
      return empty($this->lines);
   }

   /**
    * Yield the lines.
    *
    * @return \Generator<coverage\collector\DataLine>
    */
   function lines () : \Generator
   {
      foreach ($this->lines as $line) yield $line;
   }

   function hits () : int
   {
      return $this->hits;
   }

   function top () : int
   {
      return $this->min;
   }

   function bottom () : int
   {
      return $this->max;
   }
}