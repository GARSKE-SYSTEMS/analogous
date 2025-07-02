<?php
namespace Analogous\Model;

/**
 * Server.php
 *
 * Represents a server in the Analogous system.
 *
 * @author Patrick Matthias Garske <garske@garske-systems.de>
 */
class Server
{

    private $id;
    private $name;
    private $ip;

    public function __construct($id, $name, $ip)
    {
        $this->id = $id;
        $this->name = $name;
        $this->ip = $ip;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ip' => $this->ip
        ];
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['id']) || !isset($data['name']) || !isset($data['ip'])) {
            throw new \InvalidArgumentException('Invalid data for Server model');
        }
        return new self($data['id'], $data['name'], $data['ip']);
    }

    public function __toString()
    {
        return sprintf("Server [ID: %s, Name: %s, IP: %s]", $this->id, $this->name, $this->ip);
    }
}
