<?php
namespace app\index\controller;

use think\Controller;
use think\Container;
use app\admin\model\Users;
use app\admin\model\Invoices;
use app\admin\model\InfoCateTypes;
use app\admin\model\Coupons;
use think\Db;

class Index extends controller
{
    public function initialize()
    {
        parent::initialize();
        
        $uid = session('uid');
        $this->assign('uid', $uid);
        // var_dump($uid);
    }

    public function index()
    {
        $id = $this->request->get('id','','strval');
        $this->assign('id', $id);

        if(!$this->view->uid)       $award = [];
        else                        $award = (new Coupons)->getAll(['status_no'=>'3','uid'=>$this->view->uid]);
        $this->assign('award', $award);
        // var_dump($award);
        
        
        $list_cate = (new InfoCateTypes)->getMap2();
        $this->assign(['list_cate'=>$list_cate]);

        return $this->fetch();
    }
    public function winlist()
    {
        return $this->fetch();
    }
    public function Qrcode()
    {
        $no = $this->request->param('no','','strval');

        $img = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/barcodegen/example/html/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&font_size=8&thickness=30&checksum=&code=BCGcode39extended&text='.$no;
        //$img = file_get_contents('https://'.$_SERVER['HTTP_HOST'].'/barcodegen/example/html/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=0&font_size=8&thickness=30&checksum=&code=BCGcode39extended&text='.$no);
        $this->success2('展示成功',['img'=>$img]);
//        header("Content-Type: image/jpeg;text/html; charset=utf-8");
//        echo $img;
//        exit();
    }
    public function Qrcode1($no)
    {
//        $no = $this->request->param('no','','strval');

        $img = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/barcodegen/example/html/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&font_size=8&thickness=30&checksum=&code=BCGcode39extended&text='.$no;
        //$img = file_get_contents('https://'.$_SERVER['HTTP_HOST'].'/barcodegen/example/html/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=0&font_size=8&thickness=30&checksum=&code=BCGcode39extended&text='.$no);
        return $img;
//        header("Content-Type: image/jpeg;text/html; charset=utf-8");
//        echo $img;
//        exit();
    }

    public function login()
    {
        if($this->request->isPost()) {
            $data = $this->request->only(['cardno','phone']);

            $info = new Users;
			
			//$cardno = $info->getOne(['cardno'=>$data['cardno']]);
            //if($cardno)      $this->error("會員卡號已存在!");
			
			//$result = $info->where('phone',$data['phone'])->select();
			//if($result===true)      $this->error("帳號已存在!");

//            $result = $info->validate($data);
//            if($result!==true)      $this->error($result);

            $user = $info->getOne(['phone'=>$data['phone']]);
//            echo $info->getLastSql();
//             var_dump($user);exit();

            if($user){
                //if($user['phone']!=$data['phone'])      $this->error2('綁定手機號碼錯誤');
                if($user['cardno']==$data['cardno']){
                    session('uid',$user['id']);
                    session('award',0);
                    session('limit',0);
                    $this->success2('登錄成功');
                }else{
                    $this->error2('您輸入的資料錯誤請重新輸入');
                }


            }else{
                $this->error2('此號碼不存在，請確認您輸入的資料是否正確或此號碼尚未完成首次登入');
//                $result = $info->allowField(true)->save($data);
//
//                session('uid',$info['id']);
//                session('award',0);
//                session('limit',0);
            }

            // $this->redirect('index/index');
        }
    }
    public function logout()
    {
        session('uid',null);
        session('award',0);
        session('limit',0);
        $this->success2('您已成功登出！下次登入時請使用相同手機號碼及出生日期登入，即可查詢抽獎紀錄！');
//        return $this->fetch();
    }
    //第一次登入
    public function login_frist()
    {
        $data=$this->request->param();
        if($data['type']==1){
            if($data['classd1']==1) {
                $validate = $this->validate($data, [
                    'phone1' => 'require|number|length:10,10',
                ], [
                    'phone1.require' => '手機號碼不能為空',
                    'phone1.length' => '手機號碼長度為10位',
                    'phone1.number' => '必須為數字',
                ]);
            }else{
                $validate = $this->validate($data, [
                    'phone1' => 'require|number|length:9,10',
                ], [
                    'phone1.require' => '市話不能為空',
                    'phone1.length' => '市話長度為9-10位',
                    'phone1.number' => '必須為數字',
                ]);
            }
            if($validate===true){
                $user = (new Users)->getOne(['phone'=>$data['phone1']]);
                if($user){
                    $this->error2('此號碼已被使用');
                }else{
                    $this->success2('成功');
                }
            }else{
                $this->error2($validate);
            }
        }else{
            $validate = $this->validate($data, [
                'cardno1' => 'require|number|length:6,6',
                'recardno1' => 'require|number|length:6,6|confirm:cardno1',
            ], [
                'cardno1.require' => '生日不能为空',
                'cardno1.length' => '出生日期為6碼',
                'cardno1.number' => '必須為數字',
                'recardno1.require' => '重複生日不能为空',
                'recardno1.length' => '出生日期為6碼',
                'recardno1.number' => '必須為數字',
                'recardno1.confirm' => '您輸入兩次的出生日期不相同請重新輸入',
            ]);
            if($validate===true){
//                $this->success2('退出成功',$data);
                $users=new Users;
                $result = $users->allowField(true)->save(['phone'=>$data['phone1'],'cardno'=>$data['cardno1'],'add_time'=>date('Y-m-d H:i:s')]);
//                var_dump();exit();
                if($result){
                    session('uid',$users->id);
                    session('award',0);
                    session('limit',0);
                    $this->success2('設定成功');
                }else{
                     $this->error2('設定失敗');
                }
            }else{
                $this->error2($validate);
            }
        }
        $this->success2('退出成功',$data);

    }
    //登入发票
    public function enter()
    {
//        echo 11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111;
//        return $this->fetch('index');
        /*$award = session('award');
        $limit = session('limit');
        $award += 1;
        session('award',$award);
        $this->success2('操作成功',['award'=>$award,'limit'=>$award-$limit]);*/

        if($this->request->isPost()) {
            if(!$this->view->uid)       $this->error2('請先登入會員');

            $data = $this->request->only(['no1_1','no1_2','no2','date','code','money']);
            $data['no1']=$data['no1_1'].$data['no1_2'];
            $info = new Invoices;
            $result = $info->validate($data);
            if($result!==true)      $this->error($result);

            $one = $info->getOne(['no1'=>$data['no1'],'no2'=>$data['no2']]);

            //新發票
            if (!$one) {
                if($data['money']<588) $this->error2('發票金額滿588元才能進行抽獎喔!');
                $data['uid'] = $this->view->uid;
                $data['total_times'] = floor($data['money']/588);
                $data['remain_times'] = $data['total_times'];
                $data['add_time']=date('Y-m-d H:i:s');
                $result = $info->allowField(true)->save($data);
                if($result===1){
                    // $award = session('award');
                    // $limit = session('limit');
                    // 每次輸入發票重新計算
                    // $award = 0;
                    // $limit = 0;

                    // $num = floor($data['money']/588);
                    // $award += $num;
                    // $one1 = $info->getOne(['no1'=>$data['no1'],'no2'=>$data['no2'],'date'=>$data['date'],'code'=>$data['code'],'money'=>$data['money']]);

                    session('award',$data['total_times']);
                    session('limit',$data['remain_times']);
                    session('code',!empty($data['code']));
                    session('in_id',$info->id);
//                    return $this->fetch('award');
                    $this->success2('發票資訊輸入完成，立即抽紅包！',['award'=>$data['remain_times'],'limit'=>$data['remain_times']]);
                }else{
                    $this->error2('操作失敗'); 
                }
            }
            //验证是否本人使用
            if($this->view->uid != $one['uid']){
                $this->error2('該發票已被別人使用');
            }
            //驗證其他欄目
            if($one->date != $data['date'] || $one->code != $data['code'] || $one->money != $data['money']){
                $this->error2('發票資訊不符，請確認您輸入的發票資訊是否正確');
            }
            //發票抽獎次數用完
            if($one->remain_times == 0)                    $this->error2('該發票無剩餘抽獎次數！');
            
            //未抽完的發票
            if ($one->remain_times > 0) {
                $data['uid'] = $this->view->uid;
                $award = $one->total_times;
                $limit = $one->remain_times;
                session('award',$award);
                session('limit',$limit);
                session('code',!empty($data['code']));
                session('in_id',$one['id']);

//                return $this->fetch('award');
                $this->success2('發票資訊輸入完成，立即抽紅包！',['award'=>$award,'limit'=>$limit]);
            }else{
                $this->error2('操作失敗'); 
            }   
                                
        }
    }
    public function red_env()
    {
        if(!$this->view->uid) {   return $this->fetch('index');}
        $award = session('award');
        $limit = session('limit');
        $code = session('code');
        $in_id = session('in_id');
        $this->assign('award',$award);
        $this->assign('limit',$limit);
        return $this->fetch();
    }
    //查詢記錄
    public function query()
    {
        if(!$this->view->uid) {   return $this->fetch('record-1');}
        $uid=$this->view->uid;
        $info = (new Coupons)->getAll1(['uid'=>$uid]);
        $info_bool=(new Coupons)->getAll(['uid'=>$uid]);
//        $aa=boolval($info);
//        var_dump($aa);exit();

        if(!$info_bool){
            return $this->fetch('record-2');
        }
        foreach ($info as $key => $value)
        {
            $info[$key]['qrcode']=$this->Qrcode1($value['no']);
        }
//        $info=Db::table('info_coupon')->where('uid',$uid)->select();
        $this->assign('info',$info);
        $this->assign('date_time',date('Y-m-d H:i:s',time()-86400));
        return $this->fetch();
    }
    //抽獎
    public function award()
    {
        if(!$this->view->uid)       $this->error2('請先登入會員');

        $award = session('award');
        $limit = session('limit');
        $code = session('code');
        $in_id = session('in_id');

        if($limit<=0)      $this->error2('您的抽獎次數已用完');

        if($code){
            $infocatetypes = (new InfoCateTypes)->getMap2(['status'=>1]);
        }else{
            $infocatetypes = (new InfoCateTypes)->getMap2(['status'=>1,'key'=>'加價購']);
        }
        $types = array_reset($infocatetypes,'id');
        $infocatetypes = array_reset($infocatetypes,'id','pro');
        // var_dump($infocatetypes);

        $couGroup = (new Coupons)->getGroup(['group'=>'type_id','field'=>'type_id,count(id) as num','status'=>'0']);
        $couGroup = array_reset($couGroup,'type_id','num');
//         var_dump((new Coupons)->getLastSql());exit();
//                 var_dump(json_encode($couGroup));exit();

        // 抽獎--確定獎品分類
        $num = $num2 = $type_id = $rand = 0;


        foreach ($infocatetypes as $k => $v) {
            if(isset($couGroup[$k]))    $num += $v;
        }
        $rand = rand(0,$num);
//        var_dump();
        foreach ($infocatetypes as $k => $v) {
            if(isset($couGroup[$k])){
                $num2 += $v;
                if($num2>$rand){
                    $type_id = $k;
                    break;
                }
            }
        }

        // 抽獎--確定獎品--按順序
        $info = (new Coupons)->getOne(['type_id'=>$type_id,'status'=>0]);


//        exit(json_encode($types));
        if(!$info){
//            var_dump((new Coupons)->getLastSql());exit();
            $this->error2('系統發生錯誤，請重新再抽一次');
        }
//        var_dump($rand.''.$num);exit();

        $limit--;
        session('limit',$limit);
        $update = (new Invoices)->where('id',$in_id)->update(['remain_times'=>$limit]);
        $result = $info->allowField(true)->save(['uid'=>$this->view->uid,'in_id'=>$in_id,'status'=>1,'get_time'=>time()]);
        if($result===1)         $this->success2('抽獎成功',['type'=>$types[$type_id],'award'=>$info]);
        else                    $this->error2('抽獎失敗：'.$result);
    }
    public function use()
    {
	
//        if(!$this->view->uid)       $this->error2('請先登入會員');
//        return $this->error2('a');
        $id = $this->request->param('id','','strval');

        $info = (new Coupons)->getOne(['id'=>$id]);
        $cate_type=(new InfoCateTypes)->getOne(['id'=>$info['type_id']]);
        // var_dump($info);exit();
        if(!$info)                              $this->error2('獎項不存在');
//        if($info['uid']!=$this->view->uid)      $this->error2('獎項不存在');
        if($info['status']==2)                  $this->error2('獎項已使用');
        if($cate_type['time_limit'] != 2) {
            if ($info['status'] == 3) $this->error2('發票錯誤');
        }
//        $list_cate = (new InfoCateTypes)->getMap2();
//        if( $list_cate[$info['type_id']]['time_limit']==1 && $info['get_time']+86400>time())      $this->error2('獎項還在凍結中');

        // 旧数据
        if($info['in_id']==0 && $info['get_time']<strtotime('2018-12-28')){
            $result = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
            if($result===1)         $this->success2('使用成功');
            else                    $this->error2('使用失敗：'.$result);
        }
        $result = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
        if($result===1)         $this->success2('使用成功');
        else                    $this->error2('使用失敗：'.$result);

        //全部放行
//		if($info['type_id']<17){
//            $result = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
//            if($result===1)         $this->success2('使用成功');
//            else                    $this->error2('使用失敗：'.$result);
//        }

//        return $this->error2('a');

        //加價購放行
		/*if($info['type_id']==12 or $info['type_id']==13){
            $result = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
            if($result===1)         $this->success2('使用成功');
            else                    $this->error2('使用失敗：'.$result);
        }

        // 校驗發票
        $invoice = (new Invoices)->getOne(['id'=>$info['in_id']]);
        if(!$invoice)                              $this->error2('發票不存在');

        if($invoice['status']==1){
            $result = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
            if($result===1)         $this->success2('使用成功');
            else                    $this->error2('使用失敗：'.$result);
        }elseif($invoice['status']==2){
            $result = $info->allowField(true)->save(['status'=>3,'use_time'=>time()]);
            $this->error2($invoice['msg']);
        }else{
            $url = 'https://api.devsg.openlife.co/v3/invoice/query';
            
            $time = strtotime($invoice['date']);
            $post_data = [
                // 10712 KM 25390308 8805 38582646
                'barcode'=>(substr($invoice['date'], 0, 4)-1911).substr($invoice['date'], 5, 2).$invoice['no1'].$invoice['no2'].$invoice['code'].'38582646',
                'datetime'=>date('Y-m-d H:i:s'),

                //'barcode'=>"10712KM25390308880538582646",
                //'datetime'=>"2018-12-25 20:15:08",
            ];
            $str = str_replace('+', '%20', http_build_query($post_data));
            $signature = base64_encode(hash_hmac('sha256', $str, '817c60bb8f5f4a1ba20f40bc631b571aa8774b6e0e1e', true));

            $headers = array(
                "Content-type: application/json",
                'vendor: COTTONFIELD',
                'signature: '.$signature,
            );

            $result0 = $this->curl_request($url, json_encode($post_data), $headers);
            $result = json_decode($result0,true);
            // var_dump($result0,$result);

		

            if($result['error']==0){
                if(($result['data']['code']??'')==200){

					if ($invoice['money'] == $result['data']['totalAmount']) {

                        $do = $invoice->allowField(true)->save(['status'=>1,'msg'=>'']);
						$do = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
						$this->success2('使用成功');
                    }else{
                        $do = $invoice->allowField(true)->save(['status'=>2,'msg'=>'輸入發票與實際發票金額不符']);
						$do = $info->allowField(true)->save(['status'=>3]);

						$this->error2('使用失敗：輸入發票與實際發票金額不符');
					}
                    //$do = $invoice->allowField(true)->save(['status'=>1]);

                    //$do = $info->allowField(true)->save(['status'=>2,'use_time'=>time()]);
                    //if($do===1)         $this->success2('使用成功');
                    //else                    $this->error2('使用失敗：'.$result);
                }else{
                    $do = $invoice->allowField(true)->save(['status'=>2,'msg'=>$result['code'].':'.$result['data']['msg']??'']);

                    $this->error2('使用失敗：'.$result['data']['msg']);
                }
            }elseif($result['error']==400 && in_array($result['message'], ['此非棉花田發票','目前查無此發票資訊，請確認輸入資料是否正確'])){
                $do = $invoice->allowField(true)->save(['status'=>2,'msg'=>$result['message']]);
                //$do = $info->allowField(true)->save(['status'=>3,'use_time'=>time()]);
                $do = $info->allowField(true)->save(['status'=>3]);

                $this->error2('使用失敗：'.$result['message']);
            }elseif($result['error']==9002){
                $do = $invoice->allowField(true)->save(['status'=>0,'msg'=>$result['message']]);
                // $do = $info->allowField(true)->save(['status'=>3,'use_time'=>time()]);

                $this->error2($result['message']);
			}elseif($result['error']==9001){
				$do = $invoice->allowField(true)->save(['status'=>1,'msg'=>$result['message']]);
				$this->error2($result['message']);
            }else{
                $do = $invoice->allowField(true)->save(['status'=>2,'msg'=>$result['error'].':'.$result['message']]);

                $this->error2('使用失敗：'.$result['message']);
            }
        }*/
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
    public function refreshHB(){
        $id= $this->request->get('id',0,'intval');
        //echo $id;exit;
        $info = Db::table('info_coupon')
                ->alias('ic')
                ->join('info_cate_type ict','ic.type_id = ict.id')
                ->field('ic.*,ict.img,ict.time_limit')
                ->where('ic.uid',$id)
                ->where('ic.status','>',0)
                ->where('ic.status','<',3)
                ->order('get_time desc,id desc')
                ->select();
        // foreach ($info as  $v) {
        //     $map = Db::table('info_cate_type')
        //         ->where('id',$v['type_id'])
        //         ->find();
        //     $info->img = $map->type_id;
        // }
        if(count($info)>0){
            return json_encode($info,JSON_UNESCAPED_UNICODE);
        }else{

        }
    }
}

