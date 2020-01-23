<?php
namespace phMysql\tests;

/**
 * A test entity class. Used to test mapping feature.
 *
 * @author Ibrahim
 */
class EntityUser {
    private $userId;
    private $email;
    private $name;
    public function getName() {
        return $this->name;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getUserId() {
        return $this->userId;
    }
    public function setUserId($id) {
        $this->userId = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function __toString() {
        return 'ID: ['.$this->userId.'] Name: ['.$this->name.'] Email: ['.$this->email.']';
    }
}
