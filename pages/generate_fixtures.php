<?php
// Basic server-side handler: accepts teamNames[] via POST, builds round-robin fixtures,
// writes them to fixtures.json and redirects to result.php.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./knockout.html');
    exit;
}

$raw = $_POST['teamNames'] ?? [];
$teams = array_values(array_filter(array_map('trim', (array)$raw), function($v){ return $v !== ''; }));

if (count($teams) < 2) {
    header('Location: ./knockout.html?error=need_at_least_two');
    exit;
}

shuffle($teams);

$numTeams = count($teams);
if ($numTeams % 2 === 1) {
    $teams[] = 'BYE';
    $numTeams++;
}

$rounds = $numTeams - 1;
$halfSize = $numTeams / 2;

$fixed = $teams[0];
$rotating = array_slice($teams, 1);
$fixtures = [];

for ($r = 0; $r < $rounds; $r++) {
    $currentRound = [];

    $currentRound[] = [$fixed, $rotating[0]];

    for ($i = 1; $i < $halfSize; $i++) {
        $teamA = $rotating[$i];
        $teamB = $rotating[$numTeams - 2 - $i];
        $currentRound[] = [$teamA, $teamB];
    }

    $last = array_pop($rotating);
    array_unshift($rotating, $last);

    $fixtures[] = $currentRound;
}

$dataFile = __DIR__ . '/fixtures.json';
file_put_contents($dataFile, json_encode(['generated_at' => time(), 'rounds' => $fixtures], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

header('Location: ./result.php');
exit;
?>