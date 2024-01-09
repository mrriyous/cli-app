<?php

use App\Models\CheckupScheduleModel;
use App\Models\DoctorModel;
use App\Models\UserModel;
use App\Services\PDOWrapper;

require 'vendor/autoload.php';

class CLIApp {

    private $pdoWrapper;
    private $userModel;
    private $doctorModel;
    private $checkupScheduleModel;

    public function __construct() {
        $this->pdoWrapper = new PDOWrapper('localhost', 'db_user_cli', 'root', '');
        $this->userModel = new UserModel($this->pdoWrapper);
        $this->doctorModel = new DoctorModel($this->pdoWrapper);
        $this->checkupScheduleModel = new CheckupScheduleModel($this->pdoWrapper);
    }

    public function run() {
        while (true) {
            $this->printMenu();
            $choice = trim(fgets(STDIN));

            switch ($choice) {
                case '1':
                    $this->viewUser();
                    break;

                case '2':
                    $this->createUser();
                    break;

                case '3':
                    $this->updateUser();
                    break;

                case '4':
                    $this->deleteUser();
                    break;

                case '5':
                    $this->listUsers();
                    break;

                case '6':
                    $this->viewDoctor();
                    break;

                case '7':
                    $this->createDoctor();
                    break;

                case '8':
                    $this->updateDoctor();
                    break;

                case '9':
                    $this->deleteDoctor();
                    break;

                case '10':
                    $this->listDoctors();
                    break;

                case '11':
                    $this->listCheckupSchedules();
                    break;
                
                case '12':
                    $this->assignScheduleToUser();
                    break;

                case '0':
                    echo "Exiting...\n";
                    exit;

                default:
                    echo "Invalid choice. Please try again.\n";
                    break;
            }
        }
    }

    /**
     * Displays the main menu for the command-line app.
     */
    private function printMenu() {
        echo "MENU:\n";
        echo "1. View User\n";
        echo "2. Create User\n";
        echo "3. Update User\n";
        echo "4. Delete User\n";
        echo "5. List Users\n";
        echo "6. View Doctor\n";
        echo "7. Create Doctor\n";
        echo "8. Update Doctor\n";
        echo "9. Delete Doctor\n";
        echo "10. List Doctors\n";
        echo "11. List Checkup Schedules\n";
        echo "12. Assign Checkup Schedule\n";
        echo "0. Exit\n";
        echo "Enter your choice: ";
    }

    /**
     * Prompts the user for input and returns the entered value.
     * 
     * @param string $prompt The prompt message to display to the user.
     * @param function $validator The callback function for validation
     * @return string The user-entered value.
     */
    private function promptForInput($prompt, $validator = null) {
        do {
            echo $prompt;
            $input = trim(fgets(STDIN));
    
            if (!empty($validator)) {
                if (!$validator($input)) {
                    echo "Invalid input. Please try again.\n";
                }
            }
        } while (!empty($validator) && !$validator($input));
    
        return $input;
    }

    /**
     * Displays details of a specific user based on user input.
     */
    private function viewUser() {
        echo "Enter the User ID you want to view: ";
        $userId = trim(fgets(STDIN));

        if (!is_numeric($userId) || $userId <= 0) {
            echo "Invalid User ID. Please enter a positive numeric value.\n";
            return;
        }

        $user = $this->userModel->getById($userId);

        if (empty($user)) {
            echo "User not found.\n";
            return;
        }

        echo "User Details:\n";
        echo "ID: {$user['id']}\n";
        echo "Username: {$user['username']}\n";
        echo "Email: {$user['email']}\n";
        echo "Created At: {$user['created_at']}\n=========\n";
    }

    /**
     * Creates a new user based on user input.
     */
    private function createUser() {
        echo "Enter the new user details:\n";

        // Get and validate unique username
        $username = $this->promptForInput("Username: ", function ($input) {
            return $this->userModel->isFieldUnique('users', 'username', trim($input)) && (strlen(trim($input)) > 0);
        });

        // Get and validate unique email
        $email = $this->promptForInput("Email: ", function ($input) {
            return $this->userModel->isFieldUnique('users', 'email', trim($input)) && filter_var(trim($input), FILTER_VALIDATE_EMAIL);
        });

        // Create user if validation passes
        if ($username && $email) {
            $this->userModel->create(['username' => $username, 'email' => $email]);
            echo "User created successfully!\n===============\n";
        } else {
            echo "Invalid input. User not created.\n===============\n";
        }
    }

    /**
     * Updates the details of an existing user based on user input.
     */
    private function updateUser() {
        echo "Enter the User ID you want to update: ";
        $userId = trim(fgets(STDIN));

        if (!is_numeric($userId) || $userId <= 0) {
            echo "Invalid User ID. Please enter a positive numeric value.\n";
            return;
        }

        $user = $this->userModel->getById($userId);

        if (!$user) {
            echo "User not found.\n";
            return;
        }

        echo "Current User Details:\n";
        echo "ID: {$user['id']}\n";
        echo "Username: {$user['username']}\n";
        echo "Email: {$user['email']}\n";

        echo "Enter the new user details:\n";
        // Get and validate unique username
        $newUsername = $this->promptForInput("Username (Press Enter to keep current value '{$user['username']}'): ", function ($input) use ($user) {
            return $this->userModel->isFieldUnique('users', 'username', trim($input), $user['id']);
        });

        // Get and validate unique email
        $newEmail = $this->promptForInput("Email (Press Enter to keep current value '{$user['email']}'): ", function ($input) use ($user) {
            return $this->userModel->isFieldUnique('users', 'email', trim($input), $user['id']);
        });

        // Check for invalid input
        if ($newUsername !== false && $newEmail) {
            // Update user
            $this->userModel->update($userId, ['username' => $newUsername, 'email' => $newEmail]);
            echo "User updated successfully!\n===============\n";
        } else {
            echo "Invalid input. User not updated.\n===============\n";
        }
    }

    /**
     * Deletes an existing user based on user input.
     */
    private function deleteUser() {
        echo "Enter the User ID you want to delete: ";
        $userId = trim(fgets(STDIN));

        if (!is_numeric($userId) || $userId <= 0) {
            echo "Invalid User ID. Please enter a positive numeric value.\n";
            return;
        }

        $user = $this->userModel->getById($userId);

        if (!$user) {
            echo "User not found.\n";
            return;
        }

        echo "Are you sure you want to delete the following user?\n";
        echo "ID: {$user['id']}\n";
        echo "Username: {$user['username']}\n";
        echo "Email: {$user['email']}\n";

        echo "Type 'yes' to confirm deletion: ";
        $confirmation = trim(fgets(STDIN));

        if (strtolower($confirmation) === 'yes') {
            // Delete user
            $this->userModel->delete($userId);
            echo "User deleted successfully!\n===============\n";
        } else {
            echo "Deletion canceled.\n===============\n";
        }
    }

    /**
     * Lists all users along with their details.
     */
    private function listUsers() {
        $users = $this->userModel->getAll();

        if (empty($users)) {
            echo "No users found.\n";
            return;
        }

        echo "List of Users:\n";

        foreach ($users as $user) {
            echo "ID: {$user['id']}\n";
            echo "Username: {$user['username']}\n";
            echo "Email: {$user['email']}\n";
            echo "Created At: {$user['created_at']}\n==============\n";
        }
    }

    /**
     * Displays details of a specific doctor based on user input.
     */
    private function viewDoctor() {
        echo "Enter the Doctor ID you want to view: ";
        $doctorId = trim(fgets(STDIN));

        if (!is_numeric($doctorId) || $doctorId <= 0) {
            echo "Invalid Doctor ID. Please enter a positive numeric value.\n";
            return;
        }

        $doctor = $this->doctorModel->getById($doctorId);

        if (!$doctor) {
            echo "Doctor not found.\n";
            return;
        }

        echo "Doctor Details:\n";
        echo "ID: {$doctor['id']}\n";
        echo "Name: {$doctor['name']}\n";
        echo "Specialization: {$doctor['specialty']}\n";
        echo "Created At: {$doctor['created_at']}\n===============\n";
    }

    /**
     * Creates a new doctor based on user input.
     */
    private function createDoctor() {
        echo "Enter the new doctor details:\n";

        // Get and validate unique doctor name
        $name = $this->promptForInput("Name: ", function ($input) {
            return $this->doctorModel->isFieldUnique('doctors', 'name', trim($input));
        });

        $specialty = $this->promptForInput("Specialization: ");

        // Create doctor if validation passes
        if ($name) {
            $this->doctorModel->create(['name' => $name, 'specialty' => $specialty]);
            echo "Doctor created successfully!\n===============\n";
        } else {
            echo "Invalid input. Doctor not created.\n===============\n";
        }
    }

    /**
     * Updates the details of an existing doctor based on user input.
     */
    private function updateDoctor() {
        echo "Enter the Doctor ID you want to update: ";
        $doctorId = trim(fgets(STDIN));

        if (!is_numeric($doctorId) || $doctorId <= 0) {
            echo "Invalid Doctor ID. Please enter a positive numeric value.\n";
            return;
        }

        $doctor = $this->doctorModel->getById($doctorId);

        if (!$doctor) {
            echo "Doctor not found.\n";
            return;
        }

        echo "Current Doctor Details:\n";
        echo "ID: {$doctor['id']}\n";
        echo "Name: {$doctor['name']}\n";
        echo "Specialization: {$doctor['specialty']}\n";

        echo "Enter the new doctor details:\n";
        // Get and validate unique doctor name
        $newName = $this->promptForInput("Name (Press Enter to keep current value '{$doctor['name']}'): ", function ($input) use ($doctor) {
            return $this->doctorModel->isFieldUnique('doctors', 'name', trim($input), $doctor['id']);
        });

        $newSpecialization = $this->promptForInput("Specialization (Press Enter to keep current value '{$doctor['specialty']}'): ");

        // Check for invalid input
        if ($newName !== false) {
            // Update doctor
            $this->doctorModel->update($doctorId, ['name' => $newName, 'specialty' => $newSpecialization]);
            echo "Doctor updated successfully!\n===============\n";
        } else {
            echo "Invalid input. Doctor not updated.\n===============\n";
        }
    }

    /**
     * Deletes an existing doctor based on user input.
     */
    private function deleteDoctor() {
        echo "Enter the Doctor ID you want to delete: ";
        $doctorId = trim(fgets(STDIN));

        if (!is_numeric($doctorId) || $doctorId <= 0) {
            echo "Invalid Doctor ID. Please enter a positive numeric value.\n";
            return;
        }

        $doctor = $this->doctorModel->getById($doctorId);

        if (!$doctor) {
            echo "Doctor not found.\n";
            return;
        }

        echo "Are you sure you want to delete the following doctor?\n";
        echo "ID: {$doctor['id']}\n";
        echo "Name: {$doctor['name']}\n";
        echo "Specialization: {$doctor['specialty']}\n";

        echo "Type 'yes' to confirm deletion: ";
        $confirmation = trim(fgets(STDIN));

        if (strtolower($confirmation) === 'yes') {
            // Delete doctor
            $this->doctorModel->delete($doctorId);
            echo "Doctor deleted successfully!\n===============\n";
        } else {
            echo "Deletion canceled.\n===============\n";
        }
    }

    /**
     * Lists all doctors along with their details.
     */
    private function listDoctors() {
        $doctors = $this->doctorModel->getAll();

        if (empty($doctors)) {
            echo "No doctors found.\n";
            return;
        }

        echo "List of Doctors:\n";

        foreach ($doctors as $doctor) {
            echo "ID: {$doctor['id']}\n";
            echo "Name: {$doctor['name']}\n";
            echo "Specialization: {$doctor['specialty']}\n";
            echo "Created At: {$doctor['created_at']}\n===============\n";
        }
    }

    /**
     * Lists all checkup schedules along with their details.
     */
    private function listCheckupSchedules() {
        $checkupSchedules = $this->checkupScheduleModel->getAll();

        if ($checkupSchedules) {
            echo "List of Checkup Schedules:\n";

            foreach ($checkupSchedules as $schedule) {
                $user = $this->userModel->getById($schedule['user_id']);
                $doctor = $this->doctorModel->getById($schedule['doctor_id']);

                echo "Schedule ID: {$schedule['id']}\n";
                echo "User: {$user['username']}\n";
                echo "Doctor: {$doctor['name']} (Specialty: {$doctor['specialty']})\n";
                echo "Date: {$schedule['date']}\n";
                echo "Time: {$schedule['start_time']} - {$schedule['end_time']}\n===============\n";
            }
        } else {
            echo "No checkup schedules found.\n===============\n";
        }
    }

    /**
     * Handles the checkup schedule assignment functionality in the command-line app.
     */
    private function assignScheduleToUser() {
        echo "Enter the User ID for schedule assignment: ";
        $userId = trim(fgets(STDIN));

        if (!is_numeric($userId) || $userId <= 0) {
            echo "Invalid User ID. Please enter a positive numeric value.\n";
            return;
        }

        $user = $this->userModel->getById($userId);

        if (!$user) {
            echo "User not found.\n";
            return;
        }

        echo "Enter the Doctor ID for the schedule: ";
        $doctorId = trim(fgets(STDIN));

        if (!is_numeric($doctorId) || $doctorId <= 0) {
            echo "Invalid Doctor ID. Please enter a positive numeric value.\n";
            return;
        }

        $doctor = $this->doctorModel->getById($doctorId);

        if (!$doctor) {
            echo "Doctor not found.\n";
            return;
        }

        // Get and validate schedule details
        $date = $this->promptForInput("Schedule Date (YYYY-MM-DD): ");
        $startTime = $this->promptForInput("Start Time (HH:MM): ");
        $endTime = $this->promptForInput("End Time (HH:MM): ");

        // Validate the date and time format
        if (!$this->isValidDateTime($date, 'Y-m-d') || !$this->isValidDateTime($startTime, 'H:i') || !$this->isValidDateTime($endTime, 'H:i')) {
            echo "Invalid date or time format. Please use the correct format.\n";
            return;
        }

        // Check for schedule conflicts
        if ($this->checkupScheduleModel->hasScheduleConflict($doctorId, $date, $startTime, $endTime)) {
            echo "Schedule conflict. The selected time is not available.\n";
            return;
        }

        // Create the schedule
        $this->checkupScheduleModel->create(['user_id' => $userId, 'doctor_id' => $doctorId, 'date' => $date, 'start_time' => $startTime, 'end_time' => $endTime]);

        echo "Schedule assigned successfully!\n===============\n";
    }

    private function isValidDateTime($dateTimeString, $format) {
        $dateTime = DateTime::createFromFormat($format, $dateTimeString);
        return $dateTime && $dateTime->format($format) === $dateTimeString;
    }
}

$app = new CLIApp();
$app->run();