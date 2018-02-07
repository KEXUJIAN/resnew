<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 15:17
 */

use Res\Model\Phone;
use Res\Model\SimCard;
use Res\Model\Request;

class Assets extends CI_Controller
{
    public function inventory($name = 'phone', $id = null)
    {
        $name = 'simcard' === $name ? $name : 'phone';
        $params = [
            'panel' => $name,
            'assetId' => $id,
            'titleNavClass' => 'container-fluid',
        ];
        App::view('asset/own-assets', $params);
    }

    public function phone()
    {
        App::view('asset/phone');
    }

    public function simcard()
    {
        App::view('asset/simcard');
    }

    public function ownAssets($name)
    {
        $response = [];
        switch ($name) {
            case 'phone':
                $response += $this->ownPhones();
                break;
            case 'simcard':
                $response += $this->ownSimCards();
                break;
            default:
                $response += [
                    'result' => false,
                ];
        }
        echo json_encode($response);
    }

    public function dataTable($name)
    {
        $response = [];
        switch ($name) {
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

    private function ownPhones() : array
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
        if (!empty($_POST['specificId'])) {
            $c['id'] = $_POST['specificId'];
        }
        $c += [
            'userId' => App::getUser()->id(),
            'deleted' => Phone::DELETED_NO
        ];
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
            $c['timeAdded>='] = $_POST['timeAddedMin'];
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = $_POST['timeAddedMax'];
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
                    $value .= '<label class="index-label" data-id="' . $phone->$column() . '">' . ($index++ + $offset). '</label>';
                } elseif ('#action' === $column) {
                    $value .= $this->phoneAction($phone);
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'carrier':
                            $carrierList = explode(',', $phone->carrier());
                            $labels = [];
                            foreach ($carrierList as $carrierCode) {
                                $labels[] = Phone::LABEL_CARRIER[$carrierCode];
                            }
                            $value .= '<span>' . implode(',', $labels) . '</span>';
                            break;
                        case 'status':
                            $value .= $this->phoneStatus($phone);
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

    private function ownSimCards() : array
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
        if (!empty($_POST['specificId'])) {
            $c['id'] = $_POST['specificId'];
        }
        $c += [
            'userId' => App::getUser()->id(),
            'deleted' => Phone::DELETED_NO
        ];
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
            $c['timeAdded>='] = $_POST['timeAddedMin'];
        }
        if ('' !== ($_POST['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = $_POST['timeAddedMax'];
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
                    $value .= '<label class="index-label" data-id="' . $simCard->$column() . '">' . ($index++ + $offset). '</label>';
                } elseif ('#action' === $column) {
                    $value .= $this->simCardAction($simCard);
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= $this->simCardStatus($simCard);
                            break;
                        case 'imsi':
                            $value .= '<span class="long-data">' . htmlspecialchars($simCard->$column()) . '</span>';
                            break;
                        case 'carrier':
                            $carrierList = explode(',', $simCard->carrier());
                            $labels = [];
                            foreach ($carrierList as $carrierCode) {
                                $labels[] = SimCard::LABEL_CARRIER[$carrierCode];
                            }
                            $value .= '<span>' . implode(',', $labels) . '</span>';
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
        if ('' !== ($_POST['carrier'] ?? '')) {
            $c['carrier@'] = $_POST['carrier'];
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
                    $value .= '<label class="index-label" data-id="' . $phone->$column() . '">' . ($index++ + $offset). '</label>';
                } elseif ('#action' === $column) {
                    $value .= $this->phoneAction($phone);
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
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
                        case 'status':
                            $value .= $this->phoneStatus($phone);
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
        if ('' !== ($_POST['carrier'] ?? '')) {
            $c['carrier@'] = $_POST['carrier'];
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
                    $value .= '<label class="index-label" data-id="' . $simCard->$column() . '">' . ($index++ + $offset). '</label>';
                } elseif ('#action' === $column) {
                    $value .= $this->simCardAction($simCard);
                } elseif (array_key_exists($column, $fields)) {
                    switch ($column) {
                        case 'status':
                            $value .= $this->simCardStatus($simCard);
                            break;
                        case 'imsi':
                            $value .= '<span class="long-data">' . htmlspecialchars($simCard->$column()) . '</span>';
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

    private function phoneAction(Phone $phone) : string
    {
        $phoneId = $phone->id();
        $result = '<button data-toggle="modal" data-target="#ajax-modal" data-url="/phone/info/' . $phoneId . '" class="btn btn-info btn-xs action-button">查看</button>';
        switch ($phone->status()) {
            case Phone::STATUS_IN_INVENTORY:
                $result .= '<button data-role="rent-out" data-url="/phone/rent/' . $phoneId . '" class="btn btn-success btn-xs action-button">借出</button>';
                break;
            case Phone::STATUS_RENT_OUT:
                $request = Request::getOne([
                    'assetId' => $phoneId,
                    'deleted' => Request::DELETED_NO,
                    'assetType' => Request::ASSET_TYPE_PHONE,
                    'type' => Request::TYPE_TRANSFER,
                    'status' => Request::STATUS_NEW,
                ]);
                if (App::getUser()->id() === $phone->userId()) {
                    if ($request) {
                        $result .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="/phone/transferConfirmView/'. $request->id() . '" class="btn btn-warning btn-xs action-button">转借</button>';
                    }
                    $result .= '<button data-role="return" data-url="/phone/restore/' . $phoneId . '" class="btn btn-primary btn-xs action-button">归还</button>';
                    break;
                }
                if ($request) {
                    break;
                }
                $result .= '<button data-role="transfer" data-url="/phone/transferApply/' . $phoneId . '" class="btn btn-warning btn-xs action-button">申请转借</button>';
                break;
        }
        return $result;
    }

    private function phoneStatus(Phone $phone) : string
    {
        $result = '';
        $status = Phone::LABEL_STATUS[$phone->status()];
        switch ($phone->status()) {
            case Phone::STATUS_IN_INVENTORY:
                $result .= '<p class="text-success"><i class="fa fa-home"></i>' . $status . '</p>';
                break;
            case Phone::STATUS_RENT_OUT:
                $result .= '<p class="text-warning"><i class="fa fa-user"></i>' . $status . '</p>';
                break;
            case Phone::STATUS_BROKEN:
                $result .= '<p class="text-danger"><i class="fa fa-times"></i>' . $status . '</p>';
                break;
            case Phone::STATUS_OTHER:
                $result .= '<p class="text-muted"><i class="fa fa-question"></i>' . $status . '</p>';
                break;
        }
        return $result;
    }

    private function simCardAction(SimCard $simCard) : string
    {
        $simCardId = $simCard->id();
        $result = '<button data-toggle="modal" data-target="#ajax-modal" data-url="/simCard/info/' . $simCardId . '" class="btn btn-info btn-xs action-button">查看</button>';
        switch ($simCard->status()) {
            case SimCard::STATUS_IN_INVENTORY:
                $result .= '<button data-role="rent-out" data-url="/simCard/rent/' . $simCardId . '" class="btn btn-success btn-xs action-button">借出</button>';
                break;
            case SimCard::STATUS_RENT_OUT:
                $request = Request::getOne([
                    'assetId' => $simCardId,
                    'deleted' => Request::DELETED_NO,
                    'assetType' => Request::ASSET_TYPE_SIM_CARD,
                    'type' => Request::TYPE_TRANSFER,
                    'status' => Request::STATUS_NEW,
                ]);
                if (App::getUser()->id() === $simCard->userId()) {
                    if ($request) {
                        $result .= '<button data-toggle="modal" data-target="#ajax-modal" data-url="/simCard/transferConfirmView/' . $request->id() . '" class="btn btn-warning btn-xs action-button">转借</button>';
                    }
                    $result .= '<button data-role="return" data-url="/simCard/restore/' . $simCardId . '" class="btn btn-primary btn-xs action-button">归还</button>';
                    break;
                }
                if ($request) {
                    break;
                }
                $result .= '<button data-role="transfer" data-url="/simCard/transferApply/' . $simCardId . '" class="btn btn-warning btn-xs action-button">申请转借</button>';
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
                $result .= '<p class="text-success"><i class="fa fa-home"></i>' . $status . '</p>';
                break;
            case SimCard::STATUS_RENT_OUT:
                $result .= '<p class="text-warning"><i class="fa fa-user"></i>' . $status . '</p>';
                break;
            case SimCard::STATUS_BROKEN:
                $result .= '<p class="text-danger"><i class="fa fa-times"></i>' . $status . '</p>';
                break;
            case SimCard::STATUS_OTHER:
                $result .= '<p class="text-muted"><i class="fa fa-question"></i>' . $status . '</p>';
                break;
        }
        return $result;
    }
}
