<?php

namespace App\Models;

use App\Services\PDOWrapper;

class DoctorModel extends BaseModel {

    public function __construct(PDOWrapper $pdoWrapper) {
        parent::__construct($pdoWrapper, 'doctors');
    }

}