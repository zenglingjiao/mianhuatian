<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;

class InfoArticles extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_article';
    protected $order = 'id desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'title'     =>  'string',
        'stitle'     =>  'string',

        'cid'     =>  'integer',
        'orders'     =>  'integer',
        'ist'     =>  'integer',
        
        'img'    =>  'string',
        'desc'    =>  'string',
        'ms'    =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];

    protected $validate_rule = [
        'id'         => 'number',
        'cid'         => 'number',
        'orders'         => 'number',
        'ist'         => 'number',

        'title'     =>  'min:0|max:200',
        'stitle'     =>  'min:0|max:200',
        'img'     =>  'min:0|max:2000',
        'desc'     =>  'min:0|max:2000',
        'ms'     =>  'min:0|max:200000',

        'add_time'  =>  'require|dateFormat:Y-m-d H:i:s',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'pid' => [
            'key' => 'cid',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'cid' => [
            'key' => 'cid',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'cids' => [
            'key' => 'cid',
            'exp' => 'in',
            'fun' => 'strval',
        ],
        'id' => [
            'key' => 'id',
            'exp' => '=',
            'fun' => 'intval',
        ],
    ];
}