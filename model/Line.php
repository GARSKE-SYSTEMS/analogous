<?php
namespace Analogous\Model;

/**
 * Line.php
 *
 * Represents a log line in the Analogous system.
 *
 * @author Patrick Matthias Garske <garske@garske-systems.de>
 */
class Line
{

    private $id;
    private $collector;
    private $content;

    public function __construct($id = null, Collector $collector, $content)
    {
        $this->id = $id;
        $this->collector = $collector;
        $this->content = $content;
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

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'collector' => $this->collector->toArray(),
            'content' => $this->content
        ];
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['id']) || !isset($data['collector']) || !isset($data['content'])) {
            throw new \InvalidArgumentException('Invalid data for Line model');
        }
        $collector = Collector::fromArray($data['collector']);
        return new self($data['id'], $collector, $data['content']);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCollector(Collector $collector)
    {
        $this->collector = $collector;
    }

}
