<?php

namespace App\Models;

use App\Services\PDOWrapper;
use Exception;
use PDO;

class CheckupScheduleModel extends BaseModel {
    public function __construct(PDOWrapper $pdoWrapper) {
        parent::__construct($pdoWrapper, 'checkup_schedules');
    }

    public function createCheckupSchedule($userId, $doctorId, $date, $startTime, $endTime) {
        // Check for conflicts by querying the database
        // if ($this->hasScheduleConflict($doctorId, $date, $startTime, $endTime)) {
        //     throw new Exception("Doctor's schedule conflicts with the selected date and time range.");
        // }

        // Create the checkup schedule
        parent::create(['user_id' => $userId, 'doctor_id' => $doctorId, 'date' => $date, 'start_time' => $startTime, 'end_time' => $endTime]);
    }

    public function hasScheduleConflict($doctorId, $date, $startTime, $endTime) {
        $query = "SELECT * FROM $this->tableName WHERE doctor_id = :doctor_id AND date = :date AND
                  ((start_time <= :end_time AND end_time >= :start_time) OR
                  (start_time >= :start_time AND start_time <= :end_time) OR
                  (end_time >= :start_time AND end_time <= :end_time))";

        $params = [
            ':doctor_id' => $doctorId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        ];

        $stmt = $this->pdoWrapper->executeStatement($query, $params);
        $existingSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return !empty($existingSchedules);
    }
}