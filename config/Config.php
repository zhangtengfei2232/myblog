<?php
namespace  MyWeb\config;

class Config
{
      public $config = [];
      static public $instance = null;
    public  function __construct(){
        $this->config = include('./config.php');
    }
      static public function getInstance(){
          if (self::$instance == null) {
              self::$instance = new Config();
          }
          return self::$instance->config;
      }
}