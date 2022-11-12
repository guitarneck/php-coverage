<?php

namespace coverage;

// Fake Configuration
class Configuration
{
   public   $noExtraFilter,
            $includes,
            $excludes;

   function __construct ( bool $extraFilter=false, array $includes=array(), array $excludes=array() )
   {
      $this->noExtraFilter = !$extraFilter;
      $this->includes      = $includes;
      $this->excludes      = $excludes;
   }
}