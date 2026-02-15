<?php

register_menu("Port Tester", true, "port_tester", 'SETTINGS', '');

function port_tester()
{
    global $ui;

    _admin();

    $ui->assign('_title', 'Port Tester');
    $ui->assign('_system_menu', 'settings');

    $port = (int) _post('port', 8278);
    $ui->assign('port', $port);

    $result = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Validación estricta
        if ($port < 1 || $port > 65535) {
            $result = "Invalid port number. Must be between 1 and 65535.";
        } else {

            $host = 'portquiz.net';

            // Validar DNS
            $ip = gethostbyname($host);

            if ($ip === $host) {
                $result = "DNS resolution failed for $host.";
            } else {

                $timeout = 5;
                $errno = 0;
                $errstr = '';

                // Test TCP real
                $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

                if (is_resource($connection)) {

                    fclose($connection);
                    $result = "Port $port is OPEN and reachable.";

                } else {

                    $result = "Port $port is CLOSED or BLOCKED.";
                }
            }
        }

        // Escape de seguridad
        $ui->assign('result', htmlspecialchars($result));
    }

    $ui->assign('_admin', Admin::_info());
    $ui->display('port_tester.tpl');
}
