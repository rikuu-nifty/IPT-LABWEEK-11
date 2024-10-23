<?php

namespace App\Controllers;


require 'vendor/autoload.php';
use App\Models\Course;
use App\Controllers\BaseController;
use Fpdf\Fpdf;



class CourseController extends BaseController
{
    public function list()
    {
        $obj = new Course();
        $courses = $obj->all();

        $template = 'courses';
        $data = [
            'items' => $courses
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function viewCourse($course_code)
    {
        $courseObj = new Course();
        $course = $courseObj->find($course_code);
        $enrolees = $courseObj->getEnrolees($course_code);

        $template = 'single-course';
        $data = [
            'course' => $course,
            'enrollees' => $enrolees
        ];

        $output = $this->render($template, $data);

        return $output;
    }


    public function exportCourse($course_code) {
        $obj = new Course();
        
        // Get course details
        $course = $obj->find($course_code);  
        // Get enrollees for the course
        $enrollees = $obj->getEnrolees($course_code);
    
        // Create a new PDF
        $pdf = new Fpdf();
        $pdf->AddPage();
        
        // Set a custom header
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, 'Course Information', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Use a dashed line for separator
        
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(128, 128, 128);
        // With the custom dashed line method:
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(128, 128, 128);
        $this->drawDashedLine($pdf, 10, 25, 200, 25);
        
        // Course details with customized style
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(240, 240, 240); // Light grey background
        $pdf->Cell(50, 10, 'Course Code:', 0, 0, 'L', true);
        $pdf->Cell(0, 10, $course->course_code, 0, 1, 'L', true);
        
        $pdf->Cell(50, 10, 'Course Name:', 0, 0, 'L', true);
        $pdf->Cell(0, 10, $course->course_name, 0, 1, 'L', true);
    
        $pdf->Cell(50, 10, 'Description:', 0, 0, 'L', true);
        $pdf->MultiCell(0, 10, $course->description, 0, 'L', true);  
        
        $pdf->Cell(50, 10, 'Credits:', 0, 0, 'L', true);
        $pdf->Cell(0, 10, $course->credits, 0, 1, 'L', true);
        
        // Line break
        $pdf->Ln(10);
        
        // List of enrollees with title style
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(33, 150, 243); // Blue color for title
        $pdf->Cell(0, 10, 'List of Enrollees', 0, 1, 'C');  
        $pdf->Ln(5);
        
        // Define column widths
        $idWidth = 20;
        $firstNameWidth = 30;
        $lastNameWidth = 30;
        $emailWidth = 60;
        $dobWidth = 30;
        $sexWidth = 20;
    
        // Calculate total width of the table
        $totalWidth = $idWidth + $firstNameWidth + $lastNameWidth + $emailWidth + $dobWidth + $sexWidth;
    
        // Calculate starting X position for center alignment
        $startX = ($pdf->GetPageWidth() - $totalWidth) / 2;
    
        // Table header with background color and bold font
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(33, 150, 243);  // Blue background for header
        $pdf->SetTextColor(255, 255, 255);  // White text color for header
        
        // Set X position for header
        $pdf->SetX($startX);
        $pdf->Cell($idWidth, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell($firstNameWidth, 10, 'First Name', 1, 0, 'C', true);
        $pdf->Cell($lastNameWidth, 10, 'Last Name', 1, 0, 'C', true);
        $pdf->Cell($emailWidth, 10, 'Email', 1, 0, 'C', true);
        $pdf->Cell($dobWidth, 10, 'Date of Birth', 1, 0, 'C', true);
        $pdf->Cell($sexWidth, 10, 'Sex', 1, 0, 'C', true);
        $pdf->Ln();
        
        // Reset font and colors for table content
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0); 
        
        // Use alternating row colors for content
        $fill = false;
        if (!empty($enrollees)) {
            foreach ($enrollees as $index => $enrollee) {
                // Set X position for content cells
                $pdf->SetX($startX);  
                $pdf->SetFillColor(245, 245, 245); // Light grey for alternating rows
                $pdf->Cell($idWidth, 10, $enrollee["student_code"], 1, 0, 'C', $fill);
                $pdf->Cell($firstNameWidth, 10, $enrollee["first_name"], 1, 0, 'C', $fill);
                $pdf->Cell($lastNameWidth, 10, $enrollee["last_name"], 1, 0, 'C', $fill);
                $pdf->Cell($emailWidth, 10, $enrollee["email"], 1, 0, 'C', $fill);
                $pdf->Cell($dobWidth, 10, $enrollee["date_of_birth"], 1, 0, 'C', $fill);
                $pdf->Cell($sexWidth, 10, $enrollee["sex"], 1, 0, 'C', $fill);
                $pdf->Ln();
                
                // Alternate fill color
                $fill = !$fill;
            }
        } else {
            $pdf->SetX($startX); 
            $pdf->Cell(0, 10, 'No enrollees found for this course.', 1, 1, 'C');
        }
        
        // Output the PDF
        $pdf->Output('D', 'course_' . $course_code . '_enrollees.pdf');
    }

    
/**
 * Draw a dashed line in FPDF.
 * @param Fpdf 
 * @param float 
 * @param float 
 * @param float 
 * @param float 
 * @param float 
 * @param float 
 */
private function drawDashedLine($pdf, $x1, $y1, $x2, $y2, $dashLength = 1, $spaceLength = 1) {
    $dashCount = ceil(sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2)) / ($dashLength + $spaceLength));
    $dx = ($x2 - $x1) / $dashCount;
    $dy = ($y2 - $y1) / $dashCount;

    for ($i = 0; $i < $dashCount; $i++) {
        if ($i % 2 == 0) {
            $pdf->Line($x1 + $i * $dx, $y1 + $i * $dy, $x1 + ($i + 1) * $dx, $y1 + ($i + 1) * $dy);
        }
    }
}

}
