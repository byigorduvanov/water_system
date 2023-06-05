<?php
require_once 'vendor/connect.php';

if (isset($_GET['id'])) {
	$news_id = intval($_GET['id']);

	$stmt = $conn->prepare("SELECT news.id, news.title, news.content, news.image, news.publish_date, CONCAT(users.first_name, ' ', users.last_name) AS author FROM news INNER JOIN users ON news.author = users.id WHERE news.id = ?");
	$stmt->bind_param('i', $news_id);
	$stmt->execute();

	$news_item = $stmt->get_result()->fetch_assoc();
} else {
	header('Location: index.php');
	exit();
}

require_once('header.php');
?>
<section>
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="logo-mobile">
					<a href="index.php">
						<img src="img/logo.png" alt="main logo" class="">
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="news__details" data-aos="zoom-in-down">
	<div class="container">
		<div class="row">
			<div class="col">
				<h5 class="card-title register-head  mt-3">
					<?php echo htmlspecialchars($news_item['title'], ENT_QUOTES, 'UTF-8'); ?>
				</h5>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="card col-lg-10 col-sm-9 mx-auto">
					<img src="<?php echo htmlspecialchars($news_item['image'], ENT_QUOTES, 'UTF-8'); ?>"
						class="mx-auto d-block news__details-img" alt="News Image">
					<div class="card-body p-0">

						<p class="card-content register-text news__details-content text-left mt-5 ">
							<?php echo htmlspecialchars($news_item['content'], ENT_QUOTES, 'UTF-8'); ?>
						</p>
						<p class="card-text register-text text-left mt-5">
							<?php echo htmlspecialchars($news_item['publish_date'], ENT_QUOTES, 'UTF-8'); ?>
						</p>

					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
include('footer.php');
?>