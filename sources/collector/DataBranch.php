<?php

namespace coverage\collector;

use coverage\collector\DataLines;
use coverage\collector\DataLine;

class DataBranch
{
   public      $number,
               $hit;

   protected   $lines;

   function __construct ( $number, $hit = 0 )
   {
      $this->number = $number;
      $this->hit = $hit;
      $this->lines = new DataLines();
   }

   function addLine ( DataLine $line )
   {
      $this->lines->add($line);
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
}
