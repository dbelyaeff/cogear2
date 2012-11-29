<?php

/**
 * Adapter class
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Observer extends Object implements SplObserver, SplSubject {
/**
    * An array of SplObserver objects
    * to notify of Exceptions.
    *
    * @var array
    */
    private $observers = array();


    /**
    * Attaches an SplObserver
    *
    * @param SplObserver        The observer to attach
    * @return void
    */
    public function attach(SplObserver $observer)
    {
        $id = spl_object_hash($observer);
        $this->observers[$id] = $observer;
    }

    /**
    * Detaches the SplObserver
    *
    * @param SplObserver        The observer to detach
    * @return void
    */
    public function detach(SplObserver $observer)
    {
        $id = spl_object_hash($observer);
        unset($this->observers[$id]);
    }

    /**
    * Notify all observers
    *
    * @return void
    */
    public function notify()
    {
        foreach($this->observers as $obs)
        {
            $obs->update($this);
        }
    }
    /**
     * Update
     *
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject){
        /**
         * Do whatever you need here
         */
    }

}
