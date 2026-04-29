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

$queryTable = "
    SELECT 
        MONTH(pr.datetime_submit_form) AS month_num,
        DATE_FORMAT(pr.datetime_submit_form, '%b') AS month_name,
        YEAR(pr.datetime_submit_form) AS year,
        i.item_name, 
        SUM(p.quantity) AS total_quantity
    FROM item_pickup p
    JOIN item i ON p.item_id = i.item_id
    JOIN pickup_request pr ON p.pickup_request_id = pr.pickup_request_id
    GROUP BY month_num, i.item_name
    ORDER BY month_num, total_quantity DESC
";
$resultTable = $conn->query($queryTable);
// Create new PDF document using TCPDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Pickup Items Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage();

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Pickup Items Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 10, 'Item Name', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total Quantity', 1, 1, 'C');

// Track current month
$currentMonth = '';

while ($row = $resultTable->fetch_assoc()) {
    // Combine year and month to display once per group
    $rowMonth = $row['month_name'] . ' ' . $row['year'];
    
    if ($currentMonth != $rowMonth) {
        $currentMonth = $rowMonth;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Month: ' . $currentMonth, 0, 1, 'L');
    }

    // Output item name and quantity
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(60, 10, $row['item_name'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['total_quantity'], 1, 1, 'C');
}


// Output PDF
$pdf->Output('pickup_items_report.pdf', 'I');

$conn->close();
?>
