<?php
require_once 'auth_check.php';
requireRole(['admin']);
?>

<?php $pageTitle = 'Progress | GYMBRUT';
$activePage = 'progress';
$topbarTitle = 'Progress Analytics';
include 'includes/layout_top.php'; ?>
<div class="row g-4">

    
      
    
    <div class="col-xl-6">
    <div class="chart-box premium-card">
      <div class="section-title mb-3">Berat Badan</div><canvas id="weightChart"></canvas></div>
    </div>
  
  <div class="col-xl-6">
    <div class="chart-box premium-card">
      <div class="section-title mb-3">Kehadiran Gym</div><canvas id="attendanceChart"></
    canvas
  ></div></div>
  <div class="col-xl-6">
    <div class="chart-box premium-ca
      rd"><div class="section-title mb-3">Massa Otot</div><ca
      nvas id="muscleChart"></canvas></div></div>
        

        
        
        
      
    
    <div class="col-xl-6"><div class="premium-card h-100"><div class="section-title mb-3">Progress Mingguan</div><ul class="metric-list list-unstyled mb-0"><li><span>Workout completed</span><strong>4 / 5</strong></li><li><span>Berat turun</span><strong>-1.2 kg</strong></li><li><span>Massa otot naik</span><strong>+0.6 kg</strong></li><li><span>Kehadiran</span><strong>83%</strong></li></ul></div></div>
</div>
  <script>
                                               const copt={responsive:true,plugins:{legend:{labe l s:{co lor:'#f ff'}} } ,scales :{x:{t icks: {colo r:'#dd d'},grid: {c olor:' rgba( 255,2 55,2 55,.0 8)'}} ,y:{ti cks:{color:' #ddd'},gri d:{color:'rgba(2 55,255,255,.08)'}}}};
           new Chart(document.getElementById('weightChart'),{typ e :'lin e',dat a:{la b els:['W 1','W2 ','W3 ','W4 '],dat asets:[{l ab el:'Kg ',data:[7 8,77. 6,7 6. 9, 76. 2],borderColor:' #ff7a00',backgroundCo lor:'rgba(255 ,1 22 ,0 ,.18)',f ill: true,tension:.4}]},options:copt});
  new Chart(document.getElementById('attendanceChar t '),{t ype:'ba r',da t a:{labe ls:['W 1','W 2','W 3','W4 '],datase ts :[{lab el:'Visits',da ta:[3 ,4,5,4 ],bac kgrou ndColo r:'rgba(255, 122,0,.8)' ,borderRadius:12 }]},options:copt});
         new Chart(document.getElementById('muscleChart'),{type:'line',data:{labels:['W1','W2','W3','W4'],datasets:[{label:'Muscle Mass',data:[31.2,31.4,31.7,31.8],borderColor:'#ffffff',backgroundColor:'rgba(255,255,255,.18)',fill:true,tension:.4}]},options:copt});
</script>
<?php include 'includes/layout_bottom.php'; ?>
