<?php

namespace Admin\Model;
use \Think\Model\RelationModel;

class ChatModel extends RelationModel
{
    protected $_link = array(
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_name' => 'User',
            'class_name' => 'User',
            'foreign_key' => 'u_from_id',
        ),
        'User1' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_name' => 'User1',
            'class_name' => 'User',
            'foreign_key' => 'u_to_id',
        ),
    );
}