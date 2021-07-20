    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <title>APAC - Controle de dividas</title>

    </head>

    <body>
        <div id="wrapper">
            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav metismenu" id="side-menu">
                        <li class="nav-header">
                            <div class="dropdown profile-element">
                                <img alt="image" class="rounded-circle" src="img/Logo_Adventista.png" width="50%">
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                    <span class="block m-t-xs font-bold">APAC - Sistemas<br>Confissão de dívidas</span>
                                </a>
                            </div>
                            <div class="logo-element">
                                <img alt="image" class="rounded-circle" src="img/Logo_Adventista.png" width="80%">
                            </div>
                        </li>
                        <?php
                        $end = $_SERVER['REQUEST_URI'];
                        $endereco = substr($end, 1, strpos($end, ".php") - 1);

                        $acessoUsuario = 0;
                        if(isset($_SESSION['usuarioAcesso'])){
                            $acessoUsuario = $_SESSION['usuarioAcesso'];
                        }

                        // case 1:
                        //     $acesso = "Vizualização";
                        //     break;
                        // case 2:
                        //     $acesso = "Vizualização/Edição";
                        //     break;
                        // case 3:
                        //     $acesso = "Vizualização/Edição/Administração";
                        //     break;
                        // default:
                        //     $acesso = "Nível de acesso indefinido";

                        if(isset($endereco)){
                        ?>
                         <li <?php if ($endereco == "index" || 
                            $endereco == "dashpagamentos" || 
                            $endereco == "dashcolegio") {
                                echo "class='active'";
                            } ?>>
                            <a href="index.php"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboards</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php if( $acessoUsuario == 3 ) { ?>
                                    <li <?php echo $endereco == "index" ? "class='active'" : ""; ?>><a href="index.php">Início</a></li>
                                    <li <?php echo $endereco == "dashcolegio" ? "class='active'" : ""; ?>><a href="dashcolegio.php">Colégios</a></li>
                                    <li <?php echo $endereco == "dashpagamentos" ? "class='active'" : ""; ?>><a href="dashpagamentos.php">Pagamentos</a></li>
                                <?php } else{ ?>
                                    <li <?php echo $endereco == "dashpagamentos" ? "class='active'" : ""; ?>><a href="dashpagamentos.php">Início</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li <?php if ($endereco == "aluno" ||
                            $endereco == "responsavel"  ||
                                $endereco == "colegio"  ||
                                $endereco == "dividas"  ||
                                $endereco == "parcelas" ||
                                $endereco == "usuario" || $acessoUsuario == 5 ) {
                                echo "class='active'";
                            } ?>>
                            <a href="index.html"><i class="fa fa-cog"></i> <span class="nav-label">Gerenciamento</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li <?php echo $endereco == "dividas" ? "class='active'" : ""; ?>><a href="dividas.php">Dívidas</a></li>
                                <li <?php echo $endereco == "parcelas" ? "class='active'" : ""; ?>><a href="parcelas.php">Parcelas</a></li>
                                <?php if( $acessoUsuario != 5 ) { ?>
                                <li <?php echo $endereco == "aluno" ? "class='active'" : ""; ?>><a href="aluno.php">Aluno</a></li>
                                <li <?php echo $endereco == "responsavel" ? "class='active'" : ""; ?>><a href="responsavel.php">Responsável</a></li>
                                <?php } ?>
                                <?php if( $acessoUsuario == 3 ) { ?>
                                    <li <?php echo $endereco == "colegio" ? "class='active'" : ""; ?>><a href="colegio.php">Colégio</a></li>
                                    <li <?php echo $endereco == "usuario" ? "class='active'" : ""; ?>><a href="usuario.php">Usuário</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php if( $acessoUsuario != 5 ) { ?>
                        <li <?php if ($endereco == "dividalancar") {
                                echo "class='active'";
                            } ?>>
                            <a href="index.html"><i class="fa fa-money"></i> <span class="nav-label">Dívidas</span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li <?php echo $endereco == "dividalancar" ? "class='active'" : ""; ?>><a href="dividalancar.php">Lançar</a></li>
                            </ul>
                        </li>
                        <?php } ?>
                        <?php if( $acessoUsuario == 3 ) { ?>
                            <li <?php if ($endereco == "aasidividas" || $endereco == "aasipessoas") {
                                    echo "class='active'";
                                } ?>>
                                <a href="index.html"><i class="fa fa-users"></i> <span class="nav-label">Vizualizar no AASI</span> <span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li <?php echo $endereco == "aasidividas" ? "class='active'" : ""; ?>><a href="aasidividas.php">Dívidas</a></li>
                                    <li <?php echo $endereco == "aasipessoas" ? "class='active'" : ""; ?>><a href="aasipessoas.php">Pessoas</a></li>
                                </ul>
                            </li>
                            <li <?php if ($endereco == "aasiimportarparcelas" || $endereco == "aasiimportardividas") {
                                    echo "class='active'";
                                } ?>>
                                <a href="index.html"><i class="fa fa-cloud-download"></i> <span class="nav-label">Importar do ASSI</span> <span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li <?php echo $endereco == "aasiimportardividas" ? "class='active'" : ""; ?>><a href="aasiimportardividas.php">Importar Dívidas</a></li>
                                    <li <?php echo $endereco == "aasiimportarparcelas" ? "class='active'" : ""; ?>><a href="aasiimportarparcelas.php">Importar Parcelas</a></li>
                                </ul>
                            </li>
                            <li <?php if ($endereco == "aasiexportardividas") {
                                    echo "class='active'";
                                } ?>>
                                <a href="index.html"><i class="fa fa-share-square"></i> <span class="nav-label">Exportar para ASSI</span> <span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li <?php echo $endereco == "aasiexportardividas" ? "class='active'" : ""; ?>><a href="aasiexportardividas.php">Exportar Dívidas</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-success " href="#"><i class="fa fa-bars"></i> </a>
                        </div>
                        <ul class="nav navbar-top-links navbar-left">
                            <li>
                                <span class="m-r-sm text welcome-message"><?php if(isset($_SESSION['usuarioNome'])){ echo $endereco == "index" ? "Bem vindo, " : "<i class='fa fa-user'></i>  "; ?>
                                <b><?php echo $_SESSION['usuarioNome']; } }?></b></span>
                            </li>
                        </ul>
                        <ul class="nav navbar-top-links navbar-right">
                            <li>
                                <a href="logout.php">
                                    <i class="fa fa-sign-out"></i> Sair
                                </a>
                            </li>
                        </ul>
                    </nav>