<?php
/**
 * Articles
 *
 * Copyright 2011-12 by Shaun McCormick <shaun+articles@modx.com>
 *
 * Articles is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Articles is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Articles; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package articles
 */

use Articles\Model\Article;
use MODX\Revolution\modTemplateVar;

/**
 * @package articles
 */
class ArticleUpdateManagerController extends ResourceUpdateManagerController {
    /** @var Article $resource */
    public $resource;
    /** @var boolean $commentsEnabled */
    public $commentsEnabled = false;
    public function loadCustomCssJs() {
        if ($this->modx->getOption('commentsEnabled',$settings,false)) {
            $quipCorePath = $this->modx->getOption('quip.core_path',null,$this->modx->getOption('core_path',null,MODX_CORE_PATH).'components/quip/');
            if ($this->modx->addPackage('quip',$quipCorePath.'model/')) {
                $this->commentsEnabled = true;
            }
        }
        $managerUrl = $this->context->getOption('manager_url', MODX_MANAGER_URL, $this->modx->_userConfig);
        $articlesAssetsUrl = $this->modx->getOption('articles.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/articles/');
        $quipAssetsUrl = $this->modx->getOption('quip.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/quip/');
        $connectorUrl = $articlesAssetsUrl.'connector.php';
        $articlesJsUrl = $articlesAssetsUrl.'js/';
        $this->addJavascript($managerUrl.'assets/modext/util/datetime.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
        $this->addJavascript($managerUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
        $this->addJavascript($managerUrl.'assets/modext/sections/resource/update.js');
        $this->addJavascript($articlesJsUrl.'articles.js');
        $this->addJavascript($articlesJsUrl.'extras/combo.js');
        $this->addJavascript($articlesJsUrl.'extras/tagfield.js');

        if($this->commentsEnabled) {
            $this->addCss($quipAssetsUrl.'css/mgr.css');
            $this->addJavascript($quipAssetsUrl.'js/quip.js');
            $this->addJavascript($quipAssetsUrl.'js/widgets/comments.grid.js');
            $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                Quip.config = '.$this->modx->toJSON([]).';
                Quip.config.connector_url = "'.$quipAssetsUrl.'connector.php";
                Quip.request = '.$this->modx->toJSON($_GET).';
            });
            </script>');
        }
        $this->addLastJavascript($articlesJsUrl.'article/update.js');
        $this->addHtml('
        <script type="text/javascript">
        // <![CDATA[
        Articles.assets_url = "'.$articlesAssetsUrl.'";
        Articles.connector_url = "'.$connectorUrl.'";
        MODx.config.publish_document = "'.$this->canPublish.'";
        MODx.onDocFormRender = "'.$this->onDocFormRender.'";
        MODx.ctx = "'.$this->resource->get('context_key').'";
        Ext.onReady(function() {
            MODx.load({
                xtype: "articles-page-article-update"
                ,resource: "'.$this->resource->get('id').'"
                ,record: '.$this->modx->toJSON($this->resourceArray).'
                ,publish_document: "'.$this->canPublish.'"
                ,preview_url: "'.$this->previewUrl.'"
                ,locked: '.($this->locked ? 1 : 0).'
                ,lockedText: "'.$this->lockedText.'"
                ,canSave: '.($this->canSave ? 1 : 0).'
                ,canEdit: '.($this->canEdit ? 1 : 0).'
                ,canCreate: '.($this->canCreate ? 1 : 0).'
                ,canDuplicate: '.($this->canDuplicate ? 1 : 0).'
                ,canDelete: '.($this->canDelete ? 1 : 0).'
                ,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
                ,mode: "update"
            });
        });
        // ]]>
        </script>');
        /* load RTE */
        $this->loadRichTextEditor();
    }
    public function getLanguageTopics() {
        return ['resource','articles:default','quip:default'];
    }


    public function process(array $scriptProperties = []) {
        $placeholders = parent::process($scriptProperties);
        $this->getTagsTV();

        $settings = $this->resource->getContainerSettings();
        $this->resourceArray['commentsEnabled'] = $this->commentsEnabled;
        //$this->resourceArray['richtext'] = $this->modx->getOption('articlesRichtext',$settings,1);

        return $placeholders;
    }

    public function getTagsTV() {
        /** @var modTemplateVar $tv */
        $tv = $this->modx->getObject(modTemplateVar::class, [
            'name' => 'articlestags',
        ]);
        if ($tv) {
            $this->resourceArray['tags'] = $this->resource->getTVValue('articlestags');
            $this->resourceArray['tagsId'] = $tv->get('id');
        }
        return $tv;
    }
}