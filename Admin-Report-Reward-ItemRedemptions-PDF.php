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
        MONTH(rr.datetimecomplete) AS month,
        r.reward_name,
        COUNT(rr.redeem_reward_id) AS quantity
    FROM redeem_reward rr
    JOIN reward r ON rr.reward_id = r.reward_id
    WHERE 
        rr.status = 'Redeemed' 
        AND YEAR(rr.datetimecomplete) = $selected_year
    GROUP BY MONTH(rr.datetimecomplete), r.reward_name
    ORDER BY month, r.reward_name;
";

$result = $conn->query($query);

// Create new PDF document using MYPDF class
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Item Redemptions Report');
$pdf->SetMargins(10, 30, 10);
$pdf->AddPage();

// Add Report Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Item Redemptions Report', 0, 1, 'C');

// Table Headers
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(50, 10, 'Month', 1, 0, 'C');
$pdf->Cell(80, 10, 'Reward Name', 1, 0, 'C');
$pdf->Cell(30, 10, 'Quantity', 1, 1, 'C');

// Loop through result
while ($row = $result->fetch_assoc()) {
    $monthName = date('F', mktime(0, 0, 0, $row['month'], 1));
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(50, 10, $monthName, 1, 0, 'C');
    $pdf->Cell(80, 10, $row['reward_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['quantity'], 1, 1, 'C');
}

// Output PDF
$pdf->Output('item_redemptions_report.pdf', 'I');
$conn->close();
?>