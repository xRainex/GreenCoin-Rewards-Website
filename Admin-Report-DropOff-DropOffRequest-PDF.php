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

$query = "
    SELECT 
        DATE_FORMAT(do.dropoff_date, '%Y-%m') AS month, 
        do.dropoff_date, 
        u.username, 
        i.item_name, 
        d.quantity, 
        l.location_name
    FROM dropoff do
    LEFT JOIN user u ON do.user_id = u.user_id
    LEFT JOIN item_dropoff d ON do.dropoff_id = d.dropoff_id
    LEFT JOIN item i ON i.item_id = d.item_id
    LEFT JOIN location l ON do.location_id = l.location_id
    ORDER BY month ASC, do.dropoff_date ASC
";

$result = $conn->query($query);

// Create new PDF document using TCPDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Drop-Off Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage();

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Drop-Off Requests Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Drop-Off Date', 1, 0, 'C');
$pdf->Cell(40, 10, 'User', 1, 0, 'C');
$pdf->Cell(40, 10, 'Item', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(40, 10, 'Location', 1, 1, 'C');

// Track current month
$currentMonth = '';

while ($row = $result->fetch_assoc()) {
    if ($currentMonth != $row['month']) {
        $currentMonth = $row['month'];
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Month: ' . date('F Y', strtotime($row['month'] . '-01')), 0, 1, 'L');
    }

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 10, $row['dropoff_date'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['username'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['item_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['quantity'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['location_name'], 1, 1, 'C');
}

// Output PDF
$pdf->Output('dropoff_report.pdf', 'D');

$conn->close();
?>
