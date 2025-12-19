<?php
// search.php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

try {
    // Required params
    $userLat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
    $userLon = isset($_GET['lon']) ? (float)$_GET['lon'] : null;
    if ($userLat === null || $userLon === null) {
        http_response_code(400);
        echo json_encode(['error' => 'lat and lon are required']);
        exit;
    }

    $radiusKm = isset($_GET['radius']) ? max(0.1, (float)$_GET['radius']) : 5.0;
    $minRating = isset($_GET['minRating']) ? (float)$_GET['minRating'] : 0.0;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;

    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Log search
    $logSql = "INSERT INTO `search` (user_id, lat, lon, radius_km, min_rating, ip_addr)
               VALUES (NULL, :lat, :lon, :radius, :minRating, :ip)";
    $stmtLog = $pdo->prepare($logSql);
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmtLog->execute([
        ':lat' => $userLat,
        ':lon' => $userLon,
        ':radius' => $radiusKm,
        ':minRating' => $minRating,
        ':ip' => $ip
    ]);

    // Bounding box prefilter
    $latDegPerKm = 1 / 110.574;
    $lonDegPerKm = 1 / (111.320 * cos(deg2rad($userLat)));
    $latDelta = $radiusKm * $latDegPerKm;
    $lonDelta = $radiusKm * $lonDegPerKm;

    $minLat = $userLat - $latDelta;
    $maxLat = $userLat + $latDelta;
    $minLon = $userLon - $lonDelta;
    $maxLon = $userLon + $lonDelta;

    $sql = "
    SELECT
      f.id,
      f.name,
      f.price,
      f.in_stock,
      f.stock_qty,
      f.lat,
      f.lon,
      s.id AS seller_id,
      s.name AS seller_name,
      (CASE WHEN f.rating_count > 0 THEN (f.rating_sum / f.rating_count) ELSE NULL END) AS avg_rating,
      ( 6371 * ACOS(
          COS(RADIANS(:userLat)) * COS(RADIANS(f.lat)) * COS(RADIANS(f.lon) - RADIANS(:userLon)) +
          SIN(RADIANS(:userLat)) * SIN(RADIANS(f.lat))
      )) AS distance_km
    FROM flowers f
    JOIN sellers s ON s.id = f.seller_id
    WHERE
      f.in_stock = 1
      AND f.lat BETWEEN :minLat AND :maxLat
      AND f.lon BETWEEN :minLon AND :maxLon
    HAVING distance_km <= :radiusKm
      AND (avg_rating IS NULL OR avg_rating >= :minRating)
    ORDER BY distance_km ASC, avg_rating DESC
    LIMIT :limit OFFSET :offset;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':userLat', $userLat);
    $stmt->bindValue(':userLon', $userLon);
    $stmt->bindValue(':minLat', $minLat);
    $stmt->bindValue(':maxLat', $maxLat);
    $stmt->bindValue(':minLon', $minLon);
    $stmt->bindValue(':maxLon', $maxLon);
    $stmt->bindValue(':radiusKm', $radiusKm);
    $stmt->bindValue(':minRating', $minRating);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'meta' => [
            'page' => $page,
            'limit' => $limit,
            'radius_km' => $radiusKm,
            'min_rating' => $minRating,
            'count' => count($results)
        ],
        'data' => $results
    ], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
    exit;
}
