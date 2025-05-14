<div class="bg-white rounded-lg">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Biểu đồ số lượng đơn hàng</h2>
        <button id="toggleChartBtn2" class="px-4 py-1 bg-blue-500 text-white rounded cursor-pointer">
            Xem theo năm
        </button>
    </div>

    <div class="min-h-[500px] w-full">
        <canvas id="orderChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const monthlyOrders = @json($orderByMonth);
    const yearlyOrders = @json($orderByYear);
    const currentYear = new Date().getFullYear();

    let isMonthlyOrder = true;

    const ctx2 = document.getElementById('orderChart').getContext('2d');
    let chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ],
            datasets: [{
                label: 'Số đơn hàng theo tháng',
                data: monthlyOrders,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: 'rgba(59, 130, 246, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    document.getElementById('toggleChartBtn2').addEventListener('click', () => {
        isMonthlyOrder = !isMonthlyOrder;

        if (isMonthlyOrder) {
            chart.data.labels = [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ];
            chart2.data.datasets[0].label = 'Số đơn hàng theo tháng';
            chart2.data.datasets[0].data = monthlyOrders;
            document.getElementById('toggleChartBtn2').innerText = 'Xem theo năm';
        } else {
            const startYear = currentYear - yearlyOrders.length + 1;
            const yearLabels = Array.from({
                length: yearlyOrders.length
            }, (_, i) => (startYear + i).toString());

            chart2.data.labels = yearLabels;
            chart2.data.datasets[0].label = 'Số đơn hàng theo năm';
            chart2.data.datasets[0].data = yearlyOrders;
            document.getElementById('toggleChartBtn2').innerText = 'Xem theo tháng';
        }

        chart2.update();
    });
</script>
