<?php

class Db
{
    const host = DB_HOST;
    const dbname = DB_NAME;
    const uname = DB_USERNAME;
    const passe = DB_PASSE;

    private static $conn = null;

    /**
     * database Connect
     */
    public static function connect()
    {
        try {
            self::$conn = new PDO(
                'mysql:host=' . self::host . ';dbname=' . self::dbname,
                self::uname,
                self::passe
            );

            self::$conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $e) {
            die ($e->getMessage());
        }
    }

    /**
     * DB Select statement
     * @param string $query
     * @param array $bind
     * @param false $firstOnly
     * @return mixed
     */
    public static function select($query = '', $bind = array(), $firstOnly = false)
    {
        self::$conn || self::connect();

        $stmt = self::$conn->prepare($query);
        (!empty($bind)) ? $stmt->execute($bind) : $stmt->execute();

        return ($firstOnly) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert statement
     * @param string $query
     * @param array $bind
     * @return int last inserted id
     */
    public static function insert($query = '', $bind = array())
    {
        self::$conn || self::connect();
        $stmt = self::$conn->prepare($query);
        (!empty($bind)) ? $stmt->execute($bind) : $stmt->execute();
        return self::$conn->lastInsertId();
    }

    /**
     * @param string $query
     * @param array $bind
     * @return mixed
     */
    public static function update($query, $bind = array())
    {
        self::$conn || self::connect();
        return self::$conn->prepare($query)->execute($bind);
    }
}


# Como usar
# exemplo 1
$result = Db::select("SELECT CURRENT_TIMESTAMP()");
print_r($result);

#exemplo 2 - Selecionando 1 unico registro e usando parametro BIND
$result = Db::select(°SELECT * FROM tabela WHERE id = :id AND nome = :nome", array(':id' => 10, ':nome' => 'José'), true);
print_r($result);

# exemplo 3 - O mesmo exemplo de cima porem sem bind ou parametro de firstOnly
$result = Db::select(°SELECT * FROM tabela WHERE id = 10 AND nome = 'José' LIMIT 1");
print_r($result);
