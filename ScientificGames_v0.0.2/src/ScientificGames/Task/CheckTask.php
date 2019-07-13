<?php

namespace ScientificGames\Task;


use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as C;
use pocketmine\entity\Effect;
use ScientificGames\Main;

class CheckTask extends Task
{
    private $plugin;
	private $player;

    public function __construct(Main $plugin,$player)
    {
        $this->plugin = $plugin;
		$this->player = $player;
    }

    private function PlayerStateCheckEnergy($name)
    {
        $ezzz = $this->plugin->getEnergy($name);
        if($ezzz <= 100 AND $ezzz > 45)
        {
            $this->plugin->ScientificGames_addEffect($this->player, Effect::SLOWNESS,  1, 20*60,true);
            $this->player->sendPopup(C::RED."你的能量只有10%了，行动缓慢,需要补充能量");
        }
        if($ezzz <= 45 AND $ezzz > 25)
        {
            $this->plugin->ScientificGames_addEffect($this->player, Effect::SLOWNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::MINING_FATIGUE,  1, 20*60,true);
            $this->player->sendPopup(C::RED."你的能量非常少了，行动缓慢且疲劳,需要马上能量");
        }
        if($ezzz <= 25 AND $ezzz > 5)
        {
            $this->plugin->ScientificGames_addEffect($this->player, Effect::SLOWNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::NAUSEA,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::WEAKNESS,  1, 20*60,true);
            $this->player->sendPopup(C::RED."你的能量只有2.5%了，行动缓慢，头晕，失明,需要立刻能量！！");
        }
        if($ezzz <= 5)
        {
            $this->plugin->ScientificGames_addEffect($this->player, Effect::BLINDNESS,  1, 20*60,true);
            $this->player->sendPopup(C::RED."你的能量不够了,失明了,你已经没有任何力气了");
            $this->player->sendPopup(C::RED."\n 你现在必须补充能量,否则将会死亡！！！！");
            $this->player->setHealth($this->player->getHealth() - 9);
        }
    }
    private function PlayerStateCheckThirst($name)
    {
        $ezz = $this->plugin->getWater($name);
        if($ezz <= 25 AND $ezz > 15)
        {
            $this->player->sendPopup(C::RED."你的水含量低于25%,身体很虚弱,急需补充水份！！");
            $this->plugin->ScientificGames_addEffect($this->player, Effect::SLOWNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::WEAKNESS,  1, 20*60,true);
        }
        if($ezz <= 15 AND $ezz > 7)
        {
            $this->player->sendPopup(C::RED."你现在非常渴,急需补充水份！！");
            $this->plugin->ScientificGames_addEffect($this->player, Effect::SLOWNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::WEAKNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::FATIGUE,  1, 20*60,true);
        }
        if($ezz <= 7 AND $ezz > 0)
        {
            $this->player->sendPopup(C::RED."你的水含量严重不足,必须要补充水份！！");
            $this->player->sendPopup(C::RED."");
            $this->plugin->ScientificGames_addEffect($this->player, Effect::BLINDNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::NAUSEA,  1, 20*60,true);
        }
        if($ezz <= 0)
        {
            $this->player->sendPopup(C::RED."你的水含量已达0,再不补充水份即将死亡！！！！！");
            $this->player->setHealth($this->player->getHealth() - 9);
        }
    }
    private function PlayerStateCheckExcretion($name)
    {
        $ez = $this->plugin->getExcretion($name);
        if($ez >= 17 AND  $ez < 25)
        {
            $this->plugin->ScientificGames_addEffect($this->player, Effect::SLOWNESS,  1, 20*60,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::NAUSEA,  1, 20*60,true);
            $this->player->sendPopup(C::RED."你现在膀胱要爆了,行走变慢,头晕，你需要马上排泄！！");
        }
        if($ez >= 25)
        {
            $this->player->sendMessage(C::RED."你膀胱已经爆了,死亡！！");
            $this->player->kill();
        }
    }
    private function PlayerStateCheckGlu($name)
    {
        $xt = $this->plugin->getGlu($name);
        $nl = $this->plugin->getEnergy($name);
        if($nl < 180 AND  $xt > 1)
        {
            $this->plugin->setEnergy($name,$nl+250);
            $this->plugin->setGlu($name,$xt-1);
            $this->player->sendPopup(C::RED."你的能量不足,已自动将血糖转换为能量");
        }
        if($xt <= 0)
        {
            $this->player->sendMessage(C::RED."你的血糖为零,已升天~~");
            $this->player->kill();
        }
    }
    private function PlayerStateCheckpH($name)
    {
        $ph = $this->plugin->getpH($name);
        if($ph <= 6.9)
        {
            $this->plugin->ScientificGames_addEffect($this->player, Effect::MINING_FATIGUE,  1, 20*60*2,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::NAUSEA,  1, 20*60*2,true);
            $this->plugin->ScientificGames_addEffect($this->player, Effect::WEAKNESS,  1, 20*60*2,true);
            $this->player->sendPopup(C::RED."你的体质显酸性,患上了各类疾病,你需要补碱性食物！");
        }
    }

    public function onRun(int $CK)
	{
        if(in_array($this->player->getLevel()->getFolderName(),$this->plugin->world->get("worlds")))
        {
            $name = $this->player->getName();
            $this->PlayerStateCheckThirst($name);
            $this->PlayerStateCheckEnergy($name);
            $this->PlayerStateCheckExcretion($name);
            $this->PlayerStateCheckGlu($name);
            $this->PlayerStateCheckpH($name);
        }
    }
}