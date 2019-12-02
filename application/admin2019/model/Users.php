<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;

class Users extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_user';
    protected $order = 'id desc';

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'cardno'     =>  'string',
        'phone'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'cardno'     =>  'min:0|max:7',
        'phone'     =>  'require|min:10|max:10',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'id' => [
            'key' => 'id',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'key' => [
            'key' => 'cardno|phone',
            'exp' => 'like',
            'fun' => 'fun_like',
        ],
        'key2' => [
            'key' => 'cardno|phone',
            'exp' => '=',
            'fun' => 'strval',
        ],
        
        'cardno' => [
            'key' => 'cardno',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'phone' => [
            'key' => 'phone',
            'exp' => '=',
            'fun' => 'strval',
        ],
    ];
    
    public $limit = 10;

    public function getF($uid,$field='cardno')
    {
        $user = (new Users)->getOne(['id'=>$uid]);

        if(!$user)  return '';
        else        return $user['cardno'];
    }
}