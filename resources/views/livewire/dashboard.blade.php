<div class="p-6">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-600 mt-1">Selamat datang di CMS Jurusan RPL</p>
    </div>

    {{-- Statistics Widgets --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        {{-- Total Karya Siswa --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-[#12B4E0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Karya Siswa</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $widgets['total_karya_siswa'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Prestasi --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Prestasi</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $widgets['total_prestasi'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Alumni --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Alumni</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $widgets['total_alumni'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Galeri --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Galeri</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $widgets['total_galeri'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konten Draft --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Konten Draft</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $widgets['konten_draft'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konten Review --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-orange-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Konten Review</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $widgets['konten_review'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                </div>
                <div class="p-5">
                    @if($recentActivity->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Belum ada aktivitas</p>
                        </div>
                    @else
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($recentActivity as $index => $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex items-start space-x-3">
                                                <div>
                                                    <div class="relative px-1">
                                                        <div class="h-8 w-8 bg-[#12B4E0] rounded-full ring-8 ring-white flex items-center justify-center">
                                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div>
                                                        <div class="text-sm">
                                                            <span class="font-medium text-gray-900">{{ $activity['user_name'] }}</span>
                                                            <span class="text-gray-500"> {{ $activity['action'] }} </span>
                                                            <span class="font-medium text-gray-900">{{ $activity['model'] }}</span>
                                                        </div>
                                                        <p class="mt-0.5 text-sm text-gray-500">{{ $activity['title'] }}</p>
                                                        <p class="mt-0.5 text-xs text-gray-400">{{ $activity['time'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Content Status Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Status Konten</h3>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        @foreach($statusSummary as $model => $counts)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">{{ $model }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="px-3 py-2 bg-gray-50 rounded-lg">
                                        <div class="text-xs text-gray-500">Draft</div>
                                        <div class="text-lg font-semibold text-gray-900">{{ $counts['draft'] }}</div>
                                    </div>
                                    <div class="px-3 py-2 bg-orange-50 rounded-lg">
                                        <div class="text-xs text-orange-600">Review</div>
                                        <div class="text-lg font-semibold text-orange-900">{{ $counts['review'] }}</div>
                                    </div>
                                    <div class="px-3 py-2 bg-green-50 rounded-lg">
                                        <div class="text-xs text-green-600">Published</div>
                                        <div class="text-lg font-semibold text-green-900">{{ $counts['published'] }}</div>
                                    </div>
                                    <div class="px-3 py-2 bg-red-50 rounded-lg">
                                        <div class="text-xs text-red-600">Archived</div>
                                        <div class="text-lg font-semibold text-red-900">{{ $counts['archived'] }}</div>
                                    </div>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="border-t border-gray-200"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
