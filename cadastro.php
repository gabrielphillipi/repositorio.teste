<?php
//instanciando classe

$u = new usuario;
//Coleta de dados 

//verificar se clicou no botão 
if(isset($_POST['nome_usuario'])){
    $nome_usuario = addslashes($_POST['nome_usuario']);
    $email_usuario = addslashes($_POST['email_usuario']);
    $senha = addslashes($_POST['senha']);
    $confirmar_senha = addslashes($_POST['confirmar_senha']);
    $estado = addslashes($_POST['estado']);
    $cidade = addslashes($_POST['cidade']);
    $bairro = addslashes($_POST['bairro']);
    $rua = addslashes($_POST['rua']);
    $numero_casa = addslashes($_POST['numero_casa']);
    $num_hidrometro = addslashes($_POST['num_hidrometro']);
    $valor_limite = addslashes($_POST['valor_limite']);
//verificar campos em branco
    if (!empty($nome_usuario) && !empty($email_usuario) && !empty($senha) && !empty($confirmar_senha) && !empty($estado)
    && !empty($cidade) && !empty($bairro) && !empty($rua) && !empty($numero_casa) && !empty($num_hidrometro) && !empty($valor_limite))
    {
        $u->conectar("tcc","localhost","root","");
        if($u->msg_error == "")/*tudo certo*/{
            if($senha == $confirmar_senha){
                if($u->cadastrar($nome_usuario, $email_usuario, $senha, $estado, $cidade, $bairro, $rua, $numero_casa, $num_hidrometro, $valor_limite))
                {
                    echo "Cadastrado com sucesso! Acesse para entrar";
                }
                else{
                    echo "Email já cadastrado"; 
                }
            }
            else{
                echo "Os campos Senha e Confirmar senha devem ser iguais!";
            }
            
        }
        else{   
            echo  "ERRO:".$u->msg_error;
        }
    }
    else{
        echo "Preencha todos os campos!";
    }
}
 



// Classe 
class usuario{
    
    private $pdo;
    public $msg_error = "";
    public function conectar($nome, $host, $usuario, $senha){
        global $pdo;
        try {
            $pdo = new PDO("mysql:dbname=".$nome.";host=".$host, $usuario, $senha);
        } catch (PDOException $e) {
            global $msg_error;
            $msg_error = $e->getMessage(); 
        }
        
    }

    public function cadastrar($nome_usuario, $email_usuario, $senha, $estado, $cidade, $bairro, $rua, $numero_casa, $num_hidrometro, $valor_limite){
        global $pdo;
        //verificar se o email ja esta cadastrado
        $sql = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :e");
        $sql->bindValue(":e",$email_usuario);
        $sql->execute();

        if($sql->rowCount() > 0){
            return false; //ja esta cadastrado 
        }
        else{
        //caso nao esteja 
            $sql = $pdo->prepare("INSERT INTO usuario (nome_usuario, email, senha, estado, cidade, bairro, rua, numero_casa, num_hidrometro, valor_limite)
            VALUES (:NOME, :EMAIL, :SENHA, :ESTADO, :CIDADE, :BAIRRO, :RUA, :NUMERO_CASA, :NUM_HIDROMETRO, :VALOR_LIMITE)");
            $sql->bindValue(":NOME",$nome_usuario);
            $sql->bindValue(":EMAIL",$email_usuario);
            $sql->bindValue(":SENHA",md5($senha));
            $sql->bindValue(":ESTADO",$estado);
            $sql->bindValue(":CIDADE",$cidade);
            $sql->bindValue(":BAIRRO",$bairro);
            $sql->bindValue(":RUA",$rua);
            $sql->bindValue(":NUMERO_CASA",$numero_casa);
            $sql->bindValue(":NUM_HIDROMETRO",$num_hidrometro);
            $sql->bindValue(":VALOR_LIMITE",$valor_limite);
            $sql->execute();

            return true; //cadastrado com sucesso

        }
    }

    public function logar($email_usuario, $senha){
        global $pdo;
        //verificar email e senha 
        $sql = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :e AND senha = :s");
        $sql->bindValue(":e",$email_usuario);
        $sql->bindValue(":s",md5($senha));
        $sql->execute();
        if ($sql->rowCount() > 0){
            //iniciar sessao
            $dado = $sql->fetch();
            session_start();
            $_SESSION['id_usuario'] = $dado['id_usuario'];
            return true; //logado com sucesso 
        }
        else{
            return false; //nao foi possivel logar 
        }
        
    }

}






?>
