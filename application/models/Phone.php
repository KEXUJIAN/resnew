<?php
namespace Res\Model;

/**
* Phone
*/
class Phone extends MY_Model
{
    const COLUMNS = [
        'id' => 'int',
        'status' => 'int',
        'status_description' => 'string',
        'operator' => 'string',
        'modify_time' => 'string',
        'type' => 'string',
        'net_type' => 'string',
        'os' => 'string',
        'screen_size' => 'string',
        'resolution' => 'string',
        'label' => 'string',
        'ram' => 'string',
        'imei' => 'string',
        'deleted' => 'string',
    ];
    const TABLE = 'phone';
}
