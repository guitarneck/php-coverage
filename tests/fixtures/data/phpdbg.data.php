<?php

require_once 'Data.class.php';

function data_import ()
{
   $executed = array (
      Data::CLIARGUMENTS =>
      array (
         152 => 1,
         136 => 1,
         138 => 1,
         139 => 1,
         70 => 15,
         72 => 75,
       ),
      Data::CONFIGURATION =>
      array (
         108 => 1,
         20 => 2,
         21 => 5,
         28 => 2,
         34 => 4,
         56 => 16,
         57 => 10,
         37 => 10,
         40 => 2,
         63 => 2,
         66 => 2,
         67 => 7,
         69 => 2,
         72 => 2,
         75 => 2,
         76 => 3,
         78 => 2,
         81 => 3,
         83 => 3,
         85 => 2,
         87 => 1,
         41 => 1,
         29 => 1,
         22 => 2,
         48 => 9,
         50 => 27,
         97 => 1,
         99 => 4,
         104 => 1,
         105 => 7,
         101 => 10,
         102 => 12,
         103 => 2,
         90 => 4,
         92 => 9,
         93 => 30,
         94 => 3,
       ),
   );

   $executables = array (
      Data::CLIARGUMENTS =>
      array (
         'coverage\\cli\\{closure}D:\\sources\\php\\coverage\\sources\\cli\\CLIArguments.php:95$1f' =>
         array (
           95 => 0,
           97 => 0,
         ),
         'coverage\\cli\\{closure}D:\\sources\\php\\coverage\\sources\\cli\\CLIArguments.php:111$20' =>
         array (
           111 => 0,
           113 => 0,
           114 => 0,
           115 => 0,
           116 => 0,
         ),
         'coverage\\cli\\cliarguments::parameter' =>
         array (
           20 => 0,
           22 => 0,
           23 => 0,
           24 => 0,
         ),
         'coverage\\cli\\cliarguments::__construct' =>
         array (
           28 => 0,
           30 => 0,
           32 => 0,
           34 => 0,
           35 => 0,
           36 => 0,
           38 => 0,
           39 => 0,
           40 => 0,
           42 => 0,
           43 => 0,
           44 => 0,
           46 => 0,
           47 => 0,
           50 => 0,
           51 => 0,
           54 => 0,
           55 => 0,
           56 => 0,
           58 => 0,
           61 => 0,
           63 => 0,
           64 => 0,
           65 => 0,
         ),
         'coverage\\cli\\cliarguments::__get' =>
         array (
           70 => 0,
           72 => 0,
         ),
         'coverage\\cli\\cliarguments::__isset' =>
         array (
           76 => 0,
           78 => 0,
         ),
         'coverage\\cli\\cliarguments::initialize' =>
         array (
           93 => 0,
           95 => 0,
           98 => 0,
           100 => 0,
           102 => 0,
           103 => 0,
         ),
         'coverage\\cli\\cliarguments::help' =>
         array (
           107 => 0,
           109 => 0,
           111 => 0,
           117 => 0,
           119 => 0,
           120 => 0,
           121 => 0,
           122 => 0,
           123 => 0,
           124 => 0,
           126 => 0,
           127 => 0,
           128 => 0,
           129 => 0,
           130 => 0,
           131 => 0,
         ),
         'coverage\\cli\\cliarguments::onHelp' =>
         array (
           136 => 0,
           138 => 0,
         ),
         'coverage\\cli\\cliarguments::isCli' =>
         array (
           144 => 0,
         ),
         'coverage\\cli\\cliarguments::hasArguments' =>
         array (
           150 => 0,
         ),
      ),
      Data::CONFIGURATION =>
      array (
         'coverage\\{closure}D:\\sources\\php\\coverage\\sources\\Configuration.php:63$21' =>
         array (
           63 => 0,
           65 => 0,
         ),
         'coverage\\{closure}D:\\sources\\php\\coverage\\sources\\Configuration.php:99$22' =>
         array (
           99 => 0,
           101 => 0,
           102 => 0,
           103 => 0,
         ),
         'coverage\\configuration::instance' =>
         array (
           20 => 0,
           21 => 0,
           22 => 0,
         ),
         'coverage\\configuration::__construct' =>
         array (
           28 => 0,
         ),
         'coverage\\configuration::setup' =>
         array (
           34 => 0,
           35 => 0,
           37 => 0,
           38 => 0,
           40 => 0,
         ),
         'coverage\\configuration::__clone' =>
         array (
         ),
         'coverage\\configuration::__get' =>
         array (
           48 => 0,
           50 => 0,
         ),
         'coverage\\configuration::loadConfiguration' =>
         array (
           56 => 0,
           57 => 0,
         ),
         'coverage\\configuration::update' =>
         array (
           63 => 0,
           66 => 0,
           67 => 0,
           69 => 0,
           70 => 0,
           72 => 0,
           73 => 0,
           75 => 0,
           76 => 0,
           78 => 0,
           79 => 0,
           81 => 0,
           83 => 0,
           85 => 0,
           86 => 0,
         ),
         'coverage\\configuration::formatHelp' =>
         array (
           90 => 0,
           92 => 0,
           93 => 0,
         ),
         'coverage\\configuration::realpath' =>
         array (
           97 => 0,
           99 => 0,
           104 => 0,
           105 => 0,
         ),
      ),
   );

   return array($executables, $executed);
}