class Quicksetup

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
		$("#menuInfo").click((e) =>
			e.preventDefault()
			@showInfo() )
		$("#menuServices").click((e) =>
			e.preventDefault()
			@showServices() )
		@showInfo()
		$(".navbar-brand").html(@config.name)
	
	showInfo: =>
		return false if not @setActive("Info")
		$.getJSON("#{@path}/get_store_info")
		.done((data) =>
			@setContainer( Handlebars.compile($("#storeinfo").html())(@config) )
			)
	
	showServices: =>
		return false if not @setActive("Services")
		$.getJSON("#{@path}/get_services")
		.done((data) =>
			@setContainer( Handlebars.compile($("#servicelist").html())(data.content) )
			$("#addgroup").click((e) =>
				e.preventDefault()
				@showGroup()
				)
			$("a[id^='add_']").click((e) =>
				e.preventDefault()
				@showService(0, $(e.currentTarget).attr("data-id"))
				)
			$("a[id^='group_']").click((e) =>
				e.preventDefault()
				@showGroup($(e.currentTarget).attr("data-id"))
				)
			$("a[id^='service_']").click((e) =>
				e.preventDefault()
				@showService($(e.currentTarget).attr("data-id"), 0)
				)
			$("#addcoupon").click((e) =>
				e.preventDefault()
				@showCoupon()
				)
			$("a[id^='coupon_']").click((e) =>
				e.prevetDefault()
				@showCoupon($(e.currentTarget).attr("data-id"))
				)
			)
	
	showGroup: (id = 0) =>
		return false if not @setActive("Group")
		
		grouptpl = (data) =>
			@setContainer( Handlebars.compile($("#groupedit").html())(if data then data.content else null) )
			$("#savebtn").click((e) =>
				e.preventDefault()
				$.post("#{@path}/save_group", $("#groupform").serialize(), null, "json")
				.done((data) =>
					e.preventDefault()
					@showServices()
					)
				)
			$("#cancelbtn").click((e) =>
				e.preventDefault()
				@showServices()
				)
		
		if id is 0
			grouptpl()
		else
			$.getJSON("#{@path}/get_group/#{id}")
			.done((data) -> grouptpl(data))
	
	showService: (id = 0, group = 0) =>
		return false if not @setActive("Service")
		$.getJSON("#{@path}/get_service/#{group}/#{id}")
		.done((data) =>
			@setContainer( Handlebars.compile($("#serviceedit").html())(data.content) )
			$("#savebtn").click((e) =>
				e.preventDefault()
				$.post("#{@path}/save_service", $("#serviceform").serialize(), null, "json")
				.done((data) =>
					e.preventDefault()
					@showServices()
					)
				)
			$("#cancelbtn").click((e) =>
				e.preventDefault()
				@showServices()
				)
			)
	
	showCoupon: (id = 0) =>
		return false if not @setActive("Coupon")
		
		coupontpl = (data) =>
			@setContainer( Handlebars.compile($("#couponedit").html())(if data then data.content else null) )
			$("#savebtn").click((e) =>
				e.preventDefault()
				$.post("#{@path}/save_coupon", $("#couponform").serialize(), null, "json")
				.done((data) =>
					@showServices()
					)
				)
			$("#cancelbtn").click((e) =>
				e.preventDefault()
				@showServices()
				)
		
		if id is 0
			coupontpl()
		else
			$.getJSON("#{@path}/get_coupon/#{id}")
			.done((data) -> coupontpl(data))

p = new Quicksetup(window.basepath)
$(document).ready(() ->
	
	Handlebars.registerHelper('formatMoney', (val) ->
		if val < 0
			val = val * -1
		return "#{Number(val).toFixed(2)}"
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