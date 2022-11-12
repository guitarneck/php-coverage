<?php
// Disable opcache during unit tests
if ( extension_loaded('opcache') )
{
   ini_set('opcache.enable', false);
   ini_set('opcache.optimization_level', 0);
}