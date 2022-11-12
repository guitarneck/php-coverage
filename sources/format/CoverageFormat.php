<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

const COVERAGE_PERCENTS_SCRIPT   = "%s\n%s\n   - Lines: Hits %0u, Total: %0u (%0u%%)\n\n";
const COVERAGE_PERCENTS_FUNCTION = "   %s\n";
const COVERAGE_PERCENTS_BRANCHES = "      - Branches: Hits %0u, Total %0u (%0u%%)\n";
const COVERAGE_PERCENTS_LINES    = "      - Lines   : Hits %0u, Total %0u (%0u%%)\n";

class CoverageFormat extends AbstractFormat
{
   protected $pathInsteadOfBranch;

   function __construct ( array $params=null )
   {
   }

   function filenameFormat () : string
   {
      return '%s.xcov';
   }

   function render ( DataCoverage $coverage ) : string
   {
      $output = '';

      foreach ( $coverage->scripts() as $script )
      {
         // lines
         $nb = iterator_count($script->lines());
         $hi = $script->linesHits();
         $output .= sprintf(COVERAGE_PERCENTS_SCRIPT,
                    $script->name,
                    str_repeat('-', strlen($script->name)),
                    $hi,
                    $nb,
                    round($hi / $nb * 100, 2));

         // functions
         foreach ( $script->functions() as $function )
         {
            $output .= sprintf(COVERAGE_PERCENTS_FUNCTION, trim($function->name));

            // branches
            if ( $function->hasBranches() )
            {
               $hi = $function->branchesHits();
               $nb = iterator_count($function->branches());
               $output .= sprintf(COVERAGE_PERCENTS_BRANCHES,
                          $hi,
                          $nb,
                          round($hi / $nb * 100, 2));
            }

            // Lines
            $nb = iterator_count($function->lines());
            $hi = $function->linesHits();
            $output .= sprintf(COVERAGE_PERCENTS_LINES,
                       $hi,
                       $nb,
                       round($hi / $nb * 100, 2));
         }

         $output .= "\n";
      }

      if ( empty($output) ) $output = 'EMPTY: No coverage match the requirments. ';

      return rtrim($output);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_WITH_NO_PARAMETER, Formater::class2format(__CLASS__));
   }
}