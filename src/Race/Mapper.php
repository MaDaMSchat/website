<?php
namespace LVAC\Race;
use \PDO;

class Mapper {
    protected $conn;

    public function __construct($conn = null)
    {
        if ($conn !== null) {
            $this->conn = $conn;
        }
    }

    public function getResults($limit = 10, $offset = 0)
    {
        $sql = "
            SELECT * FROM races ORDER BY date DESC LIMIT :limit OFFSET :offset
            ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $result = array();
        foreach ($rows as $row) {
            $result[] = $this->createRaceFromRow($row);
        }

        return $result;
    }

    public function createRaceFromRow($row)
    {
        $race = new Race();
        $race->setTitle($row['title']);
        return $race;
    }
}