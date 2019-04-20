<?php


namespace ScientificGames;


use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\config;
use ScientificGames\Listeners;
use pocketmine\utils\TextFormat as C;
use ScientificGames\Commands;


class Main extends PluginBase
{
    public $tip, $config;

    /*
 生病系统
1. (当玩家被剑砍时)玩家被剑砍而感染开放性伤口有流血、虚弱、损伤效果
2.(当玩家从高处落下时)玩家腿摔断了需要接骨！
3.(当玩家被火烧时)玩家浴火纵身，玩家大量扣血至0死亡
4.(当玩家溺水时)玩家疲劳过度溺水了有虚弱,眩晕,疲劳效果
5.(当玩家窒息时)玩家窒息了导致无法呼吸,玩家大量扣血至0死亡
6.(当玩家饥饿值为0扣血时)玩家的血液血糖浓度太低导致饥饿,并且出现眩晕
7.(当玩家被方块爆炸伤害时,玩家大量扣血至0死亡
高原反应
1.当玩家去到高于80的地方(也就是坐标Y大于80时),会获得虚弱药水效果
2.当玩家去到高于100的地方(也就是坐标Y大于100时),会获得眩晕效果(反胃药水)
3.当玩家去到高于120的地方(也就是坐标Y大于120时),该玩家大量扣血至0死亡
食物类
1.不能食用土豆，否则中毒
2.不能食用金苹果,金萝卜,金西瓜，否则掉血6.5滴
3.不能食用生肉，否则几率中毒
钻木取火
当玩家手拿木棒点木头时,有几率擦出火花,点燃木棒
肾上腺素
当玩家使用龙息点地后,获得速度,挖掘速度加强,力量药水效果
打破方块
1.手撸木头掉血
2.打掉枯树枝,会掉落木棒
3.打掉草掉落,会小麦种子
4.用手打掉藤蔓,会掉落
5.用手打掉树叶,会掉落
6.打掉仙人掌,同时会掉落1-10个水瓶
7.玩家用剑(木,石,铁,金,钻石剑)打掉冰块,会掉落
8.玩家用剑(木,石,铁,金,钻石剑)或剪刀打掉玻璃或者玻璃片,会掉落
9.玩家用剑(木,石,铁,金,钻石剑)或剪刀打掉西瓜,会掉落整个西瓜
10.玩家打破蘑菇方块可得蘑菇物品
    */

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new Listeners($this), $this);//注册事件

        $this->getCommand("med")->setExecutor(new Commands($this), $this);//加载指令
        $this->getCommand("rlset")->setExecutor(new Commands($this), $this);

        @mkdir($this->getDataFolder(), 0777, true);//创建配置文件
        $this->config = new Config($this->getDataFolder() . "Config.yml", Config::YAML, array(
            "箱子保护" => true,
            "方块保护" => true,
            "admin" => [],
            "worlds" => []
        ));
        $this->getLogger()->info(C::AQUA . C::BOLD . "CaiBin 开发");
        $this->getLogger()->info(C::RED . C::BOLD . "真实生存插件§1§l---§e§lScientificGames §f§l已加载完成");
    }


}
