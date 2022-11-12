<?php
/*
   This script is a dry run of phpdbg for coverage purpose, for analysis only.

   https://github.com/php/php-src/tree/master/sapi/phpdbg
*/
// phpdbg -qrr tests\phpdbg.test.php
// phpdbg.exe -qrr tests/phpdbg.test.php

/*
   PHP_BINARY
   define('PHP_PHPDBG', dirname(PHP_BINARY).DIRECTORY_SEPARATOR.'phpdbg');
   print PHP_BINARY . "\n";
   print PHP_PHPDBG . "\n";
   exit;
*/
if ( ! isPHPDBG() ) exit("Must be running with : phpdbg -qrr <filename>.\nExiting.\n");

try
{
   if (!function_exists('phpdbg_start_oplog')) throw new \ErrorException('phpdbg is unreachable or not started');
   @phpdbg_start_oplog();
}
catch (\Exception $e)
{
   echo $e->getMessage();
   exit;
}

$path = realpath(__DIR__ . '/Configuration.test.php');
include_once $path;
// phpdbg_exec($path); // This is not usefull for coverage

/*
opcodes:
   - true : Résultat en numéro de opcodes
   - false: Résultat en numéro de ligne

functions:
   - true : Groupé par function
   - false: Sans la séparation par function

files: Un tableau des paths absolus des fichiers à inclure
*/

register_shutdown_function(function ()
{
   echo "\n===\n";

   /*
   Les lignes de code executées.

   ["functions": bool, ["opcodes": bool]]
   */
   $executedLines = phpdbg_end_oplog(["functions" => false, "opcodes" => false]); // QUE les lignes exécutées !
   echo "# oplog\n";
   var_export($executedLines);
   echo "\n";

   /*
   Les lignes de codes executables.

   le paramètre 'files' permet de filter les résultats, par nom de ficher, en path absolu.

   ["functions": bool]
   ["opcodes": bool]
   ["files": [string,...] (added to the file sources)]
   */
   $executableLines = phpdbg_get_executable(["functions" => true, "opcodes" => false]); // Les lignes executables, et les functions (qui ne respectent pas la casse !!!)
   // ksort($executableLines,SORT_NUMERIC);
   echo "#  executable\n";
   var_export($executableLines);
   echo "\n";

   //=========================================================================
   // All classes, filtered by only user defined classes and lowerized as key.
   //-------------------------------------------------------------------------
   $classes = get_declared_classes();
   $classes = array_filter($classes, function ($c)
   {
      $xion = new ReflectionClass($c);
      return $xion->isUserDefined();
   });
   $classes = array_combine(array_map('strtolower', $classes), $classes);
   echo "\nuser classes:\n";
   echo var_export($classes, true);
   echo "\n\n";
   //=========================================================================

   // Filter out this script
   $executedLines = array_filter($executedLines, function ($k)
   {
      return $k !== __FILE__;
   }, ARRAY_FILTER_USE_KEY);

   $executableLines = array_filter($executableLines, function ($k)
   {
      return $k !== __FILE__;
   }, ARRAY_FILTER_USE_KEY);
   //

   foreach ($executableLines as $script => $content)
   {
      if (empty($content)) continue;

      /*
      if ( array_key_exists($script,$executedLines) )
         echo "script found: $script\n";
      else
         echo "script not found: $script\n";
      */
      echo "$script\n";

      foreach ($content as $named => $lines)
      {
         if (!is_array($lines) || empty($lines)) continue;
         // if ( ! is_string($named) ) continue;

         $reflexion = null;
         $name = '   ';
         $correctedName = '';
         if (strpos($named, '::') !== false)
         {
            list($class, $method) = explode('::', $named);

            // $name .= "$named -> ";
            $name .= ($classes[$class] ?? '<not found>') . '::' . $method;
            $correctedName = ($classes[$class] ?? '<not found>') . '::' . $method;
         }
         else
         {
            if (!function_exists($named) && strpos($named, '{closure}') !== false)
               $name = trim($named);
            else
            {
               $reflexion = new ReflectionFunction($named);
               // $name = "$named -> ";
               $name = $reflexion->name;
               $correctedName = $reflexion->name;
            }
         }

         // echo "\n***\n";
         echo "$name\n";
         // echo "$reflexion\n***\n";

         ksort($lines);

         foreach ($lines as $line => &$zero)
         {
            if (array_key_exists($script, $executedLines) && array_key_exists($line, $executedLines[$script]))
               $zero = 1;
         }

         echo "   lines: " . var_export($lines, true) . "\n";
      }
   }
});

function isPHPDBG()
{
   return php_sapi_name() === 'phpdbg';
}