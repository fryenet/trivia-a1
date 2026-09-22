<?php
require_once __DIR__.'/config.php';

class DB {
    private $pdo;
    public function __construct() {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        if (preg_match('/^\s*SELECT/i', $sql)) {
            return $stmt->fetchAll();
        }
        return $stmt->rowCount();
    }
    public function queryOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    public function queryValue($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    public function insert($table, $data) {
        $cols = array_keys($data);
        $place = array_map(fn($c)=>":$c", $cols);
        $sql = "INSERT INTO $table (".implode(',',$cols).") VALUES (".implode(',',$place).")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }
    public function pdo(){ return $this->pdo; }
}
$db = new DB();
?>
