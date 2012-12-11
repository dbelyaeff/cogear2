<?php

/**
 *  Объект шаблона
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru

 */
class Template_Object extends Core_ArrayObject {

    protected $name = '';
    protected $path = '';
    protected $code = '';
    protected $vars = array();

    /**
     * Конструктор
     *
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
        $path = Gear::preparePath($this->name);
        if (file_exists($path)) {
            $this->path = $path;
        } else {
            $message = t('Template <b>%s</b> is not found by path <u>%s</u>.', $name, $path);
            exit($message);
        }
    }

    /**
     * Магический метод для задания значения переменным
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->set($name, $value);
    }

    /**
     * Задание значения переменной
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value = NULL) {
        if (is_array($name) OR $name instanceof ArrayObject) {
            foreach ($name as $key => $value) {
                $this->set($key, $value);
            }
            return;
        }
        else
            $this->vars[$name] = $value;
    }

    /**
     * Сброс переменных шаблона
     */
    public function reset() {
        $this->vars = array();
    }

    /**
     * Установка переменной
     *
     * @param string $name
     * @param mixed $value
     */
    public function assign() {
        $args = func_get_args();
        call_user_func_array(array($this, 'set'), $args);
    }

    /**
     * Магический метод вызова переемнной
     *
     * @param   string  $name
     * @return mixed
     */
    public function __get($name) {
        return $name ? (isset($this->vars[$name]) ? $this->vars[$name] : NULL) : $this->vars;
    }

    /**
     * Магический метод проверки свойства
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->vars[$name]);
    }

    /**
     * Вызов переменной
     *
     * @param   string  $name
     * @return mixed
     */
    public function get($name = '') {
        return $name ? (isset($this->vars[$name]) ? $this->vars[$name] : NULL) : $this->vars;
    }

    /**
     * Привязка переменной
     *
     * @param string $name
     * @param mixed $value
     */
    public function bind($name, &$value = NULL) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->bind($key, $value);
            }
            return;
        } else {
            $this->vars[$name] = & $value;
        }
    }

    /**
     * Рендер
     *
     * @return string
     */
    public function render() {
        if (!$this->path)
            return;
        ob_start();
        event('template.render.before', $this);
        extract($this->vars);
        include $this->path;
        event('template.render.after', $this);
        return ob_get_clean();
        ;
    }

    /**
     * Приведение к строке возвращяет результат рендеринга
     *
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

}
