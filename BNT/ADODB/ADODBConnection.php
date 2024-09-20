<?php

declare(strict_types=1);

namespace BNT\ADODB;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\AbstractException;

class ADODBConnection
{
    private Connection $connection;
    private string $type;
    private int $errorCode = 0;
    private ?string $errorMessage = null;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function Connect(string $host, string $username, string $password, string $name, $port): bool
    {
        try {
            $this->connection = DriverManager::getConnection([
                'dbname' => $name,
                'user' => $username,
                'password' => $password,
                'host' => $host,
                'port' => $port,
                'driver' => 'pdo_mysql',
            ]);
            $this->connection->connect();

            return $this->connection->isConnected();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function Execute($sql): ?ADODBResult
    {
        try {
            return new ADODBResult($this->connection->executeQuery($sql));
        } catch (AbstractException $ex) {
            $this->errorCode = $ex->getCode();
            $this->errorMessage = $ex->getMessage();
            return null;
        }
    }

    public function ErrorNo(): int
    {
        return $this->errorCode;
    }

    public function ErrorMsg(): ?string
    {
        return $this->errorMessage;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
