<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function getSettings(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            $settings = DB::table('finance_term_settings')
                ->where('current_year', $year)
                ->first();
            
            if (!$settings) {
                return response()->json([
                    'success' => true, 
                    'settings' => null,
                    'message' => 'No settings found for year ' . $year
                ]);
            }
            
            $termPercentages = json_decode($settings->term_percentages, true);
            $settings->term_percentages = $termPercentages;
            $settings->term_numbers = json_decode($settings->term_numbers, true) ?: array_keys($termPercentages);
            
            return response()->json(['success' => true, 'settings' => $settings]);
        } catch (\Exception $e) {
            Log::error('getSettings error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'settings' => null
            ]);
        }
    }

    public function updateSettings(Request $request)
    {
        try {
            $termPercentages = json_decode($request->term_percentages, true);
            $termNumbers = json_decode($request->term_numbers, true);
            $numberOfTerms = $request->number_of_terms;
            $currentYear = $request->current_year;
            
            if (empty($currentYear)) {
                $currentYear = date('Y');
            }
            
            $total = array_sum($termPercentages);
            
            if (abs($total - 100) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total percentage must equal 100%'
                ], 400);
            }
            
            $termPercentagesAssoc = [];
            foreach ($termNumbers as $index => $termNum) {
                $termPercentagesAssoc[$termNum] = (float) $termPercentages[$index];
            }
            
            $existingSettings = DB::table('finance_term_settings')
                ->where('current_year', $currentYear)
                ->first();
            
            if ($existingSettings) {
                DB::table('finance_term_settings')
                    ->where('id', $existingSettings->id)
                    ->update([
                        'number_of_terms' => (int) $numberOfTerms,
                        'term_percentages' => json_encode($termPercentagesAssoc),
                        'term_numbers' => json_encode($termNumbers),
                        'updated_at' => now()
                    ]);
            } else {
                DB::table('finance_term_settings')->insert([
                    'current_year' => $currentYear,
                    'number_of_terms' => (int) $numberOfTerms,
                    'term_percentages' => json_encode($termPercentagesAssoc),
                    'term_numbers' => json_encode($termNumbers),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            $savedSettings = DB::table('finance_term_settings')
                ->where('current_year', $currentYear)
                ->first();
            
            return response()->json([
                'success' => true, 
                'message' => 'Settings saved successfully',
                'settings' => [
                    'current_year' => $savedSettings->current_year,
                    'number_of_terms' => $savedSettings->number_of_terms,
                    'term_percentages' => json_decode($savedSettings->term_percentages, true),
                    'term_numbers' => json_decode($savedSettings->term_numbers, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving settings: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}