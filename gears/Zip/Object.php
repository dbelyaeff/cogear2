<?php

/**
 * Архиватор
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Zip_Object extends Object {

    protected $options = array(
        'file' => '',
        'create' => FALSE,
        'check' => FALSE,
    );

    public function __construct($file, $options = array()) {
        if (is_string($file)) {
            $options['file'] = $file;
        } elseif (is_array($file)) {
            $options = $file;
        }
        parent::__construct($options);
        $this->object(new ZipArchive());
        if ($this->options->create) {
            $this->open($this->options->file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        } else {
            $this->open($this->options->file);
        }
    }

    /**
     * Добавляет один файл или целую директорию с файлами рекурсивно
     *
     * @param string $path
     * @param string $mask
     */
    public function add($path, $mask = '#^[^\.].+#') {
        if (is_dir($path)) {
            $files = File::findByMask($path, $mask);
            foreach ($files as $file) {
                $archive_file = str_replace(dirname($path) . DS, '', $file);
                $this->addFile($file, $archive_file);
            }
        } elseif (file_exists($path)) {
            $this->addFile($path, basename($path));
        }
    }

    /**
     * Распаковка архива по указанному пути
     *
     * @param string $path
     * @param array $entries
     */
    public function extract($path, $entries = NULL) {
        is_dir($path) OR File::mkdir($path);
        if ($this->check()) {
            if ($this->extractTo($path, $entries)) {
                return TRUE;
            }
        }
        $this->error(t('Вы загружаете архив неверного формата!'));
        return FALSE;
    }

    /**
     * Проверяет массив с условиями
     */
    public function check($check = array()) {
        if ($check OR $check = $this->options->check) {
            $info = $this->info();
            foreach ($check as $key => $value) {
                if (!isset($info[$key]) OR $info[$key] != $value) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    /**
     * Пишет или читает метаинформацию
     *
     * @param array $data
     * @return mixed
     */
    public function info($data = array()) {
        if ($data) {
            $this->setArchiveComment(base64_encode(serialize($data)));
        } else {
            if ($info = unserialize(base64_decode($this->getArchiveComment()))) {
                return new Core_ArrayObject($info);
            } else {
                $this->error(t('Неверно указана или отсутствует цифровая подпись архива. Принимаются только архивы, выгруженные через панель управления.'));
            }
            return NULL;
        }
    }

    /**
     * Закрытие архива
     *
     * Вывод ошибок
     */
    public function close() {
        if ($errors = $this->getErrors()) {
            error($errors->toString('<br>'), '', 'content');
        }
        $this->object() && $this->object()->close();
    }

}