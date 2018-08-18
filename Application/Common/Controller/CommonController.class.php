<?php
namespace Common\Controller;

use Think\Controller;

class CommonController extends Controller
{
    protected function _initialize()
    {
        $this->setCommonData();
    }

    private function setCommonData(){
        $banner=M("Images")->field("image_path,url")->where(["status"=>1,'position'=>1])->order("position asc,sort desc,id desc")->find();
        $output['banner']=$banner;

        $article_type=getParam('ARTICLE_TYPE');
        $output['article_type']=$article_type;

        $user=session('user');
        if($user){
            $output['user'] = $user;
        }


        $this->assign($output);
    }
}