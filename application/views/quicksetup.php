<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Quick POS : Setup</title>
		<link href="<?php echo base_url('lib/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/bootstrap/css/bootstrap-theme.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/quickpos/css/pointofsale.css') ?>" rel="stylesheet">
		<script>
			var basepath = "<?php echo current_url() ?>";
		</script>
		<script src="<?php echo base_url('lib/jquery/jquery-2.1.3.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/bootstrap/js/bootstrap.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/handlebars/handlebars-v3.0.0.js') ?>"></script>
		<script src="<?php echo base_url('lib/quickpos/js/quicksetup.js') ?>"></script>
	</head>
	<body>
		
		<!-- Fixed Navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-top">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php echo site_url('quicksetup') ?>">QuickSetup</a>
				</div>
				<div id="navbar-top" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Setup <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a id="menuInfo" href="#">Store Information</a></li>
								<li><a id="menuServices" href="#">Services & Coupons</a></li>
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Main Menu <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo site_url('quickpos') ?>">Point of Sale</a></li>
								<li><a href="<?php echo site_url('quicksales') ?>">Sales</a></li>
								<li><a href="<?php echo site_url('quicksetup') ?>">Setup</a></li>
								<li><a id="menuExit" href="#">Exit</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="row">
				
				<!-- Side Navbar -->
				<div class="col-sm-3 col-md-2 sidebar hidden">
					<h4>.. loading ..</h4>
					<hr />
					<ul class="nav nav-sidebar">
					</ul>
				</div>
				
				<!-- Content Container -->
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main hidden" id="main_container"></div>
				
				<!-- Full Content Container -->
				<div class="col-sm-12 col-md-12 main" id="full_container"></div>
				
			</div>
		</div>
		
		<!-- Fixed Bottom Navbar -->
		<div id="navbar-footer" class="navbar navbar-inverse navbar-fixed-bottom hidden">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-bottom">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div id="navbar-bottom" class="navbar-collapse collapse">
				</div>
			</div>
		</div>
		
		<!-- Service List -->
		<script id="servicelist" type="text/x-handlebars-template">
			<h3>Services <a id="addgroup" href="#" class="pull-right"><small>Add Group</small></a></h3>
			<div class="panel-group">
				{{#each groups}}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent=".panel-group" href="#group{{id}}">{{name}}</a>
							<a id="add_{{id}}" href="#" class="pull-right" data-id="{{id}}"><small>Add Service</small></a>
							<a id="group_{{id}}" href="#" class="pull-right" style="margin-right: 10px;" data-id="{{id}}"><small>Edit Group</small></a>
						</h4>
					</div>
					<div id="group{{id}}" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="list-group">
								{{#each services}}
								<a id="service_{{id}}" href="#" class="list-group-item" data-id="{{id}}"><span class="badge">{{formatMoney retail}}</span>{{name}}</a>
								{{/each}}
							</div>
						</div>
					</div>
				</div>
				{{/each}}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent=".panel-group" href="#coupon">Coupons / Discounts</a>
							<a id="addcoupon" href="#" class="pull-right"><small>Add Coupon</small></a>
						</h4>
					</div>
					<div id="coupon" class="panel-collapse collapse">
						<div class="panel-body">
							<div class="list-group">
								{{#each coupons}}
								<a id="coupon_{{id}}" href="#" class="list-group-item" data-id="{{id}}"><span class="badge">{{formatMoney amount}}</span>{{name}}</a>
								{{/each}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</script>
		
		<!-- Group Edit -->
		<script id="groupedit" type="text/x-handlebars-template">
			<h3>Edit Group</h3>
			<form id="groupform" class="form-horizontal">
				<input type="hidden" id="id" name="id" value="{{group.id}}">
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Name:</label>
					<div class="col-sm-5">
						<input type="text" id="name" name="name" class="form-control" value="{{group.name}}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<button type="button" id="savebtn" class="btn btn-default">Save</button>
						<button type="button" id="cancelbtn" class="btn btn-default">Cancel</button>
					</div>
				</div>
			</form>
		</script>
		
		<!-- Service Edit -->
		<script id="serviceedit" type="text/x-handlebars-template">
			<h3>Edit Service</h3>
			<form id="serviceform" class="form-horizontal">
				<input type="hidden" id="id" name="id" value="{{service.id}}">
				<div class="form-group">
					<label for="group" class="col-sm-2 control-label">Group:</label>
					<div class="col-sm-5">
						<select id="group" name="group" class="form-control">
							{{#each groups}}
							<option value="{{id}}" {{isSelected id ../group}}>{{name}}</option>
							{{/each}}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Name:</label>
					<div class="col-sm-5">
						<input type="text" id="name" name="name" class="form-control" value="{{service.name}}">
					</div>
				</div>
				<div class="form-group">
					<label for="retail" class="col-sm-2 control-label">Retail:</label>
					<div class="col-sm-5">
						<input type="text" id="retail" name="retail" class="form-control" value="{{service.retail}}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<labe>
							<input type="checkbox" id="edit" name="edit" value="1" {{isChecked service.edit}}>
							Editable Retail Value
						</labe>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<button type="button" id="savebtn" class="btn btn-default">Save</button>
						<button type="button" id="cancelbtn" class="btn btn-default">Cancel</button>
					</div>
				</div>
			</form>
		</script>
		
		<!-- Coupon Edit -->
		<script id="couponedit" type="text/x-handlebars-template">
			<h3>Edit Coupon</h3>
			<form id="couponform" class="form-horizontal">
				<input type="hidden" id="id" name="id" value="{{coupon.id}}">
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Name:</label>
					<div class="col-sm-5">
						<input type="text" id="name" name="name" class="form-control" value="{{coupon.name}}">
					</div>
				</div>
				<div class="form-group">
					<label for="amount" class="col-sm-2 control-label">Amount:</label>
					<div class="col-sm-5">
						<input type="text" id="amount" name="amount" class="form-control" value="{{coupon.amount}}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<button type="button" id="savebtn" class="btn btn-default">Save</button>
						<button type="butt" id="cancelbtn" class="btn btn-default">Cancel</button>
					</div>
				</div>
			</form>
		</script>
		
		<!-- Store Info -->
		<script id="storeinfo" type="text/x-handlebars-template">
			<h3>Store Information</h3>
			<form id="infoform" class="form-horizontal">
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Name:</label>
					<div class="col-sm-5">
						<input type="text" id="name" name="name" class="form-control" value="{{name}}" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="address" class="col-sm-2 control-label">Address:</label>
					<div class="col-sm-5">
						<input type="text" id="address" name="address" class="form-control" value="{{address}}" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="city" class="col-sm-2 control-label">City:</label>
					<div class="col-sm-5">
						<input type="text" id="city" name="city" class="form-control" value="{{city}}" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="state" class="col-sm-2 control-label">State:</label>
					<div class="col-sm-5">
						<input type="text" id="state" name="state" class="form-control" value="{{state}}" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="postal" class="col-sm-2 control-label">Zip:</label>
					<div class="col-sm-5">
						<input type="text" id="postal" name="postal" class="form-control" value="{{postal}}" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="phone" class="col-sm-2 control-label">Phone:</label>
					<div class="col-sm-5">
						<input type="text" id="phone" name="phone" class="form-control" value="{{phone}}" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="tax" class="col-sm-2 control-label">Sales Tax:</label>
					<div class="col-sm-5">
						<input type="text" id="tax" name="tax" class="form-control" value="{{tax}}" disabled>
					</div>
				</div>
			</form>
		</script>
		
	</body>
</html>