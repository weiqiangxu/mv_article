<?php

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

class Http{

	/**
		* 单线程异步下载文件
		* @author xu
		* @copyright 2017-12-16
		* @param [string] $url 路由地址
		* @param [string] $file 文件保存地址
		* @return success:boolean[true]; erro:false
	*/
	public function down($url,$file,$config=array('timeout'  => 60.0,'verify' => false,))
	{
		$file = str_replace('\\', '/', $file);
		$client = new Client();
		// 注册异步请求
		$client->getAsync($url,$config)->then(
		    function (ResponseInterface $res) use ($file,$url)
		    {
				if($res->getStatusCode()== 200)
	    		{
		            file_put_contents($file,$res->getBody());
	    		}else{
	    		}
		    },
		    function (RequestException $e) {
		        echo $e->getMessage().PHP_EOL;
		    }
		)->wait();
	}

	/**
		* 多线程并发下载文件
		* @author xu
		* @copyright 2017-12-16
		* @param [arr] $data array(array('url'=>'','file'=>'/temp/1.html')) 路由地址及相应保存路径
		* @param [arr] $config http请求
		* @return success:boolean[true]; erro:false
	*/
	public function pool($data,$config=array('timeout'  => 60.0,'verify' => false,))
	{
		$jar = new \GuzzleHttp\Cookie\CookieJar();
		foreach ($data as $k => $v) {
			$data[$k]['config'] = $config;
		}

		// 创建request对象
		$client = new Client();
        $requests = function ($total) use ($client,$data) {
            foreach ($data as $v) {
            	$url = $v['url'];
            	$config = $v['config'];
                yield function() use ($client,$url,$config) {
                    return $client->getAsync($url,$config);
                };
            }
        };

		$pool = new Pool($client, $requests(count($data)), [
			// 每发5个请求
		    'concurrency' => count($data),
		    'fulfilled' => function ($response, $index ) use($data) {
		        // 文件保存路径
		        $file = str_replace('\\', '/', $data[$index]['file']);
		        // 校验回调成功
		        if($response->getStatusCode()==200)
		        {
		        	// 保存文件
		        	echo 'download '.$index.' success'.PHP_EOL;
		            file_put_contents($file,$response->getBody());
		        }
		    },
		    'rejected' => function ($reason, $index) use($data) {
			    // echo $datas[$index]->id.'.html'." netError!".PHP_EOL;
		    },
		]);

		// Initiate the transfers and create a promise
		$promise = $pool->promise();

		// Force the pool of requests to complete.
		$promise->wait();
	}



	/**
		* post请求获取数据 - application/x-www-form-urlencoded
		* @author xu
		* @copyright 2017-12-16
		* @return success:boolean[true]; erro:false
	*/
	public function post($url,$params,$json_decode = true)
	{
		// post数据
		$client = new Client();
		$response = $client->request('POST',$url, [
		    'form_params' => $params
		]);
		$res = $response->getBody();
		if($json_decode){
			$res = json_decode($res,true);
		}
		return $res;
	}
	
	/**
		* post请求获取数据 - multipart/form-data
		* @author xu
		* @copyright 2017-12-16
		* @return success:boolean[true]; erro:false
	*/
	public function post_form($url,$params,$json_decode = true)
	{
		// post数据
		$client = new Client();
		$temp = array();
		foreach ($params as $key => $value) {
			$temp[] = array('name'=>$key,'contents'=>$value);
		}
		$response = $client->request('POST',$url, [
		    'multipart' => $temp
		]);
		$res = $response->getBody();
		if($json_decode){
			$res = json_decode($res,true);
		}
		return $res;
	}

}