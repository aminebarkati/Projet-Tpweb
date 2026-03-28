
<?php
function autoload($className)
{
   // echo "From autolad je vais essayer de charger $className";
   include_once "class/{$className}.php";
}

spl_autoload_register('autoload');
