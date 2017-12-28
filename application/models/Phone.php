<?php
namespace Res\Model;

/**
* Phone
*/
class Phone extends MY_Model
{
    const TABLE = 'phones';
    const COLUMNS = ['id', 'status', 'status_description', 'operator', 'modify_time', 'type', 'net_type', 'os', 'screen_size', 'resolution', 'label', 'ram', 'imei', 'deleted',];

}
