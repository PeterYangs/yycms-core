<?php

namespace Ycore\Http\Middleware\admin;

use Ycore\Models\Admin;
use Ycore\Tool\Json;
use Closure;
use Illuminate\Http\Request;
use Nette\Reflection\ClassType;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\JsonResponse
     * @throws \ReflectionException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->exists('admin_id')) {
            return response()->json(Json::codeArray(11, '请登录！'));
        }

        $admin_id = session()->get('admin_id');
        $info = Admin::where('id', $admin_id)->first();

        if (!$info) {
            return response()->json(Json::codeArray(13, 'token对应的管理员不存在'));
        }

        if ($info->status === 2) {
            return response()->json(Json::codeArray(17, '该管理员已被禁用，请联系管理员'));
        }

        app()->instance('adminInfo', $info->toArray());

        return $next($request);
    }
}
