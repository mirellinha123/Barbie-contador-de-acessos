<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

require __DIR__ . '/config.php';
require __DIR__ . '/contador.php';

$visitas = numero_visitas();
$titulo = 'Barbie';
$classe = 'barbie-page';

$erro = '';
$nome = '';
$ok = $_SESSION['flash_ok'] ?? '';
unset($_SESSION['flash_ok']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        $erro = 'Sessão expirada. Recarregue a página.';
    } elseif ($nome === '' || $senha === '') {
        $erro = 'Preencha o usuário e a senha.';
    } else {
        try {
            $st = pdo()->prepare('SELECT id, nome, senha FROM usuarios WHERE nome = ?');
            $st->execute([$nome]);
            $u = $st->fetch(PDO::FETCH_ASSOC);
            $guardada = $u ? (string) $u['senha'] : '';
            $eHash = password_get_info($guardada)['algo'] !== null;

            $confere = $u && ($eHash ? password_verify($senha, $guardada) : hash_equals($guardada, $senha));
            if ($confere) {
                if (!$eHash || password_needs_rehash($guardada, PASSWORD_DEFAULT)) {
                    pdo()->prepare('UPDATE usuarios SET senha = ? WHERE id = ?')
                        ->execute([password_hash($senha, PASSWORD_DEFAULT), $u['id']]);
                }
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = (int) $u['id'];
                $_SESSION['usuario_nome'] = $u['nome'];
                header('Location: index.php');
                exit;
            }
            usleep(400000);
            $erro = 'Usuário ou senha incorretos.';
        } catch (PDOException $ex) {
            $erro = 'Erro ao conectar ao banco de dados.';
        }
    }
}
// Lê o valor atual do arquivo para guardar como ponto de partida da sua sessão
$n_atual = (int) file_get_contents(__DIR__ . '/arq.in');
$_SESSION['visitas_inicio_login'] = $n_atual;

require __DIR__ . '/inc/topo.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbie-Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<header class="navbar">
    <div class="navbar-left">
        <span class="navbar-contador" aria-label="<?= e(number_format($visitas, 0, ',', '.')) ?> visitas">
            <?= e(number_format($visitas, 0, ',', '.')) ?> visitas
        </span>
    </div>

    <a class="navbar-marca" href="login.php" aria-label="Barbie — página inicial">Barbie</a>

    <nav class="navbar-links">
        <a href="cad.php">
            <img src="img/usuario.png" alt="" style="width: 60px; height: 60px; object-fit: contain;">
        </a>
    </nav>
</header>

<main class="capa" aria-label="Página de login">
    <div class="painel-rosa" aria-hidden="true">
        <span class="assinatura">Barbie</span>
        <div class="circulo-decorativo"></div>
    </div>

    <div class="painel-creme" aria-label="Área de login">
        <div class="login-card-inline">
            <h2 class="login-titulo">Login</h2>

            <?php if ($ok): ?>
                <div class="aviso ok"><?= e($ok) ?></div>
            <?php endif; ?>
            <?php if ($erro): ?>
                <div class="aviso erro" role="alert"><?= e($erro) ?></div>
            <?php endif; ?>

            <!-- Como este arquivo agora se chama login.php, o action aponta para ele mesmo -->
            <form method="post" action="login.php" class="form-login">
                <?php if (isset($_SESSION['csrf'])): ?>
                    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                <?php endif; ?>

                <div class="grupo-input">
                    <label for="nome">Usuário</label>
                    <div class="input-com-icone">
                        <img src="img/barbie.png" alt="Usuário" class="icone-silhueta-img">
                        <input id="nome" name="nome" maxlength="50" value="<?= e($nome) ?>" autocomplete="username"
                            placeholder="Seu usuário" required>
                    </div>
                </div>

                <div class="grupo-input">
                    <label for="senha">Senha</label>
                    <input id="senha" name="senha" type="password" maxlength="72" autocomplete="current-password"
                        placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Entrar</button>
            </form>

            <div class="login-links-extra">
                <a href="cad.php">Criar conta</a>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/inc/rodape.php'; ?>