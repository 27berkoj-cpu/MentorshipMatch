<?php
declare(strict_types=1);

require_once __DIR__ . '/database/server.php';
require_once __DIR__ . '/session.php';

function db(): mysqli {
    global $db;
    return $db;
}

function run(string $sql, array $params = []): mysqli_stmt {
    $st = db()->prepare($sql);
    if ($params) {
        $types = '';
        foreach ($params as $p) {
            $types .= is_int($p) ? 'i' : 's';
        }
        $st->bind_param($types, ...$params);
    }
    $st->execute();
    return $st;
}

function rows(string $sql, array $params = []): array {
    return run($sql, $params)->get_result()->fetch_all(MYSQLI_ASSOC);
}

function row(string $sql, array $params = []): ?array {
    return run($sql, $params)->get_result()->fetch_assoc() ?: null;
}

function respond(int $code, array $body): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($body);
    exit;
}

function fail(int $code, string $msg): never {
    respond($code, ['error' => $msg]);
}

function currentUser(): array {
    $id   = $_SESSION['user_id'] ?? null;
    $role = $_SESSION['role'] ?? null;
    if (!$id || !in_array($role, ['student', 'mentor'], true)) fail(401, 'Authentication required');

    if ($role === 'mentor') {
        $user = row('SELECT mentor_id AS id, first_name, last_name FROM mentorstb WHERE mentor_id = ?', [(int)$id]);
    } else {
        $user = row('SELECT student_id AS id, first_name, last_name, aca_goal FROM studentstb WHERE student_id = ?', [(int)$id]);
    }
    if (!$user) fail(401, 'Invalid session');

    $user['role'] = $role;
    return $user;
}

function jsonBody(): array {
    $data = json_decode(file_get_contents('php://input') ?: '', true);
    return is_array($data) ? $data : [];
}

function listMatches(array $me): void {
    if ($me['role'] !== 'student') fail(403, 'Only students can browse matches');
    $matches = rows(
        "SELECT m.mentor_id AS id, m.first_name, m.last_name, m.field, m.current_career, m.rating
           FROM mentorstb m
          WHERE (? = '' OR LOWER(m.field) LIKE CONCAT('%', LOWER(?), '%'))
            AND NOT EXISTS (SELECT 1 FROM mentor_requests r
                             WHERE r.student_id = ?
                               AND r.mentor_id = m.mentor_id
                               AND r.status IN ('pending','accepted'))
          ORDER BY m.rating DESC, m.last_name
          LIMIT 20",
        [(string)($me['aca_goal'] ?? ''), (string)($me['aca_goal'] ?? ''), (int)$me['id']]
    );
    respond(200, ['matches' => $matches]);
}

function sendRequest(array $me): void {
    if ($me['role'] !== 'student') fail(403, 'Only students can send requests');
    $in       = jsonBody();
    $mentorId = filter_var($in['mentor_id'] ?? null, FILTER_VALIDATE_INT);
    $message  = trim((string)($in['message'] ?? ''));
    if (!$mentorId) fail(422, 'mentor_id is required');
    if (mb_strlen($message) > 1000) fail(422, 'message must be 1000 characters or fewer');

    if (!row('SELECT mentor_id FROM mentorstb WHERE mentor_id = ?', [$mentorId])) {
        fail(404, 'Mentor not found');
    }

    try {
        run('INSERT INTO mentor_requests (student_id, mentor_id, message) VALUES (?, ?, ?)',
            [(int)$me['id'], $mentorId, $message !== '' ? $message : null]);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1062) fail(409, 'You already have an active request with this mentor');
        throw $e;
    }
    respond(201, ['id' => db()->insert_id, 'status' => 'pending']);
}

function listRequests(array $me): void {
    $status = $_GET['status'] ?? null;
    if ($status !== null && !in_array($status, ['pending','accepted','declined','cancelled'], true))
        fail(422, 'Invalid status');

    if ($me['role'] === 'student') {
        $sql = "SELECT r.id, r.status, r.message, r.created_at, r.responded_at,
                       m.mentor_id AS other_id,
                       CONCAT_WS(' ', m.first_name, m.last_name) AS other_name,
                       m.field AS other_detail
                  FROM mentor_requests r JOIN mentorstb m ON m.mentor_id = r.mentor_id
                 WHERE r.student_id = ?";
    } else {
        $sql = "SELECT r.id, r.status, r.message, r.created_at, r.responded_at,
                       s.student_id AS other_id,
                       CONCAT_WS(' ', s.first_name, s.last_name) AS other_name,
                       s.aca_goal AS other_detail
                  FROM mentor_requests r JOIN studentstb s ON s.student_id = r.student_id
                 WHERE r.mentor_id = ?";
    }
    $params = [(int)$me['id']];
    if ($status) {
        $sql .= ' AND r.status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY r.created_at DESC LIMIT 100';

    respond(200, ['requests' => rows($sql, $params)]);
}

function transition(array $me, int $id, string $actor, string $newStatus): void {
    $db = db();
    $db->begin_transaction();
    try {
        $req = row('SELECT * FROM mentor_requests WHERE id = ? FOR UPDATE', [$id]);

        if (!$req || (int)$req[$actor] !== (int)$me['id']) {
            $db->rollback();
            fail(404, 'Request not found');
        }
        if ($req['status'] !== 'pending') {
            $db->rollback();
            fail(409, "Request is already {$req['status']}");
        }

        run('UPDATE mentor_requests SET status = ?, responded_at = NOW() WHERE id = ?',
            [$newStatus, $id]);
        $db->commit();
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
    respond(200, ['id' => $id, 'status' => $newStatus]);
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $path   = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $path   = preg_replace('#^.*/' . preg_quote(basename(__FILE__), '#') . '#i', '', $path) ?: '/';
    $me     = currentUser();

    if ($method === 'GET'  && $path === '/matches')  listMatches($me);
    if ($method === 'POST' && $path === '/requests') sendRequest($me);
    if ($method === 'GET'  && $path === '/requests') listRequests($me);

    if (preg_match('#^/requests/(\d+)(?:/(accept|decline))?$#', $path, $m)) {
        $id = (int)$m[1];
        if (isset($m[2])) {
            if ($method !== 'POST') fail(405, 'Method not allowed');
            if ($me['role'] !== 'mentor') fail(403, 'Only mentors can respond to requests');
            transition($me, $id, 'mentor_id', $m[2] === 'accept' ? 'accepted' : 'declined');
        }
        if ($method === 'DELETE') {
            if ($me['role'] !== 'student') fail(403, 'Only students can cancel requests');
            transition($me, $id, 'student_id', 'cancelled');
        }
    }

    fail(404, 'Route not found');
} catch (Throwable $e) {
    error_log($e->getMessage());
    fail(500, 'Server error');
}
?>