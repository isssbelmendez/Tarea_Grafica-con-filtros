<?php
include_once 'conexion.php';
$years = isset($_POST['years']) ? $_POST['years'] : [];

if (isset($_POST['ajax_request']) && $_POST['ajax_request'] == 1) {
    $data = [];

    $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fact
            INNER JOIN encabezado_fact ON detalle_fact.codigo=encabezado_fact.codigo
            GROUP BY YEAR(fecha)
            HAVING sum(venta) >= $totales";

    $executar = mysqli_query($conexion, $consulta);
    while ($dato = mysqli_fetch_array($executar)) {
        $data[] = number_format($dato[0], 2, '.', ''); // Formatea el valor
    }
    echo json_encode($data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <title>Gráfica con Filtros</title>
</head>

<body>
    <form action="index.php" method="POST" style="text-align:center">
        <hr>
        <br>
        <label for="">Totales anuales >= que: </label>

        <input type="text" name="totales" id="totales" value="<?php echo isset($_POST['totales']) ? $_POST['totales'] : "" ?>">
        <?php
        /* $anios = "SELECT DISTINCT year(fecha) as anio 
            FROM encabezado_fact WHERE year(fecha) BETWEEN 2013 and 2022 order by(fecha) asc;";
        $ejecucion = mysqli_query($conexion, $anios);

        while ($seleccionAnios = mysqli_fetch_array($ejecucion)) {
            $existe = '';
            if (in_array($seleccionAnios[0], $years)) {
                $existe = 'checked';
            }
            echo "<label>" . $seleccionAnios[0] . "</label>";
            echo "<input name='years[]' $existe  value='" . intval($seleccionAnios[0]) . "' type='checkbox' name='' id=''>";
        }*/
        ?>
        <!--<input type="submit" value="Graficar">-->
    </form>
    <hr>
    <figure class="highcharts-figure">
        <div id="container"></div>
    </figure>
</body>

</html>

<script>
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
            data: [<?php
                    $totales = isset($_POST['totales']) ? $_POST['totales'] : "";

                    if (!empty($years) && $totales !== "") {
                        $whereConditions = [];
                        foreach ($years as $y) {
                            $formattedYear = intval($y);
                            $whereConditions[] = "fecha LIKE '%$formattedYear%'";
                        }
                        $whereClause = implode(" OR ", $whereConditions);

                        $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fact
                    INNER JOIN encabezado_fact ON detalle_fact.codigo=encabezado_fact.codigo
                    WHERE ($whereClause)
                    GROUP BY YEAR(fecha)
                    HAVING sum(venta) >= $totales";
                    } else if ($totales !== "") {
                        $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fact
                    INNER JOIN encabezado_fact ON detalle_fact.codigo=encabezado_fact.codigo
                    GROUP BY YEAR(fecha)
                    HAVING sum(venta) >= $totales";
                    } else if (!empty($years)) {
                        $whereConditions = [];
                        foreach ($years as $y) {
                            $formattedYear = intval($y);
                            $whereConditions[] = "fecha LIKE '%$formattedYear%'";
                        }
                        $whereClause = implode(" OR ", $whereConditions);

                        $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fact
                    INNER JOIN encabezado_fact ON detalle_fact.codigo=encabezado_fact.codigo
                    WHERE ($whereClause)
                    GROUP BY YEAR(fecha)";
                    } else {
                        $consulta = "SELECT sum(venta) as venta, fecha FROM detalle_fact
                    INNER JOIN encabezado_fact ON detalle_fact.codigo=encabezado_fact.codigo
                    GROUP BY YEAR(fecha)";
                    }

                    $executar = mysqli_query($conexion, $consulta);

                    while ($dato = mysqli_fetch_array($executar)) {
                        $d = number_format($dato[0], 2, '.', '');
                        echo $d . ",";
                    }
                    ?>]
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


    let totales = document.getElementById('totales').value;

    // 
    document.getElementById('totales').addEventListener('change', ActualizarGrafico);

    Highcharts.chart('container', {
        // ... (your existing Highcharts options)

        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                },
                pointStart: 2013 // Set to the starting year of your data
            }
        },

        series: [{
            name: 'Ventas anuales',
            data: []
        }]
    });

    // Función para actualizar la gráfica
    function ActualizarGrafico() {
        var totales = document.getElementById('totales').value;
        $.ajax({
            type: "POST",
            url: "index.php",
            data: {
                totales: totales,
                ajax_request: 1
            },
            success: function (data) {
                var chartData = JSON.parse(data);
                var chart = Highcharts.charts[0];
                chart.series[0].setData(chartData);
            }
        });
    }

    // Manejar el evento 'input' del campo de totales
    $('#totales').on('input', ActualizarGrafico);

    // Llamada inicial para cargar la gráfica
    ActualizarGrafico();
</script>
</script>

<script src="/clase21/grafico.js"></script>