<?php

namespace App\Http\Controllers;

use App\Helpers\CustomerKey;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $selectedId = $request->query('id');

        $query = Customer::withCount('services')
            ->with(['services' => function ($s) {
                $s->orderByDesc('received_at');
            }]);

        if (filled($q)) {
            $digits = preg_replace('/\D/', '', $q);
            $query->where(function ($sub) use ($q, $digits) {
                $sub->where('name', 'like', "%{$q}%");
                if (strlen($digits) >= 3) {
                    $sub->orWhere('phone', 'like', "%{$digits}%");
                }
            });
        }

        $customers = $query->orderBy('name')->get();

        $selectedCustomer = null;
        if ($selectedId) {
            $selectedCustomer = $customers->firstWhere('id', (int) $selectedId)
                ?? Customer::with(['services' => function ($s) {
                    $s->orderByDesc('received_at');
                }])->find($selectedId);
        } elseif ($customers->isNotEmpty()) {
            $selectedCustomer = $customers->first();
        }

        return view('customers.index', [
            'customers' => $customers,
            'selected' => $selectedCustomer,
            'q' => $q,
        ]);
    }

    public function hint(Request $request): JsonResponse
    {
        $phone = (string) $request->query('phone', '');
        $digits = CustomerKey::normalizePhone($phone);

        if (strlen($digits) < 8) {
            return response()->json(['names' => []]);
        }

        $names = Customer::where('phone', $digits)
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return response()->json(['names' => $names]);
    }

    public function update(UpdateCustomerRequest $request, int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        $name = trim(preg_replace('/\s+/', ' ', (string) ($request->input('name') ?: $request->input('nama'))));
        $nameKey = CustomerKey::normalizeName($name);
        $phone = CustomerKey::normalizePhone((string) ($request->input('phone') ?: $request->input('hp')));

        $customer->update([
            'name' => $name,
            'name_key' => $nameKey,
            'phone' => $phone,
        ]);

        return redirect()
            ->route('customers.index', ['id' => $customer->id])
            ->with('success', 'Data pelanggan disimpan');
    }
}
