<?php
// A nothing class - (Empty lines from 3 to 6)




class Nop
{
   function __construct()
   {

   }
}

class NopTwo
{
   function __construct()
   {
      user_error("This class do nothing and it's what's it is done for.",E_USER_NOTICE);
   }
}