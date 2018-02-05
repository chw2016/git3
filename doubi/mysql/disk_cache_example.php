<?php
//
//	// Standard ezSQL Libs
//	include_once "ez_sql_core.php";
//	include_once "ez_sql_mysql.php";
//
//	// Initialise singleton
//	$db = new ezSQL_mysql('test1', 'test1test', 'ad_iwork_2015124','weiling2014.mysql.rds.aliyuncs.com');
//
//	// Cache expiry
//	$db->cache_timeout = 24; // Note: this is hours
//
//	// Specify a cache dir. Path is taken from calling script
//	$db->cache_dir = '/home/wwwroot/zm.7i1.cn/tmp';
//
//	// (1. You must create this dir. first!)
//	// (2. Might need to do chmod 775)
//
//	// Global override setting to turn disc caching off
//	// (but not on)
//	$db->use_disk_cache = true;
//
//	// By wrapping up queries you can ensure that the default
//	// is NOT to cache unless specified
//	$db->cache_queries = true;
//
//		// At last.. a query!
//		$db->get_results("SHOW TABLES");
//		$db->debug();
//
//		// Select * from use
//		$db->get_results("SELECT * FROM sys_user");
//		$db->debug();
//
//	// This ensures only the above querys are cached
//
//
//	// This query is NOT cached
//	$db->get_results("SELECT * FROM sys_user LIMIT 0,1");
//	$db->debug();
//$db->cache_queries = false;
///*
//
//	Of course, if you want to cache EVERYTHING just do..
//
//	$db = new ezSQL_mysql('db_user', 'db_pass', 'db_name');
//	$db->use_disk_cache = true;
//	$db->cache_queries = true;
//	$db->cache_timeout = 24;
//
//*/
//
//?>