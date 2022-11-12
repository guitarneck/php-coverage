<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

class RawFormat extends AbstractFormat
{
   protected $options = array();

   function __construct ( array $params=null )
   {
      $this->options = array();
   }

   function filenameFormat () : string
   {
      return '%s.xraw';
   }

   function render ( DataCoverage $coverage ) : string
   {
      $output = '';

      foreach ( $coverage->scripts() as $script )
      {

         $output .= "file: {$script->name}\n";

         $count = 0;
         foreach ( $script->lines() as $line )
         {
            $count++;
            $output .= "line: {$line->number}, hits: {$line->hit}\n";
         }
         $hits = $script->linesHits();
         $output .= "hits: {$hits}/{$count}\n";

         foreach ( $script->functions() as $function )
         {
            $output .= "function: {$function->name}\n";

            if ( $function->hasBranches() )
            {
               $count = 0;
               $output .= "Branches:\n";
               foreach ( $function->branches() as $branch )
               {
                  $count++;
                  $output .= "branch: {$branch->number}, hit: {$branch->hit}\n";
                  foreach ( $branch->lines() as $line )
                  {
                     $output .= "line: {$line->number}, hits: {$line->hit}\n";
                  }
               }
               $hits = $function->branchesHits();
               $output .= "hits: {$hits}/{$count}\n";
            }

            if ( $function->hasLines() )
            {
               $count = 0;
               $output .= "Lines:\n";
               foreach ( $function->lines() as $line )
               {
                  $count++;
                  $output .= "line: {$line->number}, hits: {$line->hit}\n";
               }
               $hits = $function->linesHits();
               $output .= "hits: {$hits}/{$count}\n";
            }
         }
      }

      return rtrim($output);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_WITH_NO_PARAMETER, Formater::class2format(__CLASS__));
   }
}