<?php

/**
 * Элемент файла для формы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class File_Element extends Form_Element_Abstract {

    protected $template = 'File/templates/element';

    /**
     * Process elements value from request
     *
     * @return
     */
    public function result() {
        $file = new File_Upload($this->options);
        if ($result = $file->upload()) {
            $this->is_fetched = TRUE;
            $result = File::pathToUri($result->path, UPLOADS);
        } else {
            $this->errors = $file->getErrors();
        }
        if(cogear()->input->post($this->name)){
            return '';
        }
        if ($this->validate()) {
            if ($result) {
                return $result;
            }
            return $this->errors ? FALSE : NULL;
        }
        return FALSE;
    }

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        if (NULL == $this->options->value) {
            if ($this->options->allowed_types) {
                $this->notice(t('Следующие типы файлов разрешены к загрузке: <b>%s</b>.', $this->options->allowed_types->toString('</b>, <b>')));
            }
            if ($this->options->maxsize) {
                $this->notice(t('Максимальный размер файла <b>%s</b>.', File::fromBytes(File::toBytes($this->options->maxsize), NULL, 2)));
            }
            if ($this->notices->count()) {
                $this->options->description .= '<ul class="file-notice"><li>' . $this->notices->toString('</li><li>') . '</li></ul>';
            }
        }
        $tpl = new Template($this->template);
        $tpl->assign($this->options);
        $this->code = $tpl->render();
        $this->decorate();
        return $this->code;
    }

}