<?php
    session_start();
    
    include "Auxillary.php";
    
    // Connect to the database
    $connect = db_connect();

    // Check for database connection errors
    if ($connect->connect_error)
    {
        retWithErr("Database connection error.\n");
    }

    else
    {
        $username = "";
        $password = "";

        // Retrieve user input
        if (isset($_POST["username"]) && isset($_POST["password"]))
        {
            $username = $_POST["username"];
            $password = $_POST["password"];
        }

        else if (empty($username) || empty($password))
        {
            retWithErr("Username or password not provided.\n");
        }

        // Query the database for the username to verify if it exists
        $sql = "SELECT user_id, username, password FROM users WHERE username = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            $user_data = $result->fetch_assoc();

            // Verify the hashed password
            if (password_verify($password, $user_data['password'])) 
            {
                $_SESSION['user_id'] = $user_data['user_id'];
                retWithInfo("Login successful. User_ID: $user_data[user_id]" . "\n");
            }

            else
            {
                retWithErr("Incorrect password.\n");
            }
        }

        else
        {
            retWithErr("User does not exist.\n");
        }

        // Close the database connection
        $stmt->close();
        $connect->close();
    }
?>
