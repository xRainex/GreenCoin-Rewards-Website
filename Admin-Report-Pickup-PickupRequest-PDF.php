<?php
require_once('tcpdf/TCPDF-main/tcpdf.php'); // Ensure the correct path to TCPDF

class MYPDF extends TCPDF {
    // Custom Header with Logo
    public function Header() {
        $this->SetFillColor(120, 162, 76); // #78A24C (RGB)
        $this->RoundedRect(8, 8, 50, 12, 5, '1111', 'F'); // (x, y, width, height, radius, corners, Fill)
        // Add Logo inside the colored box
        $this->Image('User-Logo.png', 10, 10, 46); // (x, y, width)
        $this->SetFont('helvetica', 'B', 14);
        $this->Line(10, 25, 200, 25);
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

// Fetch and Group Data by Month
$query = "
    SELECT 
        DATE_FORMAT(pr.datetime_submit_form, '%Y-%m') AS month, 
        pr.datetime_submit_form AS pickup_date, 
        u.username,
        d.driver_name,
        i.item_name,
        p.quantity
    FROM pickup_request pr
    JOIN user u ON pr.user_id = u.user_id
    JOIN driver d ON pr.driver_id = d.driver_id
    JOIN item_pickup p ON pr.pickup_request_id = p.pickup_request_id
    JOIN item i ON i.item_id = p.item_id
    ORDER BY month ASC, pr.datetime_submit_form ASC
";

$result = $conn->query($query);

// Create new PDF document using MYPDF class
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Pickup Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage();

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Pickup Items Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Pickup Date', 1, 0, 'C');
$pdf->Cell(40, 10, 'User', 1, 0, 'C');
$pdf->Cell(40, 10, 'Driver', 1, 0, 'C');
$pdf->Cell(40, 10, 'Item', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 1, 'C');

// Track current month
$currentMonth = '';

while ($row = $result->fetch_assoc()) {
    if ($currentMonth != $row['month']) {
        $currentMonth = $row['month'];
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Month: ' . date('F Y', strtotime($row['month'] . '-01')), 0, 1, 'L');
    }

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 10, $row['pickup_date'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['username'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['driver_name'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['item_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['quantity'], 1, 1, 'C');
}

// Output PDF
$pdf->Output('pickup_report.pdf', 'D');

$conn->close();
?>
