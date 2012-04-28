<?php

/**
 * File upload class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage  	File
 * @version		$Id$
 */
class File_Upload extends Adapter {
    /**
     * Info about uploaded file
     * @var string
     */
    protected $file;

    /**
     * Uri to uploaded file
     *
     * @var string
     */
    protected $uri;
    /**
     * Configuration parameters
     *
     * maxsize / in Kb
     * rename / bool    Name to rename
     * overwrite / bool
     * path / string    Path to upload
     * allowed_types / string or array "jpg,png,gif"
     *
     * @var array
     */
    public $options = array(
        'name' => 'file',
        'allowed_types' => array(),
        'maxsize' => '',
        'overwrite' => TRUE,
        'path' => UPLOADS,
    );

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Upload file
     *
     * @param string $name
     * @param array $options
     * @return string|boolean
     */
    public function upload() {
        if (!isset($_FILES[$this->name]))
            return FALSE;
        $file = $_FILES[$this->name];
        $cogear = getInstance();
        d('File');
        event('file.preupload', $file);
        switch ($file['error']) {
            case UPLOAD_ERR_CANT_WRITE:
                $this->errors[] = t('Can\'t upload file. Check write permission for temporary folder.');
                break;
            case UPLOAD_ERR_INI_SIZE:
                $this->errors[] = t('File size is bigger that it\'s allowed in <b>php.ini</b> (%s).', NULL, ini_get('upload_max_filesize'));
                break;
            case UPLOAD_ERR_NO_FILE:
                if($this->options->validators->findByValue('Required') OR $this->options->required){
                    $this->errors[] = t('You didn\'t choose file to upload.');
                }
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->errors[] = t('Please, upload file once again.');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->errors[] = t('Temporary directory is not corrected.');
                break;
        }
        if ($file['error'] == UPLOAD_ERR_OK) {
            if ($this->options->allowed_types) {
                $types = is_string($this->options->allowed_types) ?
                        new Core_ArrayObject(preg_split('#[^a-z]#', $this->options->allowed_types, -1, PREG_SPLIT_NO_EMPTY)) : $this->options->allowed_types;
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $result = FALSE;
                foreach ($types as $type) {
                    $type == $ext && $result = TRUE;
                }
                !$result && $this->errors[] = t('Only following types of files are allowed: <b>%s</b>.', NULL, $types->toString('</b>, <b>'));
            }
            $result = File_Mime::check($file['name'], $file['type']);
            if ($result !== TRUE) {
                $this->errors[] = t('File you are trying to upload has unusual MIME-type. It is like <b>%s</b>, but it was expected to be <b>%s</b>', NULL, $file['type'], $result);
            }
            $this->options->maxsize && $this->checkMaxSize($file['size'], $this->options->maxsize);
            if (!$this->options->path) {
                $this->errors[] = t('Upload path is not defined.');
            }
            strpos($this->options->path, ROOT) !== FALSE OR $this->options->path = UPLOADS . DS . $this->options->path;
            File::mkdir($this->options->path);
            if (!is_dir($this->options->path)) {
                $this->errors[] = t('Upload path <b>%s</b> doesn\'t exist.', NULL, $this->options->path);
            }
            $file['name'] = $this->prepareFileName($file['name']);
            $file['path'] = $this->options->path . DS . $file['name'];
            $this->file = new Core_ArrayObject($file);
            d();
            if($this->errors){
                return FALSE;
            }
            else {
                return $this->process();
            }
        }
        d();
        return FALSE;
    }

    /**
     * Process upload
     *
     * @return string
     */
    protected function process() {
        if (file_exists($this->file->path) && !$this->options->overwrite) {
            $filename = pathinfo($this->file->name, PATHINFO_FILENAME);
            $this->file->path = str_replace($filename, $filename . '_' . time(), $this->file->path);
        }
        move_uploaded_file($this->file->tmp_name, $this->file->path);
        $this->uri = File::pathToUri($this->file->path,UPLOADS);
        return $this->uri;
    }

    /**
     * Check file size
     * @param string $size
     * @param string $maxsize
     * @return
     */
    public function checkMaxSize($size, $maxsize) {
        $maxsize = File::toBytes($maxsize);
        if ($size > $maxsize) {
            $this->errors[] = t('Max allowed size of file is <b>%s</b>, while you\'re trying to upload <b>%s</b>.', File::fromBytes($maxsize, 'Kb'), File::fromBytes($size, 'Kb'));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Prepare filename
     * @param string $filename
     */
    public function prepareFileName($filename) {
        if ($this->options->rename) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = $this->options->rename . '.' . $ext;
        }
        // Remove all unneseccary chars from filename
        $filename = Url::name($filename);
        return $filename;
    }

    /**
     * Render upload button
     */
    public function render() {
        $tpl = new Template($this->tpl);
        $tpl->name = $this->name;
        return $tpl->render();
    }

}