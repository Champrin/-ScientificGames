<?php

namespace ScientificGames\Event;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use ScientificGames\Main;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\entity\Effect;
use pocketmine\level\sound\PopSound;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\Wood;
use pocketmine\block\Wood2;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\block\Block;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\sound\DoorBumpSound;
use pocketmine\math\Vector3;

class BionicsEvent implements Listener
{
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onDamage(EntityDamageEvent $event)
    {
        if (in_array($event->getEntity()->getLevel()->getFolderName(), $this->plugin->world->get("worlds"))) {
            $player = $event->getEntity();
            if ($player instanceof Player) {
                $cause = $event->getCause();
                $health = $player->getHealth();
                if ($event instanceof EntityDamageByEntityEvent)//喷血效果
                {
                    $damager = $event->getDamager();
                    if ($damager instanceof Player) {
                        $player->getLevel()->addSound(new DoorBumpSound($player));
                        $player->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(152)));
                    }
                }
                if ($event instanceof EntityDamageByEntityEvent)//被砍伤
                {
                    $damager = $event->getDamager();
                    if ($damager instanceof Player) {
                        $item = $damager->getInventory()->getItemInHand()->getId();
                        if ($item === 272 OR $item === 283 OR $item === 276 OR $item === 267 OR $item === 268) {
                            $event->setCancelled(true);
                            $player->sendMessage("§b你被剑砍而感染开放性伤口 \n          §e有流血、虚弱、损伤效果");
                            $this->plugin->ScientificGames_addEffect($player, Effect::WEAKNESS, 1, 20 * 120, true);
                            $player->setHealth($health - 5);
                        }
                    }
                }
                if ($cause == EntityDamageEvent::CAUSE_FALL)//摔
                {
                    $this->plugin->ScientificGames_addEffect($player, Effect::SLOWNESS, 1, 20 * 120, true);
                    $player->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(155)));
                    $player->sendMessage("§a你腿摔断了 \n          §6需要接骨！");
                }
                if ($cause == EntityDamageEvent::CAUSE_DROWNING)//溺水
                {
                    $this->plugin->ScientificGames_addEffect($player, Effect::FATIGUE, 1, 20 * 120, true);
                    $this->plugin->ScientificGames_addEffect($player, Effect::WEAKNESS, 1, 20 * 120, true);
                    $this->plugin->ScientificGames_addEffect($player, Effect::NAUSEA, 1, 20 * 120, true);
                    $player->sendMessage("§e你疲劳过度溺水了 \n          §c有虚弱,眩晕,疲劳效果！");
                }
                if ($cause == EntityDamageEvent::CAUSE_STARVATION) //饥饿
                {
                    $this->plugin->ScientificGames_addEffect($player, Effect::HUNGER, 1, 20 * 120, true);
                    $this->plugin->ScientificGames_addEffect($player, Effect::NAUSEA, 1, 20 * 120, true);
                    $player->sendMessage("§6你的血液血糖浓度太低 \n          §a导致饥饿,并且出现眩晕!");
                }
                if ($cause == EntityDamageEvent::CAUSE_LAVA OR $cause == EntityDamageEvent::CAUSE_FIRE)//熔岩、烧
                {
                    $player->sendMessage("§c你浴火纵身 \n          §6直接死亡");
                    $player->setHealth($player->getHealth() - 9);
                    $player->setHealth($player->getHealth() - 8);
                    $player->setHealth($player->getHealth() - 3);
                    $player->kill();
                }
                if ($cause === EntityDamageEvent::CAUSE_SUFFOCATION)//窒息
                {
                    $player->sendMessage("§c你窒息了 \n          §6导致无法呼吸,直接死亡！");
                    $player->setHealth($player->getHealth() - 9);
                    $player->setHealth($player->getHealth() - 8);
                    $player->setHealth($player->getHealth() - 3);
                    $player->kill();
                }
                if ($cause == EntityDamageEvent::CAUSE_BLOCK_EXPLOSION)//方块爆炸(tnt)
                {
                    $player->sendMessage("§c你被tnt炸碎得粉身碎骨 \n          §6直接死亡！");
                    $player->setHealth($player->getHealth() - 9);
                    $player->setHealth($player->getHealth() - 8);
                    $player->setHealth($player->getHealth() - 3);
                    $player->kill();
                }
            }
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->world->get("worlds"))) {
            $player = $event->getPlayer();
            $inventory = $player->getInventory();
            $level = $player->getLevel();
            $item = $event->getItem()->getId();
            switch ($item) {
                case 280://钻木取火
                    if (($event->getBlock() instanceof Wood) OR ($event->getBlock() instanceof Wood2)) {
                        $num = mt_rand(0, 100);
                        if ($num <= 30) {
                            $level->addSound(new PopSound($player));
                            $inventory->removeItem(new Item(280, 0, 1));
                            $inventory->addItem(new Item(50, 0, 1));
                            unset($num);
                        }
                    }
            }
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->world->get("worlds"))) {
            $player = $event->getPlayer();
            $y = $player->getY();
            if ($y >= 80 AND $y <= 100) {
                $this->plugin->high_80++;
                if ($this->plugin->high_80 == 5) {
                    $this->plugin->ScientificGames_addEffect($player, Effect::NAUSEA, 1, 20 * 120, true);
                    $player->sendMessage("§b高原反应\n §6开始使你变得虚弱,再高一点甚至会出现眩晕状况!");
                }
            }
            if ($y >= 100) {
                $this->plugin->high_100++;
                if ($this->plugin->high_100 == 5) {
                    $this->plugin->ScientificGames_addEffect($player, Effect::WEAKNESS, 1, 20 * 120, true);
                    $player->sendMessage("§b高原反应\n §6你已经出现眩晕状况,不能再往上走了!可能直接让你死亡!");
                }
            }
            if ($y >= 120) {
                $this->plugin->high_120++;
                if ($this->plugin->high_120 == 5) {
                    $player->sendMessage("§b高原反应\n §6高地极度缺氧让你大量扣血至死亡!");
                    $player->kill();
                }
            }
            if ($y <= 80) {
                $this->plugin->high_80 = 0;
                $this->plugin->high_100 = 0;
                $this->plugin->high_120 = 0;
            }
        }
    }

}