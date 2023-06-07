document.addEventListener("DOMContentLoaded", function () {
	const yearSelect = document.getElementById("year-select_pay");
	if (yearSelect) {
		yearSelect.addEventListener("change", (event) => {
			const selectedYear = event.target.value;
			fetchPay(selectedYear);
		});
	}

	const currentYear = new Date().getFullYear();
	document.getElementById("year-select_pay").value = currentYear;
	fetchPay(currentYear);
});

const fetchPay = async (year = new Date().getFullYear()) => {
	const response = await fetch(`fetch_pay.php?year=${year}`);
	let data;
	try {
		data = await response.json();
	} catch (error) {
		console.error("Error parsing JSON response:", error);
		return;
	}

	PayTable(data);
};

function PayTable(data) {
	const tableBody = document.querySelector("#tab4 table tbody");
	tableBody.innerHTML = "";
	let previousDebt = 0;
	data.forEach((row) => {
		const newRow = tableBody.insertRow();

		const dateCell = newRow.insertCell();
		dateCell.textContent = row.date;
		const priceCell = newRow.insertCell();
		priceCell.textContent = row.price_for_month;
		const statusCell = newRow.insertCell();

		// Перевірте, чи має платіж_статус "прострочений" або "не сплачений
		if (row.payment_status === 'прострочений' || row.payment_status === 'не сплачено') {
			const receiptButton = document.createElement("a");
			receiptButton.href = "?generate_receipt=1";
			receiptButton.target = "_blank";
			receiptButton.textContent = "Сформувати квитанцію";
			receiptButton.classList.add("btn", "btn-primary", "my_btn", "generate__receipt");
			statusCell.appendChild(receiptButton);

			const onlinePaymentButton = document.createElement("a");
			onlinePaymentButton.href = "https://www.privat24.ua/rd/send_qr/liqpay_static_qr/payment_2323840170.f81b2253adeb1439d4a985d00f8ce2d4806a7e02686c9ac46c6ca9dacd8f2604";
			onlinePaymentButton.textContent = "Оплата";
			onlinePaymentButton.target = "_blank";
			onlinePaymentButton.classList.add("btn", "btn-primary", "my_btn", "generate__receipt");
			statusCell.appendChild(onlinePaymentButton);
		} else {
			statusCell.textContent = row.payment_status;
		}
	});
}


document.getElementById("year-select_pay").addEventListener("change", function (event) {
	var year = event.target.value;
	fetchPay(year);
});