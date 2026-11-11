<?php

include_once 'conexao.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Temporário, até integrarmos o login
$id_usuario = 0;

try {
    $pdo->beginTransaction();

    // Horário local utilizado pelo AMES
    $agora = new DateTimeImmutable(
        'now',
        new DateTimeZone('America/Sao_Paulo')
    );

    $sql = "SELECT
                l.id_lembrete,
                l.mensagem,
                l.data,
                l.horario,
                a.titulo
            FROM lembrete l
            INNER JOIN atividade a
                ON l.id_atividade = a.id_atividade
            WHERE a.id_usuario = :id_usuario
              AND l.status = 'agendado'
              AND TIMESTAMP(l.data, l.horario) <= :agora
            ORDER BY l.data ASC, l.horario ASC
            LIMIT 10
            FOR UPDATE";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':agora' => $agora->format('Y-m-d H:i:s')
    ]);

    $lembretes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Registrar os avisos disponibilizados
    $sqlUpdate = "UPDATE lembrete
                  SET status = 'enviado'
                  WHERE id_lembrete = :id_lembrete
                    AND status = 'agendado'";

    $stmtUpdate = $pdo->prepare($sqlUpdate);

    foreach ($lembretes as $lembrete) {
        $stmtUpdate->execute([
            ':id_lembrete' => $lembrete['id_lembrete']
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'sucesso' => true,
        'lembretes' => $lembretes
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        'sucesso' => false,
        'erro' => 'Não foi possível verificar os lembretes.'
    ], JSON_UNESCAPED_UNICODE);
}
