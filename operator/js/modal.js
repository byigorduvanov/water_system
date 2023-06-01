function showEditModal(id, title, content, publish_date, image) {
	document.getElementById('edit_id').value = id;
	document.getElementById('edit_title').value = title;
	document.getElementById('edit_content').value = content;
	document.getElementById('edit_publish_date').value = publish_date;
	document.getElementById('current_image').src = '../img/news/' + image;
	$('#editNewsModal').modal('show');
}


function confirmDelete(id) {
	if (confirm('Are you sure you want to delete this news?')) {
		// send a POST request to operator.php
		fetch('/index.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded'
			},
			body: 'delete_news=true&id=' + id
		}).then(function () {
			location.reload();
		});
	}
}
