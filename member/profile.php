<?php
require_once 'auth_check.php';
requireRole(['admin', 'member']);
?>

<?php $pageTitle = 'Profile | GYMBRUT';
$activePage = 'profile';
$topbarTitle = 'Profile User';
include 'includes/layout_top.php'; ?>
<div class="row g-4">

    
       
       
      
      
    
    <div class="col-lg-4">
    <div class="premium-car
      d text-center h-100"><img src="https://ui-avatars
      .com/api/?name=Soni+Ju
        liansyah&background=ff7a00&color=fff&size=180" class="rounded-circle mb-3" width="140" height="140"><div class="
        sectio
        n-title">Soni Juliansyah</div><p class="text-white-50">Member Premium • Strength Focus</
p           ><button class="gradient-btn">U
        pload Foto</button></div></div>

        
        
            
            
            
          
        
           
        
           
        
      
    
    <div class="col-lg-8"><div class="form-card"><div class="section-title mb-3">Edit Profil</div><form class="row g-3"><div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" value="Soni Juliansyah"></div><div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="soni@gymbrut.com"></div><div class="col-md-6"><label class="form-label">No HP</label><input class="form-control" value="081234567890"></div><div class="col-md-6"><label class="form-label">Target</label><select class="form-select"><option>Strength</option><option>Fat Loss</option><option>Bulking</option></select></div><div class="col-md-6"><label class="form-label">Password Baru</label><input type="password" class="form-control" placeholder="••••••••"></div><div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" class="form-control" placeholder="••••••••"></div><div class="col-12 d-grid"><button type="button" class="gradient-btn py-3">Update Profil</button></div></form></div></div>
</div>
<?php include 'includes/layout_bottom.php'; ?>
