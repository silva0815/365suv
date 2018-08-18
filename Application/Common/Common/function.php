<?php


/**
 * 获取配置参数
 * @param string $param 参数名
 * @param int $key 下标
 * @return mixed 所有值或某一个
 */
function getParam($param,$key=0){
    if($key){
        $data=C($param)[$key];

    }else{
        $data=C($param);
    }

    return $data;
}

/**
 * 递归删除文件夹内文件
 * @param string $path 文件夹地址
 * @return boolean
 */
function delete_file($path)
{
    if (is_dir($path)) {
        $arr = scandir($path);
        foreach ($arr as $v) {
            if ($v != '.' && $v != '..') {
                $path_new = $path . '/' . $v;
                delete_file($path_new);
            }
        }
    } else {
        $delete_path = $path;
        @unlink($delete_path);
    }

}

/**
 * 集中定义分页功能的实现
 * @param   int     $count          总记录数
 * @param   int     $listRow        每页显示的最大记录数
 * @param   array   $get            页码的携带参数
 * @param   string   $url           定义分页的url地址
 * @param   int     $rollpage       显示的最大页码数量
 * @return  array                   分页信息
 */
function getPage($count,$listRow,$get,$url,$rollpage){
    $page=new \Common\Util\IndexPage($count,$listRow,$get);
    $page->setUrl($url);
    $page->rollPage=$rollpage;
    $page->lastSuffix=false;
    $page->setConfig('prev','上一页');
    $page->setConfig('next','下一页');
    $page->setConfig('first','首页');
    $page->setConfig('last','末页');
    $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
    return $page;
}


function http_curl($url, $method = 'GET',$postData = array(),$type=true,$header=''){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($header){
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
    }
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //10秒超时
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; SV1)");
    if (strtoupper($method) == 'POST') {
        $curlPost = is_array($postData) ? http_build_query($postData) : $postData;
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    if($type){
        return json_decode($data,true);
    }else{
        return $data;
    }

}

/**
 * 获取ip归属地
 * @param String $ip
 * @return Array
 */
function getLocation($ip){
    $ipadd = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=".$ip);//根据新浪api接口获取
    if($ipadd){
        $charset = iconv("gbk","utf-8",$ipadd);
        preg_match_all("/[\x{4e00}-\x{9fa5}]+/u",$charset,$info);
    }else{
        $info= "获取失败";
    }

    return $info;
}
