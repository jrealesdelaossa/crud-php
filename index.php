<?php
$id=(isset($_POST["identificacion"]))? $_POST["identificacion"] : "";
$nombres=(isset($_POST["nombre"]))? $_POST["nombre"] : "";
$apellido_p=(isset($_POST["apellido_p"]))? $_POST["apellido_p"] : "";
$apellido_m=(isset($_POST["apellido_m"]))? $_POST["apellido_m"] : "";
$correo=(isset($_POST["correo"]))? $_POST["correo"] : "";
$foto=(isset($_FILES["foto"]["name"]))? $_FILES["foto"]["name"] : "";
$api = "https://api.escuelajs.co/api/v1/products";

$action = (isset($_POST['action'])) ? $_POST["action"] : "";
include ("conexion/conexion.php");

switch ($action) {

    case 'guardar':
       //echo "Usted activo el boton guardar";
       $sql=$pdo->prepare("INSERT INTO empleado 
       (identificacion, nombre, apellido_p, apellido_m, correo, foto)
    VALUES (:identificacion,:nombres,:apellido_p,:apellido_m,:correo,:foto)");
        $sql->bindParam(':identificacion',$id);
        $sql->bindParam(':nombres',$nombres);
        $sql->bindParam(':apellido_p',$apellido_p);
        $sql->bindParam(':apellido_m',$apellido_m);
        $sql->bindParam(':correo',$correo);
        $fecha=new DateTime();
        $nombre_archivo=($foto!="")?$fecha->getTimestamp()."_".$_FILES["foto"]["name"]:"imagen.jpg";
        $tm_foto=$_FILES["foto"]["tmp_name"];
        if ($tm_foto!="") {
       
        move_uploaded_file($tm_foto,"img/".$nombre_archivo);
        }
        $sql->bindParam(':foto',$nombre_archivo);
        $sql->execute();
        break;

    case 'modificar':
    //echo "Usted activo el boton modificar";
    try {
        $sql=$pdo->prepare("UPDATE `empleado` SET
         identificacion=:id,
         `nombre`=:nombres,
         `apellido_p`=:apellido_p,
         `apellido_m`=:apellido_m,
         `correo`=:correo
         /*`foto`=:foto*/
        WHERE identificacion=:id");
        $sql->bindparam(':nombres',$nombres);
        $sql->bindparam(':apellido_p',$apellido_p);
        $sql->bindparam(':apellido_m',$apellido_m);
        $sql->bindparam(':correo',$correo);
        /*$sql->bindparam(':foto',$foto);*/
        $sql->bindparam(':id',$id);
        $sql->execute();
        $fecha=new DateTime();
        $nombre_archivo=($foto!="")?$fecha->getTimestamp()."_".$_FILES["foto"]["name"]:"imagen.jpg";
        $tm_foto=$_FILES["foto"]["tmp_name"];
        if ($tm_foto!="") {
            move_uploaded_file($tm_foto,"img/".$nombre_archivo);
            $sql=$pdo->prepare("SELECT foto FROM empleado WHERE identificacion=:id");   
            $sql->bindparam(':id',$id);
            $sql->execute();
            $eliminarfoto=$sql->fetch(PDO::FETCH_LAZY);
            //print_r($eliminarfoto);
            if(isset($eliminarfoto["foto"])){
                if(file_exists("img/".$eliminarfoto["foto"])){
                    if($item['foto']!="imagen.jpg"){
                        unlink("img/".$eliminarfoto["foto"]);
                    }
                }
                
            }
        
            $sql=$pdo->prepare("UPDATE empleado SET foto=:foto WHERE identificacion=:id");
            $sql->bindparam(':foto',$nombre_archivo);
            $sql->bindParam(':id',$id);
            $sql->execute();
              

            
        }

    
    } catch (PDOException $e) {
        echo ("Erorr al modificar datos" . $e->getMessage()); 
    }
   
        
    break;

    case 'eliminar':
        //echo "Usted activo el boton eliminar";
        $sentencia=$pdo->prepare("SELECT foto from empleado WHERE identificacion=:id ");
        $sentencia->bindParam(':id',$id);
        $sentencia->execute();

        $eliminarfoto=$sentencia->fetch(PDO::FETCH_LAZY);
        if(isset($eliminarfoto["foto"])&&($item['foto']!="imagen.jpg")){
            if(file_exists("img/".$eliminarfoto["foto"])){
                    unlink("img/".$eliminarfoto["foto"]);
            }
        }

        $sentencia=$pdo->prepare("DELETE FROM empleado WHERE identificacion=:id");
        $sentencia->bindParam(':id',$id);
        $sentencia->execute();
        break;

    case 'cancelar':
        echo "Usted activo el boton cancelar";
        break;

    }
    $sentencia=$pdo->prepare('SELECT *FROM empleado');
    $sentencia->execute();
    $listaempleado=$sentencia->fetchAll(PDO::FETCH_ASSOC);
            // print_r($listaempleado);//
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/logo.ico" type="image/x-icon">
    <title>Formulario</title>
</head>

<body>
<?php echo $photo ?>
<form action="" method="post" enctype="multipart/form-data" >
        <center>
            <h1>Registro de empleados</h1>
            <label for="">Identificacion</label>
            <input type="text" name="identificacion" id="identificacion" value="<?php echo $id;?>"  >
            <br><br>
            <label for="">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo $nombres;?>"  >
            <br><br>
            <label for="">Apellido paterno</label>
            <input type="text" name="apellido_p" id="apellido_p" value="<?php echo $apellido_p;?>" placeholder="" >
            <br><br>
            <label for="">Apellido materno</label>
            <input type="text" name="apellido_m" id="apellido_m"  value="<?php echo $apellido_m;?>" placeholder="" >
            <br><br>
            <label for="">Correo</label>
            <input type="text" name="correo" id="correo" value="<?php echo $correo;?>" placeholder="">
            <br><br>
            <label for="">Foto</label>
            <input type="file" accept="image/*" value="<?php echo $foto;?>" name="foto" id="foto" >

            <br><br>
            <button type="submit" value="guardar" name="action" class="btn btn-primary">Guardar</button>
            <button type="submit" value="modificar" name="action" class="btn btn-success">Modificar</button>
            <button type="submit" value="eliminar" name="action" class="btn btn-danger">Eliminar</button>
            <button type="submit" value="cancelar" name="action" class="btn btn-warning">Cancelar</button>
        </center>
    </form>
    <div class="row">
        <center>
      <table class="table">
          <thead>
             <tr>
                <th>identificacion</th>
                <th>nombres</th>
                <th>apellidos</th>
                <th>correo</th>
                <th>foto</th>
             </tr>
          </thead>
          <?php foreach($listaempleado as $empleado){ ?>
               <tr>           
                <td> <?php echo $empleado['identificacion']; ?> </td>
                <td> <?php echo $empleado['nombre']; ?> </td>
                <td> <?php echo $empleado['apellido_p']; ?> 
                <?php echo $empleado['apellido_m']; ?> </td>
                <td> <?php echo $empleado['correo']; ?> </td>
                <td><img src="img/<?php echo $empleado['foto'];?>" alt="" class="img-thumbnaim" width="100px">  </td>
                <td>
                    <form action="" method="POST">
                        <input type="hidden" name="identificacion" value="<?php echo $empleado['identificacion'];  ?>">
                        <input type="hidden" name="nombre" value="<?php echo $empleado['nombre'];  ?>">
                        <input type="hidden" name="apellido_p" value="<?php echo $empleado['apellido_p'];  ?>">
                        <input type="hidden" name="apellido_m" value="<?php echo $empleado['apellido_m'];  ?>">
                        <input type="hidden" name="correo" value="<?php echo $empleado['correo'];  ?>">
                        <input type="hidden" name="foto" value="<?php echo $empleado['foto'];  ?>">
                        <input type="submit" value="seleccionar" name="action" class="btn btn-primary">
                        <button value="eliminar" type="submit" name="action" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
               </tr>

            <?php } ?>

      </table>
      </center>
    </div>
       
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>