document.addEventListener("DOMContentLoaded", function () {
	const yearSelect = document.getElementById("year-select_profit");
	if (yearSelect) {
		yearSelect.addEventListener("change", (event) => {
			const selectedYear = event.target.value;
			fetchProfitData(selectedYear);
		});
	}

	const currentYear = new Date().getFullYear();
	document.getElementById("year-select_profit").value = currentYear;
	fetchProfitData(currentYear);
});

const fetchProfitData = async (year = new Date().getFullYear()) => {
	const response = await fetch(`fetch_profit.php?year=${year}`);
	let data;
	try {
		data = await response.json();
	} catch (error) {
		console.error("Error parsing JSON response:", error);
		return;
	}

	populateTable(data);
	renderChart(data);
};

function populateTable(data) {
	const tableBody = document.querySelector("#tab3 table tbody");
	tableBody.innerHTML = "";
	data.forEach((row) => {
		const newRow = tableBody.insertRow();

		const dateCell = newRow.insertCell();
		dateCell.textContent = row.date;
		const newCell1 = newRow.insertCell();
		newCell1.textContent = '';
		const priceCell = newRow.insertCell();
		priceCell.textContent = row.price_for_month;
		const newCell2 = newRow.insertCell();
		newCell2.textContent = '';
	});
}


// Add this line before the fetchProfitData function
let profitChartInstance;

function renderChart(data) {
	const labels = data.map((row) => row.date);
	const prices = data.map((row) => row.price_for_month);

	const ctx = document.getElementById("profitChart").getContext("2d");

	// Destroy the previous chart instance if it exists
	if (profitChartInstance) {
		profitChartInstance.destroy();
	}

	profitChartInstance = new Chart(ctx, {
		type: "line",
		data: {
			labels: labels,
			datasets: [
				{
					label: "Нарахування за місяць",
					data: prices,
					borderColor: "rgba(55, 144, 212, 0.71)",
					backgroundColor: "rgba(75, 192, 192, 0.2)",
				},
			],
		},
		options: {
			scales: {
				x: {
					type: "time",
					time: {
						unit: "year",
					},
				},
			},
		},
	});
}


document.getElementById("year-select_profit").addEventListener("change", function (event) {
	var year = event.target.value;
	fetchProfitData(year);
});

