<?php

namespace App\Http\Controllers\Api\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\VehicleDetail;
use App\Services\VehicleSearchService;
use Illuminate\Http\Request;

class VehicleSearchController extends Controller
{
    public function __construct(protected VehicleSearchService $vehicleSearchService)
    {
    }

    public function balance(Request $request)
    {
        $dealer = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'wallet_balance' => (float) $dealer->walletBalance(),
                'charge_per_search' => (float) Setting::getDealerApiVehicleSearchCharge(),
                'currency' => 'INR',
            ],
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|min:4|max:20',
        ]);

        $dealer = $request->user();
        $charge = (float) Setting::getDealerApiVehicleSearchCharge();

        $result = $this->vehicleSearchService
            ->setCharge($charge)
            ->setContext('api', $request->ip())
            ->search($dealer, $validated['registration_number']);

        if (! $result['success']) {
            $insufficient = str_contains((string) $result['message'], 'Insufficient');

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'data' => null,
            ], $insufficient ? 402 : 422);
        }

        $wasCached = (bool) ($result['cached'] ?? false);

        return response()->json([
            'success' => true,
            'cached' => $wasCached,
            'charged' => $wasCached ? 0.0 : $charge,
            'wallet_balance' => (float) $dealer->fresh()->walletBalance(),
            'message' => $wasCached ? 'Vehicle details retrieved from cache (no charge).' : 'Vehicle details retrieved successfully.',
            'data' => $this->transform($result['data']),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $record = VehicleDetail::where('id', $id)
            ->where('dealer_id', $request->user()->id)
            ->first();

        if (! $record) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $this->transform($record)]);
    }

    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->integer('per_page', 20), 100));

        $records = VehicleDetail::where('dealer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $records->through(fn (VehicleDetail $record) => [
                'id' => $record->id,
                'registration_number' => $record->registration_number,
                'is_success' => (bool) $record->is_success,
                'searched_at' => optional($record->created_at)->toIso8601String(),
            ]),
        ]);
    }

    private function transform(VehicleDetail $record): array
    {
        return [
            'id' => $record->id,
            'registration_number' => $record->registration_number,
            'is_success' => (bool) $record->is_success,
            'searched_at' => optional($record->created_at)->toIso8601String(),
            'details' => is_array($record->raw_response) ? $record->raw_response : [],
        ];
    }
}
