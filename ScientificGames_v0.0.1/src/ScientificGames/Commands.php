<?php


namespace ScientificGames;

use ScientificGames\Main;
use pocketmine\level\generator\Generator;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;

class Commands implements CommandExecutor
{

    private $plugin;
    private $Title = "§7-=§l§dScientificGames§r§7=-";

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function help(CommandSender $s)
    {
        $s->sendMessage(C::GRAY . "============== §7-=§l§dScientificGames§r§7=- ================");
        $s->sendMessage(C::WHITE . "/med                                 §8查看药物列表");
        $s->sendMessage(C::WHITE . "/rlset help                         §8查看帮助");
        $s->sendMessage(C::WHITE . "/rlset reload                       §8重载配置文件");
        $s->sendMessage(C::WHITE . "/rlset addworld [世界名称]          §3添加§8真实生存世界");
        $s->sendMessage(C::WHITE . "/rlset delworld [世界名称]          §3移除§8真实生存世界");
    }

    public function ill(CommandSender $s)
    {
        $s->sendMessage(C::GRAY . "==============§7-=§l§dScientificGames§r§7=-====================");
        $s->sendMessage(C::DARK_AQUA . "> 特效药   §b适合症状:§a眩晕  §d治疗方法:§e食用 §6物品:§f苹果 ");
        $s->sendMessage(C::GOLD . "> 骨头     §b适合症状:§a骨折  §d治疗方法:§e点地 §6物品:§f骨头");
        $s->sendMessage(C::WHITE . "> 南瓜派   §b适合症状:§a虚弱  §d治疗方法:§e食用 §6物品:§f南瓜派");
        $s->sendMessage(C::YELLOW . "> 士力架   §b适合症状:§a饥饿  §d治疗方法:§e食用 §6物品:§f面包");
        $s->sendMessage(C::GREEN . "> 曲奇     §b适合症状:§a疲劳  §d治疗方法:§e食用 §6物品:§f曲奇");
        $s->sendMessage(C::AQUA . "> 维生素A  §b适合症状:§a失明  §d治疗方法:§e食用 §6物品:§f胡萝卜");
        $s->sendMessage(C::RED . "> 云南白药 §b适合症状:§a中毒  §d治疗方法:§e点地 §6物品:§f纸");
    }
    public function onCommand(CommandSender $s, Command $command,  $label, array $args): bool
    {
        $Title = "§7-=§l§dScientificGames§r§7=-";
        switch ($command->getName()) {
            case "med":
                $this->ill($s);
                return true;
            case "rlset":
                if (count($args) == 0) {
                    $s->sendMessage($Title . " §c指令输入错误！");
                    $this->help($s);
                    return true;
                }
                if (!in_array($args[0], array("addworld", "delworld"))) {
                    $s->sendMessage($Title . " §c指令输入错误！");
                    $this->help($s);
                    return true;
                }
                if ($args[0] == "help") {
                    $this->help($s);
                    return true;
                }
                if (isset($args[0])) {
                    if ($args[0] == "reload") {
                        $this->plugin->config->reload();
                        $s->sendMessage($Title . "  §f所有配置重载完成");
                        return true;
                    }
                    if ($args[0] == "addworld") {
                        if (isset($args[1])) {
                            $levels = $this->plugin->config->get("worlds");
                            $level = $args[1];
                            if (!$this->plugin->getServer()->isLevelGenerated($level)) {
                                $s->sendMessage($Title . "  §a地图§6{$level}§a不存在！");
                                return true;
                            } else {
                                $levels[] = $level;
                                $this->plugin->config->set("worlds", $levels);
                                $this->plugin->config->save();
                                $s->sendMessage($Title . "  §6真实生存开启在世界§a$level");
                                return true;
                            }
                        } else {
                            $s->sendMessage($Title . "  §c未输入要添加的地图名");
                            $s->sendMessage($Title . "  §a用法: /rlset addworld [地图名]");
                            return true;
                        }
                    }
                    if ($args[0] == "delworld") {
                        if (isset($args[1])) {
                            $levels = $this->plugin->config->get("worlds");
                            $level = $args[1];
                            if (in_array($level, $levels)) {
                                $inv = array_search($level, $levels);
                                $inv = array_splice($levels, $inv, 1);
                                $this->plugin->config->set("worlds", $levels);
                                $this->plugin->config->save();
                                $s->sendMessage($Title . "  §6真实生存关闭在世界§a$level");
                                return true;
                            } else {
                                $s->sendMessage($Title . "  §6配置文件不存在真实生存世界§a{$level}§6,请检查后输入");
                                return true;
                            }
                        } else {
                            $s->sendMessage($Title . "  §c未输入要删除的地图名");
                            $s->sendMessage($Title . "  §a用法: /rlset delworld [地图名]");
                            return true;
                        }
                    }
                }
        }
    }
}