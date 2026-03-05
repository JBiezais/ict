@props(['categories', 'selected' => [], 'name' => 'category_ids'])

@php
    $categoriesList = $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()->all();
    $selectedIds = collect(is_array($selected) ? $selected : [])
        ->map(fn($v) => (int) $v)
        ->filter()
        ->all();
    $selectedList = collect($selectedIds)
        ->map(function ($id) use ($categories) {
            $cat = $categories->firstWhere('id', $id);
            return $cat ? ['id' => $cat->id, 'name' => $cat->name] : null;
        })
        ->filter()
        ->values()
        ->all();
@endphp

<div class="relative" x-data="{
    allCategories: @js($categoriesList),
    selectedTags: @js($selectedList),
    inputValue: '',
    suggestionsOpen: false,
    loading: false,
    storeUrl: @js(route('categories.store')),
    get filteredSuggestions() {
        const q = this.inputValue.trim().toLowerCase();
        const selectedIds = this.selectedTags.map(t => t.id);
        const unselected = this.allCategories.filter(c => !selectedIds.includes(c.id));
        if (!q) return unselected.slice(0, 15);
        return unselected
            .filter(c => c.name.toLowerCase().includes(q))
            .slice(0, 10);
    },
    get canAddNew() {
        const val = this.inputValue.trim();
        return val.length > 0 && val.length <= 100 &&
            !this.allCategories.some(c => c.name === val) &&
            !this.selectedTags.some(t => t.name === val);
    },
    async addCurrentInput() {
        const val = this.inputValue.trim();
        if (!val || val.length > 100) return;
        if (this.selectedTags.some(t => t.name === val)) {
            this.inputValue = '';
            return;
        }
        const existing = this.allCategories.find(c => c.name === val);
        if (existing) {
            if (!this.selectedTags.some(t => t.id === existing.id)) {
                this.selectedTags.push(existing);
            }
            this.inputValue = '';
            return;
        }
        await this.createCategory(val);
    },
    async createCategory(name) {
        this.loading = true;
        try {
            const csrf = document.querySelector('meta[name=csrf-token]')?.content;
            const res = await fetch(this.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ name: name }),
                credentials: 'same-origin',
            });
            if (res.ok) {
                const data = await res.json();
                const item = { id: data.id, name: data.name };
                this.selectedTags.push(item);
                this.allCategories.push(item);
            }
            this.inputValue = '';
        } finally {
            this.loading = false;
        }
    },
    addSuggestion(item) {
        if (!this.selectedTags.some(t => t.id === item.id)) {
            this.selectedTags.push(item);
        }
        this.inputValue = '';
    },
    removeTag(index) {
        this.selectedTags.splice(index, 1);
    }
}">
    <div
        class="flex flex-wrap gap-1.5 min-h-[42px] p-2 border border-neutral-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-800 focus-within:border-emerald-500 dark:focus-within:border-emerald-400 focus-within:ring-1 focus-within:ring-emerald-500 dark:focus-within:ring-emerald-400">
        <template x-for="(tag, index) in selectedTags" :key="tag.id">
            <span
                class="inline-flex items-center gap-1 pl-2.5 pr-1 py-0.5 rounded-full text-xs font-medium bg-emerald-50 dark:bg-zinc-700/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-zinc-600">
                <span x-text="tag.name"></span>
                <button type="button"
                    class="p-0.5 rounded hover:bg-emerald-200/50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                    :aria-label="`{{ __('Remove') }} ` + tag.name" @click="removeTag(index)">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </span>
        </template>
        <input type="text"
            class="flex-1 min-w-[120px] py-1 px-2 border-0 bg-transparent text-neutral-900 dark:text-zinc-100 placeholder-neutral-400 dark:placeholder-zinc-500 focus:ring-0 focus:outline-none text-sm"
            placeholder="{{ __('Select from the list or type to create a new one') }}" x-model="inputValue"
            :disabled="loading" @keydown.enter.prevent="$nextTick(() => addCurrentInput())"
            @keydown.comma.prevent="$nextTick(() => addCurrentInput())" @input="suggestionsOpen = true"
            @focus="suggestionsOpen = true" @blur="setTimeout(() => suggestionsOpen = false, 150)" role="combobox"
            :aria-expanded="suggestionsOpen" aria-autocomplete="list" />
    </div>

    <div x-show="suggestionsOpen && (filteredSuggestions.length > 0 || canAddNew) && !loading"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute z-50 mt-1 w-full max-h-48 overflow-auto rounded-md shadow-lg ring-1 ring-neutral-900/5 dark:ring-neutral-100/5 py-1 bg-white dark:bg-zinc-800"
        style="display: none;" role="listbox">
        <template x-if="canAddNew">
            <button type="button"
                class="w-full px-4 py-2 text-left text-sm text-emerald-600 dark:text-emerald-400 hover:bg-neutral-50 dark:hover:bg-zinc-700/80 focus:bg-neutral-50 dark:focus:bg-zinc-700/80 focus:outline-none"
                role="option" @mousedown.prevent="addCurrentInput()">
                <span x-text="`{{ __('Add') }} '` + inputValue.trim() + `'`"></span>
            </button>
        </template>
        <template x-for="item in filteredSuggestions" :key="item.id">
            <button type="button"
                class="w-full px-4 py-2 text-left text-sm text-neutral-700 dark:text-zinc-300 hover:bg-neutral-50 dark:hover:bg-zinc-700/80 focus:bg-neutral-50 dark:focus:bg-zinc-700/80 focus:outline-none"
                role="option" @mousedown.prevent="addSuggestion(item)" x-text="item.name"></button>
        </template>
    </div>

    <template x-for="tag in selectedTags" :key="tag.id">
        <input type="hidden" name="{{ $name }}[]" :value="tag.id">
    </template>
</div>
