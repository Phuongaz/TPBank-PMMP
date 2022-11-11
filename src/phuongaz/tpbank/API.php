<?php

namespace phuongaz\tpbank;

use phuongaz\tpbank\task\CheckTask;
use pocketmine\plugin\PluginBase;

class API {

    private string $account_number;
    private string $password;
    private PluginBase $plugin;
    private string $access_token = "";
    private string $account;

    public function __construct(string $account_number, string $account, string $password, PluginBase $plugin) {
        $this->account_number = $account_number;
        $this->password = $password;
        $this->account = $account;
        $this->plugin = $plugin;
    }

    public function runTask(int $tick) :void {
        $this->plugin->getScheduler()->scheduleRepeatingTask(new CheckTask($this), $tick);
    }

    public function getPlugin() :PluginBase {
        return $this->plugin;
    }

    public function getAccountNumber() :string {
        return $this->account_number;
    }

    public function getPassword() :string {
        return $this->password;
    }

    public function getAccount() :string {
        return $this->account;
    }

    public function getAccessToken() :string {
        return $this->access_token;
    }

    public function setAccessToken(string $access_token) :void {
        $this->access_token = $access_token;
    }

    public function getToken($username, $password) :bool|string {
        $url = "https://ebank.tpb.vn/gateway/api/auth/login";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "DEVICE_ID: LYjkjqGZ3HhGP5520GxPP2j94RDMC7Xje77MI7" . rand(10000000, 999999999999),
            "PLATFORM_VERSION: 91",
            "DEVICE_NAME: Chrome",
            "SOURCE_APP: HYDRO",
            "Authorization: Bearer",
            "Content-Type: application/json",
            "Accept: application/json, text/plain, */*",
            "Referer: https://ebank.tpb.vn/retail/vX/login?returnUrl=%2Fmain",
            "sec-ch-ua-mobile: ?0",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36",
            "PLATFORM_NAME: WEB",
            "APP_VERSION: 1.3",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = '{"username":"' . $username . '","password":"' . $password . '"}';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return ($httpcode == 200) ? $resp : false;
    }

    public function getHistoryRaw($token, $stk_tpbank) :bool|string {
        $start_day = date("Ymd");
        $end_day = date("Ymd");
        $url = "https://ebank.tpb.vn/gateway/api/smart-search-presentation-service/v1/account-transactions/find";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Connection: keep-alive",
            "DEVICE_ID: LYjkjqGZ3HhGP5520GxPP2j94RDMC7Xje77MI7" . rand(10000000, 999999999999),
            "PLATFORM_VERSION: 91",
            "DEVICE_NAME: Chrome",
            "SOURCE_APP: HYDRO",
            "Authorization: Bearer " . $token,
            "XSRF-TOKEN=3229191c-b7ce-4772-ab93-55a" . rand(10000000, 999999999999),
            "Content-Type: application/json",
            "Accept: application/json, text/plain, */*",
            "sec-ch-ua-mobile: ?0",
            "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.164 Safari/537.36",
            "PLATFORM_NAME: WEB",
            "APP_VERSION: 1.3",
            "Origin: https://ebank.tpb.vn",
            "Sec-Fetch-Site: same-origin",
            "Sec-Fetch-Mode: cors",
            "Sec-Fetch-Dest: empty",
            "Referer: https://ebank.tpb.vn/retail/vX/main/inquiry/account/transaction?id=" . $stk_tpbank,
            "Accept-Language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5",
            "Cookie: _ga=GA1.2.1679888794.1623516" . rand(10000000, 999999999999) . "; _gid=GA1.2.580582711.162" . rand(10000000, 999999999999) . "; _gcl_au=1.1.756417552.162" . rand(10000000, 999999999999) . "; Authorization=" . $token,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = '{"accountNo":"' . $stk_tpbank . '","currency":"VND","fromDate":"' . $start_day . '","toDate":"' . $end_day . '","keyword":""}';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ($httpcode == 200) ? $resp : false;
    }

}