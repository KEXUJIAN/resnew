<?php
/**
 * Created by PhpStorm.
 * User: KE, XUJIAN
 * Date: 2018/3/12
 * Time: 11:07
 */

namespace Res\Biz;

use App;
use Res\Model\Request;
use Res\Model\Phone;
use Res\Model\SimCard;

class RequestBiz
{
    public static function requestAction(Request $request): string
    {
        $result = '';
        if (Request::STATUS_NEW !== $request->status() || $request->toUserId() !== App::getUser()->id()) {
            return $result;
        }
        if (!array_key_exists($request->status(), Request::LABEL_TYPE)) {
            return $result;
        }
        $btnClass = 'btn action-button btn-xs';
        $role = 'action';
        $accept = '/admin/accept/';
        $reject = '/admin/reject/';
        $buttonDefs = [
            Request::TYPE_RENT_OUT => [
                '同意借出' => [
                    'class="'. $btnClass . ' btn-success"',
                    'data-role="' . $role . '"',
                    'data-url="' . "{$accept}{$request->id()}" . '"',
                ],
                '驳回请求' => [
                    'class="'. $btnClass . ' btn-danger"',
                    'data-role="' . $role . '"',
                    'data-url="' . "{$reject}{$request->id()}" . '"',
                ],
            ],
            Request::TYPE_RETURN => [
                '确认归还' => [
                    'class="'. $btnClass . ' btn-primary"',
                    'data-role="' . $role . '"',
                    'data-url="' . "{$accept}{$request->id()}" . '"',
                ],
                '驳回请求' => [
                    'class="'. $btnClass . ' btn-danger"',
                    'data-role="' . $role . '"',
                    'data-url="' . "{$reject}{$request->id()}" . '"',
                ],
            ],
            Request::TYPE_TRANSFER => [
                '转借' => [
                    'data-toggle="modal"',
                    'data-target="#ajax-modal"',
                    'class="'. $btnClass . ' btn-warning"',
                    'data-url="/simCard/transferConfirmView/' . $request->id() . '"'
                ],
            ],
        ];
        $buttonActDef = $buttonDefs[$request->type()];
        foreach ($buttonActDef as $name => $defs) {
            $attrs = implode(' ', $defs);
            $result .= "<button {$attrs}>{$name}</button> ";
        }
        return $result;
    }

    /**
     * @param $assetType
     * @param $id
     * @return null|Phone|SimCard
     */
    public static function assetFactory($assetType, $id)
    {
        if (!array_key_exists($assetType, Request::LABEL_ASSET_TYPE)) {
            return null;
        }
        $map = [
            Request::ASSET_TYPE_PHONE => Phone::class,
            Request::ASSET_TYPE_SIM_CARD => SimCard::class,
        ];
        $o = $map[$assetType]::get($id);
        if ($o && $map[$assetType]::DELETED_YES === $o->deleted()) {
            $o = null;
        }
        return $o;
    }
}
