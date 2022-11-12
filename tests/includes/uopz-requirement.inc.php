<?php
namespace uopz_requirement;

if ( ! extension_loaded('uopz') ) die("\e[32m[ERROR] uopz extension is required\e[0m\n");
if ( ini_get('uopz.disable') ) die("\e[33m[STOP] Usage: php -d uopz.disable=0 tests\Configuration.test.php\e[0m\n");