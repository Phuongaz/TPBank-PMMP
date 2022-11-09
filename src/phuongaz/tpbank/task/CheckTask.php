<?php

declare(strict_types=1);

namespace phuongaz\tpbank\task;

use phuongaz\tpbank\API;
use phuongaz\tpbank\History;
use phuongaz\tpbank\TPBankEvent;
use pocketmine\scheduler\Task;

class CheckTask extends Task {

    private array $cached_data = [];
    private API $api;

    public function __construct(API $api) {
        $this->api = $api;
    }

    public function onRun() :void {
        $token = $this->api->getAccessToken();
        if($token == "") {
            $tokenJson = $this->api->get_token($this->api->getAccount(), $this->api->getPassword());
            $token = json_decode($tokenJson, true)["access_token"];
            $this->api->setAccessToken($token);
        }
        $history_data = json_decode($this->api->get_history($token, $this->api->getAccountNumber()), true);
        if(!isset($history_data["transactionInfos"])) return;
        $history_data = $history_data["transactionInfos"];
        if(count($this->cached_data) == 0) {
            $this->cached_data = $history_data;
        } else {
            $new_data = array_udiff($history_data, $this->cached_data, function($a, $b) {
                return $a["id"] - $b["id"];
            });
            if(count($new_data) > 0) {
                $this->cached_data = $history_data;
                foreach($new_data as $data) {
                    $history = new History(json_encode($data));
                    $event = new TPBankEvent($history, $this->api->getPlugin());
                    $event->call();
                }
            }
        }
    }
}