<?php

namespace Home\Model;
use \Think\Model\RelationModel;

class AlbumModel extends RelationModel
{
    protected $pk = 'id';

    protected $_link = array(
        'Image' => array(
            'mapping_type' => self::HAS_MANY,
            'mapping_name' => 'Image',
            'class_name' => 'Image',
            'foreign_key' => 'album_id',
        ),
    );
}