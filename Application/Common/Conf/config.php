<?php
return array(
    //'配置项'=>'配置值'
    'LOAD_EXT_CONFIG' => 'db,param,route',
    'SHOW_PAGE_TRACE' => false,
    'URL_MODEL'             =>  2,
//    'URL_HTML_SUFFIX'       =>  '',

    'TMPL_PARSE_STRING'     => array(
        'COMMON_RES'=>'/Public/common',
        'HOME_RES'=>'/Public/home',
    ),

    'TMPL_ACTION_ERROR'     =>  'Public/jump_error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  'Public/jump_success',// 默认成功跳转对应的模板文件









);