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
        $token = $this->loadAccessToken();
        if($token == null) {
            $this->api->getPlugin()->getLogger()->error("Can't get access token");
            return;
        }
        $historyRaw = $this->api->getHistoryRaw($token, $this->api->getAccountNumber());
        if($historyRaw) {
            $history_data = json_decode($historyRaw, true);
        }else{
            $this->loadAccessToken();  //Token has expired
            return;
        }
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

    public function loadAccessToken() :?string {
        $token = $this->api->getAccessToken();
        if($token == "") {
            $tokenRaw = $this->api->getToken($this->api->getAccount(), $this->api->getPassword());
            if($tokenRaw) {
                $token = json_decode($tokenRaw, true);
                $token = $token["access_token"];
                $this->api->setAccessToken($token);
            }else{
                return null;
            }
        }
        return $token;
    }
}