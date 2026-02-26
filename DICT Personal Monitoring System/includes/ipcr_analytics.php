<?php
// Get current semester (1st or 2nd)
$current_month = (int)date('n');
$current_semester = ($current_month >= 1 && $current_month <= 6) ? '1st' : '2nd';
$current_year = (int)date('Y');

// Handle filter form submission
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;
$selected_semester = $_GET['semester'] ?? $current_semester;

// Get list of available years (from 5 years ago to 2040)
$years = [];
$current_year = (int)date('Y');
$end_year = max($current_year + 1, 2030); // Go up to 2040 or current year + 1, whichever is larger
for ($i = $current_year - 5; $i <= $end_year; $i++) {
    $years[] = $i;
}

// Get the previous semester for comparison
$prev_semester_year = $selected_year;
$prev_semester = ($current_semester === '1st') ? '2nd' : '1st';
if ($current_semester === '1st') {
    $prev_semester_year--;
}

// If it's January-June, we might want to show previous year's 2nd semester
if ($current_month <= 6) {
    $prev_semester = '2nd';
    $prev_semester_year = $current_year - 1;
} else {
    $prev_semester = '1st';
    $prev_semester_year = $current_year;
}

// Function to calculate IPCR statistics
function getIpcrStats($pdo, $year, $semester) {
    $stats = [
        'total_indicators' => 0,
        'total_accomplished' => 0,
        'total_quantity' => 0,
        'total_actual_quantity' => 0,
        'by_function' => []
    ];

    try {
        // Get all IPCR entries for the specified semester and year
        $stmt = $pdo->prepare("
            SELECT function_type, 
                   SUM(success_indicators_quantity) as total_quantity,
                   SUM(actual_accomplishments_quantity) as total_actual_quantity,
                   COUNT(*) as count_indicators,
                   SUM(CASE WHEN actual_accomplishments_quantity >= success_indicators_quantity THEN 1 ELSE 0 END) as accomplished_indicators
            FROM ipcr_entries 
            WHERE year = ? AND semester = ?
            GROUP BY function_type
        ");
        
        $stmt->execute([$year, $semester]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            $function_type = $row['function_type'];
            $stats['by_function'][$function_type] = [
                'total_indicators' => (int)$row['count_indicators'],
                'accomplished_indicators' => (int)$row['accomplished_indicators'],
                'total_quantity' => (float)$row['total_quantity'],
                'total_actual_quantity' => (float)$row['total_actual_quantity'],
                'completion_rate' => $row['count_indicators'] > 0 ? 
                    round(($row['accomplished_indicators'] / $row['count_indicators']) * 100, 1) : 0
            ];
            
            $stats['total_indicators'] += $row['count_indicators'];
            $stats['total_accomplished'] += $row['accomplished_indicators'];
            $stats['total_quantity'] += $row['total_quantity'];
            $stats['total_actual_quantity'] += $row['total_actual_quantity'];
        }
        
        // Calculate overall completion rate
        $stats['overall_completion_rate'] = $stats['total_indicators'] > 0 ? 
            round(($stats['total_accomplished'] / $stats['total_indicators']) * 100, 1) : 0;
            
        // Calculate quantity completion percentage
        $stats['quantity_completion_rate'] = $stats['total_quantity'] > 0 ? 
            min(100, round(($stats['total_actual_quantity'] / $stats['total_quantity']) * 100, 1)) : 0;
            
    } catch (PDOException $e) {
        error_log("Error getting IPCR stats: " . $e->getMessage());
    }
    
    return $stats;
}

// Get stats for current and previous semesters
$current_semester_stats = getIpcrStats($pdo, $selected_year, $selected_semester);
$prev_semester_stats = getIpcrStats($pdo, $prev_semester_year, $prev_semester);

// Calculate trend
$trend = [
    'direction' => $current_semester_stats['overall_completion_rate'] >= ($prev_semester_stats['overall_completion_rate'] ?? 0) ? 'up' : 'down',
    'percentage' => $prev_semester_stats['overall_completion_rate'] > 0 ? 
        abs(round((($current_semester_stats['overall_completion_rate'] - $prev_semester_stats['overall_completion_rate']) / $prev_semester_stats['overall_completion_rate']) * 100, 1)) : 0
];
?>

<style>
    .filter-button {
        height: 48px;
        min-width: 100px;
        max-width: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: none;
    }
</style>

<!-- IPCR Analytics Section -->
<div class="row g-2 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-white mb-0">Year</label>
                        <select name="year" id="year" class="form-select form-select-sm">
                            <?php foreach ($years as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo $selected_year == $year ? 'selected' : ''; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-white mb-0">Semester</label>
                        <select name="semester" id="semester" class="form-select form-select-sm">
                            <option value="1st" <?php echo $selected_semester === '1st' ? 'selected' : ''; ?>>1st Semester (Jan-Jun)</option>
                            <option value="2nd" <?php echo $selected_semester === '2nd' ? 'selected' : ''; ?>>2nd Semester (Jul-Dec)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-custom filter-button">
                                <i class="bi bi-funnel me-1"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 small text-uppercase fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-graph-up me-1"></i>IPCR Analytics - 
                        <?php echo $selected_semester . ' Semester ' . $selected_year; ?>
                    </h5>
                    <div class="text-muted small" style="font-size: 0.7rem;">Updated just now</div>
                </div>
                <!-- Charts Row -->
                <div class="row g-3">
                    <!-- Completion by Function Type -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-black">Completion by Function Type</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:300px;">
                                    <canvas id="functionTypeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Semester Comparison -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-black">Semester Comparison</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:300px;">
                                    <canvas id="semesterComparisonChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 2nd Semester IPCR Indicators -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 text-uppercase fw-bold" style="font-size: 0.8rem; color: #ffffff;">
                        <?php echo $selected_semester . ' Semester ' . $selected_year . ' IPCR Indicators'; ?>
                    </h6>
                        <div class="text-white small" style="font-size: 0.7rem;">Individual Performance</div>
                    </div>
                    <div class="row g-3">
                
                <?php
                // Get indicators based on selected filters
                $sql = "
                    SELECT 
                        id,
                        success_indicators,
                        success_indicators_quantity,
                        actual_accomplishments_quantity,
                        actual_accomplishments,
                        function_type,
                        semester,
                        year
                    FROM ipcr_entries 
                    WHERE user_id = ? 
                    AND year = ?
                ";
                
                $params = [$_SESSION['user_id'], $selected_year];
                
                // Always filter by selected semester
                $sql .= " AND semester = ?";
                $params[] = $selected_semester;
                
                $sql .= " ORDER BY 
                    CASE function_type 
                        WHEN 'Core Function' THEN 1 
                        WHEN 'Support Function' THEN 2 
                        ELSE 3 
                    END,
                    id ASC
                ";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($indicators)) {
                    $colorPalette = [
                        'rgba(54, 162, 235, 0.8)',  // Blue
                        'rgba(75, 192, 192, 0.8)',  // Teal
                        'rgba(255, 206, 86, 0.8)',  // Yellow
                        'rgba(153, 102, 255, 0.8)', // Purple
                        'rgba(255, 159, 64, 0.8)',  // Orange
                        'rgba(255, 99, 132, 0.8)'   // Red
                    ];
                    
                    $functionTypeColors = [
                        'Teaching' => 'rgba(54, 162, 235, 0.8)',
                        'Research' => 'rgba(75, 192, 192, 0.8)',
                        'Extension' => 'rgba(255, 206, 86, 0.8)',
                        'Production' => 'rgba(153, 102, 255, 0.8)',
                        'Administration' => 'rgba(255, 159, 64, 0.8)',
                        'Other' => 'rgba(255, 99, 132, 0.8)'
                    ];
                    
                    foreach ($indicators as $index => $indicator) {
                        $indicatorName = $indicator['success_indicators'];
                        $shortName = strlen($indicatorName) > 50 ? substr($indicatorName, 0, 50) . '...' : $indicatorName;
                        $fullName = htmlspecialchars($indicatorName);
                        $target = (int)$indicator['success_indicators_quantity'];
                        $actual = (int)$indicator['actual_accomplishments_quantity'];
                        $completion = $target > 0 ? min(100, round(($actual / $target) * 100)) : 0;
                        
                        // Determine status and color
                        if ($completion >= 100) {
                            $statusText = 'Completed';
                            $statusIcon = 'bi-check-circle-fill';
                            $color = 'rgba(40, 167, 69, 0.8)';
                        } elseif ($completion >= 75) {
                            $statusText = 'On Track';
                            $statusIcon = 'bi-check-circle';
                            $color = 'rgba(23, 162, 184, 0.8)';
                        } elseif ($completion >= 50) {
                            $statusText = 'In Progress';
                            $statusIcon = 'bi-arrow-clockwise';
                            $color = 'rgba(255, 193, 7, 0.8)';
                        } else {
                            $statusText = 'Needs Attention';
                            $statusIcon = 'bi-exclamation-triangle-fill';
                            $color = 'rgba(220, 53, 69, 0.8)';
                        }
                        
                        // Get function type color or use default
                        $functionType = $indicator['function_type'] ?? 'Other';
                        $functionColor = $functionTypeColors[$functionType] ?? 'rgba(108, 117, 125, 0.8)';
                        $functionBgColor = str_replace('0.8', '0.1', $functionColor);
                        
                        // Create a unique ID for each chart
                        $chartId = 'indicatorChart' . $index;
                        ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <!-- Function Type Badge -->
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge" style="background-color: <?php echo $functionBgColor; ?>; color: #ffffff; font-size: 0.65rem; font-weight: 500; padding: 0.35em 0.65em;">
                                            <?php echo htmlspecialchars($functionType); ?>
                                        </span>
                                        <span class="badge bg-opacity-10 p-1 small" style="background-color: <?php echo str_replace('0.8', '0.1', $color); ?>; color: <?php echo str_replace('0.8', '1', $color); ?>">
                                            <i class="bi <?php echo $statusIcon; ?> me-1"></i>
                                            <?php echo $statusText; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Indicator Title -->
                                    <h6 class="card-title mb-2 small fw-bold" style="min-height: 2.5rem;" title="<?php echo $fullName; ?>">
                                        <?php echo htmlspecialchars($shortName); ?>
                                    </h6>
                                    
                                    <!-- Progress Bar -->
                                    <div class="progress mb-3" style="height: 6px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $completion; ?>%; background-color: <?php echo $color; ?>;" 
                                             aria-valuenow="<?php echo $completion; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <!-- Mini Chart -->
                                    <div class="chart-container mt-2" style="position: relative; height: 300px;">
                                        <canvas id="<?php echo $chartId; ?>"></canvas>
                                    </div>
                                    
                                    <!-- Stats -->
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="small text-muted">
                                            <i class="bi bi-flag-fill me-1 text-white"></i> <span class="text-white">Target: <?php echo $target; ?></span>
                                        </div>
                                        <div class="small fw-medium">
                                            <i class="bi bi-check-circle-fill me-1 text-success"></i> <?php echo $actual; ?> achieved
                                        </div>
                                        <div class="small fw-bold" style="color: <?php echo str_replace('0.8', '1', $color); ?>">
                                            <?php echo $completion; ?>%
                                        </div>
                                    </div>
                                    
                                    <!-- Accomplishment Notes (Truncated) -->
                                    <?php if (!empty($indicator['actual_accomplishments'])): ?>
                                    <div class="small text-muted mt-2" style="font-size: 0.7rem;" title="<?php echo htmlspecialchars($indicator['actual_accomplishments']); ?>">
                                        <?php 
                                        $notes = strlen($indicator['actual_accomplishments']) > 60 
                                            ? substr($indicator['actual_accomplishments'], 0, 60) . '...' 
                                            : $indicator['actual_accomplishments'];
                                        echo htmlspecialchars($notes);
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Chart Script -->
                                <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctx = document.getElementById('<?php echo $chartId; ?>').getContext('2d');
                                    new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Target', 'Actual'],
                                            datasets: [{
                                                data: [<?php echo $target; ?>, <?php echo $actual; ?>],
                                                backgroundColor: [
                                                    'rgba(201, 203, 207, 0.5)',
                                                    '<?php echo $color; ?>'
                                                ],
                                                borderColor: [
                                                    'rgba(201, 203, 207, 1)',
                                                    '<?php echo str_replace('0.8', '1', $color); ?>'
                                                ],
                                                borderWidth: 1,
                                                borderRadius: 4,
                                                barPercentage: 0.7,
                                                categoryPercentage: 0.8
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            indexAxis: 'x',
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    grid: {
                                                        color: 'rgba(255, 255, 255, 0.1)'
                                                    },
                                                    ticks: {
                                                        color: '#ffffff',
                                                        font: {
                                                            size: 10
                                                        }
                                                    },
                                                    title: {
                                                        display: true,
                                                        text: 'Quantity',
                                                        color: '#ffffff',
                                                        font: {
                                                            size: 10,
                                                            weight: 'bold'
                                                        }
                                                    }
                                                },
                                                x: {
                                                    grid: { display: false },
                                                    ticks: { 
                                                        font: { 
                                                            size: 10,
                                                            color: '#ffffff'
                                                        },
                                                        color: '#ffffff'
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: { 
                                                    display: false
                                                },
                                                tooltip: {
                                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                    titleFont: { 
                                                        size: 11,
                                                        weight: 'bold',
                                                        color: '#ffffff'
                                                    },
                                                    bodyFont: { 
                                                        size: 11,
                                                        color: '#ffffff'
                                                    },
                                                    callbacks: {
                                                        label: function(context) {
                                                            let label = context.dataset.label || '';
                                                            if (label) {
                                                                label += ': ';
                                                            }
                                                            if (context.parsed.y !== null) {
                                                                label += context.parsed.y;
                                                            }
                                                            return label;
                                                        }
                                                    }
                                                }
                                            },
                                            animation: {
                                                duration: 1000,
                                                easing: 'easeInOutQuart'
                                            }
                                        }
                                    });
                                });
                                </script>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    $period = $selected_semester . ' semester ' . $selected_year;
                    echo '<div class="col-12"><div class="alert alert-info mb-0">No IPCR indicators found for ' . $period . '.</div></div>';
                }
                ?>
                    </div><!-- End of row -->
                </div><!-- End of container -->
                
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Wait for document to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Function Type Chart
    const functionTypeCtx = document.getElementById('functionTypeChart').getContext('2d');
    
    // Prepare data for the chart
    const functionLabels = <?php 
        echo json_encode(!empty($current_semester_stats['by_function']) ? array_keys($current_semester_stats['by_function']) : []); 
    ?>;
    
    const functionData = <?php 
        echo json_encode(!empty($current_semester_stats['by_function']) ? 
            array_column($current_semester_stats['by_function'], 'completion_rate') : []); 
    ?>;
    
    // Generate colors for each bar
    const backgroundColors = [];
    const borderColors = [];
    const colorPalette = [
        'rgba(54, 162, 235, 0.8)',  // Blue
        'rgba(255, 99, 132, 0.8)',  // Red
        'rgba(75, 192, 192, 0.8)',  // Teal
        'rgba(255, 206, 86, 0.8)',  // Yellow
        'rgba(153, 102, 255, 0.8)', // Purple
        'rgba(255, 159, 64, 0.8)'   // Orange
    ];
    
    for (let i = 0; i < functionData.length; i++) {
        const colorIndex = i % colorPalette.length;
        backgroundColors.push(colorPalette[colorIndex]);
        borderColors.push(colorPalette[colorIndex].replace('0.8', '1'));
    }
    
    const functionTypeChart = new Chart(functionTypeCtx, {
        type: 'bar',
        data: {
            labels: functionLabels,
            datasets: [{
                label: 'Completion Rate (%)',
                data: functionData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.7,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        }
                    },
                    title: {
                        display: true,
                        text: 'Completion Rate (%)',
                        color: '#ffffff',
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 12,
                        weight: 'bold',
                        color: '#ffffff'
                    },
                    bodyFont: {
                        size: 12,
                        color: '#ffffff'
                    },
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '%';
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Semester Comparison Chart
    const semesterCtx = document.getElementById('semesterComparisonChart').getContext('2d');
    
    // Prepare data for the chart
    const semesterLabels = [
        '<?php echo $prev_semester; ?> Sem <?php echo $prev_semester_year; ?>', 
        '<?php echo $current_semester; ?> Sem <?php echo $current_year; ?>'
    ];
    
    const completionData = [
        <?php echo $prev_semester_stats['overall_completion_rate'] ?? 0; ?>, 
        <?php echo $current_semester_stats['overall_completion_rate'] ?? 0; ?>
    ];
    
    const quantityData = [
        <?php echo $prev_semester_stats['quantity_completion_rate'] ?? 0; ?>, 
        <?php echo $current_semester_stats['quantity_completion_rate'] ?? 0; ?>
    ];
    
    const semesterChart = new Chart(semesterCtx, {
        type: 'bar',
        data: {
            labels: semesterLabels,
            datasets: [
                {
                    label: 'Completion Rate (%)',
                    data: completionData,
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(153, 102, 255, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Quantity Completion (%)',
                    data: quantityData,
                    type: 'line',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(255, 99, 132, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        }
                    },
                    title: {
                        display: true,
                        text: 'Percentage (%)',
                        color: '#ffffff',
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff',
                        font: {
                            size: 11
                        },
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 12,
                        weight: 'bold',
                        color: '#ffffff'
                    },
                    bodyFont: {
                        size: 12,
                        color: '#ffffff'
                    },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        functionTypeChart.resize();
        semesterChart.resize();
    });
});
</script>
