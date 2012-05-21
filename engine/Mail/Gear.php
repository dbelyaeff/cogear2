<?php

/**
 * Mail gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Mail_Gear extends Gear {

    protected $name = 'Mail';
    protected $description = 'Helps to send emails.';
    protected $hooks = array(
        'comment.published' => 'hookCommentPublished',
    );

    /**
     * Hook comment publishing
     *
     * @param object $Comment
     * @param object $Post
     * @param object $Parent
     * @param object $ParentAuthor
     */
    public function hookCommentPublished($Comment, $Post, $Parent = NULL, $ParentAuthor = NULL) {
        // If you post comment to your post
        if ($Post->aid != $Comment->aid) {
            $replace = array(
                '$user_link%' => $this->user->getLink(),
                '%user_name%' => $this->user->getName(),
                '%post_link%' => $Post->getLink(),
                '%post_name%' => $Post->name,
                '%comment%' => $Comment->body,
                '%reply_link%' => $Post->getLink() . '#comment-' . $Comment->id,
            );
            $mail = new Mail(array(
                        'name' => 'comment.post',
                        'subject' => t('New comment to your post', 'Mail.templates'),
                        'body' => str_replace(array_keys($replace), array_values($replace), t('User <a href="%user_link%">%user_name%</a> has published a comment to your post <a href="%post_link%">"%post_name%"</a>:
                            <p><i>%comment%</i></p>
                            <p><a href="%reply_link%">Reply &rarr;</a></p>'))
                    ));
            if ($PostAuthor = user($Post->aid)) {
                $mail->to($PostAuthor->email);
                $mail->send();
            }
        }
        /**
         * If you reply and not to yourself
         */
        if ($Parent && $Parent->aid != $this->user->id) {
            $replace = array(
                '$user_link%' => $this->user->getLink(),
                '%user_name%' => $this->user->getName(),
                '%post_link%' => $Post->getLink(),
                '%post_name%' => $Post->name,
                '%comment%' => $Comment->body,
                '%reply_link%' => $Post->getLink() . '#comment-' . $Comment->id,
            );
            $mail = new Mail(array(
                        'name' => 'comment.reply',
                        'subject' => t('Reply for your comment', 'Mail.templates'),
                        'body' => str_replace(array_keys($replace), array_values($replace), t('User <a href="%user_link%">%user_name%</a> has answered for you comment to post <a href="%post_link%">"%post_name%"</a>:
                            <p><i>%comment%</i></p>
                            <p><a href="%reply_link%">Reply &rarr;</a></p>', 'Mail.templates'))
                    ));
            $mail->to($ParentAuthor->email);
            $mail->send();
        }
        unset($mail);
    }

}