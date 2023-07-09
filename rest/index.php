<?php
require '../vendor/autoload.php';

/** TODO
 * Use PDO connection to connect to MySQL Database
 *
 * Update configuration variables used to connect to MySQL database
 *
 * NOTE: Do not create new connection within each endpoint. Use connection variable which was set using Flight
 *
 * NOTE: Do not add new files to the project
 *
 * NOTE: table that contains investors is named investors and table with tranfers is named transfers
 *
 * NOTE: If you are having issues with non working routes in flightPHP you have to enable MOD_REWRITE on Apache
 */
$servername = "localhost";
$username = "root";
$password = "root";
$schema = "midt-2022-v1";

$conn = new PDO("mysql:host=$servername;dbname=$schema", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
Flight::set("connection", $conn);

Flight::route('GET /transfers/report_by_day', function () {
    /** TODO
     * write a query that will list total number of transactions and total amount of transfers
     * per day.
     *
     * List should be sorted by the total amount and number of transactions having the highest values on the top.
     *
     * This endpoint should return output in JSON format
     */
    $query = "SELECT
    transfers.created_at AS date,
    COUNT(transfers.id) AS total_transactions,
    SUM(transfers.amount) AS total_amount
    FROM transfers
    GROUP BY transfers.created_at
    ORDER BY total_amount DESC, total_transactions DESC;";

    $stmt = Flight::get("connection")->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($result);
});

Flight::route('GET /transfers/report_by_investors', function () {
    /** TODO
     * write a query that will list total number of transactions and total amount of transfers
     * per investor.
     *
     * List should be sorted by the total transferred amount and number of transactions having the highest values on the top.
     *
     * This endpoint should return output in JSON format
     */
    $query = "SELECT
    investors.id AS investor_id,
    investors.first_name,
    investors.last_name,
    COUNT(transfers.id) AS total_transactions,
    SUM(transfers.amount) AS total_amount
    FROM investors
    LEFT JOIN transfers ON investors.id = transfers.sender_id OR investors.id = transfers.recipient_id
    GROUP BY investors.id, investors.first_name, investors.last_name
    ORDER BY total_amount DESC, total_transactions DESC;";

    $stmt = Flight::get("connection")->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($result);
});

Flight::route('GET /transfers/report_by_day_and_investors', function () {
    /** TODO
     * write a query that will list total number of transactions and total amount of transfers
     * per investor for each day.
     *
     * List should be sorted by the total transferred amount and number of transactions having the highest values on the top.
     *
     * This endpoint should return output in JSON format
     */
    $query = "SELECT
    transfers.created_at AS date,
    investors.id AS investor_id,
    investors.first_name,
    investors.last_name,
    COUNT(transfers.id) AS total_transactions,
    SUM(transfers.amount) AS total_amount
    FROM investors
    LEFT JOIN transfers ON investors.id = transfers.sender_id OR investors.id = transfers.recipient_id
    GROUP BY transfers.created_at, investors.id, investors.first_name, investors.last_name
    ORDER BY total_amount DESC, total_transactions DESC;";

    $stmt = Flight::get("connection")->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($result);
});

Flight::start();
