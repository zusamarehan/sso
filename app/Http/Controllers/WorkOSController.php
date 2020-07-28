<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use WorkOS\Resource\ConnectionType;
use WorkOS\SSO;

class WorkOSController extends Controller
{
    public function provider($provider)
    {
        $workOS_connection = ConnectionType::GoogleOAuth;
        if($provider == 'okta') $workOS_connection = null;
        if ($provider == 'azure') $workOS_connection = ConnectionType::AzureSAML;

        return $workOS_connection;
    }

    public function sso($provider)
    {
        $domain = request()->input('domain', null);
        $url = (new SSO())->getAuthorizationUrl(
            $domain,
            env('WORKOS_CALLBACK'),
            ['domain' => $domain],
            $this->provider($provider) // Pass along provider if we don't have a domain
        );

        $response = Http::get($url);
        if($response->status() == 200) {
            return redirect($url);
        }

        return response()->json(json_decode($response->body(), true), 400);
    }

    public function organization($email)
    {
        $domain = explode('@', $email)[1];

        return Organization::query()
            ->where('domain', $domain)
            ->first();
    }

    public function callback()
    {
        $profile = (new SSO())->getProfile(request()->input('code'))->toArray();

        if($profile) {
            $org = $this->organization($profile['email']);

            if($org) {
                $user = User::query()
                    ->where('email', $profile['email'])
                    ->where('organization_id', $org->id)
                    ->first();

                if(!$user) {
                    $user = new User();
                    $user->name = $profile['firstName'].' '.$profile['firstName'];
                    $user->email = $profile['email'];
                    $user->email_verified_at = now();
                    $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password
                    $user->organization_id = $org->id;
                    $user->remember_token = Str::random(10);
                    $user->save();
                }

                if($user->is_blocked == 1) {
                    return response()->json(['message' => 'You have been blocked and cannot use the application anymore'], 422);
                }

                Auth::loginUsingId($user->id);
                return redirect(RouteServiceProvider::HOME);
            }
            return response()->json(['message' => 'Organization not found in Pudding'], 422);
        }
    }

    public function okta()
    {
        return view('okta.login');
    }
}
