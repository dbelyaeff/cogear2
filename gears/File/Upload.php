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
     * If one file has been uploaded
     *
     * @var type
     */
    protected $uploaded;
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
     * Upload file
     *
     * @param string $name
     * @param array $options
     * @return string|boolean
     */
    public function upload() {
        if (!isset($_FILES[$this->name]))
            return FALSE;
        $files = $_FILES[$this->name];
        if(is_array($files['name'])){
            $files_upload = array();
            for($i = 0; $i < sizeof($files['name']);$i++){
                foreach($files as $key=>$value){
                    $files_upload[$i][$key] = $files[$key][$i];
                }
            }
            $result = array();
            foreach($files_upload as $file){
                $result[] = $this->uploadOne($file);
            }
            return $result;
        }
        else{
            return $this->uploadOne($files);
        }
    }

    /**
     * Upload one Files
     *
     * @param array $file
     * @return type
     */
    private function uploadOne($file) {
        d('File');
        $file = new Core_ArrayObject($file);
        event('file.preupload', $file);
        $file['errors'] = array();
        switch ($file['error']) {
            case UPLOAD_ERR_CANT_WRITE:
                $file['errors'][] = t('Can\'t upload file. Check write permission for temporary folder.');
                break;
            case UPLOAD_ERR_INI_SIZE:
                $file['errors'][] = t('File size is bigger that it\'s allowed in <b>php.ini</b> (%s).', NULL, ini_get('upload_max_filesize'));
                break;
            case UPLOAD_ERR_NO_FILE:
                if ($this->options->validators->findByValue('Required') OR $this->options->required) {
                    $file['errors'][] = t('You didn\'t choose file to upload.');
                }
                break;
            case UPLOAD_ERR_PARTIAL:
                $file['errors'][] = t('Please, upload file once again.');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $file['errors'][] = t('Temporary directory is not corrected.');
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
                !$result && $file['errors'][] = t('Only following types of files are allowed: <b>%s</b>.', NULL, $types->toString('</b>, <b>'));
            }
            $result = File_Mime::check($file['name'], $file['type']);
            if ($result !== TRUE) {
                $file['errors'][] = t('File you are trying to upload has unusual MIME-type. It is like <b>%s</b>, but it was expected to be <b>%s</b>', NULL, $file['type'], $result);
            }
            $this->options->maxsize && $this->checkMaxSize($file['size'], $this->options->maxsize);
            if (!$this->options->path) {
                $file['errors'][] = t('Upload path is not defined.');
            }
            strpos($this->options->path, ROOT) !== FALSE OR $this->options->path = UPLOADS . DS . $this->options->path;
            File::mkdir($this->options->path);
            if (!is_dir($this->options->path)) {
                $file['errors'][] = t('Upload path <b>%s</b> doesn\'t exist.', NULL, $this->options->path);
            }
            $file['name'] = $this->prepareFileName($file['name']);
            $file['path'] = $this->options->path . DS . $file['name'];
            d();
            if ($file['errors']) {
                return $file;
            } else {
                return $this->process($file);
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
    protected function process($file) {
        if (file_exists($file->path) && $this->options->overwrite == FALSE) {
            $filename = pathinfo($file->name, PATHINFO_FILENAME);
            $file->path = str_replace($filename, time().'_'.$filename, $file->path);
        }
        move_uploaded_file($file->tmp_name, $file->path);
        $this->uploaded = TRUE;
        $file->uri_full = File::pathToUri($file->path, ROOT);
        $file->uri = File::pathToUri($file->path, UPLOADS);
        return $file;
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
            $file['errors'] = t('Max allowed size of file is <b>%s</b>, while you\'re trying to upload <b>%s</b>.','File', File::fromBytes($maxsize, 'Kb'), File::fromBytes($size, 'Kb'));
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