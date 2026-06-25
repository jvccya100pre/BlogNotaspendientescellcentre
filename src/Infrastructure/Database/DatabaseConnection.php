<?php
/**
 * Database Connection Singleton
 * Compatible with PHP 5.2.3
 */

// MySQLi to PDO Polyfill/Fallback for servers with mysqli disabled
if (!function_exists('mysqli_connect')) {
    $GLOBALS['mysqli_connect_error_message'] = '';
    $GLOBALS['mysqli_last_error_message'] = '';

    class PdoMysqliStmtWrapper {
        private $pdo;
        private $stmt;
        private $params = array();

        public function __construct($pdo, $query) {
            $this->pdo = $pdo;
            $this->stmt = $pdo->prepare($query);
        }

        public function setParamRef($index, &$var) {
            $this->params[$index] =& $var;
        }

        public function execute() {
            $args = array();
            ksort($this->params);
            foreach ($this->params as $k => &$v) {
                $args[] = $v;
            }
            try {
                return $this->stmt->execute($args);
            } catch (Exception $e) {
                $GLOBALS['mysqli_last_error_message'] = $e->getMessage();
                return false;
            }
        }
    }

    function mysqli_connect($host, $user, $pass, $name) {
        try {
            $dsn = "mysql:host=" . $host . ";dbname=" . $name . ";charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            $GLOBALS['mysqli_connect_error_message'] = $e->getMessage();
            return false;
        }
    }

    function mysqli_connect_error() {
        return $GLOBALS['mysqli_connect_error_message'];
    }

    function mysqli_set_charset($link, $charset) {
        try {
            $link->exec("SET NAMES " . $charset);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function mysqli_real_escape_string($link, $str) {
        if ($link instanceof PDO) {
            $quoted = $link->quote($str);
            if ($quoted === false) {
                return addslashes($str);
            }
            return substr($quoted, 1, -1);
        }
        return addslashes($str);
    }

    function mysqli_query($link, $query) {
        try {
            $stmt = $link->query($query);
            return $stmt;
        } catch (Exception $e) {
            $GLOBALS['mysqli_last_error_message'] = $e->getMessage();
            return false;
        }
    }

    function mysqli_fetch_assoc($result) {
        if ($result instanceof PDOStatement) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    function mysqli_fetch_row($result) {
        if ($result instanceof PDOStatement) {
            return $result->fetch(PDO::FETCH_NUM);
        }
        return false;
    }

    function mysqli_num_rows($result) {
        if ($result instanceof PDOStatement) {
            return $result->rowCount();
        }
        return 0;
    }

    function mysqli_insert_id($link) {
        if ($link instanceof PDO) {
            return $link->lastInsertId();
        }
        return 0;
    }

    function mysqli_error($link) {
        return $GLOBALS['mysqli_last_error_message'];
    }

    function mysqli_autocommit($link, $mode) {
        if ($link instanceof PDO) {
            try {
                if ($mode === FALSE) {
                    if (!$link->inTransaction()) {
                        $link->beginTransaction();
                    }
                } else {
                    if ($link->inTransaction()) {
                        $link->commit();
                    }
                }
                return true;
            } catch (Exception $e) {
                $GLOBALS['mysqli_last_error_message'] = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    function mysqli_commit($link) {
        if ($link instanceof PDO) {
            try {
                if ($link->inTransaction()) {
                    $link->commit();
                }
                return true;
            } catch (Exception $e) {
                $GLOBALS['mysqli_last_error_message'] = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    function mysqli_rollback($link) {
        if ($link instanceof PDO) {
            try {
                if ($link->inTransaction()) {
                    $link->rollBack();
                }
                return true;
            } catch (Exception $e) {
                $GLOBALS['mysqli_last_error_message'] = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    function mysqli_prepare($link, $query) {
        if ($link instanceof PDO) {
            try {
                return new PdoMysqliStmtWrapper($link, $query);
            } catch (Exception $e) {
                $GLOBALS['mysqli_last_error_message'] = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    function mysqli_stmt_bind_param($stmt, $types, &$var1 = null, &$var2 = null, &$var3 = null, &$var4 = null) {
        if ($stmt instanceof PdoMysqliStmtWrapper) {
            $num = func_num_args();
            if ($num > 2) $stmt->setParamRef(0, $var1);
            if ($num > 3) $stmt->setParamRef(1, $var2);
            if ($num > 4) $stmt->setParamRef(2, $var3);
            if ($num > 5) $stmt->setParamRef(3, $var4);
            return true;
        }
        return false;
    }

    function mysqli_stmt_execute($stmt) {
        if ($stmt instanceof PdoMysqliStmtWrapper) {
            return $stmt->execute();
        }
        return false;
    }

    function mysqli_stmt_close($stmt) {
        return true;
    }
}

class DatabaseConnection {
    private static $instance = null;

    /**
     * Get the MySQLi database connection instance
     * @return mysqli
     */
    public static function getInstance() {
        if (self::$instance === null) {
            $config = array(
                'host' => 'localhost',
                'name' => 'createso_datosVPS',
                'user' => 'createso_vpsdatos',
                'pass' => 'Tresado37#',
                'charset' => 'utf8mb4'
            );
            
            self::$instance = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['name']);
            
            if (!self::$instance) {
                die("Error de conexión a la base de datos: " . mysqli_connect_error());
            }
            
            mysqli_set_charset(self::$instance, $config['charset']);
        }
        return self::$instance;
    }
}
