<?php

namespace coverage\collector;

use coverage\collector\DataBranches;
use coverage\collector\DataBranch;
use coverage\collector\DataLines;
use coverage\collector\DataLine;

class DataFunction
{
   const T_UNDEFINED = 0;
   const T_FUNCTION  = 1;
   const T_METHOD    = 2;
   const T_CLOSURE   = 3;

   public      $name,
               $type;

   protected   $branches,
               $lines;

   function __construct ( $name, $type = null )
   {
      $this->name = $name;
      $this->type = $type ?? DataFunction::T_UNDEFINED;
      $this->branches = new DataBranches();
      $this->lines = new DataLines();
   }

   function addBranch ( DataBranch $branch )
   {
      $this->branches->add($branch);
   }

   function addLine ( DataLine $line )
   {
      $this->lines->add($line);
   }


   function hasBranches ()
   {
      return !$this->branches->isEmpty();
   }

   function branchesHits ()
   {
      return $this->branches->hits();
   }

   /**
    * Yield the branches.
    *
    * @return \Generator<coverage\collector\DataBranch>
    */
   function branches ()
   {
      return $this->branches->branches();
   }

   function hasLines ()
   {
      return !$this->lines->isEmpty();
   }

   function linesHits ()
   {
      return $this->lines->hits();
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