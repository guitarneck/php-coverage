<?php

namespace coverage\handlers;

use coverage\handlers\AbstractHandler;

use coverage\filters\Filter;

use coverage\collector\DataCoverage;
use coverage\collector\DataScript;
use coverage\collector\DataLine;

class PCOVHandler extends AbstractHandler
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
         $this->reason .= "pcov is not loaded\n";
         return false;
      }

      if ( ! $this->canCoverage() ) $this->reason .= "pcov is not enabled\n";
      if ( ! $this->isReachable() ) $this->reason .= "pcov is unreachable\n";

      return $this->reason === '';
   }

   function start () : bool
   {
      \pcov\start();
      return true;
   }

   function stop () : bool
   {
      \pcov\stop();

      // $this->scripts = \pcov\waiting();

      //-------------------------------------------------------------------------------------------
      // Shall collect coverage information
      //
      // @param integer $type define witch type of information should be collected
      // 		 \pcov\all        shall collect coverage information for all files
      // 		 \pcov\inclusive  shall collect coverage information for the specified files
      // 		 \pcov\exclusive  shall collect coverage information for all but the specified files
      // @param array $filter path of files (realpath) that should be filtered
      //
      // @return array
      //
      // function \pcov\collect(int $type = \pcov\all, array $filter = []) : array;
      //-------------------------------------------------------------------------------------------
      list($includes, $excludes) = $this->filter->intraFilter();

      if ($includes)
         $this->datas = \pcov\collect(\pcov\inclusive, $includes);
      elseif ($excludes)
         $this->datas = \pcov\collect(\pcov\exclusive, $excludes);
      else
         $this->datas = \pcov\collect();

      \pcov\clear();

      return true;
   }

   function coverage ( DataCoverage $collector ) : void
   {
      $collector->handlerClassname = __CLASS__;
      $collector->setRawConverage(function () {
         return $this->purgedDatas();
      });

      $datas = $this->purgedDatas();

      foreach ( $datas as $sname => $lines )
      {
         $script = new DataScript($sname);

         ksort($lines);
         foreach ($lines as $line => $hit)
            $script->addLine(new DataLine($line, $hit < 0 ? HIT_NO : HIT_OK));

         $collector->add($script);
      }
   }

   public
   function purgedDatas ()
   {
      return $this->filter->extraFilter($this->filter->purge($this->datas));
   }

   protected
   function isLoaded() : bool
   {
      return extension_loaded('pcov');
   }

   protected
   function canCoverage() : bool
   {
      return ini_get('pcov.enabled') == true;
   }

   protected
   function isReachable () : bool
   {
      return function_exists('\pcov\start');
   }

}