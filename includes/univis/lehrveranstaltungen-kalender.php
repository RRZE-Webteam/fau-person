<?PHP
	require_once('class_controller.php');

	// FETCH $_GET OR CRON ARGUMENTS TO AUTOMATE TASKS
	$args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);

	$controller = new Controller("lehrveranstaltungen-kalender", $args);

	echo $controller->ladeHTML();
?>