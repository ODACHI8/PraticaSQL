<?php

// Inclui os arquivos necessários do PHPMailer
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Obtém os dados do formulário
$nome = $_POST["nome"];
$endereco = $_POST["endereco"];
$data_nascimento = $_POST["data_nascimento"];
$telefone = $_POST["telefone"];
$email = $_POST["email"];

// Prepara a query SQL para inserir os dados na tabela, com proteção contra SQL Injection, atraves de uma consulta de variaveis externas que vao ser fornecidas depois,ok
$stmt = $conn->prepare("INSERT INTO Usuarios (nome, endereco, data_nascimento, telefone, email) VALUES (?, ?, ?, ?, ?)");

if ($stmt) {
    // Vincula os parâmetros à instrução preparada 
    $stmt->bind_param("sssss", $nome, $endereco, $data_nascimento, $telefone, $email);

    // Executa a query com valores ja validados
    if ($stmt->execute()) {
        // Configuração e envio do e-mail
        $mail = new PHPMailer(true);

        try {
            // Configuração do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bl4ckb0xsteste@gmail.com'; 
            $mail->Password = ''; // a senha gerada pelo google "app password"
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remetente e destinatário
            $mail->setFrom('bl4ckb0xsteste@gmail.com', 'Black Box Studios');
            $mail->addAddress($email, $nome);

            // Conteúdo do email
            $mail->isHTML(false);
            $mail->Subject = 'Cadastro - Black Box Studios';
            $mail->Body = "Olá $nome,\n\nSeu cadastro foi realizado com sucesso na BlackBox Studios!\n\nObrigado por se cadastrar!";

            // Envia o email
            $mail->send();

            // Redireciona para a página de sucesso
            header("Location: deucerto.html");
            exit;
        } catch (Exception $e) {
            echo "<br>Erro ao enviar o email de confirmação. Erro: {$mail->ErrorInfo}";
        }
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    // Fecha a instrução preparada
    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $conn->error;
}

// Fecha a conexão com o banco de dados
$conn->close();

?>

