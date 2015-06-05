<?php
defined('_BLIB') or die;

/**
 * Class bFeedBack - for get note from user
 * Included patterns:
 * 		MVC	- use input property like action and view for create code logic
 */
class bFeedBack extends bController{

	protected $_traits  = array(/* 'bUser', */ 'bFeedBack__view', 'bFeedBack__bDataMapper');

    protected function configure($data = array()){
        if(isset($data['mods'])){
            /** @var bFeedBack__view $bFeedBack__view */
            $bFeedBack__view = $this->getInstance('bFeedBack__view');
            $bFeedBack__view->set('mods', $data['mods']);
        }
    }

    /**
     * Get base template for construct feedback window
     *
     * @return array|mixed|null
     */
    public function indexAction(){

		/** @var bUser $bUser */
		$bUser = $this->getInstance('bUser');

		/** @var bFeedBack__bDataMapper $bDataMapper */
		$bDataMapper = $this->getInstance('bFeedBack__bDataMapper');

		/** @var bFeedBack__view $bFeedBack__view */
		$bFeedBack__view = $this->getInstance('bFeedBack__view');

		$userId = $bUser->getId();

		$themes = $bDataMapper->getThemeList();
		$threads = $bDataMapper->getThreadsByUser($userId);

		$bFeedBack__view->set('themes', $themes);
		$bFeedBack__view->set('threads', $threads);

		$answer = $bFeedBack__view->index();

		switch($this->getView()){

			case "json":
				$bFeedBack__view->set('data', $answer);
				return $bFeedBack__view->json();
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

		/** @var bUser $bUser */
		$bUser = $this->getInstance('bUser');

		/** @var bFeedBack__bDataMapper $bDataMapper */
		$bDataMapper = $this->getInstance('bFeedBack__bDataMapper');

        /** @var bFeedBack__view $bFeedBack__view */
        $bFeedBack__view = $this->getInstance('bFeedBack__view');

		$userId = $bUser->getId();
		$theme = $this->get('theme', 0);
		$content = $this->get('content','');

		$thread = $bDataMapper->getThread();

		$thread->user = $userId;
		$thread->theme = $theme;
		$thread->content = strip_tags($content);

		$bDataMapper->saveThread($thread);

        switch($this->getView()){
            case "json":
                $bFeedBack__view->set('data', $thread);
                return $bFeedBack__view->json();
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

        /** @var bUser $bUser */
        $bUser = $this->getInstance('bUser');

        /** @var bFeedBack__bDataMapper $bDataMapper */
        $bDataMapper = $this->getInstance('bFeedBack__bDataMapper');

        $userId = $bUser->getId();
        $threadId = $this->get('thread', null);

        if($threadId === null) throw new Exception('Haven`t thread id.');

        $thread = $bDataMapper->getThread($threadId);

        if($thread->user != $userId)throw new Exception('Haven`t permission to change thread status.');

        $status = $this->get('status', null);

        $thread->status = $status;

        $bDataMapper->saveThread($thread);

    }


    /**
     * Set reply
     *
     * @return mixed|null|stdClass
     * @throws Exception
     */
    public function setReplyAction(){

        /** @var bUser $bUser */
        $bUser = $this->getInstance('bUser');

        /** @var bRbac $bRbac */
        $bRbac = $this->getInstance('bRbac');

        /** @var bFeedBack__bDataMapper $bDataMapper */
        $bDataMapper = $this->getInstance('bFeedBack__bDataMapper');

        /** @var bFeedBack__view $bFeedBack__view */
        $bFeedBack__view = $this->getInstance('bFeedBack__view');

        $threadUser = null;
        $userId = $bUser->getId();
        $threadId = $this->get('thread', null);
        $content = $this->get('content','');

        if($threadId == null)throw new Exception('Can`t set reply without thread.');

        $thread = $bDataMapper->getThread($threadId);
        $threadUser = $thread->user;

        if(
            $threadUser == $userId
            or $bRbac->checkAccess('bFeedBack_setReply')
        ) {


            $reply          = $bDataMapper->getReply();
            $reply->thread  = $threadId;
            $reply->content = strip_tags($content);
            $reply->user    = $userId;
            $bDataMapper->saveReply($reply);


            $thread->status = 0;
            $bDataMapper->saveThread($thread);

        }else{
            throw new Exception('Can`t set reply for feedback.');
        }

        switch($this->getView()){
            case "json":
                $bFeedBack__view->set('data', $reply);
                return $bFeedBack__view->json();
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

        /** @var bUser $bUser */
        $bUser = $this->getInstance('bUser');

        /** @var bRbac $bRbac */
        $bRbac = $this->getInstance('bRbac');

        /** @var bFeedBack__bDataMapper $bDataMapper */
        $bDataMapper = $this->getInstance('bFeedBack__bDataMapper');

        /** @var bFeedBack__view $bFeedBack__view */
        $bFeedBack__view = $this->getInstance('bFeedBack__view');

        $threadUser = null;
        $userId = $bUser->getId();
        $threadId = $this->get('thread', null);

        if($threadId == null)throw new Exception('Can`t get replies without thread.');

        $thread = $bDataMapper->getThread($threadId);
        $threadUser = $thread->user;

        if(
            $threadUser == $userId
            or $bRbac->checkAccess('bFeedBack_getReply')
        ) {
            $replies = $bDataMapper->getRepliesByThread($threadId);
        }else{
            throw new Exception('Can`t set reply for feedback.');
        }

        switch($this->getView()){
            case "json":
                $bFeedBack__view->set('data', $replies);
                return $bFeedBack__view->json();
                break;

            default:
                return $replies;
                break;
        }
    }
}