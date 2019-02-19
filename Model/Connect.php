<?php

namespace myblog\Model;

class Connect
{
    public static $conn;
    static function connectLi()
    {
        try {
            self::$conn = new \PDO('mysql:dbname=ztf;host=localhost', 'root', '2232050718');
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);//保存数据
            self::$conn->exec('set names utf8');//执行
        } catch (\PDOException $e) {
            "数据库连接失败：" . $e->getMessage();
        }
        return self::$conn;
    }
}


