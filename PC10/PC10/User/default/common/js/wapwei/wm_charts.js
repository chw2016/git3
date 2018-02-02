var wm_charts = {
	'line' : function(id, data){
		var chartsConfig = {
			chart :	{
				defaultSeriesType: 'spline'
			},
			title :	'',
			xAxis :	{
				title : '',
				lineWidth : 1,
				lineColor : '#EEE',
				type :	'datetime',
				//tickInterval : 12*3600 * 1000,
				endOnTick: false,
				startOnTick: false,
				dateTimeLabelFormats: {
					second: '%H:%M:%S',
					minute: '%H:%M',
					hour: '%H:%M',
					day: '%m - %e',
					week: '%m - %e',
					month: '%b \'%y',
					year: '%Y'
				}
			},
			yAxis :	{
				title : '',
				gridLineColor : '#EEE',
				gridLineWidth: 1,
                min: 0,
				endOnTick :false
			},
			tooltip : {
				enabled: true,
				shadow: false,
				borderWidth: 0,
				backgroundColor: 'rgba(0,0, 0, .85)',
				style:{
					color: '#FFF',
					fontSize: '12px'
				},
				formatter: function() {
					//console.log(this.series.color);
					return Highcharts.dateFormat('%Y年%m月%d日', this.x)+'<br /><strong>'+this.series.name+':'+this.y+'</strong>';
				}
			},
			series: '',
			plotOptions: {
				spline: {
					lineWidth: 2,
					states: {
						hover: {
							lineWidth: 3
						}
					},
					marker: {
						enabled: false,
						states: {
							hover: {
								enabled: true,
								symbol: 'circle',
								radius: 5,
								lineWidth: 1
							}
						}
					},
					shadow : false
				}
			},
			colors: ['#0099FF', '#393', '#f8a31f'],
			legend: {
				x:8,
				y:0,
				borderWidth : 0,
				align: 'center',
				verticalAlign: 'bottom',
				symbolWidth: 20,
				symbolPadding: 3,
				itemStyle: {
					color: '#333'
				},
				labelFormatter: function() {
					return '<strong>'+this.name+'</strong>' +'<span style="color:#BBB">(点击隐藏)</span>';
				}
			},
			exporting: {enabled: false},
			credits: {enabled: false}
		};
		Highcharts.setOptions({global: {useUTC: false}});
		chartsConfig.chart.renderTo = id;
		chartsConfig.series = data;
		if((data[0]['data'][data[0]['data'].length-1][0] - data[0]['data'][0][0]) >= 24*3600 * 1000) chartsConfig.xAxis.dateTimeLabelFormats.hour = '%m - %e';
		var chart = new Highcharts.Chart(chartsConfig);
	},
	
	'pie' : function(id, data, title){
		if(!title) title = '';
		var chartsConfig = {
			chart :	{
				defaultSeriesType: 'pie',
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false
			},
			title :{
				text: '',
				verticalAlign:'bottom',
				y:8,
				style:{
					color:'#333',
					fontSize:'12px',
					fontWeight:'bold'
				}
			},
			tooltip : {
				enabled: true,
				formatter: function(){
					return '<b style="font-size:12px;">'+ this.point.name +'</b>: '+ this.percentage +' %';
				}
			},
			series: '',
			legend: {
				x:8,
				y:0,
				borderWidth : 0,
				align: 'center',
				verticalAlign: 'bottom',
				symbolWidth: 20,
				symbolPadding: 3,
				itemStyle: {
					color: '#333'
				},
				labelFormatter: function() {
					return '<strong>'+this.name+'</strong>';
				}
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					showCheckbox: true,
					dataLabels: {
						enabled: true,
						color: '#999',
						connectorColor: '#CCC',
						formatter: function() {
						//	return '<b style="font-size:12px;">'+ this.point.name +'</b>: '+ this.percentage +' %';
							return '<b style="font-size:12px;">'+ this.point.name +'</b>';
						}
					},
					shadow:false,
					size: 80,
					showInLegend: false
				}
			},
			colors : ['#F7BA00','#0099FF','#00CC00','#FF5522','#FF70B7','#119999'],
//			colors : ['#E6C', '#FC3', '#6C0', '#36F', '#F30', '#BBB'],
			exporting: {enabled: false},
			credits: {enabled: false}
		};	
		Highcharts.setOptions({global: {useUTC: false}});
		chartsConfig.chart.renderTo = id;
		chartsConfig.title.text = title;
		chartsConfig.series = data;
		var chart = new Highcharts.Chart(chartsConfig);
		
	},

	'column' : function(id, cate, data){
		var chartsConfig = {
			chart: {
				type: 'column'
			},
			title : '',
			xAxis: {
				title:{
					text:null
				},
				lineWidth : 1,
				lineColor : '#EEE',
				labels:{
					y : 18,
					style:{
						fontSize:12
					}
				}
			},
			yAxis: {
				min: 0,
				title: '',
				gridLineColor : '#EEE',
				gridLineWidth: 1,
				endOnTick :false
			},
			legend: {
				align: 'center',
				verticalAlign: 'bottom',
				x: 0,
				y: 6,
				borderWidth: 0,
				backgroundColor: '#FFFFFF',
				shadow: false,
				itemStyle: {
					color: '#333'
				},
				labelFormatter: function() {
					return '<strong>'+this.name+'</strong>' +'<span style="color:#BBB">(点击隐藏)</span>';
				}
			},
			tooltip: {
				shadow: false,
				borderWidth: 0,
				backgroundColor: 'rgba(0,0, 0, .85)',
				style:{
					color: '#FFF',
					fontSize: '12px'
				},
				formatter: function() {
					//console.log(this.series);
					return ''+
						this.series.name +': '+ this.y + '' + this.series.options.unit;
				}
			},
			plotOptions: {
				column: {
					dataLabels: {
						enabled: false
					},
					shadow : false
				},
				series: {
					colorByPoint: false,
					animation: false,
					pointWidth: 12
				}
			},
			colors : ['#0099FF','#F7BA00','#00CC00'],
			exporting: {enabled: false},
			credits: {enabled: false}
		};
		Highcharts.setOptions({global: {useUTC: false}});
		chartsConfig.chart.renderTo = id;
		chartsConfig.xAxis.categories = cate;
		chartsConfig.series = data;
		var chart = new Highcharts.Chart(chartsConfig);
	}
};