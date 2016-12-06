<?php
namespace Home\Model;

use Think\Model\RelationModel;

class ChatnowModel extends RelationModel
{
    protected $_link = array(
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_name' => 'User',
            'class_name' => 'User',
            'foreign_key' => 'chat_id',
        ),
    );
}