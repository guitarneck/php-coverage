<?php
namespace coverage\format;

use coverage\collector\DataCoverage;
use coverage\format\Formater;

class CoverallsFormat extends AbstractFormat
{
   protected $options = array();

   function __construct ( array $params = null )
   {
      if ( ! isset($params['jobId']) ) $params['jobId'] = null;
      if ( ! isset($params['name']) )  $params['name'] = '';
      if ( ! isset($params['token']) ) $params['token'] = '';

      $this->options['service_job_id'] = $params['jobId'];
      $this->options['service_name']   = $params['name']; // "travis-ci"
      $this->options['repo_token']     = $params['token'];
   }

   function filenameFormat () : string
   {
      return '%s.coveralls';
   }

   function render ( DataCoverage $coverage ) : string
   {
      $sourceFiles = array();

      foreach ( $coverage->scripts() as $script )
      {
         $lines   = array_fill(1, $script->linesBottom(), null); // Do we need al the source lines ??? yes !
         foreach ( $script->lines() as $line ) $lines[$line->number] = $line->hit;

         $sourceFiles[] = array(
            "name"            => $this->relativeToProject($script->name),
            "source_digest"   => md5(file_get_contents($script->name)),
            "coverage"        => $lines
         );
      }

      $coveralls = array(
         "service_job_id"     => $this->options["service_job_id"],
         "service_name"       => $this->options["service_name"],
         "service_event_type" => 'manual',
         "run_at"             => date('Y-m-d H:i:s O'),
         "git"                => $this->coverage_coveralls_gitinfo(),
         "repo_token"         => $this->options["repo_token"],
         "source_files"       => $sourceFiles
      );

      return json_encode($coveralls, JSON_UNESCAPED_SLASHES);
   }

   static
   function help () : string
   {
      return sprintf(COVERAGE_FORMAT_PARMAMETER_HEAD, Formater::class2format(__CLASS__), "[?][jobId=][&][name=][&][token=]")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "jobId : (string) The service job id.")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "name  : (string) The service name.")
           . sprintf(COVERAGE_FORMAT_PARMAMETER_PARM, "token : (string) The repository token.")
           . COVERAGE_FORMAT_PARMAMETER_FOOT;
   }

   protected
   function coverage_coveralls_gitinfo () : array
   {
      $nul = PHP_OS !== 'WINNT' ? '/dev/null' : 'nul';

      $git_last_commit = 'git log -1 --pretty=format:\'{"id":"%H","author_name":"%aN","author_email":"%ae","committer_name":"%cN","committer_email":"%ce","message":"%s"}\'' . " 2>{$nul}";
      $git_last_branch = 'git log -1 --decorate-refs="refs/heads/*" --format=%D' . " 2>{$nul}";
      $gitinfo = array();
      $gitinfo["head"]     = json_decode(@exec($git_last_commit));
      $gitinfo["branch"]   = @exec($git_last_branch);

      return $gitinfo;
   }

   protected
   function relativeToProject ( string $path ) : string
   {
      static $root = null;

      if ( $root === null )
      {
         $root = $path;
         do {
            $root = dirname($root);
         } while ( ! empty($root) && ! file_exists($root.DIRECTORY_SEPARATOR.'composer.json') );
      }

      return str_replace('\\', '/', substr($path,strlen($root) + 1));
   }
}