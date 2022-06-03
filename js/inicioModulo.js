var inicioModulo = function () {
	
	this.init = function () {
		this.createChart();
		this.fetchData();
	},

	this.createChart = function () {
		this.chart = new CanvasJS.Chart("mainChart", {
			title: {
				text: "Facturación (últimos 12 meses)",
				fontSize: 20,
				fontFamily: 'Verdana',
				fontColor: '#999',
				fontStyle: 'bold'
			},
			axis: {
				title: "$",
				includeZero: true
			},
			legend: {
				cursor: 'pointer',
				// itemclick: toggleDataSeries
			},
			data: [{
				type: 'column',
				showInLegend: true,
				name: 'Emitidas',
				color: '#337ab7', // blue
				dataPoints: []
			},{
				type: 'column',
				showInLegend: true,
				name: 'Recibidas',
				color: '#c85b5b', // red
				dataPoints: []
			},{
				type: 'column',
				showInLegend: true,
				name: 'Pagos',
				color: '#de8383', // pink
				dataPoints: []
			}]
		});

		this.chart.render();
	},

	this.fetchData = function () {
		var that = this;
		
		$.ajax({
			url:  'app.php/facturas/chart12',
			type: 'POST',
			dataType: 'JSON',
			success: function (response) {
				that.parseDataPoints(response);
			},
			error: function (response) {
				var resp = JSON.parse(response.responseText);
				notify(resp.msg, 'E');
			}
		});
	},

	this.parseDataPoints = function (data) {
		var that = this;

		data.emitidas.forEach(function (item) {
			that.chart.data[0].dataPoints.push({
				label: item.label,
				y: parseFloat(item.total)
			});
		});

		data.recibidas.forEach(function (item) {
			that.chart.data[1].dataPoints.push({
				label: item.label,
				y: parseFloat(item.total)
			});
		});

		data.pagos.forEach(function (item) {
			that.chart.data[2].dataPoints.push({
				label: item.label,
				y: parseFloat(item.total)
			});
		});

		this.chart.render();
	}
}