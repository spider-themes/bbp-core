<?php

use Dev4Press\Plugin\GDBBX\Basic\AJAX;
use Dev4Press\Plugin\GDBBX\Basic\Cache;
use Dev4Press\Plugin\GDBBX\Basic\Feed;
use Dev4Press\Plugin\GDBBX\Basic\Forum;
use Dev4Press\Plugin\GDBBX\Basic\Mailer;
use Dev4Press\Plugin\GDBBX\Basic\Plugin;
use Dev4Press\Plugin\GDBBX\Basic\Roles;
use Dev4Press\Plugin\GDBBX\Basic\Signs;
use Dev4Press\Plugin\GDBBX\Basic\User;
use Dev4Press\Plugin\GDBBX\Basic\Wizard;
use Dev4Press\Plugin\GDBBX\Database\Bulk as BulkDB;
use Dev4Press\Plugin\GDBBX\Database\Cache as CacheDB;
use Dev4Press\Plugin\GDBBX\Database\Main as MainDB;
use Dev4Press\Plugin\GDBBX\Features\Attachments;
use Dev4Press\Plugin\GDBBX\Features\BuddyPressSignature;
use Dev4Press\Plugin\GDBBX\Features\CannedReplies;
use Dev4Press\Plugin\GDBBX\Features\ForumIndex;
use Dev4Press\Plugin\GDBBX\Features\LockForums;
use Dev4Press\Plugin\GDBBX\Features\PrivateReplies;
use Dev4Press\Plugin\GDBBX\Features\PrivateTopics;
use Dev4Press\Plugin\GDBBX\Features\Profiles;
use Dev4Press\Plugin\GDBBX\Features\Quote;
use Dev4Press\Plugin\GDBBX\Features\Report;
use Dev4Press\Plugin\GDBBX\Features\Signatures;
use Dev4Press\Plugin\GDBBX\Features\Thanks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gdbbx_plugin() : Plugin {
	return Plugin::instance();
}

function gdbbx_ajax() : AJAX {
	return AJAX::instance();
}

function gdbbx_cache() : Cache {
	return Cache::instance();
}

function gdbbx_roles() : Roles {
	return Roles::instance();
}

function gdbbx_signs() : Signs {
	return Signs::instance();
}

function gdbbx_feed() : Feed {
	return Feed::instance();
}

function gdbbx_mailer() : Mailer {
	return Mailer::instance();
}

function gdbbx_db() : MainDB {
	return MainDB::instance();
}

function gdbbx_db_bulk() : BulkDB {
	return BulkDB::instance();
}

function gdbbx_db_cache() : CacheDB {
	return CacheDB::instance();
}

function gdbbx_forum( int $id = 0 ) : Forum {
	return Forum::instance( $id );
}

function gdbbx_user( int $id = 0 ) : User {
	return User::instance( $id );
}

function gdbbx_wizard() : Wizard {
	return Wizard::instance();
}

/** @return Attachments|bool */
function gdbbx_attachments() {
	return Plugin::instance()->is_enabled( 'attachments' ) ? Attachments::instance() : false;
}

/** @return Signatures|bool */
function gdbbx_signature() {
	return Plugin::instance()->is_enabled( 'signatures' ) ? Signatures::instance() : false;
}

/** @return Quote|bool */
function gdbbx_quote() {
	return Plugin::instance()->is_enabled( 'quote' ) ? Quote::instance() : false;
}

/** @return Report|bool */
function gdbbx_report() {
	return Plugin::instance()->is_enabled( 'report' ) ? Report::instance() : false;
}

/** @return Profiles|bool */
function gdbbx_user_profiles() {
	return Plugin::instance()->is_enabled( 'profiles' ) ? Profiles::instance() : false;
}

/** @return ForumIndex|bool */
function gdbbx_forum_index() {
	return Plugin::instance()->is_enabled( 'forum-index' ) ? ForumIndex::instance() : false;
}

/** @return LockForums|bool */
function gdbbx_lock_forums() {
	return Plugin::instance()->is_enabled( 'lock-forums' ) ? LockForums::instance() : false;
}

/** @return PrivateTopics|bool */
function gdbbx_private_topics() {
	return Plugin::instance()->is_enabled( 'private-topics' ) ? PrivateTopics::instance() : false;
}

/** @return PrivateReplies|bool */
function gdbbx_private_replies() {
	return Plugin::instance()->is_enabled( 'private-replies' ) ? PrivateReplies::instance() : false;
}

/** @return Thanks|bool */
function gdbbx_say_thanks() {
	return Plugin::instance()->is_enabled( 'thanks' ) ? Thanks::instance() : false;
}

/** @return CannedReplies|bool */
function gdbbx_canned_replies() {
	return Plugin::instance()->is_enabled( 'canned-replies' ) ? CannedReplies::instance() : false;
}

/** @return BuddyPressSignature|bool */
function gdbbx_buddypress_signature() {
	return Plugin::instance()->is_enabled( 'buddypress-signature' ) ? BuddyPressSignature::instance() : false;
}
