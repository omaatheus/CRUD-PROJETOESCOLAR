<?php
class Usuario {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function listarUsuarios() {
        $usuarios = array();

        // prepare a consulta SQL para listar todos os usuários
        $sql = "SELECT * FROM usuarios";

        // preparar e executar a consulta
        $result = $this->db->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
            $result->close();
        }

        return $usuarios;
    }

    public function adicionarUsuario($nome, $email, $senha, $caminhoImagem) {
        // verifique se o email já está em uso
        if ($this->verificarEmailExistente($email)) {
            return false; // email já está em uso, não é possível adicionar o usuário.
        }
        // hash da senha para maior segurança
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
            // inserir um novo usuário na tabela
        $sql = "INSERT INTO usuarios (usu_nome, usu_email, usu_senha, usu_foto_perfil) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssss", $nome, $email, $senhaHash, $caminhoImagem);
        
        if ($stmt->execute()) {
            return true; // usuário adicionado com sucesso.
        } else {
            return false; // erro ao adicionar o usuário.
        }
    }

    public function verificarEmailExistente($email) {
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE usu_email = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] > 0;
    }

    public function atualizarUsuario($id, $nome, $email, $novaSenha, $caminhoNovaImagem) {
        // obtenha o caminho da imagem antiga do usuário
        $caminhoImagemAntiga = $this->obterCaminhoImagemUsuario($id);
    
        if (!empty($novaSenha)) {
            // atualizar nome, email, senha e caminho da imagem
            $sql = "UPDATE usuarios SET usu_nome = ?, usu_email = ?, usu_senha = ?, usu_foto_perfil = ? WHERE usu_id = ?";
            
            // hash da nova senha para maior segurança
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssssi", $nome, $email, $senhaHash, $caminhoNovaImagem, $id);
        } else {
            // atualizar apenas nome, email e caminho da imagem
            $sql = "UPDATE usuarios SET usu_nome = ?, usu_email = ?, usu_foto_perfil = ? WHERE usu_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $caminhoNovaImagem, $id);
        }
        
        if ($stmt->execute()) {
            // exclua a imagem antiga se existir e se a atualização for bem-sucedida
            if (!empty($caminhoImagemAntiga) && file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }
    
            return true; // usuário atualizado com sucesso.
        } else {
            return false; // erro ao atualizar o usuário.
        }
    }

    // função para obter o caminho da imagem antiga do usuário
    private function obterCaminhoImagemUsuario($id) {
        $sql = "SELECT usu_foto_perfil FROM usuarios WHERE usu_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($caminhoImagem);
        
        // verifique se a consulta retornou resultados
        if ($stmt->fetch()) {
            $stmt->close();
            return $caminhoImagem;
        } else {
            $stmt->close();
            return null; // retorna null se não houver imagem associada ao usuário
        }
    }

    public function buscarUsuarioPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE usu_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            $stmt->close();
            return $usuario;
        } else {
            return null; // usuário não encontrado.
        }
    }

    public function atualizarUsuarioSemSenha($id, $nome, $email) {
        $sql = "UPDATE usuarios SET usu_nome = ?, usu_email = ? WHERE usu_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $nome, $email, $id);
        
        if ($stmt->execute()) {
            // return true; // usuário atualizado com sucesso.
            header("location: ../public/index.php");
        } else {
            return false; // erro ao atualizar o usuário.
        }
    }

    public function deletarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE usu_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // return true; // Usuário excluído com sucesso.
            header("location: ../public/index.php");
        } else {
            return false; // erro ao excluir o usuário.
        }
    }

} //fecha a Classe


?>