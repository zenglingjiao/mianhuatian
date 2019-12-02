<?php
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use app\admin\model\BaseModel;

class Citys extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'web_city';
    protected $order = 'code_p asc,code_c asc,code_a asc';

    public $limit = 10;

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'code_p'     =>  'string',
        'code_c'     =>  'string',
        'code_a'     =>  'string',
        'name'     =>  'string',
    ];

    protected $validate_rule = [
        'id'         => 'number',
        'code_p'     =>  'min:0|max:6',
        'code_c'     =>  'min:0|max:6',
        'code_a'     =>  'min:0|max:6',
        'name'     =>  'min:0|max:50',
    ];
    // 参数搜索
    protected $request_search_rules = [
        'code_p' => [
            'key' => 'code_p',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'code_c' => [
            'key' => 'code_c',
            'exp' => '=',
            'fun' => 'strval',
        ],
        'code_a' => [
            'key' => 'code_a',
            'exp' => '=',
            'fun' => 'strval',
        ],
        
        'code_c_n' => [
            'key' => 'code_c',
            'exp' => '<>',
            'fun' => 'strval',
        ],
        'code_a_n' => [
            'key' => 'code_a',
            'exp' => '<>',
            'fun' => 'strval',
        ],
    ];

    public $cate_arr;
    protected function initialize() {
        parent::initialize();

        $this->cate_arr = $this->getMap();
    }

    // 映射数组
    public function getMap($request=[],$edit=true) {
        $request['f'] = (isset($request['f']) && in_array($request['f'], ['p','c','a']))?$request['f']:'p';
        if($request['f']=='p')    $request['code_c'] = '000000';
        $field = 'code_'.$request['f'];

        if($edit==true){
            $request['field'] = $field.' as code,id,name';
            $list = $this->getAll($request);
            $map = array_reset($list,'code');
        }else{
            $request['field'] = $field.' as code,name';
            $list = $this->getAll($request);
            $map = array_reset($list,'code','name');
        }

        return $map;
    } 

    // 获取地址
    public function getCity($code) {
        if(strlen($code)!=6 || !is_numeric($code))  return false;

        $param = [];
        if(substr($code, 2, 2)=='00'){//省
            $param['code_p'] = $code;
            $param['code_c'] = '000000';
        }elseif(substr($code, 4, 2)=='00'){//市
            $param['code_c'] = $code;
            $param['code_a'] = '000000';
        }else{//区
            $param['code_a'] = $code;
        }
        $info = $this->get($param);

        return $info['name'];
    }
    public function getCityAll($code) {
        if(strlen($code)!=6 || !is_numeric($code))  return false;

        $name = '';
        if(substr($code, 2, 2)=='00'){//省
            $name .= $this->getCity(substr($code, 0, 2).'0000');
        }elseif(substr($code, 4, 2)=='00'){//市
            $name .= $this->getCity(substr($code, 0, 2).'0000');
            $name .= $this->getCity(substr($code, 0, 4).'00');
        }else{//区
            $name .= $this->getCity(substr($code, 0, 2).'0000');
            $name .= $this->getCity(substr($code, 0, 4).'00');
            $name .= $this->getCity($code);
        }

        return $name;
    }
}