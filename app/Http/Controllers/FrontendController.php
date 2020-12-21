<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Category;
use App\Models\Content;
use App\Models\Descargas;
use App\Models\Family;
use App\Models\News;
use App\Models\Product;
use App\Models\Slider;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FrontendController extends Controller
{
    public function home()
    {

        $destacados_familias = Family::where('featured',1)->orderBy('order')->get();
        $destacados_productos = Product::where('featured',1)->orderBy('order')->get();
        $sliders = Slider::where('section','inicio')->get();
        $contenido = Content::with('block')->where('section','inicio')->first();
        $contenidoMap =  $contenido->Block->map(function ($item) {
            return [
                'title' => $item->title,
                'text' => $item->text,
                'type' => $item->type,
                'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
            ];
        });
        //$novedades = News::orderBy('order')->limit(3)->get();
        return Inertia::render('Web/Home', [
            'sliders' => $sliders->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                ];
            }),
            'bloques' => $contenidoMap->whereNull('type')->values(),
            'imagenes' => $contenidoMap->where('type','img')->values(),
            'familias' => $destacados_familias->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'ruta' => route('productos',$item->slug),
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                ];
            }),
            'productos' => $destacados_productos->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'ruta' => route('producto',$item->slug),
                    'image' => $item->gallery ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->gallery[0]) : '',
                ];
            }),

        ]);
    }
    public function empresa()
    {
        $sliders = Slider::where('section','empresa')->get();
        $contenido = Content::with('block')->where('section','empresa')->first();
        $contenidoMap =  $contenido->Block->map(function ($item) {
            return [
                'title' => $item->title,
                'text' => $item->text,
                'type' => $item->type,
                'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
            ];
        });

        return Inertia::render('Web/Empresa', [
            'sliders' => $sliders->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                ];
            }),
            'texto' => $contenidoMap->where('type','texto')->values(),

        ]);
    }
    public function calidad()
    {
        $sliders = Slider::where('section','calidad')->get();
        $contenido = Content::with('block')->where('section','calidad')->first();
        $contenidoMap =  $contenido->Block->map(function ($item) {
            return [
                'title' => $item->title,
                'text' => $item->text,
                'type' => $item->type,
                'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
            ];
        });

        return Inertia::render('Web/Calidad', [
            'sliders' => $sliders->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                ];
            }),
            'textos' => $contenidoMap->where('type','texto')->values(),
            'archivos' => $contenidoMap->where('type','img')->values(),
            'contenido' => $contenido,
        ]);
    }


    public function familias($slug = '')
    {
        $lang = app()->getLocale();
        $familias = Family::whereNull('padre_id',null)->orderBy('order')->get();

        return Inertia::render('Web/Product/Family', [
            'familias' => $familias->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'ruta' => route('productos',$item->slug),
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                ];
            }),
        ]);

    }

    public function productos($slug = '')
    {
        $lang = app()->getLocale();
        $familia = Family::where("slug->$lang",$slug)->first();
        $familias = Family::whereNull('padre_id',null)->orderBy('order')->get();

        $productos = $familia->productos->sortBy('order');

        return Inertia::render('Web/Product/Family', [
            'familia' => $familia->only('title','id','slug'),
            'sidenav' => 1,
            'productos' => $productos->values()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'ruta' => route('producto',$item->slug),
                    'image' => $item->gallery ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->gallery[0]) : '',
                ];
            }),
            'familias' => $familias->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'order' => $item->order,
                    'ruta' => route('productos',$item->slug),
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                    'productos' => $item->productos->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'slug' => $item->slug,
                            'order' => $item->order,
                            'ruta' => route('producto',$item->slug),
                        ];
                    }),
                ];
            }),
        ])->withViewData(['title' => $familia->title]);
    }

    public function producto($slug = '')
    {
        $lang = app()->getLocale();
        $producto = Product::where("slug->$lang",$slug)->first();
        $familia = $producto->family;
        $galeria = collect($producto->gallery)->map(function ($item) {
            return Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item);
        });
        $productos = $producto->related;
        $familias = Family::whereNull('padre_id',null)->orderBy('order')->get();

        return Inertia::render('Web/Product/Product', [
            'familias' => $familias->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'order' => $item->order,
                    'ruta' => route('productos',$item->slug),
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                    'productos' => $item->productos->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'slug' => $item->slug,
                            'order' => $item->order,
                            'ruta' => route('producto',$item->slug),
                        ];
                    }),
                ];
            }),
            'familia' => $familia->only('title','id','slug'),
            'gallery' => $galeria,
            'producto' => $producto->only('file','gallery','banner','video','text_video','text','title','id','slug'),
            'productos' => $productos->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'ruta' => route('producto',$item->slug),
                    'image' => $item->gallery ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->gallery[0]) : '',
                ];
            }),

        ])->withViewData(['title' => $producto->title]);
    }


    public function contacto()
    {
        $sliders = Slider::where('section','contacto')->get();
        $contenido = Content::with('block')->where('section','contacto')->first();

        return Inertia::render('Web/Contacto', [
            'sliders' => $sliders->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'text' => $item->text,
                    'order' => $item->order,
                    'image' => $item->image ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->image) : '',
                ];
            }),
            'contenido' => $contenido->only('title'),
        ]);
    }

    public function buscador(Request $request)
    {

        if ($request->name){
            $productos = Product::where('title', 'LIKE', "%$request->name%")
                ->orWhere('text', 'LIKE',"%$request->name%")
                ->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'text' => $item->description,
                        'order' => $item->order,
                        'ruta' => route('producto',$item->slug),
                        'image' => $item->gallery ? Storage::disk(env('DEFAULT_STORAGE_DISK'))->url($item->gallery[0]) : '',
                    ];
                });
        }else{
            $productos = [];
        }


        return Inertia::render('Web/Buscador', [
            'productos' => $productos
        ]);
    }

}
