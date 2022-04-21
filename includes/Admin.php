<?php
namespace Bbp\Core;

 class Admin{
	 public function __construct() {
		 $this->load_admin_options();
	 }

	 public function load_admin_options() {
		 require __DIR__.'/Admin/admin-options.php';
	 }
 }