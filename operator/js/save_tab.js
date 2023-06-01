$(function() {
	// Get the active tab from localStorage
	let activeTab = localStorage.getItem('activeTab');
	
	if (activeTab) {
		 // Show the saved active tab
		 $('#myTab a[href="' + activeTab + '"]').tab('show');
	}

	// Set the activeTab in localStorage when a tab is shown
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		 localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
});