<?php

namespace app\admin\controller;
use think\Controller;
use think\Container;
use app\admin\model\AdminLogs;
use app\admin\model\ShoukalaApi;

// 空控制器
class Error extends controller{
    // 空操作
    public function _empty($name){
        $this->error('此頁面不存在','index/index');
    }
}
