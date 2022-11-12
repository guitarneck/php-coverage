<?php

// php -d uopz.disable=0 tests\Configuration.test.php

namespace Namespace_declaration_statement_has_to_be_the_very_first_statement_suks;

   require_once 'includes/opcache-disable.inc.php';
   require_once 'includes/uopz-requirement.inc.php';
   require_once 'mocks/MockProcess.php';

namespace coverage;

   require_once dirname(__DIR__) . '/vendor/guitarneck/taphp/taphp.lib.php';
   require_once dirname(__DIR__) . '/vendor/autoload.php';

   use coverage\Configuration;
   use coverage\cli\CLIArguments;

   // Scenarii class state
   class Scenarii
   {
      const ALL_OK               = 0;
      const UNKNOWN_FORMAT_CLASS = 0b1; // Ok, but no class found
      const UNKNOWN_FORMAT       = 0b10; // Bad format required
      const NULL_FORMAT          = 0b100; // Bad format required

      protected static $state    = self::ALL_OK;

      static
      function to ( $state )
      {
         static::$state = $state;
      }

      static
      function is ( $state )
      {
         return (static::$state & $state) > 0;
      }
   }

   // Fake CLIArguments
   class FakeCLIArguments extends CLIArguments
   {
      protected $arguments;

      function __construct ( array $parameters=array() )
      {
         $this->arguments = array();
         switch (true)
         {
            case Scenarii::is(Scenarii::NULL_FORMAT):
               $this->arguments['format']  = null;
               break;
            case Scenarii::is(Scenarii::UNKNOWN_FORMAT):
               $this->arguments['format']  = 'unknown';
               break;
            case Scenarii::is(Scenarii::UNKNOWN_FORMAT_CLASS):
               $this->arguments['format']  = 'unclass';
               break;
            default:
               $this->arguments['format']  = 'coverage';
         }
      }
   }

   uopz_set_mock(CLIArguments::class,FakeCLIArguments::class);

   //
   class MockConfigurationFormat extends Configuration
   {
      protected  $config;

      function __construct ()
      {
         $this->toScenario();
      }

      protected
      static function terminate ( $status=0 )
      {
         print "here\n";
         print $status;
      }

      function toScenario ()
      {
         switch (true)
         {
            case Scenarii::is(Scenarii::UNKNOWN_FORMAT_CLASS):
            case Scenarii::is(Scenarii::UNKNOWN_FORMAT):
               $this->config = json_decode('{
                  "formats":{
                     "unclass"    : "UnClass"
                  }
               }');
               break;
            default:
               $this->config = json_decode('{
                  "formats":{
                     "coverage"    : "Coverage"
                  }
               }');
         }
      }

      function realpath ($p) : array
      {
         return parent::realpath($p);
      }

      function help () : string
      {
         ob_start();
         static::formatHelp(new CLIArguments([]));
         return ob_get_clean();
      }
   }

   //
   class MockConfiguration extends Configuration
   {
      static
      function instance () : MockConfiguration
      {
         static $instance=null;
         if ( $instance === null ) $instance = new MockConfiguration();
         return $instance;
      }

      function __construct ()
      {
         $this->setup();
      }

      protected
      function setup ()
      {
         if ( ! $this->loadConfiguration() ) $this->config = null;
         $this->setDefaults();
      }

      protected
      function setDefaults () : void
      {
         foreach ( $this->config->arguments as $parm )
         {
            if ( $parm[3] === null ) continue;
            $this->config->{$parm[1]} = $parm[3];
         }
      }

      function isConfigurationLoaded ()
      {
         return $this->config !== null;
      }
   }

   //
   // Unit Tests ===
   //

   test('Configuration - instance',function (\TAPHP $t) {
      $inst = Configuration::instance();
      $t->equal(get_class($inst), 'coverage\Configuration', 'It should be a Configuration instance');
      $t->equal($inst, Configuration::instance(), 'It should be the same instance');

      $t->end();
   });

   test('Configuration - cloning',function (\TAPHP $t) {
      $ref = new \ReflectionClass('coverage\\Configuration');
      $t->no($ref->isCloneable());

      $t->end();
   });

   test('Mock Configuration - instance',function (\TAPHP $t) {
      $inst = MockConfiguration::instance();
      $t->equal(get_parent_class($inst), 'coverage\Configuration', 'It should de a Configuration instance');
      $t->equal($inst, MockConfiguration::instance(), 'It should be the same instance');

      $t->end();
   });

   test('Configuration - get default conf',function (\TAPHP $t) {
      $inst = MockConfiguration::instance();
      $t->ok($inst->isConfigurationLoaded(), 'It should have loaded configuration');
      $t->equal($inst->version, '1.0', 'It should match the version 1.0');
      $t->looseEqual($inst->renaming, (object)['extension'=>'.test.php','rename'=>'%s.cov'], 'It should mathch renaming parms');
      $t->looseEqual($inst->arguments, [
         [
            "--handler=,--handler",
            "handler",
            false,
            "xdebug",
            "The handler to use for coverage.\n\t[xdebug|phpdbg|pcov]\n\tdft: xdebug"
         ],
         [
            "--includes=,--includes,-i",
            "includes",
            false,
            null,
            "The paths to include. Separated by ','.\n\tEx: src/,inc/"
         ],
         [
            "--excludes=,--excludes,-x",
            "excludes",
            false,
            null,
            "The paths to exclude. Separated by ','.\n\tEx: vendor/,tests/,inc/lib/"
         ],
         [
            "--output-path=,--output-path,-p",
            "output",
            false,
            "{DIR},..,reports",
            "The paths to output. Separated by ','.\n\tEx: {DIR},..,reports\n\t- {DIR}: __DIR__ ('coverage/sources')\n\t- ..   : parent path"
         ],
         [
            "--format=,--format,-f",
            "format",
            false,
            null,
            "The file format to be generated.\n\t[clover|coverage|coveralls|dot|dump|export|json|lcov|raw|serialize]\n\tdft: coverage"
         ],
         [
            "--debug",
            "debug",
            true,
            false,
            "Show debug informations."
         ],
         [
            "--no-extra-filter",
            "noExtraFilter",
            true,
            false,
            "Do not apply extra filtering (includes & excludes)."
         ]
      ]);
      $t->looseEqual($inst->includes, []);
      $t->looseEqual($inst->excludes, []);
      $t->looseEqual($inst->formats, (object)[
         'export' => 'Export',
         'serialize' => 'Serialize',
         'json' => 'JSON',
         'dot' => 'Dot',
         'dump' => 'Dump',
         'coverage' => 'Coverage',
         'coveralls' => 'Coveralls',
         'lcov' => 'LCOV',
         'clover' => 'Clover',
         'raw' => 'Raw',
      ]);
      $t->equal($inst->format, 'coverage');
      $t->equal($inst->debug, false);
      $t->equal($inst->noExtraFilter, false);

      $t->end();
   });

   test('constants',function (\TAPHP $t) {
      $t->equal(CONFIG_JSON, dirname(__DIR__) . implode(DIRECTORY_SEPARATOR,['','sources','Coverage.json']));
      $t->equal(CONFIG_ERROR, "\e[31m[ERROR]\e[0m \e[1;30mconfiguration has bad json format.\e[0m\n");

      $t->end();
   });

   test('Methods testing - realpath()',function (\TAPHP $t) {
      $mock = new MockConfigurationFormat();
      $t->ok(is_a($mock, 'coverage\Configuration'), 'It should de a Configuration instance');
      $paths = array('tests', 'tests/fixtures/tests/src');
      $paths = array_map(function ( $p ) {
         return realpath(dirname(__DIR__) . '/' . $p) . DIRECTORY_SEPARATOR;
      },$paths);
      $t->equal($mock->realpath('tests,tests/fixtures/tests/src'), $paths);

      $t->end();
   });

   test('Methods testing - help()',function (\TAPHP $t) {
      $mock = new MockConfigurationFormat();

      $help = $mock->help();
      $t->equal(gettype($help), 'string','It should be a string');
      $t->skip('insure is a string before going on...', ['skip'=> !is_string($help)]);
      $t->ok(strpos($help,'format required no parameter') !== false, 'It should have required no parameter');

      Scenarii::to(Scenarii::UNKNOWN_FORMAT);

      $mock->toScenario();
      $help = $mock->help();
      $t->equal(gettype($help), 'string', 'It should be a string');
      $t->skip('insure is a string before going on...', ['skip'=> !is_string($help)]);
      $t->ok(strpos($help,'formater not found:') !== false);

      Scenarii::to(Scenarii::UNKNOWN_FORMAT_CLASS);

      $mock->toScenario();
      $help = $mock->help();
      $t->equal(gettype($help), 'string', 'It should be a string');
      $t->skip('insure is a string before going on...', ['skip'=> !is_string($help)]);
      $t->ok(strpos($help,'formater class not found') !== false);

      Scenarii::to(Scenarii::NULL_FORMAT);

      $mock->toScenario();
      $help = $mock->help();
      $t->equal(gettype($help), 'string', 'It should be a string');
      $t->skip('insure is a string before going on...', ['skip'=> !is_string($help)]);
      $t->equal($help, '', 'It should retrieve no help format');

      $t->end();
   });