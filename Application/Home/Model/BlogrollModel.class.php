<?php
namespace Home\Model;

use Think\Model;

class BlogrollModel extends Model
{
    protected $_auto = [
        ['create_at','time', self::MODEL_INSERT, 'function'],
//        ['update_at','time', self::MODEL_UPDATE, 'function'],
    ];

}