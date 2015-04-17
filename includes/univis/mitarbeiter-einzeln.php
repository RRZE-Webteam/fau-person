<?PHP
	require_once('class_controller.php');

	function fix_for_page($value){
	    $value = htmlspecialchars(trim($value));
	    if (get_magic_quotes_gpc()) 
	        $value = stripslashes($value);
	    return $value;
	}

	// FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
	$args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);

	$name  = substr($_SERVER["PATH_INFO"], 1, -6);
	$name = fix_for_page($name);
	$name = split("-", $name,2);

	$args["firstname"] = fix_for_page($name[0]);
	$args["lastname"] = fix_for_page($name[count($name)-1]);

	$controller = new Controller("mitarbeiter-einzeln", $args);
	echo $controller->ladeHTML();


?>