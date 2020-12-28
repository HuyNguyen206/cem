<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Authen\Permission;
use App\Models\Authen\User;
use App\Component\ExtraFunction;

class BeforeAction {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = Auth::user();
        if (!empty($user)) {
            $currentAction = app()->router->getCurrentRoute()->getActionName();
            list($controller, $action) = explode('@', $currentAction);
            $controllerName = preg_replace('/.*\\\/', '', $controller);
            Session::put('main_breadcrumb', ucfirst($controllerName));
            Session::put('active_breadcrumb', ucfirst($action));
            Session::put('sub_breadcrumb', 'none');

            $allPermission = Session::get('allPermission');
            if (!isset($allPermission) || !isset($allPermission['permission'])) {
                $allPermission = User::getAllPermissionByUserId($user->id);
                Session::put('allPermission', $allPermission);
            }
            $userRole = Session::get('userRole');
            if (!isset($userRole) || empty($userRole)) {
                $userRole = User::getRole($user->id);
                Session::put('userRole', $userRole);
            }

            if ($userRole['id'] == 6) {
                return redirect('/info-new-member');
            }

            $permissionName = strtolower($controllerName) . '-' . strtolower($action);
            $canAction = ExtraFunction::checkHaveAuthenAction($controllerName, $action);
            if ($canAction === false) {
                $permission = Permission::getAllPermissionByName($permissionName);
                return redirect('error/auth')->with('permission', $permission);
            }
            return $next($request);
        } else {
            if ($request->ajax()) {
                $data = array_merge([
                    'id' => 'session_expire',
                    'code' => 800,
                    'status' => '401'
                        ], config('errors.session_expire'));

                $status = 401;
                return response()->json($data, $status);
            } else {
                return redirect()->guest('login');
            }
        }
    }

}
