<?php

namespace ScientificGames;

use pocketmine\block\Fire;
use pocketmine\block\Stone;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use ScientificGames\Main;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\item\Item;

use pocketmine\block\Block;
use pocketmine\block\DeadBush;
use pocketmine\block\TallGrass;
use pocketmine\block\Cactus;
use pocketmine\block\Leaves;
use pocketmine\block\Leaves2;
use pocketmine\block\Melon;
use pocketmine\block\Ice;
use pocketmine\block\PackedIce;
use pocketmine\block\Glass;
use pocketmine\block\GlassPane;
use pocketmine\block\Wood;
use pocketmine\block\BrownMushroomBlock;
use pocketmine\block\RedMushroomBlock;
use pocketmine\block\Vine;
use pocketmine\block\Wood2;

use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\GhastShootSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\level\sound\DoorBumpSound;

use pocketmine\math\Vector3;

/*new function
            1.玩家手持石头去点击石头，有几率擦出火花*
            2.木棒点击火 得火柴*
            */

/*使人变缓慢
 * $attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() * (1 - 0.15 * $instance->getEffectLevel()), true);
	public const KNOCKBACK_RESISTANCE = 3; // 阻力
 * */

class Listeners implements Listener
{

    private $plugin;
    private $Title = "§7-=§l§dScientificGames§r§7=-";
    private $high_80 = 0, $high_100 = 0, $high_120 = 0;
    private $cbs_x = 0, $cbs_y = 0, $cbs_z = 0;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function mt_randCheck($number)
    {
        $num = mt_rand(0, 100);
        if ($num <= $number) {
            return true;
        } else {
            return false;
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->config->get("worlds"))) {
            $player = $event->getPlayer();
            $inventory = $player->getInventory();
            $level = $player->getLevel();

            $item = $event->getItem()->getId();
            $damage = $event->getItem()->getDamage();
            $name = $player->getName();
            $block = $event->getBlock();
            $health = $player->getHealth();

            switch ($item) {
                case Item::AIR:
                    if ($block instanceof Wood) {
                        if ($item == "0") {
                            $player->setHealth($health - 1);
                            $player->sendMessage(" §7手撸木头,会伤害手哦~~");
                        }
                    }
                    if ($block instanceof Stone) {
                        if ($item == "0") {
                            $player->setHealth($health - 1);
                            $player->sendMessage(" §7手撸石头,会伤害手哦~~");
                        }
                    }
                    break;
                case Item::STONE:
                    if ($block instanceof Stone) {
                        if ($this->mt_randCheck(30) == true) {
                            $v3 = new Vector3($block->getX(), $block->getY() + 1, $block->getZ());
                            $block->getLevel()->setBlock($v3, Block::get(51));
                        }
                    }
                    break;
                case Item::STICK://钻木取火&击火得火柴50 木棒280
                    if (($block instanceof Wood) OR ($block instanceof Wood2)) {
                        if ($this->mt_randCheck(30) == true) {
                            $level->addSound(new PopSound($player));
                            $inventory->removeItem(new Item(Item::STICK, 0, 1));
                            $inventory->addItem(new Item(Item::TORCH, 0, 1));
                        }
                    }
                    if ($block instanceof Fire) {
                        $inventory->removeItem(new Item(Item::STICK, 0, 1));
                        $inventory->addItem(new Item(Item::TORCH, 0, 1));
                    }
                    break;
                case Item::DRAGON_BREATH://肾上腺素 Item:龙息439
                    $level->addSound(new GhastShootSound($player));
                    $player->sendMessage("§a你打了肾上激素\n    §6加强速度,挖掘速度,力量");
                    $this->ScientificGames_addEffect($player, Effect::SPEED, true, 3, 20 * 60 * 2);
                    $this->ScientificGames_addEffect($player, Effect::HASTE, true, 3, 20 * 60 * 2);
                    $this->ScientificGames_addEffect($player, Effect::STRENGTH, true, 3, 20 * 60 * 2);
                    $player->getInventory()->removeItem(new Item(Item::DRAGON_BREATH, 0, 1));
                    $inventory->addItem(new Item(Item::POTION, 0, 1));
                    break;
                case Item::BONE://骨头352
                    if ($player->hasEffect(Effect::SLOWNESS)) {
                        $level->addSound(new AnvilFallSound($player));
                        $player->removeEffect(Effect::SLOWNESS);
                        $inventory->removeItem(new Item(Item::BONE, 0, 1));
                        $player->sendMessage( "§6 你已换骨");
                    }
                    break;
                case Item::PAPER://纸339
                    if ($player->hasEffect(Effect::POISON)) {
                        $level->addSound(new AnvilFallSound($player));
                        $player->removeEffect(Effect::POISON);
                        $inventory->removeItem(new Item(Item::PAPER, 0, 1));
                        $player->sendMessage( "§e 你已排毒");
                    }
                    break;
            }

        }
    }

    public function OnBreak(BlockBreakEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->config->get("worlds"))) {
            $player = $event->getPlayer();
            $item = $player->getInventory()->getItemInHand()->getId();
            $block = $event->getBlock();
            $health = $player->getHealth();
            if ($block instanceof Wood) {
                if ($item == "0") {
                    $player->setHealth($health - 1);
                    $player->sendMessage(" §7手撸木头,会伤害手哦~~");
                }
            }
            if ($block instanceof Stone) {
                if ($item == "0") {
                    $player->setHealth($health - 1);
                    $player->sendMessage(" §7手撸石头,会伤害手哦~~");
                }
            }
            if ($block instanceof TallGrass) {
                $event->setDrops(array(Item::get(295, 0, 1)));
            }
            if ($block instanceof DeadBush) {
                $event->setDrops(array(Item::get(280, 0, 1)));
            }
            if ($block instanceof Vine) {
                if ($item == "0") {
                    $event->setDrops(array(Item::get(106, 0, 1)));
                }
            }
            if (($block instanceof Leaves) OR ($block instanceof Leaves2)) {
                if ($item == "0") {
                    $blockid = $block->getId();
                    $blockide = $block->getDamage();
                    $event->setDrops(array(Item::get($blockid, $blockide, 1)));
                }
            }
            if ($block instanceof Cactus) {
                if ($item == "0") {
                    $player->setHealth($health - 1);
                    $player->sendMessage(" §7手撸仙人掌,会伤害手哦~~");
                    $num = mt_rand(0, 10);
                    $event->setDrops(array(Item::get(373, 0, $num)));
                    $player->sendMessage(" §a你在仙人掌里找到了§e{$num}§a瓶水~~");
                } else {
                    $num = mt_rand(0, 10);
                    $event->setDrops(array(Item::get(373, 0, $num)));
                    $player->sendMessage(" §a你在仙人掌里找到了§e{$num}§a瓶水~~");
                }
            }
            if ($block instanceof Melon) {
                if ($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359") {
                    $event->setDrops(array(Item::get(103, 0, 1)));
                }
            }
            if ($block instanceof Ice) {
                if ($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268") {
                    $event->setDrops(array(Item::get(79, 0, 1)));
                }
            }
            if ($block instanceof PackedIce) {
                if ($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268") {
                    $event->setDrops(array(Item::get(174, 0, 1)));
                }
            }
            if ($block instanceof Glass) {
                if ($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359") {
                    $event->setDrops(array(Item::get(20, 0, 1)));
                }
            }
            if ($block instanceof GlassPane) {
                if ($item == "267" || $item == "272" || $item == "283" || $item == "276" || $item == "268" || $item == "359") {
                    $event->setDrops(array(Item::get(102, 0, 1)));
                }
            }
            if ($block instanceof BrownMushroomBlock) {
                $event->setDrops(array(Item::get(39, 0, 1)));
            }
            if ($block instanceof RedMushroomBlock) {
                $event->setDrops(array(Item::get(40, 0, 1)));
            }
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->config->get("worlds"))) {
            $player = $event->getPlayer();
            $name = $player->getName();
            $block = $player->getLevel()->getBlock($player->floor()->subtract(0, 1))->getId();

            $y = $player->getY();
            if ($y >= 80 AND $y <= 100) {
                $this->high_80++;
                if ($this->high_80 == 5) {
                    $this->ScientificGames_addEffect($player, Effect::NAUSEA, true, 0, 20 * 120);
                    $player->sendMessage("§b高原反应 \n   §6开始使你变得虚弱,再高一点甚至会出现眩晕状况!");
                }
            }
            if ($y >= 100) {
                $this->high_100++;
                if ($this->high_100 == 5) {
                    $this->ScientificGames_addEffect($player, Effect::WEAKNESS, true, 0, 20 * 120);
                    $player->sendMessage("§b高原反应 \n   §6你已经出现眩晕状况,不能再往上走了!可能直接让你死亡!");
                }
            }
            if ($y >= 120) {
                $this->high_120++;
                if ($this->high_120 == 5) {
                    $player->sendMessage("§b高原反应 \n   §6高地极度缺氧让你大量扣血至死亡!");
                    $player->kill();
                }
            }
            if ($y <= 80) {
                $this->high_80 = 0;
                $this->high_100 = 0;
                $this->high_120 = 0;
            }
        }
    }

    public function onHeld(PlayerItemHeldEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->config->get("worlds"))) {
            $name = $event->getPlayer()->getName();

            $player = $event->getPlayer();
            $item = $event->getItem();
            $item_id = $item->getId();

            switch ($item_id) {
                case 260://苹果
                    $player->sendMessage("§a>§3  特效药 §b适合症状:眩晕 §d治疗方法：食用 §6物品：苹果");
                    break;
                case 352://骨头
                    $player->sendMessage("§a>§6  骨头 §b适合症状:骨折 §d治疗方法：点地 §6物品：骨头");
                    break;
                case 400: //南瓜派
                    $player->sendMessage("§a>§f  南瓜派 §b适合症状:虚弱 §d治疗方法：点地 §6物品：南瓜派");
                    break;
                case 297: //面包
                    $player->sendMessage("§a>§e  士力架 §b适合症状:饥饿 §d治疗方法：食用 §6物品：面包");
                    break;
                case 357://曲奇
                    $player->sendMessage("§a>§9  曲奇 §b适合症状:疲劳 §d治疗方法：食用 §6物品：曲奇");
                    break;
                case 391://胡萝卜
                    $player->sendMessage("§a>§b  维生素A §b适合症状:失明 §d治疗方法：食用 §6物品：胡萝卜");
                    break;
                case 339://纸
                    $player->sendMessage("§a>§c  云南白药§b适合症状:中毒 §d治疗方法：点地  §6物品：纸");
                    break;
                case 437://龙息
                    $player->sendMessage("§a>§6  肾上腺素 §b加强速度,挖掘速度,力量");
                    break;
                case 466://金苹果
                case 322://金苹果
                case 396://金萝卜
                case 382://金西瓜
                    $player->sendMessage("§a你想磕掉呀吗2333");
                    break;
                case 349://生鱼
                case 319://生猪
                case 365://生鸡
                case 423://生羊
                case 363://生牛
                case 411://生兔
                    $player->sendMessage("§a食用生肉可能会中毒哦~~");
                    break;
            }
        }
    }

    public function onPlayerEat(PlayerItemConsumeEvent $event)
    {
        if (in_array($event->getPlayer()->getLevel()->getFolderName(), $this->plugin->config->get("worlds"))) {
            $name = $event->getPlayer()->getName();

            $player = $event->getPlayer();
            $item = $event->getItem();
            $itemid = $item->getId();
            $damage = $item->getDamage();

            switch ($itemid) {
                case 260: //苹果---特效药
                    if ($player->hasEffect(Effect::NAUSEA)) {
                        $player->removeEffect(Effect::NAUSEA);
                        $player->sendMessage( "  §a已缓解头晕！");
                    }
                    break;
                case 297://面包---士力架
                    if ($player->hasEffect(Effect::HUNGER)) {
                        $player->removeEffect(Effect::HUNGER);
                        $player->sendMessage( "  §e横扫饥饿，做回自己！");
                    }
                    break;
                case 357://曲奇
                    if ($player->hasEffect(Effect::FATIGUE)) {
                        $player->removeEffect(Effect::FATIGUE);
                        $player->sendMessage( "  §2已缓解疲劳！");
                    }
                    break;
                case 400://南瓜派
                    if ($player->hasEffect(Effect::WEAKNESS)) {
                        $player->removeEffect(Effect::WEAKNESS);
                        $player->sendMessage( "  §6强身健体");
                    }
                    break;
                case 391://胡萝卜---维生素A
                    if ($player->hasEffect(Effect::BLINDNESS)) {
                        $player->removeEffect(Effect::BLINDNESS);
                        $player->sendMessage( "  §3成功治疗失明！");
                    }
                    break;
                case 392://马铃薯--中毒
                    $this->ScientificGames_addEffect($player, Effect::POISON, true, 0, 20 * 120);
                    $player->sendMessage("§e你中毒了 \n     §a因为你生吃了马铃薯");
                    break;
                case 466://金苹果
                case 322:
                case 396://金萝卜
                case 382://金西瓜
                    $player->setHealth($player->getHealth() - 13);
                    $player->sendMessage("敢吃金子做的食物？？？\n      §e你的牙被磕掉,造成大量流血");
                    break;
                case 319://生猪肉
                case 365://生鸡肉
                case 423://生羊肉
                case 363://生牛肉
                case 411://生兔肉
                case 349://生鱼肉
                    if ($this->mt_randCheck(30) == true) {
                        $this->ScientificGames_addEffect($player, Effect::POISON, true, 0, 20 * 120);
                        $this->ScientificGames_addEffect($player, Effect::WEAKNESS, true, 0, 20 * 120);
                        $this->ScientificGames_addEffect($player, Effect::NAUSEA, true, 0, 20 * 120);
                        $player->sendMessage("§b竟然敢吃生肉？？不怕得病？？\n      §c恭喜你得病了");
                    }
                    break;
            }
        }
    }
//id 时间 等级 开启
    private function ScientificGames_addEffect($player, $id,  $amplifier, $Duration,$visible)
    {
        $player->addEffect(new EffectInstance(Effect::getEffect($id),$Duration,$amplifier,$visible));
    }

    public function onDamage(EntityDamageEvent $event)
    {
        if (in_array($event->getEntity()->getLevel()->getFolderName(), $this->plugin->config->get("worlds"))) {
            if ($event->getEntity() instanceof Player) {
                $player = $event->getEntity();
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
                    $item = $event->getDamager()->getInventory()->getItemInHand()->getId();
                    if ($item === 272 OR $item === 283 OR $item === 276 OR $item === 267 OR $item === 268) {
                        $event->setCancelled(true);
                        $player->sendMessage("§b你被剑砍而感染开放性伤口 \n          §e有流血、虚弱、损伤效果");
                        $this->ScientificGames_addEffect($player, Effect::WEAKNESS, true, 0, 20 * 120);
                        $player->setHealth($health - 5);
                    }
                }
                if ($cause == EntityDamageEvent::CAUSE_FALL)//摔
                {
                    $this->ScientificGames_addEffect($player, Effect::SLOWNESS, true, 0, 20 * 120);
                    $player->getLevel()->addParticle(new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), Block::get(155)));
                    $player->sendMessage("§a你腿摔断了 \n          §6需要接骨！");
                }
                if ($cause == EntityDamageEvent::CAUSE_DROWNING)//溺水
                {
                    $this->ScientificGames_addEffect($player, Effect::FATIGUE, true, 0, 20 * 120);
                    $this->ScientificGames_addEffect($player, Effect::WEAKNESS, true, 0, 20 * 120);
                    $this->ScientificGames_addEffect($player, Effect::NAUSEA, true, 0, 20 * 120);
                    $player->sendMessage("§e你疲劳过度溺水了 \n          §c有虚弱,眩晕,疲劳效果！");
                }
                if ($cause == EntityDamageEvent::CAUSE_STARVATION) //饥饿
                {
                    $this->ScientificGames_addEffect($player, Effect::HUNGER, true, 0, 20 * 120);
                    $this->ScientificGames_addEffect($player, Effect::NAUSEA, true, 0, 20 * 120);
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

}