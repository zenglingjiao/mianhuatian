<?php
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;

class Jumps extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'web_nav';
    protected $order = 'id desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'show_url'     =>  'string',
        'after_url'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];

    protected $validate_rule = [
        'id'         => 'number',
        'show_url'     =>  'min:0|max:200',
        'after_url'     =>  'min:0|max:200',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'show_url' => [
            'key' => 'show_url',
            'exp' => '=',
            'fun' => 'strval',
        ],
    ];

    public $cate_arr;
    protected function initialize() {
        parent::initialize();

        $this->cate_arr = $this->getMap();
    }

    // 映射数组
    public function getMap($request=[]) {
        $request['field'] = 'show_url,after_url';
        $list = $this->getAll($request);
        $map = array_reset($list,'show_url','after_url');

        return $map;
    } 
}