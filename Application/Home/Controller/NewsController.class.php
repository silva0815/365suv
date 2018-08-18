<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 1:22
 */

namespace Home\Controller;


use Common\Controller\CommonController;
use Common\Util\MyNewPage;
use Common\Util\MyPage;
use Common\Util\Upload;



class NewsController extends CommonController
{
    public function index()
    {
        $username = I('session.username','');
        $ads=M("Images")->field("title,position,image_path,url")->where(["status"=>1,"position"=>1])->order("position asc,sort desc,id desc")->select();
        $arr['ads'] = $ads;

        $obj=M('News');
        $count=$obj->where(["status"=>1])->count();
        $page=new MyNewPage($count,30);
        $data=$obj->where(["status"=>1])
            ->order('create_at desc')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        foreach($data as $k=>$v){
            $data[$k]['create_at']=date("Y-m-d",$v['create_at']);
        }
        $page->setUrl("/news.html");
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

        $article_type=C("ARTICLE_TYPE");
        $arr['article_type'] = $article_type;

        $this->assign($arr);
        $this->display();
    }




    public function blog_list()
    {

//        $this->assign($arr);
        $this->display();
    }




    public function blog_delete()
    {
        $blog_id=I('blog_id');
        $obj=M('Blog');
        $res=$obj->where("id=$blog_id")->delete();
        if($res){
            $data=1;
        }else{
            $data=0;
        }
        echo $data;
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
        $upload->rootPath="./Public/";
        $upload->savePath='Blog/photo/';
        $upload->subName=array('date', 'Y/m');
        $upload->saveName=date("YmdHis").rand(1000,9999);
        $info=$upload->uploadOne($_FILES['photo']);
        if($info){
        $addres_photo="/Public/".$info['savepath'].$info['savename'];
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