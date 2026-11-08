<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
$nome = '';
$nasc = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $nasc = trim($_POST['birthdate'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $conf = $_POST['confirma'] ?? '';

    $d = DateTime::createFromFormat('Y-m-d', $nasc);
    $dataOk = $d && $d->format('Y-m-d') === $nasc && $d <= new DateTime('today');

    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        $erro = 'Sessão expirada. Recarregue a página e tente de novo.';
    } elseif ($nome === '' || $nasc === '' || $senha === '' || $conf === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (mb_strlen($nome) > 50) {
        $erro = 'O usuário pode ter no máximo 50 caracteres.';
    } elseif (!$dataOk) {
        $erro = 'Informe uma data de nascimento válida.';
    } elseif (strlen($senha) < 6 || strlen($senha) > 72) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $conf) {
        $erro = 'As senhas não são iguais.';
    } else {
        try {
            $pdo = pdo();
            $st = $pdo->prepare('SELECT 1 FROM usuarios WHERE nome = ?');
            $st->execute([$nome]);
            if ($st->fetch()) {
                $erro = 'Já existe um usuário com esse nome. Escolha outro.';
            } else {
                $pdo->prepare('INSERT INTO usuarios (nome, birthdate, senha) VALUES (?, ?, ?)')
                    ->execute([$nome, $nasc, password_hash($senha, PASSWORD_DEFAULT)]);
                $_SESSION['flash_ok'] = 'Cadastro realizado com sucesso! Entre com sua conta.';
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $ex) {
            $erro = 'Não foi possível salvar o cadastro. Verifique o banco de dados.';
        }
    }
}

require __DIR__ . '/inc/topo.php';
?>

<header class="navbar">
    <a class="navbar-marca" href="index.php" aria-label="Barbie — página inicial">Barbie</a>

    <nav class="navbar-links" aria-label="Navegação principal">
        <a href="index.php">Entrar</a>
    </nav>
</header>

<main class="capa" aria-label="Página de cadastro">
    <div class="painel-rosa" aria-hidden="true">
        <span class="assinatura">Barbie</span>
    </div>

    <div class="painel-creme" aria-label="Área de cadastro">
        <div class="circulo-decorativo" aria-hidden="true"></div>

        <div class="login-card-inline card-largo">
            <h2 class="login-titulo">Criar conta</h2>

            <?php if ($erro): ?>
                <div class="aviso erro" role="alert"><?= e($erro) ?></div>
            <?php endif; ?>

            <form method="post" action="cad.php" class="form-login">
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
                    <label for="birthdate">Data de nascimento</label>
                    <input id="birthdate" name="birthdate" type="date" max="<?= date('Y-m-d') ?>"
                        value="<?= e($nasc) ?>" required>
                </div>

                <div class="linha-dupla">
                    <div class="grupo-input">
                        <label for="senha">Senha</label>
                        <input id="senha" name="senha" type="password" minlength="6" maxlength="72"
                            autocomplete="new-password" placeholder="Mín. 6 caracteres" required>
                    </div>

                    <div class="grupo-input">
                        <label for="confirma">Repita a senha</label>
                        <input id="confirma" name="confirma" type="password" maxlength="72" autocomplete="new-password"
                            placeholder="Repita a senha" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Criar conta</button>
            </form>

            <div class="login-links-extra">
                <a href="index.php">Já tem conta? Entrar</a>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/inc/rodape.php'; ?>