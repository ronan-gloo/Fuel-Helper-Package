# Fuel Helper Package

Idea behind this package is to provide a set of configuration driven helpers.

Each helper and helper's methods have their own pre-configured parameters in a specific config file. That mean you can standardize methods behavior to feet your needs.

### overriding configurations and languages

Originals config files are located into /packages/helper/config/
Thank's to the fuel Core config loader, simply copy this file into app/config and start to edit.

### extending classes

You can extend each helper from your app. Classes folder is by default: ``app/classes/helper``

You can modify this value in config/bootstrap.php.
Package class names are suffixed with Helper (i.e.: TextHelper')
You classes should be named as helper classes alias. 

i.e. for app/classes/helper/number.php

``namespace Helper; 
class Number extends NumberHelper {}``

### Register classes into autoloader

You can set register class in config/bootstrap.php.
That mean's you can remove or add you own classes into the package.

### Classes methods to procedural functions (experimental)

In config/bootstrap.php, set 'convert_to_procedural' to true to tell the package bootstrap to create procedural function into global namespace.

i.e.:
``Number::to_human()``
will be accessible under
``number_to_human()``

