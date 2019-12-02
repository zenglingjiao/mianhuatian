<?php
namespace app\admin\controller;
use think\Controller;
use think\Container;
use think\Db;
use think\facade\Session;
use app\admin\model\InfoCateTypes;
use app\admin\model\Coupons;

use app\admin\model\Users;
use app\admin\model\Invoices;

class Info extends controller
{
    public function userList() {
        $model = new Users;
        $param = $this->request->param();
        $list = $model->getList($param);

        $this->assign('list', $list);
        $this->assign('param', $param);

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('userAjax');
        } else {
            return $this->fetch();
        }
    }
    public function userInvoiceList() {
        $uid = $this->request->param('uid',0,'intval');
        $user = (new Users)->getOne(['id'=>$uid]);
        if(!$user)      $this->error('鏈接出錯','info/userList');
        $this->assign('user', $user);

        $model = new Invoices;
        $param = $this->request->param();
        $list = $model->getList($param);

        $this->assign('list', $list);
        $this->assign('param', $param);
        $this->assign('model', $model);
        $this->assign('users', (new Users));

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('userInvoiceAjax');
        } else {
            return $this->fetch();
        }
    }

    


    public function userCouponList() {
        $uid = $this->request->param('uid',0,'intval');
        $user = (new Users)->getOne(['id'=>$uid]);
        if(!$user)      $this->error('鏈接出錯','info/userList');
        $this->assign('user', $user);

        $list_cate = (new InfoCateTypes)->getMap();
        $this->assign(['list_cate'=>$list_cate]);

        $model = new Coupons;
        $param = $this->request->param();
        $list = $model->getList($param);

        $this->assign('list', $list);
        $this->assign('param', $param);
        $this->assign('model', $model);
        $this->assign('users', (new Users));

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('awardAjax');
        } else {
            return $this->fetch();
        }
    }

    public function invoiceList() {
        $param = $this->request->param();
        if(empty($param['status'])) $param['status'] = 2;

        $key = $this->request->param('key','','strval');
        if($key){
            $user = (new Users)->getOne(['key2'=>$key]);
            if($user){
                $param['uid'] = $user['id'];
            }
        }

        $model = new Invoices;
        $list = $model->getList($param);

        $this->assign('list', $list);
        $this->assign('param', $param);
        $this->assign('model', $model);
        $this->assign('users', (new Users));

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('userInvoiceAjax');
        } else {
            return $this->fetch();
        }
    }

    public function invoiceQuery() {
        $param = $this->request->param();
        // if(empty($param['status'])) $param['status'] = 2;

        $cardno = $this->request->param('cardno','','strval');
        if($cardno){
            $user = (new Users)->getOne(['cardno'=>$cardno]);
            if($user){
                $param['uid'] = $user['id'];
            }
        }

        $model = new Invoices;
        $list = $model->getList($param);

        $this->assign('list', $list);
        $this->assign('param', $param);
        $this->assign('model', $model);
        $this->assign('users', (new Users));

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('invoiceajax');
        } else {
            return $this->fetch();
        }
    }

    


	//刪除
     public function userinvoiceajaxDel() {
        $id = $this->request->param('id',0,'intval');
        $model = Invoices::get($id);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->delete();
        if($result)         $this->success('操作成功！');
        else                $this->error('操作錯誤：'.$result);
    }


    public function userlistajaxDel() {
        $id = $this->request->param('id',0,'intval');
        $model = Users::get($id);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->delete();
        if($result)         $this->success('操作成功！');
        else                $this->error('操作錯誤：'.$result);
    }

    //全部
    public function typeAll() {
        $model = new InfoCateTypes;
        $param = $this->request->param();
        $param['status'] = 1;
        $list = $model->getAll($param);

        $this->assign('param', $param);
        $this->assign('model', $model);
        $this->assign('list', $list);
        return $this->fetch();
    }
    //編輯
    public function typeEdit() {
        $id = $this->request->param('id',0,'intval');

        // 編輯數據
        if($id>0){
            $info = InfoCateTypes::get($id);
            if(!$info)     $this->error('數據不存在！');
        }else{
            $info = new InfoCateTypes;
        }
        $this->assign(['info'=>$info]);

        // 上傳數據
        if($this->request->isPost()) {
            $data = $this->request->only(['title','desc','used','time_limit']);

            // 沒有上傳圖片，不修改圖片
            $img = Container::get('upload')->uploads($this->request->file('img'),'banner','image',false,'');
            if($img['error'] == 0)  $data['img'] = str_replace('\\', '/', $img['url']);//上傳圖片
            elseif($img['message']!='没有上传资源！')   $this->error('操作錯誤：'.$img['message']);

            $result = $info->validate($data);
            if($result!==true)      $this->error($result);

            // var_dump($data);exit();
            $result = $info->allowField(true)->save($data);
            if($result===1 || $result===0)         $this->success('操作成功！',session('HTTP_REFERER'));
            else                    $this->error('操作失敗'); 
        }

        session('HTTP_REFERER',$_SERVER['HTTP_REFERER']??'');
        return $this->fetch();
    }
    //刪除
    public function typeDel() {
        $id = $this->request->get('id',0,'intval');

        $model = InfoCateTypes::get($id);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->allowField(true)->save(['status'=>2]);
        if($result)         $this->success('操作成功！','info/typeAll');
        else                $this->error('操作錯誤：'.$result);
    }


    //編輯
    public function typePro() {
        $list_cate = (new InfoCateTypes)->getMap2(['status'=>1]);
        $this->assign(['list_cate'=>$list_cate]);

        // 上傳數據
        if($this->request->isPost()) {
            $data = $this->request->param();
            // var_dump($data);exit();

            $num = array_sum($data);
            if($num!=100)       $this->error("滿幾率必須為100%");

            foreach ($data as $k => $v) {
                $arr = explode('_', $k);

                $result = (new InfoCateTypes)->where(['id'=>$arr[1]])->update(['pro'=>$v]);
            }

            if($result===1 || $result===0)         $this->success('操作成功！',session('HTTP_REFERER'));
            else                    $this->error('操作失敗'); 
        }

        session('HTTP_REFERER',$_SERVER['HTTP_REFERER']??'');
        return $this->fetch();
    }
    

    // 列表
    public function awardList() {
        $list_cate = (new InfoCateTypes)->getMap();
        $this->assign(['list_cate'=>$list_cate]);

        $model = new Coupons;
        $param = $this->request->param();
        $list = $model->getList($param);

        $this->assign('list', $list);
        $this->assign('param', $param);
        $this->assign('model', $model);
        $this->assign('users', (new Users));

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('awardAjax');
        } else {
            return $this->fetch();
        }
    }
    //編輯
    public function awardEdit() {
        // 分類列表
        $list_cate = (new InfoCateTypes)->getMap();
        $this->assign(['list_cate'=>$list_cate]);

        // 分類列表
        $infocatetypes = (new InfoCateTypes)->getMap2();

        $info = new Coupons;
        $this->assign(['info'=>$info]);

        // 上傳數據
        if($this->request->isPost()) {
            $data = $this->request->param();

            $data['type_name'] = $infocatetypes[$data['type_id']]['title'];
            $data['type_img'] = $infocatetypes[$data['type_id']]['img'];

            $result = $info->validate($data);
            if($result!==true)      $this->error($result);

            // var_dump($data);die();
            $result = $info->allowField(true)->save($data);
            if($result===1)         $this->success('操作成功！',session('HTTP_REFERER'));
            elseif($result===0)     $this->error('操作失敗,沒有數據更改！');
            else                    $this->error('操作失敗'); 
        }

        session('HTTP_REFERER',$_SERVER['HTTP_REFERER']??'');
        return $this->fetch();
    }
    //刪除
    public function awardDel() {
        $id = $this->request->get('id',0,'intval');

        $model = Coupons::get($id);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->delete();
        if($result)         $this->success('操作成功！');
        else                $this->error('操作錯誤：'.$result);
    }

    public function awardImport() {
        // 上傳數據
        if($this->request->isPost()) {
            // 沒有上傳圖片，不修改圖片
            $img = Container::get('upload')->uploads($this->request->file('excel'),'excel','file',false,'');
            if($img['error'] == 0)      $img = str_replace('\\', '/', $img['url']);//上傳圖片
            elseif($img['message']!='沒有上傳資源！')   $this->error('操作錯誤：'.$img['message']);
            // $img = '/upload/excel/20181221/3ad9a627272345cf495347a3e6750615.xlsx';

            // 分類列表
            $infocatetypes = (new InfoCateTypes)->getMap2();

            require '../extend/phpexcel/PHPExcel.php';
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($_SERVER['DOCUMENT_ROOT'].$img, $encode = 'utf-8');

            $excel_array = $objPHPExcel->getsheet(0)->toArray();   //轉換為數組格式
            array_shift($excel_array);  //刪除第壹個數組(標題);
            // var_dump($excel_array);
            
            $i = 0;
            foreach($excel_array as $k=>$v) {
                $info = new Coupons;
                $data = [];
                $data['no'] = $v['0'];
                $data['title'] = $v['2'];

                if(!isset($infocatetypes[$v['1']]))     continue;

                $data['type_id'] = $infocatetypes[$v['1']]['id'];
                $data['type_name'] = $infocatetypes[$v['1']]['title'];
                $data['type_img'] = $infocatetypes[$v['1']]['img'];
                
                $result = $info->allowField(true)->save($data);
                if($result)     $i++;
            }

            $this->success('匯入'.$i.'條獎項');
        }
        
        $this->error('匯入文件為空'); 
    }
    public function awardExport() {
        $list_cate = (new InfoCateTypes)->getMap();
        $users = (new Users);
        $param = $this->request->param();

        $model = new Coupons;
        $cnt = $model->getCount($param);
        if($cnt>10000)  exit('最大匯出記錄條數10000條');

        $list = $model->getAll($param);
        // var_dump($list);

        require '../extend/phpexcel/PHPExcel.php';
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("匯出表"); //給當前活動sheet設置名稱

        $PHPSheet->setCellValue("A1","序號")->setCellValue("B1","獎項類別")->setCellValue("C1","獎項名稱")->setCellValue("D1","狀態")->setCellValue("E1","擁有人")->setCellValue("F1","兌換時間")->setCellValue("G1","使用時間");

        foreach ($list as $k => $v) {
            $PHPSheet->setCellValue("A".($k+2),$v['no'])->setCellValue("B".($k+2),$v['type_name'])->setCellValue("C".($k+2),$v['title'])->setCellValue("D".($k+2),$model->status_arr[$v['status']])->setCellValue("E".($k+2),$users->getF($v['uid'],'cardno'))->setCellValue("F".($k+2),($v['get_time']>0)?date('Y-m-d H:i:s',$v['get_time']):'')->setCellValue("G".($k+2),($v['use_time']>0)?date('Y-m-d H:i:s',$v['use_time']):'');
        }

        ob_end_clean();
        $fileName = iconv("utf-8", "gb2312", 'Export.xlsx'); // 重命名表
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//創建生成的格式
        header('Content-Disposition: attachment;filename="'.$fileName.'"');//下載下來的表格名
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vnd.ms-excel');
        $PHPWriter->save("php://output"); //表示在$path路徑下面生成demo.xlsx文件
    }
    public function userExport() {
        $param = $this->request->param();
        // var_dump($param);exit;

        $model = new Users;
        $cnt = $model->getCount($param);
        if($cnt>10000)  exit('最大匯出記錄條數10000條');

        $list = $model->getAll($param);
        // var_dump($list);

        require '../extend/phpexcel/PHPExcel.php';
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("匯出表"); //給當前活動sheet設置名稱

        $PHPSheet->setCellValue("A1","NO")->setCellValue("B1","用戶")->setCellValue("C1","發票號碼");

        foreach ($list as $k => $v) {
            $PHPSheet->setCellValue("A".($k+2),$v['id'])->setCellValue("B".($k+2),$v['cardno'])->setCellValue("C".($k+2),$v['phone']);
        }

        ob_end_clean();
        $fileName = iconv("utf-8", "gb2312", 'Export.xlsx'); // 重命名表
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//創建生成的格式
        header('Content-Disposition: attachment;filename="'.$fileName.'"');//下載下來的表格名
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vnd.ms-excel');
        $PHPWriter->save("php://output"); //表示在$path路徑下面生成demo.xlsx文件
    }

    public function userExport1() {
        $param = $this->request->param();
        // var_dump($param);exit;

        $model = new Invoices;
        $cnt = $model->getCount($param);
        if($cnt>10000)  exit('最大匯出記錄條數10000條');
        $model1 = new Users;
        $data = $model1->find($param);

        $list = $model->getAll($param);
        $list[0]['cardno'] = $data['cardno'];
        // var_dump($list);exit;

        require '../extend/phpexcel/PHPExcel.php';
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("匯出表"); //給當前活動sheet設置名稱

        $PHPSheet->setCellValue("A1","NO")
                ->setCellValue("B1","用戶")
                ->setCellValue("C1","發票號碼")
                ->setCellValue("D1","購買日期")
                ->setCellValue("E1","隨機碼")
                ->setCellValue("F1","金額")
                ->setCellValue("G1","狀態")
                ->setCellValue("H1","描述");

        foreach ($list as $k => $v) {
            // var_dump($list['cardno']);exit;
            if($v['status']==0) {
                $status = "未校驗";
            }elseif ($v['status']==1) {
                $status = "校驗成功";
            }else{
                $status = "校驗失敗";
            }
            $PHPSheet->setCellValue("A".($k+2),$v['id'])
                    ->setCellValue("B".($k+2),$list[0]['cardno'])
                    ->setCellValue("C".($k+2),$v['no1'].'-'.$v['no2'])
                    ->setCellValue("D".($k+2),$v['date'])
                    ->setCellValue("E".($k+2),$v['code'])
                    ->setCellValue("F".($k+2),$v['money'])
                    ->setCellValue("G".($k+2),$status)
                    ->setCellValue("H".($k+2),$v['msg']);
        }

        ob_end_clean();
        $fileName = iconv("utf-8", "gb2312", 'Export.xlsx'); // 重命名表
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//創建生成的格式
        header('Content-Disposition: attachment;filename="'.$fileName.'"');//下載下來的表格名
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vnd.ms-excel');
        $PHPWriter->save("php://output"); //表示在$path路徑下面生成demo.xlsx文件
    }

    public function userExport2() {
        $param = $this->request->param();
        // var_dump($param);exit;

        $model = new Coupons;
        $cnt = $model->getCount($param);
        if($cnt>10000)  exit('最大匯出記錄條數10000條');
        $model1 = new Users;
        $data = $model1->find($param);

        $list = $model->getAll($param);
        $list[0]['cardno'] = $data['cardno'];
        // var_dump($list);exit;

        require '../extend/phpexcel/PHPExcel.php';
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("匯出表"); //給當前活動sheet設置名稱

        $PHPSheet->setCellValue("A1","序號")
                ->setCellValue("B1","獎項類別")
                ->setCellValue("C1","獎項名稱")
                ->setCellValue("D1","狀態")
                ->setCellValue("E1","擁有人")
                ->setCellValue("F1","領取時間")
                ->setCellValue("G1","使用時間");

        foreach ($list as $k => $v) {
            // var_dump($list['cardno']);exit;
            if ($v['status']==1) {
                $status = "已領取";
            }else{
                $status = "已使用";
            }
            $get_time = date('Y-m-d H:i',$v['get_time']);
            $use_time = "未使用";
            if (!empty($v['use_time'])) {
                $use_time = date('Y-m-d H:i',$v['use_time']);
            }
            $PHPSheet->setCellValue("A".($k+2),$v['no'])
                    ->setCellValue("B".($k+2),$v['type_name'])
                    ->setCellValue("C".($k+2),$v['title'])
                    ->setCellValue("D".($k+2),$status)
                    ->setCellValue("E".($k+2),$list[0]['cardno'])
                    ->setCellValue("F".($k+2),$get_time)
                    ->setCellValue("G".($k+2),$use_time);
        }

        ob_end_clean();
        $fileName = iconv("utf-8", "gb2312", 'Export.xlsx'); // 重命名表
        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//創建生成的格式
        header('Content-Disposition: attachment;filename="'.$fileName.'"');//下載下來的表格名
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Type: application/vnd.ms-excel');
        $PHPWriter->save("php://output"); //表示在$path路徑下面生成demo.xlsx文件
    }
    
}
