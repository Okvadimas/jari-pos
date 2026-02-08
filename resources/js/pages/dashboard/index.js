/**
 * Dashboard Page JavaScript
 */

console.log('Dashboard Page JavaScript');

$(document).ready(function() {
    initSalesChart();
});

function initSalesChart() {
    var canvas = document.getElementById('salesOverviewChart');
    if (!canvas) return;

    if (typeof Chart === 'undefined') {
        console.error('Chart.js library is not loaded.');
        return;
    }

    if (!window.dashboardData || !window.dashboardData.salesChart) {
        console.error('Dashboard data is missing.');
        return;
    }

    var ctx = canvas.getContext('2d');
    var data = window.dashboardData.salesChart;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Total Pendapatan (Rp)',
                data: data.values,
                backgroundColor: 'rgba(94, 114, 228, 0.6)',
                borderColor: 'rgba(94, 114, 228, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4] },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumSignificantDigits: 1 }).format(value);
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}
