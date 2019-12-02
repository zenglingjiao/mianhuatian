<?php
/*
 * 1.管理栏目
 * 最多三级，1级key代表 控制器名称 比如 Rbac,如果只有2级，1级key下面的menu下的key就代表控制器方法，如果三级，二级key下面的menu下的menu的key才是对应的控制器方法
 *
 * 2.数组解析
 * title 栏目标题
 * inco 栏目图标,二级栏目 inco 默认是fa-circle 三级栏目默认是fa-circle-o 所有图标网址 /admin/index/tubiao.html
 * pre 代表是否加入权限控制，除后台首页外，其他默认都加入，如果一级pre为false，二级pre设置为true失效，一级为true 二级为false有效 三级同样
 * isshow 隐形的权限判断，比如删除或者AJAX进行某些操作，或者其他权限判断，不会显示在左边控制栏，但是会判断权限
 * isshow 只有最后一级才起作用，比如2级，就只能加到二级栏目里，如果是三级，只有加到三级栏目才起作用
 *
 * 3.修改权限
 * 默认和添加权限等同，有添加权限，即有修改权限。如果要分开，请把修改和添加分开2个控制器方法
 * */

$admin_menus = [
    //分类管理 三级管理栏目示例
    'Info' => [
        'title' => '内容管理',
        'inco'  => 'fa-book',
        'pre'   => true,
        'menu'  => [
            'userList' => ['title'=>'會員管理', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'userInvoiceList' => ['title'=>'會員發票列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'userCouponList' => ['title'=>'會員優惠劵列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],

            'invoiceList' => ['title'=>'錯誤發票列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            
            'typeAll' => ['title'=>'獎項類別列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'typeEdit' =>  ['title'=>'添加獎項類別', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'typeDel' =>  ['title'=>'删除獎項類別', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'typePro' =>  ['title'=>'機率設定', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            
            'awardList' => ['title'=>'獎項序號管理', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'invoiceQuery' => ['title'=>'會員發票查詢', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'errorinvoiceQuery' => ['title'=>'未校驗錯誤發票列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            //'uncheckinvoiceQuery' => ['title'=>'未校驗發票列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'awardAdd' => ['title'=>'新增獎項', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'awardDel' =>  ['title'=>'獎項序號删除', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'awardImport' =>  ['title'=>'匯入', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'awardExport' =>  ['title'=>'匯出', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
        ]
    ],
    /*//权限组 
    'Admin' => [
        'title' => '权限管理',
        'inco'  => 'fa-user-plus',
        'pre'   => true,
        'menu'  =>[
            'userAll' => ['title'=>'管理员列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'userEdit' =>  ['title'=>'管理员编辑', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'userDel' =>  ['title'=>'管理员删除', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
        
            'authAll' => ['title'=>'权限组列表', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>true],
            'authEdit' =>  ['title'=>'权限组编辑', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
            'authDel' =>  ['title'=>'权限组删除', 'inco'=>'fa-circle-o', 'pre'=>true, 'isshow'=>false],
        ]
    ],*/
];

?>