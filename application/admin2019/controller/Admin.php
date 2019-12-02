<?php
namespace app\admin\controller;
use think\Controller;
use think\Container;
use think\facade\Session;
use app\admin\model\AdminUsers;
use app\admin\model\AdminAuths;
use app\admin\model\Login;

class Admin extends controller
{
    //全部
    public function userAll() {
        $model = new AdminUsers;
        $param = $this->request->param();
        $list = $model->getAll($param);

        $this->assign('list', $list);
        $this->assign('model', $model);
        return $this->fetch();
    }
    //編輯
    public function userEdit() {
        $id = $this->request->param('id',0,'intval');

        // 編輯數據
        if($id>0){
            $info = AdminUsers::get($id);
            if(!$info)     $this->error('數據不存在！');
        }else{
            $info = new AdminUsers;
        }
        $this->assign(['info'=>$info]);
        // var_dump($info);

        // 上傳數據
        if($this->request->isPost()) {
            $data = $this->request->only(['nickname','username','pass','rpass','pre']);

            // 二次密碼校驗
            if($data['pass']==$data['rpass']){
                unset($data['rpass']);
                
                if($data['pass']==''){
                    unset($data['pass']);
                }else{
                    $data['pass'] = md5($data['pass']);
                }
            }else{
                $this->error('二次密碼不壹致');
            }

            $result = $info->validate($data);
            if($result!==true)      $this->error($result);

            // var_dump($data);die();
            $result = $info->allowField(true)->save($data);
            if($result===1){
                // 刪除或者更改的管理員狀態改變
                cache('adminuser.'.$info->id, null);

                $this->success('操作成功！',session('HTTP_REFERER'));
            }elseif($result===0)     $this->error('操作失敗,沒有數據更改！');
            else                    $this->error('操作失敗'); 
        }

        session('HTTP_REFERER',$_SERVER['HTTP_REFERER']??'');
        return $this->fetch();
    }
    //刪除
    public function userDel() {
        $id = $this->request->get('id',0,'intval');

        $model = AdminUsers::get($id);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->delete();
        if($result){
            // 刪除或者更改的管理員狀態改變
            cache('adminuser.'.$id, null);

            $this->success('操作成功！','admin/userAll');
        }else                $this->error('操作錯誤：'.$result);
    }
    //修改密碼
    public function reset_pwd() {
        if($this->request->isPost()) {
            $data = $this->request->only(['password','password2']);
            if(empty($data['password']))    $this->error('密碼不可為空');
            if($data['password']!=$data['password2'])   $this->error('二次密碼不同');

            $result = AdminUsers::where(['id'=>$this->view->uid])->update(['pass'=>md5($data['password'])]);
            // var_dump($result);die();
            if($result===1){
                (new Login)->loginout();
                $this->success('操作成功！','index/login');
            }elseif($result===0)        $this->error('操作失敗,沒有數據更改！');
            else                        $this->error('操作失敗'); 
        }else{
            return $this->fetch();
        }
    }
    // 刷新權限
    public function reset_auth() {
        $login = new Login;
        $login->resetAuthList();

        $this->success('操作成功！');
    }


    //全部
    public function authAll() {
        $model = new AdminAuths;
        $param = $this->request->param();
        $list = $model->getAll($param);

        $this->assign('list', $list);
        return $this->fetch();
    }
    //編輯
    public function authEdit() {
        $model = new AdminAuths;
        $id = $this->request->param('id',0,'intval');

        // 編輯數據
        if($id>0){
            $info = AdminAuths::get($id);
            if(!$info)     $this->error('數據不存在！');

            $info['pre'] = json_decode($info['pre'],true);
        }else{
            $info = new AdminAuths;
        }
        $this->assign(['info'=>$info]);

        // 上傳數據
        if($this->request->isPost()) {
            $data = $this->request->only(['pre','title','desp']);
            $data['pre'] = $info->dealAuths($data['pre']);

            $result = $model->validate($data);
            if($result!==true)      $this->error($result);

            $result = $info->allowField(true)->save($data);
            if($result===1)         $this->success('操作成功！',session('HTTP_REFERER'));
            elseif($result===0)     $this->error('操作失敗,沒有數據更改！');
            else                    $this->error('操作失敗'); 
        }

        $admin_menus = $info->getAuthList();
        $this->assign('admin_menus', $admin_menus);

        session('HTTP_REFERER',$_SERVER['HTTP_REFERER']??'');
        return $this->fetch();
    }
    //刪除
    public function authDel() {
        $id = $this->request->get('id',0,'intval');

        $model = AdminAuths::get($id);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->delete();
        if($result)         $this->success('操作成功！','admin/authAll');
        else                $this->error('操作錯誤：'.$result);
    }



    //添加修改權限組
    public function addRbac() {
        $id = $this->request->param('id',0,'intval');
        $rbacmodel = new Rbacs;
        $info = $rbacmodel->getRbacInfo($id);
        $rbac = Container::get('rbac');
        $menus = $rbac->getMenus($info);
        if($this->request->isPost()) {
            $qx = $rbac->getRbac($menus);
            $data = array_merge($this->request->only(['title','ms']), ['pre'=>$qx]);
            $cj = empty($info) ? 'edit' : ($info['title'] != $data['title']) ? 'edit' : 'add';//驗證器場景
            $r = $this->validate(array_merge($data,$this->request->only(['__token__'])),'app\admin\validate\Rbac.'.$cj);
            if(true !== $r) $this->error($r);
            $return = $rbac->setRbac($data,$info?(int)$info['id']:0);
            Container::get('rbac')->setRbCache();
            $return['code'] == 1 ? $this->success('操作權限組成功！') : $this->error($return['msg']);
        }
        $this->assign(['menus' => $menus, 'info'=>$info]);
        return $this->fetch();
    }
}
