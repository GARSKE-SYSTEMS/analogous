<?php
namespace Analogous\Repository;

require_once __DIR__ . '/../model/Line.php';
require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/CollectorRepository.php';

use Analogous\Model\Line;
use Analogous\Util\Database;
use Analogous\Repository\CollectorRepository;

class LineRepository
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id): ?Line
    {
        $stmt = $this->db->prepare("SELECT * FROM `lines` WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Line::fromArray($row);
    }

    public function findByCollectorId($collectorId, int $offset = 0, int $limit = 100): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `lines` WHERE collector_id = :collector_id ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':collector_id', $collectorId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $lines = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['collector'] = new CollectorRepository()->getCollectorById($row['collector_id']);
            $lines[] = Line::fromArray($row);
        }

        return $lines;
    }

    public function addLine(Line $line): ?Line
    {
        $stmt = $this->db->prepare("INSERT INTO `lines` (collector_id, content) VALUES (:collector_id, :content)");
        $collectorId = $line->getCollector()->getId();
        $stmt->bindParam(':collector_id', $collectorId, \PDO::PARAM_INT);
        $content = $line->getContent();
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);

        if ($stmt->execute()) {
            $line->setId($this->db->lastInsertId());
            return $line;
        }

        return null;
    }

    public function searchLines($query, $limit = 100): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `lines` WHERE content LIKE :query ORDER BY id DESC LIMIT :limit");
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

    /**
     * Count total log lines for a given collector ID.
     *
     * @param int $collectorId
     * @return int
     */
    public function countByCollectorId(int $collectorId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `lines` WHERE collector_id = :collector_id");
        $stmt->bindValue(':collector_id', $collectorId, \PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }





}