<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//后台路由
// Route::rule('manage','admin/Index/index');

// user 别名路由到 index/User 控制器
Route::alias('manage','admin/Index/index');
Route::alias('back','admin/info/userlist');
Route::alias('2019cny_cotton','index/Index/index');
Route::alias('query','index/Index/query');
Route::alias('winlist','index/Index/winlist');

