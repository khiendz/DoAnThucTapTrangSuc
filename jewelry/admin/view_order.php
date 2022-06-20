<?php include 'db_connect.php' ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>
<style type="text/css">
	.img-field {
		width: calc(25%);
		max-height: 25vh;
		overflow: hidden;
		display: flex;
		justify-content: center
	}

	.detail-field {
		width: calc(50%);
	}

	.amount-field {
		width: calc(25%);
		text-align: right;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.img-field img {
		max-width: 100%;
		max-height: 100%;
	}

	.qty-input {
		width: 75px;
		text-align: center;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
</style>
<div id="cover">
	<div id="bill" style="display: none">
		<h1 style="margin-left: 400px;">Hóa đơn</h1>
		<h3>Trang sức vàng bạc Cường Tươi</h3>
		<p>Ngày đặt hàng: <?php echo $_GET['date'] ?></p>
		<p>Địa chỉ: <?php echo $_GET['adress'] ?></p>
		<p>Mã đơn hàng: <?php echo $_GET['ref_id'] ?></p>
	</div>
	<div class="col-lg-12" id="download">
		<?php
		$qry = $conn->query("SELECT o.*,p.item_code,p.name as pname FROM order_items o inner join products p on p.id = o.product_id where o.order_id ={$_GET['id']}");
		$total = 0;
		?>
		<div class="row">
			<div class="col-md-8">
				<script>
					const count = '<?php echo $qry->num_rows ?>'
				</script>
				<?php if ($qry->num_rows > 0) : ?>
					<ul class="list-group">
						<?php
						while ($row = $qry->fetch_array()) :
							$total += $row['qty'] * $row['price'];
							$size = $conn->query("SELECT * FROM sizes where id = {$row['size_id']}");
							$size = $size->num_rows > 0 ? $size->fetch_array()['size'] : 'N/A';
							$colour = $conn->query("SELECT * FROM colours where id = {$row['colour_id']}");
							$colour = $colour->num_rows > 0 ? $colour->fetch_array()['color'] : 'N/A';
							$img = array();
							if (isset($row['item_code']) && !empty($row['item_code'])) :
								if (is_dir('../assets/uploads/products/' . $row['item_code'])) :
									$_fs = scandir('../assets/uploads/products/' . $row['item_code']);
									foreach ($_fs as $k => $v) :
										if (is_file('../assets/uploads/products/' . $row['item_code'] . '/' . $v) && !in_array($v, array('.', '..'))) :
											$img[] = '../assets/uploads/products/' . $row['item_code'] . '/' . $v;
										endif;
									endforeach;
								endif;
							endif;
						?>
							<li class="list-group-item" data-id="<?php echo $row['id'] ?>" data-price="<?php echo $row['price'] ?>">
								<div class="d-flex w-100">
									<div class="img-field mr-4 img-thumbnail rounded">
										<img src="<?php echo isset($img[0]) ? $img[0] : '' ?>" alt="" class="img-fluid rounded">
									</div>
									<div class="detail-field">
										<p>Tên sản phẩm: <b id="name"><?php echo $row['pname'] ?></b></p>
										<p>Giá: <b id="price"><?php echo number_format($row['price'], 2) ?></b></p>
										<p>Kích cỡ: <b id="size"><?php echo $size ?></b></p>
										<p>Màu sắc: <b id="colour"><?php echo $colour ?></b></p>
										<p>QTY: <b id="qty"><?php echo number_format($row['qty']) ?></b></p>
									</div>
									<div class="amount-field">
										<b class="amount"><?php echo number_format($row['qty'] * $row['price'], 2) ?></b>
									</div>
								</div>
							</li>
						<?php endwhile; ?>
					</ul>
				<?php else : ?>
					<center><b>Không có đơn hàng</b></center>
				<?php endif; ?>
			</div>
			<div class="col-md-4" id="amount">
				<div class="card mb-4">
					<div class="card-header bg-primary text-white"><b>Tổng cộng</b></div>
					<div class="card-body">
						<h4 class="text-right"><b id="tamount"><?php echo number_format($total, 2) ?></b></h4>
					</div>
				</div>
			</div>

		</div>
		<div id="chuKy" style="display: none; width: 927px;">
			<br>
			<br>
			<p>Tổng giá đơn hàng: <?php echo number_format($total, 2) ?></p>
			<div style="display: flex; justify-content: space-around;">
				<p>Chữ ký người mua hàng: </p>
				<p>Chữ ký người bán hàng: </p>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer display p-0 m-0">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
	<button type="button" class="btn btn-primary btn-flat download_order" onclick="download()">
		<i class="fas fa-download"></i>
	</button>
</div>
<style>
	#uni_modal .modal-footer {
		display: none
	}

	#uni_modal .modal-footer.display {
		display: flex
	}
</style>
<script>
	function download() {
		console.log('<?php echo $_GET['date'] ?>');
		var elementHTML = document.getElementById('cover');
		var download = document.getElementById('download');
		var bill = document.getElementById('bill');
		bill.style.display = "";
		bill.style.lineHeight = "30px";
		bill.style.fontSize = "25px";
		var chuKy = document.getElementById('chuKy');
		chuKy.style.display = "";
		chuKy.style.lineHeight = "30px";
		chuKy.style.fontSize = "25px";
		var amount = document.getElementById('amount');
		amount.style.display = "none";
		elementHTML.appendChild(download);
		elementHTML.style.height = 1800;
		elementHTML.style.color = "Red";
		var tagBr = document.createElement("span");
		tagBr.innerHTML = "<br/>";
		html2canvas(elementHTML, {
			useCORS: true,
			onrendered: function(canvas) {
				var pdf = new jsPDF('p', 'pt', 'letter');
				var pageHeight = 1080;
				var pageWidth = 900;
				for (var i = 0; i <= elementHTML.clientHeight / pageHeight; i++) {
					var srcImg = canvas;
					var sX = 0;
					var sY = pageHeight * i; // start 1 pageHeight down for every new page
					var sWidth = pageWidth;
					var sHeight = pageHeight;
					var dX = 0;
					var dY = 0;
					var dWidth = pageWidth;
					var dHeight = pageHeight;

					window.onePageCanvas = document.createElement("canvas");
					onePageCanvas.setAttribute('width', pageWidth);
					onePageCanvas.setAttribute('height', pageHeight);
					var ctx = onePageCanvas.getContext('2d');
					ctx.drawImage(srcImg, sX, sY, sWidth, sHeight, dX, dY, dWidth, dHeight);

					var canvasDataURL = onePageCanvas.toDataURL("image/png", 1.0);
					var width = onePageCanvas.width;
					var height = onePageCanvas.clientHeight;

					if (i > 0) // if we're on anything other than the first page, add another page
						pdf.addPage(612, 864); // 8.5" x 12" in pts (inches*72)

					pdf.setPage(i + 1); // now we declare that we're working on that page
					pdf.addImage(canvasDataURL, 'PNG', 20, 40, (width * .62), (height * .62)); // add content to the page
				}

				// Save the PDF
				pdf.save('document.pdf');
			}
		});
	};
</script>