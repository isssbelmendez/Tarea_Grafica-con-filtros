<?php
$server="localhost";
$user="root";
$pwd="";
$db="db_ventas";

$conexion = new mysqli($server,$user,$pwd,$db);
if($conexion)
{
    //echo "bueno";
}

if ($conexion) {
    // Conexión exitosa
    $totales = isset($_POST['totales']) ? $_POST['totales'] : "";
    $data = [];
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
        $data[] = number_format($dato[0], 2, '.', ''); // Formatea el valor
    }

    echo json_encode($data);
} else {
    // Error de conexión
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
}
?>
