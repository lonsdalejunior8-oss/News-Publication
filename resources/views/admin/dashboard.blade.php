<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-stat-tile :label="__('Published')" :value="$stats['published']" dot="#0ca30c" />
            <x-stat-tile :label="__('Pending Approval')" :value="$stats['pending']" dot="#fab219" />
            <x-stat-tile :label="__('Authors')" :value="$stats['authors']" dot="#2a78d6" />
            <x-stat-tile :label="__('Active Categories')" :value="$stats['categories']" dot="#4a3aa7" />
        </div>

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">{{ __('Published Articles by Category, by Author') }}</h3>
                @if ($categoryLabels->isNotEmpty())
                    <canvas id="byCategoryAuthorChart" height="220"></canvas>
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

    @if ($categoryLabels->isNotEmpty() || $statusCounts->sum() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const mutedInk = '#898781';
            const gridline = '#e1e0d9';
            const primaryInk = '#0b0b0b';

            Chart.defaults.font.family = 'system-ui, -apple-system, "Segoe UI", sans-serif';
            Chart.defaults.color = mutedInk;

            @if ($categoryLabels->isNotEmpty())
                new Chart(document.getElementById('byCategoryAuthorChart'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($categoryLabels) !!},
                        datasets: {!! $authorSeries->map(fn ($s) => [
                            'label' => $s['label'],
                            'data' => $s['data'],
                            'backgroundColor' => $s['color'],
                            'borderColor' => '#ffffff',
                            'borderWidth' => 2,
                        ])->values()->toJson() !!},
                    },
                    options: {
                        plugins: {
                            legend: { position: 'bottom', labels: { color: primaryInk } },
                        },
                        scales: {
                            x: { stacked: true, grid: { display: false }, ticks: { color: primaryInk } },
                            y: { stacked: true, beginAtZero: true, ticks: { precision: 0 }, grid: { color: gridline } },
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
