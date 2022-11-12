<?php

namespace coverage\cli;

const CLIARG_UNKNOWN = "\e[31m[ERROR] Unknown argument: %s\e[0m\n";

class CLIArguments
{
   const    TOK   = 0;
   const    KEY   = 1;
   const    ISB   = 2;
   const    DEF   = 3;
   const    EXP   = 4;

   protected $arguments = array();

   protected static $additionalHelp = null;

   static
   function parameter ( string $tokens = '', string $key = '', bool $isbool = false, /*mixed*/ $default = null, string $explenation = '' ) : array
   {
      return array_replace(
         array('', '', false, null, ''),
         array($tokens, $key, $isbool, $default, $explenation)
      );
   }

   function __construct ( array $parameters )
   {
      $this->initialize($parameters);

      if ( ! $this->isCli() || !$this->hasArguments() ) return;

      $argv = new \ArrayIterator($_SERVER['argv']);
      $argv->offsetUnset(0); // Remove the programm name
      foreach ( $argv as $arg )
      {
         $pos = strpos($arg, '=');
         $unknown = true;
         foreach ( $parameters as $parm )
         {
            if ( ! in_array($pos !== false ? substr($arg, 0, $pos) : $arg, explode(',', $parm[self::TOK])) ) continue;
            $value = true;
            if ( ! $parm[self::ISB] )
            {
               if ( $pos !== false )
                  $value = substr($arg, $pos + 1);
               else
               {
                  $argv->next();
                  $value = $argv->current();
               }
            }
            $this->arguments[$parm[self::KEY]] = $value;
            $unknown = false;
            break;
         }
         if ( $unknown ) exit(sprintf(CLIARG_UNKNOWN, $arg));
      }

      if ( $this->arguments['help'] )
      {
         $this->help($parameters);
         if ( self::$additionalHelp !== null ) call_user_func(self::$additionalHelp, $this);
         exit("\n");
      }
   }

   public
   function __get ( $key )
   {
      return @$this->arguments[$key];
   }

   public
   function __isset ( $key )
   {
      return isset($this->arguments[$key]);
   }

   /**
    * Initialize the cli parameters ot inspect. 'help' is auto added.
    *
    * @param array &$parameters   The parameters configuration
    *                - string    Token list, serparated by comma (ex: '--file,-file,-f')
    *                - string    Key to store and retrieve the arguments (ex: 'filename')
    *                - boolean   Is a boolean true/false. A boolean don't need an additionnal value.
    *                - mixed     Default value
    *                - string    Explenation for this parameter
    * @return void
    */
   protected
   function initialize ( array & $parameters ) : void
   {
      $parameters = array_map(function ($v)
      {
         return self::parameter($v[self::TOK], $v[self::KEY], $v[self::ISB], $v[self::DEF], $v[self::EXP] ?? '');
      }, $parameters);

      array_push($parameters, self::parameter('--help,-h', 'help', true, false, 'This help page'));

      reset($parameters);
      foreach ( $parameters as $parm ) $this->arguments[$parm[self::KEY]] = $parm[self::DEF];
   }

   protected
   function help ( array $parameters ) : void
   {
      $max = 0;

      usort($parameters, function ( $a, $b ) use (&$max)
      {
         $max = max($max, strlen($a[self::TOK]), strlen($b[self::TOK]));
         $a = str_replace(['-', '='], ['', ''], explode(',', $a[self::TOK])[0]);
         $b = str_replace(['-', '='], ['', ''], explode(',', $b[self::TOK])[0]);
         return strcmp($a, $b);
      });

      $max += 8;
      $mrg  = '   ';
      $mrl  = $max + strlen($mrg);
      $pad  = "\n" . str_repeat(' ', $mrl);
      echo "\e[34m[HELP]\e[0m";
      foreach ( $parameters as $parameter )
      {
         $str = str_replace("\n\t", $pad, $parameter[self::EXP]);
         echo  "\n",
         $mrg,
         "\e[1m", str_pad($parameter[self::TOK], $max), "\e[0m",
         wordwrap($str, 132 - $mrl, $pad),
         "\n";
      }
   }

   public static
   function onHelp ( callable $additionalHelp )
   {
      self::$additionalHelp = $additionalHelp;
   }

   protected
   function isCli () : string
   {
      return (substr(php_sapi_name(), 0, 3) === 'cli' || php_sapi_name() === 'phpdbg') && empty($_SERVER['DOCUMENT_ROOT']);
   }

   protected
   function hasArguments () : bool
   {
      return $_SERVER['argc'] > 1;
   }
}