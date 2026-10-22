<?php

namespace App\Modules\Home\Http\Controllers\Web;

use App\Http\Controllers\WebController;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HomeController extends WebController
{
    protected $module = 'home';

    public function show(): View|JsonResponse
    {
        $view = config('site.pages')[preg_replace('/\.html$/', '', trim(request()->path(), '/'))] ?? null;
        abort_unless($view, 404);

        return $this->responseModule([], $view);
    }
}
