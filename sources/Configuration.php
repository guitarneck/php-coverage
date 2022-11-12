<?php

namespace coverage;

use coverage\cli\CLIArguments;
use coverage\php\Process;

const CONFIG_VERSION = '1.0';
const CONFIG_JSON = __DIR__ . DIRECTORY_SEPARATOR . 'Coverage.json';
const CONFIG_ERROR = "\e[31m[ERROR]\e[0m \e[1;30mconfiguration has bad json format.\e[0m\n";
const CONFIG_WARNING = "\e[33m[WARNING]\e[0m \e[1;30mconfiguration version conflict.\e[0m\n";

class Configuration
{
   protected  $config;

   static
   function instance () : Configuration
   {
      static $instance = null;
      if ( $instance === null ) $instance = new Configuration();
      return $instance;
   }

   private
   function __construct ()
   {
      $this->setup();
   }

   protected
   function setup ()
   {
      if ( ! $this->loadConfiguration() )
         Process::terminate(CONFIG_ERROR);

      if ( version_compare(CONFIG_VERSION, $this->config->version, '<') )
         Process::terminate(CONFIG_WARNING);

      $this->update();
   }

   private
   function __clone ()
   {
   }

   function __get ( $name )
   {
      return $this->config->{$name};
   }

   protected
   function loadConfiguration () : bool
   {
      $this->config = json_decode(file_get_contents(CONFIG_JSON));
      return JSON_ERROR_NONE === json_last_error();
   }

   protected
   function update () : void
   {
      CLIArguments::onHelp(function ( CLIArguments $arguments )
      {
         $this->formatHelp($arguments);
      });
      $arguments = new CLIArguments($this->config->arguments);

      if ($arguments->includes !== null)
         $this->config->includes = $this->realpath($arguments->includes);

      if ($arguments->excludes !== null)
         $this->config->excludes = $this->realpath($arguments->excludes);

      if ($arguments->format !== null)
         $this->config->format = $arguments->format;

      if ($arguments->output !== null)
         $this->config->output = $arguments->output;

      $this->config->debug = $arguments->debug;

      $this->config->noExtraFilter = $arguments->noExtraFilter;

      if ($arguments->handler !== null)
         $this->config->handler = $arguments->handler;
   }

   public
   function formatHelp ( CLIArguments $arguments ) : void
   {
      if ($arguments->format === null) return;
      print format\Formater::help($arguments->format, $this->config->formats);
   }

   protected
   function realpath ( string $paths ): array
   {
      return array_map(function ($p)
      {
         $path = realpath($p) ?? $p;
         if ( is_dir($path) ) $path .= DIRECTORY_SEPARATOR;
         return $path;
      },
      explode(',', $paths));
   }
}
