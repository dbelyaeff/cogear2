<?php

/**
 * File upload class
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * 	File

 */
class File_Url_Upload extends File_Upload {

    /**
     * Upload file
     *
     * @param string $name
     * @param array $options
     * @return string|boolean
     */
    public function upload() {
        if($files = cogear()->input->post[$this->name]){
            return $this->uploadOne($files);
        }
        return FALSE;
    }

    /**
     * Upload one Files
     *
     * @param array $file
     * @return type
     */
    private function uploadOne($path) {
       ;
        $file = new Core_ArrayObject();
        event('file_url.preupload', $file);
        $file['errors'] = array();
        $file->source = $path;
        $file->ext = pathinfo($path,PATHINFO_EXTENSION);
        strpos($this->options->path, ROOT) !== FALSE OR $this->options->path = UPLOADS . DS . $this->options->path;
        if (!$data = file_get_contents($path)) {
            $file->errors[] = t('Не удаётся загрузить файл с указанного URL.');
        } else {
            File::mkdir($this->path);
            if (!is_dir($this->path)) {
                $file['errors'][] = t('Папка загрузки <b>%s</b> не существует.', $this->options->path);
            }
            $file->name = $this->prepareFileName($this->rename ? $this->rename : basename($path));
            $file->path = $this->path . DS . $file->name;
            if ($size = file_put_contents($file->path, $data)) {
                $file->size = $size;
                if ($this->options->allowed_types) {
                    $types = is_string($this->options->allowed_types) ?
                            new Core_ArrayObject(preg_split('#[^a-z]#', $this->options->allowed_types, -1, PREG_SPLIT_NO_EMPTY)) : $this->options->allowed_types;
                    $ext = pathinfo($file->name, PATHINFO_EXTENSION);
                    $result = FALSE;
                    foreach ($types as $type) {
                        $type == $ext && $result = TRUE;
                    }
                    !$result && $file['errors'][] = t('Разрешены только следующие типы файлов: <b>%s</b>.', NULL, $types->toString('</b>, <b>'));
                }
                $this->options->maxsize && $this->checkMaxSize($file['size'], $this->options->maxsize);
                if (!$this->options->path) {
                    $file['errors'][] = t('Путь для загрузки файла не указан.');
                }
               ;
                if ($file['errors']) {
                    unlink($file->path);
                    return $file;
                } else {
                    $this->uploaded = TRUE;
                    $file->uri_full = File::pathToUri($file->path, ROOT);
                    $file->uri = File::pathToUri($file->path, UPLOADS);
                    return $file;
                }
            }
        }
       ;
        return FALSE;
    }

}