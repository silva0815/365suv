<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/14
 * Time: 11:42
 */

namespace Home\Behavior;


use Think\Behavior;

class SetBehavior extends Behavior
{
    public function run(&$params)
    {
        $this->checkFrontLogin();
        $this->checkBackLogin();
    }

    private function checkFrontLogin(){
        if(!$_SESSION['user']){
            if(MODULE_NAME=='Home'&& CONTROLLER_NAME=='Center'){
                redirect('/',0);
                exit;
            }
        }
    }

    private function checkBackLogin(){
        if(!$_SESSION['admin']){

            if(CONTROLLER_NAME=='Admin' && ACTION_NAME!='login'){
                $str = <<< 'TEXT'
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<a href="/Admin/login.html" target="_parent" id="jumpToLogin"></a>
<script type="text/javascript">
document.getElementById('jumpToLogin').click();
</script>
TEXT;
                echo $str;
                exit;
            }
        }
    }
}