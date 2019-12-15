<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;

class Invoices extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_invoice';
    protected $order = 'id desc';
    protected $updateTime = 'update_time';
    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'uid'    =>  'integer',

        'no1'     =>  'string',
        'no2'     =>  'string',
        'date'     =>  'string',
        'code'     =>  'string',
        'money'     =>  'string',

        'status'     =>  'integer',
        'msg'     =>  'string',


        'total_times'    =>  'integer',
        'remain_times'    =>  'integer',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'no1'     =>  'require|min:1|max:20',
        'no2'     =>  'require|min:1|max:20',
        'date'     =>  'require|min:1|max:20',
        'code'     =>  'min:0|max:20',
        'money'     =>  'require|min:1|max:20',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'id' => [
            'key' => 'id',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'uid' => [
            'key' => 'uid',
            'exp' => '=',
            'fun' => 'intval',
        ],

        'no1' => [
            'key' => 'no1',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'no2' => [
            'key' => 'no2',
            'exp' => '=',
            'fun' => 'strval',
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
    
    public $limit = 10;
    public $status_arr = ['0'=>'未校驗','1'=>'校驗成功','2'=>'校驗失敗'];
}