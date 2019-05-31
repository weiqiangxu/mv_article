<?php

// 初始化
require_once('./lib/init.php');
// 应用类库
require_once('./app/mv.class.php');

$mv = new mv();

$res = $mv->getArticle('https://www.cnblogs.com/xuweiqiang/p/10724797.html');

print_r($res);