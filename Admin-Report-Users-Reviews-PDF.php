<?php
require_once('tcpdf/TCPDF-main/tcpdf.php');

class MYPDF extends TCPDF {
    // Custom Header with Logo
    public function Header() {
        $this->SetFillColor(120, 162, 76); // #78A24C (RGB)
        $this->RoundedRect(8, 8, 50, 12, 5, '1111', 'F'); // (x, y, width, height, radius, corners, Fill)
        // Add Logo inside the colored box
        $this->Image('User-Logo.png', 10, 10, 46); // (x, y, width)
        $this->SetFont('helvetica', 'B', 14);
        $this->Line(10, 25, 290, 25); // Adjusted for portrait layout

        // Add Current Date on Top Right Corner
        $this->SetFont('helvetica', '', 10);
        $this->SetXY(240, 10); // Adjust X and Y for proper placement
        $this->Cell(40, 10, 'Date: ' . date('d-m-Y'), 0, 0, 'R');
    }

    // Custom Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cp_assignment";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "
    SELECT 
        CAST(r.date AS DATE) AS date,
        r.star, 
        u.username, 
        r.review, 
        CASE 
            WHEN r.pickup_request_id IS NOT NULL THEN 'Pickup' 
            WHEN r.dropoff_id IS NOT NULL THEN 'Dropoff' 
            ELSE 'Unknown' 
        END AS service_type
    FROM review r
    LEFT JOIN pickup_request p ON r.pickup_request_id = p.pickup_request_id
    LEFT JOIN dropoff d ON r.dropoff_id = d.dropoff_id
    LEFT JOIN user u ON (p.user_id = u.user_id OR d.user_id = u.user_id)
    ORDER BY r.date DESC;
";


$result = $conn->query($query);

// Create new PDF document using TCPDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Review Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage('l');

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Review Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(30, 10, 'Date', 1, 0, 'C'); // Date
$pdf->Cell(50, 10, 'User Name', 1, 0, 'C');
$pdf->Cell(20, 10, 'Star', 1, 0, 'C');
$pdf->Cell(100, 10, 'Review', 1, 0, 'C'); // Wider for review text
$pdf->Cell(30, 10, 'Service Type', 1, 1, 'C'); // Pickup or Dropoff

$pdf->SetFont('helvetica', '', 10);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['date'], 1, 0, 'C'); // Date
    $pdf->Cell(50, 10, $row['username'], 1, 0, 'C'); // User Name
    $pdf->Cell(20, 10, $row['star'], 1, 0, 'C'); // Star rating
    $pdf->Cell(100, 10, "   " . $row['review'], 1, 0, 'L');
    $pdf->Cell(30, 10, $row['service_type'], 1, 1, 'C'); // Pickup or Dropoff
}



// Output PDF
$pdf->Output('user_points.pdf', 'I');

$conn->close();
?>
