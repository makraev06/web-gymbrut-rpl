<?php
/* member/workouts.php */
session_start();

$_SESSION['role'] = $_SESSION['role'] ?? 'member';
$_SESSION['name'] = $_SESSION['name'] ?? 'Member';

$pageTitle = 'Workouts';
$topbarTitle = 'Workout Member';
$topbarSubtitle = 'Lihat daftar workout, kategori latihan, equipment, dan tutorial singkat.';
$searchPlaceholder = 'Cari workout...';

include '../includes/layout_top.php';

$userId = (int) ($_SESSION['user_id'] ?? 0);

$selectedCategory = $_GET['category'] ?? 'Semua';
$search = trim($_GET['search'] ?? '');

/* =========================
   CEK MEMBERSHIP AKTIF
========================= */
$activeMembership = null;

$stmt = $conn->prepare("
    SELECT 
        m.membership_id,
        m.status,
        m.start_date,
        m.end_date,
        mp.package_name
    FROM memberships m
    JOIN membership_packages mp ON m.package_id = mp.package_id
    WHERE m.user_id = ?
    AND m.status = 'aktif'
    AND m.end_date >= CURDATE()
    ORDER BY m.end_date DESC
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
  $activeMembership = $result->fetch_assoc();
}

/* =========================
   AMBIL KATEGORI WORKOUT
========================= */
$categories = ['Semua'];

$q = $conn->query("
    SELECT DISTINCT category
    FROM workouts
    ORDER BY category ASC
");

if ($q) {
  while ($row = $q->fetch_assoc()) {
    $categories[] = $row['category'];
  }
}

/* =========================
   AMBIL WORKOUTS
========================= */
$workouts = [];

$sql = "
    SELECT 
        workout_id,
        category,
        title,
        equipment,
        tutorial,
        youtube_url,
        sets_count,
        reps_count,
        image_file
    FROM workouts
    WHERE 1 = 1
";

$params = [];
$types = '';

if ($selectedCategory !== 'Semua') {
  $sql .= " AND category = ?";
  $params[] = $selectedCategory;
  $types .= 's';
}

if ($search !== '') {
  $sql .= " AND (
        title LIKE ?
        OR category LIKE ?
        OR equipment LIKE ?
        OR tutorial LIKE ?
    )";

  $keyword = '%' . $search . '%';
  $params[] = $keyword;
  $params[] = $keyword;
  $params[] = $keyword;
  $params[] = $keyword;
  $types .= 'ssss';
}

$sql .= " ORDER BY workout_id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $workouts[] = $row;
  }
}

/* =========================
   STATISTIK WORKOUT
========================= */
$totalWorkouts = count($workouts);

$totalAllWorkouts = 0;
$q = $conn->query("SELECT COUNT(*) AS total FROM workouts");
if ($q && $q->num_rows > 0) {
  $totalAllWorkouts = (int) $q->fetch_assoc()['total'];
}

$totalCategories = 0;
$q = $conn->query("SELECT COUNT(DISTINCT category) AS total FROM workouts");
if ($q && $q->num_rows > 0) {
  $totalCategories = (int) $q->fetch_assoc()['total'];
}

$membershipStatusText = $activeMembership ? 'Aktif' : 'Tidak Aktif';
$membershipBadge = $activeMembership ? 'badge-active' : 'badge-expired';
$membershipPackage = $activeMembership['package_name'] ?? 'Belum ada membership aktif';

/* =========================
   HELPER
========================= */
function workoutBadgeClass($category)
{
  if ($category === 'Fat Loss') {
    return 'badge-failed';
  }

  if ($category === 'Bulking') {
    return 'badge-active';
  }

  if ($category === 'Cardio') {
    return 'badge-info';
  }

  if ($category === 'Strength') {
    return 'badge-pending';
  }

  return 'badge-info';
}
?>

<div class="dashboard-hero mb-4">
  <div>
    <span class="banner-pill">
      <i class="bi bi-lightning-charge-fill"></i> Workout Library
    </span>

    <h2>Daftar Workout</h2>
    <p>
      Pilih latihan sesuai tujuan fitness kamu. Gunakan filter kategori untuk
      mencari workout seperti Fat Loss, Bulking, Cardio, Strength, dan Beginner.
    </p>
  </div>

  <div class="dashboard-hero-card">
    <span>Status Membership</span>
    <strong><?= e($membershipStatusText) ?></strong>
    <small><?= e($membershipPackage) ?></small>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Total Workout</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= number_format($totalAllWorkouts) ?>
        </h3>
        <span class="stat-meta">Semua workout tersedia</span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-list-check"></i>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Kategori</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= number_format($totalCategories) ?>
        </h3>
        <span class="stat-meta">Jenis latihan tersedia</span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-tags-fill"></i>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="stat-card stat-card-modern">
      <div>
        <p class="stat-label">Hasil Filter</p>
        <h3 class="stat-value" style="font-size:24px;">
          <?= number_format($totalWorkouts) ?>
        </h3>
        <span class="stat-meta">Workout yang sedang tampil</span>
      </div>

      <div class="stat-icon">
        <i class="bi bi-search"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="premium-card">
      <div class="card-header-inline">
        <div>
          <h3 class="section-title">Filter Workout</h3>
          <p class="section-subtitle">
            Cari berdasarkan nama workout, kategori, equipment, atau tutorial.
          </p>
        </div>

        <span class="badge-soft <?= e($membershipBadge) ?>">
          <?= e($membershipStatusText) ?>
        </span>
      </div>

      <form method="GET" class="row g-3">
        <div class="col-md-5">
          <label class="form-label">Cari Workout</label>
          <input type="text" name="search" class="form-control" value="<?= e($search) ?>"
            placeholder="Contoh: cardio, dumbbell, beginner...">
        </div>

        <div class="col-md-4">
          <label class="form-label">Kategori</label>
          <select name="category" class="form-select">
            <?php foreach ($categories as $category): ?>
              <option value="<?= e($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>>
                <?= e($category) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3 d-flex align-center" style="gap:8px; margin-top:30px;">
          <button type="submit" class="gradient-btn w-100">
            <i class="bi bi-funnel"></i>
            Filter
          </button>

          <a href="workouts.php" class="btn-outline-soft">
            Reset
          </a>
        </div>
      </form>

      <?php if (!$activeMembership): ?>
        <p class="text-soft mt-3 mb-0">
          Catatan: membership kamu belum aktif. Kamu tetap bisa melihat daftar workout,
          tapi untuk check-in dan progress harus memiliki membership aktif.
        </p>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row g-4">
  <?php if (empty($workouts)): ?>
    <div class="col-12">
      <div class="premium-card">
        <p class="text-soft mb-0">
          Tidak ada workout yang cocok dengan filter.
        </p>
      </div>
    </div>
  <?php endif; ?>

  <?php foreach ($workouts as $workout): ?>
    <?php
    $badge = workoutBadgeClass($workout['category']);

    $imagePath = '';
    if (!empty($workout['image_file'])) {
      $imagePath = '../assets/img/' . $workout['image_file'];
    }

    $youtubeUrl = trim($workout['youtube_url'] ?? '');
    ?>

    <div class="col-md-6 col-xl-4">
      <div class="premium-card h-100">
        <?php if (!empty($imagePath)): ?>
          <div style="height:180px; overflow:hidden; border-radius:16px; margin-bottom:16px; background:#f6f7fb;">
            <img src="<?= e($imagePath) ?>" alt="<?= e($workout['title']) ?>"
              style="width:100%; height:100%; object-fit:cover;">
          </div>
        <?php else: ?>
          <div style="
                            height:180px; 
                            border-radius:16px; 
                            margin-bottom:16px; 
                            background:linear-gradient(135deg,#fff3e8,#ffffff);
                            border:1px solid var(--primary-border);
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">
            <i class="bi bi-lightning-charge-fill" style="font-size:42px; color:var(--primary-dark);"></i>
          </div>
        <?php endif; ?>

        <div class="card-header-inline" style="margin-bottom:12px;">
          <div>
            <h3 class="section-title">
              <?= e($workout['title']) ?>
            </h3>

            <p class="section-subtitle">
              <?= e($workout['equipment'] ?: 'Tanpa equipment khusus') ?>
            </p>
          </div>

          <span class="badge-soft <?= e($badge) ?>">
            <?= e($workout['category']) ?>
          </span>
        </div>

        <ul class="metric-list mb-3">
          <li>
            <span>Set</span>
            <strong><?= e($workout['sets_count'] ?? '-') ?></strong>
          </li>

          <li>
            <span>Repetisi / Durasi</span>
            <strong><?= e($workout['reps_count'] ?? '-') ?></strong>
          </li>
        </ul>

        <p class="text-soft">
          <?= e($workout['tutorial'] ?: 'Belum ada tutorial untuk workout ini.') ?>
        </p>

        <div class="d-flex gap-8 mt-2">

          <a href="../admin/workout/detailWorkout.php?id=<?= $workout['workout_id'] ?>"
            class="btn-outline-soft btn-sm">
            <i class="bi bi-eye"></i>
            Detail
          </a>

          <?php if (!empty($youtubeUrl)): ?>
            <a href="<?= e($youtubeUrl) ?>" target="_blank" class="gradient-btn btn-sm">
              <i class="bi bi-youtube"></i>
              Tutorial
            </a>
          <?php endif; ?>

        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include '../includes/layout_bottom.php'; ?>