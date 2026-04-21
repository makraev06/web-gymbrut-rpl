<?php
require_once '../includes/auth_check.php';
requireRole(['admin']);
?>

<?php $pageTitle = 'Members | GYMBRUT';
$activePage = 'members';
$topbarTitle = 'Kelola Member';
include '../includes/layout_top.php'; ?>
<div class="row g-4">
  <div class="col-xl-8">
    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <div class="section-title">Data Member</div><small class="text-white-50">CRUD data member sesuai SRS</small>
        </div><button class="gradient-btn" data-toast-trigger><i class="bi bi-plus-lg"></i> Tambah Member</button>
      </div>
      <div class="table-responsive">
        <table class="table table-dark-premium align-middle">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>No HP</th>
              <th>Gender</th>
              <th>Usia</th>
              <th>Target</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Raka Saputra</td>
              <td>raka@gymbrut.com</td>
              <td>081234567001</td>
              <td>L</td>
              <td>24</td>
              <td>Bulking</td>
              <td><button class="btn btn-sm btn-outline-light rounded-pill">Edit</button></td>
            </tr>
            <tr>
              <td>Nadia Pratama</td>
              <td>nadia@gymbrut.com</td>
              <td>081234567002</td>
              <td>P</td>
              <td>22</td>
              <td>Fat Loss</td>
              <td><button class="btn btn-sm btn-outline-light rounded-pill">Edit</button></td>
            </tr>
            <tr>
              <td>Farel Wijaya</td>
              <td>farel@gymbrut.com</td>
              <td>081234567003</td>
              <td>L</td>
              <td>28</td>
              <td>Strength</td>
              <td><button class="btn btn-sm btn-outline-light rounded-pill">Edit</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="form-card">
      <div class="section-title mb-3">Form Member</div>
      <form class="row g-3">
        <div class="col-12"><input class="form-control" placeholder="Nama member"></div>
        <div class="col-12"><input class="form-control" placeholder="Email"></div>
        <div class="col-12"><input class="form-control" placeholder="No HP"></div>
        <div class="col-md-6"><select class="form-select">
            <option>Gender</option>
            <option>Laki-laki</option>
            <option>Perempuan</option>
          </select></div>
        <div class="col-md-6"><input class="form-control" placeholder="Usia"></div>
        <div class="col-md-6"><input class="form-control" placeholder="Tinggi badan"></div>
        <div class="col-md-6"><input class="form-control" placeholder="Berat badan"></div>
        <div class="col-12"><select class="form-select">
            <option>Target fitness</option>
            <option>Fat Loss</option>
            <option>Bulking</option>
            <option>Cardio</option>
            <option>Strength</option>
            <option>Beginner</option>
          </select></div>
        <div class="col-12 d-grid"><button type="button" class="gradient-btn py-3">Simpan Member</button></div>
      </form>
    </div>
  </div>
</div>
<?php include '../includes/layout_bottom.php'; ?>