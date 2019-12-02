<?php

/**
 *foreach遍历后unset删除,这种方法也是最容易想到的方法
 */
function delByValue($arr, $value){
    if(!is_array($arr)){
        return $arr;
    }
    foreach($arr as $k=>$v){
        if($v == $value){
            unset($arr[$k]);
        }
    }
    return $arr;
}

//把一个数组其中某个字段作为key 后返回
function array_under_reset($array, $key, $type=1){
    if (is_object($array)) {
        $array = $array->toArray();
    }

    if (is_array($array)){
        $tmp = array();
        foreach ($array as $v) {
            if ($type === 1){
                $tmp[$v[$key]] = $v;
            }elseif($type === 2){
                $tmp[$v[$key]][] = $v;
            }
        }
        return $tmp;
    }else{
        return $array;
    }
}
function array_reset($array, $key, $type=1){
    if (is_object($array)) {
        $array = $array->toArray();
    }

    if (is_array($array)){
        $tmp = array();
        foreach ($array as $v) {
            if($type!=1 && isset($v[$type])){
                $tmp[$v[$key]] = $v[$type];
            }elseif($type === 2){
                $tmp[$v[$key]][] = $v;
            }else{
                $tmp[$v[$key]] = $v;
            }
        }
        return $tmp;
    }else{
        return $array;
    }
}


/**
 * 判断当前访问的用户是  PC端  还是 手机端  返回true 为手机端  false 为PC 端
 * @return boolean
 * 是否移动端访问访问
 * @return bool
 */
function isMobile(){
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}

/**
 * 从当前URL中去掉某个参数之后的URL
 * @param unknown_type $param
 * 可以查看 hmb 的用法
 */
function filterUrl($param){
    // 先取出当前的URL地址
    $url = $_SERVER['PHP_SELF'];
    // 正则去掉某个参数
    $re = "/\/$param\/[^\/]+/";
    return preg_replace($re, '', $url);
}

//中文字串截取无乱码
function getSubstr($string, $start, $length) {
    if(mb_strlen($string,'utf-8')>$length){
        $str = mb_substr($string, $start, $length,'utf-8');
        return $str.'...';
        //return $str;
    }else{
        return $string;
    }
}

/**
 *用户名、邮箱、手机账号中间字符串以 * 隐藏
 **/
function hideStar($str) {
    if (strpos($str, '@')) {
        $email_array = explode("@", $str);
        $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3); //邮箱前缀
        $count = 0;
        $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count);
        $rs = $prevfix . $str;
    } else {
        $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i';
        if (preg_match($pattern, $str)) {
            $rs = preg_replace($pattern, '$1****$2', $str); // substr_replace($name,'****',3,4);
        } else {
            $rs = substr($str, 0, 3) . "***" . substr($str, -1);
        }
    }
    return $rs;
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
    $config = Db::name('config')->where('id',1)->find();
    vendor('PHPMailer.PHPMailerAutoload');  //从PHPMailer目录导class.phpmailer.php类文件
    $mail             = new \PHPMailer();   //PHPMailer对象
    $mail->CharSet    = 'UTF-8';  //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';                  // 使用安全协议
    $mail->Host       = $config['sms'];         // SMTP 服务器
    $mail->Port       = $config['smdk'];        // SMTP服务器的端口号
    $mail->Username   = $config['smuser'];      // SMTP服务器用户名
    $mail->Password   = $config['smpwd'];       // SMTP服务器密码
    $mail->SetFrom($config['sendemail'], $config['smname']);
    $mail->Subject    = $subject;
    $mail->Body    = $body;
    $mail->isHTML(true);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

// 返回数组数据
function return_error($msg='',$data=null){
    $array = array(
        'code' => 0,
        'msg' => $msg,
        'data' => $data,
    );
    return $array;
}
function return_success($msg='',$data=null){
    $array = array(
        'code' => 1,
        'msg' => $msg,
        'data' => $data,
    );
    return $array;
}


// use think\Validate;
// 验证码校验
// $result = check_captcha();
// if($result!==true)      $this->error($result);
function check_captcha(){
    $validate = Validate::make([
        'code|验证码'   => 'require|captcha',//验证码模块
    ]);
    $data = request()->only(['code']);

    $result = $validate->check($data);
    if($result!==true)      return $validate->getError();
    else                    return true;
}

// 表单令牌校验
// $result = check_token();
// if($result!==true)      $this->error($result);
function check_token(){
    $validate = Validate::make([
        '__token__|表单令牌'=>'require|token'
    ]);

    $data = request()->only(['__token__']);

    $result = $validate->check($data);
    if($result!==true)      return $validate->getError();
    else                    return true;
}

//日志记录///////////////
function logger($log_content,$log_type='log') {
    $log_content = print_r($log_content,true);//数组、对象也转为字符串
    $log_content = date('H:i:s')."  ".$log_content."\r\n";

    $max_size = 10000;
    $log_filename = "log/".$log_type.date('-Ymd').".txt";

    file_put_contents($log_filename, $log_content, FILE_APPEND);
}


/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}