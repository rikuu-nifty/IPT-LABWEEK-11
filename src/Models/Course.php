<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class Course extends BaseModel
{
    // Method to get all courses
    public function all()
    {
        $sql = "SELECT * FROM courses";
        $sql = "SELECT c.id, c.course_code, c.course_name, COUNT(e.student_code) AS enrolled_students
        FROM Courses c
        INNER JOIN Course_Enrolments e ON c.course_code = e.course_code
        GROUP BY c.course_code, c.course_name;
        ";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, '\App\Models\Course');
        return $result;
    }

    // Method to find a course by code
    public function find($code)
    {
        $sql = "SELECT * FROM Courses WHERE course_code = ?";
        $statement = $this->db->prepare($sql);
        $statement->execute([$code]);
        $result = $statement->fetchObject('\App\Models\Course');
        return $result;
    }

    // Method to get a list of enrollees for a specific course
    public function getEnrolees($course_code)
    {
        $sql = "SELECT s.student_code, CONCAT(s.first_name, ' ', s.last_name ) AS student_name, s.first_name, s.last_name, s.email, s.date_of_birth, s.sex
                FROM Course_Enrolments AS ce
                LEFT JOIN students AS s ON (s.student_code=ce.student_code)
                WHERE ce.course_code = :course_code";
        $statement = $this->db->prepare($sql);
        $statement->execute([
            'course_code' => $course_code
        ]);
        $result = $statement->fetchAll();
        return $result;
    }

    public function getCourseCode(){
        $sql = "SELECT course_code FROM Courses";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, '\App\Models\Course');
        return $result;
    }

    public function getCourseName(){
        $sql = "SELECT course_name FROM Courses";
        $statement = $this->db->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, '\App\Models\Course');
        return $result;

    }
}
