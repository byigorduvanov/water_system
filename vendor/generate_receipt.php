<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

include 'connect.php';
require_once('tecnickcom\tcpdf\examples\tcpdf_include.php');

$id = $_SESSION["id"]; // Retrieve the user's ID from session

// Query the database for the user's information
$userQuery = "SELECT * FROM users WHERE id = $id";
$userResult = mysqli_query($conn, $userQuery);
$userRow = mysqli_fetch_assoc($userResult);

// Query the database for the user's meter information	
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
		$previousIndicator = $currentMeterRow['meters']; // Default to the current meter reading
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

/* формування квитанції */
if (isset($_GET['generate_receipt']) && $_GET['generate_receipt'] === '1') {
	generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name, $address, $indicator, $previousIndicator);
	exit();
}

function generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name, $address, $indicator, $previousIndicator)
{
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetFont('dejavusans', '', 10);

	// Remove default header and footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// Set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Your Company');
	$pdf->SetTitle('Receipt');
	$pdf->SetSubject('Payment Receipt');

	// Set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// Set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

	// Set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// Add a page
	$pdf->AddPage();

	// Set the content for the receipt

	$html = '<h1>Квитанція про оплату</h1>';
	$html .= '<table cellspacing="0" cellpadding="2" border="1">';
	$html .= '<tr><th>Особовий рахунок:</th><td>' . $personal_account . '</td></tr>';
	$html .= '<tr><th>Платник:</th><td>' . $name . '</td></tr>';
	$html .= '<tr><th>Адреса:</th><td>' . $address . '</td></tr>';
	$html .= '<tr><th>Лічильник:</th><td>' . $number_of_meters . '</td></tr>';
	$html .= '<tr><th>Поточний показник:</th><td>' . $indicator . '</td></tr>';
	$html .= '<tr><th>Попередній показник:</th><td>' . $previousIndicator . '</td></tr>';
	$html .= '<tr><th>Дата подання показника:</th><td>' . $date_of_indicator . '</td></tr>';
	$html .= '<tr><th>Статус платежу:</th><td>' . $payment_status . '</td></tr>';
	$html .= '<tr><th>Сума до оплатити:</th><td>' . $latest_charges . ' грн</td></tr>';
	$html .= '</table>';

	// Write the HTML content to the PDF
	$pdf->writeHTML($html, true, false, true, false, '');
	// Output the PDF to the browser
	$pdf->Output('receipt.pdf', 'I');
}
?>