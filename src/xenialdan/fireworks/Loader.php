<?php

namespace xenialdan\fireworks;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use xenialdan\fireworks\entity\FireworksRocket;
use xenialdan\fireworks\item\Fireworks;
use xenialdan\fireworks\item\FireworksData;
use xenialdan\fireworks\item\FireworksExplosion;

class Loader extends PluginBase implements Listener{

	public function onLoad(){
		ItemFactory::registerItem(new Fireworks(), true);
		Item::initCreativeItems();
		Entity::registerEntity(FireworksRocket::class, true);
	}

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onJoin(PlayerJoinEvent $event){

		$explosion = new FireworksExplosion();
		$explosion->fireworkColor = [4, 4, 4];
		$explosion->fireworkFade = [5, 5, 5];
		$explosion->fireworkFlicker = true;
		$explosion->fireworkTrail = false;
		$explosion->fireworkType = 4;
		$data = new FireworksData();
		$data->flight = 2;
		$data->explosions[] = $explosion;
		$firework = new Fireworks();
		$nbt = Fireworks::ToNbt($data);
		$firework->setNamedTag($nbt);
		$event->getPlayer()->getInventory()->addItem($firework);

		$data = new FireworksData();
		$data->flight = 2;
		$firework = new Fireworks();
		$nbt = Fireworks::ToNbt($data);
		$firework->setNamedTag($nbt);
		$event->getPlayer()->getInventory()->addItem($firework);
	}
}