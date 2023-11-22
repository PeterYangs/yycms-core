<?php

namespace Ycore\Http\Middleware\third;

use App\Http\Models\AuthorizationRules;
use App\Tool\Code;
use Closure;
use Illuminate\Http\Request;
use Ycore\Models\AccessKey;
use Ycore\Tool\Signature;

class CheckSignature
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $va = \Validator::make($request->query(), [
            'time' => 'required|date_format:Y-m-d H:i:s',
            'echostr' => 'required|min:16|max:32',
            'appid' => 'required|max:100',
            'signature' => 'required'
        ]);

        if ($va->fails()) {
            return response(Signature::fail(Signature::PARAMS_ERROR, $va->errors()->first()));
        }

        $diff = time() - strtotime($request->get('time'));
        if ($diff > (50 * 60) || $diff < -(50 * 60)) {
            return response(Signature::fail(Signature::TIME_CHECK_ERROR, "时间验证错误"));
        }

        $rule = AccessKey::where('app_id', $request->get('appid'))->first();
        if (!$rule) {
            return response(Signature::fail(Signature::PARAMS_ERROR, '无此appid'));
        }

        if (!Signature::decrypt($request->get('time'), $request->get('echostr'), $request->get('appid'), $rule->secret, $request->get('signature'))) {

            return response(Signature::fail(Signature::SIGNATURE_ERROR, "签名错误"));
        }

        //刷新appid最后使用时间
        $rule->last_use = now();
        $rule->save();

        return $next($request);
    }
}
