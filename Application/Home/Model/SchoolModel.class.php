<?php

namespace Home\Model;
use \Think\Model\RelationModel;

class SchoolModel extends RelationModel
{
    protected $pk = 'id';

    protected $_link = array(
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_name' => 'User',
            'class_name' => 'User',
            'foreign_key' => 'u_id',
        ),
    );
}