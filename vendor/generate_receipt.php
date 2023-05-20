<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

include_once 'connect.php';
require_once('tfpdf.php');

$id = $_SESSION["id"];

$userQuery = "SELECT * FROM users WHERE id = $id";
$userResult = mysqli_query($conn, $userQuery);
$userRow = mysqli_fetch_assoc($userResult);

$stmt = $conn->prepare("SELECT meters.*, months.payment_status, months.price_for_month, months.id_meters  
                        FROM meters 
                        LEFT JOIN months ON meters.id = months.id_meters 
                        WHERE meters.id_user = ? 
                        ORDER BY meters.id DESC LIMIT 2");
$stmt->bind_param("i", $id);
$stmt->execute();
$meterResult = $stmt->get_result();

if ($meterResult !== false) {
	$currentMeterRow = mysqli_fetch_assoc($meterResult);
	$previousMeterRow = mysqli_fetch_assoc($meterResult);

	if ($previousMeterRow !== null) {
		$previousIndicator = $previousMeterRow['meters'];
	} else {
		$previousIndicator = $currentMeterRow['meters'];
	}
} else {
	die('Invalid query: ' . mysqli_error($conn));
}

$indicator = $currentMeterRow['meters'];
$previousIndicator = $previousMeterRow['meters'];
$latest_charges = $currentMeterRow['price_for_month'];
$payment_status = $currentMeterRow['payment_status'];
$date_of_indicator = $currentMeterRow['date'];
$personal_account = $userRow['personal_account'];
$number_of_meters = $userRow['counter'];
$name = $userRow['first_name'] . ' ' . $userRow['last_name'];
$address = $userRow['address'];
$difference = $indicator - $previousIndicator;

if (isset($_GET['generate_receipt']) && $_GET['generate_receipt'] === '1') {
	generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name, $address, $indicator, $previousIndicator, $difference);
	exit();
}

function generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name, $address, $indicator, $previousIndicator, $difference)
{
	$pdf = new tFPDF();
	$pdf->AddPage();
	// Add a Unicode font (uses UTF-8)
	$pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
	$pdf->SetFont('DejaVu', '', 14);

	$pdf->Cell(0, 10, 'Постачальник: Комунальне підприємство "Гусятин Водоканал"', 0, 1, 'C');


	// Continue with the rest of your receipt content...

	$pdf->Output('receipt.pdf', 'I');
}
?>