<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/ColorConverter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$converter = new ColorConverter();
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    switch ($input['action']) {
        case 'hex_to_rgb':
            if (!isset($input['hex'])) {
                throw new Exception('HEX value required');
            }
            $result = $converter->hexToRgb($input['hex']);
            if ($result === false) {
                throw new Exception('Invalid HEX color');
            }
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'rgb_to_hex':
            if (!isset($input['r']) || !isset($input['g']) || !isset($input['b'])) {
                throw new Exception('RGB values required');
            }
            $result = $converter->rgbToHex($input['r'], $input['g'], $input['b']);
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'generate_palette':
            if (!isset($input['baseColor'])) {
                throw new Exception('Base color required');
            }
            // Renk paleti üretme fonksiyonu
            $palette = $converter->generatePalette($input['baseColor']);
            echo json_encode(['success' => true, 'data' => $palette]);
            break;
            
        default:
            throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>