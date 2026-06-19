<?php

namespace App\Http\Controllers;

use App\Service\SsoService;
use Illuminate\Http\Request;

class SsoTestController extends Controller
{
    public function login(SsoService $sso)
    {
        return response()->json(
            $sso->loginWarga(
                env('IAE_WARGA_EMAIL')
            )
        );
    }
    public function m2m(SsoService $sso)
    {
        return response()->json(
            $sso->getM2mToken()
        );
    }
}