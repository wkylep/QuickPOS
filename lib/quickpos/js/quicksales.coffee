class Quicksales

	constructor: (@path) ->
		@active = ""
		@container = "#full_container"
	
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
		$("#menuCurrent").click((e) =>
			e.preventDefault()
			@showCurrent() )
		$("#menuConsolidated").click((e) =>
			e.preventDefault()
			@showConsolidated() )
		$("#menuDeposit").click((e) =>
			e.preventDefault()
			@showDeposit() )
		$("#menuDepositReport").click((e) =>
			e.preventDefault()
			@showDepositReport() )
		$(".navbar-brand").html(@config.name)
		@showCurrent()
	
	showCurrent: =>
		return false if not @setActive("Current")
		$.getJSON("#{@path}/get_sales_report")
		.done((data) =>
			@buildReport(data, "Current Sales")
			)
	
	showConsolidated: =>
		return false if not @setActive("Consolidated")
		@setContainer( Handlebars.compile($("#consolidated").html())() )
		$(".input-group.date").datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		})
		$("#reportbtn").click((e) =>
			e.preventDefault()
			start = $("#start").val()
			end = $("#end").val()
			if start and end
				@showConsReport(start, end)
			)
	
	showConsReport: (start, end) =>
		return false if not @setActive("ConsReport")
		$.getJSON("#{@path}/get_sales_report/#{start}/#{end}")
		.done((data) =>
			@buildReport(data, "Consolidated Sales")
			)
	
	buildReport: (data, title) =>
		if data.content
			data.content.title = title
			@setContainer( Handlebars.compile($("#salesreport").html())(data.content) )
		else
			@setContainer( Handlebars.compile($("#salesreporterror").html())() )
	
	showDeposit: =>
		return false if not @setActive("Deposit")
		$.getJSON("#{@path}/get_deposit_data")
		.done((data) =>
			@setContainer( Handlebars.compile($("#deposit").html())(data.content) )
			@updateDeposit(data.content.payments)
			$("input[id^='pay_']").change((e) =>
				@updateDeposit(data.content.payments)
				)
			$("#savebtn").click((e) =>
				e.preventDefault()
				$.post("#{@path}/save_deposit", $("#depositform").serialize(), null, "json")
				.done((data) =>
					if data.error_code
						$("#depositerror").removeClass("hidden").html(data.message)
					else
						@showCurrent()
					)
				)
			)
	
	updateDeposit: (payments) =>
		total = 0
		expected = 0
		
		for p in payments
			v = $("#pay_#{p.id}").val()
			v = if isNaN(Number(v)) then 0 else Number(v)
			t = Number(p.total)
			$("#diff_#{p.id}").val((v - t).toFixed(2))
			total += v
			expected += t
		
		$("#total").val(total.toFixed(2))
		$("#overshort").val((total - expected).toFixed(2))
	
	showDepositReport: =>
		return false if not @setActive("DepositReport")
		@setContainer( Handlebars.compile($("#depositreport").html())() )
		$(".input-group.date").datepicker({
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true
		})
		$("#reportbtn").click((e) =>
			e.preventDefault()
			v = $("#date").val()
			if v
				$("#report").empty()
				$.getJSON("#{@path}/get_deposit_report/#{v}")
				.done((data) =>
						$("#report").html( Handlebars.compile($("#depositreporttable").html())(data.content) )
					)
			)

p = new Quicksales(window.basepath)
$(document).ready(() ->
	
	Handlebars.registerHelper('formatMoney', (val) ->
		if val < 0
			val = val * -1
		n = Number(val)
		if isNaN(n)
			n = 0
		return n.toFixed(2)
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
	
	Handlebars.registerHelper('subtract', (v1, v2) ->
		if isNaN(v1)
			v1 = 0
		if isNaN(v2)
			v2 = 0
		return Number(v1 - v2).toFixed(2)
		)
	
	p.getConfig()
)