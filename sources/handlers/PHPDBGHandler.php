<?php

namespace coverage\handlers;

use coverage\handlers\AbstractHandler;

use coverage\filters\Filter;

use coverage\collector\DataCoverage;
use coverage\collector\DataScript;
use coverage\collector\DataFunction;
use coverage\collector\DataLine;

class PHPDBGHandler extends AbstractHandler
{

   protected   $filter,
               $executed,
               $executables;

   function __construct ( Filter $filter )
   {
      $this->filter = $filter;
      $this->executed = array();
      $this->executables = array();
   }

   function isAvailable () : bool
   {
      $this->reason = '';

      if ( ! $this->isLoaded() )
      {
         $this->reason .= "phpdbg is not running\n";
         return false;
      }

      if ( ! $this->isReachable() ) $this->reason .= "phpdbg is unreachable\n";
      if ( $this->isPCOVConflict() ) $this->reason .= "pcov found = conflict\n";

      return $this->reason === '';
   }

   function start () : bool
   {
      phpdbg_start_oplog();
      return true;
   }

   function stop () : bool
   {
      // line => opcodes execution count
      $this->executed = phpdbg_end_oplog(array("functions" => false, "opcodes" => false));

      $options = array("functions" => true, "opcodes" => false/*, "files" => $files*/);
      list($files,) = $this->filter->intraFilter();
      if ( ! empty($files) ) $options['files'] = $files;
      $this->executables = phpdbg_get_executable($options);

      $this->trimExecutablesFuncName($this->executables);

      return ! empty($this->executables) && ! empty($this->executed);
   }

   function coverage ( DataCoverage $collector ) : void
   {
      $collector->handlerClassname = __CLASS__;
      $collector->setRawConverage(function () {
         return array($this->purgedExecutables(), $this->purgedExecuted());
      });

      $executables = $this->purgedExecutables();

      // Get the user classes only
      $classes = $this->userClasses();

      foreach ( $executables as $sname => $sinfo)
      {
         if ( empty($sinfo) ) continue;

         $script = new DataScript($sname);
         $slines = array();

         foreach ( $sinfo as $named => $lines )
         {
            if ( ! is_array($lines) || empty($lines) ) continue;

            $name = '';
            $type = DataFunction::T_FUNCTION;
            if ( strpos($named, '::') !== false )
            {
               list($class, $method) = explode('::', $named);
               $name = ($classes[$class] ?? '<not found>') . '::' . $method;
               $type = DataFunction::T_METHOD;
            }
            else
            {
               if ( ! function_exists($named) && strpos($named, '{closure}') !== false )
               {
                  $name = trim($named);
                  $pos  = strpos($name, '{closure}');
                  $name = substr($name, $pos, strlen('{closure')) . substr($name, strrpos($name, ':')) . '}';
                  $type = DataFunction::T_CLOSURE;
               }
               else
               {
                  $reflexion = new \ReflectionFunction($named);
                  $name = $reflexion->name;
               }
            }

            $function = new DataFunction($name, $type);

            ksort($lines);
            foreach ( $lines as $line => $op )
            {
               $hit = $this->wasHit($sname, $line) ? HIT_OK : HIT_NO;
               $function->addLine(new DataLine($line, $hit));
               $slines[$line] = ($slines[$line] ?? 0) + $hit;
            }

            $script->addFunction($function);

            $collector->add($script);
         }

         ksort($slines);
         foreach ( $slines as $line => $hit ) $script->addLine(new DataLine($line, $hit));
      }
   }

   private
   function trimExecutablesFuncName ( & $executables )
   {
      foreach ( $executables as $s => $i )
         $executables[$s] = array_combine(array_map('trim', array_keys($i)), $i);
   }

   public
   function purgedExecutables ()
   {
      return $this->filter->extraFilter($this->filter->purge($this->executables));
   }

   public
   function purgedExecuted ()
   {
      return $this->filter->extraFilter($this->filter->purge($this->executed));
   }

   protected
   function wasHit ($scriptname, $line) : bool
   {
      return array_key_exists($scriptname, $this->executed)
          && array_key_exists($line, $this->executed[$scriptname]);
   }

   /**
    * Retrieve an associative array of all user classes name, where key is the lowercase key name.
    * As phpdbg_end_oplog() retrieve function names as defined by the user, but phpdbg_get_executable()
    * retrive function names in lower case, this method helps for convertion.
    *
    * @return array
    */
   protected
   function userClasses () : array
   {
      $classes = array_filter(get_declared_classes(), function ($class)
      {
         return call_user_func(array(new \ReflectionClass($class), 'isUserDefined'));
      });

      return array_combine(array_map('strtolower', $classes), $classes);
   }

   /*
      Must be running with phpdbg
   */
   protected
   function isLoaded () : bool
   {
      return php_sapi_name() === 'phpdbg';
   }

   protected
   function isReachable () : bool
   {
      return function_exists('phpdbg_start_oplog');
   }

   protected
   function isPCOVConflict ()
   {
      return extension_loaded('pcov') && ini_get('pcov.enabled') == true; // Beware of conflict !
   }
}