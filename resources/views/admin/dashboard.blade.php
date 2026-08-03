<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Published Articles by Author') }}</h3>
                @if ($authorCounts->isNotEmpty())
                    <canvas id="byAuthorChart" height="220"></canvas>
                @else
                    <p class="text-sm text-gray-500">{{ __('No published articles yet.') }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('All Articles by Status') }}</h3>
                @if ($statusCounts->sum() > 0)
                    <canvas id="byStatusChart" height="220"></canvas>
                @else
                    <p class="text-sm text-gray-500">{{ __('No articles yet.') }}</p>
                @endif
            </div>
        </div>
    </div>

    @if ($authorCounts->isNotEmpty() || $statusCounts->sum() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const mutedInk = '#898781';
            const gridline = '#e1e0d9';
            const primaryInk = '#0b0b0b';

            Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", sans-serif';
            Chart.defaults.color = mutedInk;

            @if ($authorCounts->isNotEmpty())
                new Chart(document.getElementById('byAuthorChart'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($authorLabels) !!},
                        datasets: [{
                            label: 'Published',
                            data: {!! json_encode($authorCounts) !!},
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

            @if ($statusCounts->sum() > 0)
                new Chart(document.getElementById('byStatusChart'), {
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
        </script>
    @endif
</x-app-layout>
