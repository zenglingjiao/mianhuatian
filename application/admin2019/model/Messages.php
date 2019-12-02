<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\BaseModel;

class Messages extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'web_message';
    protected $order = 'id desc';

    protected $readonly = ['id','username'];
    protected $type = [
        'id'    =>  'integer',
        'username'     =>  'string',
        'content'     =>  'string',
        'reply'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'id'            => 'number',
        'username'      =>  'require',
        'content'       =>  'min:0|max:2000',
        'reply'         =>  'min:0|max:2000',
    ];

    // 默认搜索
    protected $default_search_rules = [
        // 'status' => ['status','=', 1],
    ];
    // 参数搜索
    protected $request_search_rules = [
    ];

    public $limit = 10;
    
}