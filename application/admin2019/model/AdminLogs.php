<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;
use app\admin\model\AdminUsers;

class AdminLogs extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'admin_log';
    protected $order = 'id desc';

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'type'     =>  'integer',
        'uid'     =>  'integer',
        'remark'     =>  'string',

        'add_ip'  =>  'ip',
        'add_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'type'         => 'number',
        'uid'         => 'number',
        'remark'     =>  'min:0|max:200',
    ];

    // 参数搜索
    protected $request_search_rules = [
        'type' => [
            'key' => 'type',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'uid' => [
            'key' => 'uid',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'datetime' => [
            'key' => 'add_time',
            'exp' => 'between',
        ],
    ];

    public $limit = 10;

    //自定义初始化
    public $type_arr = [0 => '其他日志',1 => '权限操作日志',2 => '登录日志',3 => '退出日志'];
    protected function initialize() {
        parent::initialize();

        $this->admin_arr = (new AdminUsers)->getMap();
    }
    
    //添加操作日志 types 0其他日志，1登录日志，2登录日志，3权限操作日志
    public function addLog($type=0,$remark='后台登录',$uid=false){
        $data = [];
        $data['type'] = $type;
        $data['uid'] = $uid;
        $data['remark'] = $remark;
        
        $data['add_ip'] = request()->ip();

        $do = Db::table($this->table)->insert($data);
        return $do!==false;
    }
}