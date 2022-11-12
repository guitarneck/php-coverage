<?php

class Hello
{
   static
   function grettings ( $name=null )
   {
      if ( empty($name) ) $name = "who you are";
      return "Hello $name !";
   }

   static
   function byebye ( $name=null )
   {
      if ( empty($name) )
         $name = "who you are";

      return "Bye-bye $name !";
   }

   public
   function one ()
   {
      return 1;
   }

   protected
   function two ()
   {
      return rand(0,1) > 0 ? 2 : $this->one();
   }


}