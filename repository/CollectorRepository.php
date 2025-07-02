<?php
namespace Analogous\Repository;

require_once __DIR__ . '/../model/Collector.php';
require_once __DIR__ . '/../model/Server.php';
require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/ServerRepository.php';

use Analogous\Model\Collector;

class CollectorRepository
{

    private $db;

    public function __construct()
    {
        $this->db = \Analogous\Util\Database::getInstance()->getConnection();
    }

    public function getAllCollectors()
    {
        $stmt = $this->db->query("SELECT * FROM collectors");
        $collectors = [];

        $serverRepo = new ServerRepository();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $collector = Collector::fromArray($row);
            $collector->setServer(
                $serverRepo->getServerById($row['server_id'])
            );
            $collectors[] = $collector;
        }

        return $collectors;
    }

    public function getCollectorById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM collectors WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        // build the collector
        $collector = Collector::fromArray($row);

        // fetch & set its Server
        $serverRepo = new ServerRepository();
        $server = $serverRepo->getServerById($row['server_id']);
        $collector->setServer($server);

        return $collector;
    }

    public function getCollectorsByServerId($serverId)
    {
        $stmt = $this->db->prepare("SELECT * FROM collectors WHERE server_id = :server_id");
        $stmt->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
        $stmt->execute();
        $collectors = [];

        $serverRepo = new ServerRepository();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $collector = Collector::fromArray($row);
            $collector->setServer(
                $serverRepo->getServerById($row['server_id'])
            );
            $collectors[] = $collector;
        }

        return $collectors;
    }

    public function addCollector(Collector $collector)
    {
        $stmt = $this->db->prepare("INSERT INTO collectors (server_id, name) VALUES (:server_id, :name)");
        $serverId = $collector->getServer()->getId();
        $stmt->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $collector->getName());
        if ($stmt->execute()) {
            $collector->setId($this->db->lastInsertId());
            return $collector;
        }
        return null;
    }

    public function updateCollector(Collector $collector)
    {
        $stmt = $this->db->prepare("UPDATE collectors SET server_id = :server_id, name = :name WHERE id = :id");
        $serverId = $collector->getServer()->getId();
        $stmt->bindParam(':id', $collector->getId(), \PDO::PARAM_INT);
        $stmt->bindParam(':server_id', $serverId, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $collector->getName());
        return $stmt->execute();
    }

    public function deleteCollector($id)
    {
        $stmt = $this->db->prepare("DELETE FROM collectors WHERE id = :id");
        return $stmt->execute([':id' => (int) $id]);
    }

}
