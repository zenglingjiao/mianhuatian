<?PHP
namespace app\admin\model;
use think\facade\Validate;
use app\admin\model\Systems;

// 邮件发送接口
class Emails
{
    public static function send_email($eamil='',$project='',$content='') {
        if( empty($eamil) || !Validate::isEmail('thinkphp@qq.com') )         return '收件人邮箱格式错误';

        $sys = Systems::getParam();
        if( ($sys['email_isopen']??'0') != 1 )      return '邮箱发送未开启';
        // var_dump($sys);

        // 发件人信息
        $mail = new \phpmailer\phpmailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host = $sys['email_ser']??''; //smtp服务器的名称（这里以163邮箱为例）
        $mail->SMTPAuth = true; //启用smtp认证
        $mail->Username = $sys['email_user']??''; //发件人邮箱名
        $mail->Password = $sys['email_pwd']??'' ; //163邮箱发件人授权密码
        $mail->From = $sys['email_user']??''; //发件人地址（也就是你的邮箱地址）
        $mail->FromName = $sys['email_nick']??''; //发件人姓名

        // 收件人信息
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML(true); // 是否HTML格式邮件
        $mail->CharSet = 'utf-8'; //设置邮件编码

        $mail->AddAddress($eamil);
        $mail->Subject =$project; //邮件主题
        $mail->Body = $content; //邮件内容
        $mail->AltBody = "邮件内容为HTML格式，请用网页打开"; //邮件正文不支持HTML的备用显示

        return($mail->Send());
    }
}

