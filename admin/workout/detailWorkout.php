<?php
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include '../../includes/auth_admin.php';
}

include '../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);

$workout = gymbrut_query_one($conn, "
  SELECT * FROM workouts WHERE workout_id = $id
");

$steps = gymbrut_query_all($conn, "
  SELECT * FROM workout_steps 
  WHERE workout_id = $id 
  ORDER BY step_order ASC
");

function getYoutubeId($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $params);
    return $params['v'] ?? '';
}

include '../../includes/layout_top.php';
?>

<section class="page-section">

  <div class="page-grid grid-2">

    <!-- ================= LEFT ================= -->
    <div>

      <!-- HEADER -->
      <div class="card-soft mb-3">

        <?php if (!empty($workout['image_file'])): ?>
            <img src="../../assets/img/<?= e($workout['image_file']) ?>"
               style="width:100%; height:180px; object-fit:cover; border-radius:12px; margin-bottom:12px;">
        <?php endif; ?>

        <h2 class="section-title"><?= e($workout['title']) ?></h2>

        <p class="section-subtitle">
          <?= e($workout['category']) ?> • 
          <?= e($workout['sets_count']) ?> Set • 
          <?= e($workout['reps_count']) ?>
        </p>

        <div class="mt-2">
          <span class="badge-soft badge-info"><?= e($workout['category']) ?></span>
        </div>

      </div>

      <?php if (!empty($workout['youtube_url'])): ?>
        <div class="card-soft mb-3">
          <h4 class="section-title mb-2">Tutorial</h4>

          <iframe 
            width="100%" 
            height="220"
            src="https://www.youtube.com/embed/<?= getYoutubeId($workout['youtube_url']) ?>"
            style="border-radius:12px;"
            frameborder="0"
            allowfullscreen>
          </iframe>
        </div>
      <?php endif; ?>

      <div class="card-soft">
        <h4 class="section-title mb-1">Deskripsi</h4>
        <p class="text-soft">
          <?= nl2br(e($workout['tutorial'])) ?>
        </p>
      </div>

    </div>


    <!-- ================= RIGHT ================= -->
    <div>

      <div class="card-soft">

        <h3 class="section-title mb-2">Langkah Latihan</h3>

        <?php if (empty($steps)): ?>
          <p class="text-soft">Belum ada instruksi detail.</p>
        <?php else: ?>

          <div class="card-list">

            <?php foreach ($steps as $step): ?>
              <div class="list-row" style="gap:12px;">

                <!-- nomor -->
                <div class="stat-icon">
                  <?= $step['step_order'] ?>
                </div>

                <!-- isi -->
                <div style="flex:1;">

                  <p class="list-row-title mb-1">
                    Step <?= $step['step_order'] ?>
                  </p>

                  <p class="list-row-subtitle mb-1">
                    <?= e($step['instruction']) ?>
                  </p>

                  <?php if (!empty($step['duration'])): ?>
                    <span class="badge-soft badge-active">
                      <?= e($step['duration']) ?>
                    </span>
                  <?php endif; ?>

                </div>

              </div>
            <?php endforeach; ?>

          </div>

        <?php endif; ?>

      </div>

    </div>

  </div>

</section>

<?php include '../../includes/layout_bottom.php'; ?>