<?php
// Disable opcache during unit tests
if ( extension_loaded('Zend OPcache') )
{
   ini_set('opcache.enable', false);
}