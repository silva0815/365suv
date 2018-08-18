<?php
namespace Home\Controller;


use Common\Controller\CommonController;
use Common\Util\MyNewPage;
use Common\Util\Upload;

class AdminController extends CommonController
{
    public function login(){
        if(IS_POST){
           $username=I("username",'',"trim");
           $password=I("password",'',"trim");
            $pass=md5("365suv".md5($password));
            $res=M("Admin")->where(['username'=>$username,'password'=>$pass])->find();
            if(!$res){
                $this->error("用户名或密码不正确！");
            }else{
                $admin=['id'=>$res["id"],'username'=>$res["username"]];
                session("admin",$admin);
               $this->success("登录成功","/admin.html");
            }
        }else{
            $this->display();
        }
    }

    public function clearCache(){


        $path=WWW_DIR.'/../Runtime/';
        delete_file($path);

        call_user_func("opcache_reset");
        $this->success('清除成功！','/admin.html');
    }

    public function pw_change(){
        if(IS_POST){
            $admin=session('admin');
            $uid=$admin['id'];
            $old_pw=I("old_pw","","trim");
            $new_pw=I("new_pw","","trim");
            $con_pw=I("con_pw","","trim");
            if($new_pw!=$con_pw){
                $this->error("两次输入的新密码不一致！");
            }else{
                $da=M("Admin")->where(["id"=>$uid])->find();
                if(md5($old_pw)!=$da['password']){
                    $this->error("旧密码不正确！");
                }else{
                    $res=M("Admin")->where(['id'=>$uid])->setField("password",md5("365suv".md5($new_pw)));
                    if($res){
                        $this->success("修改成功","/Admin/info.html");
                    }else{
                        $this->error("修改失败！");
                    }
                }
            }
        }else{
            $this->display();
        }
    }


    public function logout(){
        session('admin',null);
        $this->success("退出成功","/Admin/login.html");
    }


    public function user_list(){
        $mod=M("User");
        $count=$mod->count();
        $page=new MyNewPage($count,10,null);
        $res=$mod->field(true)->order("id desc")->limit($page->firstRow.",".$page->listRows)->select();
        $data['user_list']=$res;
        $page->setUrl("/Admin/user_list.html");
        $page->rollPage=6;
        $page->lastSuffix=false;
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $nowPage=$page->nowPage;
        $listRows=$page->listRows;
        $data['nowPage']=$nowPage;
        $data['listRows']=$listRows;
        $show=$page->show();
        $data['page']=$show;
        $this->assign($data);
        $this->display();
    }


    public function change_user_status(){
        $id=I('id');
        $status=I('status',1);
        $backUrl=$_SERVER['HTTP_REFERER'];
        $da=['status'=>$status];
        $res=M("User")->where("id=$id")->save($da);
        if($res){
            $this->success("修改成功！",$backUrl);
        }else{
            $this->error('修改失败！');
        }
    }


    public function image_list(){
        $position=I("position",0);
        $where="";
        $position &&  $where["position"]=$position;
        $mod=M("Images");
        $count=$mod->where($where)->count();
        $page=new MyNewPage($count,10,null);
        $res=$mod->field(true)->where($where)->order("id desc")->limit($page->firstRow.",".$page->listRows)->select();
        foreach($res as $k=>$v){
            $res[$k]['position']=C("IMAGE_POSITION")[$v['position']];
            $res[$k]['create_at']=date("Y-m-d H:i:s",$v['create_at']);
            $res[$k]['update_at']=$v['update_at'] ? date("Y-m-d H:i:s",$v['update_at']) : "";

        }
        $data['image_list']=$res;
        $page->setUrl("/admin/image_list.html");
        $page->rollPage=6;
        $page->lastSuffix=false;
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $nowPage=$page->nowPage;
        $listRows=$page->listRows;
        $show=$page->show();
        $data['nowPage']=$nowPage;
        $data['listRows']=$listRows;
        $data['page']=$show;

        $data['image_position']=C("IMAGE_POSITION");
        $data['position']=$position;




        $this->assign($data);
        $this->display();
    }

    public function image_add(){
        if(IS_POST){
            $mod=D("images");
            $mod->create();
            $res=$mod->add();
            if($res){
                $this->success("添加成功！","image_list");
            }else{
                $this->error("添加失败！");
            }
        }else{
            $image_position=C("IMAGE_POSITION");
            $this->assign("image_position",$image_position);
            $this->display();
        }
    }

    public function image_edit(){
        if(IS_POST){
            $mod=D("images");
            $mod->create();
            $res=$mod->save();
            if($res){
                $this->success("修改成功！","image_list");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $id=I("id");
            $res=M("images")->where(["id"=>$id])->find();
            $image_position=C("IMAGE_POSITION");
            $this->assign("image_position",$image_position);
            $this->assign("data",$res);
            $this->display();
        }

    }

    public function image_upload(){

        $upload=new Upload();
        $upload->mimes=array('image/png','image/jpeg');
        $upload->maxsize=1024*1024;
        $upload->exts=array('jpg','png','jpeg');
        $upload->rootPath="./Uploads/";
        $upload->savePath='image/';
        $upload->subName=array('date', 'Y');
        $upload->saveName=date("YmdHis").rand(1000,9999);
        $res=$upload->uploadOne($_FILES['upload']);
        if($res){
            $da['code']=1;
            $da['file_url']='/Uploads/'.$res['savepath'].$res['savename'];
        }else{
            $da['code']=0;
            $da['error']=$upload->getError();
        }

        echo json_encode($da);
    }

    public function change_img_status(){
        $id=I('id');
        $status=I('status',1);
        $backUrl=$_SERVER['HTTP_REFERER'];
        $da=['status'=>$status,"update_at"=>time()];
        $res=M("Images")->where("id=$id")->save($da);
        if($res){
            $this->success("修改成功！",$backUrl);
        }else{
            $this->error('修改失败！');
        }
    }



    public function blog_list(){
        $mod=M("Blog as b");
        $count=$mod->count();
        $page=new MyNewPage($count,10,null);
        $res=$mod
            ->field("b.id,b.title,b.type,b.publish_at,b.edit_at,b.read_count,b.status,u.username")
            ->join("suv_user as u on u.id=b.author_id","left")
            ->order("b.id desc")
            ->limit($page->firstRow.",".$page->listRows)
            ->select();
        foreach($res as $k=>$v){
            $res[$k]['type']=C("ARTICLE_TYPE")[$v['type']];
            $res[$k]['publish_at']=date("Y-m-d H:i:s",$v['publish_at']);
            $res[$k]['edit_at']=$v['edit_at'] ? date("Y-m-d H:i:s",$v['edit_at']) : "";
            $res[$k]['comment_count']=M("Comment")->where(["blog_id"=>$v['id'],"status"=>1])->count();

        }
        $data['blog_list']=$res;
        $page->setUrl("/Admin/blog_list.html");
        $page->rollPage=6;
        $page->lastSuffix=false;
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $nowPage=$page->nowPage;
        $listRows=$page->listRows;
        $show=$page->show();
        $data['nowPage']=$nowPage;
        $data['listRows']=$listRows;
        $data['page']=$show;
        $this->assign($data);
        $this->display();
    }

    public function change_blog_status(){
        $id=I('id');
        $status=I('status',1);
        $backUrl=$_SERVER['HTTP_REFERER'];
        $da=['status'=>$status,"edit_at"=>time()];
        $res=M("Blog")->where("id=$id")->save($da);
        if($res){
            $this->success("修改成功！",$backUrl);
        }else{
            $this->error('修改失败！');
        }
    }

    public function comment_list(){
        $mod=M("Comment as c");
        $count=$mod->count();
        $page=new MyNewPage($count,10,null);
        $res=$mod
            ->field("c.id,c.ccontent,c.blog_id,c.comment_at,c.status,u.username,b.title")
            ->join("suv_user as u on u.id=c.reviewer_id","left")
            ->join("suv_blog as b on b.id=c.blog_id","left")
            ->order("c.id desc")
            ->limit($page->firstRow.",".$page->listRows)
            ->select();
        foreach($res as $k=>$v){
            $res[$k]['ccontent']=htmlspecialchars_decode($v['ccontent']);
            $res[$k]['comment_at']=date("Y-m-d H:i:s",$v['comment_at']);
        }
        $data['comment_list']=$res;
        $page->setUrl("/Admin/comment_list.html");
        $page->rollPage=6;
        $page->lastSuffix=false;
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $nowPage=$page->nowPage;
        $listRows=$page->listRows;
        $show=$page->show();
        $data['nowPage']=$nowPage;
        $data['listRows']=$listRows;
        $data['page']=$show;
        $this->assign($data);
        $this->display();
    }
    public function change_com_status(){
        $id=I('id');
        $status=I('status',1);
        $backUrl=$_SERVER['HTTP_REFERER'];
        $da=['status'=>$status];
        $res=M("Comment")->where("id=$id")->save($da);
        if($res){
            $this->success("修改成功！",$backUrl);
        }else{
            $this->error('修改失败！');
        }
    }

    public function blogroll_list(){
        $mod=M("Blogroll");
        $count=$mod->count();
        $page=new MyNewPage($count,10,null);
        $res=$mod->field(true)->order("id desc")->limit($page->firstRow.",".$page->listRows)->select();
        foreach($res as $k=>$v){
            $res[$k]['create_at']=date("Y-m-d H:i:s",$v['create_at']);
        }
        $data['blogroll_list']=$res;
        $page->setUrl("/admin/blogroll_list.html");
        $page->rollPage=6;
        $page->lastSuffix=false;
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','末页');
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $nowPage=$page->nowPage;
        $listRows=$page->listRows;
        $show=$page->show();
        $data['nowPage']=$nowPage;
        $data['listRows']=$listRows;
        $data['page']=$show;
        $this->assign($data);
        $this->display();
    }

    public function blogroll_add(){
        if(IS_POST){
            $mod=D("Blogroll");
            $mod->create();
            $res=$mod->add();
            if($res){
                $this->success("添加成功！","blogroll_list");
            }else{
                $this->error("添加失败！");
            }
        }else{
            $this->display();
        }
    }

    public function blogroll_edit(){
        if(IS_POST){
            $mod=D("Blogroll");
            $mod->create();
            $res=$mod->save();
            if($res){
                $this->success("修改成功！","blogroll_list");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $id=I("id");
            $res=M("Blogroll")->where(["id"=>$id])->find();
            $this->assign("data",$res);
            $this->display();
        }

    }

    public function change_br_status(){
        $id=I('id');
        $status=I('status',1);
        $backUrl=$_SERVER['HTTP_REFERER'];
        $da=['status'=>$status];
        $res=M("Blogroll")->where("id=$id")->save($da);
        if($res){
            $this->success("修改成功！",$backUrl);
        }else{
            $this->error('修改失败！');
        }
    }


//    public function user_del(){
//        $id=I("id");
//        $res=M("User")->where("id=$id")->delete();
//        if($res){
//            $this->success("删除成功！","user_list");
//        }else{
//            $this->error("删除失败！");
//        }
//
//    }

}