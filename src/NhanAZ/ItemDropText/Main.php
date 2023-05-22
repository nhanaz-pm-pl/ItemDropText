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

	private function ticksToTimeFormat(int $ticks): string {
		if ($ticks == -1) {
			return "Infinity";
		} else if ($ticks > 6000) {
			return $ticks . " ticks";
		} else {
			$seconds = $ticks / 20;
			$minutes = floor($seconds / 60);
			$seconds = floor($seconds) % 60;
			if ($minutes < 1) {
				return sprintf("%02ds", $seconds);
			} else {
				return sprintf("%02dm%02ds", $minutes, $seconds);
			}
		}
	}

	public function setNameTag(ItemEntity $entity, int $count = null): void {
		$item = $entity->getItem();
		$format = $this->getConfig()->get("format");
		if ($count === null) {
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
			"{despawnDelay}" => $this->ticksToTimeFormat($entity->getDespawnDelay())
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
