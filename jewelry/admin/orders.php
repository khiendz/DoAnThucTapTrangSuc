<?php include 'db_connect.php' ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">Ngày đặt hàng</th>
						<th class="text-center">Mã đặt hàng</th>
						<th class="text-center">Địa chỉ nhận</th>
						<th class="text-center">Trạng thái</th>
						<th class="text-center">Hành động</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$query = $conn->query("SELECT o.*,concat(u.lastname,', ',u.firstname,' ',u.middlename) as name FROM orders o inner join users u on u.id = o.user_id order by unix_timestamp(o.date_created)");
					while($row= $query->fetch_assoc()):
						$data[] = $row;
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td class=""><?php echo date("M d, Y",strtotime($row['date_created'])) ?></td>
						<td class=""><?php echo $row['ref_id'] ?></td>
						<td class=""><?php echo $row['delivery_address'] ?></td>
						<td class="text-center">
						<?php if($row['status'] == 0): ?>
								<span class="badge badge-secondary">Đang vận chuyển</span>
							<?php elseif($row['status'] == 1): ?>
								<span class="badge badge-primary">Đã nhận hàng</span>
							<?php elseif($row['status'] == 2): ?>
								<span class="badge badge-info">Đã chuyển hàng</span>
							<?php elseif($row['status'] == 3): ?>
								<span class="badge badge-success">Đã giao hàng</span>
							<?php else: ?>
								<span class="badge badge-danger">Hủy đơn</span>
							<?php endif; ?>
							<script >
								console.log('<?php $row['ref_id'] ?>');
							</script>
						</td>
						<td class="text-center">
	                         <div class="btn-group">
		                        <a <?php if($row['status'] == 4): echo 'style = "display: none"'?> <?php endif; ?> href="javascript:void(0)" class="btn btn-primary btn-flat update_order" data-id="<?php echo $row['id'] ?>" data-code="<?php echo $row['ref_id'] ?>">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                         <a href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-code="<?php echo $row['ref_id'] ?>" class="btn btn-info btn-flat view_order">
		                          <i class="fas fa-eye"></i>
								</a>
		                        <button type="button" class="btn btn-danger btn-flat delete_order" data-id="<?php echo $row['id'] ?>">
		                          <i class="fas fa-trash"></i>
		                        </button>
	                      </div>
					</tr>
					<?php endwhile; ?>
				</tbody>
				
			</table>
		</div>
	</div>
</div>
<?php
			$date = "";
			$adress = "";
			$ref_id = "";
			$i = 1;
			$query = $conn->query("SELECT o.*,concat(u.lastname,', ',u.firstname,' ',u.middlename) as name FROM orders o inner join users u on u.id = o.user_id order by unix_timestamp(o.date_created)");
			while ($row = $query->fetch_assoc()) :
				$data[] = $row;
				foreach ($data as $arr) {
					if ($arr['id'] == $_COOKIE['id']) {
						$date = date("M d, Y", strtotime($arr['date_created']));
						$adress = $arr['delivery_address'];
						$ref_id = $arr['ref_id'];
					}
				}
			?>
			<?php endwhile; ?>
<script>
	$(document).ready(function(){
		$('table').dataTable()
		$('.view_order').click(function(){
			document.cookie = "id = " + $(this).attr('data-id');
			uni_modal("Mã đơn hàng " + $(this).attr('data-code'), "view_order.php?id=" + $(this).attr('data-id') + "&date=" + '<?php echo $date ?>' + "&adress=" + '<?php echo $adress ?>' + "&ref_id=" + '<?php echo $ref_id ?>', "large")
		})
		$('.update_order').click(function(){
			uni_modal("Cập nhật đơn hàng "+$(this).attr('data-code')+' Status',"manage_order.php?id="+$(this).attr('data-id'))
		})
		$('.delete_order').click(function(){
		_conf("Are you sure to delete this order?","delete_order",[$(this).attr('data-id')])
		})
	})
	function delete_order($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_order',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>