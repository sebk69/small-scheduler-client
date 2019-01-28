<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 23/01/19
 * Time: 09:58
 */

class Config
{
    const CONFIG_FILENAME = "/etc/small-scheduler.json";
    const QUEUE_PREFIX = "SmallScheduler#";
    const QUEUE_CALLBACK = "SmallSchedulerCallback";
    const ROOT_PATH = "/var/lib/small-scheduler";
    const FILE_LOCK = "/var/lib/small-scheduler/lock";


    protected $config;

    public function __construct()
    {
        $this->config = json_decode(file_get_contents(static::CONFIG_FILENAME), true);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
