<div x-data="{ type: 'percentage' }">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Coupon Code <span class="text-red-500">*</span></label>
        <input type="text" name="code" value="{{ old('code') }}" required
               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm font-mono uppercase focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
               placeholder="e.g. SAVE20" oninput="this.value = this.value.toUpperCase()">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
        <input type="text" name="description" value="{{ old('description') }}"
               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none"
               placeholder="Internal note about this coupon">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
            <select name="type" x-model="type" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                <option value="percentage">Percentage (%)</option>
                <option value="fixed">Fixed Amount (₦)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Value <span class="text-red-500">*</span>
                <span class="text-gray-400 font-normal" x-text="type === 'percentage' ? '(%)' : '(₦)'"></span>
            </label>
            <input type="number" name="value" value="{{ old('value') }}" required min="0.01" step="0.01"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
                   placeholder="e.g. 20">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Min Order Amount (₦)</label>
            <input type="number" name="minimum_order" value="{{ old('minimum_order', 0) }}" min="0" step="0.01"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
                   placeholder="0 for no minimum">
        </div>
        <div x-show="type === 'percentage'">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Max Discount (₦)</label>
            <input type="number" name="maximum_discount" value="{{ old('maximum_discount') }}" min="0" step="0.01"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
                   placeholder="Leave blank for no cap">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Usage Limit</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none"
               placeholder="Leave blank for unlimited">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Date</label>
            <input type="date" name="starts_at" value="{{ old('starts_at') }}"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date</label>
            <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
    </div>
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" checked
               class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
        <span class="text-sm text-gray-700">Active (can be used by customers)</span>
    </label>
</div>
