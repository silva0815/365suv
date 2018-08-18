<?php
/*
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/4
 * Time: 22:03
 * imagecreate:创建新的画布
 * imagecolorallocate:定义背景色
 * imagefill:为画布填充背景色
 * imagestring:在图片中嵌入文字
 * imagesetpixel:在图片中插入像素点
 * imagepng:输出png图片
 * imagedestroy:删除图片
 */

header("content-type:image/png");
function create_vcode($text,$fontsize=22){//设置了默认文字大小为14
//创建画布(验证码背景)；
    $width=$fontsize*4+20;
    $height=$fontsize+18;
    $img=imagecreate($width,$height);
    $bg=imagecolorallocate($img,188,192,189);
    imagefill($img,2,2,$bg);
//创建验证码；
for($i=0;$i<strlen($text);$i++){
    $x_f=$fontsize*$i+8;
    $y_f=$height-8;
    $angle=rand(-30,30);
    $color=imagecolorallocate($img,0,rand(0,100),rand(0,200));
    imagettftext($img,$fontsize,$angle,$x_f,$y_f,$color,'msyh.ttf',$text[$i]);
}

//创建干扰点
for($i=0;$i<100;$i++){
    $x_p=rand(0,$width);
    $y_p=rand(0,$height);
    $color2=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
    imagesetpixel($img,$x_p,$y_p,$color2);
}

//创建干扰线条
for($i=0;$i<20;$i++){
    $x1=rand(0,$width);
    $y1=rand(0,$height);
    $line=rand(5,20);
    $x2=$x1+$line*(rand(-100,100)/100);
    $y2=$y1+$line*(rand(-100,100)/100);
    $color3=imagecolorallocate($img,rand(0,255),rand(0,255),rand(0,255));
    imageline($img,$x1,$y1,$x2,$y2,$color3);
}
//输出图像到缓冲区
imagepng($img);
//销毁图像
imagedestroy($img);

}
//验证码内容候选范围
$strs= "QWERTYUIPASDFGHJKLZXCVBNM3456789abcdefghijkmnpqrstuvwxy";
//创建验证码内容
$text='';
for($i=0;$i<4;$i++){
    $text.=$strs[rand(0,strlen($strs)-1)];
}
session_start();
$_SESSION['vcode']=$text;
create_vcode($text);

