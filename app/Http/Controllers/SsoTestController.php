<?php

namespace App\Http\Controllers;

use App\Service\SsoService;

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
}