<?php
/*路由配置*/
return array(
    'URL_ROUTER_ON'         =>  true,
    //路由配置
    'URL_ROUTE_RULES'       =>  array(
//        '/^homepage$/'=>'Index/index',
        '/^login$/'=>'Member/login',
        '/^logout$/'=>'Member/logout',
        '/^register$/'=>'Member/register',

        '/^main$/'=>'Index/index',
        '/^main\/(\d+)$/'=>'Index/index?p=:1',
        '/^t(\d+)$/'=>'Index/index?type=:1',
        '/^t(\d+)\/(\d+)$/'=>'Index/index?type=:1&p=:2',
        '/^miniAppsData$/' => 'Index/sendMiniAppsData',

        '/^read\/(\d+)/'=>'Article/read?bid=:1',

        '/^news$/' => 'News/index',

//        'main/p/:p'=>'Index/index',
//        'type/:type'=>'Index/index',
//        'search/:search'=>'Index/index',


        '/^user\/(\w+)$/'=>'Center/index',

        '/^publish$/'=>'Center/publish',
        '/^edit\/(\d+)/'=>'Center/edit?bid=:1',

        '/^admin$/' => 'Admin/index',

    )

);
?>