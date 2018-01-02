<?php

namespace xenialdan\fireworks;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use xenialdan\fireworks\entity\FireworksRocket;
use xenialdan\fireworks\item\Fireworks;

class Loader extends PluginBase{

	public function onLoad(){
		ItemFactory::registerItem(new Fireworks(), true);
		Item::initCreativeItems();
		Entity::registerEntity(FireworksRocket::class, true);
	}
}