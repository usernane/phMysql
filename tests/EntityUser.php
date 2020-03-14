<?php
namespace phMysql\tests;

/**
 * A test entity class. Used to test mapping feature.
 *
 * @author Ibrahim
 */
class EntityUser {
    private $email;
    private $name;
    private $userId;
    public function __toString() {
        return 'ID: ['.$this->userId.'] Name: ['.$this->name.'] Email: ['.$this->email.']';
    }
    public function getEmail() {
        return $this->email;
    }
    public function getName() {
        return $this->name;
    }
    public function getUserId() {
        return $this->userId;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setUserId($id) {
        $this->userId = $id;
    }
}
