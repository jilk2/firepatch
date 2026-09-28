<?php

$query = 'SELECT * FROM missions WHERE `start-time` >= ? ORDER BY `start-time` ASC';
$statement = mysqli_prepare($db, $query);
$statement->bind_param('s', $today);
$statement->execute();
$result = $statement->get_result();
$missions = [];
while ($row = $result->fetch_assoc()) {
    $purpose = json_decode((string) ($row['purpose'] ?? '[]'), true);
    $interventions = json_decode((string) ($row['interventions'] ?? '[]'), true);
    $storedState = json_decode((string) ($row['state'] ?? '[]'), true);

    $row['purpose'] = is_array($purpose) ? $purpose : [];
    $row['interventions'] = is_array($interventions) ? $interventions : [];
    $row['state'] = is_array($storedState) ? $storedState : [];
    $missions[] = $row;
}
$statement->close();

// select the next mission and the queued missions
$nextMission = $missions[0] ?? null;
$queuedMissions = array_slice($missions, 1);
$timeProgress = 0.0;
$state = [];

if ($nextMission) {
    // count goals and calculate progress
    $purposeCount = count($nextMission['purpose'] ?? []);
    $start = strtotime($nextMission['start-time']);
    $end = strtotime($nextMission['end-time']);
    $now = time();

    $duration = max(1, $end - $start);
    $elapsed = max(0, min($now - $start, $duration));

    $timeProgress = ($elapsed / $duration) * 100;
    $completedCount = min(
        $purposeCount,
        (int) floor(($timeProgress / 100) * $purposeCount)
    );

    // $missionProgress = $purposeCount > 0
//     ? ($completedCount / $purposeCount) * 100
//     : 0;

    foreach ($nextMission['purpose'] as $index => $purpose) {
        if ($index < $completedCount) {
            $state[] = 'Klaar';
        } elseif ($index === $completedCount && $now >= $start && $now < $end) {
            $state[] = 'Actief';
        } else {
            $state[] = 'Gepland';
        }
    }
}
