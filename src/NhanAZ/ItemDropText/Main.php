<?php

declare(strict_types=1);

namespace NhanAZ\ItemDropText;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\ItemMergeEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	use SingletonTrait;

	protected function onEnable(): void {
		self::setInstance($this);
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$format = $this->getConfig()->get("format");
		if (strpos($format, "{despawnDelay}")) {
			$this->getScheduler()->scheduleRepeatingTask(new DespawnDelayTask(), 20);
		}
	}

	private function tickToTimeFormat(int $tick): string {
		if ($tick == -1) {
			return "∞";
		}
		$originSecond = floor($tick / 20);
		$minute = floor($originSecond / 60);
		$second = $originSecond % 60;
		if ($tick > 6000) {
			return $tick . " ticks";
		}
		if ($minute >= 1) {
			return $minute . "m" . $second . "s";
		}
		return $second . "s";
	}


	public function setNameTag(ItemEntity $entity, int $count = null): void {
		$item = $entity->getItem();
		$format = $this->getConfig()->get("format");
		if (is_null($count)) {
			$count = $entity->getItem()->getCount();
		}
		$replacements = [
			"{name}" => $item->getName(),
			"{vanillaName}" => $item->getVanillaName(),
			"{count}" => $count,
			"{attackPoints}" => $item->getAttackPoints(),
			"{cooldownTicks}" => $item->getCooldownTicks(),
			"{defensePoints}" => $item->getDefensePoints(),
			"{maxStackSize}" => $item->getMaxStackSize(),
			"{lore}" => implode(TextFormat::EOL, $item->getLore()),
			"{despawnDelay}" => $this->tickToTimeFormat($entity->getDespawnDelay())
		];
		$format = str_replace(array_keys($replacements), array_values($replacements), strval($format));
		$entity->setNameTag(TextFormat::colorize($format));
		$entity->setNameTagAlwaysVisible();
	}

	public function onItemSpawn(ItemSpawnEvent $event): void {
		$entity = $event->getEntity();
		$this->setNameTag($entity);
	}

	public function onItemMerges(ItemMergeEvent $event): void {
		$entity = $event->getEntity();
		$target = $event->getTarget();
		if ($entity instanceof ItemEntity) {
			$count = $entity->getItem()->getCount() + $target->getItem()->getCount();
			$this->setNameTag($target, $count);
		}
	}
}
