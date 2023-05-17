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
$difference = $indicator - $previousIndicator;


/* формування квитанції */
if (isset($_GET['generate_receipt']) && $_GET['generate_receipt'] === '1') {
	generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name, $address, $indicator, $previousIndicator, $difference);
	exit();
}

function generate_receipt($latest_charges, $payment_status, $date_of_indicator, $personal_account, $number_of_meters, $name, $address, $indicator, $previousIndicator, $difference)
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
	$tagvs = array('p' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)));
	$pdf->setHtmlVSpace($tagvs);

	// Set the content for the receipt

	// Set the content for the receipt
	$html = '<style>
            body {
                margin-top: 50px;
                margin-bottom: 50px;
            }
            /* Add more CSS styling here */
        </style>';
	$html .= '<div style="border: 1px solid black;">';
	$html .= '<h4 style="text-align: center;">Постачальник: Комунальне підприємство "Гусятин Водоканал"</h4>';
	$html .= '<p><strong>Особовий рахунок: ' . $personal_account . '</strong></p>';
	$html .= '<p><strong>Платник:' . $name . '</strong></p>';
	$html .= '<p><strong>Адреса:' . $address . '</strong></p>';
	$html .= '<p style="text-align: center;"><strong>Повідомлення про квитанцію про оплату послуг за ' . $date_of_indicator . '</strong></p>';
	$html .= '<table cellspacing="0" cellpadding="2" border="1">';
	$html .= '<tr><th>Послуги</th><th>Тариф</th><th>Дата подання показника</th><th>Статус платежу</th><th>Сума до оплатити</th></tr>';
	$html .= '<tr>';
	$html .= '<td>Водопостачання</td>';
	$html .= '<td>32.5 грн 1 кубометр води</td>';
	$html .= '<td>' . $date_of_indicator . '</td>';
	$html .= '<td>' . $payment_status . '</td>';
	$html .= '<td>' . $latest_charges . ' грн</td>';
	$html .= '</tr>';
	$html .= '</table>';

	$html .= '<p><strong>Показники лічильника</strong></p>';
	$html .= '<table cellspacing="0" cellpadding="2" border="1">';
	$html .= '<tr><th>Послуги</th><th>Тариф</th><th>Лічильник</th><th>Поточний показник</th><th>Попередній показник</th><th>Різниця</th><th>Сума до оплатити</th></tr>';
	$html .= '<tr>';
	$html .= '<td>Водопостачання</td>';
	$html .= '<td>32.5 грн 1 кубометр води</td>';
	$html .= '<td>' . $number_of_meters . '</td>';
	$html .= '<td>' . $indicator . '</td>';
	$html .= '<td>' . $previousIndicator . '</td>';
	$html .= '<td>' . $difference . '</td>';
	$html .= '<td>' . $latest_charges . ' грн</td>';
	$html .= '</tr>';
	$html .= '</table>';
	$html .= '<p><strong>Останній термін сплати за за надані послуги до 28 числа поточного місяця. Показники лічильника записувати в правому нижньому куті квитанції. Передавати показники за тел. 2-65-44 до 28 числа поточного місяця</strong></p>';
	$html .= '</div>';


	// Write the HTML content to the PDF
	$pdf->writeHTML($html, true, false, true, false, '');
	// Output the PDF to the browser
	$pdf->Output('receipt.pdf', 'I');
}
?>