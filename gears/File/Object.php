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
     * Transform bytes to other measures
     *
     * @param	int	$bytes
     * @param	const	$measure
     * @param	float	$round
     * @return	string
     */
    public static function fromBytes($bytes, $measure = null, $round = 0.2) {
        if (is_null($measure))
            $measure = self::Kb;
        elseif (is_string($measure))
            $measure = ucfirst($measure);
        switch ($measure) {
            case self::Pb:
                $result = $byte / 1024 / 1024 / 1024 / 1024;
                break;
            case self::Tb:
                $result = $byte / 1024 / 1024 / 1024;
                break;
            case self::Gb:
                $result = $byte / 1024 / 1024;
                break;
            case self::Mb:
                $result = $byte / 1024;
                break;
            case self::Kb:
            default:
                $result = $bytes / 1024;
        }
        return round($result, $round) . $measure;
    }

    /**
     * Transform any size string to bytes
     *
     * 1Kb = 1024
     * 1 Kb = 1024
     *
     * @param string $size
     * @param float  $round
     * @return int
     */
    public static function toBytes($size) {
        if (is_numeric($size))
            return $size;
        if (preg_match('#(\d+)\s*(\w+)#im', $size, $matches)) {
            $byte = $matches[1];
            $rank = ucfirst($matches[2]);
            switch ($rank) {
                case self::Pb:
                    $result = $byte * 1024 * 1024 * 1024 * 1024;
                    break;
                case self::Tb:
                    $result = $byte * 1024 * 1024 * 1024;
                    break;
                case self::Gb:
                    $result = $byte * 1024 * 1024;
                    break;
                case self::Mb:
                    $result = $byte * 1024;
                    break;
                case self::Kb:
                default:
                    $result = $byte * 1024;
            }
            return $result;
        }
        return NULL;
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

