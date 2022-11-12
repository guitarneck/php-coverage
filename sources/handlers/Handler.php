<?php

namespace coverage\handlers;

use coverage\handlers\AbstractHandler;
use coverage\php\Process;
use coverage\filters\Filter;

const COVERAGE_HANDLER_TYPE_NOT_FOUND  = "\n\e[31m[ERROR]\e[0m \e[1;30mhandler not found:\e[0m \e[31m%s\e[0m\n";
const COVERAGE_HANDLER_CLASS_NOT_FOUND = "\n\e[31m[ERROR]\e[0m \e[1;30mhandler class not found.\e[0m\n";

class Handler
{
   protected $name;

   function __construct ( $name )
   {
      $this->name = $name;
   }

   /**
    * Retrieve a handler object.
    *
    * @param string $name      The name to use.
    * @param \stdClass $handlers  The handlers configuration.
    * @return Abstracthandler|null     The handler object.
    */
    static
    function factory ( string $name, \stdClass $handlers, Filter $filter ) : ?AbstractHandler
    {
      $handler = new Handler($name);
      list($script,$class) = $handler->findOrDie($name, $handlers);

      @include_once $script;
      $class = __NAMESPACE__ . '\\' . $class;
      if ( class_exists($class) )
         return new $class($filter);
      else
         return null;
    }

   /**
    * Retrieve the script name and the class according to a handler.
    *
    * @param string $handler      The handler to use.
    * @param \stdClass $handlers  The handlers configuration.
    * @return array<string|null,string|null> An array with the script name and the class name, or null.
    */
    function findOrDie ( string $handler, \stdClass $handlers ) : array
    {
       if ( !$this->inHandlers($handlers) )
       {
          Process::terminate(sprintf(COVERAGE_HANDLER_TYPE_NOT_FOUND, $handler));
          return array(null, null);
       }

       $hclass = $this->handlerClass($handlers);
       $hscript = $this->handlerScript($hclass);
       if ( ! file_exists($hscript) )
       {
          Process::terminate(COVERAGE_HANDLER_CLASS_NOT_FOUND);
          return array(null, null, null, null);
       }

       return array($hscript, $hclass);
    }

    function isValid ( \stdClass $handlers ) : bool
    {
       if ( ! $this->inHandlers($handlers) ) return false;

       $class   = $this->handlerClass($handlers);
       $script  = $this->handlerScript($class);
       return file_exists($script);
    }

    function inHandlers ( \stdClass $handlers ) : bool
    {
       return isset($handlers->{$this->name});
    }

    function handlerClass ( \stdClass $handlers ) : string
    {
       return sprintf('%sHandler', $handlers->{$this->name});
    }

    function handlerScript ( string $className ) : string
    {
       return sprintf('%s%s%s.php', __DIR__, DIRECTORY_SEPARATOR, $className);
    }

    static
    function class2handler ( string $classname ) : string
    {
       return strtolower(substr(str_replace('Handler', '', $classname), strlen(__NAMESPACE__) + 1));
    }

}