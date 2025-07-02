<?php
namespace Analogous\Repository;

require_once __DIR__ . '/../model/Token.php';
require_once __DIR__ . '/../util/Database.php';
require_once __DIR__ . '/CollectorRepository.php';

use Analogous\Model\Token;
use Analogous\Util\Database;
use Analogous\Repository\CollectorRepository;

class TokenRepository
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(Token $token)
    {
        $stmt = $this->db->prepare("INSERT INTO tokens (collector_id, content, created_at) VALUES (:collector_id, :token, :created_at)");

        // set creation timestamp and extract values
        $token->setCreatedAt(time());
        $collectorId = $token->getCollector()->getId();
        $content = $token->getContent();
        $createdAt = $token->getCreatedAt();
        // bind values
        $stmt->bindValue(':collector_id', $collectorId, \PDO::PARAM_INT);
        $stmt->bindValue(':token', $content, \PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $createdAt, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            $token->setId($this->db->lastInsertId());
            return $token;
        }

        return null;
    }

    public function getTokenByContent($content)
    {
        $stmt = $this->db->prepare('SELECT * FROM tokens WHERE content = :content');
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            // fetch and set its Collector
            $collectorRepo = new CollectorRepository();
            $collector = $collectorRepo->getCollectorById($row['collector_id']);
            return new Token(
                $row['id'],
                $collector,
                $row['content'],
                $row['created_at']
            );
        }
        return null;
    }

    public function delete(Token $token)
    {
        $stmt = $this->db->prepare("DELETE FROM tokens WHERE id = :id");
        // bind using a variable to delete by ID
        $id = $token->getId();
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Fetch all Tokens for a given collector ID.
     *
     * @param int $collectorId
     * @return Token[]
     */
    public function getTokensByCollectorId(int $collectorId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tokens WHERE collector_id = :collector_id");
        $stmt->bindValue(':collector_id', $collectorId, \PDO::PARAM_INT);
        $stmt->execute();
        $tokens = [];
        $collectorRepo = new CollectorRepository();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $collector = $collectorRepo->getCollectorById($row['collector_id']);
            $tokens[] = new Token(
                $row['id'],
                $collector,
                $row['content'],
                $row['created_at']
            );
        }
        return $tokens;
    }

}