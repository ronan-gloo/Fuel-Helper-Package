<?php

Autoloader::add_namespace('Helper', __DIR__.'/classes/');
Autoloader::add_core_namespace('Helper');

/*
* Allow users to extends Package classes from app:classes/helper/
* Class should Avoid *Helper suffix, and be namespaced in Helper.
* ie:
* namespace Helper;
* class Date extends DateHelper {}
*
*/

// convert class to procedural if asked
call_user_func(function(){
	
	include_once  __DIR__.'/config/bootstrap.php';
	
	$boot = null;
	
	if ($helper_bt['convert_to_procedural'] === true)
	{
		$boot = function($class) use($helper_bt) {
			
			$methods = get_class_methods($class);
			
			foreach ($methods as $method)
			{
				if (! in_array($method, $helper_bt['procedural_blacklist'][strtolower($class)]))
				{
					$alias = strtolower($class).'_'.$method;
			  	
			  	if (! function_exists($alias))
					{
						$command = "function ".$alias."() { "; 
						$command.= "\$args = func_get_args(); ";
						$command.= "return call_user_func_array(array(\"".$class."\", \"".$method."\"), \$args); }"; 
						@eval($command);
					}
				}
			}
		};
	}
	
	// init namespaces and class names
	$nspace = 'Helper\\';
	
	foreach ($helper_bt['classes'] as $key => $class)
	{
		$cpath = $helper_bt['override_folder'].strtolower($class).'.php';
		if ( ! file_exists($cpath) OR ! is_file($cpath))
		{
			class_alias($nspace.$key, $class);
		}
		else
		{
			Autoloader::add_class($nspace.$class, $cpath);
		}
		
		if ($boot) $boot($class);
	}
	
	$boot = null;

});
