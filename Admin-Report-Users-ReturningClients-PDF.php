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
        $this->SetXY(240, 10);
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
        u.username,
        u.email,
        COUNT(*) AS total_services,
        MAX(latest_service_date) AS latest_service_date
    FROM (
        SELECT 
            p.user_id,
            p.datetime_submit_form AS latest_service_date
        FROM pickup_request p

        UNION ALL

        SELECT 
            d.user_id,
            d.dropoff_date AS latest_service_date
        FROM dropoff d
    ) AS all_services
    JOIN user u ON u.user_id = all_services.user_id
    GROUP BY u.user_id, u.username, u.email
    HAVING COUNT(*) > 1
    ORDER BY latest_service_date DESC;
";


$result = $conn->query($query);

// Create new PDF document using TCPDF
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Returning Clients Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage('l');

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Returning Clients Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 10, 'Username', 1, 0, 'C');
$pdf->Cell(70, 10, 'Email', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total Services', 1, 0, 'C');
$pdf->Cell(50, 10, 'Latest Service Date', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 10);

// Table Data
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, $row['username'], 1, 0, 'C');
    $pdf->Cell(70, 10, $row['email'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['total_services'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['latest_service_date'], 1, 1, 'C');
}




// Output PDF
$pdf->Output('user_points.pdf', 'I');

$conn->close();
?>
