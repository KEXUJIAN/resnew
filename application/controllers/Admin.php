<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/1
 * Time: 11:54
 */

use \Res\Model\Phone;
use \Res\Model\SimCard;
use \Res\Model\User;
use \Res\Model\UploadFile;
use \Res\Util\MyExcel;

class Admin extends CI_Controller
{
    public function console()
    {
        App::view('admin/console');
    }

    public function new($name)
    {
        switch ($name) {
            case 'user':
                App::view('admin/user-new');
                break;
            case 'phone':
                App::view('admin/phone-new');
                break;
            case 'simcard':
                App::view('admin/simcard-new');
                break;
            default:
        }
    }

    public function data($name)
    {
        $response = [];
        switch ($name) {
            case 'user':
                $response += $this->dataUsers();
                break;
            case 'phone':
                $response += $this->dataPhones();
                break;
            case 'simcard':
                $response += $this->dataSimCards();
                break;
            default:
                $response += [
                    'result' => false,
                ];
        }
        echo json_encode($response);
    }

    public function save($name)
    {
        $response = [];
        switch ($name) {
            case 'user':
                $response += $this->saveUser();
                break;
            case 'phone':
                $response += $this->savePhone();
                break;
            case 'simcard':
                $response += $this->saveSimCard();
                break;
            default:
                $response += [
                    'result' => false,
                ];
        }
        echo json_encode($response);
    }

    public function upload($name)
    {
        $response = [];
        $files = $_FILES['files'] ?? [];
        if (!$files) {
            $response['result'] = 'false';
            echo json_encode($response);
            return;
        }
        $uploader = AppService::getUploader();
        $error = $uploader->check($files);
        if ($error) {
            $response['result'] = false;
            $response += $error;
            echo json_encode($response);
            return;
        }
        switch ($name) {
            case 'user':
                $response += $this->uploadUsers($files);
                break;
            case 'phone':
                $response += $this->uploadPhones($files);
                break;
            case 'simcard':
                $response += $this->uploadSimCards($files);
                break;
            default:
                $response += [
                    'result' => false,
                ];
        }
        echo json_encode($response);
    }

    private function dataUsers() : array
    {
        $response = [
            'result' => true,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        if ('' === ($_POST['draw'] ?? '')) {
            $response['result'] = false;
            $response['message'] = '缺少参数"draw"';
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c = ['deleted' => User::DELETED_NO];
        if ('' !== ($_POST['name'] ?? '')) {
            $c['name@'] = $_POST['name'];
        }
        if ('' !== ($_POST['username'] ?? '')) {
            $c['username@'] = $_POST['username'];
        }
        if ('' !== ($_POST['email'] ?? '')) {
            $c['email@'] = $_POST['email'];
        }
        if ('' !== ($_POST['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = date('Y-m-d 00:00:00', strtotime($_POST['timeAddedMin']));
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = date('Y-m-d 23:59:59', strtotime($_POST['timeAddedMax']));
        }
        $count = User::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit = $_POST['length'];
        $offset = $_POST['start'];
        $userList = User::getList($c, $order, $limit, $offset);
        if (!$userList) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data = [];
        $index = 1;
        $fields = User::COLUMNS;
        $fields = array_flip($fields);
        foreach ($userList as $user) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $user->$column() . '" type="checkbox"> ' . ($index++ + $offset). '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'role':
                            $value .= '<span>' . User::LABEL_ROLE[$user->$column()] . '</span>';
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($user->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data'] = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function dataPhones() : array
    {
        $response = [
            'result' => true,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        if ('' === ($_POST['draw'] ?? '')) {
            $response['result'] = false;
            $response['message'] = '缺少参数"draw"';
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c = ['deleted' => Phone::DELETED_NO];
        if ('' !== ($_POST['type'] ?? '')) {
            $c['type@'] = $_POST['type'];
        }
        if ('' !== ($_POST['label'] ?? '')) {
            $c['label@'] = $_POST['label'];
        }
        if ('' !== ($_POST['os'] ?? '')) {
            $c['os@'] = $_POST['os'];
        }
        if ('' !== ($_POST['resolution'] ?? '')) {
            $c['resolution@'] = $_POST['resolution'];
        }
        if ('' !== ($_POST['ramMin'] ?? '')) {
            $c['ram>='] = $_POST['ramMin'];
        }
        if ('' !== ($_POST['ramMax'] ?? '')) {
            $c['ram<='] = $_POST['ramMax'];
        }
        if (isset($_POST['status']) && is_array($_POST['status'])) {
            $c['status()'] = $_POST['status'];
        }
        if ('' !== ($_POST['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = date('Y-m-d 00:00:00', strtotime($_POST['timeAddedMin']));
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = date('Y-m-d 23:59:59', strtotime($_POST['timeAddedMax']));
        }
        $count = Phone::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit = $_POST['length'];
        $offset = $_POST['start'];
        $phoneList = Phone::getList($c, $order, $limit, $offset);
        if (!$phoneList) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data = [];
        $index = 1;
        $fields = Phone::COLUMNS;
        $fields = array_flip($fields);
        foreach ($phoneList as $phone) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $phone->$column() . '" type="checkbox"> ' . ($index++ + $offset). '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= $this->phoneStatus($phone);
                            break;
                        case 'carrier':
                            if (!$phone->$column()) {
                                break;
                            }
                            $carrierList = explode(',', $phone->$column());
                            $labels = [];
                            foreach ($carrierList as $carrierCode) {
                                $labels[] = Phone::LABEL_CARRIER[$carrierCode];
                            }
                            $value .= '<span>' . implode(',', $labels) . '</span>';
                            break;
                        case 'type':
                        case 'os':
                        case 'imei':
                            $value .= '<span class="long-data">' . htmlspecialchars($phone->$column()) . '</span>';
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($phone->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data'] = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function dataSimCards() : array
    {
        $response = [
            'result' => true,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];
        if ('' === ($_POST['draw'] ?? '')) {
            $response['result'] = false;
            $response['message'] = '缺少参数"draw"';
            return $response;
        }
        $response['draw'] = $_POST['draw'];
        $c = [];
        if ('' !== ($_POST['phoneNumber'] ?? '')) {
            $c['phoneNumber@'] = $_POST['phoneNumber'];
        }
        if ('' !== ($_POST['place'] ?? '')) {
            $c['place@'] = $_POST['place'];
        }
        if ('' !== ($_POST['label'] ?? '')) {
            $c['label@'] = $_POST['label'];
        }
        if (isset($_POST['status']) && is_array($_POST['status'])) {
            $c['status()'] = $_POST['status'];
        }
        if ('' !== ($_POST['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = date('Y-m-d 00:00:00', strtotime($_POST['timeAddedMin']));
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = date('Y-m-d 23:59:59', strtotime($_POST['timeAddedMax']));
        }
        $count = SimCard::getCount($c);
        $columns = [];
        foreach ($_POST['columns'] as $columnDef) {
            $columns[] = $columnDef['data'];
        }
        $order = [];
        if ($_POST['order'] ?? []) {
            foreach ($_POST['order'] as $orderDef) {
                $key = $columns[$orderDef['column']];
                $order[$key] = 'desc' === $orderDef['dir'] ? 'desc' : 'asc';
            }
        }
        $limit = $_POST['length'];
        $offset = $_POST['start'];
        $simCardList = SimCard::getList($c, $order, $limit, $offset);
        if (!$simCardList) {
            $response['result'] = false;
            $response['message'] = '没有记录';
            return $response;
        }
        $data = [];
        $index = 1;
        $fields = SimCard::COLUMNS;
        $fields = array_flip($fields);
        foreach ($simCardList as $simCard) {
            $row = [];
            foreach ($columns as $column) {
                $value = '';
                if ('id' === $column) {
                    $value .= '<span class="checkbox"><label><input value="' . $simCard->$column() . '" type="checkbox"> ' . ($index++ + $offset). '</label></span>';
                } elseif ('#action' === $column) {
                    $value .= '<button class="btn btn-info" style="padding-bottom: 0;padding-top: 0;"><i class="fa fa-edit"></i> 编辑</button>';
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= $this->simCardStatus($simCard);
                            break;
                        case 'carrier':
                            if (!$simCard->$column()) {
                                break;
                            }
                            $carrierList = explode(',', $simCard->carrier());
                            $labels = [];
                            foreach ($carrierList as $carrierCode) {
                                $labels[] = SimCard::LABEL_CARRIER[$carrierCode];
                            }
                            $value .= '<span>' . implode(',', $labels) . '</span>';
                            break;
                        case 'imsi':
                            $value .= '<span class="long-data">' . htmlspecialchars($simCard->$column()) . '</span>';
                            break;
                        default:
                            $value .= '<span>' . htmlspecialchars($simCard->$column()) . '</span>';
                            break;
                    }
                }
                $row[$column] = $value;
            }
            $data[] = $row;
        }
        $response['data'] = $data;
        $response['recordsTotal'] = $response['recordsFiltered'] = $count;
        return $response;
    }

    private function phoneStatus(Phone $phone) : string
    {
        $result = '';
        $status = Phone::LABEL_STATUS[$phone->status()];
        switch ($phone->status()) {
            case Phone::STATUS_IN_INVENTORY:
                $result .= '<i class="fa fa-home text-success"></i>';
                break;
            case Phone::STATUS_RENT_OUT:
                $result .= '<i class="fa fa-user text-warning"></i>';
                break;
            case Phone::STATUS_BROKEN:
                $result .= '<i class="fa fa-times text-danger"></i>';
                break;
            case Phone::STATUS_OTHER:
                $result .= '<i class="fa fa-question text-muted"></i>';
                break;
        }
        return $result;
    }

    private function simCardStatus(SimCard $simCard) : string
    {
        $result = '';
        $status = SimCard::LABEL_STATUS[$simCard->status()];
        switch ($simCard->status()) {
            case SimCard::STATUS_IN_INVENTORY:
                $result .= '<i class="fa fa-home text-success"></i>';
                break;
            case SimCard::STATUS_RENT_OUT:
                $result .= '<i class="fa fa-user text-warning"></i>';
                break;
            case SimCard::STATUS_BROKEN:
                $result .= '<i class="fa fa-times text-danger"></i>';
                break;
            case SimCard::STATUS_OTHER:
                $result .= '<i class="fa fa-question text-mute"></i>';
                break;
        }
        return $result;
    }

    private function saveUser()
    {
        $response = [
            'result' => true,
            'message' => '',
        ];
        $required = [
            'name' => 'str',
            'username' => 'str',
            'password' => 'str',
            'email' => 'str',
        ];
        $missing = '';
        foreach ($required as $name => $type) {
            $val = $_POST[$name] ?? '';
            if ('array' === $type) {
                if (is_array($val) && count($val)) {
                    continue;
                }
                $missing = $name;
                break;
            }
            if ('' === trim($val)) {
                $missing = $name;
                break;
            }
        }
        if ($missing) {
            $response['result'] = false;
            $response['message'] = "缺少字段: {$missing}";
            return $response;
        }
        $username = $_POST['username'];
        $o = User::getOne([
            'deleted' => User::DELETED_NO,
            'username' => $username,
        ]);
        if ($o) {
            $response['result'] = false;
            $response['message'] = '用户名已存在';
            return $response;
        }
        $o = new User();
        $o->username($username);
        $o->name($_POST['name']);
        $salt = md5($o->role() . $o->timeAdded());
        $o->passwordSalt($salt);
        $o->password(sha1($_POST['password'] . $salt));
        $o->email($_POST['email']);
        $saved = $o->save();
        $response['message'] = $saved ? '保存成功' : '未保存';
        return $response;
    }

    private function savePhone()
    {
        $response = [
            'result' => true,
            'message' => '',
        ];
        $required = [
            'type' => 'str',
            'os' => 'str',
            'carrier' => 'array',
            'status' => 'str',
            'label' => 'str',
        ];
        $missing = '';
        foreach ($required as $name => $type) {
            $val = $_POST[$name] ?? '';
            if ('array' === $type) {
                if (is_array($val) && count($val)) {
                    continue;
                }
                $missing = $name;
                break;
            }
            if ('' === trim($val)) {
                $missing = $name;
                break;
            }
        }
        if ($missing) {
            $response['result'] = false;
            $response['message'] = "缺少字段: {$missing}";
            return $response;
        }
        $label = $_POST['label'];
        $o = Phone::getOne([
            'deleted' => User::DELETED_NO,
            'label' => $label,
        ]);
        if ($o) {
            $response['result'] = false;
            $response['message'] = '同标识的测试机已经存在';
            return $response;
        }
        $status = $_POST['status'];
        if (!array_key_exists($status, Phone::LABEL_STATUS)) {
            $response['result'] = false;
            $response['message'] = '非法的状态值';
            return $response;
        }
        $userId = $_POST['userId'] ?? null;
        if ($status === Phone::STATUS_RENT_OUT && !$userId) {
            $response['result'] = false;
            $response['message'] = '已借出的测试机需要选择借出人';
            return $response;
        }
        if ($status !== Phone::STATUS_RENT_OUT && $userId) {
            $response['result'] = false;
            $response['message'] = '未借出的测试机不需要选择借出人';
            return $response;
        }
        $carrier = $_POST['carrier'];
        $carrier = implode(',', $carrier);
        $ram = is_numeric($_POST['ram'] ?? null) ? $_POST['ram'] : null;
        $screenSize = $_POST['screenSize'] ?? null;
        $resolutionW = $_POST['resolutionW'] ?? '';
        $resolutionH = $_POST['resolutionH'] ?? '';
        $imei = $_POST['imei'] ?? '';
        if (preg_match_all('#\d{15}#', $imei, $match)) {
            $imei = implode(',', $match[0]);
        }
        $imei = $imei ?: null;
        $type = $_POST['type'] ?? null;
        $os = $_POST['os'];
        $o = new Phone();
        if ($resolutionH && $resolutionW) {
            $o->resolution("{$resolutionW} X {$resolutionH}");
        }
        $o->type($type);
        $o->label($label);
        $o->os($os);
        $o->screenSize($screenSize);
        $o->status($status);
        $o->ram($ram);
        $o->carrier($carrier);
        $o->imei($imei);
        $o->userId($userId);
        $saved = $o->save();
        $response['message'] = $saved ? '保存成功' : '未保存';
        return $response;
    }

    private function saveSimCard()
    {
        $response = [
            'result' => true,
            'message' => '',
        ];
        $required = [
            'phoneNumber' => 'str',
            'label' => 'str',
            'carrier' => 'array',
            'status' => 'str',
        ];
        $missing = '';
        foreach ($required as $name => $type) {
            $val = $_POST[$name] ?? '';
            if ('array' === $type) {
                if (is_array($val) && count($val)) {
                    continue;
                }
                $missing = $name;
                break;
            }
            if ('' === trim($val)) {
                $missing = $name;
                break;
            }
        }
        if ($missing) {
            $response['result'] = false;
            $response['message'] = "缺少字段: {$missing}";
            return $response;
        }
        $phoneNumber = $_POST['phoneNumber'];
        $o = SimCard::getOne([
            'deleted' => User::DELETED_NO,
            'phoneNumber' => $phoneNumber,
        ]);
        if ($o) {
            $response['result'] = false;
            $response['message'] = '同号码的测试卡已经存在';
            return $response;
        }
        $status = $_POST['status'];
        if (!array_key_exists($status, SimCard::LABEL_STATUS)) {
            $response['result'] = false;
            $response['message'] = '非法的状态值';
            return $response;
        }
        $userId = $_POST['userId'] ?? null;
        if ($status === SimCard::STATUS_RENT_OUT && !$userId) {
            $response['result'] = false;
            $response['message'] = '已借出的测试卡需要选择借出人';
            return $response;
        }
        if ($status !== SimCard::STATUS_RENT_OUT && $userId) {
            $response['result'] = false;
            $response['message'] = '未借出的测试卡不需要选择借出人';
            return $response;
        }
        $carrier = $_POST['carrier'];
        $carrier = implode(',', $carrier);
        $label = $_POST['label'];
        $place = $_POST['place'] ?? null;
        $imsi = $_POST['imsi'] ?? null;

        $o = new SimCard();
        $o->phoneNumber($phoneNumber);
        $o->label($label);
        $o->status($status);
        $o->carrier($carrier);
        $o->imsi($imsi);
        $o->userId($userId);
        $o->place($place);
        $saved = $o->save();
        $response['message'] = $saved ? '保存成功' : '未保存';
        return $response;
    }

    private function uploadUsers(array &$files) : array
    {
        $response = [
            'result' => true,
        ];
        $excel = new MyExcel();
        $head = [
            'name' => ['#姓名#u'],
            'username' => ['#用户名#u'],
            'email' => ['#邮箱#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }

        foreach ($excelResult['content'] as &$row) {
            if ($row['username']['value']) {
                $user = User::getOne([
                    'username' => $row['username']['value'],
                    'deleted' => User::DELETED_NO,
                ]);
                if ($user) {
                    unset($user);
                    continue;
                }
            } else {
                continue;
            }
            $o = new User();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value) {
                    continue;
                }
                $o->$name($value);
            }
            unset($def);
            $salt = md5($o->role() . $o->timeAdded());
            $o->passwordSalt($salt);
            $o->password(sha1(sha1($o->username() . '123456') . $salt));
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_USER_EXCEL);
        $o = new UploadFile();
        $o->type(UploadFile::TYPE_USER_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function uploadPhones(array &$files) : array
    {
        $response = [
            'result' => true,
        ];
        $excel = new MyExcel();
        $head = [
            'type' => ['#机型#u'],
            'os' => ['#系统#u'],
            'resolution' => ['#分辨率#u'],
            'ram' => ['#ram#i'],
            'carrier' => ['#运营商#u'],
            'screenSize' => ['#屏幕尺寸#u'],
            'label' => ['#编号#u'],
            'imei' => ['#imei#i'],
            'status' => ['#状态#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }
        $carrierList = array_flip(Phone::LABEL_CARRIER);
        foreach ($excelResult['content'] as &$row) {
            if ($row['label']['value']) {
                $phone = Phone::getOne([
                    'label' => $row['label']['value'],
                    'deleted' => Phone::DELETED_NO,
                ]);
                if ($phone) {
                    unset($phone);
                    continue;
                }
            }
            $o = new Phone();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value && 'status' !== $name) {
                    continue;
                }
                switch ($name) {
                    case 'resolution':
                        if (!preg_match_all('#(\d+)#', $value, $match)) {
                            break;
                        }
                        $match = $match[0];
                        if (2 !== count($match)) {
                            break;
                        }
                        $value = "{$match[0]} X {$match[1]}";
                        $o->$name($value);
                        break;
                    case 'carrier':
                        if (!preg_match_all("#电信|移动|联通#u", $value, $match)) {
                            break;
                        }
                        $match = array_flip($match[0]);
                        $carrierCodes = [];
                        foreach ($carrierList as $label => $code) {
                            if (!array_key_exists($label, $match)) {
                                continue;
                            }
                            $carrierCodes[] = $code;
                        }
                        $carrierCodes = implode(',', $carrierCodes);
                        $o->$name($carrierCodes);
                        break;
                    case 'imei':
                        if (!preg_match_all('#\d{15}#m', $value, $match)) {
                            break;
                        }
                        $value = implode(',', $match[0]);
                        $o->$name($value);
                        break;
                    case 'status':
                        if ('' === $value) {
                            $o->$name(Phone::STATUS_OTHER);
                            $o->statusDescription('没有注明');
                            break;
                        }
                        if (false !== strpos($value, '组内')) {
                            $o->$name(Phone::STATUS_IN_INVENTORY);
                            break;
                        }
                        if (false !== strpos($value, '坏')) {
                            $o->$name(Phone::STATUS_BROKEN);
                            break;
                        }
                        $user = User::getOne([
                            'deleted' => User::DELETED_NO,
                            'name' => $value,
                        ]);
                        if ($user) {
                            $o->$name(Phone::STATUS_RENT_OUT);
                            $o->userId($user->id());
                            $o->statusDescription($value);
                            unset($user);
                            break;
                        }
                        $o->$name(Phone::STATUS_OTHER);
                        $o->statusDescription($value);
                        break;
                    case 'ram':
                        if (preg_match('#^\d+$#', $value)) {
                            $o->$name($value);
                            break;
                        }
                        if (preg_match('#^(\d+)G$#i', $value, $match)) {
                            $o->$name(intval($match[1]) * 1024);
                            break;
                        }
                        break;
                    case 'label':
                        $o->$name(strtoupper($value));
                        break;
                    default:
                        $o->$name($value);
                }
            }
            unset($def);
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_PHONE_EXCEL);
        $o = new UploadFile();
        $o->type(UploadFile::TYPE_PHONE_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }

    private function uploadSimCards(array &$files) : array
    {
        $response = [
            'result' => true,
        ];
        $excel = new MyExcel();
        $head = [
            'phoneNumber' => ['#手机号#u'],
            'label' => ['#标识#u'],
            'carrier' => ['#运营商#u'],
            'place' => ['#归属地#u'],
            'imsi' => ['#imsi#i'],
            'status' => ['#状态#u'],
        ];
        $excelResult = $excel->load($files['tmp_name'], $head);
        $response = array_merge($response, $excelResult);
        if (!$excelResult['result']) {
            return $response;
        }
        $carrierList = array_flip(SimCard::LABEL_CARRIER);
        foreach ($excelResult['content'] as &$row) {
            if ($row['phoneNumber']['value']) {
                $simCard = SimCard::getOne([
                    'phoneNumber' => $row['phoneNumber']['value'],
                    'deleted' => SimCard::DELETED_NO,
                ]);
                if ($simCard) {
                    unset($simCard);
                    continue;
                }
            }
            $o = new SimCard();
            foreach ($row as $name => &$def) {
                $value = $def['value'];
                if ('' === $value && 'status' !== $name) {
                    continue;
                }
                switch ($name) {
                    case 'phoneNumber':
                        if (!preg_match('#\d+#', $value, $match)) {
                            continue;
                        }
                        $o->$name($match[0]);
                        break;
                    case 'carrier':
                        if (!preg_match_all("#电信|移动|联通|虚拟运营商#u", $value, $match)) {
                            break;
                        }
                        $match = array_flip($match[0]);
                        $carrierCodes = [];
                        foreach ($carrierList as $label => $code) {
                            if (!array_key_exists($label, $match)) {
                                continue;
                            }
                            $carrierCodes[] = $code;
                        }
                        $carrierCodes = implode(',', $carrierCodes);
                        $o->$name($carrierCodes);
                        break;
                    case 'status':
                        if ('' === $value) {
                            $o->$name(SimCard::STATUS_OTHER);
                            $o->statusDescription('没有注明');
                            break;
                        }
                        if (false !== strpos($value, '组内')) {
                            $o->$name(SimCard::STATUS_IN_INVENTORY);
                            break;
                        }
                        if (preg_match('#暂停|注销#u', $value)) {
                            $o->$name(SimCard::STATUS_BROKEN);
                            break;
                        }
                        $user = User::getOne([
                            'deleted' => User::DELETED_NO,
                            'name' => $value,
                        ]);
                        if ($user) {
                            $o->$name(SimCard::STATUS_RENT_OUT);
                            $o->userId($user->id());
                            $o->statusDescription($value);
                            unset($user);
                            break;
                        }
                        $o->$name(SimCard::STATUS_OTHER);
                        $o->statusDescription($value);
                        break;
                    case 'label':
                        $o->$name(strtoupper($value));
                        break;
                    default:
                        $o->$name($value);
                }
            }
            unset($def);
            $o->save();
        }
        unset($row);
        $uploader = AppService::getUploader();
        $fileName = $uploader->saveFile($files, UploadFile::TYPE_SIMCARD_EXCEL);
        $o = new UploadFile();
        $o->type(UploadFile::TYPE_SIMCARD_EXCEL);
        $o->originName($files['name']);
        $o->fileName($fileName);
        $o->uploadByUser(App::getUser()->id());
        $o->save();
        return $response;
    }
}