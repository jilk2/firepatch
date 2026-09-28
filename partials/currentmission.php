<?php
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
$nextMission = $missions[0] ?? null;
$queuedMissions = array_slice($missions, 1);

$purposeCount = count($nextMission['purpose'] ?? []);
$completedCount = count(array_filter($nextMission['state'] ?? [], fn($s) => $s === 'Klaar'));
$missionProgress = $purposeCount > 0 ? ($completedCount / $purposeCount) * 100 : 0;