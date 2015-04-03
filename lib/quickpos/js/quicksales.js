// Generated by CoffeeScript 1.8.0
var Quicksales, p,
  __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

Quicksales = (function() {
  function Quicksales(path) {
    this.path = path;
    this.showDepositReport = __bind(this.showDepositReport, this);
    this.updateDeposit = __bind(this.updateDeposit, this);
    this.showDeposit = __bind(this.showDeposit, this);
    this.buildReport = __bind(this.buildReport, this);
    this.showConsReport = __bind(this.showConsReport, this);
    this.showConsolidated = __bind(this.showConsolidated, this);
    this.showCurrent = __bind(this.showCurrent, this);
    this.defaultClose = __bind(this.defaultClose, this);
    this.setActive = __bind(this.setActive, this);
    this.closeActive = __bind(this.closeActive, this);
    this.active = "";
    this.container = "#full_container";
  }

  Quicksales.prototype.getConfig = function() {
    return $.getJSON("" + this.path + "/config").done((function(_this) {
      return function(data) {
        _this.config = data.content;
        return _this.setupNav();
      };
    })(this));
  };

  Quicksales.prototype.closeActive = function() {
    if (this["close" + this.active] != null) {
      return this["close" + this.active]();
    } else {
      return this.defaultClose();
    }
  };

  Quicksales.prototype.setActive = function(name) {
    if (this.active === name || !this.closeActive()) {
      return false;
    }
    this.active = name;
    return true;
  };

  Quicksales.prototype.defaultClose = function() {
    this.active = "";
    $(this.container).empty();
    return true;
  };

  Quicksales.prototype.setContainer = function(val) {
    return $(this.container).html(val);
  };

  Quicksales.prototype.setupNav = function() {
    $("#menuCurrent").click((function(_this) {
      return function(e) {
        e.preventDefault();
        return _this.showCurrent();
      };
    })(this));
    $("#menuConsolidated").click((function(_this) {
      return function(e) {
        e.preventDefault();
        return _this.showConsolidated();
      };
    })(this));
    $("#menuDeposit").click((function(_this) {
      return function(e) {
        e.preventDefault();
        return _this.showDeposit();
      };
    })(this));
    $("#menuDepositReport").click((function(_this) {
      return function(e) {
        e.preventDefault();
        return _this.showDepositReport();
      };
    })(this));
    $(".navbar-brand").html(this.config.name);
    return this.showCurrent();
  };

  Quicksales.prototype.showCurrent = function() {
    if (!this.setActive("Current")) {
      return false;
    }
    return $.getJSON("" + this.path + "/get_sales_report").done((function(_this) {
      return function(data) {
        return _this.buildReport(data, "Current Sales");
      };
    })(this));
  };

  Quicksales.prototype.showConsolidated = function() {
    if (!this.setActive("Consolidated")) {
      return false;
    }
    this.setContainer(Handlebars.compile($("#consolidated").html())());
    $(".input-group.date").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true
    });
    return $("#reportbtn").click((function(_this) {
      return function(e) {
        var end, start;
        e.preventDefault();
        start = $("#start").val();
        end = $("#end").val();
        if (start && end) {
          return _this.showConsReport(start, end);
        }
      };
    })(this));
  };

  Quicksales.prototype.showConsReport = function(start, end) {
    if (!this.setActive("ConsReport")) {
      return false;
    }
    return $.getJSON("" + this.path + "/get_sales_report/" + start + "/" + end).done((function(_this) {
      return function(data) {
        return _this.buildReport(data, "Consolidated Sales");
      };
    })(this));
  };

  Quicksales.prototype.buildReport = function(data, title) {
    if (data.content) {
      data.content.title = title;
      return this.setContainer(Handlebars.compile($("#salesreport").html())(data.content));
    } else {
      return this.setContainer(Handlebars.compile($("#salesreporterror").html())());
    }
  };

  Quicksales.prototype.showDeposit = function() {
    if (!this.setActive("Deposit")) {
      return false;
    }
    return $.getJSON("" + this.path + "/get_deposit_data").done((function(_this) {
      return function(data) {
        _this.setContainer(Handlebars.compile($("#deposit").html())(data.content));
        _this.updateDeposit(data.content.payments);
        $("input[id^='pay_']").change(function(e) {
          return _this.updateDeposit(data.content.payments);
        });
        return $("#savebtn").click(function(e) {
          e.preventDefault();
          return $.post("" + _this.path + "/save_deposit", $("#depositform").serialize(), null, "json").done(function(data) {
            if (data.error_code) {
              return $("#depositerror").removeClass("hidden").html(data.message);
            } else {
              return _this.showCurrent();
            }
          });
        });
      };
    })(this));
  };

  Quicksales.prototype.updateDeposit = function(payments) {
    var expected, p, t, total, v, _i, _len;
    total = 0;
    expected = 0;
    for (_i = 0, _len = payments.length; _i < _len; _i++) {
      p = payments[_i];
      v = $("#pay_" + p.id).val();
      v = isNaN(Number(v)) ? 0 : Number(v);
      t = Number(p.total);
      $("#diff_" + p.id).val((v - t).toFixed(2));
      total += v;
      expected += t;
    }
    $("#total").val(total.toFixed(2));
    return $("#overshort").val((total - expected).toFixed(2));
  };

  Quicksales.prototype.showDepositReport = function() {
    if (!this.setActive("DepositReport")) {
      return false;
    }
    this.setContainer(Handlebars.compile($("#depositreport").html())());
    $(".input-group.date").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true
    });
    return $("#reportbtn").click((function(_this) {
      return function(e) {
        var v;
        e.preventDefault();
        v = $("#date").val();
        if (v) {
          $("#report").empty();
          return $.getJSON("" + _this.path + "/get_deposit_report/" + v).done(function(data) {
            return $("#report").html(Handlebars.compile($("#depositreporttable").html())(data.content));
          });
        }
      };
    })(this));
  };

  return Quicksales;

})();

p = new Quicksales(window.basepath);

$(document).ready(function() {
  Handlebars.registerHelper('formatMoney', function(val) {
    var n;
    if (val < 0) {
      val = val * -1;
    }
    n = Number(val);
    if (isNaN(n)) {
      n = 0;
    }
    return n.toFixed(2);
  });
  Handlebars.registerHelper('isChecked', function(expr) {
    if (Number(expr)) {
      return 'checked';
    } else {
      return '';
    }
  });
  Handlebars.registerHelper('isSelected', function(v1, v2) {
    if (v1 === v2) {
      return 'selected';
    } else {
      return '';
    }
  });
  Handlebars.registerHelper('ifEqOr', function(v1, v2, v3, options) {
    if (v1 === v2 || v1 === v3) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }
  });
  Handlebars.registerHelper('ifNegative', function(val, options) {
    if (val < 0) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }
  });
  Handlebars.registerHelper('subtract', function(v1, v2) {
    if (isNaN(v1)) {
      v1 = 0;
    }
    if (isNaN(v2)) {
      v2 = 0;
    }
    return Number(v1 - v2).toFixed(2);
  });
  return p.getConfig();
});