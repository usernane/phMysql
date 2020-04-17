<?php
namespace phMysql\entity;

/**
 * An auto-generated entity class which maps to a record in the
 * table 'users'
 **/
class User {
    /**
     * The attribute which is mapped to the column 'id'.
     * @var int
     **/
    private $id;
    /**
     * The attribute which is mapped to the column 'created_on'.
     * @var string
     **/
    private $createdOn;
    /**
     * The attribute which is mapped to the column 'last_updated'.
     * @var string|null
     **/
    private $lastUpdated;
    /**
     * Sets the value of the attribute 'lastUpdated'.
     * The value of the attribute is mapped to the column which has
     * the name 'id'.
     * @param $id int The new value of the attribute.
     **/
    public function setId($id) {
        $this->id = $id;
    }
    /**
     * Returns the value of the attribute 'lastUpdated'.
     * The value of the attribute is mapped to the column which has
     * the name 'id'.
     * @return int The value of the attribute.
     **/
    public function getId() {
        return $this->id;
    }
    /**
     * Sets the value of the attribute 'lastUpdated'.
     * The value of the attribute is mapped to the column which has
     * the name 'created_on'.
     * @param $createdOn string The new value of the attribute.
     **/
    public function setCreatedOn($createdOn) {
        $this->createdOn = $createdOn;
    }
    /**
     * Returns the value of the attribute 'lastUpdated'.
     * The value of the attribute is mapped to the column which has
     * the name 'created_on'.
     * @return string The value of the attribute.
     **/
    public function getCreatedOn() {
        return $this->createdOn;
    }
    /**
     * Sets the value of the attribute 'lastUpdated'.
     * The value of the attribute is mapped to the column which has
     * the name 'last_updated'.
     * @param $lastUpdated string|null The new value of the attribute.
     **/
    public function setLastUpdated($lastUpdated) {
        $this->lastUpdated = $lastUpdated;
    }
    /**
     * Returns the value of the attribute 'lastUpdated'.
     * The value of the attribute is mapped to the column which has
     * the name 'last_updated'.
     * @return string|null The value of the attribute.
     **/
    public function getLastUpdated() {
        return $this->lastUpdated;
    }
}
