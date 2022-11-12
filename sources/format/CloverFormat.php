<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\collector\DataFunction;
use coverage\collector\DataScript;
use coverage\format\Formater;

class CloverFormat extends AbstractFormat
{
   protected $options = array();

   private $xml;

   function __construct ( array $params=null )
   {
      // Setting default parameter value
      if ( ! isset($params['name']) ) $params['name'] = 'All files';

      // Storing the user (or default) parameter
      $this->options['name'] = $params['name'];
   }

   function filenameFormat () : string
   {
      return 'clover_%s.xml';
   }

   function render ( DataCoverage $coverage ) : string
   {
      $this->xml = new \DOMDocument( "1.0", "UTF-8" );

      $time = time();

      $xml = $this->xml->createElement( "coverage" );
      $xml->setAttribute('generated', $time);
      $xml->setAttribute('clover', '3.2.0');
      $this->xml->appendChild($xml);

      $project = $this->xml->createElement( "project" );
      $project->setAttribute('timestamp', $time);
      $project->setAttribute('name', $this->options['name']);
      $xml->appendChild($project);

      $pmetrics = array(
         '__root'             => $project,
         'methods'            => 0,
         'coveredmethods'     => 0,
         'statements'         => 0,
         'coveredstatements'  => 0,
         // 'conditionals'       => 0,
         // 'coveredconditionals'=> 0,
         'complexity'         => 0,
         'packages'           => 1,
         'files'              => 0
      );

      /* Scripts ---
         $sname: The full path of the script
         $info: The xdebug script converage informations, lines & functions
      */
      foreach ( $coverage->scripts() as $script )
      {
         $file = $this->xml->createElement('file');
         $file->setAttribute('name', basename($script->name));
         $file->setAttribute('path', $script->name);

         $pmetrics['files']++;
         $smetrics = array(
            '__root'             => $file,
            'methods'            => 0,
            'coveredmethods'     => 0,
            'statements'         => 0,
            'coveredstatements'  => 0,
            'elements'           => 0,
            'coveredelements'    => 0,
            'classes'            => null,
            'conditionals'       => null, // Number of branches
            'coveredconditionals'=> null, // Number of branches hits
            'loc'                => 0, // Lines Of Code
            'ncloc'              => 0  // Not Covered Lines Of Code
         );

         if ( $script->hasFunctions() )
            $this->scanFunctions($script, $file, $smetrics);
         else
            $this->scanLines($script, $file, $smetrics);

         $this->createElementMetrics($smetrics);
         $this->addMetrics($smetrics, $pmetrics);
         $project->appendChild($file);
      }

      $this->createElementMetrics($pmetrics);

      $this->xml->formatOutput = true;
      return rtrim($this->xml->saveXML());
   }

   private
   function scanFunctions ( DataScript $script, $file, & $smetrics )
   {
      $classes = array();

      foreach ( $script->functions() as $function )
      {
         $fmetrics = array(
            '__root'             => $file,
            'methods'            => 0,
            'coveredmethods'     => 0,
            'statements'         => 0,
            'coveredstatements'  => 0,
            'elements'           => 0,
            'coveredelements'    => 0,
            'classes'            => null,
            // 'conditionals'       => null,
            // 'coveredconditionals'=> null,
            'loc'                => 0,
            'ncloc'              => 0
         );

         $name = null;
         if ( $function->type === DataFunction::T_METHOD )
         {
            $type = 'method';
            list($class,$name) = preg_split('/->|::/', $function->name);
            if ( ! isset($classes[$class]) )
            {
               $classes[$class] = array(
                  'methods'               => 0,
                  'coveredmethods'        => 0,
                  // 'conditionals'          => null,
                  // 'coveredconditionals'   => null,
                  'statements'            => 0,
                  'coveredstatements'     => 0,
                  'elements'              => 0,
                  'coveredelements'       => 0
               );
            }
         }
         else
         {
            $type = $function->type === DataFunction::T_FUNCTION ? 'function' : 'closure';
            $name = $function->name;
            $class= null;
         }

         foreach ( $function->lines() as $n => $line )
         {
            $xline = $this->xml->createElement('line');
            $xline->setAttribute('num', $line->number);
            $xline->setAttribute('count', $line->hit);

            if ( $n > 0 )
            {
               $xline->setAttribute('type', 'stmt');
               $fmetrics['statements']++;
               if ( $line->hit > 0 ) $fmetrics['coveredstatements']++;
            }
            else
            {
               $xline->setAttribute('type', $type);
               if ( $name !== null ) $xline->setAttribute('name',$name);
               if ( $class !== null ) $xline->setAttribute('classname',$class);

               if ( $type === 'method' )
               {
                  $fmetrics['methods']++;
                  if ( $line->hit > 0 ) $fmetrics['coveredmethods']++;
               }
            }

            $fmetrics['elements']++;
            if ( $line->hit > 0 ) $fmetrics['coveredelements']++;

            $fmetrics['loc']++;
            if ( $line->hit === 0 ) $fmetrics['ncloc']++;

            $file->appendChild($xline);
         }

         $this->addMetrics($fmetrics, $smetrics);

         if ( $class !== null )
            $this->addMetrics($fmetrics, $classes[$class]);
      }

      $firstChild = $file->firstChild;
      foreach ( $classes as $name => $metrics )
      {
         $classnode = $this->xml->createElement('class');
         $classnode->setAttribute('name', $name);

         $metrics['__root'] = $classnode;
         $this->createElementMetrics($metrics);
         $file->insertBefore($classnode, $firstChild);
      }
   }

   private
   function scanLines ( DataScript $script, $file, & $smetrics )
   {
      $lmetrics = array(
         '__root'             => $file,
         'statements'         => 0,
         'coveredstatements'  => 0,
         'elements'           => 0,
         'coveredelements'    => 0,
         'loc'                => 0,
         'ncloc'              => 0
      );

      foreach ( $script->lines() as $n => $line )
      {
         $xline = $this->xml->createElement('line');
         $xline->setAttribute('num', $line->number);
         $xline->setAttribute('count', $line->hit);

         $xline->setAttribute('type', 'stmt');
         $lmetrics['statements']++;
         if ( $line->hit > 0 ) $lmetrics['coveredstatements']++;

         $lmetrics['elements']++;
         if ( $line->hit > 0 ) $lmetrics['coveredelements']++;

         $lmetrics['loc']++;
         if ( $line->hit === 0 ) $lmetrics['ncloc']++;

         $file->appendChild($xline);
      }

      $this->addMetrics($lmetrics, $smetrics);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_PARMAMETER_HEAD, Formater::class2format(__CLASS__), "[?][name=]")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "name: (string) A name to store in the clover.")
           . COVERAGE_FORMAT_PARMAMETER_FOOT;
   }

   private
   function addMetrics ( array $from, array & $into ) : void
   {
      foreach ( $from as $k => $v )
      {
         if ( $k === '__root' ) continue;
         if ( $v === null ) continue;
         if ( ! isset($into[$k]) || $into[$k] === null ) continue;
         $into[$k] += $v;
      }
   }

   private
   function createElementMetrics ( array $parms ) : void
   {
      $metrics = $this->xml->createElement( "metrics" );
      $root    = $parms['__root'];
      unset($parms['__root']);
      foreach ( $parms as $k => $value )
      {
         if ( $value === null ) continue;
         $metrics->setAttribute($k, $value);
      }
      $root->insertBefore($metrics, $root->firstChild);
   }
}