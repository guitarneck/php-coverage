<?php

namespace coverage\handlers;

use coverage\collector\DataCoverage;

const HIT_NO = 0;
const HIT_OK = 1;

abstract
class AbstractHandler
{

   public   $reason = '';

   /**
    * Tell the handler is available.
    *
    * @return boolean
    */
   abstract function isAvailable () : bool;

   /**
    * Start the handler.
    *
    * @return boolean
    */
   abstract function start () : bool;

   /**
    * Stop the handler.
    *
    * @return boolean
    */
   abstract function stop () : bool;

   /**
    * Collect the coverage datas.
    *
    * @param coverage\collector\DataCoverage $collector   The collector for the datas.
    * @return void
    */
    abstract function coverage ( DataCoverage $collector ) : void;

}