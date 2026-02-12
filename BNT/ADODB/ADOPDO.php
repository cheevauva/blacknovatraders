<?php

declare(strict_types=1);

namespace BNT\ADODB;

use PDO;

class ADOPDO extends PDO
{

    public function fetch($sql, array $params = [], array $types = []): ?array
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (empty($row)) {
            return null;
        }
        
        return $row;
    }
    
    
    public function fetchAllKeyValue($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public function fetchAll($sql, array $params = [], array $types = []): array
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function column($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchColumn(0);
    }

    /**
     * @param string $sql
     * @param array $params
     * @param array $types
     * @return PDOStatement
     */
    protected function prepareStmt($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepare($sql);

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, isset($types[$param]) ? $types[$param] : PDO::PARAM_STR);
        }

        return $stmt;
    }

    public function adoExecute($sql)
    {
        $stmt = $this->query($sql);

        return new ADOPDOStatement($stmt);
    }

    public function q($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    public function ErrorNo()
    {
        return $this->errorCode();
    }

    public function ErrorMsg()
    {
        return implode(' ', $this->errorInfo());
    }
}
