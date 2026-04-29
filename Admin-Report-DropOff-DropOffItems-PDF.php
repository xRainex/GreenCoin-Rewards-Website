<?php
require_once('tcpdf/TCPDF-main/tcpdf.php'); // Ensure correct path to TCPDF

class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFillColor(120, 162, 76); // #78A24C (RGB)
        $this->RoundedRect(8, 8, 50, 12, 5, '1111', 'F');
        $this->Image('User-Logo.png', 10, 10, 46);
        $this->SetFont('helvetica', 'B', 14);
        $this->Line(10, 25, 200, 25);
    }

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
        YEAR(d.dropoff_date) AS year,
        MONTHNAME(d.dropoff_date) AS month, 
        l.location_name, 
        i.item_name, 
        id.quantity
    FROM dropoff d
    JOIN location l ON d.location_id = l.location_id
    JOIN item_dropoff id ON d.dropoff_id = id.dropoff_id
    JOIN item i ON id.item_id = i.item_id
    WHERE status = 'Completed'
    ORDER BY d.dropoff_date;
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
$pdf->Cell(0, 10, 'Drop-Off Items Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 10, 'Drop-Off Date', 1, 0, 'C');
$pdf->Cell(60, 10, 'Location Name', 1, 0, 'C');
$pdf->Cell(40, 10, 'Item Name', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 1, 'C'); // Fixed alignment (1,1 moves to new line)

// Track current month
$currentMonth = '';

while ($row = $result->fetch_assoc()) {
    if ($currentMonth != $row['month']) {
        $currentMonth = $row['month'];
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Month: ' . $row['month'] . ' ' . $row['year'], 0, 1, 'L');
    }

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(40, 10, $row['year'] . '-' . $row['month'], 1, 0, 'C'); 
    $pdf->Cell(60, 10, $row['location_name'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['item_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['quantity'], 1, 1, 'C'); // 1,1 moves to next row
}

// Output PDF
$pdf->Output('dropoff_report.pdf', 'I'); // Change 'D' to 'I' for preview

$conn->close();
?>
