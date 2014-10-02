<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/8/14
 * Time: 5:48 PM
 */

spl_autoload_extensions( '.php , .class.php' );
function my_autoload($class) {
	$classSepIndex = strrpos($class, "\\");
	$classPath = str_replace("\\", "/", strtolower(substr($class, 0, $classSepIndex)));
	$className = substr($class, $classSepIndex + 1);

	if (file_exists(dirname(__FILE__) . "/" . $classPath . "/" . $className . ".class.php")) {
		include_once(dirname(__FILE__) . "/" . $classPath . "/" . $className . ".class.php");
	} else {
		echo 'class ' . $class . ' could not be found in '.dirname(__FILE__) . "/" . $classPath . "/" . $className . ".class.php".".\n";
		exit;
	}

}
spl_autoload_register("my_autoload");

// redirect to user interface
$query = $_SERVER['PHP_SELF'];
if (\Model\Common\Globals::endsWith($query, "index.php")) {
	header('Location: ./interface/step1.php');
}