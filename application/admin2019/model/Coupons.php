<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;

class Coupons extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_coupon';
    protected $order = 'get_time desc,id desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',

        'no'     =>  'string',
        'title'     =>  'string',

        'type_id'     =>  'integer',
        'type_name'     =>  'string',
        'type_img'     =>  'string',

        'uid'     =>  'integer',
        'in_id'     =>  'integer',
        'status'     =>  'integer',

        'get_time'    =>  'string',
        'use_time'    =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'no|序號'     =>  'require|min:12|max:12',
        'title|獎項名稱'     =>  'require|min:0|max:20',
        'type_id'         => 'require|number',
        'type_name'     =>  'require|min:0|max:20',
        'type_img'     =>  'require|min:0|max:200',

        'uid'         => 'number',
        'in_id'         => 'number',
        'status'         => 'number',

        'get_time'     =>  'min:0|max:20',
        'use_time'     =>  'min:0|max:20',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'id' => [
            'key' => 'id',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'key' => [
            'key' => 'no|title',
            'exp' => 'like',
            'fun' => 'fun_like',
        ],
        'type_id' => [
            'key' => 'type_id',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'uid' => [
            'key' => 'uid',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'in_id' => [
            'key' => 'in_id',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'status' => [
            'key' => 'status',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'status_no' => [
            'key' => 'status',
            'exp' => '<>',
            'fun' => 'strval',
        ],
    ];

    public $status_arr = ['0'=>'未領取','1'=>'已領取','2'=>'已使用','3'=>'發票錯誤'];
}