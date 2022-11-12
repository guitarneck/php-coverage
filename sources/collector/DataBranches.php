<?php

namespace coverage\collector;

use coverage\collector\DataBranch;

class DataBranches
{
   protected   $branches,
               $hits;

   function __construct ()
   {
      $this->branches = array();
      $this->hits = 0;
   }

   function add ( DataBranch $branch )
   {
      $this->branches[$branch->number] = $branch;
      $this->hits += $branch->hit;
   }

   function isEmpty ()
   {
      return empty($this->branches);
   }

   function hits ()
   {
      return $this->hits;
   }

   /**
    * Yield the branches.
    *
    * @return \Generator<coverage\collector\DataBranch>
    */
   function branches () : \Generator
   {
      foreach ( $this->branches as $branch ) yield $branch;
   }
}