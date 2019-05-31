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
	* @param [string] $cnblogs_article_url 博客园文章链接
	* @author xu
	* @since  2019年02月26日
	*/ 
	public function getArticle($cnblogs_article_url){
		$Http = new Http();
		$file = APP_CACHE.'article.html';
		$Http->down($cnblogs_article_url,$file,$config=array('timeout'  => 60.0,'verify' => false,));
		$html = file_get_contents($file);

		// dom解析获取标题和内容
		$article_title = '';
		$article_content = ''; 
		if($dom = HtmlDomParser::str_get_html($html)){
			if($dom->find('.postTitle2',0)){
				$article_title = $dom->find('.postTitle2',0)->innertext;
			}
			if($dom->find('#cnblogs_post_body',0)){
				$article_content = $dom->find('#cnblogs_post_body',0)->innertext;
			}
		}
		// 转换格式
		$article_content = $this->transferContent($article_content);
		$data = array('title'=>$article_title,'content'=>$article_content);
		return $data;
	}


	/**
	* 博客园文章转换为markdown格式
	*
	* @author xu
	* @since  2019年02月26日
	*/ 
	public function transferContent($html){
		// 去除css样式
		$html = preg_replace('/style="[\s\S]+"/U', '', $html);
		// 代码块保留
		$html = str_replace(array('<pre>','</pre>'), array(PHP_EOL.'```'.PHP_EOL,PHP_EOL.'```'.PHP_EOL), $html);
		// 加粗保留
		$html = str_replace(array('<strong>','</strong>'), '**', $html);
		// 去除干扰图像
		$html = preg_replace('/<img\s+src="\/\/common.cnblogs([\s\S]+)>/U', '',$html);
		// 图像保留
		$html = str_replace(array('<img src="','" alt="">'), array('![](',')'), $html);
		// &lt;&nbsp;空格、尖括号、换行
		$html = str_replace(array('&lt;','&nbsp;','<br>'), array('','',PHP_EOL), $html);
		$html = strip_tags($html);
		$html = str_replace('?php', '<?php', $html);
		return $html;
	}


	/**
	* 将文章写在简书上
	*
	* @author xu
	* @since  2019年02月26日
	*/ 
	public function Writer($html){

	}


}


