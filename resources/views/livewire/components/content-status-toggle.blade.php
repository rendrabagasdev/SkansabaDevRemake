<div class="relative inline-block">
    {{-- Current Status Badge --}}
    <button 
        wire:click="toggleTransitions"
        type="button"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-colors
               @if($model->status === 'draft') bg-gray-100 text-gray-800 hover:bg-gray-200
               @elseif($model->status === 'review') bg-yellow-100 text-yellow-800 hover:bg-yellow-200
               @elseif($model->status === 'published') bg-green-100 text-green-800 hover:bg-green-200
               @elseif($model->status === 'archived') bg-red-100 text-red-800 hover:bg-red-200
               @endif"
    >
        <span>{{ $model->status_label }}</span>
        <svg class="w-4 h-4 transition-transform {{ $showTransitions ? 'rotate-180' : '' }}" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Transition Menu --}}
    @if($showTransitions && count($availableTransitions) > 0)
        <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
            <div class="p-2 space-y-1">
                @foreach($availableTransitions as $transition)
                    <button
                        wire:click="initiateTransition('{{ $transition['status'] }}', '{{ $transition['action'] }}')"
                        type="button"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-md transition-colors
                               hover:bg-{{ $transition['color'] }}-50 text-{{ $transition['color'] }}-700
                               hover:text-{{ $transition['color'] }}-900"
                    >
                        <span>{{ $transition['icon'] }}</span>
                        <span class="flex-1 text-left">{{ $transition['label'] }}</span>
                        @if($transition['requires_admin'])
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Confirmation Modal --}}
    @if($confirmingTransition)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Confirm Status Change
                </h3>
                <p class="text-gray-600 mb-4">
                    Are you sure you want to change the status from 
                    <span class="font-semibold">{{ $model->status_label }}</span> to 
                    <span class="font-semibold">{{ ucfirst($pendingStatus) }}</span>?
                </p>
                <div class="flex gap-3 justify-end">
                    <button
                        wire:click="cancelTransition"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="confirmTransition"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Flash Messages --}}
    @if(session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 5000)">
            <p>{{ session('error') }}</p>
        </div>
    @endif
</div>
