<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Articles') }}
            </h2>
            <a href="{{ route('articles.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                {{ __('New Article') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="p-4 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('My Articles by Status') }}</h3>
                    @if ($statusCounts->sum() > 0)
                        <canvas id="myStatusChart" height="200"></canvas>
                    @else
                        <p class="text-sm text-gray-500">{{ __("You haven't written any articles yet.") }}</p>
                    @endif
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">{{ __('My Published Articles by Category') }}</h3>
                    @if ($categoryCounts->isNotEmpty())
                        <canvas id="myCategoryChart" height="200"></canvas>
                    @else
                        <p class="text-sm text-gray-500">{{ __('Nothing published yet.') }}</p>
                    @endif
                </div>
            </div>

            @forelse ($articlesByCategory as $categoryName => $articles)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-3 bg-gray-50 border-b font-semibold text-gray-700">
                        {{ $categoryName }}
                    </div>
                    <ul class="divide-y">
                        @foreach ($articles as $article)
                            <li class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('articles.edit', $article) }}" class="font-medium text-gray-900 hover:underline">
                                        {{ $article->title }}
                                    </a>
                                    <div class="text-sm text-gray-500">{{ $article->created_at->format('M j, Y') }}</div>
                                </div>
                                <span @class([
                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                    'bg-gray-200 text-gray-700' => $article->status === 'draft',
                                    'bg-yellow-100 text-yellow-800' => $article->status === 'pending',
                                    'bg-red-100 text-red-800' => $article->status === 'rejected',
                                    'bg-green-100 text-green-800' => $article->status === 'published',
                                ])>
                                    {{ ucfirst($article->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-500">
                    {{ __("You haven't written any articles yet.") }}
                </div>
            @endforelse
        </div>
    </div>

    @if ($statusCounts->sum() > 0 || $categoryCounts->isNotEmpty())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const mutedInk = '#898781';
            const gridline = '#e1e0d9';
            const primaryInk = '#0b0b0b';

            Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", sans-serif';
            Chart.defaults.color = mutedInk;

            @if ($statusCounts->sum() > 0)
                new Chart(document.getElementById('myStatusChart'), {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($statusLabels) !!},
                        datasets: [{
                            data: {!! json_encode($statusCounts) !!},
                            backgroundColor: ['#c3c2b7', '#fab219', '#d03b3b', '#0ca30c'],
                        }],
                    },
                    options: {
                        plugins: {
                            legend: { position: 'bottom', labels: { color: primaryInk } },
                        },
                    },
                });
            @endif

            @if ($categoryCounts->isNotEmpty())
                new Chart(document.getElementById('myCategoryChart'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($categoryLabels) !!},
                        datasets: [{
                            label: 'Published',
                            data: {!! json_encode($categoryCounts) !!},
                            backgroundColor: '#2a78d6',
                            borderRadius: 4,
                            maxBarThickness: 24,
                        }],
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: primaryInk } },
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: gridline } },
                        },
                    },
                });
            @endif
        </script>
    @endif
</x-app-layout>
