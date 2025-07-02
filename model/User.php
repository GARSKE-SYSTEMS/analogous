<?php
namespace Analogous\Model;

/**
 * User.php
 *
 * Represents a User account.
 *
 * @author Patrick Matthias Garske <garske@garske-systems.de>
 * */
class User
{

    private $id;
    private $username;
    private $password;
    private $created_on;

    public function __construct($id, $username, $password, $created_on)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->created_on = $created_on;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getCreatedAt()
    {
        return $this->created_on;
    }

    public function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function __toString()
    {
        return $this->username;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'created_on' => $this->created_on
        ];
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['id']) || !isset($data['username']) || !isset($data['password']) || !isset($data['created_on'])) {
            throw new \InvalidArgumentException('Invalid data for User model');
        }
        return new self($data['id'], $data['username'], $data['password'], $data['created_on']);
    }
}