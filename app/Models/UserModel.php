<?php

namespace App\Models;

use App\Services\PDOWrapper;

class UserModel extends BaseModel {

    public function __construct(PDOWrapper $pdoWrapper) {
        parent::__construct($pdoWrapper, 'users');
    }

}