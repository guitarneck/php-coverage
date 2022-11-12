<?php

if (!defined('ROOT')) :
   define('ROOT', dirname(__DIR__, 3));
endif;

class Data
{
   const CONFIGURATION  = ROOT . '\\sources\\Configuration.php';
   const PROCESS        = ROOT . '\\sources\\php\\Process.php';
   const CLIARGUMENTS   = ROOT . '\\sources\\cli\\CLIArguments.php';
}