<?php
namespace Analogous\Model;

/**
 * Token.php
 *
 * Represents an API Access Token for an Analogous collector.
 *
 * @author Patrick Matthias Garske <garske@garske-systems.de>
 */
class Token
{
    private $id;
    private $collector;
    private $content;
    private $created_at;

    public function __construct($id, Collector $collector, $content, $created_at)
    {
        $this->id = $id;
        $this->collector = $collector;
        $this->content = $content;
        $this->created_at = $created_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCollector()
    {
        return $this->collector;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'collector' => $this->collector->toArray(),
            'content' => $this->content,
            'created_at' => $this->created_at
        ];
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['id']) || !isset($data['collector']) || !isset($data['content']) || !isset($data['created_at'])) {
            throw new \InvalidArgumentException('Invalid data for Token model');
        }
        $collector = Collector::fromArray($data['collector']);
        return new self($data['id'], $collector, $data['content'], $data['created_at']);
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setCollector(Collector $collector)
    {
        $this->collector = $collector;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

}
