<?php

/**
 * File Object
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class File_Object extends Adapter {
    /**
     * Measure constants
     */

    const Kb = 'Kb';
    const Mb = 'Mb';
    const Gb = 'Gb';
    const Tb = 'Tb';
    const Pb = 'Pb';
    const Kilobyte = 1024;
    const Megabyte = 1048576;
    const Gigabyte = 1073741824;
    const Terabyte = 1099511627776;
    const Petabyte = 1125899906842624;

    /**
     * Path to files
     *
     * @var string
     */
    protected $path;

    /**
     * Конструктор
     *
     * @param type $path
     * @param type $options
     */
    public function __construct($path, $options = array()) {
        parent::__construct($options);
        $this->object(new SplFileInfo($path));
    }

    /**
     * Render file
     */
    public function render() {
        $ext = self::extension($this->getBasename());
        return '<a href="' . $this->options->uri_full . '" class="icon-file-' . $ext . '">' . $this->getBasename() . '</a>';
    }

    /**
     * Make nice uri for file
     *
     * @param string $file
     * @param string
     */
    public static function pathToUri($file, $replace = ROOT) {
        return l(Url::toUri($file, $replace, FALSE));
    }

    /**
     * Get file extention
     *
     * @param  string $path
     * @return string
     */
    public static function extension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Переводим из байтов в более крупные разряды
     *
     * @param	int	$bytes
     * @param	const	$measure
     * @param	float	$round
     * @return	string
     */
    public static function fromBytes($bytes, $measure = NULL, $round = 0) {
        if (is_string($measure)){
            $measure = ucfirst($measure);
        }
        switch ($measure) {
            case NULL:
            case 'Auto':
                if ($bytes > self::Kilobyte && $bytes < self::Megabyte) {
                    return self::fromBytes($bytes, 'Kb', $round);
                }
                if ($bytes > self::Megabyte && $bytes < self::Gigabyte) {
                    return self::fromBytes($bytes, 'Mb', $round);
                }
                if ($bytes > self::Gigabyte && $bytes < self::Terabyte) {
                    return self::fromBytes($bytes, 'Gb', $round);
                }
                if ($bytes > self::Terabyte && $bytes < self::Petabyte) {
                    return self::fromBytes($bytes, 'Tb', $round);
                }
                if ($bytes > self::Petabyte) {
                    return self::fromBytes($bytes, 'Pb', $round);
                }
                break;
            case self::Pb:
                $bytes = $bytes / self::Petabyte;
                break;
            case self::Tb:
                $bytes = $bytes / self::Terabyte;
                break;
            case self::Gb:
                $bytes = $bytes / self::Gigabyte;
                break;
            case self::Mb:
                $bytes = $bytes / self::Megabyte;
                break;
            case self::Kb:
            default:
                $bytes = $bytes / self::Kilobyte;
        }
        return round($bytes, $round) . $measure;
    }

    /**
     * Переводит в байты
     *
     * @param string $size
     * @param string  $measure
     * @return int
     */
    public static function toBytes($size) {
        if (is_numeric($size)) {
            $size .= 'Kb';
        }
        if (preg_match('#(\d+)\s*(\w+)#im', $size, $matches)) {
            $size = $matches[1];
            $rank = ucfirst($matches[2]);
            switch ($rank) {
                case self::Pb:
                    $result = $size * self::Petabyte;
                    break;
                case self::Tb:
                    $result = $size * self::Terabyte;
                    break;
                case self::Gb:
                    $result = $size * self::Gigabyte;
                    break;
                case self::Mb:
                    $result = $size * self::Megabyte;
                    break;
                case self::Kb:
                default:
                    $result = $size * self::Kilobyte;
            }
            return $result;
        }
        return $size;
    }

    /**
     * Create dir if it's not exist
     *
     * @param string $dir
     * @param int $perms
     * @param boolean $recursive
     * @return  string
     */
    public static function mkdir($dir, $perms = 0777, $recursive = TRUE) {
        is_dir($dir) OR $dir && mkdir($dir, $perms, $recursive);
        return $dir;
    }

    /**
     * Delete file
     *
     * @param string $file
     */
    public static function delete($file) {
        @unlink($file);
    }

    /**
     * Read file
     *
     * @param string $path
     * @return string
     */
    public static function read($path) {
        return file_get_contents($path);
    }

    /**
     * Находит файлы согласно маске. В том числе и рекурсивно
     *
     * @param string $dir
     * @param string $mask
     * @param boolean $recursive
     * @return array
     */
    public static function findByMask($dir, $mask = '/^.+\.(php|js)$/i', $recursive = TRUE) {
        if ($recursive) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
        } else {
            $it = new IteratorIterator(DirectoryIterator($dir));
        }
        $it = new RegexIterator($it, $mask);
        $files = array();
        foreach ($it as $file) {
            $files[] = $file->__toString();
        }
        return $files;
    }

    /**
     * Отправка файла в браузер
     *
     * @param mixed $data
     * @param string $filename
     * @param boolean   $delete Флаг, удалять ли файл или нет
     */
    public static function download($data, $filename, $delete = FALSE) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        if (file_exists($data)) {
            header('Content-Length: ' . filesize($data));
            if ($fd = fopen($data, 'rb')) {
                while (!feof($fd)) {
                    print fread($fd, 1024);
                }
                fclose($fd);
            }
        } else {
            echo $data;
        }
        $delete && unlink($data);
        exit;
    }

}

