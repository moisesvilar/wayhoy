<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo $url_absolute; ?>menu.php">PromoTV</a>
    </div>
    <ul class="nav navbar-right top-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $user['username']; ?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li class="divider"></li>
                <li>
                    <a href="?logout"><i class="fa fa-fw fa-power-off"></i> Cerrar sesión</a>
                </li>
            </ul>
        </li>
    </ul>

    <?php include(dirname(__FILE__).'/side.php'); ?>
</nav>