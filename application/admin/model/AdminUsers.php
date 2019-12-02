<?php
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;
use app\admin\model\AdminAuths;

class AdminUsers extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'admin_user';
    protected $order = 'id desc';

    protected $readonly = ['id','username'];
    protected $type = [
        'id'    =>  'integer',
        'nickname'     =>  'string',
        'username'     =>  'string',
        'pass'     =>  'string',
        
        'pre'     =>  'integer',
        'skin'     =>  'string',
    ];
    protected $validate_rule = [
        'id'    =>  'number',
        'nickname'     =>  'min:2|max:200',
        'username'     =>  'min:2|max:200',
        'pass'     =>  'min:32|max:32',
        
        'pre'     =>  'number',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'username' => [
            'key' => 'username',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'pre' => [
            'key' => 'pre',
            'exp' => '=',
            'fun' => 'intval',
        ],
    ];

    public $limit = 10;

    public $auth_arr;
    protected function initialize() {
        parent::initialize();

        $this->auth_arr = (new AdminAuths)->getMap();
    }

    //更新用户登录操作
    public function updateUserLogin($info) {
        $up = $where = [];
        $where['id'] = $info['id'];

        if(empty($info['logintime'])) {
            $up['lastlogintime'] = $up['logintime'] = date('Y-m-d H:i:s');
            $up['lastloginip'] = $up['loginip'] = request()->ip();
        } else {
            $up['lastlogintime'] = $info['logintime'];
            $up['lastloginip'] = $info['lastloginip'];

            $up['logintime'] = date('Y-m-d H:i:s');
            $up['loginip'] = request()->ip();
        }

        $do = Db::table($this->table)->where($where)->update($up);
        return $do!==false;
    }

    // 获取管理员列表
    public function getMap($request=[]) {
        $request['field'] = 'id,nickname,username';
        $list = $this->getAll($request);
        $map = Container::get('basic')->array_under_reset($list,'id');

        return $map;
    }    
}