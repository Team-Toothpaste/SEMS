<?php

require_once("../.credentials.php");
$errors = [];
$data = [];

if(empty($_POST["question"])) {
    $errors["question"] = "Question is required.";
}

if (empty($_POST["answer"])) {
    $errors["answer"] = "Answer is required.";
}

if (!empty($errors)) {
    $data["success"] = false;
    $data["message"] = $errors["answer"] . $errors["question"];
} else {
    $conn = mysqli_connect($_DB['location'], $_DB['username'], $_DB['password'], $_DB['name']);
    $question = $errors["question"];
    $answer = $errors["answer"];
    if ($conn) {
        $userid = $_COOKIE["sems-user"].explode()[0];
        $ret = mysqli_query($conn, "INSERT INTO users (question, answer) VALUES ('".$question."','".$answer."') WHERE userId = '".$userid."'");

        if ($ret == TRUE) {
            $data["success"] = true;
            $data["message"] = "Success";
        }
    }

}

echo json_encode($data);

?>