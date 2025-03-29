<?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    spl_autoload_register(function ($classname) {
        include __DIR__ . "/$classname.php";
    });

    $fitness_tracker = new FitnessTrackerController($_GET);

    $fitness_tracker->run();
