<?PHP
namespace app\admin\model;
use think\Container;
use think\Model;
use think\Db;
use think\facade\Cache;
use app\admin\model\BaseModel;

class Systems extends BaseModel
{
    protected $table = 'web_system';
    protected $pk = 'key';
    protected $order = 'key asc';

    protected $readonly = ['key'];
    protected $type = [
        'key'     =>  'string',
        'value'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'key'     =>  'min:0|max:200',
        'value'     =>  'min:0|max:10000',
    ];
    public $limit = 0;

    static $_table = 'web_system';
    public static function getParam($ids='') {
        $info = Cache::get(self::$_table);
        if(!$info){
            $list = Db::table(self::$_table)->select();

            $info = [];
            foreach ($list as $k => $v) {
                $info[$v['key']] = $v['value'];
            }
            Cache::set(self::$_table,$info);
        }

        if(empty($ids)){
            return $info;
        }elseif(is_string($ids)){
            return $info[$ids]??'';
        }elseif(is_array($ids)){
            $return = [];
            foreach ($ids as $id) {
                if(isset($info[$id]))    $return[$id] = $info[$id];
            }

            return $return;
        }else{
            return '';
        }
    }
    public static function removeCache() {
        Cache::set(self::$_table,null);
        
        return true;
    }
}

