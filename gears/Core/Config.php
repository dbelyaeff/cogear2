<?php

/**
 * Config
 *
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Config extends Core_ArrayObject {

    protected $file;
    protected $write_flag;

    const AS_ARRAY = 1;
    const AS_OBJECT = 21;

    /**
     * Constructor
     *
     * @param string $path
     * @param string $section
     */
    public function __construct($path = '', $section = '') {
        if ($path) {
            $this->file = $path;
            $this->load($path, $section);
            hook('exit', array($this, 'store'));
        }
    }

    /**
     * Load file into internal config
     *
     * @param   string  $path
     * @param   string  $section
     */
    public function load($path, $section = '') {
        if (!file_exists($path)) {
            return;
        }
        if ($section) {
            $this->$section OR $this->$section = new Core_ArrayObject();
            if ($config = self::read($path)) {
                $this->$section->extend($config);
            }
        } else {
            if ($config = self::read($path)) {
                $this->extend($config);
            }
        }
    }

    /**
     * Get config var
     *
     * @param   string  $name
     * @param   string  $default
     * @return  string
     */
    public function get($name = NULL, $default = NULL) {
        if ($name === NULL) {
            return $this;
        }
        $pieces = explode('.', $name);
        $size = sizeof($pieces);
        $current = $this;
        $depth = 1;
        foreach ($pieces as $piece) {
            if ($current->$piece !== NULL) {
                if ($depth < $size && $current->$piece instanceof Core_ArrayObject) {
                    $current = $current->$piece;
                    $depth++;
                    continue;
                }
                return $current->$piece;
            } else {
                return $current->$piece ? $current->$piece : $default;
            }
        }
        return $default;
    }

    /**
     * Set config value
     *
     * @param type $name
     * @param type $value
     * @return  boolean
     */
    public function set($name, $value) {
        $pieces = explode('.', $name);
        $current = $this;
        $i = 0;
        $size = sizeof($pieces);
        foreach ($pieces as $piece) {
            if ($i < $size - 1) {
                if ($current->$piece && $current->$piece instanceof Core_ArrayObject) {
                    $current = $current->$piece;
                } else {
                    $current->$piece = new Core_ArrayObject();
                    $current = $current->$piece;
                }
            } else {
                $current->$piece = $value;
            }
            $i++;
        }
        $current = $value;
        $this->write_flag = TRUE;
        return TRUE;
    }

    /**
     * Read config from file
     *
     * @param string $file
     */
    public static function read($file, $mode = NULL) {
        $mode OR $mode = self::AS_ARRAY;
        if (!file_exists($file)) {
            return NULL;
        } elseif ($mode === self::AS_OBJECT) {
            return Core_ArrayObject::transform(include $file);
        } elseif ($mode === self::AS_ARRAY) {
            return include $file;
        }
    }

    /**
     * Save config to file
     *
     * @param string $file
     * @param array $data
     */
    public function store($force = FALSE) {
        if (!$this->file)
            return FALSE;
        if ($this->write_flag OR $force) {
            if (self::write($this->file, $this->toArray())) {
                return TRUE;
            } else {
                error(t('Не могу записать в файл:<br/>
                <b>%s</b><br/>
                Пожалуйста, проверьте права (должны быть 0755 как минимум).', $file));
                return FALSE;
            }
        }
        return NULL;
    }

    /**
     * Write data
     *
     * @param string $file
     * @param mixed $data
     * @return  mixed
     */
    public static function write($file, $data) {
        File::mkdir(dirname($file));
        $data = var_export($data, TRUE);
        // Now we need to replace paths with constants
        $constants = get_defined_constants(true);
        $paths = array();
        foreach ($constants['user'] as $key => $value) {
            if (PHP_FILE_PREFIX === $value)
                continue;
            if (is_string($value) && strlen($value) > 5 && is_dir($value)) {
                $paths["'" . $value] = $key . '.\'';
            }
        }
        $paths = array_reverse($paths);
        $data = str_replace(DS . DS, DS, $data);
        $data = str_replace(array_keys($paths), array_values($paths), $data);
        // Done
        return file_put_contents($file, PHP_FILE_PREFIX . "return " . $data . ';');
    }

}