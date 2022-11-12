<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

const COVERAGE_LCOV_SKIP_MAIN = true;

class LCOVFormat extends AbstractFormat
{
   protected $options = array();

   function __construct ( array $params = null )
   {
      if (!isset($params['name'])) $params['name'] = ''; // What when there's no testing name ?

      $this->options['name'] = $params['name'];
   }

   function filenameFormat () : string
   {
      return '%s_lcov.info';
   }

   function render ( DataCoverage $coverage ) : string
   {
      $output = sprintf("TN:%s\n", $this->options['name']); // Testing Name

      foreach ( $coverage->scripts() as $script )
      {
         $output .= sprintf("SF:%s\n", $script->name); // Script File

         $functions = array();
         $branches  = array();
         $func_hits = array();

         // functions
         foreach ( $script->functions() as $function )
         {

            foreach ( $function->lines() as $line )
            {
               $line_start = $line->number;
               break;
            }

            $functions[] = sprintf("FN:%u,%s", $line_start, $function->name); // Function

            foreach ( $function->branches() as $branch )
            {
               if ( isset($func_hits[$function->name]) )
                  $func_hits[$function->name] += $branch->hit;
               else
                  $func_hits[$function->name] = $branch->hit;

               foreach ( $branch->lines() as $line )
               {
                  $branches[] = sprintf("BRDA:%u,%s,%u,%s", $line->number, $line_start, $branch->number, $branch->hit ? '1' : '-'); // Branch Data
               }
            }
         }

         if ( count($functions) > 0 )
         {
            $output .= implode("\n", $functions) . "\n";
            foreach ( $func_hits as $fname => $calls ) $output .= sprintf("FNDA:%u,%s\n", $calls, $fname); // Function Data

            $output .= sprintf("FNF:%u\n", count($functions)); // Function Found
            $output .= sprintf("FNH:%u\n", count(array_filter($func_hits, function ($v) { return $v > 0; }))); // Function Hits
         }

         if ( count($branches) > 0 )
         {
            $output .= implode("\n", $branches) . "\n";
            $output .= sprintf("BRF:%u\n", count($branches)); // Branch found
            $output .= sprintf("BRH:%u\n", count(array_filter($branches, function ($v) { return '-' !== substr($v, -1); }))); // Branch Hit
         }

         $count = 0;
         foreach ( $script->lines() as $line )
         {
            $output .= sprintf("DA:%u,%u\n", $line->number, $line->hit); // Script Data
            $count++;
         }

         $output .= sprintf("LH:%u\n", $script->linesHits()); // Line hit
         $output .= sprintf("LF:%u\n", $count); // Line Found

         $output .= "end_of_record\n";
      }

      return rtrim($output);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_PARMAMETER_HEAD, Formater::class2format(__CLASS__), "[?][name=]")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "name: (string) A testname to store in the lcov.")
           . COVERAGE_FORMAT_PARMAMETER_FOOT;
   }
}