<?php
/*
 * Controle de visitas:
 * - Conta para todos (logados e deslogados).
 * - Salva o valor inicial da sessão no login para reverter no logout, se necessário.
 */
function numero_visitas(): int
{
    $fp = fopen(__DIR__ . '/arq.in', 'c+')
        or die('Sem permissão para gravar o arquivo arq.in.');
    
    flock($fp, LOCK_EX);
    $n = (int) stream_get_contents($fp);

    // Incrementa a cada acesso/atualização para todo mundo
    $n++;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, (string) $n);

    flock($fp, LOCK_UN);
    fclose($fp);
    
    return $n;
}