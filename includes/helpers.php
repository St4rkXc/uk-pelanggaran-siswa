<?php

function dbFetchAll($pdo, $table) {
    $stmt = $pdo->prepare("SELECT * FROM $table");
    $stmt->execute();
    return $stmt->fetchAll();
}

function dbFetchById($pdo, $table, $idField, $id) {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $idField = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function dbInsert($pdo, $table, $data) {
    $fields = implode(',', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));

    $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);

    return $stmt->execute($data);
}

function dbUpdate($pdo, $table, $data, $idField, $id) {
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "$key = :$key";
    }

    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $idField = :id";
    $data['id'] = $id;

    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

function dbDelete($pdo, $table, $idField, $id) {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE $idField = :id");
    return $stmt->execute(['id' => $id]);
}
