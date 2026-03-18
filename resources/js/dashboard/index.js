import Chart from 'chart.js/auto';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const dataElement = document.getElementById('dashboard-data');
let dashboardData = {};

if (dataElement) {
    try {
        dashboardData = JSON.parse(dataElement.textContent || '{}');
    } catch (error) {
        dashboardData = {};
    }
}

const cssVariables = getComputedStyle(document.documentElement);
const chartColors = {
    primary: cssVariables.getPropertyValue('--bs-primary').trim() || '#0d6efd',
    success: cssVariables.getPropertyValue('--bs-success').trim() || '#198754',
    warning: cssVariables.getPropertyValue('--bs-warning').trim() || '#ffc107',
    danger: cssVariables.getPropertyValue('--bs-danger').trim() || '#dc3545',
    secondary: cssVariables.getPropertyValue('--bs-secondary').trim() || '#6c757d',
    bodyColor: cssVariables.getPropertyValue('--bs-body-color').trim() || '#212529',
};

const baseChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: chartColors.bodyColor,
            },
        },
    },
    scales: {
        x: {
            ticks: {
                color: chartColors.bodyColor,
            },
            grid: {
                color: 'rgba(0,0,0,.05)',
            },
        },
        y: {
            beginAtZero: true,
            ticks: {
                color: chartColors.bodyColor,
                precision: 0,
            },
            grid: {
                color: 'rgba(0,0,0,.05)',
            },
        },
    },
};

function initAdminCharts() {
    const rangeDatasets = dashboardData?.charts?.admin_ranges || {};
    const defaultRange = rangeDatasets['30'] ? '30' : (rangeDatasets['7'] ? '7' : '90');

    const fallbackData = {
        labels: dashboardData?.charts?.labels || [],
        users_per_day: dashboardData?.charts?.users_per_month || [],
        simulations_per_day: dashboardData?.charts?.simulations_per_month || [],
        blog_status: dashboardData?.charts?.blog_status || {},
    };

    const currentRangeData = rangeDatasets[defaultRange] || fallbackData;
    const labels = currentRangeData.labels || [];

    const growthCanvas = document.getElementById('adminGrowthChart');
    let growthChart = null;
    if (growthCanvas) {
        growthChart = new Chart(growthCanvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Nouveaux utilisateurs',
                        data: currentRangeData.users_per_day || [],
                        borderColor: chartColors.primary,
                        backgroundColor: `${chartColors.primary}33`,
                        fill: true,
                        tension: 0.35,
                    },
                    {
                        label: 'Simulations',
                        data: currentRangeData.simulations_per_day || [],
                        borderColor: chartColors.success,
                        backgroundColor: `${chartColors.success}22`,
                        fill: true,
                        tension: 0.35,
                    },
                ],
            },
            options: baseChartOptions,
        });
    }

    const statusCanvas = document.getElementById('adminBlogStatusChart');
    let statusChart = null;
    if (statusCanvas) {
        const statusData = currentRangeData.blog_status || {};

        statusChart = new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Brouillon', 'En attente', 'Publié', 'Rejeté'],
                datasets: [
                    {
                        data: [
                            statusData.draft || 0,
                            statusData.pending || 0,
                            statusData.published || 0,
                            statusData.rejected || 0,
                        ],
                        backgroundColor: [
                            chartColors.secondary,
                            chartColors.warning,
                            chartColors.success,
                            chartColors.danger,
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    }

    const rangeButtons = document.querySelectorAll('.js-admin-range-btn');

    const applyRange = (range) => {
        const selectedData = rangeDatasets[range] || fallbackData;

        if (growthChart) {
            growthChart.data.labels = selectedData.labels || [];
            growthChart.data.datasets[0].data = selectedData.users_per_day || [];
            growthChart.data.datasets[1].data = selectedData.simulations_per_day || [];
            growthChart.update();
        }

        if (statusChart) {
            const statusData = selectedData.blog_status || {};
            statusChart.data.datasets[0].data = [
                statusData.draft || 0,
                statusData.pending || 0,
                statusData.published || 0,
                statusData.rejected || 0,
            ];
            statusChart.update();
        }

        rangeButtons.forEach((button) => {
            const isActive = button.dataset.range === range;
            button.classList.toggle('active', isActive);
        });
    };

    rangeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const range = button.dataset.range;
            if (!range) {
                return;
            }

            applyRange(range);
        });
    });

    applyRange(defaultRange);

    const adminSimulationValueCanvas = document.getElementById('adminSimulationValueChart');
    if (adminSimulationValueCanvas) {
        new Chart(adminSimulationValueCanvas, {
            type: 'bar',
            data: {
                labels: dashboardData?.charts?.labels || [],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Aides simulées (€)',
                        data: dashboardData?.charts?.simulation_aid_per_month || [],
                        backgroundColor: `${chartColors.warning}cc`,
                        borderRadius: 8,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Gain énergétique moyen',
                        data: dashboardData?.charts?.simulation_avg_gain_per_month || [],
                        borderColor: chartColors.danger,
                        backgroundColor: `${chartColors.danger}22`,
                        tension: 0.35,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        labels: {
                            color: chartColors.bodyColor,
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: chartColors.bodyColor,
                        },
                        grid: {
                            color: 'rgba(0,0,0,.05)',
                        },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: chartColors.bodyColor,
                        },
                        grid: {
                            color: 'rgba(0,0,0,.05)',
                        },
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        ticks: {
                            color: chartColors.bodyColor,
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                },
            },
        });
    }
}

function initUserCharts() {
    const labels = dashboardData?.charts?.labels || [];

    const simulationsCanvas = document.getElementById('userSimulationChart');
    if (simulationsCanvas) {
        new Chart(simulationsCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Mes simulations',
                        data: dashboardData?.charts?.my_simulations_per_month || [],
                        backgroundColor: `${chartColors.primary}cc`,
                        borderRadius: 8,
                    },
                ],
            },
            options: baseChartOptions,
        });
    }

    const blogStatusCanvas = document.getElementById('userBlogStatusChart');
    if (blogStatusCanvas) {
        const statusData = dashboardData?.charts?.my_blog_status || {};

        new Chart(blogStatusCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Brouillon', 'En attente', 'Publié', 'Rejeté'],
                datasets: [
                    {
                        data: [
                            statusData.draft || 0,
                            statusData.pending || 0,
                            statusData.published || 0,
                            statusData.rejected || 0,
                        ],
                        backgroundColor: [
                            chartColors.secondary,
                            chartColors.warning,
                            chartColors.success,
                            chartColors.danger,
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    }

    const userSimulationValueCanvas = document.getElementById('userSimulationValueChart');
    if (userSimulationValueCanvas) {
        new Chart(userSimulationValueCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Mes aides simulées (€)',
                        data: dashboardData?.charts?.my_simulation_aid_per_month || [],
                        backgroundColor: `${chartColors.success}cc`,
                        borderRadius: 8,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Mon gain énergétique moyen',
                        data: dashboardData?.charts?.my_simulation_avg_gain_per_month || [],
                        borderColor: chartColors.primary,
                        backgroundColor: `${chartColors.primary}22`,
                        tension: 0.35,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        labels: {
                            color: chartColors.bodyColor,
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: chartColors.bodyColor,
                        },
                        grid: {
                            color: 'rgba(0,0,0,.05)',
                        },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: chartColors.bodyColor,
                        },
                        grid: {
                            color: 'rgba(0,0,0,.05)',
                        },
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        ticks: {
                            color: chartColors.bodyColor,
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                },
            },
        });
    }
}

function initAdminMap() {
    const mapElement = document.getElementById('adminUsersMap');
    if (!mapElement) {
        return;
    }

    const cities = dashboardData?.map?.cities || [];
    if (!cities.length) {
        mapElement.innerHTML = '<div class="alert alert-light border text-muted m-3">Aucune donnée géolocalisée disponible.</div>';
        return;
    }

    const map = L.map(mapElement, {
        scrollWheelZoom: false,
    }).setView([46.603354, 1.888334], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    const bounds = [];

    cities.forEach((city) => {
        const lat = Number(city.lat);
        const lng = Number(city.lng);
        const usersCount = Number(city.users_count || 0);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        const radius = Math.max(6, Math.min(24, usersCount * 1.4));
        const marker = L.circleMarker([lat, lng], {
            radius,
            color: chartColors.primary,
            fillColor: chartColors.primary,
            fillOpacity: 0.35,
            weight: 1.5,
        }).addTo(map);

        marker.bindPopup(`<strong>${city.city}</strong><br>${usersCount} utilisateur(s)`);
        bounds.push([lat, lng]);
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [20, 20] });
    }
}

if (dataElement) {
    if (dashboardData.isAdmin) {
        initAdminCharts();
        initAdminMap();
    } else {
        initUserCharts();
    }
}
