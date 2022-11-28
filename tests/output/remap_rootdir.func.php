<?php

function remap_rootdir ( $rootdir, $output )
{
   return str_replace(
      array(
         $rootdir,
         str_replace('\\', '\\\\', $rootdir)
      ),
      array('{% TUXROOT %}', '{% WINROOT %}'),
      $output);
}