<?php
/* admin/workouts.php */
session_start();

$pageTitle = 'Workout Programs';
$topbarTitle = 'Workouts';
$topbarSubtitle = 'Kelola program latihan yang tersedia untuk seluruh member gym.';
$searchPlaceholder = 'Cari program workout...';

include '../../includes/layout_top.php';

$workouts = gymbrut_query_all($conn, "
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
  ORDER BY workout_id DESC
");
?>

<section class="page-section">
  <div class="membership-section-header">
    <div>
      <h3 class="section-title">Daftar Program Workout</h3>
      <p class="section-subtitle">Program latihan modern yang bisa dipilih sesuai tujuan fitness.</p>
    </div>

    <a href="addWorkouts.php" class="gradient-btn btn-sm">
      <i class="bi bi-plus-lg"></i> Tambah Program
    </a>
  </div>

  <?php if (empty($workouts)): ?>
    <div class="card-soft">
      <p class="text-soft mb-0">Belum ada data workout di database.</p>
    </div>
  <?php else: ?>
    <div class="page-grid grid-2">
      <?php foreach ($workouts as $workout): ?>
        <div class="card-soft">
          <div class="card-header-inline">
            <div>
              <h3 class="section-title"><?= e($workout['title']) ?></h3>
              <p class="section-subtitle">
                <?= e($workout['category']) ?> •
                <?= e($workout['sets_count'] ?? '-') ?> Set •
                <?= e($workout['reps_count'] ?? '-') ?> Reps
              </p>
            </div>

            <span class="badge-soft badge-info">Workout</span>
          </div>

          <div class="card-list">
            <div class="list-row">
              <div>
                <p class="list-row-title">Equipment</p>
                <p class="list-row-subtitle">
                  <?= !empty($workout['equipment']) ? e($workout['equipment']) : '-' ?>
                </p>
              </div>
            </div>

            <div class="list-row">
              <div>
                <p class="list-row-title">Tutorial</p>
                <p class="list-row-subtitle">
                  <?= !empty($workout['tutorial']) ? e($workout['tutorial']) : 'Belum ada tutorial.' ?>
                </p>
              </div>
            </div>
          </div>

          <div class="d-flex align-center gap-8 mt-3">
            <?php if (!empty($workout['youtube_url'])): ?>
              <a href="<?= e($workout['youtube_url']) ?>" target="_blank" class="btn-outline-soft btn-sm">
                <i class="bi bi-youtube"></i> Video
              </a>
            <?php endif; ?>

            <a href="detailWorkout.php?id=<?= $workout['workout_id'] ?>" class="btn-outline-soft btn-sm">
              <i class="bi bi-eye"></i> Detail
            </a>

            <a href="editWorkout.php?id=<?= $workout['workout_id'] ?>" class="btn-outline-soft btn-sm">
              <i class="bi bi-pencil-square"></i> Edit
            </a>

            <a href="deleteWorkout.php?id=<?= $workout['workout_id'] ?>"
              onclick="return confirm('Yakin ingin menghapus program ini?')" class="btn-outline-soft btn-sm">
              <i class="bi bi-trash3"></i> Hapus
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include '../../includes/layout_bottom.php'; ?>