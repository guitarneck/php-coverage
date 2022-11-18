<?php

if (!defined('ROOT')) :
   define('ROOT', dirname(__DIR__, 3));
endif;

class Data
{
   const CONFIGURATION  = ROOT . '\\sources\\Configuration.php';
   const PROCESS        = ROOT . '\\sources\\php\\Process.php';
   const CLIARGUMENTS   = ROOT . '\\sources\\cli\\CLIArguments.php';

   static
   function hideRoot ( $path )
   {
      return substr($path, strrpos($path,'sources') - 1);
   }

   static
   function onlyRoot ( $path )
   {
      return substr($path, 0, strrpos($path,'sources') - 1);
   }
}