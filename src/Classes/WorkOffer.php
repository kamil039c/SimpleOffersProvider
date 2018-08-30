<?php
namespace App\Classes;

class WorkOffer {
    private $id;
    private $createdAt;
    private $updatedAt;
    private $dateStart;
    private $dateEnd;
    private $adminName;
    private $showInList;
    private $createdBy;
    private $code;
    private $expiringNotification;
    private $categories = [];
    private $positions = [];
    private $cities = [];
    private $title;
    private $content;

    // $this->id, json key: id

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    // $this->createdAt, json key: created_at

    public function setCreatedAt(string $date) {
        $this->createdAt = $date;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    // $this->updatedAt, json key: updated_at

    public function setUpdatedAt(string $date) {
        $this->updatedAt = $date;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    // $this->dateStart, json key: date_start

    public function setDateStart(string $date) {
        $this->dateStart = $date;
    }

    public function getDateStart() {
        return $this->dateStart;
    }

    // $this->dateEnd, json key: date_end

    public function setDateEnd(string $date) {
        $this->dateEnd = $date;
    }

    public function getDateEnd() {
        return $this->dateEnd;
    }

    // $this->adminName, json key: admin_name

    public function setAdminName(string $name) {
        $this->adminName = $name;
    }

    public function getAdminName() {
        return $this->adminName;
    }

    // $this->showInList, json key: show_in_list

    public function setShowInList(int $state) {
        $this->showInList = $state;
    }

    public function getShowInList() {
        return $this->showInList;
    }

    // $this->createdBy, json key: created_by

    public function setCreatedBy(int $id) {
        $this->createdBy = $id;
    }

    public function getCreatedBy() {
        return $this->createdBy;
    }

    // $this->code, json key: code
    public function setCode(string $code) {
        $this->code = $code;
    }

    public function getCode() {
        return $this->code;
    }

    // $this->expiringNotification, json key: expiring_notification

    public function setExpiringNotification(int $state) {
        $this->expiringNotification = $state;
    }

    public function getExpiringNotification() {
        return $this->expiringNotification;
    }

    // $this->categories, json key: categories

    public function setCategories(array $categories) {
        $this->categories = $categories;
    }

    public function addCategory(string $cat) {
        $this->categories[] = $cat;
    }

    public function getCategories() {
        return $this->categories;
    }

    public function getCategoriesCount() {
        return count($this->categories);
    }

    public function getCategory(int $id) {
        return $this->categories[$id];
    }

    // $this->positions, json key: positions

    public function setPositions(array $positions) {
        $this->positions = $positions;
    }

    public function addPosition(string $pos) {
        $this->positions[] = $pos;
    }

    public function getPositions() {
        return $this->positions;
    }

    public function getPositionsCount() {
        return count($this->positions);
    }

    public function getPosition(int $id) {
        return $this->positions[$id];
    }

    // $this->cities, json key: cities

    public function setCities(array $cities) {
        $this->cities = $cities;
    }

    public function addCity(string $city) {
        $this->cities[] = $city;
    }

    public function getCities() {
        return $this->cities;
    }

    public function getCitiesCount() {
        return count($this->cities);
    }

    public function getCity(int $id) {
        return $this->cities[$id];
    }

    // $this->title, json key: content.title

    public function setTitle(string $title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    // $this->content, json key: content.content

    public function setContent(string $content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    public static function buildFromArray(array $arr) {
        $offer = new WorkOffer( $arr['id']);

        $offer->setCreatedAt($arr['created_at']);
        $offer->setUpdatedAt($arr['updated_at']);
        $offer->setDateStart($arr['date_start']);
        $offer->setDateEnd((string)$arr['date_end']);
        $offer->setAdminName($arr['admin_name']);
        $offer->setShowInList((int)$arr['show_in_list']);
        $offer->setCode($arr['code']);
        $offer->setExpiringNotification((int)$arr['expiring_notification']);
        $offer->setCreatedBy((int)$arr['created_by']);
        $offer->setCategories($arr['categories']);
        $offer->setPositions($arr['positions']);
        $offer->setCities($arr['cities']);
        $offer->setTitle($arr['content']['title']);
        $offer->setContent($arr['content']['content']);
        
        return $offer;
    }

    public function getJsonArray() {
        return [
            'id' => $this->getId(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
            'date_start' => $this->getDateStart(),
            'date_end' => $this->getDateEnd(),
            'admin_name' => $this->getAdminName(),
            'show_in_list' => $this->getShowInList(),
            'code' => $this->getCode(),
            'expiring_notification' => $this->getExpiringNotification(),
            'created_by' => $this->getCreatedBy(),
            'categories' => $this->getCategories(),
            'positions' => $this->getPositions(),
            'cities' => $this->getCities(),
            'content' => [
                'title' => $this->getTitle(),
                'content' => $this->getContent(),
            ]
        ];
    }

    public function isActual() {
        if (empty($this->dateEnd)) return true;
        return time() <= strtotime($this->dateEnd);
    }

    public function getDaysSinceAdd() {
        return (int)((time() - strtotime($this->createdAt)) / (3600 * 24));
    }
}
?>