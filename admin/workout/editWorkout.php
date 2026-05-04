<?php
session_start();
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $equipment = $_POST['equipment'] ?? '';
    $tutorial = $_POST['tutorial'] ?? '';
    $youtube = $_POST['youtube_url'] ?? '';
    $sets = (int) ($_POST['sets_count'] ?? 0);
    $reps = $_POST['reps_count'] ?? '';

    // upload gambar
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/img/' . $imageName);

        $stmt = $conn->prepare("
      UPDATE workouts 
      SET title=?, category=?, equipment=?, tutorial=?, youtube_url=?, sets_count=?, reps_count=?, image_file=?
      WHERE workout_id=?
    ");
        $stmt->bind_param("sssssiisi", $title, $category, $equipment, $tutorial, $youtube, $sets, $reps, $imageName, $id);
    } else {
        $stmt = $conn->prepare("
      UPDATE workouts 
      SET title=?, category=?, equipment=?, tutorial=?, youtube_url=?, sets_count=?, reps_count=?
      WHERE workout_id=?
    ");
        $stmt->bind_param("sssssiis", $title, $category, $equipment, $tutorial, $youtube, $sets, $reps, $id);
    }

    $stmt->execute();

    // hapus step lama
    $conn->query("DELETE FROM workout_steps WHERE workout_id = $id");

    // insert ulang step
    if (!empty($_POST['step_instruction'])) {
        foreach ($_POST['step_instruction'] as $i => $instruction) {
            $order = $i + 1;
            $duration = $_POST['step_duration'][$i] ?? '';

            $stmt = $conn->prepare("
        INSERT INTO workout_steps (workout_id, step_order, instruction, duration)
        VALUES (?, ?, ?, ?)
      ");
            $stmt->bind_param("iiss", $id, $order, $instruction, $duration);
            $stmt->execute();
        }
    }

    header("Location: workouts.php");
    exit;
}

include '../../includes/layout_top.php';
?>

<section class="page-section">
    <div class="premium-card">

        <h3 class="section-title mb-3">Edit Workout</h3>

        <form method="POST" enctype="multipart/form-data">

            <div class="card-soft mb-3">
                <h4 class="section-title mb-2">Informasi Workout</h4>

                <div class="d-flex gap-8 mb-2">
                    <div style="flex:1;">
                        <label class="text-soft">Judul Workout</label>
                        <input type="text" name="title" class="form-control" value="<?= e($workout['title'] ?? '') ?>"
                            placeholder="Nama program latihan">
                    </div>

                    <div style="flex:1;">
                        <label class="text-soft">Kategori</label>
                        <input type="text" name="category" class="form-control"
                            value="<?= e($workout['category'] ?? '') ?>" placeholder="Contoh: Beginner / Strength">
                    </div>
                </div>

                <div class="d-flex gap-8 mb-2">
                    <div style="flex:1;">
                        <label class="text-soft">Equipment</label>
                        <input type="text" name="equipment" class="form-control"
                            value="<?= e($workout['equipment'] ?? '') ?>" placeholder="Alat yang digunakan">
                    </div>

                    <div style="flex:1;">
                        <label class="text-soft">Jumlah Set</label>
                        <input type="number" name="sets_count" class="form-control"
                            value="<?= e($workout['sets_count'] ?? 0) ?>">
                    </div>
                </div>

                <div class="d-flex gap-8 mb-2">
                    <div style="flex:1;">
                        <label class="text-soft">Repetisi / Durasi</label>
                        <input type="text" name="reps_count" class="form-control"
                            value="<?= e($workout['reps_count'] ?? '') ?>" placeholder="Contoh: 10-12 reps / 20 menit">
                    </div>

                    <div style="flex:1;">
                        <label class="text-soft">Link Video</label>
                        <input type="text" name="youtube_url" class="form-control"
                            value="<?= e($workout['youtube_url'] ?? '') ?>" placeholder="YouTube URL">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="text-soft">Deskripsi Latihan</label>
                    <textarea name="tutorial" class="form-control"
                        placeholder="Penjelasan singkat tentang workout ini"><?= e($workout['tutorial'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="text-soft">Gambar Workout</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>

            <?php foreach ($steps as $index => $step): ?>
                <div class="list-row mb-2" style="align-items:center; gap:10px;">

                    <div class="stat-icon" style="width:36px; height:36px;">
                        <?= $index + 1 ?>
                    </div>

                    <div style="flex:1;">

                        <label class="text-soft" style="font-size:12px;">Instruksi Latihan</label>
                        <input type="text" name="step_instruction[]" value="<?= e($step['instruction']) ?>"
                            class="form-control mb-1" placeholder="Contoh: Push up, squat">

                        <label class="text-soft" style="font-size:12px;">Durasi / Repetisi</label>
                        <input type="text" name="step_duration[]" value="<?= e($step['duration']) ?>" class="form-control"
                            placeholder="Contoh: 10x / 30 detik">
                    </div>

                    <button type="button" onclick="removeStep(this)" class="btn-outline-soft btn-sm">
                        ✕
                    </button>

                </div>
            <?php endforeach; ?>

    </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="gradient-btn">
            Update Workout
        </button>
    </div>

    </form>

    </div>
</section>

<script>
    function addStep() {
        const container = document.getElementById('steps-container');
        const count = container.children.length + 1;

        const div = document.createElement('div');
        div.classList.add('list-row', 'mb-2');
        div.style.alignItems = "center";
        div.style.gap = "10px";

        div.innerHTML = `
    <div class="stat-icon" style="width:36px; height:36px;">
      ${count}
    </div>

    <div style="flex:1;">

      <label class="text-soft" style="font-size:12px;">Instruksi Latihan</label>
      <input type="text" name="step_instruction[]" 
        class="form-control mb-1" 
        placeholder="Instruksi latihan">

      <label class="text-soft" style="font-size:12px;">Durasi / Repetisi</label>
      <input type="text" name="step_duration[]" 
        class="form-control" 
        placeholder="Durasi / repetisi">
    </div>

    <button type="button" onclick="removeStep(this)" class="btn-outline-soft btn-sm">
      ✕
    </button>
  `;

        container.appendChild(div);
    }

    function removeStep(btn) {
        btn.parentElement.remove();

        // update nomor step
        const items = document.querySelectorAll('#steps-container .stat-icon');
        items.forEach((el, index) => {
            el.innerText = index + 1;
        });
    }
</script>

<?php include '../../includes/layout_bottom.php'; ?>