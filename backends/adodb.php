<?php

interface DatabaseInterface
{

    /**
     * Выполняет SQL-запрос и возвращает результат
     * @param string $query SQL-запрос
     * @return mixed Результат выполнения
     */
    public function Execute($query);

    /**
     * Выполняет SQL-запрос с подготовкой параметров
     * @param string $sql SQL-запрос с плейсхолдерами
     * @param array $params Параметры для подстановки
     * @return mixed Результат выполнения
     */
    public function exec($sql, $params = []);

    /**
     * Возвращает текст последней ошибки
     * @return string Сообщение об ошибке
     */
    public function ErrorMsg();

    /**
     * Возвращает количество строк в результате
     * @return int Количество строк
     */
    public function RecordCount();
}

// Также есть методы, которые предположительно существуют в ADOdb:
interface ADOdbConnectionInterface extends DatabaseInterface
{

    /**
     * Устанавливает постоянное соединение
     * @param string $host Хост
     * @param string $username Имя пользователя
     * @param string $password Пароль
     * @param string $database Имя базы данных
     * @return bool Результат подключения
     */
    public function PConnect($host, $username, $password, $database);

    /**
     * Устанавливает обычное соединение
     * @param string $host Хост
     * @param string $username Имя пользователя
     * @param string $password Пароль
     * @param string $database Имя базы данных
     * @return bool Результат подключения
     */
    public function Connect($host, $username, $password, $database);
}

// И типизация результата запроса:
interface DatabaseResultInterface
{

    /**
     * Возвращает текущую строку как ассоциативный массив
     * @return array
     */
    public function fields();

    /**
     * Перемещает указатель на следующую строку
     * @return bool
     */
    public function MoveNext();

    /**
     * Проверяет, достигнут ли конец результата
     * @return bool
     */
    public function EOF();
}

class ADOPDOStatement
{

    /**
     * @var \PDOStatement
     */
    protected $stmt;
    private $fields;
    public $EOF;

    public function __construct(\PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function RecordCount()
    {
        return $this->stmt->rowCount();
    }

    public function MoveNext()
    {
        $this->fields = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $this->EOF = empty($this->fields);
    }

    public function __get($name)
    {
        if ($name == 'fields') {
            $this->MoveNext();
            return $this->fields;
        }
    }
}

class ADOPDO extends \PDO
{

    public function fetch($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    public function fetchAllKeyValue($sql, array $params = [], array $types = [])
    {
        $stmt = $this->prepareStmt($sql, $params, $types);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public function fetchAll($sql, array $params = [], array $types = [])
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
