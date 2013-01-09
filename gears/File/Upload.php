<?php

/**
 * Класс загрузки файла
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru

 */
class File_Upload extends Notify_Handler {

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
    protected $options = array(
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
        if (!isset($_FILES[$this->name]) OR empty($_FILES[$this->name]['name'])) {
            if (strpos($this->options->validators->toString(), 'Required') !== FALSE) {
                $this->error(t('Выберите файл для загрузки.'));
            }
            return FALSE;
        }
        $files = $_FILES[$this->name];
        if (is_array($files['name'])) {
            $files_upload = array();
            for ($i = 0; $i < sizeof($files['name']); $i++) {
                foreach ($files as $key => $value) {
                    $files_upload[$i][$key] = $files[$key][$i];
                }
            }
            $result = array();
            foreach ($files_upload as $file) {
                $result[] = $this->uploadOne($file);
            }
            return $result;
        } else {
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
        $file = new Core_ArrayObject($file);
        event('file.preupload', $file);
        switch ($file['error']) {
            case UPLOAD_ERR_CANT_WRITE:
                $this->error(t('Не удаётся загрузить файл. Проверьте права на временую папку загрузки.'));
                break;
            case UPLOAD_ERR_INI_SIZE:
                $this->error(t('Файл больше максимально дозволенного размера, указанного в <b>php.ini</b> (%s).', ini_get('upload_max_filesize')));
                break;
            case UPLOAD_ERR_PARTIAL:
                $this->error(t('Пожалуйста, загрузите файл снова.'));
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->error(t('Неверно указана временная директория загрузки.'));
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
                !$result && $this->error(t('Разрешены только следующие типы файлов: <b>%s</b>.', $types->toString('</b>, <b>')));
            }
            $result = File_Mime::check($file['name'], $file['type']);
            if ($result !== TRUE) {
                $this->error(t('Загружаемый вами файл имеет некорректный MIME-типа. Он должен иметь тип <b>%s</b>, но имеет иной тип — <b>%s</b>', $file['type'], $result));
            }
            $this->options->maxsize && $this->checkMaxSize($file['size'], $this->options->maxsize);
            if (!$this->options->path) {
                $this->error(t('Путь для загрузки файла не указан.'));
            }
            strpos($this->options->path, ROOT) !== FALSE OR $this->options->path = UPLOADS . DS . $this->options->path;
            File::mkdir($this->options->path);
            if (!is_dir($this->options->path)) {
                $this->error(t('Путь загрузки файла <b>%s</b> не существует.', NULL, $this->options->path));
            }
            $file['name'] = $this->prepareFileName($file['name']);
            $file['path'] = $this->options->path . DS . $file['name'];
            if ($this->errors->count()) {
                return FALSE;
            } else {
                return $this->file = $this->process($file);
            }
        }
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
            $file->path = str_replace($filename, time() . '_' . $filename, $file->path);
        }
        if (move_uploaded_file($file->tmp_name, $file->path)) {
            $this->uploaded = TRUE;
            $file->uri_full = File::pathToUri($file->path, ROOT);
            $file->uri = File::pathToUri($file->path, UPLOADS);
        }
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
            $this->error(t('Максимально разрешенный размер загружаемого файла составляет <b>%s</b>. Вы же пытаетесь загрузкить файл размером <b>%s</b>.', File::fromBytes($maxsize), File::fromBytes($size, 'auto',2)));
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