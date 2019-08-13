<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class tokenMiddleware
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
        $session = $request->session();
        $dt = Carbon::now();
        $dt->tz = "America/Mexico_City";
        try {
            if ($session->exists('tokens')) {
                $current_tokens = $session->get('tokens');
                    if (!empty($current_tokens) && gettype($current_tokens) == "array") {
                        $diff_seconds = $current_tokens["expired"]->diffInSeconds($dt, false);
                        if ( $diff_seconds >= 0 ){
                            $tokens = $this->get_token();
                            if (!empty($tokens) && gettype($tokens) == "array"){
                                if (array_has($tokens, "access_token")){
                                    $tokens["expired"] = Carbon::now()->addSeconds($tokens['expires_in']);
                                    $session->put('tokens',$tokens);
                                }
                            }
                        }
                    } else {
                        $error = "Invalid tokens";
                        throw new Exception($error);
                    }

            } else {
                $tokens = $this->get_token();
                if (!empty($tokens) && gettype($tokens) == "array"){
                    if (array_has($tokens, "access_token")){
                        $tokens["expired"] = Carbon::now()->addSeconds($tokens['expires_in']);
                        $session->put('tokens',$tokens);
                    }
                }
            }
        } catch (\Exception $th) {
            Log::info($th);
            $session->forget('tokens');
        }
        return $next($request);
    }

    public function get_token()
    {
      $ch = curl_init();
      curl_setopt_array($ch, [
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_POST           => true,
          //CURLOPT_POSTFIELDS     => $data,
          CURLOPT_URL            => 'https://api.sandbox.paypal.com/v1/oauth2/token',
          CURLOPT_HTTPHEADER     => ['Content-Type:application/x-www-form-urlencoded'],
          CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
          CURLOPT_USERPWD        =>  env('SANDBOX_USER_BUSINESS') . ":" . env('SANDBOX_PASSWORD_BUSINESS')
      ]);
      $response = curl_exec($ch);
      curl_close($ch);
      $jsonDecoded = json_decode($response, true); // Returns an array

      return $jsonDecoded;
    }
}
