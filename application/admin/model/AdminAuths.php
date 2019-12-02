<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;
use app\admin\model\AdminLogs;

class AdminAuths extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'admin_auth';
    protected $order = 'id desc';

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'title'     =>  'string',
        'desp'     =>  'string',
        'pre'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'id'    =>  'number',
        'title'     =>  'min:2|max:200',
        'desp'     =>  'min:0|max:200',
        'pre'     =>  'min:0|max:2000',
    ];

    public $limit = 10;
    
    // 获取管理员列表
    public function getMap($request=[]) {
        $list = $this->getAll($request);
        $map = Container::get('basic')->array_under_reset($list,'id');

        $map[-1] = ['id'=>-1,'title'=>'超级管理员'];

        return $map;
    }

    // 获取全部权限
    public function getAuthList() {
        include '../application/admin/menu.php';
        return $admin_menus;
    }

    // 获取列表--把权限解析为可读文字
    public function getAll($request=[]) {
        $admin_menus = $this->getAuthList();

        $list = parent::getAll($request);
        foreach ($list as $k => $v) {
            $pre = json_decode($v['pre'],true);

            $pre_text = '';
            foreach ($pre as $k2 => $v2) {
                if(!isset($admin_menus[$k2]))   continue;

                // 把权限解析为可读文字
                $pre_text .= $admin_menus[$k2]['title'].':';
                foreach ($v2 as $k3) {
                    if(isset($admin_menus[$k2]['menu'][$k3])){
                        $pre_text .= $admin_menus[$k2]['menu'][$k3]['title'].'  ';
                    }else{
                        foreach ($admin_menus[$k2]['menu'] as $arr => $vo) {
                            if(isset($vo['menu'][$k3])){
                                $pre_text .= $vo['menu'][$k3]['title'].'  ';

                                break;
                            }
                        }
                    }
                }
                $pre_text .= '<br>';
            }

            $list[$k]['pre_text'] = $pre_text;
        }

        return $list;
    }

    // 处理权限编辑上传的数组数据
    public function dealAuths($arr=[]) {
        // $admin_menus = $this->getAuthList();

        foreach ($arr as $k => $v) {
            $arr[$k] = array_keys($v);
        }

        return json_encode($arr);
    }

}