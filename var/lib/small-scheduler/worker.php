<?php
/**
 * This file is a part of SmallScheduler
 * Copyright (c) 2019 Sébastien Kus
 * Under GNU GPL Licence
 */

// Includes
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__ . "/Config.php";

// Queue name
$queue = $argv[1];
$queueName = Config::QUEUE_PREFIX.$queue;

// Get config
$configClass = new Config();
$config = $configClass->getConfig();

// Initialize message broker
$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection($config["server"]["ip"], $config["server"]["port"], $config["server"]["user"], $config["server"]["password"]);
$channel = $connection->channel();
$channel->queue_declare($queueName, false, false, false, false);

// Callback
$callback = function ($msg) {
    global $queue;

    // Decode message
    $decodedMsg = json_decode($msg->body, true);

    // Execute command
    $pipes = array();
    $process = proc_open($decodedMsg["command"], array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w"),
    ), $pipes);

    // Get result
    $stdOut = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stdErr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $returnCode = proc_close($process);

    // Get config
    $configClass = new Config();
    $config = $configClass->getConfig();

    // Initialize message broker
    $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection($config["server"]["ip"], $config["server"]["port"], $config["server"]["user"], $config["server"]["password"]);
    $channel = $connection->channel();
    $channel->queue_declare(Config::QUEUE_CALLBACK, false, false, false, false);

    // Send callback message
    $callbackMessage = array(
        "id" => $decodedMsg["id"],
        "queue" => $queue,
        "command" => $decodedMsg["command"],
        "returnValue" => $returnCode,
        "stdout" => $stdOut,
        "stderr" => $stdErr,
    );
    $channel->basic_publish(new \PhpAmqpLib\Message\AMQPMessage(json_encode($callbackMessage)), "", Config::QUEUE_CALLBACK);

    // Close connection
    $channel->close();
    $connection->close();
};

// Consume
$channel->basic_consume($queueName, '', false, true, false, false, $callback);
while (count($channel->callbacks)) {
    if (!is_file(Config::FILE_LOCK)) {
        exit;
    }
    $channel->wait();
}