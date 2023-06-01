<?php

use Slim\Routing\RouteCollectorProxy;

$app->group('/absences', function (RouteCollectorProxy $group) use ($mysqli) {

    $group->get('', function ($request, $response) use ($mysqli) {
        $claims = $request->getAttribute('claims');

        $getallQuery = $claims->is_admin ? "1=1 or " : "";

        $stmt = $mysqli->prepare("select * from absence_plan where $getallQuery user_id = ?");
        $stmt->bind_param("i", $claims->id);

        $positions = array();

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $positions[] = $row;
            }

            return JsonResponse::withJson($response, $positions);
        } else {
            return JsonResponse::badRequest($response);
        }
        if ($claims->is_admin == true) {
        }
        return JsonResponse::withJson($response, $claims);
    });

    $group->get('/types', function ($request, $response) use ($mysqli) {

        $stmt = $mysqli->prepare("select * from absence_types order by name");

        $positions = array();

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $positions[] = $row;
            }

            return JsonResponse::withJson($response, $positions);
        }
        return JsonResponse::badRequest($response);
    });

    $group->post('', function ($request, $response) use ($mysqli) {
        $claims = $request->getAttribute('claims');

        $data = $request->getParsedBody();


        // Validate request data
        if (!isset($data['typeId']) || !isset($data['dateFrom']) || !isset($data['dateTo'])) {
            return JsonResponse::badRequest($response, 'Missing required fields (typeId, dateFrom, dateTo)');
        }
        $userId = $claims->id;
        $typeId = $data['typeId'];
        $from = $data['dateFrom'];
        $to = $data['dateTo'];

        $fromDate = date_create($from);
        $toDate = date_create($to);
        if ($fromDate >= $toDate) {
            return JsonResponse::badRequest($response, "Starting date cannot be lower than end date");
        }

        if ($toDate < date("now")) {
            return JsonResponse::badRequest($response, "PLanned absence date range cannot be in past");
        }

        try {
            $stmt = $mysqli->prepare("INSERT INTO absence_plan (user_id, absence_type_id, date_from, date_to) VALUES (?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?));");
            $stmt->bind_param("isss", $userId, $typeId, date_timestamp_get($fromDate), date_timestamp_get($toDate));

            if ($stmt->execute()) {
                $newId = $stmt->insert_id;
                return JsonResponse::created($response, "/absences/$newId", getAbsenceById($mysqli, $newId));
            } else {
                return JsonResponse::badRequest($response, 'Failed to create absence');
            }
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() == 1062) {
                return JsonResponse::conflict($response, "That absence exists already");
            } else
                return JsonResponse::badRequest($response, $ex->getMessage());
        }
    });

    function getAbsenceById($mysqli, int $id)
    {
        $stmt = $mysqli->prepare("SELECT * from absence_plan where id = ?;");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row;
        }
        return null;
    }
})->add(new AuthMiddleware());
