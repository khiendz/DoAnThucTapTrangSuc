<!DOCTYPE html>
<html lang="en">
<?php include 'header.php' ?>
<body class="hold-transition register-page">
<div class=" col-lg-7">
  <div class="register-logo">
    <a href="#"><b>Trang sức vàng bạc Cường Tười</b>Shop</a>
  </div>
<?php session_start() ?>
<?php include('admin/db_connect.php'); ?>
<?php 
if(isset($_SESSION['login_id'])){
	$qry = $conn->query("SELECT * from users where id = {$_SESSION['login_id']} ");
	foreach($qry->fetch_array() as $k => $v){
		$$k = $v;
	}
}
?>
  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg"><?php echo !isset($id) ? 'Create Account' : 'Manage Account'; ?></p>
      <form id="manage-signup">
      	<input type="hidden" value="<?php echo isset($id) ? $id : '' ?>" name="id">
	      <div class="col-md-12">
	      	<div class="row">
	      		<div class="col-md-6 border-right">
      				<div class="input-group mb-3">
			          <input type="text" class="form-control" name="firstname" required placeholder="Họ" value="<?php echo isset($firstname) ? $firstname : '' ?>">
			          <div class="input-group-append">
			            <div class="input-group-text">
			              <span class="fas fa-user"></span>
			            </div>
			          </div>
			        </div>
			        <div class="input-group mb-3">
			          <input type="text" class="form-control" name="lastname" required placeholder="Tên" value="<?php echo isset($lastname) ? $lastname : '' ?>">
			          <div class="input-group-append">
			            <div class="input-group-text">
			              <span class="fas fa-user"></span>
			            </div>
			          </div>
			        </div>
			        <div class="input-group mb-3">
			          <input type="text" class="form-control" name="contact" required placeholder="Số điện thoại" value="<?php echo isset($contact) ? $contact : '' ?>">
			          <div class="input-group-append">
			            <div class="input-group-text">
			              <span class="fas fa-mobile"></span>
			            </div>
			          </div>
			        </div>
			        <div class="mb-3">
			          <textarea cols="30" rows="3" class="form-control" name="address" required placeholder="Địa chỉ"><?php echo isset($address) ? $address : '' ?></textarea>
			        </div>
	      		</div>
	      		<div class="col-md-6">
	      			<div class="input-group mb-3">
			          <input type="email" class="form-control" name="email" required="" placeholder="Email" value="<?php echo isset($email) ? $email : '' ?>">
			          <div class="input-group-append">
			            <div class="input-group-text">
			              <span class="fas fa-envelope"></span>
			            </div>
			          </div>
			        </div>
			        <small id="msg"></small>
			        <div class="input-group mb-3">
			          <input type="password" class="form-control" name="password" <?php echo isset($id) ? '' : "required" ?> placeholder="Mật khẩu">
			          <div class="input-group-append">
			            <div class="input-group-text">
			              <span class="fas fa-lock"></span>
			            </div>
			          </div>
			        </div>
			        <?php if(isset($id)): ?>
						<small><i>Để trống trường này nếu bạn không muốn thay đổi mật khẩu của mình.</i></small>
					<?php endif; ?>
			        <div class="input-group mb-3">
			          <input type="password" class="form-control" name="cpass" <?php echo isset($id) ? '' : "required" ?> placeholder="Xác nhận mật khẩu">
			          <div class="input-group-append">
			            <div class="input-group-text">
			              <span class="fas fa-lock"></span>
			            </div>
			          </div>
			        </div>
					<small id="pass_match" data-status=''></small>

	      		</div>
	      	</div>
	      </div>
        <div class="row">
          <div class="col-8">
	        <?php if(!isset($id)): ?>

            <div class="icheck-primary">
              <input type="checkbox" id="agreeTerms" name="terms" value="agree">
              <label for="agreeTerms">
               Đồng ý với <a href="#">điều khoản</a>
              </label>
            </div>
			<?php endif; ?>

          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block"><?php echo !isset($id) ? 'Đăng ký' : 'Cập nhật tài khoản'; ?></button>
          </div>
          <!-- /.col -->
        </div>
      </form>

	        <?php if(!isset($id)): ?>
      <a href="login.php" class="text-center">Đã có tài khoản</a>
			<?php endif; ?>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->
<script>
	$(document).ready(function(){
	
	$('#manage-signup').submit(function(e){
		debugger
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if($('#pass_match').attr('data-status') != 1){
			if($("[name='password']").val() !=''){
				$('[name="password"],[name="cpass"]').addClass("border-danger")
				end_load()
				return false;
			}
		}
		$.ajax({
			url:'admin/ajax.php?action=signup',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast('Data successfully saved.',"success");
					setTimeout(function(){
						location.replace('index.php?page=home')
					},750)
				}else if(resp == 2){
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			},error:function(resp)
			{
				console.log(resp);
			}
		})
	})
		$('[name="password"],[name="cpass"]').keyup(function(){
			var pass = $('[name="password"]').val()
			var cpass = $('[name="cpass"]').val()
			if(cpass == '' ||pass == ''){
				$('#pass_match').attr('data-status','')
			}else{
				if(cpass == pass){
					$('#pass_match').attr('data-status','1').html('<i class="text-success">Khớp mật khẩu.</i>')
				}else{
					$('#pass_match').attr('data-status','2').html('<i class="text-danger">Mật khẩu không khớp.</i>')
				}
			}
		})
	})

</script>
<?php include 'footer.php' ?>

</body>
</html>
