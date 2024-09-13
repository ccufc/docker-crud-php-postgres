<?php
$host = 'db';
$dbname = 'testdb';
$user = 'postgres';
$password = 'password';

try {
  $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Conexão com o PostgreSQL foi bem-sucedida!<br>";

  $sql = "
    CREATE TABLE IF NOT EXISTS users (
      id SERIAL PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(100) NOT NULL UNIQUE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
  $pdo->exec($sql);
  echo "Tabela 'users' criada com sucesso!<br>";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (!empty($name) && !empty($email)) {
      $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);

      try {
        $stmt->execute();
        echo "Usuário criado com sucesso!<br>";
      } catch (PDOException $e) {
        echo "Erro ao criar usuário: " . $e->getMessage();
      }
    } else {
      echo "Nome e e-mail são obrigatórios!<br>";
    }
  }

  $sql = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($users) {
    echo "<h2>Lista de Usuários</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Criado em</th></tr>";
    foreach ($users as $user) {
      echo "<tr>";
      echo "<td>" . htmlspecialchars($user['id']) . "</td>";
      echo "<td>" . htmlspecialchars($user['name']) . "</td>";
      echo "<td>" . htmlspecialchars($user['email']) . "</td>";
      echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "Nenhum usuário encontrado.<br>";
  }
} catch (PDOException $e) {
  echo "Erro na conexão: " . $e->getMessage();
}
?>

<form method="POST">
  <label for="name">Nome:</label>
  <input type="text" id="name" name="name" required>
  <br>
  <label for="email">E-mail:</label>
  <input type="email" id="email" name="email" required>
  <br>
  <input type="submit" value="Criar Usuário">
</form>
