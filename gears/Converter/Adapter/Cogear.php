<?php

/**
 * Abstract converter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Converter_Adapter_Cogear extends Converter_Adapter_Abstract {

    /**
     * Convert users
     */
    public function users(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.users', 20);
        $import_users = new Db_Item('users', 'id', $db);
        $start = session('converter.users');
        if (!$start) {
            // If we start from the beginning all users except admin should be deleted
            $users = new User();
            $users->id = 1;
            $users->find();
            $users->truncate();
            $users->insert();
            $start = 0;
        }
        $import_users->order('id', 'asc');
        $import_users->limit($limit, $start);
        $output = array();
        if ($result = $import_users->findAll()) {
            foreach ($result as $import_user) {
                $user = new User();
                $user->id = $import_user->id;
                $action = $user->find() ? 'update' : 'insert';
                $user->login = $import_user->name;
                $user->email = $import_user->email;
                $user->password = $import_user->password;
                $user->object()->avatar = str_replace('/uploads', '', $import_user->avatar);
                $user->role = $import_user->user_group;
                $user->posts = $db->where('aid', $import_user->id)->count('nodes', 'id', TRUE);
                $user->comments = $db->where('aid', $import_user->id)->count('comments', 'id', TRUE);
                $user->reg_date = strtotime($import_user->reg_date);
                $user->last_visit = strtotime($import_user->last_visit);
                $user->votes = $import_user->charge;
                $user->rating = $import_user->points;
                $user->ip = $import_user->ip;
                if (FALSE != $user->$action()) {
                    $output[] = t('%d. User <b>%s</b> has been imported.', 'Converter', $user->id, $user->login);
                } else {
                    $output[] = t('%d. Failed to import user <b>%s</b>.', 'Converter', $import_user->id, $user->login);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
            $output[] = t('%d users has been imported', 'Converter', $start);
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.users', $start);
    }

    /**
     * Convert private messages
     */
    public function pm(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.pm', 10);
        $import_pm = new Db_Item('pm', 'id', $db);
        $start = session('converter.pm');
        if (NULL === $start) {
            // If we start from the beginning all chats must be deleted
            $chat = chat();
            $chat->truncate();
            $chat_msg = chat_msg();
            $chat_msg->truncate();
            $chat_view = new Chat_View();
            $chat_view->truncate();
            $start = 0;
        }
        $import_pm->order('id', 'asc');
        $import_pm->limit($limit, $start);
        $output = array();
        if ($result = $import_pm->findAll()) {
            foreach ($result as $pm) {
                if ($pm->system) {
                    continue;
                }
                $chat = chat();
                if ($import->owner == 'to') {
                    $chat->aid = $pm->from;
                    $chat->users = $pm->to;
                } else {
                    $chat->aid = trim(reset(explode(',', $pm->to)));
                    $chat->users = $pm->from;
                }
                $action = 'insert';
                if (preg_match('#Re\(\d+\):\s(.+)*#i', $pm->subject, $matches)) {
                    $chat->name = $matches[1];
                    if ($chat->find()) {
                        $action = 'update';
                        $result = TRUE;
                    }
                }
                if ($action == 'insert') {
                    $chat->name = $pm->subject;
                    $chat->created_date = strtotime($pm->created_date);
                    $chat->last_update = strtotime($pm->last_update);
                    $result = $chat->insert();
                }
//                debug($result);
//                debug($chat->last());
//                die();
                if ($result) {
                    $output[] = t('%d. Chat has been imported. <b>"%s"</b>', 'Converter', $start, $pm->subject);
                    $msg = chat_msg();
                    $msg->cid = $chat->id;
                    $msg->aid = $import->owner == 'to' ? $pm->to : $pm->from;
                    $msg->body = $pm->body;
                    $msg->created_date = $chat->created_date;
                    if ($msg->insert()) {
//                        $output[] = "&nbsp;&nbsp;".t('%d. Pm has been imported as chat msg.', 'Converter', $pm->id);
                    } else {
//                        $output[] = "&nbsp;&nbsp;".'<p class="alert alert-warning">' . t('%d. Failed to import pm.', 'Converter', $pm->id) ;
                    }
                    if ($pm->comments) {
                        $comments = new Db_Item('comments', 'id', $db);
                        $comments->join('comments_pm', array('comments_pm.cid' => 'comments.id', 'comments_pm.pid' => $pm->id));
                        $comments->order('path', 'asc');
                        if ($cresult = $comments->findAll()) {
                            foreach ($cresult as $comment) {
                                $msg = chat_msg();
                                $msg->cid = $chat->id;
                                $msg->aid = $comment->aid;
                                $msg->body = $comment->body;
                                $msg->ip = $comment->ip;
                                $msg->created_date = strtotime($comment->created_date);
                                $msg->insert();
                            }
                        }
                    }
                } else {
                    $output[] = t('%d. Failed to import chat. <b>"%s"</b>', 'Converter', $start, $pm->subject);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
        }
        $output = new Core_ArrayObject($output);
        $ajax->text = $output->toString('<br/>');
        session('converter.pm', $start);
    }

    /**
     * Convert blogs
     */
    public function blogs(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.blogs', 20);
        $import_blogs = new Db_Item('community', 'id', $db);
        $start = session('converter.blogs');
        if (NULL === $start) {
            // If we start from the beginning all users except admin should be deleted
            $blogs = new Blog();
            $blogs->truncate();
            $follower = new Blog_Followers();
            $follower->truncate();
            $start = 0;
            session('converter.userblogs', 0);
        }
        $import_blogs->order('id', 'asc');
        $import_blogs->limit($limit, $start);
        $output = array();
        if ($result = $import_blogs->findAll()) {
            foreach ($result as $import_blog) {
                $blog = new Blog();
                $blog->id = $import_blog->id;
                $action = $blog->find() ? 'update' : 'insert';
                $blog->name = $import_blog->name;
                $blog->login = $import_blog->url_name;
                $blog->body = $import_blog->descripiotn;
                $blog->object()->avatar = str_replace('/uploads', '', $import_blog->icon);
                $blog->type = $import_blog->private ? Blog::$types['private'] : Blog::$types['public'];
                $blog->created_date = strtotime($import_blog->created_date);
                $blog->rating = $import_blog->points;
                $import_users = new Db_Item('community_users', 'id', $db);
                $import_users->role = 'admin';
                $import_users->cid = $blog->id;
                if ($import_users->find()) {
                    $blog->aid = $import_users->uid;
                }
                if (FALSE != $blog->$action()) {
                    $import_users = new Db_Item('community_users', 'id', $db);
                    $import_users->cid = $blog->id;
                    if ($users = $import_users->findAll()) {
                        foreach ($users as $user) {
                            if ($user->role == 'admin') {
                                $blog->update(array('aid' => $user->uid));
                            }
                            $follower = new Blog_Followers();
                            $follower->uid = $user->uid;
                            $follower->bid = $user->cid;
                            switch ($user->role) {
                                case 'admin':
                                    $follower->role = 4;
                                    break;
                                case 'member':
                                default:
                                    if ($user->approved == 'true') {
                                        $follower->role = 2;
                                    } else {
                                        $follower->role = 1;
                                    }
                                    break;
                            }
                            $follower->created_date = strtotime($user->created_date);
                            $follower->insert();
                        }
                        $blog->update(array('followers' => $users->count()));
                    }
                    $output[] = t('%d. Blog <b>%s</b> has been imported.', 'Converter', $blog->id, $blog->name);
                } else {
                    $output[] = t('%d. Failed to import blog <b>%s</b>.', 'Converter', $import_blog->id, $blog->name);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $users = new User();
            $users->order('id', 'asc');
            $ustart = session('converter.userblogs');
            $users->limit($limit, $ustart);
            if ($result = $users->findAll()) {
                foreach ($result as $user) {
                    cogear()->blog->hookAutoRegUserBlog($user);
                    $output[] = t('%d. User blog <b>%s</b> has been imported.', 'Converter', $ustart, $user->login);
                    $ustart++;
                }
                $ajax->status = 'update';
                session('converter.userblogs', $ustart);
            } else {
                $ajax->status = 'finish';
                $output[] = t('%d blogs has been imported', 'Converter', $start + session('converter.users'));
            }
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.blogs', $start);
    }

    /**
     * Convert friends
     */
    public function friends(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.friends', 50);
        $import_friends = new Db_Item('buddies', 'id', $db);
        $start = session('converter.friends');
        if (NULL === $start) {
            // If we start from the beginning all users except admin should be deleted
//            $users = new ();
//            $users->where('id', '1', '!=');
//            $users->delete();
            $friends = new Friends_Object();
            $friends->truncate();
            $start = 0;
        }
        $import_friends->order('id', 'asc');
        $import_friends->limit($limit, $start);
        $output = array();
        if ($result = $import_friends->findAll()) {
            foreach ($result as $ifriend) {
                $friend = new Friends_Object();
                $friend->u1 = $ifriend->from;
                $friend->u2 = $ifriend->to;
                $friend->created_date = strtotime($ifriend->created_date);
                $friend->insert();
                if ($ifriend->approved) {
                    $friend = new Friends_Object();
                    $friend->u2 = $ifriend->from;
                    $friend->u1 = $ifriend->to;
                    $friend->created_date = strtotime($ifriend->created_date);
                    $friend->insert();
                }
                $output[] = t('%d. Friendship has been imported.', 'Converter', $start);
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
            $output[] = t('%d friendships has been imported', 'Converter', $start);
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.friends', $start);
    }

    /**
     * Convert posts
     */
    public function posts(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.posts', 20);
        $nodes = new Db_Item('nodes', 'id', $db);
        $start = session('converter.posts');
        if (NULL === $start) {
            // If we start from the beginning all users except admin should be deleted
            $posts = post();
            $posts->truncate();
            cogear()->db->truncate('tags');
            cogear()->db->truncate('tags_links');
            $start = 0;
        }
        $nodes->order('id', 'asc');
        $nodes->limit($limit, $start);
        $output = array();
        if ($result = $nodes->findAll()) {
            foreach ($result as $node) {
                $post = post();
                $post->id = $node->id;
                $post->aid = $node->aid;
                if ($node->cid) {
                    $post->bid = $node->cid;
                } else {
                    $blog = blog();
                    $blog->aid = $node->aid;
                    $blog->type = 0;
                    $blog->find();
                    $node->bid = $blog->id;
                }
                $post->name = $node->name;
                $post->body = $node->body;
                $post->allow_comments = 1;
                if ($post->tags = $node->tags) {
                    $tags = preg_split('#([,]+)#', $post->tags, -1, PREG_SPLIT_NO_EMPTY);
                    foreach ($tags as $name) {
                        $tag = tag();
                        $tag->name = trim($name);
                        if (!$tag->find()) {
                            $tag->insert();
                        }
                        $link = new Tags_Link();
                        $link->tid = $tag->id;
                        $link->pid = $post->id;
                        $link->insert();
                    }
                }
                $post->views = $node->views;
                $post->rating = $node->points;
                $post->keywords = $node->keywords;
                $post->description = $node->description;
                $post->created_date = strtotime($node->created_date);
                $post->last_update = strtotime($node->last_update);
                $post->published = $node->published == 'true' ? 1 : 0;
                $post->front = $node->promoted == 'true' ? 1 : 0;
                $post->front_time = strtotime($node->promoted_date);
                if (FALSE != $post->insert()) {
                    $output[] = t('%d. Post <b>%s</b> has been imported.', 'Converter', $node->id, $node->name);
                } else {
                    $output[] = t('%d. Failed to import user <b>%s</b>.', 'Converter', $node->id, $node->name);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
            $output[] = t('%d posts has been imported', 'Converter', $start);
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.posts', $start);
    }

    /**
     * Convert fave
     */
    public function fave(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.fave', 100);
        $faves = new Db_Item('favorites', 'id', $db);
        $start = session('converter.fave');
        if (NULL === $start) {
            // If we start from the beginning all users except admin should be deleted
            $fave = new Fave_Object();
            $fave->truncate();
            $start = 0;
        }
        $faves->order('id', 'asc');
        $faves->limit($limit, $start);
        $output = array();
        if ($result = $faves->findAll()) {
            foreach ($result as $ifave) {
                $fave = new Fave_Object();
                $fave->pid = $ifave->nid;
                $fave->uid = $ifave->uid;
                $fave->created_date = strtotime($ifave->created_date);
                if (FALSE != $fave->insert()) {
                    $output[] = t('%d. Favorite item has been imported.', 'Converter', $ifave->id);
                } else {
                    $output[] = t('%d. Failed to import favorite item.', 'Converter', $ifave->id);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
            $output[] = t('%d favorite items has been imported', 'Converter', $start);
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.fave', $start);
    }

    /**
     * Convert comments
     */
    public function comments(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.comments', 100);
        $import_comments = new Db_Item('comments', 'id', $db);
        $import_comments->select('comments.*,comments_nodes.nid as nid')->join('comments_nodes', array('comments_nodes.cid' => 'comments.id'), 'inner');
        $start = session('converter.comments');
        if (!$start) {
            // If we start from the beginning all users except admin should be deleted
            $comments = comments();
            $comments->truncate();
            $start = 0;
        }
        $import_comments->order('comments.id', 'asc');
        $import_comments->limit($limit, $start);
        $output = array();
        if ($result = $import_comments->findAll()) {
            foreach ($result as $import_comment) {
                $comment = new Comments();
                $comment->id = $import_comment->id;
                $comment->post_id = $import_comment->nid;
                $comment->aid = $import_comment->aid;
                $pieces = explode('.', trim($import_comment->path));
                if (sizeof($pieces) == 1) {
                    $comment->pid = $pieces[0];
                } else {
                    array_pop($pieces);
                    $pid = end($pieces);
                    $comment->pid = $pid == $comment->id ? 0 : $pid;
                }
                $comment->body = $import_comment->body;
                $comment->published = 1;
                $comment->frozen = 0;
                $comment->rating = $import_comment->points;
                $comment->ip = $import_comment->ip;
                $comment->created_date = strtotime($import_comment->created_date);

                if (FALSE != $comment->insert()) {
                    $output[] = t('%d. Comment has been imported. ', 'Converter', $start);
                } else {
                    $output[] = t('%d. Failed to import comment.', 'Converter', $start);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
            $output[] = t('%d comments has been imported', 'Converter', $start);
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.comments', $start);
    }

    /**
     * Convert pages
     */
    public function pages(Ajax $ajax) {
        $db = new Db($this->options);
        $limit = config('converter.limit.pages', 20);
        $ipages = new Db_Item('pages', 'id', $db);
        $start = session('converter.pages');
        if (!$start) {
            // If we start from the beginning all users except admin should be deleted
            $pages = new Pages_Object();
            $pages->truncate();
            $start = 0;
        }
        $ipages->order('id', 'asc');
        $ipages->limit($limit, $start);
        $output = array();
        if ($result = $ipages->findAll()) {
            foreach ($result as $ipage) {
                $page = new Pages_Object();
                $page->aid = $ipage->aid;
                $page->name = $ipage->name;
                $page->link = $ipage->url_name;
                $page->body = $ipage->body;
                $page->created_date = strtotime($ipage->created_date);
                $page->last_update = strtotime($ipage->last_update);
                if (FALSE != $page->insert()) {
                    $output[] = t('%d. Page <b>%s</b> has been imported.', 'Converter', $start, $page->name);
                } else {
                    $output[] = t('%d. Failed to import page <b>%s</b>.', 'Converter', $start, $page->name);
                }
                $start++;
            }
            $ajax->status = 'update';
        } else {
            $ajax->status = 'finish';
            $output[] = t('%d pages has been imported', 'Converter', $start);
        }
        $ajax->text = implode('<br/>', $output);
        session('converter.pages', $start);
    }

    /**
     * Reset step
     */
    public function reset(Ajax $ajax) {
        $step = $this->input->get('step');
        $this->session->remove('converter.' . $step);
    }

    /**
     * Clear
     */
    public function clear() {
        parent::clear();
        $this->session->remove('converter.users');
        $this->session->remove('converter.blogs');
        $this->session->remove('converter.posts');
        $this->session->remove('converter.friends');
        $this->session->remove('converter.comments');
        $this->session->remove('converter.pm');
        $this->session->remove('converter.pages');
        $this->session->remove('converter.fave');
    }

}