<?php

declare(strict_types=1);

namespace BNT\ADODB;

use PDO;

class ADOPDO extends PDO
{

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @param array<string, mixed> $types
     * @return array<string, mixed>|null
     */
    public function fetch(string $sql, array $params = [], array $types = []): ?array
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @param array<string, mixed> $types
     * @return array<mixed, mixed>
     */
    public function fetchAllKeyValue(string $sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @param array<string, mixed> $types
     * @return array<int, array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = [], array $types = []): array
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @param array<string, mixed> $types
     * @return mixed
     */
    public function column($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchColumn(0);
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @param array<string, mixed> $types
     * @return \PDOStatement
     */
    protected function prepareStmt(string $sql, array $params = [], array $types = []): \PDOStatement
    {
        $stmt = $this->prepare($sql);

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, isset($types[$param]) ? $types[$param] : PDO::PARAM_STR);
        }

        return $stmt;
    }

    public function adoExecute(string $sql): ADOPDOStatement
    {
        $stmt = $this->query($sql) ?: throw new \Exception($sql);

        return new ADOPDOStatement($stmt);
    }

    /**
     * @param string $sql
     * @param array<string, mixed> $params
     * @param array<string, mixed> $types
     * @return int
     */
    public function q(string $sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function ErrorNo(): ?string
    {
        return $this->errorCode();
    }

    public function ErrorMsg(): string
    {
        if ($this->errorCode() === '00000') {
            return '';
        }
        
        return implode(' ', $this->errorInfo());
    }
}
