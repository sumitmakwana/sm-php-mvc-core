<?php

namespace smcodes\phpmvc\db;

use smcodes\phpmvc\Application;

class Database
{
    public \PDO $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? "";
        $user = $config['user'] ?? "";
        $password = $config['password'] ?? "";

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigratoins = $this->getAppliedMigrations();

        $newMigrations = [];

        $files = scandir(Application::$ROOT_DIR."/migrations");
        $toApplyMigrations = array_diff($files,$appliedMigratoins);

        foreach ($toApplyMigrations as $migration) {
            if($migration === "." || $migration === ".." || trim($migration) === "")
            {
                continue;
            }

            require_once Application::$ROOT_DIR."/migrations/".$migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);

            $instance = new $className();
            $this->log("Applying migration $migration.");

            $instance->up();
            $this->log("Applied migration $migration successfully.");

            $newMigrations[] = $migration;
        }
        if (!empty($newMigrations))
        {
            $this->saveMigrations($newMigrations);
        }
        else {
//            $this->log("All migrations are Applied.");
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("create table if not EXISTS migrations (
        id int AUTO_INCREMENT PRIMARY KEY,
        migration varchar(255),
        created_at datetime default CURRENT_TIMESTAMP
    ) ENGINE=INNODB;");
    }

    private function getAppliedMigrations()
    {
         $statement = $this->pdo->prepare("select migration from migrations");
         $statement->execute();
         return $statement->fetchAll(\PDO::FETCH_COLUMN);

    }

    public function saveMigrations(array $newMigrations)
    {
        $str = implode(",",array_map(fn($m) => "('$m')",$newMigrations));

        $statement = $this->pdo->prepare("insert into migrations (migration) values $str");

        $statement->execute();
    }

    public function  prepare($sql)
    {
         return $this->pdo->prepare($sql);
    }

    protected function log($message)
    {
        echo '['.date('d-m-Y H:i:s').'] - '.$message.PHP_EOL;
    }
}