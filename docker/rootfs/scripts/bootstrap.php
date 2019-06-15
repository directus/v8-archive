<?php

ob_implicit_flush(true);
ini_set("memory_limit", "4096M");
set_time_limit(0);

function write($line)
{
    echo $line . "\n";
    // ob_flush();
}

$config = require("/var/www/html/config/api.php");

if ($config["database"]["type"] !== "mysql") {
    write("");
    write("  ERROR: Bootstrap script only supports MySQL backends.");
    write("");
    exit(1);
}

// Database connection

write("Connecting to the database...");

try {
    if (array_key_exists("socket", $config["database"])) {
        $dsn = "mysql:" .
            "dbname=" . $config["database"]["name"] . ";" .
            "unix_socket=" . $config["database"]["socket"] . ";";
    } else {
        $dsn = "mysql:" .
            "host=" . $config["database"]["host"] . ";" .
            "port=" . $config["database"]["port"] . ";" .
            "dbname=" . $config["database"]["name"] . ";";
    }

    $connection = new PDO($dsn,
        $config["database"]["username"],
        $config["database"]["password"], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
} catch (PDOException $e) {
    write("");
    write("  ERROR: Database connection failed.");
    write("  " . $e->getMessage());
    write("");
    exit(1);
}

// Check if we need to install

$install = true;

try {
    $statement = $connection->prepare("SHOW TABLES");
    $statement->execute();
    $tables = $statement->fetchAll();
    foreach ($tables as $table) {
        $name = strtolower($table[0]);
        if (substr($name, 0, 9) === "directus_") {
            $install = false;
            break;
        }
    }
} catch (PDOException $e) {
    write("");
    write("  ERROR: Failed to list database tables.");
    write("  " . $e->getMessage());
    write("");
    exit(1);
}

// Initialize

if ($install) {

    $admin_email = getenv("ADMIN_EMAIL");
    if (!$admin_email) {
        write("");
        write("  ERROR: Missing ADMIN_EMAIL environment variable");
        write("");
        exit(1);
    }

    $admin_password_generated = false;
    $admin_password = getenv("ADMIN_PASSWORD");
    if (!$admin_password) {
        $admin_password = substr(md5(uniqid("directus")), 0, 8);
        $admin_password_generated = true;
    }

    write("Installing database...");
    passthru("/var/www/html/bin/directus install:database");

    write("Installing data...");
    passthru("/var/www/html/bin/directus install:install -e \"" . $admin_email . "\" -p \"" . $admin_password . "\" -t \"Directus\"");

    write("");
    write(" Directus database installed.");
    if ($admin_password_generated) {
        write("");
        write("  The credentials for admin account are:");
        write("");
        write("      Email: " . $admin_email);
        write("   Password: " . $admin_password);
        write("");
    }
    write("");

}
