<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 1:22
 */

namespace Home\Controller;


use Common\Controller\CommonController;
use Common\Util\MyPage;
use Common\Util\Upload;

class CenterController extends CommonController
{
    public function index()
    {
        $user=session('user');
        $user_id=$user['id'];
        $obj1=M('Blog');
        $obj2=M('User');
        $obj3=M('Comment');
        $count1=$obj1->where("author_id=$user_id")->count();
        $count2=$obj3->where("reviewer_id=$user_id")->count();
        $data2=$obj2->field('mobile,email,photo,create_at,login_at,login_count')
                 ->where("id=$user_id")
                 ->find();

        $arr['count1']=$count1;
        $arr['count2']=$count2;
        $arr['data2']=$data2;
        $this->assign($arr);
        $this->display();
    }

    public function article_list()
    {
        $user=session('user');
        $user_id=$user['id'];
        $obj=M('Blog as blog');
        $count=$obj->where("author_id=$user_id")->count();
        $page=new MyPage($count,15);
        $data=$obj->field('blog.id as blog_id,title,publish_at,read_count,edit_at')
            ->where("author_id=$user_id")
            ->order('edit_at desc')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        foreach($data as $k=>$v){
            $data[$k]['comment_count']=M("Comment")->where(["blog_id"=>$v['blog_id'],"status"=>1])->count();
        }
        $page->rollPage=5;
        $page->lastSuffix=false;
        $page->setConfig('header','<span class="rows">&nbsp;&nbsp;共<b>%TOTAL_ROW%</b>篇文章/共<b>%TOTAL_PAGE%</b>页</span>');
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show=$page->show();
        $arr['data']=$data;
        $arr['page']=$show;
        $this->assign($arr);
        $this->display();
    }

    public function publish(){
        if(IS_POST){
            $obj=M('Blog');
            $user=session('user');
            $user_id=$user['id'];
            $da=$obj->create();
            $da['author_id']=$user_id;
            $da['publish_at']=time();
            $da['edit_at']=time();
            $res=$obj->data($da)->add();
            if($res){
                $data=$res;
            }else{
                $data=0;
            }
            echo $data;
        }else{
            $this->display();
        }
    }

    public function edit()
    {
        if(IS_POST){
            $blog_id=I('id');
            $obj=M('Blog');
            $da=$obj->create();
            $da['edit_at']=time();
            $res=$obj->where("id=$blog_id")->data($da)->save();
            if($res!==false){
                $data=1;
            }else{
                $data=0;
            }
            echo $data;
        }else{
            $user=session('user');
            $user_id=$user['id'];
            $username=$user['username'];
            $blog_id=I('bid');
            $obj=M('Blog');
            $data=$obj->field('author_id,title,bContent,type,refer')
                ->where("id=$blog_id")
                ->find();
            if($data['author_id']!=$user_id){
                $this->error('无权限访问！',"/user/$username");
                exit;
            }
            $arr['blog_id']=$blog_id;
            $arr['data']=$data;
            $this->assign($arr);
            $this->display();
        }
    }



    public function article_delete()
    {
        $blog_id=I('bid');
        $obj=M('Blog');
        $res=$obj->where("id=$blog_id")->delete();
        if($res){
           echo 1;
        }else{
           echo 0;
        }
    }


    public function photoSubmitCheck()
    {
        $user_id=I('session.user_id');
        $obj=M('User');
        $arr=$obj->field('photo')->where("id=$user_id")->find();
        echo $arr['photo'];
    }


    public function photo_save()
    {
        $user_id=I('session.user_id');
        $upload=new Upload();
        $upload->mimes=array('image/png','image/jpeg');
        $upload->exts=array('png','jpeg','jpg');
        $upload->maxSize=30*1024;
        $upload->rootPath="./Uploads/";
        $upload->savePath='photo/';
        $upload->subName=array('date', 'Y/m');
        $upload->saveName=date("YmdHis").rand(1000,9999);
        $info=$upload->uploadOne($_FILES['photo']);
        if($info){
        $addres_photo="/Uploads/".$info['savepath'].$info['savename'];
         $obj=M('User');
            $data=$obj->where("id=$user_id")->find();
            $obj->where("id=$user_id")->setField('photo',$addres_photo);
            if($data['photo']){

                $address_photo_old=WWW_DIR.$data['photo'];

//                file_put_contents("a.log",$address_photo_old,FILE_APPEND);

                unlink($address_photo_old);

            }
                $tip='上传成功！';

            $this->address_photo=$addres_photo;
        }else{
            $tip=$upload->getError();

        }

        $this->tip=$tip;
        $this->display();

    }

}