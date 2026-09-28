<?php
// SELECT MISSIONS
$query = "SELECT * FROM missions WHERE `start-time` >= '$today' ORDER BY `start-time` ASC";
$result = mysqli_prepare($db, $query);
$result->execute();
$result = $result->get_result();
$missions = [];
while ($row = $result->fetch_assoc()) {
    $row['purpose'] = json_decode($row['purpose']);
    $row['interventions'] = json_decode($row['interventions']);
    $row['state'] = json_decode($row['state']);
    $missions[] = $row;
}
// select the next mission and the queued missions
$nextMission = $missions[0] ?? null;
$queuedMissions = array_slice($missions, 1);

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

    $state = [];

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