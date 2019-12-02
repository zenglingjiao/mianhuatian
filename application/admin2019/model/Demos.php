<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\BaseModel;

class Demos extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'demo';
    protected $order = 'id desc';

    protected $readonly = ['id','key'];
    protected $type = [
        'id'    =>  'integer',
        'key'     =>  'string',
        'value'     =>  'string',
        'desp'     =>  'string',

        'status'    =>  'integer',
        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'id'         => 'number',
        'key|KK'     =>  'require',
        'value'     =>  'min:0|max:200',
        'desp'     =>  'min:0|max:2000',

        'status'    =>  'number',
        'add_time'  =>  'require|dateFormat:Y-m-d H:i:s',
    ];

    // 默认搜索
    protected $default_search_rules = [
        'id' => ['id','>', 1],
        // 'status' => ['status','=', 1],
    ];
    // 参数搜索
    protected $request_search_rules = [
        'status' => [
            'key' => 'status',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'status2' => [
            'key' => 'status',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'key' => [
            'key' => 'key',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'datetime' => [
            'key' => 'add_time',
            'exp' => 'between',
        ],
    ];



    public $limit = 10;
    public $status_arr = ['0'=>'0000','1'=>'正常','2'=>'待审核','3'=>'3333','4'=>'4444'];
    
    // 修改器
    /*public function setAddTimeAttr($value) {
        $time = strtotime($value);
        return $time>0?date('Y-m-d H:i:s',$time):date('Y-m-d H:i:s');
    }*/
}