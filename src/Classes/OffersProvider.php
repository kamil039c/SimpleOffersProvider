<?php
namespace App\Classes;

class OffersProvider {
    const OFFERS_SRC_URL = 'https://demo.appmanager.pl/api/v1/ads';
    const CACHE_FILE = 'offers.json';
    private $offers = [];

    public function __construct(array $filters) {
        $ptr = curl_init();
        
        if (!is_resource($ptr)) {
            throw new OffersProviderException("OffersProvider - cannot init cURL library!", 1);
        }

        curl_setopt($ptr, CURLOPT_URL, self::OFFERS_SRC_URL);
        curl_setopt($ptr, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ptr, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ptr);

        if (curl_errno($ptr)) {
            throw new OffersProviderException("OffersProvider - cURL error occured: " . curl_error($ptr), 2);
        }
            
        curl_close($ptr);

        $response = json_decode($response, true);

        if (!$response['success'] || !is_array($response['data'])) {
            throw new OffersProviderException("OffersProvider - bad HTTP Response occured!", 3);
        }

        foreach ($response['data'] as $offer) {
            $clOffer = WorkOffer::buildFromArray($offer);

            if (!$clOffer->getShowInList()) continue;
            if ($filters['only_active'] && !$clOffer->isActual()) continue;

            if (!empty($filters['city'])) {
                $filter = false;
                foreach ($clOffer->getCities() as $city) {
                    if (preg_match($this->freg($filters['city']), $this->vreg($city))) {
                        $filter = true;
                        break;
                    }
                }

                if (!$filter) continue;
            }
           
            if ($filters['company_id'] != 0 && $clOffer->getCreatedBy() != $filters['company_id']) continue;
            if ($filters['days_since_add'] > 0 && $clOffer->getDaysSinceAdd() > $filters['days_since_add']) continue;
            if (!empty($filters['admin_name']) && !preg_match( $this->freg($filters['admin_name'] ), $this->vreg( $clOffer->getAdminName() ))) continue;

            $this->offers[] = WorkOffer::buildFromArray($offer);
        }
    }

    public function freg(string $filter) {
        return '/' . addslashes(strtolower($filter)) . '/';
    }

    public function vreg(string $value) {
        return addslashes(strtolower($value));
    }

    public function getOffersRange(int $startFrom, int $count) {
        return array_slice($this->offers, $startFrom, $count);
    }

    public function getOffers() {
        return $this->offers;
    }

    public function getOffersCount() {
        return count($this->offers);
    }
}

?>