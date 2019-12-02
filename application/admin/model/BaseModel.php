<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use think\Db;

class BaseModel extends Model
{
    protected $field = '*';
    public $limit = 10;

    // 默认搜索
    protected $default_search_rules = [];
    // 参数搜索
    protected $request_search_rules = [
        // 日期搜索
        /*'datetime' => [
            'key' => 'Add_Date',
            'exp' => 'between',
        ],*/
        // 自定义方法
        /*'Title' => [
            'key' => 'Title',
            'exp' => 'like',
            'fun' => 'fun_like',
        ],*/
        // 自定义查询
        /*'NotCode' => [
            'key' => 'CityPostCode',
            'exp' => 'exp',
            'fun' => 'strval',
        ],*/
    ];

    // 初始化
    protected function initialize() {
        parent::initialize();
        return $this;
    }

    // 设置参数
    public function set($data, $value = null) {
        if (is_string($data)) {
            $this->$data = $value;
        } else {
            if (is_object($data)) {
                $data = $data->toArray();
            }

            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    $this->$k = $v;
                }
            }
        }

        return $this;
    }

    // 获取一条记录
    public function getOne($data=[]) {
        $request = $this->dealSelectData($data);
        $selectW = $this->getSelectWhere($request);
        // var_dump($selectW);

        $info = $this->field($selectW['field'])->where($selectW['where'])->order($selectW['order'])->find();
        // var_dump($this::getlastsql());
        // var_dump($info);

        return $info;
    }

    //获取数量
    public function getCount($data=[]) {
        $request = $this->dealSelectData($data);
        $selectW = $this->getSelectWhere($request);
        // var_dump($selectW);
        
        if(!isset($selectW['count']))   $selectW['count'] = $this->pk;

        $count = Db::table($this->table)->where($selectW['where'])->count($selectW['count']);
        // var_dump($this::getlastsql());
        // var_dump($count);
        
        return $count;
    }
    
    //全部
    public function getGroup($data=[]) {
        if(empty($data['limit']))   $data['limit'] = 0;
        
        $request = $this->dealSelectData($data);
        $selectW = $this->getSelectWhere($request);
        // var_dump($selectW);

        $list = Db::table($this->table)->field($selectW['field'])->where($selectW['where'])->group($selectW['group'])->limit($selectW['limit'])->select();
        // var_dump($this::getlastsql());
        // var_dump($list);

        return $list;
    }
    
    //全部
    public function getAll($data=[]) {
        if(empty($data['limit']))   $data['limit'] = 0;
        
        $request = $this->dealSelectData($data);
        $selectW = $this->getSelectWhere($request);
        // var_dump($selectW);

        $list = Db::table($this->table)->field($selectW['field'])->where($selectW['where'])->order($selectW['order'])->limit($selectW['limit'])->select();
        // var_dump($this::getlastsql());
        // var_dump($list);

        return $list;
    }
    //分页列表
    public function getList($data=[]) {
        $request = $this->dealSelectData($data);
        $selectW = $this->getSelectWhere($request);
        // var_dump($selectW);

        $count = Db::table($this->table)->where($selectW['where'])->count();
        $list = Db::table($this->table)->field($selectW['field'])->where($selectW['where'])->order($selectW['order'])->paginate($selectW['limit'], $count, ['query'=>$data]);
        // var_dump($this::getlastsql());
        // var_dump($count);
        
        return $list;
    }

    // 数据校验
    public function validate($data,$rule='validate_rule') {
        $validate = new Validate($this->$rule);
        $result = $validate->check($data);

        if($result!==true)      return $validate->getError();
        else                    return true;
    }

    // 处理请求数据
    public function dealSelectData($request) {
        // 分页
        $request['limit'] = (isset($request['limit'])&&strval($request['limit'])!='') ? (int)$request['limit'] : (int)$this->limit;
        $request['page'] = (isset($request['page'])&&$request['page']!='') ? (int)$request['page'] : 1;
        $this->limit = $request['limit'];

        // 排序
        $request['field'] = (isset($request['field'])&&strval($request['field'])!='') ? $request['field'] : $this->field;
        $request['order'] = (isset($request['order'])&&$request['order']!='') ? trim($request['order']) : ( isset($this->order) ? $this->order : ( isset($this->pk) ? $this->pk.' desc' : 'id desc' ) );

        // 参数格式化
        $request_rules = $this->request_search_rules;
        if(count($request_rules)>0) {
            foreach ($request_rules as $key=>$value) {
                if( isset($request[$key])&&$request[$key]!='' ){
                    $fun = $value['fun'];

                    if(function_exists($fun))          $request[$key] = $fun($request[$key]);
                    elseif(method_exists($this,$fun))  $request[$key] = $this->$fun($request[$key]);
                    else                               $request[$key] = strval($request[$key]);
                }
            }
        }

        // 时间区间搜索处理
        if(!empty($request['starttime']) && !empty($request['endtime'])){
            $request['datetime'] = [date('Y-m-d',strtotime($request['starttime'])).' 00:00:00', date('Y-m-d',strtotime($request['endtime'])).' 23:59:59'];
            $request['date'] = [date('Y-m-d',strtotime($request['starttime'])), date('Y-m-d',strtotime($request['endtime']))];
        }

        return $request;
    }

    /*组合参数返回查询数组
    1.$request $this->request->param()控制器传这个就行 $data 是过滤和组装的数组
    2.返回的数组，limit 是查询的条数，order 是排序方式 where 是查询条件
    3.条数一定要是limit 默认15,排序字段名一定要是order 排序方式方式一定要是ordertype 默认order是id ordertype 是DESC 组合就是 id desc,其他都是条件和组参的字段
     * */
    public function getSelectWhere($request) {
        $default_rules = $this->default_search_rules;
        $request_rules = $this->request_search_rules;
        // var_dump($default_rules,$request_rules,$request);

        // 搜索项
        $where = $default_rules;
        if(count($request_rules)>0) {
            foreach ($request_rules as $key=>$value) {
                if( isset($request[$key])&&$request[$key]!=='' ){
                    if($value['exp']!='exp')     $where[$value['key']] = [$value['key'], $value['exp'], $request[$key]];
                    else        $where[$value['key'].' '.$request[$key]] = ['', $value['exp'], $value['key'].' '.$request[$key]];
                }
            }
        }
        $request['where'] = $where;

        return $request;
    }

    // 搜索时 自定义方法
    // like 模糊搜索
    public function fun_like($data='') {
        return '%'.trim(strval($data)).'%';
    }
    // 时间区间搜索
    public function fun_datetime($date='') {
        $time = strtotime($date);
        if(!$time)  $time = strtotime('today');

        return [date('Y-m-d',$time),date('Y-m-d',$time+3600*24)];
    }
    public function fun_timestamp($date='') {
        $time = strtotime($date);
        if(!$time)  $time = strtotime('today');

        return [$time,$time+3600*24];
    }
}