<?php

namespace Lightscale\Migrator;

class MigratorConfigException extends \Exception {};

class Migrator {

    private $config = NULL;

    public function __construct($config_file) {
        $this->config = require($config_file);

        $this->call_config_fn('init');
    }

    private function call_config_fn($name, $args = [], $required = false) {
        $fn = isset($this->config[$name]) ? $this->config[$name] : NULL;
        $callable = is_callable($fn);
        if($required && !$callable)
            throw(new MigratorConfigException(
                'Failed to call config function "' . $name . '"'
            ));

        if(!$required && !$callable) return NULL;

        return call_user_func_array($fn, $args);
    }

    private function require_config_fn($name, $args = []) {
        return $this->call_config_fn($name, $args, true);
    }

    private static function make_version() {
        $dt = new \DateTime();
        return $dt->format('ymd_His');
    }

    private function load_migrations() {
        return self::split_all_migrations(
            glob('./' . $this->config['migrations_dir'] . '/*')
        );
    }

    private function get_db() {
        return $this->require_config_fn('get_db_fn');
    }

    private function get_version($db) {
        return $this->require_config_fn('version_get_fn', [$db]);
    }

    private function set_version($db, $ver) {
        $this->require_config_fn('version_update_fn', [$db, $ver]);
    }

    private static function split_path($path) {
        $tmp = explode('/', $path);
        $file = end($tmp);
        $tmp = explode('_', $file);
        $version = array_shift($tmp) . '_' . array_shift($tmp);
        $name = implode($tmp, '_');
        $name = explode('.', $name);
        $name = array_shift($name);
        return [
            $path,
            $version,
            $name
        ];
    }

    private static function cb($fn) { return self::class . '::' . $fn; }

    private static function split_all_migrations($migrations) {
        return array_map(self::cb('split_path'), $migrations);
    }

    public function createMigration($name) {
        $fname = self::make_version() . "_{$name}.php";
        $dir = $this->config['migrations_dir'];

        $extends = (isset($this->config['migration_extends']) ?
                    $this->config['migration_extends'] : NULL);
        $extends = empty($extends) ? '' : ' extends ' . $extends;

        if(!file_exists($dir)) mkdir($dir, 0777, true);

        if(count(glob("{$dir}/*_{$name}.php")) > 0)
            throw new \Exception('A migration with that name already exists.');

        $path = $dir . '/' . $fname;

        $file = <<<FILE
<?php

use Lightscale\Migrator\Migration;

class {$name}{$extends} implements Migration {

    public function up(\$db) {

    }

    public function down(\$db) {

    }

}

FILE;

        if(file_put_contents($path, $file) !== FALSE) {
            return $fname;
        }
        return FALSE;
    }

    private static function execute_migration($m, $db, $fn, $logfn) {
        list($path, $version, $name) = $m;
        if($logfn) $logfn("Running:   {$version}_{$name}");
        try {
            require($path);
            $migration = new $name;
            call_user_func_array([$migration, $fn], [$db]);
        }
        catch(Exception $exception) {
            if($logfn) $logfn("Failed to complete {$version}_{$name}", true);
            throw $exception;
        }
        if($logfn) $logfn("Completed: {$version}_{$name}");
        return $version;
    }

    public function migrate($logfn = NULL) {
        $db = $this->get_db();
        $ver = $this->get_version($db);

        $migrations = $this->load_migrations();

        $remaining = array_filter($migrations, function($m) use ($ver) {
            $ver = empty($ver) ? '0' : $ver;
            list($p, $v) = $m;
            return $ver < $v;
        });

        if(empty($remaining)) {
            if($logfn) $logfn('All migrations are complete');
            return true;
        }

        $migration = NULL;

        foreach($remaining as $m) {
            try {
                $ver = self::execute_migration($m, $db,'up', $logfn);
            }
            catch (Exception $e) {
                $this->set_version($db, $ver);
                throw $e;
            }
        }

        $this->set_version($db, $ver);

        if($logfn)
            $logfn(sprintf('Ran %d migrations', count($remaining)));

        return true;
    }

    public function rollback($logfn = null) {
        $db = $this->get_db();
        $ver = $this->get_version($db);

        $migrations = $this->load_migrations();

        $current = null;
        $prev = null;
        foreach($migrations as $m) {
            list($p, $v) = $m;
            if($v === $ver) {
                $current = $m;
                break;
            }
            $prev = $m;
        }

        if(!$current) {
            if($logfn) $logfn('No more migrations to rollback');
            return true;
        }

        self::execute_migration($current, $db, 'down', $logfn);

        $this->set_version($db, $prev[1]);

        return true;
    }

    public function reset($logfn = null) {
        $db = $this->get_db();
        $ver = $this->get_version($db);

        $migrations = $this->load_migrations();

        $migrations = array_filter($migrations, function($m) use ($ver) {
            $ver = empty($ver) ? '0' : $ver;
            list($p, $v) = $m;
            return $ver >= $v;
        });

        $migrations = array_reverse($migrations);

        foreach($migrations as $m) {
            try {
                self::execute_migration($m, $db, 'down', $logfn);
            }
            catch (Exception $e) {
                $this->set_version($db, $ver);
                throw $e;
            }
        }

        $this->set_version($db, NULL);

        if($logfn)
            $logfn(sprintf('Rolled back %d migrations', count($migrations)));
        return true;
    }

}
