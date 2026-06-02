@extends('layouts.app')

@section('title', 'My Contributions')
@section('page-title', 'Contribution Management')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">My Contributions</h1>
       
    </div>

    <!-- TOP GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- LEFT CARD - Annual Contribution -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">
                    Your {{ $currentYear }} Annual Contribution
                </h2>
                @if(auth()->user()->isSuperAdmin())
                <button onclick="openEditAmountModal()"
                    class="text-sm text-blue-600 hover:text-blue-800 border border-blue-300 px-3 py-1 rounded-lg transition">
                    <i class="fas fa-edit mr-1"></i> Edit Amount
                </button>
                @endif
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-600">Annual Amount:</span>
                    <span class="text-2xl font-bold text-blue-600">RF {{ number_format($totalRequired, 0, ',', '.') }}</span>
                </div>

                <!-- Bible Verse -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mt-3">
                    <h4 class="font-bold text-blue-800 text-sm mb-2">2 Abakorinto 9:7</h4>
                    <p class="italic text-blue-700 text-xs leading-relaxed">
                        "Umuntu wese atange nk'uko abigambiriye mu mutima we, atinuba kandi adahatwa kuko Imana ikunda utanga anezerewe."
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT CARD - Progress -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">My Progress</h2>

            <div class="flex justify-between mb-2">
                <span class="text-sm text-gray-600">Overall Progress</span>
                <span class="text-sm font-medium">
                    RF {{ number_format($totalPaid, 0, ',', '.') }} / RF {{ number_format($totalRequired, 0, ',', '.') }}
                </span>
            </div>

            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mb-2">
                <div class="h-full bg-blue-600 rounded-full" style="width: {{ min($progressPercent, 100) }}%"></div>
            </div>

            <p class="text-xs text-gray-500 mb-5">{{ number_format($progressPercent, 1) }}% complete</p>

            <!-- TERM CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach([1, 2, 3] as $termNum)
                @php
                    $contribution = $contributions[$termNum] ?? null;
                    $amount = $contribution ? $contribution->amount : 0;
                    $requiredAmount = $settings->{'term'.$termNum.'_amount'} ?? 0;
                    $status = $contribution ? $contribution->status : 'pending';
                    
                    if ($termNum == 1) {
                        $borderColor = $status == 'completed' ? 'border-green-200' : 'border-gray-200';
                        $bgColor = $status == 'completed' ? 'bg-green-50' : 'bg-white';
                        $statusColor = 'green';
                        $statusIcon = '✓';
                        $statusText = 'completed';
                    } elseif ($termNum == 2) {
                        $borderColor = $status == 'partial' ? 'border-yellow-200' : 'border-gray-200';
                        $bgColor = $status == 'partial' ? 'bg-yellow-50' : 'bg-white';
                        $statusColor = 'yellow';
                        $statusIcon = '⚡';
                        $statusText = 'partial';
                    } else {
                        $borderColor = $status == 'pending' ? 'border-gray-200' : 'border-gray-200';
                        $bgColor = 'bg-white';
                        $statusColor = 'gray';
                        $statusIcon = '⌛';
                        $statusText = 'pending';
                    }
                    
                    $progressPercentTerm = $requiredAmount > 0 ? ($amount / $requiredAmount) * 100 : 0;
                @endphp
                <div class="border-2 {{ $borderColor }} {{ $bgColor }} rounded-xl p-3 text-center">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Term {{ $termNum }}</h3>
                    <p class="text-xl font-bold text-gray-800">RF {{ number_format($amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">of RF {{ number_format($requiredAmount, 0, ',', '.') }}</p>
                    
                    <div class="w-full h-1.5 bg-gray-200 rounded-full mt-3">
                        <div class="h-1.5 rounded-full 
                            {{ $status == 'completed' ? 'bg-green-500' : ($status == 'partial' ? 'bg-yellow-500' : 'bg-gray-300') }}" 
                            style="width: {{ min($progressPercentTerm, 100) }}%"></div>
                    </div>

                    <div class="mt-3 inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                        {{ $status == 'completed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $status == 'partial' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $status == 'pending' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ $statusIcon }} {{ $statusText }}
                    </div>

                    @if($status == 'completed')
                        <p class="text-green-600 text-xs mt-2">Complete!</p>
                    @else
                        <button onclick="openPaymentModal({{ $termNum }}, {{ $requiredAmount }})" 
                                class="block w-full mt-3 text-blue-600 text-xs font-medium hover:underline">
                            Tap to submit
                        </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 id="paymentModalTitle" class="text-lg font-bold text-gray-800">Submit Payment</h3>
            <button onclick="closeModal('paymentModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('financial.submit-payment') }}">
            @csrf
            <input type="hidden" name="term" id="paymentTerm">
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Amount (RWF)</label>
                    <input type="number" name="amount" id="paymentAmount" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="maxAmountHint" class="text-xs text-gray-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Select Method</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Transaction ID / Reference</label>
                    <input type="text" name="transaction_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-5 pt-3 border-t">
                <button type="button" onclick="closeModal('paymentModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Submit Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Annual Amount Modal (Super Admin only) -->
<div id="editAmountModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg font-bold text-gray-800">Edit Annual Contribution Amount</h3>
            <button onclick="closeModal('editAmountModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('financial.update-annual-amount') }}">
            @csrf
            <div class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Year</label>
                    <input type="number" name="year" value="{{ $currentYear }}" readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Annual Amount (RWF)</label>
                    <input type="number" name="annual_amount" required value="{{ $totalRequired }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Term 1: 40%, Term 2: 30%, Term 3: 30%</p>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-5 pt-3 border-t">
                <button type="button" onclick="closeModal('editAmountModal')" class="px-4 py-2 border rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(term, maxAmount) {
    document.getElementById('paymentTerm').value = term;
    document.getElementById('paymentModalTitle').innerHTML = 'Submit Payment - Term ' + term;
    document.getElementById('maxAmountHint').innerHTML = 'Maximum: ' + numberFormat(maxAmount) + ' RWF';
    document.getElementById('paymentModal').classList.remove('hidden');
}

function openEditAmountModal() {
    document.getElementById('editAmountModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function numberFormat(num) {
    return new Intl.NumberFormat().format(num);
}
</script>

<style>
.modal { display: none; }
.modal:not(.hidden) { display: block !important; }
</style>
@endsection