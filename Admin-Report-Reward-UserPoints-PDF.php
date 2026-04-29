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
        $this->Line(10, 25, 200, 25); // Adjusted for portrait layout

        // Add Current Date on Top Right Corner
        $this->SetFont('helvetica', '', 10);
        $this->SetXY(160, 10); // Adjust X and Y for proper placement
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
        username,
        points
    FROM user
";

$result = $conn->query($query);

// Create new PDF document using TCPDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('User Points Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage(); // Default is Portrait

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'User Points Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(20, 10, 'Bil', 1, 0, 'C'); // Bil column added
$pdf->Cell(70, 10, 'User Name', 1, 0, 'C');
$pdf->Cell(40, 10, 'Points', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$bil = 1; // Initialize serial number

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(20, 10, $bil++, 1, 0, 'C'); // Bil column
    $pdf->Cell(70, 10, $row['username'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['points'], 1, 1, 'C');
}

// Output PDF
$pdf->Output('user_points.pdf', 'I');

$conn->close();
?>
