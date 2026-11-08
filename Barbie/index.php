<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// Se não estiver logado, vai direto para a tela de login
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit;
}

require __DIR__ . '/config.php';
require __DIR__ . '/contador.php';

$visitas = numero_visitas();
$titulo = 'Barbie — Linha do Tempo';
$classe = 'barbie-page';
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Visitante';
?>


<?php require __DIR__ . '/inc/topo.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbie</title>
    <link rel="stylesheet" href="style.css">
</head>


<header class="navbar">
  <div class="navbar-left">
    <span class="navbar-contador" aria-label="<?= e(number_format($visitas, 0, ',', '.')) ?> visitas">
      <?= e(number_format($visitas, 0, ',', '.')) ?> visitas
    </span>
  </div>

  <a class="navbar-marca" href="index.php" aria-label="Barbie — página inicial">Barbie</a>

  <nav class="navbar-links">
    <span
      style="color: var(--vinho); font: 600 .75rem/1 'Times New Roman', Times, Georgia, serif; text-transform: uppercase;">Olá,
      <?= e($nomeUsuario) ?></span>
    <a href="logout.php">
      <img src="img/logaut.png" alt=""
        style="width: 60px; height: 60px; object-fit: contain; transform: rotate(180deg);">
    </a>
  </nav>
</header>

<main class="capa-timeline" aria-label="Linha do tempo das Barbies">
  <div class="timeline-container">
    <h2 class="timeline-titulo">Linha do Tempo</h2>

    <div class="timeline-horizontal">
      <div class="timeline-item">
        <span class="timeline-ano">1959</span>
        <div class="timeline-card">
          <p>A primeira Barbie clássica em traje de banho listrado.</p>
        </div>
      </div>

      <div class="timeline-item">
        <span class="timeline-ano">1970</span>
        <div class="timeline-card">
          <p>A era Superstar Barbie, glamour e sorrisos brilhantes.</p>
        </div>
      </div>

      <div class="timeline-item">
        <span class="timeline-ano">1980</span>
        <div class="timeline-card">
          <p>Barbie Fashion Icon, cores vibrantes e muita atitude.</p>
        </div>
      </div>

      <div class="timeline-item">
        <span class="timeline-ano">2000</span>
        <div class="timeline-card">
          <p>Era moderna, filmes clássicos em animação e coleções.</p>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/inc/rodape.php'; ?>