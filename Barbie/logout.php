<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se a sessão guardou o valor de quando você entrou, vamos reverter o contador
if (isset($_SESSION['visitas_inicio_login'])) {
    $valor_inicial = $_SESSION['visitas_inicio_login'];
    
    $fp = fopen(__DIR__ . '/arq.in', 'c+')
        or die('Sem permissão para gravar o arquivo arq.in.');
    
    flock($fp, LOCK_EX);
    
    // Lê o número atual do arquivo (com os F5 que você deu dentro da conta)
    $n_atual = (int) stream_get_contents($fp);
    
    // Descobre quantos acessos foram feitos SÓ por você enquanto logada
    $acessos_da_conta = $n_atual - $valor_inicial;
    
    // Se por acaso outras pessoas acessaram o site nesse meio tempo, 
    // nós tiramos apenas os seus cliques e mantemos o dos outros.
    // Ou, se você quiser voltar estritamente para o valor exato de quando entrou:
    $novo_valor = $valor_inicial; 
    
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) max(0, $novo_valor)); // Garante que não fique negativo

    flock($fp, LOCK_UN);
    fclose($fp);
}

// Destroi a sessão e manda para o login
session_destroy();
header("Location: login.php");
exit;
?>