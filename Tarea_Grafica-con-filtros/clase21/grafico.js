$(document).ready(function () {
    function ActualizarGrafico( totales) {
        $.ajax({
            type: "POST",
            url: "conexion.php", 
            data: {
                years: years,
                totales: totales
            },
            success: function (data) {
                // Parsea los datos JSON recibidos
                var chartData = JSON.parse(data);

                // Configura la gráfica con los nuevos datos
                Highcharts.chart('container', {
                    title: {
                        text: 'Empresa XYZ',
                        align: 'center'
                    },
                    subtitle: {
                        text: 'Total de ventas anuales de los últimos 10 años',
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
                        data: chartData
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
            }
        });
    }

    // Maneja cambios en el formulario
    $("#totales").on('input', function () {
        var totales = $("#totales").val();
        ActualizarGrafico( totales);
    });
});



