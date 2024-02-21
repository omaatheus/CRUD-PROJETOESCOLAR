<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários</title>
</head>
<body>
    <h1>Usuários Cadastrados</h1>

    <?php
        // inclua seus arquivos PHP aqui (Database.php e Usuario.php) e crie uma instância da classe Usuario.
        include('../classes/Database.php');
        include('../classes/Usuario.php');
        $conexao = (new Database())->conectar();
        $usuario = new Usuario($conexao);

        // listar os usuários
        $usuarios = $usuario->listarUsuarios();

        // loop para exibir a lista
        foreach ($usuarios as $user) {
            echo '<div>';
            echo '<p>Nome: ' . $user['usu_nome'] . '</p>';
            echo '<p>Email: ' . $user['usu_email'] . '</p>';
            echo '<p>Thumb: ' .'<img src="'.$user['usu_foto_perfil'] . '" style="width:10%;" /></p>';
            echo '<a href="atualizar.php?id=' . $user['usu_id'] . '"><button>Atualizar</button></a>';
            echo '<a href="excluir.php?id=' . $user['usu_id'] . '"><button>Excluir</button></a>';
            echo '</div>';
            echo '<hr>';
        }
    ?>

</body>
</html>