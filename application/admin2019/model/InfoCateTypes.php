<?php
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;

class InfoCateTypes extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_cate_type';
    protected $order = 'id desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'title'     =>  'string',
        'desc'     =>  'string',
        'used'     =>  'string',
        'img'     =>  'string',   
        'pro'     =>  'float',   

        'status'    =>  'integer',
        'time_limit'    =>  'integer',
        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];

    protected $validate_rule = [
        'pro'         => 'number',
        'status'         => 'number',
        'time_limit'         => 'number',

        'title|獎項類別'     =>  'require|min:0|max:200',
        'img'     =>  'min:0|max:200',
        'desc'     =>  'min:0|max:2000',
        'used'     =>  'min:0|max:2000',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'id' => [
            'key' => 'id',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'key' => [
            'key' => 'title',
            'exp' => 'like',
            'fun' => 'fun_like',
        ],
        'status' => [
            'key' => 'status',
            'exp' => '=',
            'fun' => 'intval',
        ],
    ];

    public $time_limit_arr = ['1'=>'24小時后可用','2'=>'立即可用'];

    public $cate_arr;
    protected function initialize() {
        parent::initialize();

        $this->cate_arr = $this->getMap();
    }

    // 映射数组
    public function getMap($request=[]) {
        $request['field'] = 'id,title';
        $request['status'] = '1';
        $list = $this->getAll($request);
        $map = array_reset($list,'id','title');

        return $map;
    } 
    public function getMap2($request=[]) {
        $request['field'] = 'id,title,pro,img,desc,used,time_limit';
        $list = $this->getAll($request);
        $map = array_reset($list,'id');

        return $map;
    } 
}