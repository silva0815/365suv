<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/12
 * Time: 22:03
 */

namespace Home\Controller;


use Common\Controller\CommonController;
use Common\Util\MyPage;


class ArticleController extends CommonController
{
    public function read()
    {
        $pl_id=I("pl","");
        $blog_id=I('bid');
        $obj1=M('Blog as blog');
        $obj1->where("id=$blog_id")->setInc('read_count');
        $data1=$obj1->field('blog.title,blog.bContent,blog.refer,blog.publish_at,
                            blog.read_count,blog.edit_at,
                            user.username as author,user.photo')
                    ->join('left join suv_user as user on blog.author_id=user.id')
                    ->where("blog.id=$blog_id")
                    ->find();
        $data1['comment_count']=M("Comment")->where(["blog_id"=>$blog_id,"status"=>1])->count();

        $arr['pl_id'] = $pl_id;
        $arr['blog_id']=$blog_id;
        $arr['data1']=$data1;
        $this->assign($arr);
        $this->display();
    }

    public function comment_list()
    {
        $blog_id=I('blog_id',0);
        $p=I('p',1);
        $obj=M('Comment as comment');
        $count=$obj->where("comment.blog_id=$blog_id")->count();
        $page=new MyPage($count,5);
        $page->setUrl("/Article/comment_list/blog_id/$blog_id");
        $data=$obj->field('comment.id as comment_id,comment.ccontent,
                             comment.comment_at,user.username as reviewer,user.photo')
            ->join('left join suv_user as user on comment.reviewer_id=user.id')
            ->where("comment.blog_id=$blog_id")
            ->limit($page->firstRow.','.$page->listRows)
            ->order('comment_at asc')
            ->select();

        $page->rollPage=5;
        $page->lastSuffix=false;
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');

        $show=$page->show();
        $arr['listRows']=$page->listRows;
        $arr['nowPage']=$page->nowPage;
        $arr['p']=$p;
        $arr['data']=$data;
        $arr['page']=$show;
        $this->assign($arr);
        $this->display();

    }

    public function comment_save()
    {
        if(IS_POST){
            $user=session('user');

            $reviewer_id=$user['id'];
            $res1=false;
            if($reviewer_id){
                $time=time();
                $obj=M('Comment');
                $da=$obj->create();
                $da['reviewer_id']=$reviewer_id;
                $da['comment_at']=$time;
                $res1=$obj->data($da)->add();
            }
            if($res1){
                $data=1;
            }else{
                $data=0;
            }
        }else{
            $data=0;
        }
        echo $data;
    }
}