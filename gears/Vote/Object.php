<?php

/**
 * Vote object
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Vote_Object extends Db_ORM {

    protected $table = 'votes';
    protected $data;
    protected $type;
    protected $points;
    protected $rating = 0;
    public static $types = array(
        'user' => 0,
        'post' => 1,
        'comment' => 2,
        'blog'=> 3,
    );
    protected $error = '';

    /**
     * Конструктор
     *
     * @param type $Object
     */
    public function __construct(Object $Object = NULL, $type = NULL) {
        if(NULL != $Object){
            $this->data = $Object;
            $this->type($type);
        }
        parent::__construct();
    }

    /**
     * Vote
     *
     * @param type $direction
     * @return  mixed
     */
    public function vote($direction = 'up') {
        $rating = $this->data->rating;
        $this->object()->type = self::$types[$this->type];
        $this->object()->tid = $this->data->id;
        $this->object()->uid = user()->id;
        if (!$this->check()) {
            $this->error = t('You can\'t vote for yourself!', 'Vote');
            return FALSE;
        }
        if ($this->find()) {
            $this->error = t('You can\'t vote twice for the same item!', 'Vote');
            return FALSE;
        }
        if (user()->votes < 1) {
            $this->error = t('You don\'t have any votes!', 'Vote');
            return FALSE;
        }
        $this->object()->created_date = time();
        $this->object()->points = $this->calcPoints();
        if ($direction == 'down') {
            $this->object()->points = -$this->object()->points;
        }
        $this->rating = $rating + $this->object()->points;
        if ($this->insert() && $this->data->update(array('rating' => $this->rating))) {
            $this->transferVotes();
            cogear()->vote->clear();
            event('vote.action',$this);
            return TRUE;
        } else {
            $this->error = t('You can\'t vote twice for the same item!', 'Vote');
            return FALSE;
        }
    }

    /**
     * Check if user can vote
     *
     * @return type
     */
    public function check() {
        if ($this->type == 'user' && $this->data->id == user()->id
                OR $this->type != 'user' && $this->data->aid == user()->id) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Get vote ratings
     *
     * @return float
     */
    public function rating() {
        return $this->rating;
    }

    /**
     * Calculate points for current user
     *
     * @return float
     */
    protected function calcPoints() {
        return $this->points = 1 + round(user()->rating / 100, 1, PHP_ROUND_HALF_DOWN);
    }

    /**
     * At the moment of voting transfter vote from one user to another
     */
    protected function transferVotes() {
        user()->update(array('votes' => user()->votes - 1));
        if ($this->type == 'user') {
            $this->data->update(array('votes' => $this->data->votes + 1));
        }
        // if post or comment
        elseif ($user = user($this->data->aid)) {
            $user->update(array('votes' => $user->votes - 1));
        }
    }

    /**
     * Get error
     */
    public function error() {
        return $this->error;
    }

    /**
     * Set or get vote type
     *
     * @param   string  $type
     * @return  mixed   $type
     */
    public function type($type = NULL) {
        if ($type) {
            return $this->type = $type;
        } elseif (!$this->type) {
            switch ($this->data->reflection->getName()) {
                case 'Post':
                case 'Post_Object':
                    $this->type = 'post';
                    break;
                case 'Comments':
                case 'Comments_Object':
                    $this->type = 'comment';
                    break;
                case 'User':
                case 'User_Object':
                    $this->type = 'user';
                    break;
                case 'Blog':
                case 'Blog_Object':
                    $this->type = 'blog';
                    break;
            }
        }
        return $this->type;
    }

    /**
     * Render vote
     *
     * @return type
     */
    public function render() {
        $object = $this->data;

        $link = l('/vote/status/' . $this->type . '/');
        $class = 'sh ajax';
        $up = '';
        $down = '';
        if($votes = cogear()->session->get('votes')){
            if(isset($votes[self::$types[$this->type]]) && $type = $votes[self::$types[$this->type]]){
                if(isset($type[$this->data->id])){
                    if($type[$this->data->id]){
                        $up = ' active disabled';
                        $down = ' disabled';
                    }
                    else {
                        $up = ' disabled';
                        $down = ' active disabled';
                    }
                }
            }
        }
        if (access('Vote.status') && $this->check()) {
            $vote_up = '<a href="' . $link . '/' . $object->id . '/up/" class="vote-up '.$class.$up.'"><i class="icon-arrow-up"></i></a>';
        } else {
            $vote_up = '';
        }
        $vote_count = '<span title="'.t('Rating','Vote').'" class="vote-counter '.($object->rating ? $object->rating > 0 ? 'positive' : 'negative' : '').'">' . $object->rating . '</span>';
        if (access('Vote.status') && $this->check()) {
            $vote_down = '<a href="' . $link . '/' . $object->id . '/down/" class="vote-down '.$class.$down.'"><i class="icon-arrow-down"></i></a>';
        } else {
            $vote_down = '';
        }
        return '<span class="vote vote-' . $this->type . '" id="vote-' . $this->type . '-' . $this->data->id . '">' . $vote_up . ' ' . $vote_count . ' ' . $vote_down . '</span>';
    }

}