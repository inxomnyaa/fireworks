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
use xenialdan\fireworks\FakeSetEntityDataPacket;
use xenialdan\fireworks\item\Fireworks;

class FireworksRocket extends Projectile{
	const NETWORK_ID = self::FIREWORKS_ROCKET;

	public $width = 0.25;
	public $height = 0.25;

	public $gravity = 0.0;
	public $drag = 0.01;

	private $lifeTime = 0;
	public $random;
	public $fireworks;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, ?Fireworks $item = null, ?Random $random = null){
		$this->random = $random;
		$this->fireworks = $item;
		parent::__construct($level, $nbt, $shootingEntity);
	}

	protected function initEntity(){
		parent::initEntity();
		$random = $this->random ?? new Random();

		$this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);
		$this->setGenericFlag(self::DATA_FLAG_AFFECTED_BY_GRAVITY, true);
		$this->setDataProperty(16, self::DATA_TYPE_SLOT, [$this->fireworks->getId(), $this->fireworks->getDamage(), $this->fireworks->getCount(), $this->fireworks->getCompoundTag()]);
		//id [1][0], meta $d[1][2], count $d[1][1], data $d[1][3]

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

	/**
	 * @param Player[]|Player $player
	 * @param array $data Properly formatted entity data, defaults to everything
	 */
	public function sendData($player, array $data = null){
		if (!is_array($player)){
			$player = [$player];
		}

		$pk = new FakeSetEntityDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = $data ?? $this->dataProperties;

		foreach ($player as $p){
			if ($p === $this){
				continue;
			}
			$p->dataPacket(clone $pk);
		}

		if ($this instanceof Player){
			$this->dataPacket($pk);
		}
	}

	public function spawnTo(Player $player){
		$this->setMotion($this->getDirectionVector());
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LAUNCH);
		parent::spawnTo($player);
	}

	public function despawnFromAll(){
		$this->broadcastEntityEvent(EntityEventPacket::FIREWORK_PARTICLES, 0);

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
