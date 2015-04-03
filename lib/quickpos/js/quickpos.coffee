class Quickpos

	constructor: (@path) ->
		@active = ""
		@invoice_items = []
		@container = "#main_container"
	
	getConfig: ->
		$.getJSON("#{@path}/config")
		.done((data) =>
			@config = data.content
			@setupNav()
			)
	
	closeActive: =>
		if @["close#{@active}"]?
			return @["close#{@active}"]()
		else
			return @defaultClose()
	
	setActive: (name) =>
		return false if @active is name or not @closeActive()
		@active = name
		true
	
	defaultClose: =>
		@active = ""
		$(@container).empty()
		true
	
	setContainer: (val) ->
		$(@container).html(val)
	
	setupNav: ->
		$("#btnservice").click((e) =>
			e.preventDefault()
			@showServices() )
		$("#btncoupon").click((e) =>
			e.preventDefault()
			@showCoupons() )
		$("#btncashout").click((e) =>
			e.preventDefault()
			@showCashout() )
		$("#menuVoid").click((e) =>
			e.preventDefault()
			@showVoid() )
		$("#menuCountDrawer").click((e) =>
			e.preventDefault()
			@showCountDrawer() )
		$("#menuPayout").click((e) =>
			e.preventDefault()
			@showPayout() )
		@showServices()
		@showInvoiceTable(false)
		$(".navbar-brand").html(@config.name)
	
	addInvoiceItem: (id, name, retail, type = "service") =>
		@invoice_items.push({ id: id, name: name, retail: retail, type: type })
		@showInvoiceTable()
	
	showServices: =>
		$.getJSON("#{@path}/get_services")
		.done((data) =>
			@setSidebar("Services", data.content)
			$(".sidebar .nav-sidebar button").click((e) =>
				e.preventDefault()
				id = $(e.currentTarget).attr("data-id")
				$.getJSON("#{@path}/get_service/#{id}")
				.done((data) =>
					if data.content.edit is "1"
						$("#servicemodal .modal-title").html(data.content.name)
						$("#servicemodal #serviceretail").val(Number(data.content.retail).toFixed(2))
						$("#servicemodalbtn").off("click").click((e) =>
							comment = $("#servicecomment").val()
							retail = $("#serviceretail").val()
							if isNaN(Number(retail))
								$("#serviceretailgroup").addClass("has-error")
								return true
							@addInvoiceItem(data.content.id, "#{data.content.name}: #{comment}", Number(retail))
							$("#servicemodal").modal("hide")
							)
						$("#serviceretailgroup").removeClass("has-error")
						$("#servicemodal").modal("show")
					else
						@addInvoiceItem(data.content.id, data.content.name, Number(data.content.retail))
					)
				)
			)
	
	showCoupons: =>
		$.getJSON("#{@path}/get_coupons")
		.done((data) =>
			@setSidebar("Coupons", data.content)
			$(".sidebar .nav-sidebar button").click((e) =>
				e.preventDefault()
				id = $(e.currentTarget).attr("data-id")
				$.getJSON("#{@path}/get_coupon/#{id}")
				.done((data) =>
					@addInvoiceItem(data.content.id, data.content.name, Number(data.content.amount) * -1, "coupon")
					)
				)
			)
	
	showCashout: =>
		$.getJSON("#{@path}/get_payments")
		.done((data) =>
			@setSidebar("Select Tender", data.content)
			$(".sidebar .nav-sidebar button").click((e) =>
				e.preventDefault()
				id = $(e.currentTarget).attr("data-id")
				$.post("#{@path}/complete_invoice", { payment: id, items: JSON.stringify(@invoice_items) }, null, "json")
				.done((recdata) =>
					@invoice_items = []
					if @config.showreceipt
						@showReceipt(recdata.content)
					else
						@showServices()
						@showInvoiceTable()
					)
				)
			)
	
	setSidebar: (name, items) =>
		$(".sidebar h4").html(name)
		sidebar = $(".sidebar .nav-sidebar")
		$(sidebar).empty()
		tpl = Handlebars.compile( $("#sidebaritem").html() )
		for i in items
			$(sidebar).append(tpl(i))
	
	showInvoiceTable: (forceRefresh = true) =>
		@closeActive() if forceRefresh and @active is "InvoiceTable"
		return false if not @setActive("InvoiceTable")
		@setContainer( Handlebars.compile($("#invoicetable").html())() )
		tpl = Handlebars.compile($("#invoicetableitem").html())
		for item, index in @invoice_items
			$("#itemtable tbody").append(tpl({ index: index, item: item }))
		@updateTotals()
		$("#itemtable tbody button").click((e) =>
			index = $(e.currentTarget).attr("data-id")
			@invoice_items.splice(index, 1)
			@showInvoiceTable()
			)
	
	updateTotals: =>
		subtotal = 0
		for item in @invoice_items
			subtotal += Number(item.retail)
		tax = subtotal * Number(@config.tax)
		$("#subtotal").html(subtotal.toFixed(2))
		$("#tax").html(tax.toFixed(2))
		$("#total").html((subtotal + tax).toFixed(2))
		
		if subtotal < 0
			$("#error").html("Total can not be less than zero.").removeClass("hidden")
		else
			$("#error").html("").addClass("hidden")
	
	showPassword: (valid = [], callback) =>
		$("#passwordmodal").modal("show")
		$("#password").val("")
		$("#passwordbtn").off("click").click((e) =>
			password = $("#password").val()
			for v in valid
				if v == password
					$("#passwordmodal").modal("hide")
					if callback
						callback()
				return true
			$("#password").val("")
			)
	
	showVoid: =>
		@showPassword(@config.password, @showVoidAuthed)
	
	showVoidAuthed: =>
		$.getJSON("#{@path}/get_receipt_list")
		.done((data) =>
			$("#voidmodal").modal("show")
			$("#voidmodal .list-group").empty()
			tpl = Handlebars.compile( $("#voidmodalitem").html() )
			
			for rec in data.content
				$("#voidmodal .list-group").append( tpl(rec) )
				
			$("#voidmodal .list-group a").click((e) =>
				e.preventDefault()
				if $(e.currentTarget).hasClass("disabled")
					return
				
				id = $(e.currentTarget).attr("data-id")
				$.getJSON("#{@path}/void_receipt/#{id}")
				.done( -> $("#voidmodal").modal("hide") )
				)
			)
	
	showCountDrawer: =>
		@showPassword(@config.password, @showCountDrawerAuthed)
	
	showCountDrawerAuthed: =>
		$("#countdrawermodal").modal("show")
		fields = ["penny", "nickel", "dime", "quarter", "one", "five", "ten", "twenty", "fifty", "hundred"]
		for f in fields
			$("##{f}").off("change").change(@updateCountDrawerTotals).val("")
		$("#opendrawer").off("click").click((e) =>
			$.getJSON("#{@path}/open_cashdrawer")
			)
		$("#printdrawer").off("click").click((e) =>
			$.post("#{@path}/print_cashdrawer", $("#cashdrawerform").serialize(), null, "json")
			.done((data) =>
				$("#countdrawermodal").modal("hide")
				)
			)
		@updateCountDrawerTotals()
	
	updateCountDrawerTotals: =>
		total = 0
		fields = { penny: 0.01, nickel: 0.05, dime: 0.1, quarter: 0.25, one: 1, five: 5, ten: 10, twenty: 20, fifty: 50, hundred: 100 }
		for f, v of fields
			c = Number( $("##{f}").val() )
			if c is 0 or isNaN(c)
				$("##{f}_count").val("")
			else
				total += c * v
				$("##{f}_count").val("$#{(c * v).toFixed(2)}")
		$("#totaldrawer").val("$#{total.toFixed(2)}")
	
	showPayout: =>
		@showPassword(@config.password, @showPayoutAuthed)
	
	showPayoutAuthed: =>
		$("#payoutmodal").modal("show")
		$("#payoutdesc,#payoutamount").val("")
		$("#payoutopen").off("click").click((e) =>
			$.getJSON("#{@path}/open_cashdrawer")
			)
		$("#payoutdone").off("click").click((e) =>
			$.post("#{@path}/payout", $("#payoutform").serialize(), null, "json")
			.done((data) =>
				# check for validation errors
				$("#payoutmodal").modal("hide")
				)
			)
	
	showReceipt: (data) =>
		return false if not @setActive("Receipt")
		$("#main_container,.navbar,.sidebar").addClass("hidden")
		data.config = @config
		$("#full_container").removeClass("hidden").html( Handlebars.compile($("#receipt").html())(data) )
		$("#printbtn").click((e) =>
			e.preventDefault()
			window.print()
			)
		$("#closebtn").click((e) =>
			e.preventDefault()
			$("#main_container,.navbar,.sidebar").removeClass("hidden")
			$("#full_container").addClass("hidden").empty()
			@showServices()
			@showInvoiceTable()
			)

p = new Quickpos(window.basepath)
$(document).ready(() ->
	
	Handlebars.registerHelper('formatMoney', (val) ->
		if val < 0
			val = val * -1
		return "#{Number(val).toFixed(2)}"
		)
	
	Handlebars.registerHelper('formatTax', (val) ->
		return "#{Number(val * 100).toFixed(2)}%"
		)
	
	Handlebars.registerHelper('isChecked', (expr) ->
		return if Number(expr) then 'checked' else ''
		)
	
	Handlebars.registerHelper('isSelected', (v1, v2) ->
		return if v1 is v2 then 'selected' else ''
		)
	
	Handlebars.registerHelper('ifEqOr', (v1, v2, v3, options) ->
		return if v1 is v2 or v1 is v3 then options.fn(@) else options.inverse(@)
		)
	
	Handlebars.registerHelper('ifNegative', (val, options) ->
		return if val < 0 then options.fn(@) else options.inverse(@)
		)
	
	p.getConfig()
)