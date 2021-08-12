<?php

namespace EffechantForm;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\command\{Command, CommandSender};

use onebone\economyapi\EconomyAPI;



class Main extends PluginBase implements Listener {

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        if (!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder(), 0744, true);
        }
            $this->EF = new Config($this->getDataFolder() . "EnchantMoney.yml", Config::YAML,array(
              '説明' => '名前(数字)の数字はレベルのこと。　100とかは値段',
              '効率強化3' => '500',
              '効率強化5' => '1000',
              '耐久力3' => '2000',
              '耐久力5' => '3000'
            ));
            $this->Economy = EconomyAPI::getInstance();
      }
      //API /*==========================================================================================================================*/

  public function sendForm(Player $player, $title, $come, $buttons, $id) {
  $pk = new ModalFormRequestPacket(); 
  $pk->formId = $id;
  $this->pdata[$pk->formId] = $player;
  $data = [ 
  'type'    => 'form', 
  'title'   => $title, 
  'content' => $come, 
  'buttons' => $buttons 
  ]; 
  $pk->formData = json_encode( $data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_UNESCAPED_UNICODE );
  $player->dataPacket($pk);
  $this->lastFormData[$player->getName()] = $data;
  }

 
      public function startMenu($player) {
    
        $name = $player->getName();
          
        $buttons[] = [ 
        'text' => "効率強化", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //0

        $buttons[] = [ 
        'text' => "耐久力", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //1
         
        $this->sendForm($player,"エンチャント選択","\n\n",$buttons,2001);
        $this->info[$name] = "form";
        }

        public function endMenu($player) {
        $name = $player->getName();
        $buttons[] = [ 
        'text' => "戻る", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //0
        $this->sendForm($player,"","お金が足りなく、購入に失敗しました。\n\n\n\n\n\n\n",$buttons,9999);
        $this->info[$name] = "form";
        }

        public function endMenu2($player) {
        $name = $player->getName();
        $buttons[] = [ 
        'text' => "戻る", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //0
        $this->sendForm($player,"","Enchantを購入することに成功しました！\n\n\n\n\n\n\n",$buttons,9998);
        $this->info[$name] = "form";
        }

        public function endMenu3($player) {
        $name = $player->getName();
        $buttons[] = [ 
        'text' => "戻る", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //0
        $this->sendForm($player,"","Effectを購入することに成功しました！\n\n\n\n\n\n\n",$buttons,9997);
        $this->info[$name] = "form";
        }
        
        public function endMenu4($player) {
        $name = $player->getName();
        $buttons[] = [
        'text' => "戻る",
        'image' => [ 'type' => 'path', 'data' => "" ]
        ]; //0
        $this->sendForm($player,"","手に持っているアイテムの修復に成功しました！\n\n\n\n\n\n\n",$buttons,9996);
        $this->info[$name] = "form";
        }
        
        public function errorMenu($player) {
        $name = $player->getName();
        $buttons[] = [
        'text' => "戻る",
        'image' => [ 'type' => 'path', 'data' => "" ]
        ]; //0
        $this->sendForm($player,"","手に持っているアイテムは修復済みです。\n\n\n\n\n\n\n",$buttons,9995);
        $this->info[$name] = "form";
        }


      public function onPrecessing(DataPacketReceiveEvent $event){

  $player = $event->getPlayer();
  $pk = $event->getPacket();
  $name = $player->getName();
  $money = EconomyAPI::getInstance()->myMoney($name);
    if($pk->getName() == "ModalFormResponsePacket"){
      $data = $pk->formData;
      if($data == "null\n"){
      }else{
          switch($pk->formId){
          case 2001:
        if($data == 0){//効率強化

            $buttons[] = [ 
            'text' => "3Lv.", 
            ]; //0

            $buttons[] = [ 
            'text' => "5Lv.", 
            ]; //1
            
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2200);

        }elseif($data == 1){//耐久力
            
            $buttons[] = [ 
            'text' => "3Lv.", 
            ];
            //0
            
            $buttons[] = [ 
            'text' => "5Lv.", 
            ]; //1
            
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2400);
        }
        break;

          case 2200:
          if($data == 0){//効率強化Lv3
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化3Lv\n値段:".$this->EF->get("効率強化3")."$",$buttons,2203);
      }elseif($data == 1){//効率強化Lv5
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化5Lv\n値段:".$this->EF->get("効率強化5")."$",$buttons,2205);
      }
          break;
                  
          case 2203:
            if($data == 0){//効率強化Lv3
            if($money >= $this->EF->get("効率強化3")){
              $this->Economy->reduceMoney($name,$this->EF->get("効率強化3"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15),3));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

             case 2205:
             if($data == 0){//効率強化Lv5
              if($money >= $this->EF->get("効率強化5")){
                $this->Economy->reduceMoney($name,$this->EF->get("効率強化5"));
                $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(15),5));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
          }else{
            $this->endMenu($player);
          }
        }
        break;
              
        case 2400:
        if($data == 0){//耐久力Lv3
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","耐久力3Lv\n値段:".$this->EF->get("耐久力3")."$",$buttons,2403);
      }elseif($data == 1){//耐久力Lv5
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","耐久力5Lv\n値段:".$this->EF->get("耐久力5")."$",$buttons,2405);
        }
          break;

          case 2403:
            if($data == 0){//耐久力Lv3
            if($money >= $this->EF->get("耐久力3")){
              $this->Economy->reduceMoney($name,$this->EF->get("耐久力3"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17),3));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;
           
          case 2405:
            if($data == 0){//耐久力Lv5
            if($money >= $this->EF->get("耐久力5")){
              $this->Economy->reduceMoney($name,$this->EF->get("耐久力5"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17),5));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;
         
  }
}
}
}



    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
          $name = $sender->getName();
          switch($cmd->getName()){
          case "ef":
          if(strtolower(implode($args)) == "help"){
            $buttons[] = [ 
            'text' => "閉じる", 
            ]; //0
          $this->sendForm($sender,"説明","コマンド一覧\n/efで説明！\n/ef enchantでエンチャントショップを開きます！\n\n\n\n\n\n",$buttons,1000);
          break;
          }elseif(strtolower(implode($args)) == "enchant"){
          $this->startMenu($sender);
          break;
          }
          default:
          $buttons[] = [ 
            'text' => "閉じる", 
            ]; //0
          $this->sendForm($sender,"説明","コマンド一覧\n/efで説明！\n/ef enchantでエンチャントショップを開きます！\n\n\n\n\n\n",$buttons,1001);
          break;
        
          }

           return true;
         
          }
          }
