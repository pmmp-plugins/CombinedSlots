<?php
namespace AnkitM252;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\server\QueryRegenerateEvent;

class CombinedSlots extends PluginBase implements Listener{
	public $maxCount;
	public $playerCount;
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getTotal();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("CombinedSlots has been enabled!");
	}
	public function getTotal(){
		$config = $this->getConfig()->get("servers");
		foreach($config as $servers){
			$content = file_get_contents("http://mcapi.ca/query/".$servers."/mcpe");
			$decode = json_decode($content, true);
			if(!isset($decode["error"])){
				$this->maxCount = $this->maxCount + $decode["players"]["max"];
				$this->playerCount = $this->playerCount + $decode["players"]["online"];
			} else {
				$this->getLogger()->notice($servers ." is not online or something went wrong while querying.");
			}
		}
	}
	public function onRegenerate(QueryRegenerateEvent $event){
		$localTotalPlayers = count($this->getServer()->getOnlinePlayers());
		$localPlayers = $this->getServer()->getMaxPlayers();
		
		$totalMaxCount = $localPlayers + $this->maxCount;
		$totalPlayerCount = $localTotalPlayers + $this->playerCount;
		
		$event->setPlayerCount($totalPlayerCount);
		$event->setMaxPlayerCount($totalMaxCount);
	}
	public function onDisable(){
		$this->getLogger()->info("CombinedSlots has been enabled!");
	}
}