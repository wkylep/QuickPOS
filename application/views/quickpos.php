<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Quick POS</title>
		<link href="<?php echo base_url('lib/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/bootstrap/css/bootstrap-theme.min.css') ?>" rel="stylesheet">
		<link href="<?php echo base_url('lib/quickpos/css/pointofsale.css') ?>" rel="stylesheet">
		<script>
			var basepath = "<?php echo current_url() ?>";
		</script>
		<script src="<?php echo base_url('lib/jquery/jquery-2.1.3.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/bootstrap/js/bootstrap.min.js') ?>"></script>
		<script src="<?php echo base_url('lib/handlebars/handlebars-v3.0.0.js') ?>"></script>
		<script src="<?php echo base_url('lib/quickpos/js/quickpos.js') ?>"></script>
	</head>
	<body>
		
		<!-- Fixed Navbar -->
		<div class="navbar navbar-inverse navbar-fixed-top hidden-print">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-top">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php echo site_url('quickpos') ?>">QuickPOS</a>
				</div>
				<div id="navbar-top" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Cashier <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a id="menuCountDrawer" href="#">Count Drawer</a></li>
								<li><a id="menuVoid" href="#">Void</a></li>
								<!-- <li><a id="menuPayout" href="#">Payout</a></li> -->
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Main Menu <span class="caret"></span></a>
							<ul class="dropdown-menu">
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
				<div class="col-sm-3 col-md-2 sidebar">
					<h4>.. loading ..</h4>
					<hr />
					<ul class="nav nav-sidebar">
					</ul>
				</div>
				
				<!-- Content Container -->
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="main_container"></div>
				
				<!-- Full Content Container -->
				<div class="col-sm-12 col-md-12 main hidden" id="full_container"></div>
				
			</div>
		</div>
		
		<!-- Fixed Bottom Navbar -->
		<div id="navbar-footer" class="navbar navbar-inverse navbar-fixed-bottom hidden-print">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-bottom">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div id="navbar-bottom" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-left footer-right">
						<li><button id="btnservice" class="btn btn-default navbar-btn">Services</button></li>
						<li><button id="btncoupon" class="btn btn-default navbar-btn">Coupons</button></li>
					</ul>
					<ul class="nav navbar-nav navbar-right footer-right">
						<li><button id="btncashout" class="btn btn-default navbar-btn">Cash Out</button></li>
					</ul>
				</div>
			</div>
		</div>
		
		<!-- Service Modal -->
		<div id="servicemodal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Service Information</h4>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<form class="form-horizontal">
								<div class="form-group">
									<label for="servicecomment" class="control-label col-sm-6">Note / Comment:</label>
									<div class="col-sm-6">
										<input type="text" id="servicecomment" name="servicecomment" class="form-control" autocomplete="off" maxlength="30">
									</div>
								</div>
								<div id="serviceretailgroup" class="form-group">
									<label for="serviceretail" class="control-label col-sm-6">Retail:</label>
									<div class="col-sm-6">
										<input type="text" id="serviceretail" name="serviceretail" class="form-control" autocomplete="off" maxlength="10">
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="servicemodalbtn" class="btn btn-default">Next</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Password Modal -->
		<div id="passwordmodal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Password Required</h4>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<form class="form-horizontal">
								<div class="form-group">
									<label for="password" class="control-label col-sm-6">Password:</label>
									<div class="col-sm-6">
										<input type="password" id="password" name="password" class="form-control">
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="passwordbtn" class="btn btn-default">Login</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Void Modal -->
		<div id="voidmodal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Void Receipt</h4>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<div class="list-group"></div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Void Modal Item -->
		<script id="voidmodalitem" type="text/x-handlebars-template">
			<a href="#" data-id="{{id}}" class="list-group-item{{#if void}} disabled{{/if}}"><span class="badge">${{formatMoney total}}</span>#{{id}} - {{datetime}}</a>
		</script>
		
		<!-- Count Drawer Modal -->
		<div id="countdrawermodal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Count Cash Drawer</h4>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<form id="cashdrawerform" class="form-horizontal">
								<div class="form-group">
									<label for="penny" class="control-label col-sm-4">Pennies</label>
									<div class="col-sm-4">
										<input type="text" id="penny" name="penny" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="penny_count" name="penny_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="nickel" class="control-label col-sm-4">Nickels</label>
									<div class="col-sm-4">
										<input type="text" id="nickel" name="nickel" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="nickel_count" name="nickel_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="dime" class="control-label col-sm-4">Dimes</label>
									<div class="col-sm-4">
										<input type="text" id="dime" name="dime" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="dime_count" name="dime_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="quarter" class="control-label col-sm-4">Quarters</label>
									<div class="col-sm-4">
										<input type="text" id="quarter" name="quarter" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="quarter_count" name="quarter_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="one" class="control-label col-sm-4">$1</label>
									<div class="col-sm-4">
										<input type="text" id="one" name="one" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="one_count" name="one_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="five" class="control-label col-sm-4">$5</label>
									<div class="col-sm-4">
										<input type="text" id="five" name="five" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="five_count" name="five_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="ten" class="control-label col-sm-4">$10</label>
									<div class="col-sm-4">
										<input type="text" id="ten" name="ten" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="ten_count" name="ten_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="twenty" class="control-label col-sm-4">$20</label>
									<div class="col-sm-4">
										<input type="text" id="twenty" name="twenty" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="twenty_count" name="twenty_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="fifty" class="control-label col-sm-4">$50</label>
									<div class="col-sm-4">
										<input type="text" id="fifty" name="fifty" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="fifty_count" name="fifty_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="hundred" class="control-label col-sm-4">$100</label>
									<div class="col-sm-4">
										<input type="text" id="hundred" name="hundred" class="form-control">
									</div>
									<div class="col-sm-4">
										<input type="text" id="hundred_count" name="hundred_count" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="totaldrawer" class="control-label col-sm-4">Total:</label>
									<div class="col-sm-4 col-sm-offset-4">
										<input type="text" id="totaldrawer" name="totaldrawer" class="form-control" disabled>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default" id="opendrawer">Open Drawer</button>
						<button class="btn btn-default" id="printdrawer">Print</button>
						<button class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Payout Modal -->
		<div id="payoutmodal" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Payout</h4>
					</div>
					<div class="modal-body">
						<div class="container-fluid">
							<form id="payoutform" class="form-horizontal">
								<div class="form-group">
									<label for="payoutdesc" class="control-label col-sm-6">Description:</label>
									<div class="col-sm-6">
										<input type="text" id="payoutdesc" name="payoutdesc" class="form-control">
									</div>
								</div>
								<div class="form-group">
									<label for="payoutamount" class="control-label col-sm-6">Amount:</label>
									<div class="col-sm-6">
										<input type="text" id="payoutamount" name="payoutamount" class="form-control">
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default" id="payoutopen">Open Drawer</button>
						<button class="btn btn-default" id="payoutdone">Complete</button>
						<button class="btn btn-default" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Side Navbar Item -->
		<script id="sidebaritem" type="text/x-handlebars-template">
			<li><button data-id="{{id}}" class="btn btn-link">{{name}}</button></li>
		</script>
		
		<!-- Invoice Table -->
		<script id="invoicetable" type="text/x-handlebars-template">
			<div id="error" class="alert alert-danger hidden"></div>
			<table id="itemtable" class="table table-bordered">
				<thead>
					<tr>
						<th class="col-sm-9">Description</th>
						<th class="col-sm-3"><span class="pull-right">Total</span></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			
			<table class="table">
				<tbody>
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">Subtotal:</strong></td>
						<td class="col-sm-3" style="border: 0px;">$<strong id="subtotal" class="pull-right">0.00</strong></td>
					</tr>
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">Sales Tax:</strong></td>
						<td class="col-sm-3">$<strong id="tax" class="pull-right">0.00</strong></td>
					</tr>
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">Total:</strong></td>
						<td class="col-sm-3">$<strong id="total" class="pull-right">0.00</strong></td>
					</tr>
				</tbody>
			</table>
		</script>
		
		<!-- Invoice Table Item -->
		<script id="invoicetableitem" type="text/x-handlebars-template">
			<tr>
				<td><button data-id="{{index}}" class="close pull-left" style="margin-right: 5px;">&times;</button>{{item.name}}</td>
				{{#ifNegative item.retail}}
				<td>$<span class="pull-right" style="color: red;">({{formatMoney item.retail}})</span></td>
				{{else}}
				<td>$<span class="pull-right">{{formatMoney item.retail}}</span></td>
				{{/ifNegative}}
			</tr>
		</script>
		
		<!-- Receipt -->
		<script id="receipt" type="text/x-handlebars-template">
			<div class="pull-right hidden-print">
				<button id="closebtn" class="btn btn-default">Close</button>
				<button id="printbtn" class="btn btn-default">Print</button>
			</div>
			<div class="col-sm-12 text-center">
				<h3>Receipt #{{id}}</h3>
				<strong>{{config.name}}</strong><br />
				{{config.address}}<br />
				{{config.city}}, {{config.state}} {{config.postal}}<br />
				{{config.phone}}
			</div>
			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<td class="col-sm-9">Description</td>
						<td class="col-sm-3"><span class="pull-right">Total</span></td>
					</tr>
				</thead>
				<tbody>
					{{#each items}}
					<tr>
						<td>{{name}}</td>
						{{#ifNegative item.retail}}
						<td>$<span class="pull-right" style="color: red;">({{formatMoney retail}})</span></td>
						{{else}}
						<td>$<span class="pull-right">{{formatMoney retail}}</span></td>
						{{/ifNegative}}
					</tr>
					{{/each}}
				</tbody>
			</table>
			
			<table class="table">
				<tbody>
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">Subtotal:</strong></td>
						<td class="col-sm-3" style="border: 0px;">$<strong id="subtotal" class="pull-right">{{formatMoney subtotal}}</strong></td>
					</tr>
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">Sales Tax ({{formatTax config.tax}}):</strong></td>
						<td class="col-sm-3">$<strong id="tax" class="pull-right">{{formatMoney tax}}</strong></td>
					</tr>
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">Total:</strong></td>
						<td class="col-sm-3">$<strong id="total" class="pull-right">{{formatMoney total}}</strong></td>
					</tr>
					{{#each payments}}
					<tr>
						<td class="col-sm-9" style="border: 0px;"><strong class="pull-right">{{name}}:</strong></td>
						<td class="col-sm-3">$<strong id="total" class="pull-right" style="color: red;">({{formatMoney amount}})</strong></td>
					</tr>
					{{/each}}
				</tbody>
			</table>
		</script>
		
	</body>
</html>