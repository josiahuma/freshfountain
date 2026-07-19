<x-filament-panels::page>
    <x-filament::section>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Year</label>
                <select wire:model.live="year" class="w-full rounded-lg border-gray-300 bg-white dark:border-white/10 dark:bg-gray-900">
                    @foreach ($this->getYears() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Service / Event</label>
                <select wire:model.live="serviceTypeId" class="w-full rounded-lg border-gray-300 bg-white dark:border-white/10 dark:bg-gray-900">
                    <option value="">All services</option>
                    @foreach ($this->getServiceTypes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-filament::section>

    <x-filament-widgets::widgets
    :widgets="$this->getHeaderWidgets()"
    :columns="$this->getHeaderWidgetsColumns()"
/>
</x-filament-panels::page>
