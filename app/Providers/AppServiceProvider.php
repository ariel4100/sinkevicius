<?php

namespace App\Providers;

use App\Models\Content;
use App\Models\Meta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use function PHPSTORM_META\map;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(request()->is('adm*')){

//            return 'adm.layouts.app';
            Inertia::setRootView('adm.layouts.app');
        } else {
            Inertia::setRootView('app');
//            return 'app';
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (php_sapi_name() != 'cli') {
            Collection::macro('slug', function () {
                return $this->map(function ($value) {
                    return Str::slug($value);
                });
            });
            $metas =  Meta::get();
            $contacto = Content::where('section', 'contacto')->first();
            $telefonos = collect(@$contacto->data['phones']);
//            dd($contacto->data['google']);
            Inertia::share([
                'key_web_captcha' => @$contacto->data['google'][0]['key'],
                'key_secret_captcha' => @$contacto->data['google'][1]['key'],
                'appUrl' => config('app.url'),
                'appName' => config('app.name'),
                'favicon' => @$contacto->image[0]['image'] ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($contacto->image[0]['image']) : '',
                'header' => @$contacto->image[1]['image'] ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($contacto->image[1]['image']) : '',
                'footer' => @$contacto->image[2]['image'] ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($contacto->image[2]['image']) : '',
                'redes' => @$contacto->data['redes'],
                'direcciones' => @$contacto->data['address'],
                'emails' => @$contacto->data['emails'],
                'telefonos' => $telefonos->where('type','tel')->values(),
                'whatsapps' => $telefonos->where('type','wha')->values(),
                'idioma' => LaravelLocalization::getSupportedLocales(),
                'flash' => function () {
                    return [
                        'success' => Session::get('success'),
                        'message' => Session::get('message'),
                        'error' => Session::get('error'),
                    ];
                },
                'auth' => function () {
                    return [
                        'user' => auth()->guard('client')->user() ? [
                            'id' => auth()->guard('client')->user()->id,
                            'name' => auth()->guard('client')->user()->name,
                            'username' => auth()->guard('client')->user()->username,
                            'email' => auth()->guard('client')->user()->email,
                        ] : null,
                        'check' => auth()->guard('client')->check()
                    ];
                },

            ]);


            view()->share([
                'favicon' =>  @$contacto->image[0]['image'] ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($contacto->image[0]['image']) : '',
                'header' =>  @$contacto->image[1]['image'] ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($contacto->image[1]['image']) : '',
                'metas' => @$metas,
                'key_web_captcha' => @$contacto->data['google'][0]['key'],
                'key_secret_captcha' => @$contacto->data['google'][1]['key'],
            ]);
        }
    }
}
