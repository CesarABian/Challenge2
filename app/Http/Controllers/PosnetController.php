<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use App\Services\PosnetService;
use Illuminate\Routing\Controller;

class PosnetController extends Controller
{
    /**
     * $service
     *
     * @var PosnetService
     */
    protected $service;

    /**
     * __construct
     *
     * @param  PosnetService $service
     * @return void
     */
    public function __construct(PosnetService $service)
    {
        $this->service = $service;
    }

    public function doRegister(Request $request)
    {
        return $this->service->register($request);
    }

    public function doPayment(Request $request)
    {
        return $this->service->payment($request);
    }
}
