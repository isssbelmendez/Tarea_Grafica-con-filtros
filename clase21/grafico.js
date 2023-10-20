Highcharts.chart('container', {

    title: {
        text: 'Empresa XYZ',
        align: 'center'
    },

    subtitle: {
        text: 'Total de ventas anuales de los ultimos 10 años',
        align: 'center'
    },

    yAxis: {
        title: {
            text: 'Ventas en $'
        }
    },

    xAxis: {
        accessibility: {
            rangeDescription: 'Desde 2013 al 2022'
        }
    },

    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            pointStart: 2013
        }
    },

    series: [{
        name: 'Ventas anuales',

        data: [
            30000,25000,10000,30000,34000,25033,28900,90293,93220,23782]
    }],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
