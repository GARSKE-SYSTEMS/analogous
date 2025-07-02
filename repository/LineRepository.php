<?php
namespace Analogous\Repository;

require_once __DIR__ . '/../model/Line.php';
require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/CollectorRepository.php';

use Analogous\Model\Line;
use Analogous\Util\Database;

class LineRepository
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id): ?Line
    {
        $stmt = $this->db->prepare("SELECT * FROM lines WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Line::fromArray($row);
    }

    public function findByCollectorId($collectorId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM lines WHERE collector_id = :collector_id");
        $stmt->bindParam(':collector_id', $collectorId, \PDO::PARAM_INT);
        $stmt->execute();
        $lines = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lines[] = Line::fromArray($row);
        }

        return $lines;
    }

    public function addLine(Line $line): ?Line
    {
        $stmt = $this->db->prepare("INSERT INTO lines (collector_id, content) VALUES (:collector_id, :content)");
        $collectorId = $line->getCollector()->getId();
        $stmt->bindParam(':collector_id', $collectorId, \PDO::PARAM_INT);
        $stmt->bindParam(':content', $line->getContent());

        if ($stmt->execute()) {
            $line->setId($this->db->lastInsertId());
            return $line;
        }

        return null;
    }

    public function searchLines($query, $limit = 100): array
    {
        $stmt = $this->db->prepare("SELECT * FROM lines WHERE content LIKE :query LIMIT :limit");
        $likeQuery = '%' . $query . '%';
        $stmt->bindParam(':query', $likeQuery);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $lines = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lines[] = Line::fromArray($row);
        }

        return $lines;
    }





}