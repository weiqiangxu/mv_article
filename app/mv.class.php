<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Sunra\PhpSimple\HtmlDomParser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverOptions;
use Zxing\QrReader;
use thiagoalessio\TesseractOCR\TesseractOCR;

/**
* 自动化
*/
class mv {

	/**
	* 读取博客园文章
	*
	* @author xu
	* @since  2019年02月26日
	*/ 
	public function getArticle(){

		// Selemium服务器
		$host = 'http://localhost:4444/wd/hub'; // this is the default
		$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
		// 登录地址
		$driver->get("https://www.cnblogs.com/xuweiqiang/");

		$driver->quit();

		echo 'success'.PHP_EOL;
	}
}


