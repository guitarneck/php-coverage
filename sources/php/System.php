<?

namespace coverage\php;

class System
{
   static
   function devnul ()
   {
      return PHP_OS !== 'WINNT' ? '/dev/null' : 'nul';
   }

   static
   function curdir ()
   {
      return getcwd();
   }
}