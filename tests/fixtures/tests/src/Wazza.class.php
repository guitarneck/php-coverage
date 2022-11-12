<?php

const WAZZA_CONST = 'Tony';

function wazza_func ()
{
   return Wazza::grettings();
}

class Wazza
{
   static
   function grettings ( $name=null )
   {
      // branches cover diff
      if ( empty($name) )
         $gret = "Wazza who you are !";
      else
      {
         $name = $name;
         $gret = "Wazza $name !";
      }
      return $gret;
   }
}