<?php
namespace Home\Controller;

use Common\Controller\CommonController;
use Common\Util\MyPage;


class IndexController extends CommonController
{
    public function index()
    {
        $obj_b=M('Blog as b');
        $obj_c=M('Comment as c');

        $search = I('get.search','');
        $type = I('type',0);
        $con1['title|bcontent']=array('like',"%$search%");
        $con2['type']=$type;



        if ($type != '') {
            if ($search != ''){
                $con2[]=[$con1];
                $con=$con2;
            } else {
                $con=$con2;
            }
            $url="t{$type}";

        } else {
            $con= $search ? $con1 : [];
            $url="main";

        }
        $con["b.status"]=1;
        $count=$obj_b->join('left join suv_user as u on b.author_id=u.id')->where($con)->count();

        $page=getPage($count,12,$_GET,$url,5);

        $data = $obj_b->field('b.id as blog_id,b.title,b.bContent,
                   b.publish_at,b.read_count,b.edit_at,
                  u.username as author,u.photo')
            ->join('left join suv_user as u on b.author_id=u.id')
            ->where($con)
            ->order('b.edit_at desc')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();


        foreach($data as $k=>$var){
            $var['bcontent']=strip_tags(htmlspecialchars_decode($var['bcontent']));
            if(mb_strlen($var['bcontent'],'utf-8')>200){
                $data[$k]['summary']=mb_substr($var['bcontent'],0,200,'utf-8')."...";
            }else{
                $data[$k]['summary']=mb_substr($var['bcontent'],0,200,'utf-8');
            }

            if ($search){
                $data[$k]['title']=preg_replace("/($search)/i",'<span style="color: red">$1</span>',$var['title']);
                $data[$k]['summary']=preg_replace("/($search)/i",'<span style="color: red">$1</span>',$data[$k]['summary']);
            }
            $data[$k]['comment_count']=M("Comment")->where(["blog_id"=>$var['blog_id'],"status"=>1])->count();
        }

        $data2=$obj_c->field('c.id as comment_id,c.blog_id,c.ccontent,c.comment_at,u.username as reviewer')
            ->join('left join suv_user as u on c.reviewer_id=u.id')
            ->order('c.comment_at desc')
            ->where("c.status=1")
            ->limit(0,14)
            ->select();

        foreach($data2 as $k=>$var){
            $data2[$k]['ccontent']=htmlspecialchars_decode($var['ccontent']);
        }

        $this->insertNews();
        $arrNews=M("News")->field("url,title,create_at")->order("create_at desc")->limit(0,8)->select();


        $show=$page->show();


        $images=M("Images")->field("title,position,image_path,url")->where(["status"=>1])->order("position asc,sort desc,id desc")->select();
        $ads=[];
        foreach($images as $k => $v){
            switch($v['position']){
                case 1 :
                    $ads["banner"][]=$v;
                    break;
                case 2 :
                    $ads["slideshows"][]=$v;
                    break;
                case 3 :
                    $ads["right_top"][]=$v;
                    break;
                case 4 :
                    $ads["right_middle"][]=$v;
                    break;
                case 5 :
                    $ads["right_bottom"][]=$v;
                    break;
            }
        }

        $blogrolls=M("Blogroll")->field("title,image_path,url")->where(["status"=>1])->limit(6)->order("sort desc,id desc")->select();

        $output['ads'] = $ads;
        $output['blogrolls'] = $blogrolls;
        $output['type'] = $type;
        $output['search'] = $search;
        $output['data_b'] = $data;
        $output['data_c']=$data2;
        $output['arrNews']=$arrNews;
        $output['page']=$show;
        $this->assign($output);
        $this->display();

    }

    public function insertNews()
    {
        $res = M("News")->field("create_at")->order("create_at desc")->find();
        $last_time = $res['create_at'] ? $res['create_at'] : 0;
        $time_interval = time() - $last_time;
        if ($time_interval > 10800) {
            $contentNews = file_get_contents('http://car.bitauto.com/suv/all/');
            $pat1 = '/<h2>SUV热门文章<\/h2>(.*)<h2>SUV精彩问答<\/h2>/s';
            preg_match($pat1, $contentNews, $content1);
            $pat2 = '/<a href="(.*?)"target="_blank">(.*?)<\/a>/s';
            preg_match_all($pat2, $content1[1], $content2, PREG_SET_ORDER);
            if (!empty($content2)) {
                $arrNews = [];
                foreach ($content2 as $k => $v) {
                    $arrNews[$k]['url'] = $v[1];
                    $arrNews[$k]['title'] = strip_tags($v[2]);
                    $arrNews[$k]['create_at'] = time();
                }
                if (!empty($arrNews)) {
                    foreach ($arrNews as $v) {
                        if ($v['url'] && $v['title'] && (!M("News")->where("url='{$v['url']}'")->find())) {
                            M("News")->add($v);
                        }
                    }
                }
            }
        }
    }
    public function insertNews2(){
        $res=M("News")->field("create_at")->order("create_at desc")->find();
        $last_time= $res['create_at'] ? $res['create_at'] : 0;
        $time_interval=time()-$last_time;
        if($time_interval>10800){
            $contentNews = file_get_contents('http://car.bitauto.com/suv/all/');
            $div1='<h2>SUV热门文章<\/h2>';
            $div2='(?:.*?)<h2>SUV精彩问答<\/h2>';
            $aa='(?:.*?)<a href="(.*?)"target="_blank">(.*?)<\/a>';

            $pat2='/'.$div1.$aa.$aa.$aa.$aa.$aa.$aa.$aa.$aa.$div2.'/s';
            preg_match_all($pat2,$contentNews,$rs);
            if(!empty($rs)){
                $arrNews=[];
                for($i=1;$i<=8;$i++){
                    $j=$i*2;
                    $arrNews[$i]['url']=$rs[$j-1][0];
                    $arrNews[$i]['title']=$rs[$j][0];
                    $arrNews[$i]['create_at']=time();
                }

                if(!empty($arrNews)){
                    foreach($arrNews as $v){
                        if($v['url'] && $v['title'] && (!M("News")->where("url='{$v['url']}'")->find())){
                            M("News")->add($v);
                        }
                    }
                }
            }
        }
    }


    public function sendMiniAppsData(){
        $arr['code']=200;
        $arr['data']=[
            [
                'id'=>'1',
                'img'=>'/images/pro_01.jpg',
                'title'=>'标题001',
                'shortDesc'=>'这是标题001的简单描述这是标题的简单描述这是标题的简单描述这是标题的简单描述'
            ],
            [
                'id'=>'2',
                'img'=>'/images/pro_02.jpg',
                'title'=>'标题002',
                'shortDesc'=>'这是标题002的简单描述这是标题的简单描述这是标题的简单描述这是标题的简单描述'
            ],
            [
                'id'=>'3',
                'img'=>'/images/pro_03.jpg',
                'title'=>'标题003',
                'shortDesc'=>'这是标题003的简单描述这是标题的简单描述这是标题的简单描述这是标题的简单描述'
            ]
        ];

        $jsonData=json_encode($arr);
        echo $jsonData;
    }
}