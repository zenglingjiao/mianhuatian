<?php

namespace app\admin\model;
use think\Container;
use think\Model;
use app\admin\model\AdminUsers;
use app\admin\model\AdminAuths;
use app\admin\model\AdminLogs;

//管理員登入+狀態操作類
class Login extends Model
{
    //key 0,1,2分別代表SESSION UID，SESSION INFO ,CACHE 管理員賬號狀態更改 ，權限列表
    private $sessionkey = [
        '_admin_login_uid_',
        '_admin_login_userinfo_',
        '_change_auth_user_id_',
        '_all_auth_list_cache_key_',
    ];
    
    public $uid,$userinfo, $authlist,$authname,$allauth, $allmenu,$menu;
    private $active, $open, $java;

    // 初始化
    public function __construct() {
        parent::__construct();

        // 樣式
        $this->active = 'active';
        $this->open = 'menu-open';
        $this->java = 'javascript:void(0);';

        try{
            $this->uid = (int)session($this->sessionkey[0]);
            $this->userinfo = session($this->sessionkey[1]);

            // 權限獲取
            // $this->userinfo['pre'] = 10;//測試
            // var_dump($this->userinfo['pre']);die();
            if($this->userinfo['pre']>0){
                $auth = $this->getAuth($this->userinfo['pre']);
                $this->authlist = json_decode($auth['pre'],true);
                $this->authname = $auth['title'];
            }else{
                $this->authlist = false;
                $this->authname = '超級管理員';
            }

            return $this;
        }catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    //檢查是否登入
    public function checkLogin() {
        return boolval($this->uid > 0);
    }

    //登入操作
    public function setLogin($data) {
        try{
            if($this->uid > 0){
                throw new \Exception('您已登入！');
            }

            $info = (new AdminUsers)->getOne(['username'=>$data['username']]);
            if(!$info){
                throw new \Exception('用護名不存在！');
            }                        

            if($info['pass'] != $data['pass']){
                throw new \Exception('密碼錯誤！');
            }

            // 用戶主表登入時間更新
            $res = (new AdminUsers)->updateUserLogin($info);
            if(!$res) throw new \Exception('更新用戶主表信息失敗！');

            // 添加日誌
            $res = (new AdminLogs)->addLog(2,'後臺登入', $info['id']);
            if(!$res) throw new \Exception('寫入用戶登入日誌失敗！');

            $this->uid = (int)$info['id'];
            $this->userinfo = $info;

            session($this->sessionkey[0],$info['id']);
            session($this->sessionkey[1],$info);

            if(config('LOGIN_SINGLE')) {//單點登入
                cache('adminuser.'.$this->uid,session_id());
            }
                
            return return_success('登入成功！');
        }catch (\Exception $e) {
            return return_error($e->getMessage());
        }
    }

    //退出登入
    public function loginout($type=1) {
        try{
            $res = (new AdminLogs)->addLog(3,($type==1)?'退出登入':'多點登入,舊狀態取消', $this->uid);
            if($res) {
                $this->uid = null;
                $this->userinfo = null;

                session($this->sessionkey[0],null);
                session($this->sessionkey[1],null);
            } else {
                throw new \Exception('退出日誌寫入失敗！');
            }

            return true;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    //檢查用戶是否被刪除
    public function checkUserDel($uid) {
        // 獲取刪除的用戶緩存
        $cache = Container::get('cache');
        $delarr = $cache->get($this->sessionkey['2']);
        
        // 檢查
        $r = true;
        if(!empty($delarr) && in_array($uid, $delarr)) {
            $this->loginout();
            $r = false;
        }

        return $r;
    }

    //重置用戶登入信息
    public function setUserSession($parm,$value) {
        $this->userinfo[$parm] = $value;
        session($this->sessionkey[1], $this->userinfo);
    }


    //獲取用戶的權限
    public function checkAuth($controller, $action) {
        if(!$this->authlist)    return true;//超級管理員

        // 權限判定
        $auth = false;
        if(array_key_exists($controller, $this->authlist)) {
            foreach ($this->authlist[$controller] as $action_name) {
                if(strcasecmp($action_name,$action) == 0) {
                    $auth = true;
                    break;
                }
            }
        }
        return $auth;
    }

    //獲取權限組所有緩存
    public function getAuth($pre) {
        // 緩存
        $list = $this->getAuthList();

        if(!isset($list[$pre])){
            throw new \Exception('用戶權限數據錯誤！');
        }

        return $list[$pre];
    }
    //獲取權限組所有緩存
    public function getAuthList() {
        // 緩存
        $list = cache($this->sessionkey['3']);
        if(!$list) {
            // 數據庫
            $list = (new AdminAuths)->getMap();
            cache($this->sessionkey['3'], $list);
        }

        $this->allauth = $list;
        return $list;
    }
    //清楚權限組所有緩存
    public function resetAuthList() {
        cache($this->sessionkey['3'], null);
    }

    //根據權限獲取對應左邊欄目
    public function getMenu() {
        // 權限總表
        include '../application/admin/menu.php';
        $this->allmenu = $admin_menus;
        $menu = $this->allmenu;

        $authlist = $this->authlist;

        foreach ($menu as $k=>$v) {
            if(!empty($v['menu']) && (!$this->authlist || array_key_exists($k, $authlist))) {//判斷用戶是否擁有這個大欄目權限
                foreach($v['menu'] as $key=>$value) {
                    if(!empty($value['menu'])) {// 3級菜單
                        foreach ($value['menu'] as $ky=>$val) {
                            $this->doMenuShow($menu, $k, $key, $val, $authlist, $ky);
                        }
                    } else {// 2級菜單
                        $this->doMenuShow($menu, $k, $key, $value, $authlist);
                    }
                }
            } else {
                unset($menu[$k]);
            }
        }
        $this->menu = $menu;
        return $menu;
    }

    //處理權限欄目 $ykey 壹級key,ekey二級K，value,當前處理數據，skey三級K,authlist 用戶當前權限，超級管理員是true，其他數組
    private function doMenuShow (&$menu, $ykey, $ekey, $value, $authlist, $skey=false) {
        $isdel = false;
        //先幹掉不顯示的欄目
        if($value['isshow'] === false) {
            if($skey === false) {
                unset($menu[$ykey]['menu'][$ekey]);
            } else {
                unset($menu[$ykey]['menu'][$ekey]['menu'][$skey]);
            }
            $isdel = true;
        }

        //在幹掉沒在用戶權限裏的欄目
        if($authlist) {
            if($skey === false) {
                if(!in_array($ekey, $authlist[$ykey])) {
                    unset($menu[$ykey]['menu'][$ekey]);
                    $isdel = true;
                }
            } else {
                if(!in_array($skey, $authlist[$ykey])) {
                    unset($menu[$ykey]['menu'][$ekey]['menu'][$skey]);
                    $isdel = true;
                }
            }
        }

        //判斷二級或者三級是否為空了，為空幹掉上級
        if($isdel === true) {
            if($skey === false) {
                if(empty($menu[$ykey]['menu'])) unset($menu[$ykey]);
            } else {
                if(empty($menu[$ykey]['menu'][$ekey]['menu'])) unset($menu[$ykey]['menu'][$ekey]);
            }
        } else {
            //加載URL
            if($skey === false) {
                $menu[$ykey]['menu'][$ekey]['url'] = url($ykey.'/'.$ekey);
            } else {
                $menu[$ykey]['menu'][$ekey]['menu'][$skey]['url'] = url($ykey.'/'.$skey);
            }
        }
    }

    //獲取當前路徑
    public function getNow($controller, $action) {
        $menu = $this->menu;

        $weizhi = [];
        if(array_key_exists($controller, $menu)) {
            $one = [];
            $one['class'] = '';
            $one['url'] = "javascript:void(0);";
            $one['title'] = $menu[$controller]['title'];
            $weizhi[] = $one;
        } else {
            return $weizhi;
        }

        foreach ($menu[$controller]['menu'] as $k=>$v) {
            if(!empty($v['menu'])) {//3級
                foreach($v['menu'] as $key=>$value) {
                    $this->checkPos($action, $key, $value, $weizhi);
                }
            } else {//2級
                $this->checkPos($action, $k, $v, $weizhi);
            }
        }
        return $weizhi;
    }

    //組合當前路徑
    private function checkPos($action,  $key, $value, &$weizhi) {
        if($action == strtolower($key)) {
            $one = [];
            $one['class'] = '';
            $one['url'] = "javascript:void(0);";
            $one['title'] = $value['title'];
            $weizhi[] = $one;
        }
    }

}