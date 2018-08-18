<?php
namespace Home\Model;
use Think\Model;

class BlogModel extends Model
{
    protected $_auto=array(
//        array('create_at','getD',self::MODEL_INSERT,'function'),
        array('publish_at','time',self::MODEL_INSERT,'callback'),
        array('edit_at','time',self::MODEL_UPDATE,'callback')
//        array('school','某某大学',self::MODEL_INSERT,'string'),
//        array('sex','男',self::MODEL_INSERT,'string')
    );


//    protected $_validate=array(
//        array('name','require','用户名不能为空'),
//        array('name','0,10','姓名不得大于10个字符',self::MUST_VALIDATE,'length'),
//        array('age','number','年龄必须是数字'),
//        array('sex',['男','女'],'性别输入不正确',self::MUST_VALIDATE,'in'),
//        array('mobile','/^\d{11}$/','手机号码输入不正确',self::MUST_VALIDATE,'regex'),
//        array('name','checkRealname','字符长度不得小于6位',self::MUST_VALIDATE,'callback'),
//        array('email','email','邮箱格式不正确')

//    );

//    protected function checkRealname($aa)
//    {
//      $len=strlen($aa);
//        if($len<6){
//            return false;
//        }else{
//            return true;
//        }
//    }










}