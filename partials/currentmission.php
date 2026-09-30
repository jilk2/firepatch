<?php

$activeQuery = "SELECT * FROM missions WHERE mission_state = 'active' ORDER BY id ASC LIMIT 1";
$activeResult = mysqli_query($db, $activeQuery);

$nextMission = mysqli_fetch_assoc($activeResult) ?: null;

$queueQuery = "SELECT * FROM missions
               WHERE mission_state = 'queued'
               ORDER BY CASE WHEN priority = 'high' THEN 0 ELSE 1 END,
                        `start-time` ASC,
                        id ASC";
$queueResult = mysqli_query($db, $queueQuery);
$queuedMissions = [];

while ($row = mysqli_fetch_assoc($queueResult)) {
    $purpose = json_decode((string) ($row['purpose'] ?? '[]'), true);
    $interventions = json_decode((string) ($row['interventions'] ?? '[]'), true);
    $storedState = json_decode((string) ($row['purpose_state'] ?? '[]'), true);

    $row['purpose'] = is_array($purpose) ? $purpose : [];
    $row['interventions'] = is_array($interventions) ? $interventions : [];
    $row['purpose_state'] = is_array($storedState) ? $storedState : [];

    $queuedMissions[] = $row;
}

if ($nextMission) {
    $purpose = json_decode((string) ($nextMission['purpose'] ?? '[]'), true);
    $interventions = json_decode((string) ($nextMission['interventions'] ?? '[]'), true);
    $storedState = json_decode((string) ($nextMission['purpose_state'] ?? '[]'), true);

    $nextMission['purpose'] = is_array($purpose) ? $purpose : [];
    $nextMission['interventions'] = is_array($interventions) ? $interventions : [];
    $nextMission['purpose_state'] = is_array($storedState) ? $storedState : [];
}

$timeProgress = 0.0;
$state = [];


if ($nextMission) {

    // count goals and calculate progress

    $purposeCount = count($nextMission['purpose'] ?? []);

    $start = strtotime($nextMission['start-time']);
    $end = strtotime($nextMission['end-time']);
    $now = time();

    $duration = max(1, $end - $start);

    $elapsed = max(
        0,
        min($now - $start, $duration)
    );

    $timeProgress = ($elapsed / $duration) * 100;


    $completedCount = min(
        $purposeCount,
        (int) floor(
            ($timeProgress / 100) * $purposeCount
        )
    );


    foreach ($nextMission['purpose'] as $index => $purpose) {

        if ($index < $completedCount) {

            $state[] = 'Klaar';
            $logStatus = 'done';

        } elseif (
            $index === $completedCount &&
            $now >= $start &&
            $now < $end
        ) {

            $state[] = 'Actief';
            $logStatus = 'active';

        } else {

            $state[] = 'Gepland';
            $logStatus = 'pending';
        }

        //zal ik hier state[] pushen naar de db?


        // Status ook aanpassen in het logboek

        $logQuery = "UPDATE logboek
                     SET status = ?
                     WHERE mission_id = ?
                     AND activity = ?";

        $logResult = mysqli_prepare($db, $logQuery);

        $logResult->bind_param(
            'sis',
            $logStatus,
            $nextMission['id'],
            $purpose
        );

        $logResult->execute();
        $logResult->close();
    }

    $purposeState = json_encode(
        $state,
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );
    $stateQuery = "
    UPDATE missions
    SET purpose_state = ?
    WHERE id = ?
";

    $stateStatement = mysqli_prepare($db, $stateQuery);
    $stateStatement->bind_param(
        'si',
        $purposeState,
        $nextMission['id']
    );
    $stateStatement->execute();
    $stateStatement->close();
}