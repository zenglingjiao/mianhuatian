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
        $this->assign('coupons', (new Coupons));

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
        $this->assign('coupons', (new Coupons));

        if($this->request->isPjax()) {
            Container::get('config')->set('default_ajax_return', 'html');
            return $this->fetch('userInvoiceAjax');
        } else {
            return $this->fetch();
        }
    }
	
	//帳號合併頁
	public function douserjoin() {
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
	//帳號合併
	public function userjoin()
	{
		$userold = $id = $this->request->param('userold',0,'intval');
		$usernew = $id = $this->request->param('usernew',0,'intval');
		echo $userold."->".$usernew."<br>";
		 
		 $useroldtest = (new Users)->getOne(['id'=>$userold]);
		 if (!$useroldtest) {
              return $this->success('被合併會員不存在！');
          }
		  
		  $usernewtest = (new Users)->getOne(['id'=>$usernew]);
		 if (!$usernewtest) {
              return $this->success('合併會員不存在！');
          }
		 
		 $coupons = new Coupons;
          $result = $coupons->where('uid',$userold)->select();
          if (!$result) {
              return $this->success('數據不存在！');
          }else{
			  echo "coupons-";
			  foreach ($result as $key => $value) {
				 echo $value['id']."-";
				 (new Coupons)->where('id',$value['id'])
                        ->update(['uid'=>$usernew]);
			  }
			  echo "<br>";
		  }
		  
		  $invoices = new Invoices;
          $result = $invoices->where('uid',$userold)->select();
          if (!$result) {
              return $this->success('數據不存在！');
          }else{
			  echo "invoices-";
			  foreach ($result as $key => $value) {
				 echo $value['id']."-";
				 (new Invoices)->where('id',$value['id'])
                        ->update(['uid'=>$usernew]);
			  }
			echo "<br>";
		  }
          return $this->success('操作成功！');
		 
	} 
	  
	//發票查詢
    public function invoiceQuery() {
        $param = $this->request->param();
        // if(empty($param['status'])) $param['status'] = 2;

        $cardno = $this->request->param('cardno','','strval');
        if($cardno){
            $user = (new Users)->getOne(['phone'=>$cardno]);
//            var_dump((new Users)->getLastSql());exit;
            if($user){
                $param['uid'] = $user['id'];
            }
        }

        $model = new Invoices;
        $list = $model->getList($param);
//         var_dump($list);exit;

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


    //重置生日
    public function reset_pwd()
    {
        $data = $this->request->param();
//        $this->error2('操作錯誤：',$data);
        $model = Users::get($data['id']);
        if(!$model)     $this->success('數據不存在！');

        $result = $model->allowField(true)->save($data);
        if($result)         $this->success('修改成功！');
        else                $this->error('修改失敗！');
    }

	//刪除錯誤發票
     public function userinvoiceajaxDel() {
        $id = $this->request->param('id',0,'intval');
        $model = Invoices::get($id);
        if(!$model)     $this->success('數據不存在！');

//         $data=(new Coupons)->getAll(['in_id'=>$id,'type_id'=>['>',19]]);
         $data=Db::table('info_coupon')->where('in_id',$id)->where('type_id','>',19)->select();
//         var_dump(json_encode((new Coupons)->getLastSql()));exit();
         foreach ($data as $key =>$value){
             $qqq=[
                 'status'=>0,
                 'uid'=>0,
                 'get_time'=>0,
                 'use_time'=>0,
                 'in_id'=>0,
             ];
             $res=(new Coupons)->allowField(true)->save($qqq,['id'=>$value['id']]);
             if(!$res){
                 $this->error('操作錯誤：' . $res);
             }
         }
         $result = $model->delete();
        if($result) {

            $this->success('操作成功！');
        } else {
            $this->error('操作錯誤：' . $result);
        }
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
//         var_dump($param);exit;

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
    public function userinvoiceExport() {
        $param = $this->request->param();
//        var_dump($param);exit;

        $model = new Invoices;
        $cnt = $model->getCount($param);
//        var_dump($model->getLastSql());exit;

        if($cnt>10000)  exit('最大匯出記錄條數10000條');

        $list = $model->getAll($param);
        // var_dump($list);

        require '../extend/phpexcel/PHPExcel.php';
        $PHPExcel = new \PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle("匯出表"); //給當前活動sheet設置名稱

        $PHPSheet->setCellValue("A1","NO")
            ->setCellValue("B1","用戶")
            ->setCellValue("C1","電話號碼")
            ->setCellValue("D1","發票號碼")
            ->setCellValue("E1","購買日期")
            ->setCellValue("F1","隨機碼")
            ->setCellValue("G1","金額")
            ->setCellValue("H1","狀態")
            ->setCellValue("I1","描述");

        foreach ($list as $k => $v) {
            $user=(new Users)->getOne(['id'=>$v['uid']]);
            if($v['status']==0) {
                $status = "未校驗";
            }elseif ($v['status']==1) {
                $status = "校驗成功";
            }else{
                $status = "校驗失敗";
            }
            $PHPSheet->setCellValue("A".($k+2),$v['id'])
                ->setCellValue("B".($k+2),$user['id'])
                ->setCellValue("C".($k+2),$user['phone'])
                ->setCellValue("D".($k+2),$v['no1'].'-'.$v['no2'])
                ->setCellValue("E".($k+2),$v['date'])
                ->setCellValue("F".($k+2),$v['code'])
                ->setCellValue("G".($k+2),$v['money'])
                ->setCellValue("H".($k+2),$status)
                ->setCellValue("I".($k+2),$v['msg']);
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
    //錯誤發票
    public function errorinvoiceExport() {
        $param = $this->request->param();
//        var_dump($param);exit;
        $param['status'] = 2;
        $model = new Invoices;
        $cnt = $model->getCount($param);
//        var_dump($model->getLastSql());exit;

        if($cnt>10000)  exit('最大匯出記錄條數10000條');

        $list = $model->getAll($param);
        // var_dump($list);

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
            $user=(new Users)->getOne(['id'=>$v['uid']]);
            if($v['status']==0) {
                $status = "未校驗";
            }elseif ($v['status']==1) {
                $status = "校驗成功";
            }else{
                $status = "校驗失敗";
            }
            $PHPSheet->setCellValue("A".($k+2),$v['id'])
                ->setCellValue("B".($k+2),$user['cardno'])
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

    public function errorinvoiceQuery()
    {   
        $model = new Invoices;
        // $result= $model->query("select * from info_invoice where date < DATE('2018-12-27') and status=0 and msg=''")->paginate(5);
        $result = $model->where('date','<',DATE('2019-12-01'))->where('status',0)->where('msg','')->order('id')->paginate(10);
        // dump($result);
        $this->assign('model',$model);
        $this->assign('list',$result);
        return $this->fetch();
    }

    public function uncheckinvoiceQuery()
    {
        $model = new Invoices;
        // $sql = "select * from info_invoice where status=0 and date<DATE_SUB(NOW(),INTERVAL 1 DAY) and msg='' order by add_time desc";
        // $result= $model->query($sql)->paginate(5);
        $result = $model->where('status',0)->where('add_time','<', date('Y-m-d H:i:s',strtotime("-1 day")))->where('msg','')->order('add_time desc')->paginate(5);
        // echo date('Y-m-d H:i:s',strtotime("-1 day"));exit;
        // var_dump($result);exit;
        $page = $result->render();
        $this->assign('model',$model);
        $this->assign('page',$page);
        $this->assign('list',$result);
        return $this->fetch();
    }
    //检验发票
    public function checkInvoice()
    {

        ini_set('max_execution_time',0);
        $invoice = new Invoices;
//         $sql = "select * from info_invoice where status=0 and date<DATE_SUB(NOW(),INTERVAL 1 DAY) and msg='' limit 20";
        $sql = "select * from info_invoice where status=0  and add_time>DATE_SUB(NOW(),INTERVAL 3 DAY)  order by update_time  limit 30";
//         $sql = "select * from info_invoice where id=34329";
        $unresult = $invoice->query($sql);
        $length = count($unresult);
        if (!$unresult) {
            return $this->success('數據不存在！');
        }

        foreach ($unresult as $key => $value) {
//            echo json_encode($value);
//            exit();
            $url = 'https://api.devsg.openlife.co/v3/invoice/query';
            $time = strtotime($value['date']);
            $post_data = [
                // 10712 KM 25390308 8805 38582646
                'barcode'=>(substr($value['date'], 0, 4)-1911).substr($value['date'], 5, 2).$value['no1'].$value['no2'].$value['code'].'38582646',
                'datetime'=>date('Y-m-d H:i:s'),
            ];

            $str = str_replace('+', '%20', http_build_query($post_data));
            $signature = base64_encode(hash_hmac('sha256', $str, '817c60bb8f5f4a1ba20f40bc631b571aa8774b6e0e1e', true));
//            exit($signature);
            $headers = array(
                "Content-type: application/json",
                'vendor: COTTONFIELD',
                'signature: '.$signature,
            );

//           var_dump($headers); exit();
            $result0 = $this->curl_request($url, json_encode($post_data), $headers);
//            return $this->error2('異常，沒收到api數據',$result0);
//            echo $result0;
//            exit();
            if (!$result0) {
                return $this->error('數據不存在！',null,$value['id']);
            }
            $result = json_decode($result0,true);
            // dump($result);
            if($result['error']==0){
                if(($result['data']['code'])==200){
                    if ($value['money'] == $result['data']['totalAmount']) {
                        $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>1,'msg'=>'']);
                    }else{
                        (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                        $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>'您輸入的發票與實際發票金額不符']);
                    }
                }else{
                    $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>0,'msg'=>$result['data']['msg']]);
                }
            }elseif($result['error']==400 && in_array($result['message'], ['此非棉花田發票','目前查無此發票資訊，請確認輸入資料是否正確'])){
                (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>$result['message']]);
            }elseif($result['error']==9002){
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>0,'msg'=>$result['message']]);
			}elseif($result['error']==9001){
                (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>$result['message']]);
            }else{
                (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>$result['message']]);
            } 
        }
         return $this->success('校驗成功！');
//        return '成功';
//        $this->checkInvoice();
    }


    public function checkInvoice1()
    {

        ini_set('max_execution_time',0);
        $invoice = new Invoices;
        // $sql = "select * from info_invoice where status=0 and date<DATE_SUB(NOW(),INTERVAL 1 DAY) and msg='' limit 20";
        // $sql = "select * from info_invoice where status=0 and date<DATE_SUB(NOW(),INTERVAL 1 DAY) and msg='' order by add_time desc limit 5";
//        $sql = "select * from info_invoice where status=0 order by add_time limit 10";
        $sql = "select * from info_invoice where status=0 and add_time>DATE_SUB(NOW(),INTERVAL 3 DAY)  order by update_time  limit 30";

//        $sql = "select * from info_invoice where id=34338";
        $unresult = $invoice->query($sql);
//        dump($unresult);
        $length = count($unresult);
        if (!$unresult) {
            Db::table('logs')->insert(['msg'=>'數據不存在！','add_time'=>date('Y-m-d H:i:s')]);
            return $this->success2('數據不存在！');
        }
        foreach ($unresult as $key => $value) {
            $url = 'https://api.devsg.openlife.co/v3/invoice/query';
            $time = strtotime($value['date']);
            $post_data = [
                // 10712 KM 25390308 8805 38582646
                'barcode'=>(substr($value['date'], 0, 4)-1911).substr($value['date'], 5, 2).$value['no1'].$value['no2'].$value['code'].'38582646',
                'datetime'=>date('Y-m-d H:i:s'),
            ];
            $str = str_replace('+', '%20', http_build_query($post_data));
            $signature = base64_encode(hash_hmac('sha256', $str, '817c60bb8f5f4a1ba20f40bc631b571aa8774b6e0e1e', true));
            $headers = array(
                "Content-type: application/json",
                'vendor: COTTONFIELD',
                'signature: '.$signature,
            );
            $result0 = $this->curl_request($url, json_encode($post_data), $headers);
            if (!$result0) {
                Db::table('logs')->insert(['msg'=>'異常，沒收到api數據','add_time'=>date('Y-m-d H:i:s')]);
                return $this->error2('異常，沒收到api數據');
            }
            $result = json_decode($result0,true);

            if($result['error']==0){
                if(($result['data']['code'])=='200'){
                    if ($value['money'] == $result['data']['totalAmount']) {
                        $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>1,'msg'=>'']);
                    }else{
                        (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                        $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>'您輸入的發票與實際發票金額不符']);
                    }
                }else{
                    $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>0,'msg'=>$result['data']['msg']]);
                }
            }elseif($result['error']==400 && in_array($result['message'], ['此非棉花田發票','目前查無此發票資訊，請確認輸入資料是否正確'])){
                (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>$result['message']]);
            }elseif($result['error']==9002){
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>0,'msg'=>$result['message']]);
            }else{
                (new Coupons)->where('in_id',$unresult[$key]['id'])->where('uid',$unresult[$key]['uid'])->where('type_id','>',19)->update(['status'=>3]);
                $do = $invoice->allowField(true)->where('id',$unresult[$key]['id'])->update(['status'=>2,'msg'=>$result['message']]);
            } 
        }
        Db::table('logs')->insert(['msg'=>'成功','add_time'=>date('Y-m-d H:i:s')]);
        return $this->success2('校驗成功！');

        // $this->checkInvoice();
    }



    public function curl_request($url, $post_data = '', $headers = []){//curl  
        // var_dump($url, $post_data, $headers);
        $ch = curl_init();  
        curl_setopt ($ch, CURLOPT_URL, $url);  
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。

        curl_setopt ($ch, CURLOPT_POST, 1);  
        if($post_data != ''){  
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
        }  
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);   
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 60);  

        curl_setopt($ch, CURLOPT_HEADER, 0);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $file_contents = curl_exec($ch);  

        // var_dump( curl_getinfo($ch, CURLINFO_HEADER_OUT) );
        curl_close($ch);  
        return $file_contents;  
    }
    //先过滤掉不是活动日期内的
    public function errorivodel()
    {
        //驗證11號之前的
        $result = (new Invoices)->query("select * from info_invoice where status=0 and date < DATE('2019-11-1')");
        $result1 = (new Invoices)->query("select * from info_invoice where status=0 and  add_time<DATE_SUB(NOW(),INTERVAL 3 DAY)");

        if (!$result && !$result1) {
            return $this->success2('數據不存在！');
        }
        foreach ($result as $key => $value) {
            (new Coupons)->where('in_id',$value['id'])
                        ->where('uid',$value['uid'])
                        ->where('type_id','>',19)
                        ->update(['status'=>3]);
            (new Invoices)->where('id',$value['id'])->update(['status'=>2,'msg'=>"您輸入的發票非符合活動期間之發票"]);
        }
//        //驗證3天後的
//        if (!$result1) {
//            return $this->success2('數據不存在！');
//        }
        foreach ($result1 as $key => $value) {
            (new Coupons)->where('in_id',$value['id'])
                ->where('uid',$value['uid'])
                ->where('type_id','>',19)
                ->update(['status'=>3]);
            (new Invoices)->where('id',$value['id'])->update(['status'=>2,'msg'=>"查無此發票資訊，請確認您輸入的發票資料是否正確"]);
        }

        Db::table('logs')->insert(['msg'=>'過濾成功','add_time'=>date('Y-m-d H:i:s')]);
        return $this->success2('操作成功！');
    }



    public function updatecou()
      {
          $invoice = new Invoices;
          $result = $invoice->where('status',2)->select();
          if (!$result) {
              return $this->success('數據不存在！');
          }
          foreach ($result as $key => $value) {
            (new Coupons)->where('in_id',$value['id'])
                        ->where('uid',$value['uid'])
                        ->where('type_id','<',12)
                        ->update(['status'=>3]);
            //(new Invoices)->where('id',$value['id'])->delete();
          }
          return $this->success('操作成功！');
      }  


}
