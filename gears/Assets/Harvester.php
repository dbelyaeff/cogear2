<?php

/**
 * Assets harvester class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Assets
 * @version		$Id$
 */
class Assets_Harvester {

    /**
     * Scripts
     *
     * @var Core_ArrayObject
     */
    private $scripts;

    /**
     * Groups of scripts that must be rendered
     *
     * @var array
     */
    public static $scriptsRenderGroups = array('default');

    /**
     * Styles
     *
     * @var Core_ArrayObject
     */
    private $styles;

    /**
     * Group of stypes that must be rendered
     *
     * @var array
     */
    public static $stylesRenderGroups = array('screen');

    /**
     * Constructor
     */
    public function __construct() {
        $this->clear();
        hook('head', array($this, 'output'), NULL, 'css');
        hook('head', array($this, 'output'), NULL, 'js');
    }

    /**
     * Add script
     *
     * @param   string  $path
     * @param   string  $group
     */
    public function addScript($path, $group = 'default') {
        $this->add($path, $group);
    }

    /**
     * Add script or style
     *
     * @param string $path
     * @param string $type
     */
    private function add($path, $type) {
        if (is_array($path)) {
            foreach ($path as $item) {
                $this->add($item, $type);
            }
        } else {
            $ext = File::extension($path);
            switch ($ext) {
                case 'js':
                    $method = 'attachScript';
                    break;
                case 'css':
                    $method = 'attachStyle';
                    break;
            }
            $special_types = self::checkConditions($path);
            if (FALSE === $special_types) {
                if ($new_type = self::defineGroupFromFilename($path)) {
                    if (is_array($new_type)) {
                        foreach ($new_type as $type) {
                            $this->$method($path, $type);
                        }
                    }
                } else {
                    $this->$method($path, $type);
                }
            } else {
                foreach ($special_types as $type) {
                    if ($ext == 'js') {
                        self::$scriptsRenderGroups[] = $type;
                    } elseif ($ext == 'css') {
                        self::$stylesRenderGroups[] = $type;
                    }
                    $this->$method($path, $type);
                }
            }
        }
    }

    /**
     * Attach script to some group
     *
     * @param string $path
     * @param string $group
     */
    private function attachScript($path, $group) {
        isset($this->scripts->$group) OR $this->scripts->$group = new Core_ArrayObject();
        $this->scripts->$group->$path = HTML::script(self::preparePath($path));
    }

    /**
     * This method simple look througth the file name and find snippets for groups
     *
     * Example: style@screen.css, style@print.css, style@screen+print.css
     *
     * @param string $path
     * @return  boolean|array
     */
    public static function defineGroupFromFilename($path) {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        if (preg_match("#@(.+)$#", $filename, $matches)) {
            return $result = explode(',', $matches[1]);
        }
        return FALSE;
    }

    /**
     * Check adding file for special contidions
     *
     * main[firefox7,ie9].js, super_stype[windows+ie9,android,linux+firefox]
     *
     * @param type $path
     * @return type
     */
    public static function checkConditions($path) {
        $filename = pathinfo($path, PATHINFO_FILENAME);
        if (preg_match("#\[([^\]]+)\]#", $filename, $matches)) {
// Get list of conditions
// For example: windows+ie_9,linux+firefox
            $conditions = explode(',', $matches[1]);
            $ua = cogear()->request->getUserAgent();
            $types = array();
            foreach ($conditions as $condition) {
                $subconditions = explode('+', $condition);
                $result = array();
                foreach ($subconditions as $subcondition) {
                    if ($subcondition == $ua['browser'] OR
                            $subcondition == $ua['browser'] . $ua['version'] OR
                            $subcondition == $ua['os'] OR
                            $subcondition == 'mobile' && !is_null($ua['is_mobile']) OR
                            $subcondition == $ua['locale']) {
                        $result[] = TRUE;
                    } else {
                        $result[] = FALSE;
                    }
                }
                if (!in_array(FALSE, $result)) {
                    $types[] = $condition;
                }
            }
            return $types;
        }
        return FALSE;
    }

    /**
     * Add directory with scripts
     *
     * @param   string  $path
     * @param   string  $group
     */
    public function addScriptsFolder($path, $group = 'default') {
        if (is_dir($path) && $files = glob($path . DS . '*.js')) {
            foreach ($files as $file) {
                $this->addScript($file, $group);
            }
        }
    }

    /**
     * Add style
     *
     * @param   string  $path
     */
    public function addStyle($path, $media = 'screen') {
        $this->add($path, $media);
    }

    /**
     * Attach style
     *
     * @param string $path
     * @param string $media
     */
    private function attachStyle($path, $media) {
        isset($this->styles->$media) OR $this->styles->$media = new Core_ArrayObject();
        $this->styles->$media->$path = HTML::style(self::preparePath($path), array('media' => $media));
    }

    /**
     * Add directory with styles
     *
     * @param   string  $path
     * @param   string  $media
     */
    public function addStylesFolder($path, $media = 'screen') {
        if (is_dir($path) && $files = glob($path . DS . '*.css')) {
            foreach ($files as $file) {
                $this->addStyle($file, $media);
            }
        }
    }

    /**
     * Prepare path to be valid like url
     *
     * @param   string  $path
     * @return  string
     */
    public static function preparePath($path) {
        if (strpos($path, ROOT) !== FALSE) {
            $path = str_replace(ROOT, '', $path);
            $path = Gear::normalizePath($path);
        }
        return Url::link($path);
    }

    /**
     * Get scripts
     *
     * @string  $group
     * @return string
     */
    public function getScripts($group = NULL) {
        if ($group) {
            return $this->scripts->$group->__toString() . "\n";
        }
        $output = '';
        foreach (self::$scriptsRenderGroups as $group) {
            $this->scripts->$group && $output .= $this->scripts->$group->__toString() . "\n";
        }
        $cogear = new Core_ArrayObject();
        $cogear->settings = new Core_ArrayObject();
        $cogear->settings->site = config('site.url');
        event('assets.js.global', $cogear);
        $code = "\n<script>var cogear = cogear || " . json_encode($cogear).'</script>';
        return $code.$output;
    }

    /**
     * Get styles
     *
     * @return string
     */
    public function getStyles($media = NULL) {
        if ($media) {
            $styles = $this->styles->$media->toArray();
        } else {
            $styles = array();
            foreach (self::$stylesRenderGroups as $group) {
                $this->styles->$group && $styles = array_merge($styles, $this->styles->$group->toArray());
            }
        }
        $theme_styles = array();
        foreach ($styles as $key => $value) {
            if (strpos($key, 'themes') !== FALSE) {
                $theme_styles[$key] = $value;
                unset($styles[$key]);
            }
        }
        return implode("\n", array_merge($theme_styles, $styles));
    }

    /**
     * Get assets = scripts + styles
     *
     * @return string
     */
    public function getAssets() {
        return $this->getScripts() . "\n" . $this->getStyles() . "\n";
    }

    /**
     * Output
     *
     * @param   string  $type
     */
    public function output($type = 'all') {
        switch ($type) {
            case 'js':
                echo $this->getScripts();
                break;
            case 'css':
                echo $this->getStyles();
                break;
            case 'all':
            default:
                echo $this->getAssets();
        }
    }

    /**
     * Reset styles and scripts
     */
    public function clear() {
        $this->scripts = new Core_ArrayObject();
        $this->styles = new Core_ArrayObject();
    }

}

function css($url, $region='content') {
    append($region, HTML::style($url));
}

function js($url, $region='content') {
    append($region, HTML::script($url));
}

function inline_js($code, $region='content') {
    append($region, HTML::script($code, array(), TRUE));
}