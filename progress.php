<?php $pageTitle='Progress | GYMBRUT'; $activePage='progress'; $topbarTitle='Progress Analytics'; include 'includes/layout_top.php'; ?>
<div class="row g-4">
  <div class="col-xl-6"><div class="chart-box premium-card"><div class="section-title mb-3">Berat Badan</div><canvas id="weightChart"></canvas></div></div>
  <div class="col-xl-6"><div class="chart-box premium-card"><div class="section-title mb-3">Kehadiran Gym</div><canvas id="attendanceChart"></canvas></div></div>
  <div class="col-xl-6"><div class="chart-box premium-card"><div class="section-title mb-3">Massa Otot</div><canvas id="muscleChart"></canvas></div></div>
  <div class="col-xl-6"><div class="premium-card h-100"><div class="section-title mb-3">Progress Mingguan</div><ul class="metric-list list-unstyled mb-0"><li><span>Workout completed</span><strong>4 / 5</strong></li><li><span>Berat turun</span><strong>-1.2 kg</strong></li><li><span>Massa otot naik</span><strong>+0.6 kg</strong></li><li><span>Kehadiran</span><strong>83%</strong></li></ul></div></div>
</div>
<script>
const copt={responsive:true,plugins:{legend:{labels:{color:'#fff'}}},scales:{x:{ticks:{color:'#ddd'},grid:{color:'rgba(255,255,255,.08)'}},y:{ticks:{color:'#ddd'},grid:{color:'rgba(255,255,255,.08)'}}}};
new Chart(document.getElementById('weightChart'),{type:'line',data:{labels:['W1','W2','W3','W4'],datasets:[{label:'Kg',data:[78,77.6,76.9,76.2],borderColor:'#ff7a00',backgroundColor:'rgba(255,122,0,.18)',fill:true,tension:.4}]},options:copt});
new Chart(document.getElementById('attendanceChart'),{type:'bar',data:{labels:['W1','W2','W3','W4'],datasets:[{label:'Visits',data:[3,4,5,4],backgroundColor:'rgba(255,122,0,.8)',borderRadius:12}]},options:copt});
new Chart(document.getElementById('muscleChart'),{type:'line',data:{labels:['W1','W2','W3','W4'],datasets:[{label:'Muscle Mass',data:[31.2,31.4,31.7,31.8],borderColor:'#ffffff',backgroundColor:'rgba(255,255,255,.18)',fill:true,tension:.4}]},options:copt});
</script>
<?php include 'includes/layout_bottom.php'; ?>
