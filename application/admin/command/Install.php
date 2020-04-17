<?php

namespace app\admin\command;

use PDO;
use think\facade\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Exception;

class Install extends Command
{
    protected $model = null;

    protected function configure()
    {
        $config = Config::pull('database');
        $this
            ->setName('install')
            ->addOption('hostname', 'a', Option::VALUE_OPTIONAL, 'mysql hostname', $config['hostname'])
            ->addOption('hostport', 'o', Option::VALUE_OPTIONAL, 'mysql hostport', $config['hostport'])
            ->addOption('database', 'd', Option::VALUE_OPTIONAL, 'mysql database', $config['database'])
            ->addOption('prefix', 'r', Option::VALUE_OPTIONAL, 'table prefix', $config['prefix'])
            ->addOption('username', 'u', Option::VALUE_OPTIONAL, 'mysql username', $config['username'])
            ->addOption('password', 'p', Option::VALUE_OPTIONAL, 'mysql password', $config['password'])
            ->addOption('force', 'f', Option::VALUE_OPTIONAL, 'force override', false)
            ->setDescription('New installation of FastAdmin');
    }

    protected function execute(Input $input, Output $output)
    {
        // 覆盖安装
        $force = $input->getOption('force');
        $hostname = $input->getOption('hostname');
        $hostport = $input->getOption('hostport');
        $database = $input->getOption('database');
        $prefix = $input->getOption('prefix');
        $username = $input->getOption('username');
        $password = $input->getOption('password');

        $installLockFile = __DIR__ . "/Install/install.lock";
        if (is_file($installLockFile) && !$force) {
            throw new Exception("\nFastAdmin already installed!\nIf you need to reinstall again, use the parameter --force=true ");
        }

        $sql = file_get_contents(__DIR__ . '/Install/fastadmin.sql');

        $sql = str_replace("`fa_", "`{$prefix}", $sql);

        // 先尝试能否自动创建数据库
        $config = Config::pull('database');
        $pdo = new PDO("{$config['type']}:host={$hostname}" . ($hostport ? ";port={$hostport}" : ''), $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}"
        ]);
        //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //检测是否支持innodb存储引擎
        $pdoStatement = $pdo->query("SHOW VARIABLES LIKE 'innodb_version'");
        $result = $pdoStatement->fetch();
        if (!$result) {
            throw new Exception("当前数据库不支持innodb存储引擎，请开启后再重新尝试安装");
        }
        $pdo->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8 COLLATE utf8_general_ci;");
        $pdo->query("USE `{$database}`");
        $pdo->exec($sql);

        file_put_contents($installLockFile, 1);

        $dbConfigFile = ROOT_PATH . 'config/database.php';

        $config = @file_get_contents($dbConfigFile);
        $callback = function ($matches) use ($hostname, $hostport, $username, $password, $database, $prefix) {
            $field = $matches[1];
            $replace = $$field;
            if ($matches[1] == 'hostport' && $hostport == 3306) {
                $replace = '';
            }
            return "'{$matches[1]}'{$matches[2]}=>{$matches[3]}Env::get('database.{$matches[1]}', '{$replace}'),";
        };
        $config = preg_replace_callback("/'(hostname|database|username|password|hostport|prefix)'(\s+)=>(\s+)Env::get\((.*)\)\,/", $callback, $config);
        // 写入数据库配置
        file_put_contents($dbConfigFile, $config);

        \think\facade\Cache::rm('__menu__');

        $output->info("Install Successed!");
    }
}
