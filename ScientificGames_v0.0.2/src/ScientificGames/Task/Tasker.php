<?php

namespace ScientificGames\Task;


use pocketmine\Player;
use pocketmine\scheduler\Task;
use ScientificGames\Main;

class Tasker extends Task
{
    private $plugin;
    private $player;

    public function __construct(Main $plugin,$player)
    {
        $this->plugin = $plugin;
        $this->player = $player;
    }

    public function onRun(int $CK)
    {
        if(in_array($this->player->getLevel()->getFolderName(),$this->plugin->world->get("worlds")))
        {
            $name = $this->player->getName();
            $nl = $this->plugin->getEnergy($name);
            $kkz = $this->plugin->getWater($name);
            $pxz = $this->plugin->getExcretion($name);
            $this->plugin->setEnergy($name,$nl-69);
            $this->plugin->setWater($name,$kkz-1);
            $this->plugin->setExcretion($name,$pxz+0.5);
        }
    }
}