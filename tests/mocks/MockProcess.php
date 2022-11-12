<?php

namespace coverage\php;

   /**
    * A Mock Process class.
    * @class MockProcess
    */
   class MockProcess
   {
      static
      function terminate ( $status = 0 )
      {
         print $status;
      }
   }

   class_alias(MockProcess::class,'coverage\\php\\Process'); // Yes, it's a static Mock !