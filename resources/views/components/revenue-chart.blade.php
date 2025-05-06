<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Biểu đồ doanh thu</h2>
        <div>
            <button id="toggleChartBtn" class="px-4 cursor-pointer py-1 bg-blue-500 text-white rounded">
                Xem theo năm
            </button>
        </div>
    </div>

    <canvas id="revenueChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const monthlyData = @json($salesDataByMonth);
    const yearlyData = @json($salesDataByYear);

    let isMonthly = true;

    const ctx = document.getElementById('revenueChart').getContext('2d');
    let chart = new window.Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ],
            datasets: [{
                label: 'Doanh thu theo tháng (VNĐ)',
                data: monthlyData,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + ' đ';
                        }
                    }
                }
            }
        }
    });

    document.getElementById('toggleChartBtn').addEventListener('click', () => {
        isMonthly = !isMonthly;

        if (isMonthly) {
            chart.data.labels = [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ];
            chart.data.datasets[0].label = 'Doanh thu theo tháng (VNĐ)';
            chart.data.datasets[0].data = monthlyData;
            document.getElementById('toggleChartBtn').innerText = 'Xem theo năm';
        } else {
            chart.data.labels = yearlyData.map(item => item.year.toString());
            chart.data.datasets[0].label = 'Doanh thu theo năm (VNĐ)';
            chart.data.datasets[0].data = yearlyData.map(item => item.total);
            document.getElementById('toggleChartBtn').innerText = 'Xem theo tháng';
        }

        chart.update();
    });
</script>
