<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;

class InfoAwards extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_coupon';
    protected $order = 'id desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',

        'no'     =>  'string',
        'title'     =>  'string',

        'type_id'     =>  'integer',
        'type_name'     =>  'string',

        'uid'     =>  'integer',
        'status'     =>  'integer',

        'get_time'    =>  'string',
        'use_time'    =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'no'     =>  'min:0|max:20',
        'title'     =>  'min:0|max:20',

        'type_id'         => 'number',
        'type_name'     =>  'min:0|max:20',

        'uid'         => 'number',
        'status'         => 'number',

        'get_time'     =>  'min:0|max:20',
        'use_time'     =>  'min:0|max:20',
    ];
    // 参数搜索
    protected $request_search_rules = [
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
        'status' => [
            'key' => 'status',
            'exp' => '=',
            'fun' => 'strval',
        ],
    ];

    public $status_arr = ['0'=>'未兌換','1'=>'已兌換','2'=>'已使用'];
}