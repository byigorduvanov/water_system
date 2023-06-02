function openEditForm(id, title, content, publish_date, image) {
	// Fill the form with data
	document.getElementById('edit_id').name = 'news_id';
	document.getElementById('edit_id').value = id;
	document.getElementById('edit_title').value = title;
	document.getElementById('edit_content').value = content;
	document.getElementById('edit_publish_date').value = publish_date;
	document.getElementById('current_image').src = '../img/news/' + image; 

	// Show the form
	document.getElementById('edit_news_card').style.display = 'block';
}
