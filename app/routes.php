<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

use Slim\Views\PhpRenderer;

require __DIR__ . '/database.php';

return function (App $app) {
    $app->get('/', function (Request $request, Response $response, array $args) {
        $renderer = new PhpRenderer('../views');
        return $renderer->render($response, "welcome.php", $args);
    });

    $app->get('/request', function (Request $request, Response $response, array $args) {
        $renderer = new PhpRenderer('../views');
        return $renderer->render($response, "request.php", $args);
    });

    $app->get('/database', function (Request $request, Response $response) {
        $query = "SELECT * FROM pixels";
        try {
            $database = new Database();
            $connection = $database->connect();
            $statement = $connection->query($query);
            $pixels = $statement->fetchAll(PDO::FETCH_OBJ);
            $database = null;

            $response->getBody()->write(json_encode($pixels));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (PDOException $e) {
            $error = array("message" => $e->getMessage());

            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    $app->post('/pixelapi', function (Request $request, Response $response) {
        // PixelData
        $pixelData = $request->getParsedBody()['pixelData'];

        $pixelType = $pixelData['pixelType'];
        $userId = $pixelData['userId'];
        $occuredOn = $pixelData['occuredOn'];
        $portalId = $pixelData['portalId'];

        // Soft validation
        if (!isset($pixelType) || !isset($userId) || !isset($occuredOn) || !isset($portalId) || !is_string($pixelType) || !is_int($userId) || !is_int($occuredOn) || !is_int($portalId)) {
            $response->getBody()->write(json_encode(['message' => 'Bad Request (Invalid input / Object invalid)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } else {
            // Convert timestamp to date to store it in the database
            $occuredOn = date('Y-m-d H:i:s', $occuredOn);

            // If we wanna check for a record where we include the exact time of occurance
            $query = "SELECT * FROM `pixels` WHERE `pixelType` = :pixelType AND `userId` = :userId AND `occuredOn` = :occuredOn AND `portalId` = :portalId";

            // If we wanna check for a record excluding the exact time of occurance
            // $query = "SELECT * FROM `pixels` WHERE `pixelType` = :pixelType AND `userId` = :userId AND `portalId` = :portalId";
            try {
                $database = new Database();
                $connection = $database->connect();
                $statement = $connection->prepare($query);

                $statement->bindParam(':pixelType', $pixelType);
                $statement->bindParam(':userId', $userId);
                $statement->bindParam(':occuredOn', $occuredOn);
                $statement->bindParam(':portalId', $portalId);
                $result = $statement->execute();
                $database = null;

                if ($statement->rowCount()) {
                    $response->getBody()->write(json_encode(['message' => 'Unauthorized (An existing item already exists)']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
                } else {
                    $query = "INSERT INTO pixels (pixelType, userId, occuredOn, portalId) VALUE (:pixelType, :userId, :occuredOn, :portalId)";
                    try {
                        $database = new Database();
                        $connection = $database->connect();
                        $statement = $connection->prepare($query);

                        $statement->bindParam(':pixelType', $pixelType);
                        $statement->bindParam(':userId', $userId);
                        $statement->bindParam(':occuredOn', $occuredOn);
                        $statement->bindParam(':portalId', $portalId);
                        $result = $statement->execute();
                        $database = null;

                        $response->getBody()->write(json_encode(['message' => 'OK (Data saved)']));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
                    } catch (PDOException $e) {
                        $error = array("message" => $e->getMessage());

                        $response->getBody()->write(json_encode($error));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                    }
                }
            } catch (PDOException $e) {
                $error = array("message" => $e->getMessage());

                $response->getBody()->write(json_encode($error));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        }
    });
};
