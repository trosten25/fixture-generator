<?php
// Reads fixtures.json and renders a simple HTML page.
// If fixtures.json missing, redirect back.

$dataFile = __DIR__ . '/fixtures.json';
if (!file_exists($dataFile)) {
    header('Location: ./knockout.html');
    exit;
}

$json = file_get_contents($dataFile);
$data = json_decode($json, true);
$rounds = $data['rounds'] ?? [];
$generatedAt = isset($data['generated_at']) ? date('Y-m-d H:i:s', $data['generated_at']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Fixture Results</title>
<link rel="stylesheet" href="../styles/knockout.css">
<style>
    body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Arial; background: #f6f7fb; color:#111; padding:2rem; }
    .wrap { max-width:1000px; margin:0 auto; }
    .round { margin-bottom:1.25rem; background:rgba(255,255,255,0.95); border-radius:10px; padding:1rem; box-shadow:0 8px 28px rgba(10,10,20,0.06); }
    .match { padding:0.6rem 0.4rem; border-bottom:1px solid #eef2f6; display:flex; justify-content:space-between; }
    .match:last-child { border-bottom: none; }
    .bye { opacity:0.7; font-style:italic; color:#666; }
    .meta { font-size:0.9rem; color:#666; margin-bottom:1rem; }
    .back { display:inline-block; margin-top:1rem; text-decoration:none; color:#0b5cff; }
</style>
</head>
<body>
<div class="wrap">
    <h1>Generated Fixtures</h1>
    <?php if ($generatedAt): ?>
        <div class="meta">Generated: <?= htmlspecialchars($generatedAt) ?></div>
    <?php endif; ?>

    <?php if (empty($rounds)): ?>
        <p>No fixtures found.</p>
    <?php else: ?>
        <?php foreach ($rounds as $idx => $round): ?>
            <div class="round">
                <h2>Round <?= $idx + 1 ?></h2>
                <?php foreach ($round as $match): 
                    $a = $match[0] ?? '';
                    $b = $match[1] ?? '';
                    if ($a === 'BYE' || $b === 'BYE'):
                        $playing = ($a === 'BYE') ? $b : $a;
                ?>
                    <div class="match bye">
                        <div><?= htmlspecialchars($playing) ?></div>
                        <div>(BYE)</div>
                    </div>
                <?php else: ?>
                    <div class="match">
                        <div><?= htmlspecialchars($a) ?></div>
                        <div>vs</div>
                        <div><?= htmlspecialchars($b) ?></div>
                    </div>
                <?php endif; endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a class="back" href="./knockout.html">← Back to setup</a>
</div>
</body>
</html>