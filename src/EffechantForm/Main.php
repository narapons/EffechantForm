<?php

namespace EffchantForm;

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
        $this->getLogger()->info("§aEffchantFormを読み込みました! by mixpowder");
        if (!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder(), 0744, true);
        }
            $this->EF = new Config($this->getDataFolder() . "EnchantMoney.yml", Config::YAML,array(
              '説明' => '名前(数字)の数字はレベルのこと。　100とかは値段',
              '防護1' => '100',
              '防護2' => '200',
              '防護3' => '300',
              '防護4' => '400',
              '防護5' => '500',
              '効率強化1' => '100',
              '効率強化2' => '200',
              '効率強化3' => '300',
              '効率強化4' => '400',
              '効率強化5' => '500',
              'シルクタッチ1' => '100',
              '耐久力1' => '100',
              '耐久力2' => '200',
              '耐久力3' => '300',
              '幸運' => '100',
              'エフェクト説明' => '時間は20×秒数のを書く。',
              'スピード1' => '100',
              'スピード1のエフェクト時間' => '3600',
              'スピード2' => '200',
              'スピード2のエフェクト時間' => '1200',
              '採掘速度上昇1' => '100',
              '採掘速度上昇1のエフェクト時間' => '3600',
              '採掘速度上昇2' => '200',
              '採掘速度上昇2のエフェクト時間' => '1200',
              '暗視' => '100',
              '暗視のエフェクト時間' => '3600',
              '修復' => '5000'
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
        'text' => "防護", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //0
        $buttons[] = [ 
        'text' => "効率強化", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //1
        $buttons[] = [ 
        'text' => "シルクタッチ", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //2
        $buttons[] = [ 
        'text' => "耐久力", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //3
        $buttons[] = [
        'text' => "幸運 (ベータ)",
        'image' => [ 'type' => 'path', 'data' => "" ]
        ];//4
        $this->sendForm($player,"エンチャント選択","\n\n",$buttons,2001);
        $this->info[$name] = "form";
        }
        
      public function startMenu2($player) {
        $name = $player->getName();
        $buttons[] = [ 
        'text' => "スピード", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //0
        $buttons[] = [ 
        'text' => "採掘速度上昇", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //1
        $buttons[] = [ 
        'text' => "暗視", 
        'image' => [ 'type' => 'path', 'data' => "" ] 
        ]; //2
        $this->sendForm($player,"エフェクト選択","\n\n",$buttons,3000);
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
          if($data == 0){//防護
         $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
            $buttons[] = [ 
            'text' => "2Lv.", 
            ]; //1
            $buttons[] = [ 
            'text' => "3Lv.", 
            ]; //2
            $buttons[] = [ 
            'text' => "4Lv.", 
            ]; //3
            $buttons[] = [ 
            'text' => "5Lv.", 
            ]; //4
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2100);
        }elseif($data == 1){//効率強化
        $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
            $buttons[] = [ 
            'text' => "2Lv.", 
            ]; //1
            $buttons[] = [ 
            'text' => "3Lv.", 
            ]; //2
            $buttons[] = [ 
            'text' => "4Lv.", 
            ]; //3
            $buttons[] = [ 
            'text' => "5Lv.", 
            ]; //4
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2200);
        }elseif($data == 2){//シルクタッチ
        $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2300);
        }elseif($data == 3){//耐久力
        $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
            $buttons[] = [ 
            'text' => "2Lv.", 
            ]; //1
            $buttons[] = [ 
            'text' => "3Lv.", 
            ]; //2
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2400);
        }elseif($data == 4){//幸運
        $buttons[] = [
            'text' => "1Lv",
            ]; //0
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,2500);
        }
        break;

          case 2100:
        if($data == 0){//防護Lv1
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","防護1Lv\n値段:".$this->EF->get("防護1")."$",$buttons,2101);
      }elseif($data == 1){//防護Lv2
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","防護2Lv\n値段:".$this->EF->get("防護2")."$",$buttons,2102);
      }elseif($data == 2){//防護Lv5
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","防護3Lv\n値段:".$this->EF->get("防護3")."$",$buttons,2103);
      }elseif($data == 3){//防護Lv4
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","防護4Lv\n値段:".$this->EF->get("防護4")."$",$buttons,2104);
      }elseif($data == 4){//防護Lv5
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","防護5Lv\n値段:".$this->EF->get("防護5")."$",$buttons,2105);
      }
      break;

          

          case 2101://防護Lv1
          if($data == 0){
            if($money >= $this->EF->get("防護1")){
               $item = $player->getInventory()->getItemInHand();
              $this->Economy->reduceMoney($name,$this->EF->get("防護1"));
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("0"),"1"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
         }else{
          $this->endMenu($player);
         }
        }
        break;

          case 2102:
          if($data == 0){//防護Lv2
            if($money >= $this->EF->get("防護2")){
              $this->Economy->reduceMoney($name,$this->EF->get("防護2"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("0"),"2"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
          }else{
            $this->endMenu($player);
          }
        }
        break;

          case 2103:
          if($data == 0){//防護Lv3
            if($money >= $this->EF->get("防護3")){
              $this->Economy->reduceMoney($name,$this->EF->get("防護3"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("0"),"3"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

           case 2104:
           if($data == 0){//防護Lv4
            if($money >= $this->EF->get("防護4")){
              $this->Economy->reduceMoney($name,$this->EF->get("防護4"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("0"),"4"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
            }else{
              $this->endMenu($player);
            }
          }
          break;

             case 2105:
             if($data == 0){//防護Lv5
              if($money >= $this->EF->get("防護5")){
                $this->Economy->reduceMoney($name,$this->EF->get("防護5"));
                $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("0"),"5"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
          }else{
            $this->endMenu($player);
          }
        }
        break;

          case 2200:
          if($data == 0){//効率強化Lv1
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化1Lv\n値段:".$this->EF->get("効率強化1")."$",$buttons,2201);
      }elseif($data == 1){//効率強化Lv2
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化2Lv\n値段:".$this->EF->get("効率強化2")."$",$buttons,2202);
      }elseif($data == 2){//効率強化Lv3
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化3Lv\n値段:".$this->EF->get("効率強化3")."$",$buttons,2203);
      }elseif($data == 3){//効率強化Lv4
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化4Lv\n値段:".$this->EF->get("効率強化4")."$",$buttons,2204);
      }elseif($data == 4){//効率強化Lv5
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","効率強化5Lv\n値段:".$this->EF->get("効率強化5")."$",$buttons,2205);
      }
          break;
          case 2201://効率強化Lv1
            if($data == 0){
            if($money >= $this->EF->get("効率強化1")){
               $item = $player->getInventory()->getItemInHand();
              $this->Economy->reduceMoney($name,$this->EF->get("効率強化1"));
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("15"),"1"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
         }else{
          $this->endMenu($player);
         }
        }
        break;

          case 2202:
            if($data == 0){//効率強化Lv2
            if($money >= $this->EF->get("効率強化2")){
              $this->Economy->reduceMoney($name,$this->EF->get("効率強化2"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("15"),"2"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
          }else{
            $this->endMenu($player);
          }
        }
        break;

          case 2203:
            if($data == 0){//効率強化Lv3
            if($money >= $this->EF->get("効率強化3")){
              $this->Economy->reduceMoney($name,$this->EF->get("効率強化3"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("15"),"3"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

           case 2204:
            if($data == 0){//効率強化Lv4
            if($money >= $this->EF->get("効率強化4")){
              $this->Economy->reduceMoney($name,$this->EF->get("効率強化4"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("15"),"4"));
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
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("15"),"5"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
          }else{
            $this->endMenu($player);
          }
        }
        break;
              case 2300:
              if($data == 0){//シルクタッチLv1
              $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","シルクタッチ1Lv\n値段:".$this->EF->get("シルクタッチ1")."$",$buttons,2301);
        }
          break;

          case 2301://シルクタッチLv1
          if($data == 0){
            if($money >= $this->EF->get("シルクタッチ1")){
               $item = $player->getInventory()->getItemInHand();
              $this->Economy->reduceMoney($name,$this->EF->get("シルクタッチ1"));
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("16"),"1"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
         }else{
          $this->endMenu($player);
         }
        }
        break;

        case 2400:
        if($data == 0){//耐久力Lv1
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","耐久力1Lv\n値段:".$this->EF->get("耐久力1")."$",$buttons,2401);
      }elseif($data == 1){//耐久力Lv2
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","耐久力2Lv\n値段:".$this->EF->get("耐久力2")."$",$buttons,2402);
      }elseif($data == 2){//耐久力Lv3
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","耐久力3Lv\n値段:".$this->EF->get("耐久力3")."$",$buttons,2403);
        }
          break;

          case 2401://耐久力Lv1
            if($data == 0){
            if($money >= $this->EF->get("耐久力1")){
               $item = $player->getInventory()->getItemInHand();
              $this->Economy->reduceMoney($name,$this->EF->get("耐久力1"));
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("17"),"1"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
             break;
         }else{
          $this->endMenu($player);
         }
        }
        break;

          case 2402:
            if($data == 0){//耐久力Lv2
            if($money >= $this->EF->get("耐久力2")){
              $this->Economy->reduceMoney($name,$this->EF->get("耐久力2"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("17"),"2"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
          }else{
            $this->endMenu($player);
          }
        }
        break;

          case 2403:
            if($data == 0){//耐久力Lv3
            if($money >= $this->EF->get("耐久力3")){
              $this->Economy->reduceMoney($name,$this->EF->get("耐久力3"));
              $item = $player->getInventory()->getItemInHand();
            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("17"),"3"));
            $player->getInventory()->setItemInHand($item);
            $this->endMenu2($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;
          
          case 2500:
           if($data == 0){//幸運1Lv
          $buttons[] = [ 
             'text' => "はい", 
             ]; //0
             $buttons[] = [ 
             'text' => "いいえ", 
             ]; //1
          $this->sendForm($player,"これでいいですか？","幸運1Lv\n値段:".$this->EF->get("幸運")."$",$buttons,2501);
          }
          break;
          
          case 2501:
           if($data == 0){//幸運1Lv
           if($money >= $this->EF->get("幸運")){
               $this->Economy->reduceMoney($name,$this->EF->get("幸運"));
               $item = $player->getInventory()->getItemInHand();
               $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment("18"),"1"));
               $player->getInventory()->setItemInHand($item);
               $this->endMenu2($player);
               return true;
           }else{
               $this->endMenu($player);
           }
           }
           break;
           
          case 3000:
          if($data == 0){//スピード
            $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
            $buttons[] = [ 
            'text' => "2Lv.", 
            ]; //1
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,3100);
          }elseif($data == 1){//採掘速度上昇
            $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
            $buttons[] = [ 
            'text' => "2Lv.", 
            ]; //1
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,3200);
          }elseif($data = 2){//暗視
            $buttons[] = [ 
            'text' => "1Lv.", 
            ]; //0
          $this->sendForm($player,"レベルを選んでください","\n\n",$buttons,3300);
          }
          break;

          case 3100:
          if($data == 0){//スピードLv1
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","スピード1Lv\n値段:".$this->EF->get("スピード1")."$",$buttons,3101);
      }elseif($data == 1){//スピードLv2
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","スピード2Lv\n値段:".$this->EF->get("スピード2")."$",$buttons,3102);
          }
          break;

          case 3101:
          if($data == 0){//スピードLv1
            if($money >= $this->EF->get("スピード1")){
              $this->Economy->reduceMoney($name,$this->EF->get("スピード1"));
              $player->addEffect(new EffectInstance(Effect::getEffect(1),$this->EF->get("スピード1のエフェクト時間"), 1, true));
              $this->endMenu3($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

          case 3102:
          if($data == 0){//スピードLv2
            if($money >= $this->EF->get("スピード2")){
              $this->Economy->reduceMoney($name,$this->EF->get("スピード2"));
              $player->addEffect(new EffectInstance(Effect::getEffect(1),$this->EF->get("スピード1のエフェクト時間"), 2, true));
              $this->endMenu3($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

          case 3200:
          if($data == 0){//採掘速度上昇Lv1
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","採掘速度上昇1Lv\n値段:".$this->EF->get("採掘速度上昇1")."$",$buttons,3201);
      }elseif($data == 1){//採掘速度上昇Lv2
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ",
            ]; //1
          $this->sendForm($player,"これでいいですか？","採掘速度上昇2Lv\n値段:".$this->EF->get("採掘速度上昇2")."$",$buttons,3202);
          }
          break;

          case 3201:
          if($data == 0){//採掘速度上昇Lv1
            if($money >= $this->EF->get("採掘速度上昇1")){
              $this->Economy->reduceMoney($name,$this->EF->get("採掘速度上昇1"));
              $player->addEffect(new EffectInstance(Effect::getEffect(3),$this->EF->get("採掘速度上昇1のエフェクト時間"), 1, true));
              $this->endMenu3($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

          case 3202:
          if($data == 0){//採掘速度上昇Lv2
            if($money >= $this->EF->get("採掘速度上昇2")){
              $this->Economy->reduceMoney($name,$this->EF->get("採掘速度上昇2"));
              $player->addEffect(new EffectInstance(Effect::getEffect(3),$this->EF->get("採掘速度上昇2のエフェクト時間"), 2, true));
              $this->endMenu3($player);
           }else{
            $this->endMenu($player);
           }
        }
          break;

          case 3300:
          if($data == 0){//暗視
       $buttons[] = [ 
            'text' => "はい", 
            ]; //0
            $buttons[] = [ 
            'text' => "いいえ", 
            ]; //1
          $this->sendForm($player,"これでいいですか？","暗視\n値段:".$this->EF->get("暗視")."$",$buttons,3301);
          }
          break;

          case 3301:
          if($data == 0){//暗視
            if($money >= $this->EF->get("暗視")){
              $this->Economy->reduceMoney($name,$this->EF->get("暗視"));
              $player->addEffect(new EffectInstance(Effect::getEffect(16),$this->EF->get("暗視のエフェクト時間"), 1, true));
              $this->endMenu3($player);
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
          $this->sendForm($sender,"説明","コマンド一覧\n/efで説明！\n/ef enchantでエンチャントショップを開きます！\n/ef effectでエフェクトショップを開きます！\n/ef repairでアイテムを修復します！\n\n\n\n\n\n",$buttons,1000);
          break;
          }elseif(strtolower(implode($args)) == "enchant"){
          $this->startMenu($sender);
          break;
          }elseif(strtolower(implode($args)) == "repair"){
              $money = EconomyAPI::getInstance()->myMoney($name);
              if($money >= 5000){
                  foreach($sender->getInventory()->getContents() as $index => $item){
                      if($item->getDamage() > 0){
                          $sender->getInventory()->setItem($index, $item->setDamage(0));
                          $this->Economy->reduceMoney($name,$this->EF->get("修復"));
                          $this->endMenu4($sender);
                          return true;
                      }else{
                          $this->errorMenu($sender);
                      }
                  }
              return true;
              }else{
                  $this->endMenu($sender);
              }
          break;
        } elseif(strtolower(implode($args)) == "effect"){
      $this->startMenu2($sender);
      break;
        }
      default:
      $buttons[] = [ 
            'text' => "閉じる", 
            ]; //0
          $this->sendForm($sender,"説明","コマンド一覧\n/efで説明！\n/ef enchantでエンチャントショップを開きます！\n/ef effectでエフェクトショップを開きます！\n/ef repairでアイテムを修復します！\n\n\n\n\n\n",$buttons,1001);
          break;
        
          }

           return true;
         
          }
          }
