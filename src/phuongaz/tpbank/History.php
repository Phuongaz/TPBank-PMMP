<?php

declare(strict_types=1);

namespace phuongaz\tpbank;

class History {

    private string $raw_data;

    private string $id;
    private string $arrangementId;
    private string $reference;
    private string $description;
    private string $bookingDate;
    private string $valueDate;
    private string $amount;
    private string $currency;
    private string $creditDebitIndicator;
    private string $runningBalance;

    public function __construct(string $raw_data) {
        $this->raw_data = $raw_data;
        $this->parseHistory();
    }

    public function getRawData() :string {
        return $this->raw_data;
    }

    public function getId() :string {
        return $this->id;
    }

    public function getArrangementId() :string {
        return $this->arrangementId;
    }

    public function getReference() :string {
        return $this->reference;
    }

    public function getDescription() :string {
        return $this->description;
    }

    public function getBookingDate() :string {
        return $this->bookingDate;
    }

    public function getValueDate() :string {
        return $this->valueDate;
    }

    public function getAmount() :string {
        return $this->amount;
    }

    public function getCurrency() :string {
        return $this->currency;
    }

    public function getCreditDebitIndicator() :string {
        return $this->creditDebitIndicator;
    }

    public function getRunningBalance() :string {
        return $this->runningBalance;
    }

    public function parseHistory() :void {
        $data = json_decode($this->raw_data, true);
        $this->id = $data["id"];
        $this->arrangementId = $data["arrangementId"];
        $this->reference = $data["reference"];
        $this->description = $data["description"];
        $this->bookingDate = $data["bookingDate"];
        $this->valueDate = $data["valueDate"];
        $this->amount = $data["amount"];
        $this->currency = $data["currency"];
        $this->creditDebitIndicator = $data["creditDebitIndicator"];
        $this->runningBalance = $data["runningBalance"];
    }

    public function getHistoryData() :array {
        $data = json_decode($this->raw_data, true);
        $history = [];
        foreach($data["transactionInfos"] as $transaction) {
            $history[] = [
                "id" => $transaction["id"],
                "arrangementId" => $transaction["arrangementId"],
                "reference" => $transaction["reference"],
                "description" => $transaction["description"],
                "bookingDate" => $transaction["bookingDate"],
                "valueDate" => $transaction["valueDate"],
                "amount" => $transaction["amount"],
                "currency" => $transaction["currency"],
                "creditDebitIndicator" => $transaction["creditDebitIndicator"],
                "runningBalance" => $transaction["runningBalance"]
            ];
        }
        return $history;
    }
}