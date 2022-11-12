<?php

namespace coverage\collector;

class DataLine
{
   public      $number,
               $hit;

   function __construct ( $number, $hit = 0 )
   {
      $this->number = $number;
      $this->hit = $hit;
   }
}