<?php
require_once('ini.php');
require_once('include/administrador_db.php');

$where = '';

$rows = get_administrators();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>PromoTV - Panel de administración</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/plugins/morris.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div id="wrapper">
    <?php include(dirname(__FILE__).'/partials/nav.php'); ?>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Administradores <small>Listado de administradores vinculados a esta cuenta</small>
                    </h1>
                    <ol class="breadcrumb col-lg-6">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="<?php echo $url_absolute; ?>menu.php">Principal</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-users"></i> Administradores
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    if ($error) echo $msg_error;
                    elseif($success) echo $msg_success;
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="text-left"></th>
                                <?php
                                $row = $rows[0];
                                foreach($row as $key=>$value) {
                                ?>
                                    <th class="text-center"><?php echo $key; ?></th>
                                <?php
                                }
                                ?>
                                <th class="text-right">Editar</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $cont = 0;
                            foreach($rows as $row) {
                                $cont++;
                                ?>
                                <tr>
                                    <td class="text-left"><?php echo $cont; ?></td>
                                    <?php
                                    foreach($row as $key=>$value) {
                                        ?>
                                        <td class="text-center">
                                            <?php echo $value; ?>
                                        </td>
                                    <?php
                                    }
                                    ?>
                                    <td class="text-right"><a href="<?php echo $url_absolute; ?>administradores_write.php?id=<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></a></td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="js/custom.js"></script>
</body>

</html>
