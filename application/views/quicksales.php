<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Quick POS : Sales</title>
		<link href="<?php echo base_url('lib/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/bootstrap/css/bootstrap-theme.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/datepicker/css/bootstrap-datepicker3.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/quickpos/css/pointofsale.css') ?>" rel="stylesheet">
		<script>
			var basepath = "<?php echo current_url() ?>";
		</script>
		<script src="<?php echo base_url('lib/jquery/jquery-2.1.3.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/bootstrap/js/bootstrap.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/handlebars/handlebars-v3.0.0.js') ?>"></script>
		<script src="<?php echo base_url('lib/datepicker/js/bootstrap-datepicker.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/quickpos/js/quicksales.js') ?>"></script>
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
					<a class="navbar-brand" href="<?php echo site_url('quicksales') ?>">Quick Sales</a>
				</div>
				<div id="navbar-top" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a id="menuCurrent" href="#">Current Sales</a></li>
								<li><a id="menuConsolidated" href="#">Consolidated Sales</a></li>
								<li><a id="menuDeposit" href="#">Deposit</a></li>
								<li><a id="menuDepositReport" href="#">Deposit Report</a></li>
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
		
		<!-- Sales Report -->
		<script id="salesreport" type="text/x-handlebars-template">
			<h3>{{title}}</h3>
			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<th class="col-sm-8">Description</th>
						<th class="col-sm-2">Count</th>
						<th class="col-sm-2">Total</th>
					</tr>
				</thead>
				<tbody>
					{{#each services}}
					<tr>
						<td>{{description}}</td>
						<td><span class="pull-right">{{count}}</span></td>
						<td>$<span class="pull-right">{{formatMoney total}}</span></td>
					</tr>
					{{/each}}
					<tr>
						<td><strong class="pull-right">Sales Subtotal</strong></td>
						<td></td>
						<td>$<span class="pull-right">{{formatMoney service_total}}</span></td>
					</tr>
					{{#each coupons}}
					<tr>
						<td>{{description}}</td>
						<td><span class="pull-right">{{count}}</span></td>
						<td>$<span class="pull-right" style="color: red;">({{formatMoney total}})</span></td>
					</tr>
					{{/each}}
					<tr>
						<td><strong class="pull-right">Coupon Subtotal</strong></td>
						<td></td>
						<td>$<span class="pull-right" style="color: red;">({{formatMoney coupon_total}})</span></td>
					</tr>
					<tr>
						<td><strong class="pull-right">Net Sales</strong></td>
						<td></td>
						<td>$<span class="pull-right">{{subtract total tax}}</span></td>
					</tr>
					<tr>
						<td><strong class="pull-right">Sales Tax</strong></td>
						<td></td>
						<td>$<span class="pull-right">{{formatMoney tax}}</span></td>
					</tr>
					<tr>
						<td><strong class="pull-right">Gross Sales</strong></td>
						<td></td>
						<td>$<span class="pull-right">{{formatMoney total}}</span></td>
					</tr>
				</tbody>
			</table>
			<h3>Sales Summary</h3>
			<ul class="list-group col-sm-5">
				<li class="list-group-item">Customers<span class="pull-right">{{count}}</span></li>
				<li class="list-group-item">Voids<span class="pull-right">{{void}}</span></li>
				<li class="list-group-item">Net Average<span class="pull-right">${{formatMoney net_average}}</span></li>
				<li class="list-group-item">Coupon Average<span class="pull-right">${{formatMoney coupon_average}}</span></li>
			</ul>
		</script>
		
		<!-- Sales Report Error -->
		<script id="salesreporterror" type="text/x-handlebars-template">
			<h3>Report Error</h3>
			<div class="alert alert-danger">There is no sales data for the requested dates. Please try again.</div>
		</script>
		
		<!-- Consolidated Report Form -->
		<script id="consolidated" type="text/x-handlebars-template">
			<h3>Consolidated Sales Report</h3>
			<form id="salesform" class="form-horizontal">
				<div class="form-group">
					<label for="start" class="col-sm-2 control-label">Start Date:</label>
					<div class="col-sm-5">
						<div class="input-group date">
							<input type="text" id="start" name="start" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="end" class="col-sm-2 control-label">End Date:</label>
					<div class="col-sm-5">
						<div class="input-group date">
							<input type="text" id="end" name="end" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<button type="button" id="reportbtn" class="btn btn-default">View Report</button>
					</div>
				</div>
			</form>
		</script>
		
		<!-- Deposit -->
		<script id="deposit" type="text/x-handlebars-template">
			<style>
				input {
					text-align: right;
				}
			</style>
			<h3>Deposit</h3>
			{{#if today}}
			<div class="alert alert-danger">A deposit has already been posted today at {{today}}.</div>
			{{/if}}
			<div id="depositerror" class="alert alert-danger hidden"></div>
			<form id="depositform" class="form-horizontal">
				{{#each payments}}
				<div class="form-group">
					<label for="pay_{{id}}" class="col-sm-2 control-label">{{name}}</label>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon">$</div>
							<input type="text" id="pay_{{id}}" name="pay_{{id}}" data-id="{{id}}" class="form-control">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon">$</div>
							<input type="text" id="diff_{{id}}" name="diff_{{id}}" class="form-control" disabled>
						</div>
					</div>
				</div>
				{{/each}}
				<div class="form-group">
					<label for="total" class="col-sm-2 control-label">Deposit Total</label>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon">$</div>
							<input type="text" id="total" name="total" class="form-control" disabled>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon">$</div>
							<input type="text" id="overshort" name="overshort" class="form-control" disabled>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<button type="button" id="savebtn" name="savebtn" class="btn btn-default" {{#if today}} disabled {{/if}}>Save Deposit</button>
					</div>
				</div>
			</form>
		</script>
		
		<!-- Deposit Report -->
		<script id="depositreport" type="text/x-handlebars-template">
			<h3>Deposit Report</h3>
			<form class="form-horizontal">
				<div class="form-group">
					<label for="date" class="col-sm-2 control-label">Deposit Date:</label>
					<div class="col-sm-5">
						<div class="input-group date">
							<input type="text" id="date" name="date" class="form-control"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-5">
						<button type="button" id="reportbtn" class="btn btn-default">View Report</button>
					</div>
				</div>
			</form>
			<div id="report"></div>
		</script>
		
		<!-- Deposit Report Table -->
		<script id="depositreporttable" type="text/x-handlebars-template">
			<h3>{{title}}</h3>
			{{#if payments}}
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th class="col-sm-3">Payment</th>
						<th class="col-sm-3"><span class="pull-right">Deposited</span></th>
						<th class="col-sm-3"><span class="pull-right">Expected</span></th>
						<th class="col-sm-3"><span class="pull-right">Over / Short</span></th>
					</tr>
				</thead>
				<tbody>
					{{#each payments}}
					<tr>
						<td>{{name}}</td>
						<td>$<span class="pull-right">{{formatMoney deposit}}</span></td>
						<td>$<span class="pull-right">{{formatMoney total}}</span></td>
						<td>$<span class="pull-right">{{subtract deposit total}}</span></td>
					</tr>
					{{/each}}
				</tbody>
			</table>
			{{else}}
			<div class="alert alert-danger">No deposit data for the requested date.</div>
			{{/if}}
		</script>
		
	</body>
</html>