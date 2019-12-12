<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/9/009
 * Time: 17:22
 */

//$url = 'https://www.cottonfield.top/admin/info/errorivodel';

class aa
{
    function crul($url = '')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        curl_setopt($ch, CURLOPT_POST, 1); //启用POST提交

        $file_contents = curl_exec($ch);

        curl_close($ch);
        return $file_contents;
    }

}
$curl=new aa();
print_r($curl->crul('https://www.cottonfield.top/admin/info/errorivodel'));
print_r($curl->crul('https://www.cottonfield.top/admin/info/checkInvoice'));
exit();