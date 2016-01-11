<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19.12.15
 * Time: 0:34
 */

namespace LazyNewbie\Boilerplate\Middleware;


use Illuminate\Http\Request;

class InputFilterMiddleware
{

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, \Closure $next)
  {

    $input = $request->input(null, []);
    array_walk_recursive($input, function(&$val){
      $val = trim($val);
    });
    $request->replace($input);

    return $next($request);
  }

}