<?php

// 初始化
require_once('./lib/init.php');
// 断章扫描对象
require_once('./app/find.php');

if(isset($argv[1])){
	$last_number = $argv[1];
}else{
	$last_number = 'unlimit';
}

$find = new find();

$find->start($last_number);