<?php

declare(strict_types=1);

namespace NhanAZ\ItemDropText;

use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\ItemMergeEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

	protected function onEnable(): void {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function setNameTag(Entity|ItemEntity $entity, int $count): void {
		$item = $entity->getItem();
		$format = $this->getConfig()->get("format");
		$replacements = [
			"{name}" => $item->getName(),
			"{vanillaName}" => $item->getVanillaName(),
			"{count}" => $count,
			"{attackPoints}" => $item->getAttackPoints(),
			"{cooldownTicks}" => $item->getCooldownTicks(),
			"{defensePoints}" => $item->getDefensePoints(),
			"{maxStackSize}" => $item->getMaxStackSize(),
			"{lore}" => implode(TextFormat::EOL, $item->getLore())
		];
		$format = str_replace(array_keys($replacements), array_values($replacements), $format);
		$entity->setNameTag(TextFormat::colorize($format));
		$entity->setNameTagAlwaysVisible();
	}

	public function onItemSpawn(ItemSpawnEvent $event): void {
		$entity = $event->getEntity();
		$this->setNameTag($entity, $entity->getItem()->getCount());
	}

	public function onItemMerge(ItemMergeEvent $event): void {
		$entity = $event->getEntity();
		$target = $event->getTarget();
		if ($entity instanceof ItemEntity) {
			$count = $entity->getItem()->getCount() + $target->getItem()->getCount();
		}
		$this->setNameTag($target, $count);
	}
}
