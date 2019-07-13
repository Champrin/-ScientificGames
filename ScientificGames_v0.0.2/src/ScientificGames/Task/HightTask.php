<?php

namespace ScientificGames\Task;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use ScientificGames\Main;

class HightTask extends Task
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
            $player = $this->player;
            $name = $player->getName();
            $this->plugin->setAge($name,$this->plugin->getAge($name)+1);
            if($this->plugin->getAge($name) <= 21)
            {
                $this->plugin->setHight($name,$this->plugin->getHight($name)+0.05);
                $this->plugin->PlayerHight($name);
            }
        }
    }
}