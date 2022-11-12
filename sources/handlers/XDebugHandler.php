<?php

namespace coverage\handlers;

use coverage\handlers\AbstractHandler;

use coverage\filters\Filter;

use coverage\collector\DataCoverage;
use coverage\collector\DataScript;
use coverage\collector\DataFunction;
use coverage\collector\DataBranch;
use coverage\collector\DataLine;

if ( defined('XDEBUG_PATH_BLACKLIST') ):
   define('COVERAGE_PATH_INCLUDE', XDEBUG_PATH_WHITELIST);
   define('COVERAGE_PATH_EXCLUDE', XDEBUG_PATH_BLACKLIST);
endif;

if ( defined('XDEBUG_PATH_EXCLUDE') ):
   define('COVERAGE_PATH_INCLUDE', XDEBUG_PATH_INCLUDE);
   define('COVERAGE_PATH_EXCLUDE', XDEBUG_PATH_EXCLUDE);
endif;

// Thoose lines only to not crash when xdebug is missing
if ( ! defined('XDEBUG_FILTER_CODE_COVERAGE') ):
   define('COVERAGE_PATH_INCLUDE', 1);
   define('COVERAGE_PATH_EXCLUDE', 2);
   define('XDEBUG_FILTER_CODE_COVERAGE', 256);
   define('XDEBUG_CC_UNUSED', 1);
   define('XDEBUG_CC_DEAD_CODE', 2);
   define('XDEBUG_CC_BRANCH_CHECK', 4);
endif;

const HANDLER_XDEBUG_SKIP_MAIN = true;

const LINE_WAS_EXECUTED =  1; // this line was executed
const LINE_NOT_EXECUTED = -1; // this line was not executed
const LINE_UNEXECUTABLE = -2; // this line did not have executable code on it

const BRANCHE_OPCODE_EXIT = 2147483645;

class XDebugHandler extends AbstractHandler
{
   protected   $filter,
               $datas;

   function __construct ( Filter $filter )
   {
      $this->filter = $filter;
      $this->datas = array();
   }

   function isAvailable () : bool
   {
      $this->reason = '';

      if ( ! $this->isLoaded() )
      {
         $this->reason .= "xdebug is not loaded\n";
         return false;
      }

      if ( ! $this->canCoverage() ) $this->reason .= "xdebug coverage is not enable\n";
      if ( ! $this->isReachable() ) $this->reason .= "xdebug is unreachable\n";
      if ( $this->isPHPDBGConflict() ) $this->reason .= "phpbdg is running : conflict\n";

      return $this->reason === '';
   }

   function start () : bool
   {
      list($includes, $excludes) = $this->filter->intraFilter();

      if ( $includes )
         xdebug_set_filter(
            XDEBUG_FILTER_CODE_COVERAGE,
            COVERAGE_PATH_INCLUDE,
            $includes
         );
      elseif ( $excludes )
         xdebug_set_filter(
            XDEBUG_FILTER_CODE_COVERAGE,
            COVERAGE_PATH_EXCLUDE,
            $excludes
         );

      xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE | XDEBUG_CC_BRANCH_CHECK);
      return $this->hasStarted();
   }

   function stop () : bool
   {
      $this->datas = xdebug_get_code_coverage();
      xdebug_stop_code_coverage();

      return !$this->hasStarted();
   }

   function coverage ( DataCoverage $collector ) : void
   {
      $collector->handlerClassname = __CLASS__;
      $collector->setRawConverage(function () {
         return $this->purgedDatas();
      });

      $datas = $this->purgedDatas();

      foreach ( $datas as $sname => $sinfo )
      {
         if ( empty($sinfo['functions']) ) continue;
         $script = new DataScript($sname);

         if ( HANDLER_XDEBUG_SKIP_MAIN ) array_pop($sinfo['lines']); // extract main line, the last one.

         $lines = array_filter($sinfo['lines'], function ($v)
         {
            return $v !== LINE_UNEXECUTABLE;
         });
         $hits = array_fill_keys(array_keys($lines), 0);

         $fmin = \PHP_INT_MAX;
         $fmax = \PHP_INT_MIN;
         $this->sortLinesFunctions($sinfo['functions']);
         foreach ( $sinfo['functions'] as $fname => $finfo )
         {
            if ( HANDLER_XDEBUG_SKIP_MAIN && $this->isMain($fname) ) continue;
            $lines    = array();
            $type     = $this->getDataFunctionType($fname);
            $function = new DataFunction($fname, $type);

            foreach ( $finfo['branches'] as $bnr => $binfo )
            {
               $branch = new DataBranch($bnr, $binfo['hit']);
               for ( $i = $binfo['line_start'] ; $i <= $binfo['line_end'] ; $i++ )
               {
                  if ($i < $fmin) $fmin = $i;
                  if ($i > $fmax) $fmax = $i;

                  $branch->addLine(new DataLine($i, $binfo['hit']));
                  $lines[$i] = ($lines[$i] ?? 0) + $binfo['hit'];
                  $hits[$i] = ($hits[$i] ?? 0) + $binfo['hit'];

                  // if (!isset($hits[$i])) $hits[$i] = 0;
                  // $hits[$i] += $binfo['hit'];
               }
               $function->addBranch($branch);
            }

            ksort($lines);
            foreach ( $lines as $num => $hit ) $function->addLine(new DataLine($num, $hit));

            $script->addFunction($function);
         }

         ksort($hits);
         foreach ( $hits as $line => $hcount )
         {
            if ( $line < $fmin || $line > $fmax ) continue; // only lines covered by a branch
            $script->addLine(new DataLine($line, $hcount));
         }

         $collector->add($script);
      }
   }

   public
   function purgedDatas ()
   {
      return $this->filter->extraFilter($this->filter->purge($this->datas));
   }

   protected
   function mainLine ( array $lines ) : int
   {
      $lnbrs = array_keys($lines);
      return array_pop($lnbrs);
   }

   protected
   function getDataFunctionType ( string & $name )
   {
      if ( strpos($name,'->') !== false )
         return DataFunction::T_METHOD;
      elseif ( strpos($name, '{closure:') !== false )
      {
         $pos  = strpos($name, '{closure:');
         $name = substr($name, $pos, strlen('{closure')) . substr($name, strrpos($name, ':'));
         return DataFunction::T_CLOSURE;
      }
      else
         return DataFunction::T_FUNCTION;
   }

   protected
   function isMain ( string $fname ) : bool
   {
      return $fname === '{main}';
   }

   protected
   function xdebugVersion () : string
   {
      return phpversion('xdebug');
   }

   protected
   function hasStarted () : bool
   {
      return xdebug_code_coverage_started();
   }

   protected
   function isLoaded () : bool
   {
      return extension_loaded('xdebug');
   }

   protected
   function canCoverage () : bool
   {
      return strpos(ini_get('xdebug.mode'), 'coverage') !== false || ini_get('xdebug.coverage_enable') === true;
   }

   protected
   function isReachable () : bool
   {
      return function_exists('\xdebug_start_code_coverage');
   }

   protected
   function isPHPDBGConflict () : bool
   {
      return php_sapi_name() === 'phpdbg';
   }
   // ascending sorting of integers
   private
   function cmpasc ( $a, $b ) : int
   {
      return ($a > $b) - ($a < $b);
   }

   /**
    * Sort the branches of the functions by line number.
    *
    * @param arrays &$functions[]   The function informations to sort.
    * @return void
    */
   protected
   function sortLinesFunctions ( & $functions ) : void
   {
      uasort( $functions, function ($a, $b) {
         return $this->cmpasc($a['branches'][0]['line_start'], $b['branches'][0]['line_start']);
      });
   }
}