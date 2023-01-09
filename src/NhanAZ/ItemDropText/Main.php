<?php

declare(strict_types=1);

namespace NhanAZ\ItemDropText;

use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

	protected function onEnable(): void {
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onItemSpawn(ItemSpawnEvent $event): void {
		$entity = $event->getEntity();
		$item = $entity->getItem();
		$format = $this->getConfig()->get("format");
		$replacements = [
			"{name}" => $item->getName(),
			"{vanillaName}" => $item->getVanillaName(),
			"{count}" => $item->getCount(),
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
}
