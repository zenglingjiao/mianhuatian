<?php
namespace app\admin\model;
use think\Container;
use think\Validate;
use think\Model;
use think\View;
use app\admin\model\BaseModel;

class InfoBanners extends BaseModel
{
    protected $pk = 'id';
    protected $table = 'info_banner';
    protected $order = 'id desc';

    protected $readonly = ['id'];
    protected $type = [
        'id'    =>  'integer',
        'title'     =>  'string',
        'img'     =>  'string',
        'url'     =>  'string',

        'add_time'  =>  'datetime',
        'update_time'  =>  'datetime',
    ];
    protected $validate_rule = [
        'id|ID'         => 'number',

        'title'     =>  'min:0|max:200',
        'url'     =>  'min:0|max:2000',

        // 'img'     =>  'require',
        'img'     =>  'min:0|max:2000',
    ];
    
    public $limit = 10;
}