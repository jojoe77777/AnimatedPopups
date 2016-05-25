<?php

namespace AnimatedPopups;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{

    public function onEnable()
    {
        $this->saveDefaultConfig();
        $server = $this->getServer();
        $server->getPluginManager()->registerEvents($this, $this);
        $server->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $config = $this->getConfig()->getAll()), $config["interval"]);
    }

}