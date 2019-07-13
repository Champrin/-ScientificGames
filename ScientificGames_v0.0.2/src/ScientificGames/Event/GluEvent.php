<?php
namespace ScientificGames\Event;


use pocketmine\event\Listener;
use ScientificGames\Main;
use pocketmine\entity\Effect;
use pocketmine\event\player\PlayerBedLeaveEvent;

class GluEvent implements Listener
{

    private $plugin;
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onLeaveBed(PlayerBedLeaveEvent $event)
    {
        if(in_array($event->getPlayer()->getLevel()->getFolderName(),$this->plugin->world->get("worlds")))
        {
            $player = $event->getPlayer();
            $name = $player->getName();
            $a = $this->plugin->getEnergy($name);
            $this->plugin->setEnergy($name,$a-120);
            $xt = $this->plugin->getGlu($name);
            if($a-120 <= 0)
            {
                $player->sendMessage("  §e你由于能量过低,已在睡梦中死去,一睡不醒！");
                $player->kill();
            }
            if($a-120 <= 60)
            {
                $player->sendMessage("  §a你刚起床时能量过低 \n  §e会有短时间眩晕和虚弱效果");
                $this->plugin->ScientificGames_addEffect($player, Effect::NAUSEA,  1, 20*20,true);
                $this->plugin->ScientificGames_addEffect($player, Effect::WEAKNESS,  1, 20*20,true);
            }
            if($xt < 3)
            {
                $player->sendMessage("  §a你刚起床时能量过低 \n  §e会有短时间眩晕和虚弱效果");
                $this->plugin->ScientificGames_addEffect($player, Effect::NAUSEA,  1, 20*20,true);
                $this->plugin->ScientificGames_addEffect($player, Effect::WEAKNESS,  1, 20*20,true);
            }
        }
    }

}