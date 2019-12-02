<?php
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;

class Seos extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'web_seo';
    protected $order = 'id desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'title'     =>  'string',
        'keywords'     =>  'string',
        'description'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];

    protected $validate_rule = [
        'id'         => 'number',
        'title'     =>  'min:0|max:200',
        'keywords'     =>  'min:0|max:2000',
        'description'     =>  'min:0|max:2000',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'title' => [
            'key' => 'title',
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
        $request['field'] = 'title,keywords,keywords';
        $list = $this->getAll($request);
        $map = array_reset($list,'title');

        return $map;
    } 
}