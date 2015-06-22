<?PHP
	require_once('class_controller.php');

	// FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
	$args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);

	$args["id"] = substr($_SERVER["PATH_INFO"], 1, -6);

	$controller = new Controller("lehrveranstaltungen-einzeln", $args);
	echo $controller->ladeHTML();

?>