<?php
namespace app\admin\controller;
use think\Controller;
use think\Container;
use think\Validate;
use app\admin\model\Login;
use app\admin\model\AdminLogs;
use app\admin\model\AdminUsers;

class Index extends controller
{
    //首頁 |raw
    public function index() {
        $this->redirect('info/userList');

        $info = [
            ['name'=>'操作系統','value'=>php_uname()],
            ['name'=>'運行環境','value'=>$_SERVER["SERVER_SOFTWARE"]],
            ['name'=>'PHP運行方式','value'=>php_sapi_name()],
            ['name'=>'PHP版本','value'=>PHP_VERSION],
            ['name'=>'Zend版本','value'=>zend_version()],
            ['name'=>'服務器最大上傳','value'=>ini_get('upload_max_filesize')],
            ['name'=>'PHP最大執行時間','value'=>ini_get('max_execution_time').'秒'],
            ['name'=>'服務器當前時間','value'=>date("Y年n月j日 H:i:s")],
            ['name'=>'服務器域名/IP','value'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]'],
            ['name'=>'服務器剩余空間','value'=>round((disk_free_space(".")/(1024*1024*1024)),2).'G'],
        ];
        $this->assign('serverinfo', $info);

        return $this->fetch();
    }

    //設置主題
    public function setSkin() {
        if($this->request->isAjax()) {
            $login = new Login;

            $skins = ['skin-blue','skin-purple','skin-yellow','skin-red','skin-green'];
            $skin = $this->request->post('skin');
            if(!in_array($skin,$skins))     $this->error('參數錯誤！');

            try{
                //更改數據庫
                $res = AdminUsers::where("id",$login->uid)->setField("skin",$skin);
                if(!$res)   throw new \Exception('更新用戶信息失敗，請刷新頁面重試！');

                //添加日誌
                $res = (new AdminLogs)->addLog(1,'更換後臺主題'.$skin, $login->uid);
                if(!$res)   throw new \Exception('記錄日誌失敗，請刷新頁面重試！');

                //更改緩存
                $login->setUserSession('skin',$skin);
            }catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            
            $this->success('ok！');
        } else {
            $this->error('錯誤的請求！');
        }
    }

    //登錄
    public function login() {
        // 是否登錄
        $model = new Login;
        if($model->checkLogin()){
            $this->success('您已登錄！','admin/Index/index');
        }

        // 登錄處理
        if($this->request->isPost()) {
            // 驗證碼校驗
            /*$result = check_captcha();
            if($result!==true)      $this->error($result);*/

            // 校驗參數
            $request = $this->request->only(['username','pass','code']);
            $validate = new Validate([
                'username|用戶名'     => 'require',
                'pass|密碼'         => 'require',
            ]);
            $result = $validate->check($request);
            if($result!==true)      $this->error($validate->getError());
            // var_dump($request);die();

            // 登錄處理
            $result = $model->setLogin($request);
            exit(json_encode($result));
        }

        $this->view->engine->layout(false);
        return $this->fetch();
    }

    //退出登錄
    public function loginout() {
        $result = (new Login)->loginout();
        if($result===true)      $this->success('退出登錄成功！','admin/Index/login');
        else                    $this->error('操作失敗:'.$result);
    }

    /** 清空系統緩存 **/
    public function cache(){
        header("Content-type: text/html; charset=utf-8");
        $dirs = array('./../runtime/');//清文件緩存
        @mkdir('runtime',0777,true);
        foreach($dirs as $value) {  //清理緩存
            $this->rmdirr($value);
        }
        $this->success('緩存清除成功！');
    }
    //處理方法
    public function rmdirr($dirname) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        if($dir){
            while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);//遞歸
            }
        }
        $dir->close();
        return rmdir($dirname);
    }
}
