<?php
header("Content-Type:text/html;charset=gb2312");
/**
 * 调用方式比较简单: echo getcontent($url); 可以抓取网页/也可以抓取图片
 * 批量抓取的时候用file_get_contents可能会飙升服务器cpu和内存, 而 curl方式则不会, 而且还可以伪装马甲防止批量抓取被发现封ip.
 * Author: Tekin  http://dev.yunnan.ws
 */
function getcontent($weburl)
{
	$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
	$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HEADER, true); // 过滤HTTP头
	curl_setopt($curl, CURLOPT_TIMEOUT, 40);
	curl_setopt($curl, CURLOPT_URL, $weburl);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $user_IP, 'CLIENT-IP:' . $user_IP)); //伪装IP为用户IP
	curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com'); //伪装一个来路
	curl_setopt($curl, CURLOPT_USERAGENT, 'Baiduspider+(+http://www.google.com/search/spider.htm)'); //伪装成百度蜘蛛 
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //抓取转跳
	curl_setopt($curl, CURLOPT_BINARYTRANSFER, true) ;
	curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate'); //gzip解压
	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
	$data = curl_exec($curl);
	// $infos = (curl_getinfo($curl));//返回抓取网页参数的值(数组);
	curl_close($curl);
	return $data;
}
	$furl = 'http://dongtaiwang.com/loc/download.php';
	$udata = @getcontent($furl);
	$fgpattern = "|<div id=\"image_center\" align=\"center\"><a href=\"([^\s]*)\"\sclass=\"download\" target=\"_blank\">免费下载(.*?)</a></div>|";
	preg_match_all($fgpattern, $udata, $fgarr); //使用正则匹配所有href=

    $title = $fgarr[2][0];
    $fgdurl = 'http://dongtaiwang.com'.$fgarr[1][0];
	echo '<pre><BR>FG最新版本:' . $title ;
    echo '<BR>下载地址:' . $fgdurl .'</pre>';

//放到任何支持PHP curl的空间即可直接下载FQ工具
if ($fgdurl ) {
      $content= @getcontent($fgdurl);
        header('cache-control:public');
        header('content-type:application/octet-stream');
        header('content-disposition:attachment; filename='.basename($fgdurl ));
        echo $content;
}
?>