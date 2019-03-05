<?php
/**
 * This file is a part of SmallScheduler
 * Copyright (c) 2019 Sébastien Kus
 * Under GNU GPL Licence
 */
// Includes
require_once __DIR__ . "/Config.php";

// Get config
$configObject = new Config();
$config = $configObject->getConfig();

process($config, $argv);

function process($config, $argv)
{
    // What to do ?
    switch ($argv[1]) {
        case "start":
            echo "Starting...\n";
	    // Try lock
            if (file_exists(Config::FILE_LOCK) && (!isset($argv[2]) || $argv[2] != "--force")) {
                echo "Service already started\n";
                exit;
            }

            if (!is_dir(Config::ROOT_PATH)) {
                mkdir(Config::ROOT_PATH);
            }

            // Starting
            start($config);
            echo "Done\n";
            break;

        case "stop":
            echo "Stopping...\n";
            if (is_file(Config::FILE_LOCK)) {
                // Get pids and kill them
                $pids = json_decode(file_get_contents(Config::FILE_LOCK), true);
                foreach ($pids as $pid) {
                    $cmd = "kill -9 " . $pid;
                    exec($cmd);
                }

                // Remove lock
                unlink(Config::FILE_LOCK);
            } else {
                echo "Service is already stopped";
            }
            break;

        case "restart":
            process($config, array($argv[0], "stop"));
            process($config, array($argv[0], "start"));
            break;
    }
}

/**
 * Start client
 * @param $config
 */
function start($config)
{
    $pids = array();
    foreach ($config["workers"] as $queueConfig) {
        for ($i = 0; $i < $queueConfig["number"]; $i++) {
            $pids[] = exec("php ".__DIR__."/worker.php ".$queueConfig["queue"]." > /dev/null 2>&1 & echo $!");
        }
    }

    file_put_contents(Config::FILE_LOCK, json_encode($pids));
}
