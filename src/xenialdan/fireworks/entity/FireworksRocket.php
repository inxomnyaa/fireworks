<?php

namespace xenialdan\fireworks\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\utils\Random;
use xenialdan\fireworks\item\Fireworks;

class FireworksRocket extends Projectile{
	const NETWORK_ID = self::FIREWORKS_ROCKET;

	public $width = 0.25;
	public $height = 0.25;

	public $gravity = 0.0;
	public $drag = 0.01;

	private $lifeTime = 0;
	private $random;
	private $fireworks = null;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, ?Fireworks $item = null, ?Random $random = null){
		parent::__construct($level, $nbt, $shootingEntity);
		$this->random = $random;
		$this->fireworks = $item;
	}

	protected function initEntity(){
		parent::initEntity();
		$random = $this->random ?? new Random();

		$this->setGenericFlag(self::DATA_FLAG_INVISIBLE, true);
		$this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);
		$this->setGenericFlag(self::DATA_FLAG_AFFECTED_BY_GRAVITY, true);
		$this->setDataProperty(16, self::DATA_TYPE_SLOT, $this->fireworks);

		$flyTime = 1;

		try{
			if (!is_null($this->namedtag->getCompoundTag("Fireworks")))
				if ($this->namedtag->getCompoundTag("Fireworks")->getByte("Flight", 1))
					$flyTime = $this->namedtag->getCompoundTag("Fireworks")->getByte("Flight", 1);
		} catch (\Exception $exception){
			$this->server->getLogger()->debug($exception);
		}

		$this->lifeTime = 20 * $flyTime + $random->nextBoundedInt(5) + $random->nextBoundedInt(7);
	}

	public function spawnTo(Player $player){
		$this->setMotion($this->getDirectionVector());
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LAUNCH);
		parent::spawnTo($player);
	}

	/*public function despawnFrom(Player $player, bool $send = true){
		$entityEvent = new EntityEventPacket();
		$entityEvent->entityRuntimeId = $this->id;
		$entityEvent->event = EntityEventPacket::FIREWORK_PARTICLES;
		$entityEvent->data = 0;
		$this->getLevel()->getServer()->broadcastPacket($this->level->getPlayers(), $entityEvent);

		parent::despawnFrom($player, $send);

		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BLAST);
	}*/

	public function despawnFromAll(){
		$entityEvent = new EntityEventPacket();
		$entityEvent->entityRuntimeId = $this->id;
		$entityEvent->event = EntityEventPacket::FIREWORK_PARTICLES;
		$entityEvent->data = 0;
		$this->getLevel()->getServer()->broadcastPacket($this->level->getPlayers(), $entityEvent);

		parent::despawnFromAll();

		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BLAST);
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		if ($this->lifeTime-- < 0){
			$this->flagForDespawn();
			return true;
		} else{
			return parent::entityBaseTick($tickDiff);
		}
	}
}
