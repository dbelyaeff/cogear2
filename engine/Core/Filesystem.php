<?php

/**
 * Filesystem class
 *
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Filesystem {
    /**
     * Measure constants
     */
    const Kb = 'Kb';
    const Mb = 'Mb';
    const Gb = 'Gb';
    const Tb = 'Tb';
    const Pb = 'Pb';

    /**
     * Get file extention
     *
     * @param  string $path
     * @return string
     */
    public static function getExtension($path) {
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
    public static function makeDir($dir, $perms = 0777, $recursive = TRUE) {
        is_dir($dir) OR $dir && mkdir($dir, $perms, $recursive);
        return $dir;
    }
    /**
     * Delete file
     * 
     * @param string $file 
     */
    public static function delete($file){
        @unlink($file);
    }
    /**
     * Read file
     *  
     * @param string $path
     * @return string 
     */
    public static function read($path){
        return file_get_contents($path);
    }

}