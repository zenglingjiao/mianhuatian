<?php
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;

class InfoCates extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_cate';
    protected $order = 'path asc,orders desc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'pid'    =>  'integer',
        'orders'     =>  'integer',
        'ist'     =>  'integer',
        
        'path'    =>  'string',
        'title'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];

    protected $validate_rule = [
        'id'         => 'number',
        'pid'    =>  'number',
        'orders'         => 'number',
        'ist'         => 'number',

        'title'     =>  'min:0|max:200',
        'path'     =>  'min:2|max:2000',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'tid' => [
            'key' => 'tid',
            'exp' => '=',
            'fun' => 'intval',
        ],
        'pid' => [
            'key' => 'pid|id',
            'exp' => '=',
            'fun' => 'intval',
        ],
    ];


    public $cate_arr;
    protected function initialize() {
        parent::initialize();

        $this->cate_arr = $this->getMap();
    }

    // 映射数组
    public function getMap($request=[]) {
        $request['field'] = 'id,path,title';
        $list = $this->getAll($request);
        $map = Container::get('basic')->array_under_reset($list,'id');

        $map['0']['title'] = '顶级';

        return $map;
    } 

    // 分类数据处理
    public function dealCate($request=[]) {
        $list = $this->getAll($request);

        $multi_arr = $this->nulti_in($list);
        // var_export($multi_arr);
        //die();

        $list_o=array();
        $this->nulti_out($list_o, $multi_arr);
        // var_dump($list_o);
        
        return $list_o;
    }
    public function nulti_in($list) {
        $multi_arr = array();
        foreach ($list as $k => $v) {
            $path = str_replace(',', ']["menu"][', substr($v['path'], 3, -1));

            if($path=='')   eval('$multi_arr['.$v['id'].'] = '.var_export($v,true).';');
            else            eval('$multi_arr['.$path.']["menu"]['.$v['id'].'] = '.var_export($v,true).';');
        }
        
        return $multi_arr;
    }
    public function nulti_out(&$list_o, $multi_arr, $level=0) {
        foreach ($multi_arr as $k => $v) {
            $menu = $v['menu']??'';
            unset($v['menu']);

            // 有数据
            if(isset($v['title'])){
                if($level>0)    $v['title'] = str_repeat("——", $level).$v['title'];
                $list_o[] = $v;
            }

            if(!empty($menu)){
                $this->nulti_out($list_o, $menu, $level+1);
            }     
        }
    }
}