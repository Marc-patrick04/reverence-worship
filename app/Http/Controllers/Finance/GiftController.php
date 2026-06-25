<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GiftController extends Controller
{
    public function getGifts(Request $request)
    {
        try {
            $gifts = DB::table('gifts')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json(['success' => true, 'gifts' => $gifts]);
        } catch (\Exception $e) {
            Log::error('getGifts error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}