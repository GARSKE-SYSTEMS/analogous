<?php
namespace Analogous\Model;

require_once __DIR__ . '/Server.php';

/**
 * Collector.php
 *
 * Represents a collector in the Analogous system.
 *
 * @author Patrick Matthias Garske <garske@garske-systems.de>
 */
class Collector
{

    private $id;
    private $server;
    private $name;

    public function __construct($id, Server $server, $name)
    {
        $this->id = $id;
        $this->server = $server;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setServer(Server $server)
    {
        $this->server = $server;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'server' => $this->server->toArray(),
            'name' => $this->name
        ];
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['id']) || !isset($data['server']) || !isset($data['name'])) {
            throw new \InvalidArgumentException('Invalid data for Collector model');
        }
        $server = $data['server'];
        return new self($data['id'], $server, $data['name']);
    }

    public function __toString()
    {
        return sprintf("Collector [ID: %s, Server: %s, Name: %s]", $this->id, $this->server, $this->name);
    }

}