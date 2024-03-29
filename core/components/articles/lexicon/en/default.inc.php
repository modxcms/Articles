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
$_lang['articles.advanced_settings'] = 'Advanced Settings';
$_lang['articles.all'] = 'All';
$_lang['articles.article'] = 'Article';
$_lang['articles.article_alias'] = 'Alias (Permalink)';
$_lang['articles.article_content'] = 'Content';
$_lang['articles.article_create'] = 'New Article';
$_lang['articles.article_delete'] = 'Delete Article';
$_lang['articles.article_delete_confirm'] = 'Are you sure you want to delete the selected Article?';
$_lang['articles.article_delete_multiple'] = 'Delete Multiple Articles';
$_lang['articles.article_delete_multiple_confirm'] = 'Are you sure you want to delete the selected Articles?';
$_lang['articles.article_duplicate'] = 'Duplicate Article';
$_lang['articles.article_edit'] = 'Edit Article';
$_lang['articles.article_options'] = 'Article Options';
$_lang['articles.article_edit_options'] = 'Editing Options';
$_lang['articles.article_publish'] = 'Publish';
$_lang['articles.article_selected_delete'] = 'Delete Selected';
$_lang['articles.article_selected_undelete'] = 'Undelete Selected';
$_lang['articles.article_selected_publish'] = 'Publish Selected';
$_lang['articles.article_selected_unpublish'] = 'Unpublish Selected';
$_lang['articles.article_summary'] = 'Summary';
$_lang['articles.article_description'] = 'Description';
$_lang['articles.article_description_desc'] = 'A short description of your Article.';
$_lang['articles.article_tags'] = 'Tags';
$_lang['articles.article_title'] = 'Article Title';
$_lang['articles.article_unpublish'] = 'Unpublish';
$_lang['articles.articles'] = 'Articles';
$_lang['articles.articles_err_ns_multiple'] = 'Please select at least one Article.';
$_lang['articles.articles_import'] = 'Import Articles';
$_lang['articles.articles_manage'] = 'Manage Articles';
$_lang['articles.articles_none'] = 'No articles yet!';
$_lang['articles.articles_search'] = 'Search Articles...';
$_lang['articles.articles_view'] = 'View Articles';
$_lang['articles.articles_write_new'] = 'Write New Article';
$_lang['articles.author'] = 'Author';
$_lang['articles.categories'] = 'Categories';
$_lang['articles.comments'] = 'Comments';
$_lang['articles.comments.intro_msg'] = 'Here you can review, approve and reject comments for all posts for your Articles.';
$_lang['articles.container'] = 'Articles Container';
$_lang['articles.container_alias'] = 'Container Alias';
$_lang['articles.container_alias_desc'] = 'The web friendly URL that will be used to calculate the URL of this Articles Container.';
$_lang['articles.container_create_here'] = 'Create Articles Here';
$_lang['articles.container_delete'] = 'Delete Articles Container';
$_lang['articles.container_description'] = 'Description';
$_lang['articles.container_description_desc'] = 'A short description of your Articles container.';
$_lang['articles.container_duplicate'] = 'Duplicate Articles Container';
$_lang['articles.container_menutitle_desc'] = 'The title used when displayed in a navigation menu.';
$_lang['articles.container_new'] = 'New Articles Container';
$_lang['articles.container_publish'] = 'Publish Articles Container';
$_lang['articles.container_settings'] = 'Container Settings';
$_lang['articles.container_title'] = 'Container Title';
$_lang['articles.container_title_desc'] = 'The title of your Articles Container.';
$_lang['articles.container_undelete'] = 'Undelete Articles Container';
$_lang['articles.container_unpublish'] = 'Unpublish Articles Container';
$_lang['articles.content'] = 'Content';
$_lang['articles.filter_ellipsis'] = 'Filter...';
$_lang['articles.import_blogger_container_err_nf'] = 'Could not find Articles Container to import into!';
$_lang['articles.import_blogger_file'] = 'Blogger Export XML File';
$_lang['articles.import_blogger_file_desc'] = 'Select the Blogger XML file that you get when you export from Blogger.';
$_lang['articles.import_blogger_file_err_nf'] = 'Please specify the Blogger Export XML File.';
$_lang['articles.import_blogger_file_server'] = 'Blogger Export XML File on Server';
$_lang['articles.import_blogger_file_server_desc'] = 'Or, alternatively, specify an absolute path to the Blogger XML file that you get when you export from Blogger that is located on your server. You may use {core_path}, {base_path} and {assets_path} as placeholders.';
$_lang['articles.import_modx_change_template'] = 'Change Template to Container\'s';
$_lang['articles.import_modx_change_template_desc'] = 'If checked, will change the Template of the new Article to this Article Container\'s Template. Recommended to leave checked.';
$_lang['articles.import_modx_commentsThreadNameFormat'] = 'Quip Thread Name Format';
$_lang['articles.import_modx_commentsThreadNameFormat_desc'] = 'The name format (such as "blog-post-[[*id]]") that you set for the thread property for Quip. Use [[*id]] to replace with the imported Resource\'s ID. Leave blank to not import comments.';
$_lang['articles.import_modx_err_no_criteria'] = 'Please specify at least one limiting factor in the import criteria.';
$_lang['articles.import_modx_intro'] = 'This will import content, tags and comments from existing MODX Resources. Note that this does not import any content outside of the main content field, comments and the tags field.';
$_lang['articles.import_modx_hidemenu'] = 'Import Hidden Resources';
$_lang['articles.import_modx_hidemenu_desc'] = 'If checked, will also get Resources checked with Hide From Menus.';
$_lang['articles.import_modx_parents'] = 'Parent Resource(s)';
$_lang['articles.import_modx_parents_desc'] = 'A comma-separated list of parent Resources to import all their children from.';
$_lang['articles.import_modx_resources'] = 'Specific Resources';
$_lang['articles.import_modx_resources_desc'] = 'A comma-separated list of specific Resources to import. Add a - before the ID (eg, -123) to exclude it from the import.';
$_lang['articles.import_modx_template'] = 'From Template';
$_lang['articles.import_modx_template_desc'] = 'If selected, will grab all Resources from this specific Template, unless excluded via the Specific Resources field. NOTE: Will not import any Template content or TV values.';
$_lang['articles.import_modx_tagsField'] = 'Tags Field';
$_lang['articles.import_modx_tagsField_desc'] = 'The field (or TV) that your old blog set tags on. Prefix with "tv." to specify a TV (eg, "tv.tags").';
$_lang['articles.import_modx_unpublished'] = 'Import Unpublished';
$_lang['articles.import_modx_unpublished_desc'] = 'If checked, will also import any unpublished Resources.';
$_lang['articles.import_options'] = 'Import Options';
$_lang['articles.import_service'] = 'Import From';
$_lang['articles.import_service_desc'] = 'Select the Service or Application that you would like to import from.';
$_lang['articles.import_wp_container_err_nf'] = 'Could not find Articles Container to import into!';
$_lang['articles.import_wp_file'] = 'WordPress Export WXR File';
$_lang['articles.import_wp_file_desc'] = 'Select the WordPress WXR (XML) file that you get when you export from WordPress.';
$_lang['articles.import_wp_file_err_nf'] = 'Please specify the WordPress WXR File.';
$_lang['articles.import_wp_file_server'] = 'WordPress Export WXR File on Server';
$_lang['articles.import_wp_file_server_desc'] = 'Or, alternatively, specify an absolute path to the WordPress WXR (XML) file that you get when you export from WordPress that is located on your server. You may use {core_path}, {base_path} and {assets_path} as placeholders.';
$_lang['articles.publishedon'] = 'Published On';
$_lang['articles.published'] = 'Published';
$_lang['articles.publishing_information'] = 'Publishing Information';
$_lang['articles.settings'] = 'Settings';
$_lang['articles.settings_archiving'] = 'Archives';
$_lang['articles.settings_comments'] = 'Comments';
$_lang['articles.settings_comments_display'] = 'Display';
$_lang['articles.settings_comments_latest'] = 'Latest';
$_lang['articles.settings_comments_moderation'] = 'Security';
$_lang['articles.settings_comments_other'] = 'Other';
$_lang['articles.settings_general'] = 'General';
$_lang['articles.settings_latest_posts'] = 'Latest Posts';
$_lang['articles.settings_notifications'] = 'Notifications';
$_lang['articles.settings_pagination'] = 'Pagination';
$_lang['articles.settings_rss'] = 'RSS';
$_lang['articles.settings_tagging'] = 'Tagging';
$_lang['articles.statistics'] = 'Statistics';
$_lang['articles.status'] = 'Status';
$_lang['articles.template'] = 'Template';
$_lang['articles.template_desc'] = 'The Template that the main listing view uses.';
$_lang['articles.tags'] = 'Tags';
$_lang['articles.unpublished'] = 'Not Published';
$_lang['none'] = 'None';

/* General */
$_lang['articles.setting.updateServicesEnabled'] = 'Enable Update Services';
$_lang['articles.setting.updateServicesEnabled_desc'] = 'If on, Articles will attempt to ping Ping-o-Matic whenever you publish an Article, to send out your article\'s title and URL to major search engines.';
$_lang['articles.setting.published_desc'] = 'Default published status for new articles.';
$_lang['articles.setting.richtext_desc'] = 'Once created, individual Articles can override this value.';
$_lang['articles.setting.sortBy'] = 'Sort Field';
$_lang['articles.setting.sortBy_desc'] = 'The field to sort by on the main and archives listing pages.';
$_lang['articles.setting.sortDir'] = 'Sort Direction';
$_lang['articles.setting.sortDir_desc'] = 'The direction to sort by on the main and archives listing pages (DESC or ASC).';
$_lang['articles.setting.archivesIncludeTVs'] = 'Include TVs in Listing';
$_lang['articles.setting.archivesIncludeTVs_desc'] = 'If on, will include TV values as options in the listing chunks.';
$_lang['articles.setting.includeTVsList'] = 'Include TVs List';
$_lang['articles.setting.includeTVsList_desc'] = 'An optional comma-delimited list of TemplateVar names to include explicitly if Include TVs is on.';
$_lang['articles.setting.archivesProcessTVs'] = 'Process TVs in Listing';
$_lang['articles.setting.archivesProcessTVs_desc'] = 'If on, will process the TV values in the listing chunks.';
$_lang['articles.setting.processTVsList'] = 'Process TVs List';
$_lang['articles.setting.processTVsList_desc'] = 'An optional comma-delimited list of TemplateVar names to process explicitly. TemplateVars specified here must be included via Include TVs/Include TVs List.';
$_lang['articles.setting.otherGetArchives'] = 'Other Listing Parameters';
$_lang['articles.setting.otherGetArchives_desc'] = 'Any other properties you would like to add to the getResources/getPage call for the Articles listing page. Put them in MODX tag syntax, as if you were adding it to the tag call (eg, &property=`value`).';
$_lang['articles.setting.articleUriTemplate'] = 'Articles URL Format';
$_lang['articles.setting.articleUriTemplate_desc'] = '%Y = year, 4 digits, %m = month (with leading zeros), %d = day (with leading zeros), %alias = Article Alias, %id = Article ID, %ext = File extension (e.g. html). <b>Note</b>: Changes to this setting will only affect *new* Articles, unless you change the alias or unpublish and then republish old articles, which will then regenerate their URL to the new format.';

/* template / archives settings */
$_lang['articles.setting.articleTemplate'] = 'Article Template';
$_lang['articles.setting.articleTemplate_desc'] = 'The default Template to use for Articles.';
$_lang['articles.setting.tplArticleRow'] = 'Article Row Chunk';
$_lang['articles.setting.tplArticleRow_desc'] = 'The Chunk to use when displaying Articles on the front page or archive pages.';
$_lang['articles.setting.archiveByMonth'] = 'Archive By Month';
$_lang['articles.setting.archiveByMonth_desc'] = 'Whether or not to archive by month or by year. Yes will archive by month.';
$_lang['articles.setting.tplArchiveMonth'] = 'Archive Listing Chunk';
$_lang['articles.setting.tplArchiveMonth_desc'] = 'The Chunk to use for each month/year row that is listed.';
$_lang['articles.setting.archiveListingsLimit'] = 'Archive Listings to Show';
$_lang['articles.setting.archiveListingsLimit_desc'] = 'The number of archive months/years to show.';
$_lang['articles.setting.archiveCls'] = 'Archive CSS Class';
$_lang['articles.setting.archiveCls_desc'] = 'A CSS class to apply to each archive listing.';
$_lang['articles.setting.archiveAltCls'] = 'Archive Alternate CSS Class';
$_lang['articles.setting.archiveAltCls_desc'] = 'A CSS class to apply to each alternate row for each archive listing.';
$_lang['articles.setting.archiveGroupByYear'] = 'Group By Year';
$_lang['articles.setting.archiveGroupByYear_desc'] = 'If set to 1, will group archive results by year into a nested list. If set to 1, this will ignore the Archive By Month setting.';
$_lang['articles.setting.archiveGroupByYearTpl'] = 'Group By Year Chunk';
$_lang['articles.setting.archiveGroupByYearTpl_desc'] = 'If Group By Year is set to 1, the Chunk to use for the wrapper for the archive list grouping.';

/* Pagination */
$_lang['articles.setting.articlesPerPage'] = 'Articles Per Page';
$_lang['articles.setting.articlesPerPage_desc'] = 'The number of Articles to show per page when listing posts.';
$_lang['articles.setting.pageLimit'] = 'Pages Limit';
$_lang['articles.setting.pageLimit_desc'] = 'The maximum number of pages to display when rendering paging controls';
$_lang['articles.setting.pageNavTpl'] = 'Page Nav Tpl';
$_lang['articles.setting.pageNavTpl_desc'] = 'Content representing a single page navigation control.';
$_lang['articles.setting.pageActiveTpl'] = 'Page Active Tpl';
$_lang['articles.setting.pageActiveTpl_desc'] = 'Content representing the current page navigation control.';
$_lang['articles.setting.pageFirstTpl'] = 'Page First Tpl';
$_lang['articles.setting.pageFirstTpl_desc'] = 'Content representing the first page navigation control.';
$_lang['articles.setting.pageLastTpl'] = 'Page Last Tpl';
$_lang['articles.setting.pageLastTpl_desc'] = 'Content representing the last page navigation control.';
$_lang['articles.setting.pagePrevTpl'] = 'Page Prev Tpl';
$_lang['articles.setting.pagePrevTpl_desc'] = 'Content representing the previous page navigation control.';
$_lang['articles.setting.pageNextTpl'] = 'Page Next Tpl';
$_lang['articles.setting.pageNextTpl_desc'] = 'Content representing the next page navigation control.';
$_lang['articles.setting.pageOffset'] = 'Page Offset';
$_lang['articles.setting.pageOffset_desc'] = 'The offset, or record position to start at within the collection for rendering results for the current page; should be calculated based on page variable set in Page Var Key.';
$_lang['articles.setting.pageVarKey'] = 'Page Var Key';
$_lang['articles.setting.pageVarKey_desc'] = 'The key of a property that indicates the current page.';
$_lang['articles.setting.pageTotalVar'] = 'Total Var';
$_lang['articles.setting.pageTotalVar_desc'] = 'The key of a placeholder that must contain the total records in the limitable collection being paged.';
$_lang['articles.setting.pageNavVar'] = 'Page Nav Var';
$_lang['articles.setting.pageNavVar_desc'] = 'The key of a placeholder to be set with the paging navigation controls.';


/* RSS settings */
$_lang['articles.setting.rssAlias'] = 'RSS Alias (Permalink)';
$_lang['articles.setting.rssAlias_desc'] = 'The alias (permalink) for the RSS feed, appended to the Articles Container URL.';
$_lang['articles.setting.rssItems'] = 'Number of RSS Items';
$_lang['articles.setting.rssItems_desc'] = 'The number of RSS items to show on the RSS feed. Set to 0 for unlimited.';
$_lang['articles.setting.tplRssFeed'] = 'RSS Feed Chunk';
$_lang['articles.setting.tplRssFeed_desc'] = 'The Chunk to use for the RSS Feed template.';
$_lang['articles.setting.tplRssItem'] = 'RSS Item Chunk';
$_lang['articles.setting.tplRssItem_desc'] = 'The Chunk to use for each item in the RSS Feed.';

/* Tagging */
$_lang['articles.setting.tagsLimit'] = 'Tag Listings to Show';
$_lang['articles.setting.tagsLimit_desc'] = 'The number of tags to show in the popular tags listing.';
$_lang['articles.setting.tplTagRow'] = 'Tag Listing Chunk';
$_lang['articles.setting.tplTagRow_desc'] = 'The Chunk to use when displaying tags on the listing pages.';
$_lang['articles.setting.tagCls'] = 'Tag CSS Class';
$_lang['articles.setting.tagCls_desc'] = 'A CSS class to apply to each tag listing.';
$_lang['articles.setting.tagAltCls'] = 'Tag Alternate CSS Class';
$_lang['articles.setting.tagAltCls_desc'] = 'A CSS class to apply to each alternate row for each tag listing.';

/* Latest Posts */
$_lang['articles.setting.latestPostsTpl'] = 'Latest Articles Chunk';
$_lang['articles.setting.latestPostsTpl_desc'] = 'The Chunk to use for each Latest Article.';
$_lang['articles.setting.latestPostsLimit'] = 'Latest Articles to Show';
$_lang['articles.setting.latestPostsLimit_desc'] = 'The number of latest Articles to show.';
$_lang['articles.setting.latestPostsOffset'] = 'Latest Articles Offset';
$_lang['articles.setting.latestPostsOffset_desc'] = 'The starting index of the listing of latest Articles.';
$_lang['articles.setting.otherLatestPosts'] = 'Other Listing Parameters';
$_lang['articles.setting.otherLatestPosts_desc'] = 'Any other properties you would like to add to the getResources/getPage call for the Latest Posts widget. Put them in MODX tag syntax, as if you were adding it to the tag call (eg, &property=`value`).';

/* Notifications */
$_lang['articles.setting.notifyTwitter'] = 'Send to Twitter';
$_lang['articles.setting.notifyTwitter_desc'] = 'Automatically post link to Twitter when Article is published.';
$_lang['articles.setting.notifyTwitter_notyet_desc'] = 'Automatically post link to Twitter when Article is published. <strong>Note: You must first visit <a href="[[+authUrl]]" target="_blank">the authentication page</a> to authenticate Articles to your Twitter account!</strong>';
$_lang['articles.setting.notifyTwitterConsumerKey'] = 'Twitter Consumer Key';
$_lang['articles.setting.notifyTwitterConsumerKey_desc'] = 'Optional. The Consumer Key for your Twitter account that maps to the Twitter app used for authentication. If not set, will use the MODX-Articles default. Override with your own Twitter App Consumer Key for more security.';
$_lang['articles.setting.notifyTwitterConsumerKeySecret'] = 'Secret Twitter Consumer Key';
$_lang['articles.setting.notifyTwitterConsumerKeySecret_desc'] = 'Optional. The Secret Access Token for your Twitter that maps to the Twitter app used for authentication. If not set, will use the MODX-Articles default. Override with your own Twitter App Consumer Key Secret for more security.';
$_lang['articles.setting.notifyTwitterTpl'] = 'Twitter Template';
$_lang['articles.setting.notifyTwitterTpl_desc'] = 'The template that the message to Twitter will be sent as.';
$_lang['articles.setting.notifyTwitterTagLimit'] = 'Twitter Tag Limit';
$_lang['articles.setting.notifyTwitterTagLimit_desc'] = 'The number of tags to be used when [[+hashtags]] is used in the tpl; this placeholder appends tags as hashtags to the Tweet.';
$_lang['articles.setting.shorteningService'] = 'URL Shortener';
$_lang['articles.setting.shorteningService_desc'] = 'The service to use for shortening URLs. Set to None to not shorten URLs.';

/* Latest Comments */
$_lang['articles.setting.latestCommentsTpl'] = 'Latest Comments Chunk';
$_lang['articles.setting.latestCommentsTpl_desc'] = 'The Chunk to use for each latest comment.';
$_lang['articles.setting.latestCommentsLimit'] = 'Latest Comments to Show';
$_lang['articles.setting.latestCommentsLimit_desc'] = 'The number of latest comments to show. Defaults to 10.';
$_lang['articles.setting.latestCommentsBodyLimit'] = 'Body Limit of Latest Comments';
$_lang['articles.setting.latestCommentsBodyLimit_desc'] = 'The number of characters to show for the latest comment before truncating with an ellipsis (...).';
$_lang['articles.setting.latestCommentsRowCss'] = 'Latest Comments Row CSS';
$_lang['articles.setting.latestCommentsRowCss_desc'] = 'The CSS class to set for each latest comment.';
$_lang['articles.setting.latestCommentsAltRowCss'] = 'Latest Comments Alt Row CSS';
$_lang['articles.setting.latestCommentsAltRowCss_desc'] = 'The CSS class to set for each alternate latest comment.';

/* Comments */
$_lang['articles.setting.commentsEnabled'] = 'Enable Comments';
$_lang['articles.setting.commentsEnabled_desc'] = 'Whether or not to enable comments.';
$_lang['articles.setting.commentsThreaded'] = 'Threaded Comments';
$_lang['articles.setting.commentsThreaded_desc'] = 'Whether or not this thread can have threaded comments. Threaded comments allow users to comment on comments, increasing the level of indentation. Non-threaded comments allow users to comment only on the parent article, not on the comments.';
$_lang['articles.setting.commentsReplyResourceId'] = 'Reply Resource ID';
$_lang['articles.setting.commentsReplyResourceId_desc'] = 'The ID of the Resource where the QuipReply snippet is held, for replying to threaded comments. <strong>This is required for threaded comments.</strong>';
$_lang['articles.setting.commentsMaxDepth'] = 'Maximum Threading Depth';
$_lang['articles.setting.commentsMaxDepth_desc'] = 'The maximum depth that replies can be made in a threaded comment thread.';
$_lang['articles.setting.commentsTplComment'] = 'Comment Chunk';
$_lang['articles.setting.commentsTplComment_desc'] = 'The Chunk to use for each comment.';
$_lang['articles.setting.commentsTplCommentOptions'] = 'Comments Options Chunk';
$_lang['articles.setting.commentsTplCommentOptions_desc'] = 'A chunk for the options, such as delete, shown to an owner of a comment.';
$_lang['articles.setting.commentsTplComments'] = 'Comments Wrapper Chunk';
$_lang['articles.setting.commentsTplComments_desc'] = 'The outer wrapper for comments. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['articles.setting.commentsTplAddComment'] = 'Reply Form Chunk';
$_lang['articles.setting.commentsTplAddComment_desc'] = 'The add comment form Chunk.';
$_lang['articles.setting.commentsTplLoginToComment'] = 'Login to Comment Chunk';
$_lang['articles.setting.commentsTplLoginToComment_desc'] = 'The Chunk that shows when the user is not logged in and authentication is required.';
$_lang['articles.setting.commentsTplPreview'] = 'Preview Chunk';
$_lang['articles.setting.commentsTplPreview_desc'] = 'The Chunk for the Preview Comment view.';
$_lang['articles.setting.commentsUseCss'] = 'Use Quip CSS';
$_lang['articles.setting.commentsUseCss_desc'] = 'Provide a basic CSS template for the presentation.';
$_lang['articles.setting.commentsAltRowCss'] = 'Alternate Row CSS Class';
$_lang['articles.setting.commentsAltRowCss_desc'] = 'The CSS class to put on alternating comments.';
$_lang['articles.setting.commentsSortDir'] = 'Comment Sort Direction';
$_lang['articles.setting.commentsSortDir_desc'] = 'The direction to sort comments (DESC or ASC).';
$_lang['articles.setting.commentsNameField'] = 'Name Field';
$_lang['articles.setting.commentsNameField_desc'] = 'The field to use for the author name of each comment. Recommended values are "name" or "username".';
$_lang['articles.setting.commentsShowAnonymousName'] = 'Show Anonymous Name';
$_lang['articles.setting.commentsShowAnonymousName_desc'] = 'Display the value of anonymousName property (defaults to "Anonymous") if the user is not logged in when posting.';
$_lang['articles.setting.commentsAnonymousName'] = 'Anonymous Name';
$_lang['articles.setting.commentsAnonymousName_desc'] = 'The name to display for anonymous postings. Defaults to "Anonymous".';
$_lang['articles.setting.commentsLimit'] = 'Number of Comments Per Page';
$_lang['articles.setting.commentsLimit_desc'] = 'The number of comments to limit per page. Setting this to a non-zero number will enable pagination.';
$_lang['articles.setting.commentsCloseAfter'] = 'Close Comments After';
$_lang['articles.setting.commentsCloseAfter_desc'] = 'The number of days at which the thread will automatically close after it was created. Set to 0 to leave open indefinitely.';
$_lang['articles.setting.commentsRequirePreview'] = 'Require Posting Preview';
$_lang['articles.setting.commentsRequirePreview_desc'] = 'Require a user to preview their comment before posting.';
$_lang['articles.setting.commentsRequireAuth'] = 'Require Authentication To Comment';
$_lang['articles.setting.commentsRequireAuth_desc'] = 'Only logged in users will be able to comment on the thread.';
$_lang['articles.setting.commentsReCaptcha'] = 'ReCaptcha';
$_lang['articles.setting.commentsReCaptcha_desc'] = 'Enable reCaptcha in the add comment form.';
$_lang['articles.setting.commentsDisableReCaptchaWhenLoggedIn'] = 'Disable reCaptcha When Logged In';
$_lang['articles.setting.commentsDisableReCaptchaWhenLoggedIn_desc'] = 'Disable reCaptcha validation for logged in users.';
$_lang['articles.setting.commentsModerate'] = 'Moderate Comments';
$_lang['articles.setting.commentsModerate_desc'] = 'All new posts to the thread will be moderated.';
$_lang['articles.setting.commentsModerateAnonymousOnly'] = 'Moderate Anonymous Postings Only';
$_lang['articles.setting.commentsModerateAnonymousOnly_desc'] = 'Only moderate posts by users not logged in.';
$_lang['articles.setting.commentsModerateFirstPostOnly'] = 'Moderate First Post Only';
$_lang['articles.setting.commentsModerateFirstPostOnly_desc'] = 'Only moderate the first post of logged-in users. All other posts will auto-approve. Anonymous posts will always be moderated.';
$_lang['articles.setting.commentsModerators'] = 'Moderators';
$_lang['articles.setting.commentsModerators_desc'] = 'A comma-separated list of moderator usernames.';
$_lang['articles.setting.commentsModeratorGroup'] = 'Moderator Group';
$_lang['articles.setting.commentsModeratorGroup_desc'] = 'Any Users in this User Group will have moderator access.';
$_lang['articles.setting.commentsAllowRemove'] = 'Allow Remove';
$_lang['articles.setting.commentsAllowRemove_desc'] = 'Allow logged-in users to remove their own postings.';
$_lang['articles.setting.commentsRemoveThreshold'] = 'Removal Threshold';
$_lang['articles.setting.commentsRemoveThreshold_desc'] = 'If Allow Remove is on, the number of minutes a user can remove their posting after they have posted it.';
$_lang['articles.setting.commentsAllowReportAsSpam'] = 'Allow Report As Spam';
$_lang['articles.setting.commentsAllowReportAsSpam_desc'] = 'Allow logged-in users to report comments as spam.';
$_lang['articles.setting.commentsDateFormat'] = 'Post Date Format';
$_lang['articles.setting.commentsDateFormat_desc'] = 'The format of the date to show for a comment\'s post date. The syntax is in PHP strftime format.';
$_lang['articles.setting.commentsAutoConvertLinks'] = 'Auto Convert Hyperlinks';
$_lang['articles.setting.commentsAutoConvertLinks_desc'] = 'Automatically convert URLs to links.';
$_lang['articles.setting.commentsGravatar'] = 'Use Gravatar';
$_lang['articles.setting.commentsGravatar_desc'] = 'Whether or not to show Gravatar icons in comments.';
$_lang['articles.setting.commentsGravatarIcon'] = 'Gravatar Icon Style';
$_lang['articles.setting.commentsGravatarIcon_desc'] = 'The type of Gravatar icon to use for a user without a Gravatar.';
$_lang['articles.setting.commentsGravatarSize'] = 'Gravatar Icon Size';
$_lang['articles.setting.commentsGravatarSize_desc'] = 'The size in pixels of the Gravatar. Default is 50.';
$_lang['articles.setting.'] = '';
$_lang['articles.setting._desc'] = '';
$_lang['articles.loading'] = 'Loading';

/* Settings */
$_lang['setting_articles.article_show_longtitle'] = 'Show Long Title Field';
$_lang['setting_articles.article_show_longtitle_desc'] = 'Set this option to "Yes" if you want the field "Long Title" to be displayed when editing an article.';

$_lang['setting_articles.default_container_template'] = 'Default Articles Container Template';
$_lang['setting_articles.default_container_template_desc'] = 'The default Template (ID) to use when creating a new Articles Container';

$_lang['setting_articles.default_article_template'] = 'Default Article Template';
$_lang['setting_articles.default_article_template_desc'] = 'The default Template (ID) to use when creating a new Article when there is none specified on the Container itself.';

$_lang['setting_articles.container_ids'] = 'Articles FURL IDs';
$_lang['setting_articles.container_ids_desc'] = 'A comma-separated list of container IDs in use for FURL routing. Best to leave this alone.';

$_lang['setting_articles.default_article_sort_field'] = 'Default Sort Field for Articles In Manager';
$_lang['setting_articles.default_article_sort_field_desc'] = 'The default sorting field for articles in the grid when editing a Container. This field must be a column in the site_content table. TVs not allowed.';

$_lang['setting_articles.mgr_date_format'] = 'Manager Date Format';
$_lang['setting_articles.mgr_date_format_desc'] = 'Date format displayed inside an article container when listing articles inside the manager.';

$_lang['setting_articles.mgr_time_format'] = 'Manager Time Format';
$_lang['setting_articles.mgr_time_format_desc'] = 'Time format displayed inside an article container when listing articles inside the manager.';
