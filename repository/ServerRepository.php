<?php
namespace Analogous\Repository;

require_once __DIR__ . '/../model/Server.php';

use Analogous\Model\Server;
use Analogous\Util\Database;

class ServerRepository
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllServers()
    {
        $stmt = $this->db->query("SELECT * FROM servers");
        $servers = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $servers[] = Server::fromArray($row);
        }
        return $servers;
    }

    public function getServerById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM servers WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return Server::fromArray($row);
        }
        return null;
    }

    public function addServer(Server $server)
    {
        $stmt = $this->db->prepare("INSERT INTO servers (name, ip) VALUES (:name, :ip)");
        $stmt->bindParam(':name', $server->getName());
        $stmt->bindParam(':ip', $server->getIp());
        if ($stmt->execute()) {
            $server->setId($this->db->lastInsertId());
            return $server;
        }
        return null;
    }

    public function updateServer(Server $server)
    {
        $stmt = $this->db->prepare("UPDATE servers SET name = :name, ip = :ip WHERE id = :id");
        $stmt->bindParam(':id', $server->getId(), \PDO::PARAM_INT);
        $stmt->bindParam(':name', $server->getName());
        $stmt->bindParam(':ip', $server->getIp());
        return $stmt->execute();
    }

    public function deleteServer(Server $server)
    {
        $stmt = $this->db->prepare("DELETE FROM servers WHERE id = :id");
        $stmt->bindParam(':id', $server->getId(), \PDO::PARAM_INT);
        return $stmt->execute();
    }

}