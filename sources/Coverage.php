<?php

namespace coverage;

use coverage\collector\DataCoverage;
use coverage\Configuration;
use coverage\filters\Filter;
use coverage\format\AbstractFormat;
use coverage\handlers\Handler;
use coverage\php\Memory;
use coverage\php\Process;

const COVERAGE_NOT_AVAILABLE = "\n\e[33m[STOP]\e[0m \e[1;30mthe handler `%1\$s` is not available :\e[0m\n";
const COVERAGE_COVERAGE_OK = "\n\e[32m[DONE]\e[0m \e[1;30mcoverage ended nicely in %0.4fs.\e[0m\n";
const COVERAGE_DEBUG = "\n\e[34m[DEBUG]\e[0m%s";
const COVERAGE_FILE_ERROR = "\n\e[31m[ERROR]\e[0m \e[1;30moutput path failed: No such directory or not writable.\e[0m\n";

/*
30 = normal
31 = red
32 = green
33 = yellow
34 = blue
35 = magenta
36 = cyan
37 = [white]
reason:
[ERROR] (.+)\n => \e[31m[ERROR]\e[0m \e[1;30m $1\e[0m\n
[STOP] (.+)\n => \e[33m[STOP]\e[0m \e[1;30m $1\e[0m\n
[WARN] (.+)\n => \e[33m[WARN]\e[0m \e[1;30m $1\e[0m\n

*/

class Coverage
{
   protected $active;
   protected $caller;

   protected $timer,
             $handler;

   function __construct ()
   {
      $configuration = Configuration::instance();

      $filter = new Filter($configuration);
      $this->handler = Handler::factory($configuration->handler, $configuration->handlers, $filter);

      if ( $this->isDebug() ) Process::terminate("\n");

      if ( !$this->handler->isAvailable() )
      {
         $message = sprintf(COVERAGE_NOT_AVAILABLE, $configuration->handler);
         $tok     = strtok($this->handler->reason, "\n");
         while ( $tok !== false )
         {
            $message .= str_repeat(' ', 3) . sprintf("- \e[33m%s\e[0m\n", $tok);
            $tok = strtok("\n");
         }

         Process::terminate($message);
      }

      $this->active = true;
      $this->timer = microtime(true);

      $this->handler->start();
   }

   function __destruct ()
   {
      if ( ! $this->active ) return;

      $this->handler->stop();

      $coverage   = new DataCoverage();
      $this->handler->coverage($coverage);

      $formater = $this->formater();

      if ( file_put_contents($this->reportPath($formater), $formater->render($coverage)) === false ) Process::terminate(COVERAGE_FILE_ERROR);

      $timer = (microtime(true) - $this->timer)/*  * 1000.0 */;
      printf(COVERAGE_COVERAGE_OK,$timer);
   }

   protected
   function reportPath ( AbstractFormat $formater ): string
   {
      $config  = Configuration::instance();

      $rename = $formater->filenameFormat();
      if ( $rename !== null ) $config->renaming->rename = $rename;

      $script  = $_SERVER['argv'][0];
      $basename= basename($script, $config->renaming->extension);
      $name    = sprintf($config->renaming->rename, $basename);

      $path    = $this->output_path();
      array_push($path, $name);
      return implode(DIRECTORY_SEPARATOR, $path);
   }

   protected
   function isDebug (): bool
   {
      $config = Configuration::instance();
      if ( ! $config->debug ) return false;

      $formater = new format\Formater($config->format);

      $R = "\e[31m";
      $G = "\e[32m";
      $E = "\e[0m";

      $isAvailable = $this->handler->isAvailable();

      $debug = "\n";
      $debug .= sprintf("%s : %s%s\n", str_pad("{$config->handler} is available",20,' '), $isAvailable ? "{$G}ok" : "{$R}no", $E);
      if ( !$isAvailable )
      {
         $tok = strtok($this->handler->reason,"\n");
         while ( $tok !== false )
         {
            $debug .= str_repeat(' ', 23) . sprintf("- {$R}%s{$E}\n", $tok);
            $tok = strtok("\n");
         }
      }
      $debug .= sprintf("format               : %s%s%s\n", $formater->isValid($config->formats) ? $G : $R, $config->format, $E);
      $debug .= "\n";
      $debug .= sprintf("includes\n%s\n", str_repeat('-', 20));
      $debug .= ! $config->includes ? '<N/A>' : implode("\n", $config->includes);
      $debug .= "\n\n";
      $debug .= sprintf("excludes\n%s\n", str_repeat('-', 20));
      $debug .= ! $config->excludes ? '<N/A>' : implode("\n", $config->excludes);
      $debug .= "\n\n";
      $debug .= sprintf("output directory     : %s\n", implode(DIRECTORY_SEPARATOR, $this->output_path()));
      $debug .= sprintf("memory               : used %s, peak %s\n", Memory::usage(), Memory::peakUsage());

      printf(COVERAGE_DEBUG, $debug);
      return true;
   }

   protected
   function output_path (): array
   {
      $config = Configuration::instance();

      $path = explode(',', $config->output);
      $path = array_map(function ($v) {
         $v = str_replace('{DIR}', __DIR__, $v);
         return $v;
      },$path);
      return $path;
   }

   protected
   function formater (): AbstractFormat
   {
      $config = Configuration::instance();
      return format\Formater::factory($config->format, $config->formats);
   }

}