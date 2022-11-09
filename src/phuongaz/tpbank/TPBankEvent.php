<?php

declare(strict_types=1);

namespace phuongaz\tpbank;

use pocketmine\event\CancellableTrait;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\plugin\PluginBase;

class TPBankEvent extends PluginEvent {
    use CancellableTrait;

    private History $history;

    public function __construct(History $history, PluginBase $plugin) {
        parent::__construct($plugin);
        $this->history = $history;
    }

    public function getHistory() :History {
        return $this->history;
    }
}