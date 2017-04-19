<?php

/*
 * get current uri
 */
function app_current_uri()
{
  $url='http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'];
  return $url;
}

/** translate functions **/
/*
 *translate ,but print directly
*/
function __t()
{
	//$translate=Frd::getGlobal("translate");
	global $am;

	$args=func_get_args();
	$num=func_num_args();

	if($num == 0)
	return '';

	$str=$args[0];
	if($num == 1)
	{
		return $am->getTranslation($str);
	}

	unset($args[0]);
	return $am->getTranslation($str, $args);
}

function __p()
{
	//$translate=Frd::getGlobal("translate");
	global $am;

	$args=func_get_args();
	$num=func_num_args();

	if($num == 0)
		return '';

	$str=$args[0];
	if($num == 1)
	{
		echo $am->getTranslation($str);
		return $am->getTranslation($str);

	}

	unset($args[0]);
	echo $am->getTranslation($str, $args);
	return $am->getTranslation($str, $args);
}
