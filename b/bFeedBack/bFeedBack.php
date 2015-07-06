<?php
defined('_BLIB') or die;

/**
 * Class bFeedBack - for get note from user
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bFeedBack extends bController{

    /** @var bUser $_user */
    protected $_user = null;
    /** @var bFeedBack__bDataMapper $_db */
    protected $_db = null;
    /** @var bFeedBack__view $_view */
    protected $_view = null;
    /** @var bRbac $_rbac */
    protected $_rbac = null;

    public function initialize($data = array()){

        $this->_user = $this->getInstance('user', 'bUser');
        $this->_db   = $this->getInstance('db', 'bFeedBack__bDataMapper');
        $this->_view = $this->getInstance('view', 'bFeedBack__view');
        $this->_rbac = $this->getInstance('rbac', 'bRbac');

        if(isset($data['mods'])){
            $this->_view->set('mods', $data['mods']);
        }
    }

    /**
     * Get base template for construct feedback window
     *
     * @return array|mixed|null
     */
    public function indexAction(){

        $_view = $this->_view;
        $_db   = $this->_db;
        $_user = $this->_user;

        $userId  = $_user->getId();
        $themes  = $_db->getThemeList();
        $threads = $_db->getThreadsByUser($userId);

        $_view->set('themes', $themes);
        $_view->set('threads', $threads);

		$answer = $_view->index();

		switch($this->getView()){

			case "json":
                $_view->set('data', $answer);
				return $_view->json();
				break;

			case "index":
			default:
				return $answer;
				break;
		}
	}


    /**
     * Set thread
     *
     * @return mixed|null|stdClass
     * @throws Exception
     */
    public function setThreadAction(){

        $_view = $this->_view;
        $_db   = $this->_db;
        $_user = $this->_user;

        $userId  = $_user->getId();
        $theme   = $this->get('theme', 0);
        $content = $this->get('content', '');

        $thread = $_db->getThread();

        $thread->user    = $userId;
        $thread->theme   = $theme;
        $thread->content = strip_tags($content);

        $_db->saveThread($thread);

        switch($this->getView()){
            case "json":
                $_view->set('data', $thread);
                return $_view->json();
                break;

            default:
                return $thread;
                break;
        }

	}


    /**
     * Set thread status
     *
     * @throws Exception
     */
    public function setThreadStatusAction(){

        $_db   = $this->_db;
        $_user = $this->_user;

        $userId = $_user->getId();
        $threadId = $this->get('thread', null);

        if($threadId === null) throw new Exception('Haven`t thread id.');

        $thread = $_db->getThread($threadId);

        if($thread->user != $userId)throw new Exception('Haven`t permission to change thread status.');

        $status = $this->get('status', null);

        $thread->status = $status;

        $_db->saveThread($thread);

    }


    /**
     * Set reply
     *
     * @return mixed|null|stdClass
     * @throws Exception
     */
    public function setReplyAction(){

        $_view = $this->_view;
        $_db   = $this->_db;
        $_user = $this->_user;
        $_rbac = $this->_rbac;

        $threadUser = null;
        $userId     = $_user->getId();
        $threadId   = $this->get('thread', null);
        $content    = $this->get('content', '');

        if($threadId == null)throw new Exception('Can`t set reply without thread.');

        $thread = $_db->getThread($threadId);
        $threadUser = $thread->user;

        if(
            $threadUser == $userId
            or $_rbac->checkAccess('bFeedBack_setReply')
        ) {


            $reply          = $_db->getReply();
            $reply->thread  = $threadId;
            $reply->content = strip_tags($content);
            $reply->user    = $userId;
            $_db->saveReply($reply);


            $thread->status = 0;
            $_db->saveThread($thread);

        }else{
            throw new Exception('Can`t set reply for feedback.');
        }

        switch($this->getView()){
            case "json":
                $_view->set('data', $reply);
                return $_view->json();
                break;

            default:
                return $reply;
                break;
        }
    }


    /**
     * Get replies by thread id
     *
     * @return array|mixed|null
     * @throws Exception
     */
    public function getRepliesAction(){

        $_view = $this->_view;
        $_db   = $this->_db;
        $_user = $this->_user;
        $_rbac = $this->_rbac;

        $threadUser = null;
        $userId     = $_user->getId();
        $threadId   = $this->get('thread', null);

        if($threadId == null)throw new Exception('Can`t get replies without thread.');

        $thread = $_db->getThread($threadId);
        $threadUser = $thread->user;

        if(
            $threadUser == $userId
            or $_rbac->checkAccess('bFeedBack_getReply')
        ) {
            $replies = $_db->getRepliesByThread($threadId);
        }else{
            throw new Exception('Can`t set reply for feedback.');
        }

        switch($this->getView()){
            case "json":
                $_view->set('data', $replies);
                return $_view->json();
                break;

            default:
                return $replies;
                break;
        }
    }
}