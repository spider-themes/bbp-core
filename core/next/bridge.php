<?php

use SpiderDevs\Plugin\BBPC\Basic\AJAX;
use SpiderDevs\Plugin\BBPC\Basic\Cache;
use SpiderDevs\Plugin\BBPC\Basic\Feed;
use SpiderDevs\Plugin\BBPC\Basic\Forum;
use SpiderDevs\Plugin\BBPC\Basic\Mailer;
use SpiderDevs\Plugin\BBPC\Basic\Plugin;
use SpiderDevs\Plugin\BBPC\Basic\Roles;
use SpiderDevs\Plugin\BBPC\Basic\Signs;
use SpiderDevs\Plugin\BBPC\Basic\User;
use SpiderDevs\Plugin\BBPC\Basic\Wizard;
use SpiderDevs\Plugin\BBPC\Database\Bulk as BulkDB;
use SpiderDevs\Plugin\BBPC\Database\Cache as CacheDB;
use SpiderDevs\Plugin\BBPC\Database\Main as MainDB;
use SpiderDevs\Plugin\BBPC\Features\Attachments;
use SpiderDevs\Plugin\BBPC\Features\BuddyPressSignature;
use SpiderDevs\Plugin\BBPC\Features\CannedReplies;
use SpiderDevs\Plugin\BBPC\Features\ForumIndex;
use SpiderDevs\Plugin\BBPC\Features\LockForums;
use SpiderDevs\Plugin\BBPC\Features\PrivateReplies;
use SpiderDevs\Plugin\BBPC\Features\PrivateTopics;
use SpiderDevs\Plugin\BBPC\Features\Profiles;
use SpiderDevs\Plugin\BBPC\Features\Quote;
use SpiderDevs\Plugin\BBPC\Features\Report;
use SpiderDevs\Plugin\BBPC\Features\Signatures;
use SpiderDevs\Plugin\BBPC\Features\Thanks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bbpc_plugin() : Plugin {
	return Plugin::instance();
}

function bbpc_ajax() : AJAX {
	return AJAX::instance();
}

function bbpc_cache() : Cache {
	return Cache::instance();
}

function bbpc_roles() : Roles {
	return Roles::instance();
}

function bbpc_signs() : Signs {
	return Signs::instance();
}

function bbpc_feed() : Feed {
	return Feed::instance();
}

function bbpc_mailer() : Mailer {
	return Mailer::instance();
}

function bbpc_db() : MainDB {
	return MainDB::instance();
}

function bbpc_db_bulk() : BulkDB {
	return BulkDB::instance();
}

function bbpc_db_cache() : CacheDB {
	return CacheDB::instance();
}

function bbpc_forum( int $id = 0 ) : Forum {
	return Forum::instance( $id );
}

function bbpc_user( int $id = 0 ) : User {
	return User::instance( $id );
}

function bbpc_wizard() : Wizard {
	return Wizard::instance();
}

/** @return Attachments|bool */
function bbpc_attachments() {
	return Plugin::instance()->is_enabled( 'attachments' ) ? Attachments::instance() : false;
}

/** @return Signatures|bool */
function bbpc_signature() {
	return Plugin::instance()->is_enabled( 'signatures' ) ? Signatures::instance() : false;
}

/** @return Quote|bool */
function bbpc_quote() {
	return Plugin::instance()->is_enabled( 'quote' ) ? Quote::instance() : false;
}

/** @return Report|bool */
function bbpc_report() {
	return Plugin::instance()->is_enabled( 'report' ) ? Report::instance() : false;
}

/** @return Profiles|bool */
function bbpc_user_profiles() {
	return Plugin::instance()->is_enabled( 'profiles' ) ? Profiles::instance() : false;
}

/** @return ForumIndex|bool */
function bbpc_forum_index() {
	return Plugin::instance()->is_enabled( 'forum-index' ) ? ForumIndex::instance() : false;
}

/** @return LockForums|bool */
function bbpc_lock_forums() {
	return Plugin::instance()->is_enabled( 'lock-forums' ) ? LockForums::instance() : false;
}

/** @return PrivateTopics|bool */
function bbpc_private_topics() {
	return Plugin::instance()->is_enabled( 'private-topics' ) ? PrivateTopics::instance() : false;
}

/** @return PrivateReplies|bool */
function bbpc_private_replies() {
	return Plugin::instance()->is_enabled( 'private-replies' ) ? PrivateReplies::instance() : false;
}

/** @return Thanks|bool */
function bbpc_say_thanks() {
	return Plugin::instance()->is_enabled( 'thanks' ) ? Thanks::instance() : false;
}

/** @return CannedReplies|bool */
function bbpc_canned_replies() {
	return Plugin::instance()->is_enabled( 'canned-replies' ) ? CannedReplies::instance() : false;
}

/** @return BuddyPressSignature|bool */
function bbpc_buddypress_signature() {
	return Plugin::instance()->is_enabled( 'buddypress-signature' ) ? BuddyPressSignature::instance() : false;
}
