<?php

    //implement caching
    require 'autoload.php';

    use src\Database;
    
    $database = new Database();
    $query = $database->getQuery(); 
    $query2 = $database->getQuery();
    //nesting of subqueries
    //select from query2
    
    $results = $query->select('employees')
                ->fields(['*'])
                ->join('offices', 'employees.officeCode = offices.officeCode')
                ->condition('firstName', 'Diane')
                ->execute();
    
   // $results = $query->delete('employees')
                //->condition('firstName', 'John')
                //->execute();
    
    //$results = $query->select($query2)->fields(['*'])->condition('name', 'ayush')->execute();

    foreach($results as $result){
        echo "First Name: " . $result['firstName'] . "<br>";
        echo "Last Name: " . $result['lastName'] . "<br>";
        echo "Office Code: " . $result['officeCode'] . "<br>";
        echo "Office City: " . $result['city'] . "<br>";
        echo "<br>";
    }

?>