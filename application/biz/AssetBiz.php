<?php
/**
 * Created by PhpStorm.
 * User: KE, XUJIAN
 * Date: 2018/2/24
 * Time: 21:08
 */

namespace Res\Biz;

use Res\Model\Phone;
use Res\Model\SimCard;
use Res\Model\User;

class AssetBiz
{
    public static function phoneStatus(Phone $phone): string
    {
        $result = '';
        $status = Phone::LABEL_STATUS[$phone->status()];
        $result .= '<span data-role="status" data-value="' . $phone->status() . '">';
        switch ($phone->status()) {
            case Phone::STATUS_IN_INVENTORY:
                $result .= '<p class="text-success"><i class="fa fa-home"></i>' . $status . '</p>';
                break;
            case Phone::STATUS_RENT_OUT:
                $user   = User::getOne(['id' => $phone->userId()]);
                $result .= '<p class="text-warning"><i class="fa fa-user"></i>' . $user->name() . '</p>';
                break;
            case Phone::STATUS_BROKEN:
                $result .= '<p class="text-danger"><i class="fa fa-times"></i>' . $status . '</p>';
                break;
            case Phone::STATUS_OTHER:
                $result .= '<p class="text-muted"><i class="fa fa-question"></i>' . $status . '</p>';
                break;
            default:
                $result .= '<p class="text-info"><i class="fa fa-lock"></i>' . "{$status}, 待确认" . '</p>';
        }
        $result .= '</span>';
        return $result;
    }

    public static function simCardStatus(SimCard $simCard): string
    {
        $result = '';
        $status = SimCard::LABEL_STATUS[$simCard->status()];
        $result .= '<span data-role="status" data-value="' . $simCard->status() . '">';
        switch ($simCard->status()) {
            case SimCard::STATUS_IN_INVENTORY:
                $result .= '<p class="text-success"><i class="fa fa-home"></i>' . $status . '</p>';
                break;
            case SimCard::STATUS_RENT_OUT:
                $user   = User::getOne(['id' => $simCard->userId()]);
                $result .= '<p class="text-warning"><i class="fa fa-user"></i>' . $user->name() . '</p>';
                break;
            case SimCard::STATUS_BROKEN:
                $result .= '<p class="text-danger"><i class="fa fa-times"></i>' . $status . '</p>';
                break;
            case SimCard::STATUS_OTHER:
                $result .= '<p class="text-muted"><i class="fa fa-question"></i>' . $status . '</p>';
                break;
            default:
                $result .= '<p class="text-info"><i class="fa fa-lock"></i>' . "{$status}, 待确认" . '</p>';
        }
        $result .= '</span>';
        return $result;
    }

    public static function phoneCondition(array &$c, array &$params)
    {
        if ('' !== ($params['type'] ?? '')) {
            $c['type@'] = $params['type'];
        }
        if ('' !== ($params['label'] ?? '')) {
            $c['label@'] = $params['label'];
        }
        if ('' !== ($params['os'] ?? '')) {
            $c['os@'] = $params['os'];
        }
        if ('' !== ($params['resolution'] ?? '')) {
            $c['resolution@'] = $params['resolution'];
        }
        if ('' !== ($params['ramMin'] ?? '')) {
            $c['ram>='] = $params['ramMin'];
        }
        if ('' !== ($params['ramMax'] ?? '')) {
            $c['ram<='] = $params['ramMax'];
        }
        if (isset($params['status']) && is_array($params['status'])) {
            $c['status()'] = $params['status'];
        }
        if ('' !== ($params['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = date('Y-m-d 00:00:00', strtotime($params['timeAddedMin']));
        }
        if ('' !== ($params['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = date('Y-m-d 23:59:59', strtotime($params['timeAddedMax']));
        }
        if ('' !== ($params['carrier'] ?? '')) {
            $c['carrier@'] = $params['carrier'];
        }
        if ('' !== ($params['imei'] ?? '')) {
            $c['imei@'] = $params['imei'];
        }
    }

    public static function simCardCondition(array &$c, array &$params)
    {
        if ('' !== ($params['phoneNumber'] ?? '')) {
            $c['phoneNumber@'] = $params['phoneNumber'];
        }
        if ('' !== ($params['place'] ?? '')) {
            $c['place@'] = $params['place'];
        }
        if ('' !== ($params['label'] ?? '')) {
            $c['label@'] = $params['label'];
        }
        if (isset($params['status']) && is_array($params['status'])) {
            $c['status()'] = $params['status'];
        }
        if ('' !== ($params['timeAddedMin'] ?? '')) {
            $c['timeAdded>='] = date('Y-m-d 00:00:00', strtotime($params['timeAddedMin']));
        }
        if ('' !== ($params['timeAddedMax'] ?? '')) {
            $c['timeAdded<='] = date('Y-m-d 23:59:59', strtotime($params['timeAddedMax']));
        }
        if ('' !== ($params['carrier'] ?? '')) {
            $c['carrier@'] = $params['carrier'];
        }
        if ('' !== ($params['imsi'] ?? '')) {
            $c['imsi@'] = $params['imsi'];
        }
    }
}
