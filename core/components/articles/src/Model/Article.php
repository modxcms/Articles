<?php

namespace Articles\Model;

use Articles\Model\Notification\ArticlesNotification;
use Articles\Model\Update\ArticlesPingomatic;
use Articles\Model\Update\ArticlesUpdateService;
use MODX\Revolution\modContentType;
use MODX\Revolution\modResource;
use MODX\Revolution\modResourceGroupResource;
use MODX\Revolution\modTemplateVarResource;
use MODX\Revolution\modX;
use quipThread;
use xPDO\xPDO;

/**
 * @package articles
 */
class Article extends modResource {
    public $allowListingInClassKeyDropdown = false;
    public $showInContextMenu = false;

    function __construct(xPDO &$xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key',self::class);
        $this->set('show_in_tree',false);
        $this->set('richtext',true);
        $this->set('searchable',true);
    }
    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('articles.core_path',null,$modx->getOption('core_path').'components/articles/').'controllers/article/';
    }

    public function getContent(array $options = []) {
        if ($this->xpdo instanceof modX) {
            $settings = $this->getContainerSettings();
            if ($this->xpdo->getOption('commentsEnabled',$settings,true)) {
                $this->getCommentsCall($settings);
                $this->getCommentsReplyCall($settings);
                $this->getCommentsCountCall($settings);
                $this->xpdo->setPlaceholder('comments_enabled',1);
            } else {
                $this->xpdo->setPlaceholder('comments_enabled',0);
            }
            $this->getTagsCall($settings);
            /** @var ArticlesContainer $container */
            $container = $this->getOne('Container');
            if ($container instanceof ArticlesContainer) {
                $container->getArchivistCall();
                $container->getLatestCommentsCall();
                $container->getLatestPostsCall();
                $container->getTagListerCall();
            }
        }
        $content = parent::getContent($options);
        return $content;
    }

    /**
     * @return array
     */
    public function getContainerSettings() {
        $settings = $this->getProperties('articles');
        /** @var ArticlesContainer $container */
        $container = $this->getOne('Container');
		if(method_exists($container, 'getContainerSettings')) {
			$settings = $container->getContainerSettings();
		}
        return is_array($settings) ? $settings : [];
    }

    /**
     * Prevent isLazy error since Articles types have extra DB fields
     * @param string $key
     * @return bool
     */
    public function isLazy($key = '') {
        return false;
    }

    /**
     * @param array $settings
     * @return string
     */
    public function getCommentsCall(array $settings = []) {
        $call = '[[!Quip?
   &thread=`article-b'.$this->get('parent').'-'.$this->get('id').'`
   &threaded=`'.$this->xpdo->getOption('commentsThreaded',$settings,1).'`
   &replyResourceId=`'.$this->xpdo->getOption('commentsReplyResourceId',$settings,0).'`
   &maxDepth=`'.$this->xpdo->getOption('commentsMaxDepth',$settings,5).'`

   &tplComment=`'.$this->xpdo->getOption('commentsTplComment',$settings,'quipComment').'`
   &tplCommentOptions=`'.$this->xpdo->getOption('commentsTplCommentOptions',$settings,'quipCommentOptions').'`
   &tplComments=`'.$this->xpdo->getOption('commentsTplComments',$settings,'quipComments').'`

   &dateFormat=`'.$this->xpdo->getOption('commentsDateFormat',$settings,'%b %d, %Y at %I:%M %p').'`
   &closeAfter=`'.$this->xpdo->getOption('commentsCloseAfter',$settings,0).'`

   &useCss=`'.$this->xpdo->getOption('commentsUseCss',$settings,1).'`
   &altRowCss=`'.$this->xpdo->getOption('commentsAltRowCss',$settings,'quip-comment-alt').'`

   &requireAuth=`'.$this->xpdo->getOption('commentsRequireAuth',$settings,0).'`

   &nameField=`'.$this->xpdo->getOption('commentsNameField',$settings,'username').'`
   &showAnonymousName=`'.$this->xpdo->getOption('commentsShowAnonymousName',$settings,0).'`
   &anonymousName=`'.$this->xpdo->getOption('commentsAnonymousName',$settings,'Anonymous').'`

   &allowRemove=`'.$this->xpdo->getOption('commentsAllowRemove',$settings,1).'`
   &removeThreshold=`'.$this->xpdo->getOption('commentsRemoveThreshold',$settings,3).'`
   &allowReportAsSpam=`'.$this->xpdo->getOption('commentsAllowReportAsSpam',$settings,1).'`

   &useGravatar=`'.$this->xpdo->getOption('commentsGravatar',$settings,1).'`
   &gravatarIcon=`'.$this->xpdo->getOption('commentsGravatarIcon',$settings,'identicon').'`
   &gravatarSize=`'.$this->xpdo->getOption('commentsGravatarSize',$settings,50).'`

   &limit=`'.$this->xpdo->getOption('commentsLimit',$settings,0).'`
   &sortDir=`'.$this->xpdo->getOption('commentsSortDir',$settings,0).'`
]]';
        $this->xpdo->setPlaceholder('comments',$call);
        return $call;
    }

    /**
     * @param array $settings
     * @return string
     */
    public function getCommentsReplyCall(array $settings = []) {
        $requireAuth = $this->xpdo->getOption('commentsRequireAuth',$settings,0);
        if ($requireAuth) $requireAuth = '&requireAuth=`1`';
        $call = '[[!QuipReply?
   &thread=`article-b'.$this->get('parent').'-'.$this->get('id').'`

   &tplAddComment=`'.$this->xpdo->getOption('commentsTplAddComment',$settings,'quipAddComment').'`
   &tplLoginToComment=`'.$this->xpdo->getOption('commentsTplLoginToComment',$settings,'quipLoginToComment').'`
   &tplPreview=`'.$this->xpdo->getOption('commentsTplPreview',$settings,'quipPreviewComment').'`

   &requirePreview=`'.$this->xpdo->getOption('commentsRequirePreview',$settings,0).'`
   '.(!empty($requireAuth) ? $requireAuth : '').'

   &recaptcha=`'.$this->xpdo->getOption('commentsReCaptcha',$settings,0).'`
   &disableRecaptchaWhenLoggedIn=`'.$this->xpdo->getOption('commentsDisabledReCaptchaWhenLoggedIn',$settings,1).'`

   &moderate=`'.$this->xpdo->getOption('commentsModerate',$settings,1).'`
   &moderateAnonymousOnly=`'.$this->xpdo->getOption('commentsModerateAnonymousOnly',$settings,0).'`
   &moderateFirstPostOnly=`'.$this->xpdo->getOption('commentsModerateFirstPostOnly',$settings,1).'`
   &moderators=`'.$this->xpdo->getOption('commentsModerators',$settings,'').'`
   &moderatorGroup=`'.$this->xpdo->getOption('commentsModeratorGroup',$settings,'Administrator').'`

   &closeAfter=`'.$this->xpdo->getOption('commentsCloseAfter',$settings,0).'`
   &dateFormat=`'.$this->xpdo->getOption('commentsDateFormat',$settings,'%b %d, %Y at %I:%M %p').'`
   &autoConvertLinks=`'.$this->xpdo->getOption('commentsAutoConvertLinks',$settings,1).'`

   &useGravatar=`'.$this->xpdo->getOption('commentsGravatar',$settings,1).'`
   &gravatarIcon=`'.$this->xpdo->getOption('commentsGravatarIcon',$settings,'identicon').'`
   &gravatarSize=`'.$this->xpdo->getOption('commentsGravatarSize',$settings,50).'`
]]';
        $this->xpdo->setPlaceholder('comments_form',$call);
        return $call;
    }

    /**
     * @param array $settings
     * @return string
     */
    public function getCommentsCountCall(array $settings = []) {
        $call = '[[!QuipCount? &thread=`article-b'.$this->get('parent').'-'.$this->get('id').'`]]';
        $this->xpdo->setPlaceholder('comments_count',$call);
        return $call;
    }

    /**
     * @param array $settings
     * @return string
     */
    public function getTagsCall(array $settings = []) {
        $call = '[[!tolinks? &useTagsFurl=`[[++friendly_urls]]` &items=`[[*articlestags]]` &target=`'.$this->get('parent').'`]]';
        $this->xpdo->setPlaceholder('article_tags',$call);
        return $call;
    }

    /**
     * @return boolean
     */
    public function sendNotifications() {
        $success = false;
        $settings = $this->getContainerSettings();
        if (empty($settings['notificationServices'])) return $success;
        $services = explode(',',$settings['notificationServices']);
        $modelPath = $this->xpdo->getOption('articles.core_path',null,$this->xpdo->getOption('core_path').'components/articles/').'src/Model/';

        /* get context to prepare url mapping */
        $this->xpdo->getContext($this->get('context_key'));
        $url = $this->xpdo->makeUrl($this->get('id'),$this->get('context_key'),'','full');

        foreach ($services as $service) {
            $className = ArticlesNotification::class.ucfirst(strtolower($service));
            $classPath = $modelPath.'notification/'.strtolower($className).'.class.php';
            if (file_exists($classPath)) {
                require_once $classPath;
                /** @var ArticlesNotification $notifier */
                $notifier = new $className($this);
                $success = $notifier->send($this->get('pagetitle'),$url);
            }
        }
        return $success;
    }

    /**
     * Send any notification pings to notification services, such as Ping-O-Matic
     * @return boolean
     */
    public function notifyUpdateServices() {
        $success = false;
        $settings = $this->getContainerSettings();
        if (!$this->getOption('updateServicesEnabled',$settings,true)) return $success;

        $service = $this->getUpdateService();
        if ($service) {
            /** @var ArticlesUpdateService $service */
            $url = $this->xpdo->makeUrl($this->get('id'),$this->get('context_key'),'','full');
            $success = $service->notify($this->get('pagetitle'),$url);
        }
        return $success;
    }

    /**
     * Get the notification service
     * @return ArticlesPingomatic
     */
    protected function getUpdateService() {
        $settings = $this->getContainerSettings();
        $modelPath = $this->xpdo->getOption('articles.core_path',null,$this->xpdo->getOption('core_path').'components/articles/').'src/Model/';
        $updateServiceClass = $this->getOption('updateServiceClass',$settings,ArticlesPingomatic::class);
        $updateServicePath = $this->getOption('updateServicePath',$settings,$modelPath.'Update/ArticlesPingomatic.php');
        $included = include_once $updateServicePath;
        $service = false;
        if ($included) {
            $service = new $updateServiceClass($this);
        }
        return $service;
    }

    public function setArchiveUri() {
        /** @var ArticlesContainer $container */
        $container = $this->xpdo->getObject(ArticlesContainer::class, ['id' => $this->get('parent')]);
        if (!$container) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[Articles] Could not find Container to set Article URI from.');
            return false;
        }

	    if(method_exists($container, 'getContainerSettings')) {
		    $settings = $container->getContainerSettings();
	    }

        if($this->get('pub_date')) $date = $this->get('pub_date');
        else $date = $this->get('published') ? $this->get('publishedon') : $this->get('createdon');
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $day = date('d',strtotime($date));

        $containerUri = $container->get('uri');
        if (empty($containerUri)) {
            $containerUri = $container->get('alias');
        }

        $furlTemplate = $this->xpdo->getOption('articleUriTemplate',$settings,'%Y/%m/%d/%alias/');
        $furlTemplate = str_replace('%Y', $year, $furlTemplate);
        $furlTemplate = str_replace('%m', $month, $furlTemplate);
        $furlTemplate = str_replace('%d', $day, $furlTemplate);
        $furlTemplate = str_replace('%alias', $this->get('alias'), $furlTemplate);

        /** @var modContentType $contentType */
        $contentType = $this->xpdo->getObject(modContentType::class, '');
        if ($contentType) {
            $extension = ltrim($contentType->getExtension(), '.');
            $furlTemplate = str_replace('%ext', $extension, $furlTemplate);
        }

        $furlTemplate = str_replace('%id', $this->get('id'), $furlTemplate);

        $uri = rtrim($containerUri,$this->xpdo->getOption('container_suffix', null, '/')) .'/'. rtrim($furlTemplate);

        $this->set('uri',$uri);
        $this->set('uri_override',true);
        return $this->get('uri');
    }

    /**
     * Override remove to remove the associated Quip thread
     *
     * {@inheritDoc}
     *
     * @param array $ancestors
     * @return boolean
     */
    public function remove(array $ancestors = []) {
        $removed = parent::remove($ancestors);

        if ($removed) {
            $quipPath = $this->xpdo->getOption('quip.core_path',null,$this->xpdo->getOption('core_path').'components/quip/');
            $this->xpdo->addPackage('quip',$quipPath.'model/');
            /** @var quipThread $thread */
            $thread = $this->xpdo->getObject(quipThread::class, [
                'name' => 'article-b'.$this->get('parent').'-'.$this->get('id'),
            ]);
            if ($thread) {
                $thread->remove();
            }
        }

        return $removed;
    }

    /**
     * Duplicate the Article
     *
     * @param array $options An array of options.
     * @return mixed Returns either an error message, or the newly created modResource object.
     */
    public function duplicate(array $options = []) {
        if (!($this->xpdo instanceof modX)) return false;

        /* duplicate resource */
        $prefixDuplicate = !empty($options['prefixDuplicate']) ? true : false;
        $newName = !empty($options['newName']) ? $options['newName'] : $this->get('pagetitle');
        /** @var Article $newResource */
        $newResource = $this->xpdo->newObject(Article::class);
        $newResource->fromArray($this->toArray('', true), '', false, true);
        $newResource->set('pagetitle', $newName);

        /* do published status preserving */
        $publishedMode = $this->getOption('publishedMode',$options,'preserve');
        switch ($publishedMode) {
            case 'unpublish':
                $newResource->set('published',false);
                $newResource->set('publishedon',0);
                $newResource->set('publishedby',0);
                break;
            case 'publish':
                $newResource->set('published',true);
                $newResource->set('publishedon',time());
                $newResource->set('publishedby',$this->xpdo->user->get('id'));
                break;
            case 'preserve':
            default:
                $newResource->set('published',$this->get('published'));
                $newResource->set('publishedon',$this->get('publishedon'));
                $newResource->set('publishedby',$this->get('publishedby'));
                break;
        }

        /* allow overrides for every item */
        if (!empty($options['overrides']) && is_array($options['overrides'])) {
            $newResource->fromArray($options['overrides']);
        }
        $newResource->set('id',0);

        /* make sure children get assigned to new parent */
        $newResource->set('parent',isset($options['parent']) ? $options['parent'] : $this->get('parent'));
        $newResource->set('createdby',$this->xpdo->user->get('id'));
        $newResource->set('createdon',time());
        $newResource->set('editedby',0);
        $newResource->set('editedon',0);

        /* get new alias */
        $alias = $newResource->cleanAlias($newName);
        if ($newResource->get('published')) {
            $newResource->setArchiveUri();
        } else {
            $newResource->set('uri','');
            $newResource->set('uri_override',false);
            $aliasPath = $newResource->getAliasPath($newName);
            $newResource->set('uri',$aliasPath);
            $newResource->set('uri_override',true);
            $newResource->set('alias',$alias);
        }

        /* set new menuindex */
        $childrenCount = $this->xpdo->getCount(modResource::class, ['parent' => $this->get('parent')]);
        $newResource->set('menuindex',$childrenCount);

        /* save resource */
        if (!$newResource->save()) {
            return $this->xpdo->lexicon('resource_err_duplicate');
        }

        $tvds = $this->getMany('TemplateVarResources');
        /** @var modTemplateVarResource $oldTemplateVarResource */
        foreach ($tvds as $oldTemplateVarResource) {
            /** @var modTemplateVarResource $newTemplateVarResource */
            $newTemplateVarResource = $this->xpdo->newObject(modTemplateVarResource::class);
            $newTemplateVarResource->set('contentid',$newResource->get('id'));
            $newTemplateVarResource->set('tmplvarid',$oldTemplateVarResource->get('tmplvarid'));
            $newTemplateVarResource->set('value',$oldTemplateVarResource->get('value'));
            $newTemplateVarResource->save();
        }

        $groups = $this->getMany('ResourceGroupResources');
        /** @var modResourceGroupResource $oldResourceGroupResource */
        foreach ($groups as $oldResourceGroupResource) {
            /** @var modResourceGroupResource $newResourceGroupResource */
            $newResourceGroupResource = $this->xpdo->newObject(modResourceGroupResource::class);
            $newResourceGroupResource->set('document_group',$oldResourceGroupResource->get('document_group'));
            $newResourceGroupResource->set('document',$newResource->get('id'));
            $newResourceGroupResource->save();
        }

        /* duplicate resource, recursively */
        $duplicateChildren = isset($options['duplicateChildren']) ? $options['duplicateChildren'] : true;
        if ($duplicateChildren) {
            if (!$this->checkPolicy('add_children')) return $newResource;

            $children = $this->getMany('Children');
            if (is_array($children) && count($children) > 0) {
                /** @var modResource $child */
                foreach ($children as $child) {
                    $child->duplicate([
                        'duplicateChildren' => true,
                        'parent' => $options['parent'],
                        'prefixDuplicate' => $prefixDuplicate,
                        'overrides' => !empty($options['overrides']) ? $options['overrides'] : false,
                        'publishedMode' => $publishedMode,
                    ]);
                }
            }
        }
        return $newResource;
    }
}
