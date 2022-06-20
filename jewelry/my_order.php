<?php include 'admin/db_connect.php' ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>

<iframe id="pdf" type="application/pdf" src="" width="800" height="400"></iframe>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<button onclick="myFunction()"></button>
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Ngày đặt hàng</th>
						<th class="text-center">Mã đặt hàng</th>
						<th class="text-center">Địa chỉ nhận sản phẩm</th>
						<th class="text-center">Trạng thái</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$query = $conn->query("SELECT * FROM orders where user_id = '{$_SESSION['login_id']}' order by unix_timestamp(date_created)");
					while ($row = $query->fetch_assoc()) :
					?>
						<tr>
							<td class="text-center"><?php echo $i++ ?></td>
							<td class=""><?php echo date("M d, Y", strtotime($row['date_created'])) ?></td>
							<td class=""><?php echo $row['ref_id'] ?></td>
							<td class=""><?php echo $row['delivery_address'] ?></td>
							<td class="text-center">
							<span><?php if ($row['status'] == 0) : ?></span>
									<span class="badge badge-secondary">Đang vận chuyển</span>
								<?php elseif ($row['status'] == 1) : ?>
									<span class="badge badge-primary">Đã nhận hàng</span>
								<?php elseif ($row['status'] == 2) : ?>
									<span class="badge badge-info">Đã chuyển hàng</span>
								<?php elseif ($row['status'] == 3) : ?>
									<span class="badge badge-success">Đã giao hàng</span>
								<?php else : ?>
									<span class="badge badge-danger">Hủy đơn</span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<a href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-code="<?php echo $row['ref_id'] ?>" class="btn btn-primary btn-flat view_order">
									<i class="fas fa-eye"></i>Xem đơn hàng
								</a>
							<script>
								console.log(<?php echo $row ?>);
							</script>
						</tr>
					<?php endwhile; ?>
				</tbody>

			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('table').dataTable()
		$('.view_order').click(function() {
			uni_modal("My Order " + $(this).attr('data-code'), "view_order.php?id=" + $(this).attr('data-id'), "large")
		})
	})

	function myFunction() {
		var doc = new jsPDF();
		doc.text(20, 20, 'Hello world!');
		doc.text(20, 30, 'This is client-side Javascript, pumping out a PDF.');
		doc.text(20, 40, 'Do you like that?');
		doc.output('save', 'filename.pdf');
	};
	window.myFunction();

	window.onload = myFunction;
</script>