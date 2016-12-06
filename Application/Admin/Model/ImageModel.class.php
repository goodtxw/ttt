<?php
namespace Admin\Model;
use Think\Model\RelationModel;

class ImageModel extends RelationModel
{
    protected $pk = 'id';

    protected $_link = array(
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_name' => 'User',
            'class_name' => 'User',
            'foreign_key' => 'u_id',
            'as_fields' => 'name:username'
        ),
    );
}