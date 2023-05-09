<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

include 'connect.php';
require_once('tecnickcom\tcpdf\examples\tcpdf_include.php');

$latest_charges = isset($_SESSION['latest_charges']) ? $_SESSION['latest_charges'] : 0;
$payment_status = isset($_SESSION['payment_status']) ? $_SESSION['payment_status'] : 'unpaid';
$date_of_indicator = isset($_SESSION['date_of_indicator']) ? $_SESSION['date_of_indicator'] : '';
$personal_account = isset($_SESSION['personal_account']) ? $_SESSION['personal_account'] : '';
$number_of_meters = isset($_SESSION['number_of_meters']) ? $_SESSION['number_of_meters'] : '';
$name = isset($_SESSION['name']) ? $_SESSION['name'] : '';

/*формування квитанції*/
if (isset($_GET['generate_receipt']) && $_GET['generate_receipt'] === '1') {
	generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name);
	exit();
}

function generate_receipt($charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name)
{
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
	$html = '<h1>Payment Receipt</h1>';
	$html .= '<p>Personal Account: ' . $personal_account . '</p>';
	$html .= '<p>Name: ' . $name . '</p>';
	$html .= '<p>Number of Meters: ' . $number_of_meters . '</p>';
	$html .= '<p>Indicator: ' . $charges . '</p>';
	$html .= '<p>Date of Submission: ' . $date_of_indicator . '</p>';
	$html .= '<p>Payment Status: ' . $payment_status . '</p>';
	$html .= '<p>Amount to Pay: ' . $charges . ' грн</p>';
	// Write the HTML content to the PDF
	$pdf->writeHTML($html, true, false, true, false, '');

	// Output the PDF to the browser
	$pdf->Output('receipt.pdf', 'I');
}
?>