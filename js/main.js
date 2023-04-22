const popupLinks = document.querySelectorAll('.popup-link');
const body = document.querySelector('body');
const lockPadding = document.querySelectorAll(".lock-padding");

let unlock = true;

const timeout = 800;

if (popupLinks.length > 0) {
	for (let index = 0; index < popupLinks.length; index++) {
		const popupLink = popupLinks[index];
		popupLink.addEventListener("click", function (e) {
			e.preventDefault();
			const popupName = popupLink.getAttribute('data-target');
			const curentPopup = document.querySelector(popupName);
			popupOpen(curentPopup);
		});
	}
}

if (popupLinks.length > 0) {
	for (let index = 0; index < popupLinks.length; index++) {
		const popupLink = popupLinks[index];
		popupLink.addEventListener("click", function (e) {
			e.preventDefault();
			const hrefValue = popupLink.getAttribute('href');
			if (hrefValue) {
				const popupName = hrefValue.replace('#', '');
				const curentPopup = document.getElementById(popupName);
				popupOpen(curentPopup);
			}
		});
	}
}

const popupCloseIcon = document.querySelectorAll('.close-popup');
if (popupCloseIcon.length > 0) {
	for (let index = 0; index < popupCloseIcon.length; index++) {
		const el = popupCloseIcon[index];
		el.addEventListener('click', function (e) {
			popupClose(el.closest('.popup'));
			e.preventDefault();
		})
	}
}
function popupOpen(curentPopup) {
	if (curentPopup && unlock) {
		const popupActive = document.querySelector('.popup.open');
		if (popupActive) {
			popupClose(popupActive, false);
		} else {
			bodyLock();
		}
		curentPopup.classList.add('open');
		curentPopup.addEventListener("click", function (e) {
			if (!e.target.closest('.popup__content')) {
				popupClose(e.target.closest('.popup'));
			}
		});
	}
}
function popupClose(popupActive, doUnlock = true) {
	if (unlock) {
		popupActive.classList.remove('open');
		if (doUnlock) {
			bodyUnlock();
		}
	}
}

function bodyLock() {
	const lockPaddingValue = window.innerWidth - document.documentElement.clientWidth + 'px';


	if (lockPadding.length > 0) {
		for (let index = 0; index < lockPadding.length; index++) {
			const el = lockPadding[index];
			el.style.paddingRight = lockPaddingValue;
		}
	}
	body.style.paddingRight = lockPaddingValue;
	body.classList.add('lock');

	unlock = false;
	setTimeout(function () {
		unlock = true;
	}, timeout);
}

function bodyUnlock() {
	setTimeout(function () {
		if (lockPadding.length > 0) {
			for (let index = 0; index < lockPadding.length; index++) {
				const el = lockPadding[index];
				el.style.paddingRight = '0px';
			}
		}
		body.style.paddingRight = '0px';
		body.classList.remove('lock');
	}, timeout);
	unlock = false;
	setTimeout(function () {
		unlock = true;
	}, timeout);
}

document.addEventListener('keydown', function (e) {
	if (e.which === 27) {
		const popupActive = document.querySelector('.popup.open');
		popupClose(popupActive);
	}
});

(function () {
	if (!Element.prototype.closest) {
		Element.prototype.closest = function (css) {
			var node = this;
			while (node) {
				if (node.matches(css)) return node;
				else node = node.parentElement;
			}
			return null;
		};
	}
})();
(function () {
	if (!Element.prototype.matches) {
		Element.prototype.matches = Element.prototype.matchesSelector ||
			Element.prototype.webkitMatchesSelector ||
			Element.prototype.mozMatchesSelector ||
			Element.prototype.msMatchesSelector;
	}
})();
//переміщення показника
document.addEventListener('DOMContentLoaded', function () {
	var openDoMetersButton = document.querySelector('button[name="open_do_meters"]');
	var inputNewMeter = document.querySelector('input[name="new_meter"]');
	var inputDoMetersSave = document.querySelector('input[name="do_meters_save"]');

	openDoMetersButton.addEventListener('click', function () {
		inputNewMeter.value = inputDoMetersSave.value;
	});
});
//пагінація
function getUrlParameter(url, parameter) {
	parameter = parameter.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
	var regex = new RegExp('[\\?|&]' + parameter.toLowerCase() + '=([^&#]*)');
	var results = regex.exec('?' + url.toLowerCase().split('?')[1]);
	return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ''));
}

function setUrlParameter(url, key, value) {
	var baseUrl = url.split('?')[0],
		urlQueryString = '?' + url.split('?')[1],
		newParams = key + '=' + value,
		params = '?' + newParams;

	if (urlQueryString) {
		var updateRegex = new RegExp('([\?&])' + key + '[^&]*');
		var removeRegex = new RegExp('([\?&])' + key + '=[^&;]+[&;]?');

		if (typeof value === 'undefined' || value === null || value === '') {
			params = urlQueryString.replace(removeRegex, "$1");
			params = params.replace(/[&;]$/, "");
		} else if (urlQueryString.match(updateRegex) !== null) {
			params = urlQueryString.replace(updateRegex, "$1" + newParams);
		} else {
			params = urlQueryString + '&' + newParams;
		}
	}

	params = params === '?' ? '' : params;

	return baseUrl + params;
}
// додавання класу active до li
var searchParams = new URLSearchParams(window.location.search);
var currentPage = searchParams.has('page') ? parseInt(searchParams.get('page')) : 1;

// Set the active class based on the current page
var pageItems = document.querySelectorAll('#page-select .page-item');
pageItems.forEach(function (pageItem) {
	var pageValue = parseInt(pageItem.dataset.value); // Access the data-value attribute
	if (pageValue === currentPage) {
		pageItem.classList.add('active');
	} else {
		pageItem.classList.remove('active');
	}
});
// Helper function to set URL parameter
function setUrlParameter(url, key, value) {
	var searchParams = new URLSearchParams(new URL(url).search);
	searchParams.set(key, value);
	return url.split('?')[0] + '?' + searchParams.toString();
}
// Remember the last opened tab using localStorage
document.addEventListener('DOMContentLoaded', function () {
	let lastTab = localStorage.getItem("activeTab");
	if (lastTab) {
		let lastTabElement = document.getElementById(lastTab);
		let lastTabButton = document.querySelector(`button[data-tab="${lastTab}"]`);

		if (lastTabElement) {
			lastTabElement.classList.add("show");
		}

		if (lastTabButton) {
			lastTabButton.classList.add("active");
		}
	} else {
		document.querySelector(".tabcontent").classList.add("show");
		document.querySelector(".tablinks").classList.add("active");
	}
});
function clickPageItem() {
	const activePageItem = document.querySelector('#page-select .page-item.active');
	if (activePageItem) {
		activePageItem.querySelector('a.page-link').click();
	}
}
function openTab(evt, tabName) {
	// Remove active classes from all buttons and tab contents
	document.querySelectorAll(".tablinks").forEach(btn => btn.classList.remove("active"));
	document.querySelectorAll(".tabcontent").forEach(tab => tab.classList.remove("show"));

	// Show the selected tab content and mark the button as active
	document.getElementById(tabName).classList.add("show");
	evt.currentTarget.classList.add("active");

	// Save the active tab to localStorage
	localStorage.setItem("activeTab", tabName);
	if (tabName === 'tab2') {
		clickPageItem();
	}
	if (tabName === 'tab3') {
		const currentYear = new Date().getFullYear();
		document.getElementById("year-select_profit").value = currentYear;
		fetchProfitData(currentYear);
	}
}


let histogramChart = null;
//таблиця показників
const fetchMetersTableData = async (page, year = new Date().getFullYear()) => {
	const yearParam = year ? `&year=${year}` : '';
	const response = await fetch(`fetch_data.php?page=${page}${yearParam}`);

	let data;
	try {
		data = await response.json();
	} catch (error) {
		console.error('Error parsing JSON response:', error);
		return;
	}

	const tableRow = document.getElementById('table-row');
	tableRow.innerHTML = '';

	data.forEach(row => {
		const tr = document.createElement('tr');

		const counterCell = document.createElement('td');
		counterCell.textContent = row.counter;
		tr.appendChild(counterCell);

		const dateCell = document.createElement('td');
		dateCell.textContent = row.date;
		tr.appendChild(dateCell);

		const metersCell = document.createElement('td');
		metersCell.textContent = row.meters;
		tr.appendChild(metersCell);

		const sourceCell = document.createElement('td');
		sourceCell.textContent = row.source;
		tr.appendChild(sourceCell);

		tableRow.appendChild(tr);
	});

	if (histogramChart) {
		histogramChart.destroy();
	}
	histogramChart = renderHistogram(data);
};
document.addEventListener("DOMContentLoaded", function () {
	const currentYear = new Date().getFullYear();
	document.getElementById("year-select").value = currentYear;
	fetchMetersTableData(currentPage, currentYear);
});
// Fetch table data on page load
document.addEventListener("DOMContentLoaded", function () {
	fetchMetersTableData(currentPage, document.getElementById("year-select").value);
});

// Update table data when the year select changes
document.getElementById("year-select").addEventListener("change", function (event) {
	var year = event.target.value;
	fetchMetersTableData(1, year);
});

//
document.getElementById("page-select").addEventListener("click", async function (event) {
	event.preventDefault();
	var target = event.target;

	if (target.tagName === "A" && target.classList.contains("page-link")) {
		var page = target.parentNode.dataset.value; // Access the data-value attribute
		var year = document.getElementById("year-select").value;
		pageItems.forEach(function (pageItem) {
			pageItem.classList.remove("active");
		});

		// Add 'active' class to the clicked page-item
		target.parentNode.classList.add("active");

		// Fetch and update the table data
		await fetchMetersTableData(page, year);
	}
});

//діаграма
const renderHistogram = (data) => {
	data.sort((a, b) => parseInt(a.meters) - parseInt(b.meters));
	const histogramElement = document.getElementById('histogram');
	const histogramContext = histogramElement.getContext('2d');

	const labels = data.map(row => row.date);
	const values = data.map(row => parseInt(row.meters));

	const chart = new Chart(histogramContext, {
		type: 'bar',
		data: {
			labels: labels,
			datasets: [{
				label: 'Показник',
				data: values,
				backgroundColor: '#378FD4'
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});

	return chart;
};





