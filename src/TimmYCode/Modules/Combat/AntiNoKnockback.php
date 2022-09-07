<?php

namespace TimmYCode\Modules\Combat;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;

class AntiNoKnockback extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $knockback = 0.0, $knockbackAllowed = 0.0;
	private bool $checkMov = false;
	private Player $target;

	public function getName(): string
	{
		return "AntiNoKnockback";
	}

	public function getWarningLimit(): int
	{
		return 2;
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function checkB(Event $event, Player $damager, Player $target): string
	{
		if (!$this->isActive() || $this->getIgnored($damager)) return "";
		$this->checkAndFirePunishment($this, $damager);

		$this->target = $target;
		$this->checkMov = true;

		return "";
	}

	public function checkA(Event $event, Player $player): string
	{
		if (!$this->checkMov) {
			$this->checkMov = false;
			return "";
		}


		$this->counter->increaseTick(1);
		$inAirTicks = $this->target->getInAirTicks();

		if ($this->counter->reachedTick(1)) {
			if (PlayerUtil::knockbackInfluenced($this->target)) {
				$this->reset();
				return "Knockback influenced";
			}
		}

		if ($inAirTicks != 0) {
			//$this->target->sendMessage("Knockback at tick: " . $this->counter->getTick() . " InAirTicks: " . $inAirTicks);
			$this->setWarning(0);
			$this->reset();
			return "";
		} else if ($this->counter->reachedTick(10)) {
			$this->addWarning(1, $this->target);
			$this->checkAndFirePunishment($this, $this->target);
			//$player->sendMessage($this->target->getName() . " has no Knockback Ping: " . PlayerUtil::getPing($this->target));
			$this->reset();
			return "No Knockback?";
		}

		return "";
	}

	private function reset(): void
	{
		$this->counter->resetTick();
		$this->checkMov = false;
	}

}
