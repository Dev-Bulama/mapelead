@extends('layouts.admin')
@section('title', 'Coupons')

@section('content')
<div class="space-y-6" x-data="couponsPage()">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Coupons</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $coupons->total() }} coupon{{ $coupons->total() !== 1 ? 's' : '' }} total</p>
        </div>
        <button @click="showCreate = true"
                class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Coupon
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Stats Row --}}
    @php
    $total  = $coupons->total();
    $active = \App\Models\Coupon::where('is_active', true)->count();
    $used   = \App\Models\Coupon::where('used_count', '>', 0)->count();
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Total</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Active</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $active }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Used</p>
            <p class="text-3xl font-bold text-brand-600 mt-1">{{ $used }}</p>
        </div>
    </div>

    {{-- Coupons Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        @if($coupons->isEmpty())
        <div class="p-16 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <p class="text-gray-500 font-medium">No coupons yet</p>
            <button @click="showCreate = true" class="mt-3 text-brand-600 hover:underline text-sm">Create your first coupon →</button>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Code</th>
                        <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Discount</th>
                        <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Min Order</th>
                        <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Usage</th>
                        <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Validity</th>
                        <th class="text-left font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Status</th>
                        <th class="text-right font-semibold text-gray-500 uppercase tracking-wide text-xs px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($coupons as $coupon)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div>
                                <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded text-xs tracking-widest">{{ $coupon->code }}</span>
                                @if($coupon->description)
                                <p class="text-xs text-gray-400 mt-1">{{ $coupon->description }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-gray-900">
                                @if($coupon->type === 'percentage')
                                    {{ number_format($coupon->value, 0) }}%
                                    @if($coupon->maximum_discount)
                                    <span class="text-xs text-gray-400 font-normal">(max ₦{{ number_format($coupon->maximum_discount, 0) }})</span>
                                    @endif
                                @else
                                    ₦{{ number_format($coupon->value, 0) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">
                            {{ $coupon->minimum_order > 0 ? '₦' . number_format($coupon->minimum_order, 0) : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-gray-900 font-medium">{{ $coupon->used_count }}</span>
                            <span class="text-gray-400">{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-500">
                            @if($coupon->starts_at)
                            <div>From {{ $coupon->starts_at->format('M d, Y') }}</div>
                            @endif
                            @if($coupon->expires_at)
                            <div class="{{ now()->gt($coupon->expires_at) ? 'text-red-500' : '' }}">
                                Until {{ $coupon->expires_at->format('M d, Y') }}
                            </div>
                            @else
                            <span class="text-gray-400">No expiry</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php $valid = $coupon->isValid(); @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $valid ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $valid ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $valid ? 'Active' : ($coupon->is_active ? 'Expired/Limit' : 'Inactive') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit({{ $coupon->id }}, '{{ addslashes($coupon->code) }}', '{{ addslashes($coupon->description ?? '') }}', '{{ $coupon->type }}', {{ $coupon->value }}, {{ $coupon->minimum_order ?? 0 }}, {{ $coupon->maximum_discount ?? 0 }}, {{ $coupon->usage_limit ?? 0 }}, '{{ $coupon->starts_at?->format('Y-m-d') ?? '' }}', '{{ $coupon->expires_at?->format('Y-m-d') ?? '' }}', {{ $coupon->is_active ? 'true' : 'false' }})"
                                        class="text-xs text-brand-600 hover:text-brand-800 font-medium px-3 py-1.5 rounded-lg hover:bg-brand-50 transition-colors">
                                    Edit
                                </button>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                      onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $coupons->links() }}</div>
        @endif
        @endif
    </div>

    {{-- Create Modal --}}
    <div x-show="showCreate" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
         @click.self="showCreate = false" @keydown.escape.window="showCreate = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition duration-150" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
             @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-lg">Create Coupon</h3>
                <button @click="showCreate = false" class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @include('admin.coupons._form')
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showCreate = false" class="text-sm text-gray-500 hover:text-gray-700 font-medium px-4 py-2 transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">Create Coupon</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="showEdit" x-cloak
         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
         @click.self="showEdit = false" @keydown.escape.window="showEdit = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition duration-150" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
             @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-lg">Edit Coupon</h3>
                <button @click="showEdit = false" class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="'/admin/coupons/' + editId" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Coupon Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" :value="editCode" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                           placeholder="e.g. SAVE20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <input type="text" name="description" :value="editDescription"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
                           placeholder="Internal note about this coupon">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select name="type" x-model="editType" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₦)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Value <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal" x-text="editType === 'percentage' ? '(%)' : '(₦)'"></span>
                        </label>
                        <input type="number" name="value" :value="editValue" required min="0.01" step="0.01"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Min Order Amount (₦)</label>
                        <input type="number" name="minimum_order" :value="editMinOrder" min="0" step="0.01"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
                               placeholder="0">
                    </div>
                    <div x-show="editType === 'percentage'">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Max Discount (₦)</label>
                        <input type="number" name="maximum_discount" :value="editMaxDiscount" min="0" step="0.01"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
                               placeholder="Leave blank for no cap">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Usage Limit</label>
                    <input type="number" name="usage_limit" :value="editUsageLimit > 0 ? editUsageLimit : ''" min="1"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
                           placeholder="Leave blank for unlimited">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Date</label>
                        <input type="date" name="starts_at" :value="editStartsAt"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date</label>
                        <input type="date" name="expires_at" :value="editExpiresAt"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" :checked="editIsActive"
                           class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-gray-700">Active (can be used by customers)</span>
                </label>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="button" @click="showEdit = false" class="text-sm text-gray-500 hover:text-gray-700 font-medium px-4 py-2 transition-colors">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function couponsPage() {
    return {
        showCreate: {{ $errors->any() ? 'true' : 'false' }},
        showEdit: false,
        editId: null,
        editCode: '', editDescription: '', editType: 'percentage', editValue: 0,
        editMinOrder: 0, editMaxDiscount: 0, editUsageLimit: 0,
        editStartsAt: '', editExpiresAt: '', editIsActive: true,
        openEdit(id, code, description, type, value, minOrder, maxDiscount, usageLimit, startsAt, expiresAt, isActive) {
            this.editId = id;
            this.editCode = code;
            this.editDescription = description;
            this.editType = type;
            this.editValue = value;
            this.editMinOrder = minOrder;
            this.editMaxDiscount = maxDiscount;
            this.editUsageLimit = usageLimit;
            this.editStartsAt = startsAt;
            this.editExpiresAt = expiresAt;
            this.editIsActive = isActive;
            this.showEdit = true;
        }
    }
}
</script>
@endpush
@endsection
