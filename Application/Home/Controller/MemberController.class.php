<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/12
 * Time: 22:03
 */

namespace Home\Controller;


use Common\Controller\CommonController;
use Think\Model;

class MemberController extends CommonController
{
    /*登录*/
    public function login(){
        if(IS_POST){
            $username=I('username','', 'trim');
            $password =md5(I('password','', 'trim'));
            $where_b['username']=$username;
            $where_b['mobile']=$username;
            $where_b['email']=$username;
            $where_b['_logic']='or';
            $where['password']=$password;
            $where[]=$where_b;
            $res=M('User')->where($where)->find();
            if($res){
                if($res['status']==0){
                    $this->error('该用户已冻结！');
                }else{
                    $da['login_at']=time();
                    $da['login_count']=$res['login_at']+1;
                    $update=M('User')->where(['id'=>$res['id']])->save($da);
                    if($update){
                        $user['id']=$res['id'];
                        $user['username']=$res['username'];
                        session('user',$user);
                        if ($pre_url = cookie('pre_url')) {
                            redirect($pre_url,0);
                        } else {
                            redirect('/',0);
                        }

                    }else{
                        $this->error('系统故障，登录失败！');
                    }
                }
            }else{
                $this->error('用户名或密码错误');
            }
        }else{
            $http_host=$_SERVER['HTTP_HOST'];
            $http_referer=$_SERVER['HTTP_REFERER'];
            $index=stripos($http_referer,$http_host)+strlen($http_host);
            $pre_url=substr($http_referer,$index);
            if(!in_array($pre_url,['/login.html','/register.html'])){
                cookie('pre_url',$pre_url);
            }
            $this->display();
        }
    }

    /*登出*/
    public function logout()
    {
        session('user', null);
        cookie('pre_url',null);
        $this->redirect('/', 0);
    }

    /*注册*/
    public function register(){
        if(IS_POST){
            $vcode_request=I('post.vcode');
            $vcode_session=I('session.vcode');
            if(strtolower(trim($vcode_request))==strtolower($vcode_session)){
                $obj=D('User');
                $da=$obj->create();
                $da['password']=md5(I('password'));
                $da['create_at']=time();
                $da['login_at']=time();
                $da['login_count']=1;
                $username=$obj->username;
                $new_id=$obj->data($da)->add();
                if($new_id){
                    $user['id']=$new_id;
                    $user['username']=$username;
                    session('user',$user);
                    session('vcode',null);
                    if ($pre_url = cookie('pre_url')) {
                        redirect($pre_url,0);
                    } else {
                        redirect('/',0);
                    }
                }else{
                    $this->error('系统故障，注册失败');
                }
            }else{
                $this->error('验证码错误');
            }
        }else{
            $this->display();
        }
    }

    public function register_check(){
        if(IS_POST){
            $section=I('request.section');
            switch($section){
                case 'username':
                    $where['username']=I('username');
                    break;
                case 'mobile':
                    $where['mobile']=I('mobile');
                    break;
                case 'email':
                    $where['email']=I('email');
                    break;
            }
            $res=M('User')->where($where)->find();
            if($res){
                echo "false";
            }else{
                echo "true";
            }
        }
    }

}