<?php

namespace TimmYCode\Punishment\Methods;

use pocketmine\player\Player;
use TimmYCode\Punishment\Punishment;

class Kick implements Punishment
{

	private string $reason;

	function __construct(string $reason)
	{
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->kick($this->reason);
	}

}
