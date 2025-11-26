<?php

namespace App\Http\Middleware;

use Closure;

class CheckRoleType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		
		
		$role_set1 = array_slice(func_get_args(), 2); 
		
		if (! $request->user()->hasAnyRole($role_set1)) {
			$cur_role = $request->user()->roles->toArray();
			if(empty($cur_role)){
				return redirect('/');
			}
			switch($cur_role[0]['name']){
				
					case 'super_admin':
					case 'admin':
							return redirect('/admin');
					break;
					
					case 'billing_admin':
							return redirect('/billing');
					break;
					
					case 'collection_officer':
							return redirect('/collections');
					break;
			}
			
			return redirect('/');
		}//		
        
        return $next($request);
    }
}

/*
 * 
->middleware('role_type:editor');
*  * 
 * */
