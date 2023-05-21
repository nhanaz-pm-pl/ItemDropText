<?php

declare(strict_types=1);

namespace NhanAZ\ItemDropText;

use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class DespawnDelayTask extends Task {

	public function onRun(): void {
		$worlds = Server::getInstance()->getWorldManager()->getWorlds();
		foreach ($worlds as $word) {
			foreach ($word->getEntities() as $entity) {
				if ($entity instanceof ItemEntity) {
					Main::getInstance()->setNameTag($entity);
				}
			}
		}
	}
}
