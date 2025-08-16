@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Dashboard Admin</h1>
            <p class="text-gray-600 text-lg">Selamat datang, <span
                    class="font-medium text-blue-600">{{ Auth::user()->name }}</span>. Ini adalah dashboard Admin.</p>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('admin.index') }}"
                    class="bg-blue-100 text-blue-600 font-semibold text-center py-4 rounded-lg hover:bg-blue-200 transition">
                    <i class="fas fa-users mr-2"></i> Kelola User
                </a>
                <a href="#"
                    class="bg-green-100 text-green-600 font-semibold text-center py-4 rounded-lg hover:bg-green-200 transition">
                    <i class="fas fa-chart-bar mr-2"></i> Laporan
                </a>
            </div>

            <!-- Grafik -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Statistik Data</h2>
                <canvas id="dashboardChart" width="400" height="200"></canvas>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx = document.getElementById('dashboardChart').getContext('2d');
                    const sparepartCount = {{ $sparepartCount ?? 0 }};
                    const purchaseRequestCount = {{ $purchaseRequestCount ?? 0 }};
                    const pengambilanCount = {{ $pengambilanCount ?? 0 }};
                    const dashboardChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Sparepart', 'Pengajuan Sparepart', 'Pengambilan Sparepart'],
                            datasets: [{
                                label: 'Jumlah',
                                data: [sparepartCount, purchaseRequestCount, pengambilanCount],
                                backgroundColor: [
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(54, 162, 235, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(54, 162, 235, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </div>
        </section>
    </main>
@endsection
