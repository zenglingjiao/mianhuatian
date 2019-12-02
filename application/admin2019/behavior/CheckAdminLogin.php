<?php
namespace app\admin\behavior;

use think\Controller;
use think\Container;
use app\admin\model\Login;

class CheckAdminLogin extends Controller
{
    private $notlogin = [
        ['c'=>'Index','a'=>'login'],
    ];
    private $notauth = [
        ['c'=>'Index','a'=>'index'],
        ['c'=>'Index','a'=>'loginout'],
        ['c'=>'Admin','a'=>'reset_pwd'],
        ['c'=>'Admin','a'=>'reset_auth'],
    ];

    private $uid,$userinfo;

    public function ActionBegin()
    {
        try{
            $login = new Login;
            $uid = $login->uid;
            
            // 功能路径数据
            $controller = $this->request->controller();
            $action = $this->request->action();

            //检查Action 是否需要登录
            $notlogin = false;
            if(!empty($this->notlogin)) {
                foreach($this->notlogin as $v) {
                    if(strcasecmp($v['c'],$controller)==0 && strcasecmp($v['a'],$action)==0) {
                        $notlogin = true;
                        break;
                    }
                }
            }
            $notauth = false;
            if(!empty($this->notauth)) {
                foreach($this->notauth as $v) {
                    if(strcasecmp($v['c'],$controller)==0 && strcasecmp($v['a'],$action)==0) {
                        $notauth = true;
                        break;
                    }
                }
            }

            if($uid<1 && !$notlogin){
                throw new \Exception('nologin');
            }elseif($uid>0 && $notlogin){
                throw new \Exception('login');
            }elseif($uid>0 && config('LOGIN_SINGLE')) {//单点登录
                if( cache('adminuser.'.$uid)!=session_id() ){
                    $login->loginout(2);
                    throw new \Exception('nologin');
                }
            }

            if($uid > 0) {
                //检查用户是否被删除
                if(!$login->checkUserDel($uid)){
                    throw new \Exception('change');
                }

                //检查用户权限
                if(!$notauth && !$login->checkAuth($controller, $action)){
                    throw new \Exception('noauth');
                }

                //加载menu
                $menu = $login->getMenu();
                $nowUrl = $login->getNow($controller, $action);

                $this->assign([
                    'uid' => $uid,
                    'userinfo' => $login->userinfo,
                    'authname' => $login->authname,

                    '_c' => $controller,
                    '_a' => $action,
                    
                    'allmenu'  => $login->allmenu,
                    'allauth'  => $login->allauth,

                    'menu'  => $menu,
                    'nowUrl'   => $nowUrl,
                ]);
            }
        }catch (\Exception $e) {
            $errmsg = $e->getMessage();
            switch ($errmsg) {
                case 'nologin':
                    $this->error('请先登录！',"admin/Index/login");
                    break;
                case 'login':
                    $this->error('已经登录！',"admin/Index/index");
                    break;
                case 'change':
                    $this->error('当前用户状态变化，请重新登录！',"admin/Index/login");
                    break;
                case 'noauth':
                    $this->error('对不起，您没有权限进行此操作！');
                    break;
                default:
                    $this->error($errmsg);
            }
        }
    }
}
