<?php

namespace Ycore\Http\Middleware\admin;

use Ycore\Models\Role;
use Ycore\Models\Rules;
use Ycore\Tool\Json;
use Closure;
use Illuminate\Http\Request;
use Nette\Reflection\ClassType;

class Auth
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


        $route = $request->route();

        $controller = $route->action['controller'];

        $c = explode('@', $controller);

        $class = new ClassType($c[0]);

        $data = $class->getMethod($c[1])->getAnnotation('Auth');


        //无验证标记
        if ($data && ($data['type'] ?? "") === 'no_check') {

            return $next($request);
        }


        $role_id = resolve('adminInfo')['role_id'];


        //超级管理员
        if ($role_id === 0) {

            app()->instance('allRule', true);

            return $next($request);
        }


        $role = Role::find($role_id);

        if (!$role) {

            return response()->json(Json::codeArray(15, '该管理员未找到路由规则'));
        }

        $rule_list = $role['rules'];

        if (count($rule_list) <= 0) {
            return response()->json(Json::codeArray(16, '该角色拥有的权限数小于1'));
        }

        $rules = Rules::whereIn('id', $rule_list)->get();

        if (!$rules) {
            return response()->json(Json::codeArray(16, '该角色拥有的权限数小于1'));
        }

        $rules = array_column($rules->toArray(), 'rule');

        app()->instance('allRule', $rules);

        //是否有权限跳出标记
        if ($data['type'] ?? "" === 'skip_auth') {
            return $next($request);
        }

        //判断依附权限
        if ($data['type'] ?? "" === 'attach') {

            $value = $data['value'];

            $attachList = explode(',', $value);

            foreach ($rules as $k => $v) {

                if (in_array($v, $attachList, true)) {
                    return $next($request);
                }

            }

        }

        $currentRoute = '/' . $route->uri;


//        $rules = array_map(function ($v) {
//
//
//            return  $v;
//
//        }, $rules);


        if (!in_array($currentRoute, $rules, true)) {

            return response()->json(Json::codeArray(51, '你没有该权限'));
        }


        return $next($request);
    }
}
